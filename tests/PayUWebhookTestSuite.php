<?php
/**
 * Automated Test Matrix: PayU Webhook & Installments System
 *
 * Runs 9 comprehensive security, idempotency, fulfillment, out-of-order,
 * installments, refund, and dispute test scenarios.
 *
 * Usage:
 *   php tests/PayUWebhookTestSuite.php
 */

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use TCM\Controllers\PayUWebhookController;
use TCM\Core\Database;
use TCM\Core\PayU;
use TCM\Models\Course;
use TCM\Models\Enrollment;
use TCM\Models\InstallmentSchedule;
use TCM\Models\Order;
use TCM\Models\PaymentDispute;
use TCM\Models\PaymentRefund;
use TCM\Models\PaymentTransaction;
use TCM\Models\User;
use TCM\Models\WebhookAuditLog;
use TCM\Services\InstallmentService;

class PayUWebhookTestSuite
{
    private int $passed = 0;
    private int $failed = 0;
    private int $testUserId = 0;
    private int $testCourseId = 0;

    public function run(): void
    {
        echo "=========================================================\n";
        echo "   TCM One — PayU Webhook & Installments Test Matrix     \n";
        echo "=========================================================\n\n";

        $this->setupFixtures();

        $this->test1_SignatureReverseHashVerification();
        $this->test2_ForgedSignatureRejection();
        $this->test3_LegitimatePaymentSuccessFulfillment();
        $this->test4_IdempotencyDuplicateSuccessProtection();
        $this->test5_OutOfOrderFailureProtection();
        $this->test6_LegitimatePaymentFailureHandling();
        $this->test7_InstallmentPlanAndMilestonePayment();
        $this->test8_RefundWebhookProcessing();
        $this->test9_DisputeWebhookProcessing();

        echo "\n=========================================================\n";
        echo "   TEST SUMMARY: {$this->passed} Passed, {$this->failed} Failed\n";
        echo "=========================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function setupFixtures(): void
    {
        // 1. Ensure test student user exists
        $user = Database::first("SELECT id FROM users WHERE email = 'test_student_payu@thecodemunk.in'");
        if ($user) {
            $this->testUserId = (int) $user['id'];
        } else {
            Database::run("
                INSERT INTO users (name, email, password_hash, role, status, email_verified)
                VALUES ('Test PayU Student', 'test_student_payu@thecodemunk.in', 'dummy', 'student', 'active', 1)
            ");
            $this->testUserId = (int) Database::scalar("SELECT LAST_INSERT_ID()");
        }

        // 2. Ensure test course exists
        $course = Database::first("SELECT id FROM courses LIMIT 1");
        if ($course) {
            $this->testCourseId = (int) $course['id'];
        } else {
            Database::run("
                INSERT INTO courses (title, slug, description, price, duration, level, status)
                VALUES ('Test Automation Mastery', 'test-automation-mastery', 'Automated test fixture', 4999.00, '4 weeks', 'Beginner', 'published')
            ");
            $this->testCourseId = (int) Database::scalar("SELECT LAST_INSERT_ID()");
        }
    }

    /**
     * Test 1: Verify correct PayU reverse SHA-512 calculation.
     */
    private function test1_SignatureReverseHashVerification(): void
    {
        $payload = [
            'key'         => PayU::key(),
            'txnid'       => 'TEST_TXN_001',
            'amount'      => '99.00',
            'productinfo' => 'Test Product',
            'firstname'   => 'Test',
            'email'       => 'test@thecodemunk.in',
            'status'      => 'success',
            'udf1'        => 'course',
            'udf2'        => '1',
            'udf3'        => '',
            'udf4'        => '1',
            'udf5'        => 'TCM-ORDER-001',
        ];

        // Reverse hash formula: sha512(salt|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
        $salt = PayU::salt();
        $seq = sprintf(
            '%s|%s||||||%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s',
            $salt,
            $payload['status'],
            $payload['udf5'],
            $payload['udf4'],
            $payload['udf3'],
            $payload['udf2'],
            $payload['udf1'],
            $payload['email'],
            $payload['firstname'],
            $payload['productinfo'],
            $payload['amount'],
            $payload['txnid'],
            $payload['key']
        );
        $payload['hash'] = strtolower(hash('sha512', $seq));

        $valid = PayU::verifyResponseHash($payload);
        $this->assert($valid, 'Test 1: Reverse SHA-512 hash calculation matches PayU specification.');
    }

    /**
     * Test 2: Forged / tampered hash must be rejected with HTTP 400.
     */
    private function test2_ForgedSignatureRejection(): void
    {
        $payload = [
            'key'         => PayU::key(),
            'txnid'       => 'TEST_FORGED_002',
            'amount'      => '99.00',
            'productinfo' => 'Test',
            'firstname'   => 'Test',
            'email'       => 'forged@test.com',
            'status'      => 'success',
            'hash'        => 'deadbeef1234567890abcdef', // Invalid hash
        ];

        $res = $this->callWebhook('success', $payload);
        $this->assert(
            $res['code'] === 400 && str_contains($res['body'], 'Invalid signature hash'),
            'Test 2: Forged signature hash is securely rejected with HTTP 400.'
        );
    }

    /**
     * Test 3: Valid payment success transitions order to paid, generates receipt, and grants enrollment.
     */
    private function test3_LegitimatePaymentSuccessFulfillment(): void
    {
        $txnid = 'TCM_TEST_' . time() . '_A';
        $orderNumber = Order::generateNumber();

        $orderId = Order::create([
            'user_id'          => $this->testUserId,
            'order_number'     => $orderNumber,
            'item_type'        => 'course',
            'item_id'          => $this->testCourseId,
            'item_title'       => 'Test Course',
            'amount'           => 99.00,
            'paid_amount'      => 0.00,
            'remaining_amount' => 99.00,
            'currency'         => 'INR',
            'status'           => 'pending',
            'gateway_status'   => 'initiated',
            'payment_method'   => 'payu',
            'payment_ref'      => $txnid,
            'txnid'            => $txnid,
        ]);

        $payload = [
            'key'          => PayU::key(),
            'txnid'        => $txnid,
            'amount'       => '99.00',
            'productinfo'  => 'Test Course',
            'firstname'    => 'Student',
            'email'        => 'test_student_payu@thecodemunk.in',
            'status'       => 'success',
            'mihpayid'     => 'MIH_' . bin2hex(random_bytes(4)),
            'bank_ref_num' => 'BANK_REF_12345',
            'mode'         => 'UPI',
            'udf1'         => 'course',
            'udf2'         => (string) $this->testCourseId,
            'udf3'         => '',
            'udf4'         => (string) $this->testUserId,
            'udf5'         => $orderNumber,
        ];
        $payload['hash'] = $this->calculateReverseHash($payload);

        $res = $this->callWebhook('success', $payload);

        // Verify Order
        $order = Order::find($orderId);
        $txn = PaymentTransaction::findByTxnid($txnid);
        $enrollment = Database::first("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?", [$this->testUserId, $this->testCourseId]);

        $this->assert(
            $res['code'] === 200 &&
            $order['status'] === 'paid' &&
            !empty($order['receipt_number']) &&
            $txn !== null && $txn['status'] === 'success' &&
            $enrollment !== null && $enrollment['status'] === 'active',
            'Test 3: Authentic success webhook confirms order, creates receipt, records transaction, and activates enrollment.'
        );
    }

    /**
     * Test 4: Idempotency Protection - duplicate webhooks must acknowledge 200 without duplicate enrollments or errors.
     */
    private function test4_IdempotencyDuplicateSuccessProtection(): void
    {
        $txnid = 'TCM_TEST_' . time() . '_IDEMP';
        $orderNumber = Order::generateNumber();

        $orderId = Order::create([
            'user_id'          => $this->testUserId,
            'order_number'     => $orderNumber,
            'item_type'        => 'course',
            'item_id'          => $this->testCourseId,
            'item_title'       => 'Idempotency Test Course',
            'amount'           => 199.00,
            'paid_amount'      => 199.00,
            'remaining_amount' => 0.00,
            'currency'         => 'INR',
            'status'           => 'paid',
            'gateway_status'   => 'success',
            'receipt_number'   => 'TCM-REC-EXISTING',
            'payment_ref'      => 'MIH_EXISTING',
            'txnid'            => $txnid,
        ]);

        $payload = [
            'key'          => PayU::key(),
            'txnid'        => $txnid,
            'amount'       => '199.00',
            'productinfo'  => 'Idempotency Test Course',
            'firstname'    => 'Student',
            'email'        => 'test_student_payu@thecodemunk.in',
            'status'       => 'success',
            'mihpayid'     => 'MIH_EXISTING',
            'udf1'         => 'course',
            'udf2'         => (string) $this->testCourseId,
            'udf3'         => '',
            'udf4'         => (string) $this->testUserId,
            'udf5'         => $orderNumber,
        ];
        $payload['hash'] = $this->calculateReverseHash($payload);

        $res = $this->callWebhook('success', $payload);

        $this->assert(
            $res['code'] === 200 && str_contains($res['body'], 'already confirmed'),
            'Test 4: Idempotency protection cleanly acknowledges duplicate webhook without double-processing.'
        );
    }

    /**
     * Test 5: Out-of-Order Failure Protection - delayed failure callback must NEVER downgrade an already paid order.
     */
    private function test5_OutOfOrderFailureProtection(): void
    {
        $txnid = 'TCM_TEST_' . time() . '_OUTOFORDER';
        $orderNumber = Order::generateNumber();

        $orderId = Order::create([
            'user_id'          => $this->testUserId,
            'order_number'     => $orderNumber,
            'item_type'        => 'course',
            'item_id'          => $this->testCourseId,
            'item_title'       => 'Protected Course',
            'amount'           => 499.00,
            'paid_amount'      => 499.00,
            'remaining_amount' => 0.00,
            'currency'         => 'INR',
            'status'           => 'paid',
            'gateway_status'   => 'success',
            'receipt_number'   => 'TCM-REC-PROTECTED',
            'txnid'            => $txnid,
        ]);

        $payload = [
            'txnid'         => $txnid,
            'udf5'          => $orderNumber,
            'status'        => 'failure',
            'error_Message' => 'Delayed bank timeout notification',
        ];

        $res = $this->callWebhook('failed', $payload);
        $order = Order::find($orderId);

        $this->assert(
            $res['code'] === 200 &&
            $order['status'] === 'paid' && // PRESERVED!
            str_contains($res['body'], 'already confirmed as paid'),
            'Test 5: Out-of-order protection preserves paid status against late failure callbacks.'
        );
    }

    /**
     * Test 6: Legitimate Failure Callback marks pending order as failed.
     */
    private function test6_LegitimatePaymentFailureHandling(): void
    {
        $txnid = 'TCM_TEST_' . time() . '_LEGIT_FAIL';
        $orderNumber = Order::generateNumber();

        $orderId = Order::create([
            'user_id'          => $this->testUserId,
            'order_number'     => $orderNumber,
            'item_type'        => 'course',
            'item_id'          => $this->testCourseId,
            'item_title'       => 'Failed Test Course',
            'amount'           => 149.00,
            'currency'         => 'INR',
            'status'           => 'pending',
            'gateway_status'   => 'initiated',
            'txnid'            => $txnid,
        ]);

        $payload = [
            'txnid'         => $txnid,
            'udf5'          => $orderNumber,
            'status'        => 'failure',
            'error_Message' => 'User cancelled payment on bank portal',
        ];

        $res = $this->callWebhook('failed', $payload);
        $order = Order::find($orderId);

        $this->assert(
            $res['code'] === 200 && $order['status'] === 'failed',
            'Test 6: Legitimate payment failure transitions pending order to failed.'
        );
    }

    /**
     * Test 7: Installment Engine - Milestone payment partial balance update.
     */
    private function test7_InstallmentPlanAndMilestonePayment(): void
    {
        $parentNumber = Order::generateNumber();
        $parentOrderId = Order::create([
            'user_id'            => $this->testUserId,
            'order_number'       => $parentNumber,
            'item_type'          => 'course',
            'item_id'            => $this->testCourseId,
            'item_title'         => 'Full Stack Bootcamp',
            'amount'             => 10000.00,
            'paid_amount'        => 0.00,
            'remaining_amount'   => 10000.00,
            'currency'           => 'INR',
            'status'             => 'pending',
            'gateway_status'     => 'installment_plan',
            'payment_method'     => 'payu',
            'payment_ref'        => 'PLAN_TEST',
            'is_installment'     => 1,
            'total_installments' => 2,
        ]);

        // Create 2 installments of ₹5,000 each
        $planRes = InstallmentService::createPlan($parentOrderId, $this->testUserId, $this->testCourseId, 10000.00, 2);
        $scheduleIds = is_array($planRes) && isset($planRes['schedule_ids']) ? $planRes['schedule_ids'] : $planRes;
        $inst1Id = $scheduleIds[0];

        // Pay milestone 1
        $inst1Txn = 'TCM_INST_' . time() . '_M1';
        $inst1Mih = 'MIH_INST_001';
        InstallmentService::markInstallmentPaid($inst1Id, $inst1Txn, $inst1Mih, 5000.00);

        $inst1 = InstallmentSchedule::find($inst1Id);
        $parent = Order::find($parentOrderId);

        $this->assert(
            $inst1['status'] === 'paid' &&
            (float) $parent['paid_amount'] === 5000.00 &&
            (float) $parent['remaining_amount'] === 5000.00 &&
            $parent['status'] === 'pending', // NOT fully paid yet!
            'Test 7: Installment milestone paid updates paid/remaining balance; partial state preserved.'
        );
    }

    /**
     * Test 8: Refund Webhook updates balance and deactivates access on full refund.
     */
    private function test8_RefundWebhookProcessing(): void
    {
        $txnid = 'TCM_REF_' . time() . '_TXN';
        $orderNumber = Order::generateNumber();

        $orderId = Order::create([
            'user_id'          => $this->testUserId,
            'order_number'     => $orderNumber,
            'item_type'        => 'course',
            'item_id'          => $this->testCourseId,
            'item_title'       => 'Refundable Course',
            'amount'           => 1000.00,
            'paid_amount'      => 1000.00,
            'remaining_amount' => 0.00,
            'currency'         => 'INR',
            'status'           => 'paid',
            'txnid'            => $txnid,
        ]);

        $payload = [
            'txnid'          => $txnid,
            'payu_refund_id' => 'PAYU_REF_' . bin2hex(random_bytes(4)),
            'amount'         => '1000.00',
            'status'         => 'success',
            'reason'         => 'Student requested cancellation within policy',
        ];

        $res = $this->callWebhook('refund', $payload);
        $order = Order::find($orderId);
        $refund = PaymentRefund::findBy(['order_id' => $orderId]);

        $this->assert(
            $res['code'] === 200 &&
            $order['status'] === 'refunded' &&
            $refund !== null && (float) $refund['amount'] === 1000.00,
            'Test 8: Full refund marks order as refunded and persists audit refund record.'
        );
    }

    /**
     * Test 9: Dispute Webhook records dispute and alerts administration.
     */
    private function test9_DisputeWebhookProcessing(): void
    {
        $txnid = 'TCM_DISP_' . time() . '_TXN';
        $orderNumber = Order::generateNumber();

        $orderId = Order::create([
            'user_id'          => $this->testUserId,
            'order_number'     => $orderNumber,
            'item_type'        => 'course',
            'item_id'          => $this->testCourseId,
            'item_title'       => 'Disputed Course',
            'amount'           => 2500.00,
            'paid_amount'      => 2500.00,
            'remaining_amount' => 0.00,
            'currency'         => 'INR',
            'status'           => 'paid',
            'txnid'            => $txnid,
        ]);

        $payload = [
            'txnid'          => $txnid,
            'dispute_id'     => 'DISP_' . bin2hex(random_bytes(4)),
            'dispute_type'   => 'chargeback',
            'dispute_status' => 'open',
            'amount'         => '2500.00',
            'reason'         => 'Cardholder unrecognized transaction',
            'due_date'       => date('Y-m-d', strtotime('+7 days')),
        ];

        $res = $this->callWebhook('dispute', $payload);
        $order = Order::find($orderId);
        $dispute = PaymentDispute::findBy(['order_id' => $orderId]);

        $this->assert(
            $res['code'] === 200 &&
            $order['gateway_status'] === 'disputed' &&
            $dispute !== null && (float) $dispute['amount'] === 2500.00,
            'Test 9: Dispute webhook logs chargeback for review and flags order gateway status.'
        );
    }

    // ── Helper Utilities ─────────────────────────────────────────────

    private function calculateReverseHash(array $p): string
    {
        $salt = PayU::salt();
        $seq = sprintf(
            '%s|%s||||||%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s',
            $salt,
            $p['status'],
            $p['udf5'] ?? '',
            $p['udf4'] ?? '',
            $p['udf3'] ?? '',
            $p['udf2'] ?? '',
            $p['udf1'] ?? '',
            $p['email'] ?? '',
            $p['firstname'] ?? '',
            $p['productinfo'] ?? '',
            $p['amount'] ?? '',
            $p['txnid'] ?? '',
            $p['key'] ?? PayU::key()
        );
        return strtolower(hash('sha512', $seq));
    }

    /**
     * Invoke PayUWebhookController action directly capturing output and status code.
     * @return array{code:int,body:string}
     */
    private function callWebhook(string $action, array $postData): array
    {
        $_POST = $postData;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/webhooks/payu/' . $action;
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $controller = new PayUWebhookController();

        ob_start();
        try {
            $controller->$action();
        } catch (\Throwable $e) {
            // Controller exits on exit/die, or outputs JSON
        }
        $body = ob_get_clean();
        $code = http_response_code() ?: 200;

        return ['code' => $code, 'body' => (string) $body];
    }

    private function assert(bool $condition, string $message): void
    {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$message}\n";
        } else {
            $this->failed++;
            echo "  [FAIL] {$message}\n";
        }
    }
}

// Execute Suite
$suite = new PayUWebhookTestSuite();
$suite->run();
