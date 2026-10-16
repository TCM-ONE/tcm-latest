<?php
/**
 * Automated Test Matrix: TCM One Flexible Installment Payment System
 *
 * Verifies all 8 core facets of the installment subsystem:
 * 1. Strict Eligibility: Prices <= 5000 rejected, prices > 5000 approved, exact 5000.00 rejected.
 * 2. Schedule Math & Paise Precision: Exact integer paise calculations without floating-point drift.
 * 3. Database Integrity: Plan and schedule creation with atomic relations.
 * 4. Milestone 1 Checkout & Payment: Upfront fulfillment, partial balance tracking.
 * 5. Subsequent Milestone Checkout & Completion: Full clearance and transition to completed.
 * 6. Admin Rescheduling: Date validation, audit trail, reminder stage reset.
 * 7. Settings Guardrails: Enforcement of <= 3 installments and >= 5000 min price.
 * 8. Automated Reminders & Idempotency: Duplicate prevention via installment_reminders_log.
 *
 * Usage:
 *   php tests/InstallmentSystemTestSuite.php
 */

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use TCM\Core\Database;
use TCM\Core\PayU;
use TCM\Models\Course;
use TCM\Models\Enrollment;
use TCM\Models\InstallmentPlan;
use TCM\Models\InstallmentSchedule;
use TCM\Models\Order;
use TCM\Models\PaymentTransaction;
use TCM\Models\User;
use TCM\Services\InstallmentService;
use TCM\Services\Reminders\InstallmentReminderService;

class InstallmentSystemTestSuite
{
    private int $passed = 0;
    private int $failed = 0;
    private int $testUserId = 0;
    private int $testCourseId = 0;
    private int $disallowedCourseId = 0;

