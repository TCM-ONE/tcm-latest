<?php
/**
 * TCM One: PayU Gateway Asynchronous Payment Reconciliation & Cron Job
 *
 * Usage:
 *   php bin/reconcile_payu.php
 *
 * Cron setup (every 15 minutes):
 *   * /15 * * * * php /home/u743529483/domains/thecodemunk.in/public_html/bin/reconcile_payu.php >> /dev/null 2>&1
 */

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use TCM\Core\Database;
use TCM\Core\PayU;
use TCM\Models\Order;
use TCM\Models\PaymentTransaction;
use TCM\Models\WebhookAuditLog;
use TCM\Services\InstallmentService;

echo "=========================================================\n";
echo "  TCM One — PayU Payment Reconciliation & Overdue Cron   \n";
echo "  Time: " . date('Y-m-d H:i:s T') . "\n";
echo "=========================================================\n\n";

if (!PayU::isConfigured()) {
    echo "[ABORT] PayU merchant key/salt not configured.\n";
    exit(1);
}

// 1. Refresh Overdue Installment Schedules
echo "[1/2] Refreshing overdue installment schedules...\n";
$overdueCount = InstallmentService::refreshOverdueStatuses();
echo "  -> Marked {$overdueCount} milestone(s) as overdue.\n\n";

// 2. Query Pending Orders Created within the Last 7 Days
echo "[2/2] Scanning pending orders for server-to-server PayU verification...\n";
$pendingOrders = Database::all("
    SELECT * FROM orders
    WHERE status = 'pending' AND txnid IS NOT NULL AND txnid != ''
      AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY id ASC
    LIMIT 50
");

if (empty($pendingOrders)) {
    echo "  -> No pending orders pending reconciliation.\n";
    echo "\nReconciliation process completed successfully.\n";
    exit(0);
}

echo "  -> Found " . count($pendingOrders) . " pending order(s) to verify against PayU.\n\n";

$reconciledCount = 0;

foreach ($pendingOrders as $order) {
    $txnid = (string) $order['txnid'];
    echo "  Checking Order #{$order['id']} ({$order['order_number']}), Txn: {$txnid}... ";

    $details = PayU::verifyPaymentApi($txnid);
    if ($details === null) {
        echo "No transaction record found on gateway (or still in progress).\n";
        continue;
    }

    $status = strtolower((string) ($details['status'] ?? ''));
    $amount = (float) ($details['amount'] ?? 0.0);
    $mihpayid = (string) ($details['mihpayid'] ?? $txnid);

    echo "Gateway Status: [{$status}], Amount: ₹{$amount}... ";

    if ($status === 'success' && abs($amount - (float) $order['amount']) <= 0.10) {
        echo "MATCH! Confirming payment... ";

        $receiptNumber = $order['receipt_number'] ?: Order::generateReceiptNumber((int) $order['id']);

        Database::transaction(function () use ($order, $txnid, $mihpayid, $amount, $receiptNumber, $details) {
            // Update order
            Database::run("
                UPDATE orders SET
                    status           = 'paid',
                    gateway_status   = 'success',
                    paid_amount      = amount,
                    remaining_amount = 0.00,
                    payment_ref      = ?,
                    receipt_number   = ?,
                    verified_by_api  = 1,
                    raw_response     = ?,
                    updated_at       = NOW()
                WHERE id = ?
            ", [$mihpayid, $receiptNumber, json_encode($details, JSON_UNESCAPED_SLASHES), $order['id']]);

            // Upsert payment_transactions
            $existing = PaymentTransaction::findByTxnid($txnid);
            if ($existing) {
                PaymentTransaction::update((int) $existing['id'], [
                    'status'       => 'success',
                    'mihpayid'     => $mihpayid,
                    'api_verified' => 1,
                    'raw_payload'  => json_encode(PayU::sanitizePayload($details), JSON_UNESCAPED_SLASHES),
                ]);
            } else {
                PaymentTransaction::create([
                    'order_id'      => (int) $order['id'],
                    'user_id'       => (int) $order['user_id'],
                    'txnid'         => $txnid,
                    'mihpayid'      => $mihpayid,
                    'amount'        => $amount,
                    'currency'      => 'INR',
                    'status'        => 'success',
                    'api_verified'  => 1,
                    'hash_verified' => 1,
                    'raw_payload'   => json_encode(PayU::sanitizePayload($details), JSON_UNESCAPED_SLASHES),
                ]);
            }

            // Grant Enrollment
            if ($order['item_type'] === 'course') {
                Database::run("
                    INSERT INTO enrollments (user_id, course_id, order_id, status, progress, enrolled_at)
                    VALUES (?, ?, ?, 'active', 0, NOW())
                    ON DUPLICATE KEY UPDATE status = 'active', order_id = VALUES(order_id)
                ", [(int) $order['user_id'], (int) $order['item_id'], (int) $order['id']]);
            }

            // Audit
            WebhookAuditLog::logEvent(
                'reconciliation',
                'cli/bin/reconcile_payu.php',
                'CLI',
                '127.0.0.1',
                null,
                json_encode(PayU::sanitizePayload($details), JSON_UNESCAPED_SLASHES),
                200,
                'verified',
                "Order #{$order['id']} successfully reconciled and marked paid via PayU Verify Payment API."
            );
        });

        echo "DONE (Receipt: {$receiptNumber})\n";
        $reconciledCount++;

    } elseif ($status === 'failure') {
        echo "Payment marked failed on gateway.\n";
        Database::run("
            UPDATE orders SET status = 'failed', gateway_status = 'failed', updated_at = NOW()
            WHERE id = ? AND status != 'paid'
        ", [$order['id']]);
    } else {
        echo "Pending or undetermined.\n";
    }
}

echo "\n=========================================================\n";
echo "  Summary: {$reconciledCount} order(s) reconciled & confirmed.\n";
echo "=========================================================\n";
