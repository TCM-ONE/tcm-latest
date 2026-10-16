<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Mailer;
use TCM\Core\PayU;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Models\Course;
use TCM\Models\Enrollment;
use TCM\Models\InstallmentSchedule;
use TCM\Models\Order;
use TCM\Models\PaymentDispute;
use TCM\Models\PaymentRefund;
use TCM\Models\PaymentTransaction;
use TCM\Models\WebhookAuditLog;
use TCM\Models\WorkshopLead;
use TCM\Services\InstallmentService;

final class PayUWebhookController extends Controller
{
    /**
     * POST /api/webhooks/payu/success
     * Official PayU Payment Success Webhook Callback.
     */
    public function success(): void
    {
        $payload   = $this->getRequestPayload();
        $rawBody   = file_get_contents('php://input') ?: '';
        $sanitized = PayU::sanitizePayload($payload);
        $url       = (string) ($_SERVER['REQUEST_URI'] ?? '/api/webhooks/payu/success');
        $ip        = $this->getClientIp();
        $headers   = $this->getHeadersJson();

        $txnid       = (string) ($payload['txnid'] ?? '');
        $orderNumber = (string) ($payload['udf5'] ?? '');
        $postedHash  = (string) ($payload['hash'] ?? '');

        // 1. Verify Hash
        $hashValid = PayU::verifyResponseHash($payload);
        if (!$hashValid) {
            WebhookAuditLog::logEvent(
                'payment_success',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                400,
                'hash_mismatch',
                'Cryptographic reverse hash verification failed. Provided hash: ' . $postedHash
            );

            http_response_code(400);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode(['error' => 'Invalid signature hash'], JSON_UNESCAPED_SLASHES);
            return;
        }

        // 2. Locate Order Record
        $order = null;
        if ($txnid !== '') {
            $order = Order::findByTxnid($txnid);
        }
        if (!$order && $orderNumber !== '') {
            $order = Order::findBy(['order_number' => $orderNumber]);
        }

        if (!$order) {
            WebhookAuditLog::logEvent(
                'payment_success',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                404,
                'invalid_order',
                "Order record not found for txnid: {$txnid}, order_number: {$orderNumber}"
            );

            http_response_code(404);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode(['error' => 'Order not found'], JSON_UNESCAPED_SLASHES);
            return;
        }

        $orderId = (int) $order['id'];
        $userId  = (int) $order['user_id'];
        $amount  = (float) $order['amount'];
        $mihpayid = (string) ($payload['mihpayid'] ?? $txnid);

        // 3. Idempotency Check: Already Paid
        if ($order['status'] === 'paid') {
            WebhookAuditLog::logEvent(
                'payment_success',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                200,
                'verified',
                "Idempotent: Order #{$orderId} ({$order['order_number']}) is already marked as paid. Duplicate webhook acknowledged."
            );

            http_response_code(200);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode([
                'status'         => 'ok',
                'message'        => 'Order already confirmed',
                'order_id'       => $orderId,
                'receipt_number' => $order['receipt_number'] ?? null,
            ], JSON_UNESCAPED_SLASHES);
            return;
        }

        // 4. Server-to-Server PayU Verify Payment API Check
        $apiVerified = false;
        $apiDetails = PayU::verifyPaymentApi($txnid);
        if ($apiDetails !== null) {
            $apiStatus = strtolower((string) ($apiDetails['status'] ?? ''));
            $apiAmount = (float) ($apiDetails['amount'] ?? 0.0);

            if ($apiStatus !== 'success' || abs($apiAmount - $amount) > 0.10) {
                WebhookAuditLog::logEvent(
                    'payment_success',
                    $url,
                    'POST',
                    $ip,
                    $headers,
                    json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                    400,
                    'api_mismatch',
                    "PayU PostService API verification mismatch: status={$apiStatus}, amount={$apiAmount}, expected={$amount}"
                );

                http_response_code(400);
                if (!headers_sent()) { header('Content-Type: application/json'); }
                echo json_encode(['error' => 'PayU server-to-server verification mismatch'], JSON_UNESCAPED_SLASHES);
                return;
            }
            $apiVerified = true;
        } else {
            // In case of network timeout or sandbox mode fallback
            $apiVerified = false;
        }

        // 5. Atomic Fulfillment Inside Transaction
        $receiptNumber = $order['receipt_number'] ?: Order::generateReceiptNumber($orderId);

        try {
            Database::transaction(function () use (
                $order,
                $orderId,
                $userId,
                $amount,
                $txnid,
                $mihpayid,
                $payload,
                $sanitized,
                $receiptNumber,
                $apiVerified
            ) {
                // Record / Update payment_transactions
                $existingTxn = PaymentTransaction::findByTxnid($txnid);
                if ($existingTxn) {
                    PaymentTransaction::update((int) $existingTxn['id'], [
                        'mihpayid'      => $mihpayid,
                        'bank_ref_num'  => (string) ($payload['bank_ref_num'] ?? ''),
                        'status'        => 'success',
                        'payment_mode'  => (string) ($payload['mode'] ?? ''),
                        'card_type'     => (string) ($payload['cardtype'] ?? ''),
                        'bank_code'     => (string) ($payload['bankcode'] ?? ''),
                        'hash_verified' => 1,
                        'api_verified'  => $apiVerified ? 1 : 0,
                        'raw_payload'   => json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                    ]);
                } else {
                    PaymentTransaction::create([
                        'order_id'      => $orderId,
                        'user_id'       => $userId,
                        'txnid'         => $txnid,
                        'mihpayid'      => $mihpayid,
                        'bank_ref_num'  => (string) ($payload['bank_ref_num'] ?? ''),
                        'amount'        => $amount,
                        'currency'      => 'INR',
                        'status'        => 'success',
                        'payment_mode'  => (string) ($payload['mode'] ?? ''),
                        'card_type'     => (string) ($payload['cardtype'] ?? ''),
                        'bank_code'     => (string) ($payload['bankcode'] ?? ''),
                        'hash_verified' => 1,
                        'api_verified'  => $apiVerified ? 1 : 0,
                        'raw_payload'   => json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                    ]);
                }

                // Update orders row
                $isInstallment = (int) ($order['is_installment'] ?? 0);
                $remainingAmount = $isInstallment ? 0.00 : 0.00;

                Database::run("
                    UPDATE orders SET
                        status           = 'paid',
                        gateway_status   = 'success',
                        paid_amount      = ?,
                        remaining_amount = ?,
                        payment_ref      = ?,
                        receipt_number   = ?,
                        verified_by_api  = ?,
                        raw_response     = ?,
                        updated_at       = NOW()
                    WHERE id = ?
                ", [
                    $amount,
                    $remainingAmount,
                    $mihpayid,
                    $receiptNumber,
                    $apiVerified ? 1 : 0,
                    json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                    $orderId,
                ]);

                // Handle Installment schedule linking
                $scheduleId = (int) ($payload['udf3'] ?? 0);
                if ($isInstallment && $scheduleId > 0) {
                    InstallmentService::markInstallmentPaid($scheduleId, $txnid, $mihpayid, $amount);
                    if (!empty($existingTxn['id'])) {
                        PaymentTransaction::update((int) $existingTxn['id'], ['installment_schedule_id' => $scheduleId]);
                    }
                }

                // Grant Enrollment Access
                $itemType = (string) ($payload['udf1'] ?? ($order['item_type'] === 'event' ? 'workshop' : 'course'));
                $itemId   = (int) ($payload['udf2'] ?? $order['item_id']);

                if ($itemType === 'course' || $order['item_type'] === 'course') {
                    Database::run("
                        INSERT INTO enrollments (user_id, course_id, order_id, status, progress, enrolled_at)
                        VALUES (?, ?, ?, 'active', 0, NOW())
                        ON DUPLICATE KEY UPDATE status = 'active', order_id = VALUES(order_id)
                    ", [$userId, $itemId, $orderId]);

                    if ($isInstallment) {
                        $enrollmentId = (int) Database::scalar(
                            "SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? LIMIT 1",
                            [$userId, $itemId]
                        );
                        if ($enrollmentId > 0) {
                            Database::run(
                                "UPDATE installment_plans SET enrollment_id = ? WHERE user_id = ? AND course_id = ?",
                                [$enrollmentId, $userId, $itemId]
                            );
                        }
                    }
                } elseif ($itemType === 'workshop' || $itemType === 'event' || $order['item_type'] === 'event') {
                    $itemSlug = (string) ($payload['udf3'] ?? '');
                    $email    = strtolower((string) ($payload['email'] ?? ''));
                    Database::run("
                        UPDATE workshop_leads SET
                            payment_status    = 'paid',
                            enrollment_status = 'confirmed',
                            lead_status       = 'enrolled',
                            updated_at        = NOW()
                        WHERE (workshop_id = ? OR workshop_slug = ?) AND email = ?
                        ORDER BY id DESC LIMIT 1
                    ", [$itemId, $itemSlug, $email]);
                }

                // Compatibility: record in payment_submissions
                try {
                    Database::run("
                        INSERT INTO payment_submissions (user_id, item_type, item_id, item_title, amount, method, status, notes, verified_at, created_at)
                        VALUES (?, ?, ?, ?, ?, 'online', 'approved', ?, NOW(), NOW())
                    ", [
                        $userId,
                        $order['item_type'],
                        $order['item_id'],
                        $order['item_title'],
                        $amount,
                        "Webhook confirmed: PayU ID {$mihpayid} | Txn: {$txnid} | Receipt: {$receiptNumber}",
                    ]);
                } catch (\Throwable) {}
            });

            // Log Audit
            WebhookAuditLog::logEvent(
                'payment_success',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                200,
                'verified',
                "Payment successfully confirmed for order #{$orderId}. Receipt: {$receiptNumber}"
            );

            // Send Confirmation Email
            $this->sendPaymentSuccessEmail($order, $payload, $receiptNumber);

            http_response_code(200);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode([
                'status'         => 'ok',
                'message'        => 'Payment confirmed and access granted',
                'order_id'       => $orderId,
                'order_number'   => $order['order_number'],
                'receipt_number' => $receiptNumber,
            ], JSON_UNESCAPED_SLASHES);
            return;

        } catch (\Throwable $e) {
            WebhookAuditLog::logEvent(
                'payment_success',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                500,
                'error',
                'Transaction execution error: ' . $e->getMessage()
            );

            http_response_code(500);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode(['error' => 'Server error processing payment'], JSON_UNESCAPED_SLASHES);
            return;
        }
    }