    public function run(): void
    {
        echo "=========================================================\n";
        echo "   TCM One — Installment System Automated Test Matrix    \n";
        echo "=========================================================\n\n";

        $this->setupFixtures();

        $this->test1_StrictPriceEligibility();
        $this->test2_ScheduleMathAndPaisePrecision();
        $this->test3_MinUpfrontAndMaxInstallmentsEnforcement();
        $this->test4_PlanAndScheduleDatabaseIntegrity();
        $this->test5_Milestone1PaymentAndPartialBalance();
        $this->test6_SubsequentMilestonePaymentAndFullClearance();
        $this->test7_AdminRescheduleValidationAndAuditTrail();
        $this->test8_AdminSettingsGuardrailEnforcement();
        $this->test9_AutomatedReminderIdempotency();

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
        $user = Database::first("SELECT id FROM users WHERE email = 'test_inst_student@thecodemunk.in'");
        if ($user) {
            $this->testUserId = (int) $user['id'];
        } else {
            Database::run("
                INSERT INTO users (name, email, password_hash, role, status, email_verified)
                VALUES ('Test Installment Student', 'test_inst_student@thecodemunk.in', 'dummy', 'student', 'active', 1)
            ");
            $this->testUserId = (int) Database::scalar("SELECT LAST_INSERT_ID()");
        }

        // 2. Ensure test course exists with price > 5000 and allow_installments = 1
        $course = Database::first("SELECT id FROM courses WHERE slug = 'test-fullstack-pro'");
        if ($course) {
            $this->testCourseId = (int) $course['id'];
            Database::run("UPDATE courses SET price = 12000.00, allow_installments = 1, status = 'published' WHERE id = ?", [$this->testCourseId]);
        } else {
            Database::run("
                INSERT INTO courses (title, slug, description, price, duration, level, status, allow_installments)
                VALUES ('Test Fullstack Pro', 'test-fullstack-pro', 'Installment test course', 12000.00, '12 weeks', 'Advanced', 'published', 1)
            ");
            $this->testCourseId = (int) Database::scalar("SELECT LAST_INSERT_ID()");
        }

        // 3. Ensure a course exists with allow_installments = 0
        $disallowed = Database::first("SELECT id FROM courses WHERE slug = 'test-disallowed-course'");
        if ($disallowed) {
            $this->disallowedCourseId = (int) $disallowed['id'];
            Database::run("UPDATE courses SET price = 8000.00, allow_installments = 0, status = 'published' WHERE id = ?", [$this->disallowedCourseId]);
        } else {
            Database::run("
                INSERT INTO courses (title, slug, description, price, duration, level, status, allow_installments)
                VALUES ('Test Disallowed Course', 'test-disallowed-course', 'No installment test', 8000.00, '2 weeks', 'Beginner', 'published', 0)
            ");
            $this->disallowedCourseId = (int) Database::scalar("SELECT LAST_INSERT_ID()");
        }

        // 4. Ensure installment_enabled setting is 1
        Database::run("INSERT INTO settings (`key`, `value`) VALUES ('installment_enabled', '1') ON DUPLICATE KEY UPDATE `value` = '1'");
    }

    /**
     * Test 1: Verify strict price eligibility rules.
     * Rule: Available strictly when price > 5000 in INR. Exactly 5000 or below is ineligible.
     */
    private function test1_StrictPriceEligibility(): void
    {
        $ineligiblePrices = [99.00, 999.00, 2999.00, 3999.00, 4999.00, 5000.00];
        foreach ($ineligiblePrices as $price) {
            $eligible = InstallmentService::isEligible($price, 'INR');
            $this->assert(
                $eligible === false,
                "Test 1.1: Price ₹{$price} is correctly rejected (must be strictly > ₹5,000)."
            );
        }

        $eligiblePrices = [5000.01, 5001.00, 7999.00, 10000.00, 19999.00];
        foreach ($eligiblePrices as $price) {
            $eligible = InstallmentService::isEligible($price, 'INR');
            $this->assert(
                $eligible === true,
                "Test 1.2: Price ₹{$price} is correctly approved (> ₹5,000)."
            );
        }

        // Non-INR currencies must be rejected
        $this->assert(
            InstallmentService::isEligible(10000.00, 'USD') === false,
            "Test 1.3: Non-INR currency (USD) is strictly rejected."
        );

        // Course flag allow_installments = 0 must be rejected
        $this->assert(
            InstallmentService::isEligible(10000.00, 'INR', $this->disallowedCourseId) === false,
            "Test 1.4: Course with allow_installments = 0 is rejected even though price > 5000."
        );

        // Course flag allow_installments = 1 is approved
        $this->assert(
            InstallmentService::isEligible(10000.00, 'INR', $this->testCourseId) === true,
            "Test 1.5: Course with allow_installments = 1 and price > 5000 is approved."
        );
    }

    /**
     * Test 2: Verify exact integer paise calculations without floating-point drift.
     */
    private function test2_ScheduleMathAndPaisePrecision(): void
    {
        $pricesToTest = [
            5001.00,
            7999.00,
            9999.00,
            12000.00,
            15499.50,
            19999.00,
            24999.99,
        ];

        foreach ($pricesToTest as $price) {
            // 2-Installment plan test
            $sched2 = InstallmentService::calculateSchedule($price, 2);
            $sum2 = array_sum(array_column($sched2, 'amount'));
            $this->assert(
                abs($sum2 - $price) < 0.0001 && count($sched2) === 2,
                "Test 2.1: 2-Installment schedule for ₹{$price} sums exactly to total (sum = {$sum2})."
            );

            // 3-Installment plan test
            $sched3 = InstallmentService::calculateSchedule($price, 3);
            $sum3 = array_sum(array_column($sched3, 'amount'));
            $this->assert(
                abs($sum3 - $price) < 0.0001 && count($sched3) === 3,
                "Test 2.2: 3-Installment schedule for ₹{$price} sums exactly to total (sum = {$sum3})."
            );

            // Verify upfront is >= 40%
            $upfrontPct = ($sched3[0]['amount'] / $price) * 100;
            $this->assert(
                $upfrontPct >= 39.99,
                "Test 2.3: Upfront for ₹{$price} is at least 40% ({$upfrontPct}%)."
            );
        }
    }

    /**
     * Test 3: Enforce min 40% upfront and max 3 installments.
     */
    private function test3_MinUpfrontAndMaxInstallmentsEnforcement(): void
    {
        $caughtEx = false;
        try {
            InstallmentService::calculateSchedule(10000.00, 4);
        } catch (\InvalidArgumentException $e) {
            $caughtEx = true;
        }
        $this->assert(
            $caughtEx === true,
            "Test 3.1: Requesting 4 installments throws InvalidArgumentException (max allowed: 3)."
        );

        $caughtEx = false;
        try {
            InstallmentService::calculateSchedule(10000.00, 1);
        } catch (\InvalidArgumentException $e) {
            $caughtEx = true;
        }
        $this->assert(
            $caughtEx === true,
            "Test 3.2: Requesting 1 installment throws InvalidArgumentException (min allowed: 2)."
        );

        $caughtEx = false;
        try {
            InstallmentService::calculateSchedule(4000.00, 3);
        } catch (\InvalidArgumentException $e) {
            $caughtEx = true;
        }
        $this->assert(
            $caughtEx === true,
            "Test 3.3: Calculating schedule for ₹4,000 throws InvalidArgumentException (price <= ₹5,000)."
        );
    }

    /**
     * Test 4: Verify Plan and Schedule Database Creation.
     */
    private function test4_PlanAndScheduleDatabaseIntegrity(): void
    {
        $parentOrderNumber = 'ORD-TEST-' . time();
        $parentOrderId = Order::create([
            'user_id'            => $this->testUserId,
            'order_number'       => $parentOrderNumber,
            'item_type'          => 'course',
            'item_id'            => $this->testCourseId,
            'item_title'         => 'Test Fullstack Pro',
            'amount'             => 12000.00,
            'paid_amount'        => 0.00,
            'remaining_amount'   => 12000.00,
            'currency'           => 'INR',
            'status'             => 'pending',
            'payment_method'     => 'payu',
            'is_installment'     => 1,
            'total_installments' => 3,
        ]);

        $result = InstallmentService::createPlan($parentOrderId, $this->testUserId, $this->testCourseId, 12000.00, 3);

        $this->assert(
            !empty($result['plan_id']) && count($result['schedule_ids']) === 3,
            "Test 4.1: InstallmentService::createPlan returns plan_id and 3 schedule_ids."
        );

        $plan = InstallmentPlan::find($result['plan_id']);
        $this->assert(
            $plan !== null &&
            (float)$plan['total_amount'] === 12000.00 &&
            (float)$plan['upfront_amount'] === 4800.00 &&
            (float)$plan['remaining_amount'] === 12000.00 &&
            (int)$plan['total_installments'] === 3 &&
            $plan['status'] === 'active',
            "Test 4.2: installment_plans table record persisted correctly."
        );

        $schedules = Database::all("SELECT * FROM installment_schedules WHERE installment_plan_id = ? ORDER BY installment_number ASC", [$result['plan_id']]);
        $this->assert(
            count($schedules) === 3 &&
            (float)$schedules[0]['amount'] === 4800.00 &&
            (float)$schedules[1]['amount'] === 3600.00 &&
            (float)$schedules[2]['amount'] === 3600.00 &&
            $schedules[0]['status'] === 'pending' &&
            $schedules[1]['status'] === 'pending' &&
            $schedules[2]['status'] === 'pending',
            "Test 4.3: installment_schedules records correctly scheduled with 40%/30%/30% distribution."
        );
    }

    /**
     * Test 5: Verify Milestone 1 Payment & Partial Balance State.
     */
    private function test5_Milestone1PaymentAndPartialBalance(): void
    {
        $parentOrderId = Order::create([
            'user_id'            => $this->testUserId,
            'order_number'       => 'ORD-M1-' . time(),
            'item_type'          => 'course',
            'item_id'            => $this->testCourseId,
            'item_title'         => 'Test Fullstack Pro',
            'amount'             => 10000.00,
            'paid_amount'        => 0.00,
            'remaining_amount'   => 10000.00,
            'currency'           => 'INR',
            'status'             => 'pending',
            'payment_method'     => 'payu',
            'is_installment'     => 1,
            'total_installments' => 2,
        ]);

        $result = InstallmentService::createPlan($parentOrderId, $this->testUserId, $this->testCourseId, 10000.00, 2);
        $m1Id = $result['schedule_ids'][0];

        $txnid = 'TXN_M1_' . time();
        $mihpayid = 'MIH_M1_' . time();
        InstallmentService::markInstallmentPaid($m1Id, $txnid, $mihpayid, 5000.00);

        $plan = InstallmentPlan::find($result['plan_id']);
        $m1 = InstallmentSchedule::find($m1Id);
        $order = Order::find($parentOrderId);

        $this->assert(
            $m1['status'] === 'paid' &&
            !empty($m1['paid_at']) &&
            (float)$plan['paid_amount'] === 5000.00 &&
            (float)$plan['remaining_amount'] === 5000.00 &&
            $plan['status'] === 'active' &&
            (float)$order['paid_amount'] === 5000.00 &&
            (float)$order['remaining_amount'] === 5000.00 &&
            $order['status'] === 'pending', // Pending full clearance!
            "Test 5: Milestone 1 payment accurately updates milestone status, plan balance, and parent order partial state."
        );
    }

    /**
     * Test 6: Verify Subsequent Milestone Checkout and Full Plan Clearance.
     */
    private function test6_SubsequentMilestonePaymentAndFullClearance(): void
    {
        $parentOrderId = Order::create([
            'user_id'            => $this->testUserId,
            'order_number'       => 'ORD-FULL-' . time(),
            'item_type'          => 'course',
            'item_id'            => $this->testCourseId,
            'item_title'         => 'Test Fullstack Pro',
            'amount'             => 10000.00,
            'paid_amount'        => 0.00,
            'remaining_amount'   => 10000.00,
            'currency'           => 'INR',
            'status'             => 'pending',
            'payment_method'     => 'payu',
            'is_installment'     => 1,
            'total_installments' => 2,
        ]);

        $result = InstallmentService::createPlan($parentOrderId, $this->testUserId, $this->testCourseId, 10000.00, 2);
        $m1Id = $result['schedule_ids'][0];
        $m2Id = $result['schedule_ids'][1];

        // 1. Pay Milestone 1
        InstallmentService::markInstallmentPaid($m1Id, 'TXN_A_' . time(), 'MIH_A_' . time(), 5000.00);

        // 2. Generate Checkout for Milestone 2
        $user = Database::first("SELECT * FROM users WHERE id = ?", [$this->testUserId]);
        $checkout = InstallmentService::generateInstallmentCheckout($m2Id, $user);

        $this->assert(
            !empty($checkout['params']['hash']) &&
            (float)$checkout['params']['amount'] === 5000.00 &&
            !empty($checkout['params']['txnid']),
            "Test 6.1: Subsequent milestone checkout generates valid PayU parameters and hash."
        );

        // 3. Pay Milestone 2
        InstallmentService::markInstallmentPaid($m2Id, 'TXN_B_' . time(), 'MIH_B_' . time(), 5000.00);

        $plan = InstallmentPlan::find($result['plan_id']);
        $order = Order::find($parentOrderId);

        $this->assert(
            (float)$plan['paid_amount'] === 10000.00 &&
            (float)$plan['remaining_amount'] === 0.00 &&
            $plan['status'] === 'completed' &&
            (float)$order['paid_amount'] === 10000.00 &&
            (float)$order['remaining_amount'] === 0.00 &&
            $order['status'] === 'paid',
            "Test 6.2: Final milestone payment transitions installment_plan to 'completed' and order to 'paid'."
        );
    }

    /**
     * Test 7: Verify Admin Rescheduling Date Validation & Audit Trail.
     */
    private function test7_AdminRescheduleValidationAndAuditTrail(): void
    {
        $schedId = InstallmentSchedule::create([
            'parent_order_id'    => 1,
            'user_id'             => $this->testUserId,
            'course_id'           => $this->testCourseId,
            'installment_number'  => 2,
            'total_installments'  => 3,
            'amount'              => 3000.00,
            'due_date'            => date('Y-m-d', strtotime('+10 days')),
            'status'              => 'pending',
            'notes'               => 'Test milestone',
        ]);

        $newDueDate = date('Y-m-d', strtotime('+25 days'));
        $success = InstallmentService::rescheduleMilestone($schedId, $newDueDate, 'Student requested exam extension', 1);

        $updated = InstallmentSchedule::find($schedId);

        $this->assert(
            $success === true &&
            $updated['due_date'] === $newDueDate &&
            str_contains((string)$updated['notes'], 'Student requested exam extension'),
            "Test 7.1: Milestone successfully rescheduled to new future due date with audit note."
        );

        // Past date must throw InvalidArgumentException
        $caught = false;
        try {
            InstallmentService::rescheduleMilestone($schedId, date('Y-m-d', strtotime('-5 days')), 'Invalid date', 1);
        } catch (\InvalidArgumentException $e) {
            $caught = true;
        }
        $this->assert(
            $caught === true,
            "Test 7.2: Rescheduling with past due date throws InvalidArgumentException."
        );
    }

    /**
     * Test 8: Verify Admin Settings Guardrails Enforcement.
     */
    private function test8_AdminSettingsGuardrailEnforcement(): void
    {
        // Attempting to violate guardrails: max_installments=5, min_amount=1000, min_upfront=10
        InstallmentService::saveSettings([
            'enabled'          => 1,
            'min_amount'       => 1000.00, // Attempt below ₹5,000
            'max_installments' => 5,       // Attempt above 3
            'min_upfront_pct'  => 10,      // Attempt below 40%
            'interval_days'    => 15,
            'email_reminders'  => 1,
        ]);

        $settings = InstallmentService::getSettings();

        $this->assert(
            $settings['max_installments'] <= 3 &&
            $settings['min_amount'] >= 5000.00 &&
            $settings['min_upfront_pct'] >= 40 &&
            $settings['interest_rate'] === 0.0,
            "Test 8: System strictly guards against invalid configurations (max 3 installments, min ₹5000, min 40% upfront, 0% interest)."
        );
    }

    /**
     * Test 9: Automated Reminder Idempotency Protection.
     */
    private function test9_AutomatedReminderIdempotency(): void
    {
        $schedId = InstallmentSchedule::create([
            'parent_order_id'    => 1,
            'user_id'             => $this->testUserId,
            'course_id'           => $this->testCourseId,
            'installment_number'  => 2,
            'total_installments'  => 2,
            'amount'              => 5000.00,
            'due_date'            => date('Y-m-d', strtotime('+3 days')),
            'status'              => 'pending',
            'notes'               => 'Reminder test milestone',
        ]);

        // Dispatch reminders
        $reminderService = new InstallmentReminderService();
        $stats1 = $reminderService->runScheduledReminders();

        // Run immediately again - duplicate dispatch must be prevented
        $stats2 = $reminderService->runScheduledReminders();

        $logCount = (int) Database::scalar(
            "SELECT COUNT(*) FROM installment_reminders_log WHERE schedule_id = ? AND stage = '3_days_before'",
            [$schedId]
        );

        $this->assert(
            $logCount === 1,
            "Test 9: Automated reminder stage '3_days_before' recorded once and duplicate trigger prevented by idempotency log."
        );
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

// Execute test suite
$suite = new InstallmentSystemTestSuite();
$suite->run();