    /**
     * POST /api/webhooks/payu/failed
     * Official PayU Payment Failure Callback.
     */
    public function failed(): void
    {
        $payload   = $this->getRequestPayload();
        $sanitized = PayU::sanitizePayload($payload);
        $url       = (string) ($_SERVER['REQUEST_URI'] ?? '/api/webhooks/payu/failed');
        $ip        = $this->getClientIp();
        $headers   = $this->getHeadersJson();

        $txnid        = (string) ($payload['txnid'] ?? '');
        $orderNumber  = (string) ($payload['udf5'] ?? '');
        $errorCode    = (string) ($payload['error'] ?? $payload['error_code'] ?? 'E000');
        $errorMessage = (string) ($payload['error_Message'] ?? $payload['errorMessage'] ?? 'Transaction failed');

        $order = null;
        if ($txnid !== '') {
            $order = Order::findByTxnid($txnid);
        }
        if (!$order && $orderNumber !== '') {
            $order = Order::findBy(['order_number' => $orderNumber]);
        }

        if (!$order) {
            WebhookAuditLog::logEvent(
                'payment_failed',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                404,
                'invalid_order',
                "Failure callback received but order not found for txnid: {$txnid}"
            );

            http_response_code(404);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode(['error' => 'Order not found'], JSON_UNESCAPED_SLASHES);
            return;
        }

        $orderId = (int) $order['id'];
        $userId  = (int) $order['user_id'];

        // OUT-OF-ORDER PROTECTION:
        // If order is ALREADY paid, NEVER downgrade it to failed!
        if ($order['status'] === 'paid') {
            WebhookAuditLog::logEvent(
                'payment_failed',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                200,
                'verified',
                "Out-of-order protection: Received late failure webhook for already PAID order #{$orderId}. Paid state preserved."
            );

            // Record transaction row as failed for audit trace without touching order status
            try {
                PaymentTransaction::create([
                    'order_id'      => $orderId,
                    'user_id'       => $userId,
                    'txnid'         => $txnid . '_late_fail',
                    'amount'        => (float) $order['amount'],
                    'currency'      => 'INR',
                    'status'        => 'failure',
                    'error_code'    => $errorCode,
                    'error_message' => $errorMessage,
                    'raw_payload'   => json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                ]);
            } catch (\Throwable) {}

            http_response_code(200);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode([
                'status'  => 'ignored',
                'message' => 'Order is already confirmed as paid. Failure notification ignored.',
            ], JSON_UNESCAPED_SLASHES);
            return;
        }

        // If order was pending, mark failed
        Database::run("
            UPDATE orders SET
                status         = 'failed',
                gateway_status = 'failed',
                raw_response   = ?,
                updated_at     = NOW()
            WHERE id = ? AND status != 'paid'
        ", [json_encode($sanitized, JSON_UNESCAPED_SLASHES), $orderId]);

        // Record in payment_transactions
        try {
            $existingTxn = PaymentTransaction::findByTxnid($txnid);
            if ($existingTxn) {
                PaymentTransaction::update((int) $existingTxn['id'], [
                    'status'        => 'failure',
                    'error_code'    => $errorCode,
                    'error_message' => $errorMessage,
                    'raw_payload'   => json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                ]);
            } else {
                PaymentTransaction::create([
                    'order_id'      => $orderId,
                    'user_id'       => $userId,
                    'txnid'         => $txnid,
                    'amount'        => (float) $order['amount'],
                    'currency'      => 'INR',
                    'status'        => 'failure',
                    'error_code'    => $errorCode,
                    'error_message' => $errorMessage,
                    'raw_payload'   => json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                ]);
            }
        } catch (\Throwable) {}

        WebhookAuditLog::logEvent(
            'payment_failed',
            $url,
            'POST',
            $ip,
            $headers,
            json_encode($sanitized, JSON_UNESCAPED_SLASHES),
            200,
            'verified',
            "Recorded payment failure for order #{$orderId}: {$errorMessage} (Code: {$errorCode})"
        );

        http_response_code(200);
        if (!headers_sent()) { header('Content-Type: application/json'); }
        echo json_encode([
            'status'  => 'ok',
            'message' => 'Failure recorded',
        ], JSON_UNESCAPED_SLASHES);
        return;
    }

    /**
     * POST /api/webhooks/payu/refund
     * Official PayU Refund Webhook.
     */
    public function refund(): void
    {
        $payload   = $this->getRequestPayload();
        $sanitized = PayU::sanitizePayload($payload);
        $url       = (string) ($_SERVER['REQUEST_URI'] ?? '/api/webhooks/payu/refund');
        $ip        = $this->getClientIp();
        $headers   = $this->getHeadersJson();

        $txnid        = (string) ($payload['txnid'] ?? '');
        $refundId     = (string) ($payload['payu_refund_id'] ?? $payload['refund_id'] ?? $payload['refundId'] ?? '');
        $refundAmount = (float) ($payload['amount'] ?? $payload['refund_amount'] ?? 0.0);
        $reason       = (string) ($payload['reason'] ?? $payload['refund_reason'] ?? 'Merchant refund');
        $status       = strtolower((string) ($payload['status'] ?? 'success'));

        $order = Order::findByTxnid($txnid);
        if (!$order) {
            WebhookAuditLog::logEvent(
                'refund',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                404,
                'invalid_order',
                "Refund webhook received for unknown txnid: {$txnid}"
            );

            http_response_code(404);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode(['error' => 'Associated order not found'], JSON_UNESCAPED_SLASHES);
            return;
        }

        $orderId = (int) $order['id'];
        $userId  = (int) $order['user_id'];
        $txn = PaymentTransaction::findByTxnid($txnid);
        $txnId = $txn ? (int) $txn['id'] : null;

        Database::transaction(function () use (
            $order,
            $orderId,
            $userId,
            $txnId,
            $txnid,
            $refundId,
            $refundAmount,
            $reason,
            $status,
            $sanitized
        ) {
            PaymentRefund::create([
                'payment_transaction_id' => $txnId,
                'order_id'               => $orderId,
                'user_id'                => $userId,
                'payu_refund_id'         => $refundId ?: 'REF_' . bin2hex(random_bytes(6)),
                'refund_txnid'           => $txnid,
                'amount'                 => $refundAmount,
                'currency'               => 'INR',
                'status'                 => ($status === 'success' || $status === 'completed') ? 'success' : 'pending',
                'reason'                 => $reason,
                'raw_response'           => json_encode($sanitized, JSON_UNESCAPED_SLASHES),
            ]);

            if ($status === 'success' || $status === 'completed') {
                $totalPaid = (float) $order['paid_amount'];
                $newPaid = max(0.0, $totalPaid - $refundAmount);
                $isFullRefund = ($newPaid <= 0.01);

                Database::run("
                    UPDATE orders SET
                        status           = IF(? = 1, 'refunded', status),
                        gateway_status   = 'refunded',
                        paid_amount      = ?,
                        remaining_amount = amount - ?,
                        updated_at       = NOW()
                    WHERE id = ?
                ", [$isFullRefund ? 1 : 0, $newPaid, $newPaid, $orderId]);

                // If full refund, deactivate course enrollment
                if ($isFullRefund && $order['item_type'] === 'course') {
                    Database::run("
                        UPDATE enrollments SET status = 'cancelled'
                        WHERE user_id = ? AND course_id = ?
                    ", [$userId, (int) $order['item_id']]);
                }
            }
        });

        WebhookAuditLog::logEvent(
            'refund',
            $url,
            'POST',
            $ip,
            $headers,
            json_encode($sanitized, JSON_UNESCAPED_SLASHES),
            200,
            'verified',
            "Recorded refund of ₹{$refundAmount} for order #{$orderId}"
        );

        http_response_code(200);
        if (!headers_sent()) { header('Content-Type: application/json'); }
        echo json_encode([
            'status'   => 'ok',
            'message'  => 'Refund recorded successfully',
            'order_id' => $orderId,
        ], JSON_UNESCAPED_SLASHES);
        return;
    }

    /**
     * POST /api/webhooks/payu/dispute
     * Official PayU Dispute / Chargeback Webhook.
     */
    public function dispute(): void
    {
        $payload   = $this->getRequestPayload();
        $sanitized = PayU::sanitizePayload($payload);
        $url       = (string) ($_SERVER['REQUEST_URI'] ?? '/api/webhooks/payu/dispute');
        $ip        = $this->getClientIp();
        $headers   = $this->getHeadersJson();

        $txnid         = (string) ($payload['txnid'] ?? '');
        $disputeId     = (string) ($payload['dispute_id'] ?? $payload['disputeId'] ?? ('DISP_' . bin2hex(random_bytes(6))));
        $disputeType   = (string) ($payload['dispute_type'] ?? 'chargeback');
        $disputeStatus = (string) ($payload['dispute_status'] ?? 'open');
        $amount        = (float) ($payload['amount'] ?? $payload['dispute_amount'] ?? 0.0);
        $reason        = (string) ($payload['reason'] ?? 'Customer disputed transaction');
        $dueDate       = !empty($payload['due_date']) ? date('Y-m-d', strtotime((string)$payload['due_date'])) : null;

        $order = Order::findByTxnid($txnid);
        if (!$order) {
            WebhookAuditLog::logEvent(
                'dispute',
                $url,
                'POST',
                $ip,
                $headers,
                json_encode($sanitized, JSON_UNESCAPED_SLASHES),
                404,
                'invalid_order',
                "Dispute webhook received for unknown txnid: {$txnid}"
            );

            http_response_code(404);
            if (!headers_sent()) { header('Content-Type: application/json'); }
            echo json_encode(['error' => 'Associated order not found'], JSON_UNESCAPED_SLASHES);
            return;
        }

        $orderId = (int) $order['id'];
        $userId  = (int) $order['user_id'];
        $txn = PaymentTransaction::findByTxnid($txnid);
        $txnId = $txn ? (int) $txn['id'] : null;

        Database::transaction(function () use (
            $orderId,
            $userId,
            $txnId,
            $disputeId,
            $disputeType,
            $disputeStatus,
            $amount,
            $reason,
            $dueDate,
            $sanitized
        ) {
            PaymentDispute::create([
                'payment_transaction_id' => $txnId,
                'order_id'               => $orderId,
                'user_id'                => $userId,
                'payu_dispute_id'        => $disputeId,
                'dispute_type'           => $disputeType,
                'dispute_status'         => $disputeStatus,
                'amount'                 => $amount,
                'currency'               => 'INR',
                'reason'                 => $reason,
                'due_date'               => $dueDate,
                'raw_payload'            => json_encode($sanitized, JSON_UNESCAPED_SLASHES),
            ]);

            Database::run("
                UPDATE orders SET gateway_status = 'disputed', updated_at = NOW()
                WHERE id = ?
            ", [$orderId]);
        });

        WebhookAuditLog::logEvent(
            'dispute',
            $url,
            'POST',
            $ip,
            $headers,
            json_encode($sanitized, JSON_UNESCAPED_SLASHES),
            200,
            'verified',
            "Recorded payment dispute for order #{$orderId}. Dispute ID: {$disputeId}"
        );

        // Send Urgent Admin Alert
        $this->sendDisputeAdminAlert($order, $disputeId, $amount, $reason, $dueDate);

        http_response_code(200);
        if (!headers_sent()) { header('Content-Type: application/json'); }
        echo json_encode([
            'status'     => 'ok',
            'message'    => 'Dispute logged for administrative review',
            'dispute_id' => $disputeId,
        ], JSON_UNESCAPED_SLASHES);
        return;
    }

    /**
     * Unified payload extractor supporting application/x-www-form-urlencoded and JSON.
     * @return array<string,mixed>
     */
    private function getRequestPayload(): array
    {
        $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw ?: '', true);
            if (is_array($data)) {
                return $data;
            }
        }

        if (!empty($_POST)) {
            return $_POST;
        }

        // Fallback: check php://input as urlencoded
        $raw = file_get_contents('php://input');
        if ($raw) {
            $parsed = [];
            parse_str($raw, $parsed);
            if (!empty($parsed)) {
                return $parsed;
            }
        }

        return [];
    }

    private function getClientIp(): string
    {
        return (string) (
            $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '127.0.0.1'
        );
    }

    private function getHeadersJson(): string
    {
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($k, 5)))));
                $headers[$name] = $v;
            }
        }
        return json_encode($headers, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Send official payment confirmation email.
     */
    private function sendPaymentSuccessEmail(array $order, array $payload, string $receiptNumber): void
    {
        try {
            $email = (string) ($payload['email'] ?? '');
            $name  = (string) ($payload['firstname'] ?? 'Student');
            if ($email === '') {
                $user = Database::first('SELECT email, name FROM users WHERE id = ?', [(int)$order['user_id']]);
                if ($user) {
                    $email = (string) $user['email'];
                    $name  = (string) ($user['name'] ?: $name);
                }
            }
            if ($email === '') {
                return;
            }

            $amountFmt = number_format((float)$order['amount'], 2);
            $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
            $receiptUrl = $appUrl . '/student/payments/' . $order['id'] . '/receipt';

            $subject = sprintf('Payment Confirmed [Receipt #%s] — The Code Munk', $receiptNumber);
            $body = sprintf(
                '<div style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#0A0A0A;color:#F5F5F0;border:1px solid rgba(255,255,255,0.1);">' .
                '<h2 style="margin-top:0;color:#FFFFFF;letter-spacing:-0.02em;">Payment Confirmation</h2>' .
                '<p>Hi %s,</p>' .
                '<p>Thank you for your enrollment with <strong>The Code Munk (TCM One)</strong>. Your payment of <strong>₹%s</strong> has been securely verified and processed via PayU.</p>' .
                '<table style="width:100%%;border-collapse:collapse;margin:24px 0;font-size:14px;background:#141414;border:1px solid rgba(255,255,255,0.08);">' .
                '<tr><td style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);">Official Receipt #:</td><td style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.08);text-align:right;font-weight:600;">%s</td></tr>' .
                '<tr><td style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);">Order Number:</td><td style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.08);text-align:right;">%s</td></tr>' .
                '<tr><td style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);">Program / Course:</td><td style="padding:12px;border-bottom:1px solid rgba(255,255,255,0.08);text-align:right;font-weight:600;">%s</td></tr>' .
                '<tr><td style="padding:12px;color:rgba(255,255,255,0.6);">Amount Paid:</td><td style="padding:12px;text-align:right;font-weight:700;color:#22C55E;">₹%s INR</td></tr>' .
                '</table>' .
                '<p style="margin:24px 0;"><a href="%s" style="background:#FFFFFF;color:#0A0A0A;padding:12px 24px;text-decoration:none;font-weight:700;display:inline-block;">View & Print Official Receipt</a></p>' .
                '<p style="font-size:12px;color:rgba(255,255,255,0.4);margin-top:32px;">The Code Munk (TCM One) &bull; Verified Merchant Integration</p>' .
                '</div>',
                htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                $amountFmt,
                htmlspecialchars($receiptNumber, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)$order['order_number'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)$order['item_title'], ENT_QUOTES, 'UTF-8'),
                $amountFmt,
                htmlspecialchars($receiptUrl, ENT_QUOTES, 'UTF-8')
            );

            Mailer::send($email, $subject, $body);
        } catch (\Throwable) {}
    }

    /**
     * Send urgent alert to admin regarding dispute.
     */
    private function sendDisputeAdminAlert(array $order, string $disputeId, float $amount, string $reason, ?string $dueDate): void
    {
        try {
            $adminEmail = (string) config('mail.from.address', 'team@thecodemunk.in');
            $subject = sprintf('URGENT: PayU Payment Dispute #%s — Order %s', $disputeId, $order['order_number']);
            $body = sprintf(
                '<h2>Payment Dispute / Chargeback Alert</h2>' .
                '<p>A payment dispute has been lodged via PayU:</p>' .
                '<ul>' .
                '<li><strong>Dispute ID:</strong> %s</li>' .
                '<li><strong>Order Number:</strong> %s</li>' .
                '<li><strong>Course / Item:</strong> %s</li>' .
                '<li><strong>Amount:</strong> ₹%s</li>' .
                '<li><strong>Reason:</strong> %s</li>' .
                '<li><strong>Response Due Date:</strong> %s</li>' .
                '</ul>' .
                '<p>Please log in to your PayU Merchant Dashboard immediately to submit proof of fulfillment.</p>',
                htmlspecialchars($disputeId, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)$order['order_number'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)$order['item_title'], ENT_QUOTES, 'UTF-8'),
                number_format($amount, 2),
                htmlspecialchars($reason, ENT_QUOTES, 'UTF-8'),
                $dueDate ? htmlspecialchars($dueDate, ENT_QUOTES, 'UTF-8') : 'N/A'
            );

            Mailer::send($adminEmail, $subject, $body);
        } catch (\Throwable) {}
    }
}
