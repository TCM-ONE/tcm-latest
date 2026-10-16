<?php

declare(strict_types=1);

namespace TCM\Services;

use TCM\Core\Database;
use TCM\Core\PayU;
use TCM\Models\Course;
use TCM\Models\Enrollment;
use TCM\Models\InstallmentPlan;
use TCM\Models\InstallmentSchedule;
use TCM\Models\Order;
use TCM\Models\User;
use TCM\Models\WebhookAuditLog;

final class InstallmentService
{
    /** Mandatory strict minimum course price for installments (exclusive: price must be > 5000) */
    public const STRICT_MIN_ELIGIBLE_AMOUNT = 5000.00;

    /** Mandatory maximum allowed installments per enrollment */
    public const STRICT_MAX_INSTALLMENTS = 3;

    /** Mandatory minimum upfront percentage */
    public const STRICT_MIN_UPFRONT_PERCENT = 40;

    /**
     * Check if a course price is eligible for installment payments.
     * STRICT CONDITION: finalPayableAmount > 5000
     */
    public static function isEligible(float $coursePrice, string $currency = 'INR', ?int $courseId = null): bool
    {
        // Must be in INR
        if (strtoupper(trim($currency)) !== 'INR') {
            return false;
        }

        // Strict rule: Price must be strictly greater than 5000 (5000.00 exact is NOT eligible)
        if ($coursePrice <= self::STRICT_MIN_ELIGIBLE_AMOUNT) {
            return false;
        }

        // Global admin toggle check
        $enabled = (string) (Database::scalar("SELECT `value` FROM settings WHERE `key` = 'installment_enabled'") ?? '1');
        if ($enabled === '0' || strtolower($enabled) === 'false') {
            return false;
        }

        // Course-specific eligibility check if courseId provided
        if ($courseId !== null && $courseId > 0) {
            $course = Course::find($courseId);
            if (!$course) {
                return false;
            }
            if (isset($course['allow_installments']) && (int) $course['allow_installments'] === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate multi-part installment schedule using exact integer paise.
     * Prevents any floating point errors and guarantees sum(milestones) === totalCourseFee.
     *
     * @return array<int, array{
     *   installment_number: int,
     *   amount: float,
     *   amount_paise: int,
     *   due_date: string,
     *   notes: string,
     *   percent: int
     * }>
     */
    public static function calculateSchedule(
        float $totalCourseFee,
        int $totalInstallments = 3,
        ?int $upfrontPercent = null,
        int $intervalDays = 15
    ): array {
        if (!self::isEligible($totalCourseFee)) {
            throw new \InvalidArgumentException(
                sprintf('Installments are only available for course fees strictly greater than ₹%s.', number_format(self::STRICT_MIN_ELIGIBLE_AMOUNT, 0))
            );
        }

        if ($totalInstallments < 2 || $totalInstallments > self::STRICT_MAX_INSTALLMENTS) {
            throw new \InvalidArgumentException(
                sprintf('Installments must be either 2 or %d.', self::STRICT_MAX_INSTALLMENTS)
            );
        }

        // Upfront percent must be at least 40%
        $upfrontPct = $upfrontPercent ?? ($totalInstallments === 2 ? 50 : 40);
        if ($upfrontPct < self::STRICT_MIN_UPFRONT_PERCENT) {
            $upfrontPct = self::STRICT_MIN_UPFRONT_PERCENT;
        }
        if ($upfrontPct > 80) {
            $upfrontPct = 80;
        }

        $totalPaise = (int) round($totalCourseFee * 100);
        $milestones = [];
        $today = date('Y-m-d');

        if ($totalInstallments === 2) {
            // Milestone 1 (Today): Upfront %
            $upfrontPaise = (int) round($totalPaise * ($upfrontPct / 100.0));
            $milestone2Paise = $totalPaise - $upfrontPaise;
            $dueDate2 = date('Y-m-d', strtotime("+{$intervalDays} days"));

            $milestones = [
                [
                    'installment_number' => 1,
                    'amount'             => round($upfrontPaise / 100, 2),
                    'amount_paise'       => $upfrontPaise,
                    'due_date'           => $today,
                    'notes'              => 'Upfront Payment (Milestone 1 of 2)',
                    'percent'            => $upfrontPct,
                ],
                [
                    'installment_number' => 2,
                    'amount'             => round($milestone2Paise / 100, 2),
                    'amount_paise'       => $milestone2Paise,
                    'due_date'           => $dueDate2,
                    'notes'              => "Milestone 2 of 2 (Due in {$intervalDays} days)",
                    'percent'            => 100 - $upfrontPct,
                ],
            ];
        } else {
            // 3 Installments:
            // Milestone 1 (Today): Upfront % (default 50%, min 40%)
            // Remaining split evenly across Milestone 2 & Milestone 3 (e.g. 25% each if upfront 50%)
            $upfrontPaise = (int) round($totalPaise * ($upfrontPct / 100.0));
            $remainingPaise = $totalPaise - $upfrontPaise;

            $m2Paise = (int) intdiv($remainingPaise, 2);
            $m3Paise = $remainingPaise - $m2Paise; // Guarantees remainder paisa is allocated

            $dueDate2 = date('Y-m-d', strtotime("+{$intervalDays} days"));
            $dueDate3 = date('Y-m-d', strtotime('+' . ($intervalDays * 2) . ' days'));

            $remainingPct = 100 - $upfrontPct;
            $halfPct = (int) floor($remainingPct / 2);

            $milestones = [
                [
                    'installment_number' => 1,
                    'amount'             => round($upfrontPaise / 100, 2),
                    'amount_paise'       => $upfrontPaise,
                    'due_date'           => $today,
                    'notes'              => 'Upfront Payment (Milestone 1 of 3)',
                    'percent'            => $upfrontPct,
                ],
                [
                    'installment_number' => 2,
                    'amount'             => round($m2Paise / 100, 2),
                    'amount_paise'       => $m2Paise,
                    'due_date'           => $dueDate2,
                    'notes'              => "Milestone 2 of 3 (Due in {$intervalDays} days)",
                    'percent'            => $halfPct,
                ],
                [
                    'installment_number' => 3,
                    'amount'             => round($m3Paise / 100, 2),
                    'amount_paise'       => $m3Paise,
                    'due_date'           => $dueDate3,
                    'notes'              => 'Milestone 3 of 3 (Due in ' . ($intervalDays * 2) . ' days)',
                    'percent'            => $remainingPct - $halfPct,
                ],
            ];
        }

        // Sanity verify: Sum of milestone amounts must match total fee exactly
        $sumPaise = array_sum(array_column($milestones, 'amount_paise'));
        if ($sumPaise !== $totalPaise) {
            // Adjust any 1-paisa drift onto final milestone
            $diff = $totalPaise - $sumPaise;
            $lastIdx = count($milestones) - 1;
            $milestones[$lastIdx]['amount_paise'] += $diff;
            $milestones[$lastIdx]['amount'] = round($milestones[$lastIdx]['amount_paise'] / 100, 2);
        }

        return $milestones;
    }

    /**
     * Get available plans (Full Payment, 2 Installments, 3 Installments) for display in modal.
     * @return array<string,mixed>
     */
    public static function getAvailablePlansForCourse(float $coursePrice, ?int $courseId = null): array
    {
        $eligible = self::isEligible($coursePrice, 'INR', $courseId);

        $plans = [
            'eligible'     => $eligible,
            'course_price' => $coursePrice,
            'currency'     => 'INR',
            'options'      => [],
        ];

        // Option 1: Full Payment is always an option
        $plans['options']['full'] = [
            'id'           => 'full',
            'title'        => 'OPTION 1 — FULL PAYMENT',
            'summary'      => sprintf('Pay ₹%s today.', number_format($coursePrice, 0)),
            'today_amount' => $coursePrice,
            'total_amount' => $coursePrice,
            'installments' => 1,
            'schedule'     => [
                [
                    'label'  => 'Today',
                    'amount' => $coursePrice,
                    'due'    => 'Today',
                ],
            ],
        ];

        if ($eligible) {
            // Option 2: 2 Installments
            $s2 = self::calculateSchedule($coursePrice, 2);
            $plans['options']['2_installments'] = [
                'id'           => '2_installments',
                'title'        => 'OPTION 2 — 2 INSTALLMENTS',
                'summary'      => sprintf('Today: ₹%s | After 15 Days: ₹%s', number_format($s2[0]['amount'], 0), number_format($s2[1]['amount'], 0)),
                'today_amount' => $s2[0]['amount'],
                'total_amount' => $coursePrice,
                'installments' => 2,
                'schedule'     => [
                    ['label' => 'Today', 'amount' => $s2[0]['amount'], 'due' => date('d M Y', strtotime($s2[0]['due_date']))],
                    ['label' => 'After 15 Days', 'amount' => $s2[1]['amount'], 'due' => date('d M Y', strtotime($s2[1]['due_date']))],
                ],
            ];

            // Option 3: 3 Installments
            $s3 = self::calculateSchedule($coursePrice, 3);
            $plans['options']['3_installments'] = [
                'id'           => '3_installments',
                'title'        => 'OPTION 3 — 3 INSTALLMENTS',
                'summary'      => sprintf('Today: ₹%s | After 15 Days: ₹%s | After 30 Days: ₹%s', number_format($s3[0]['amount'], 0), number_format($s3[1]['amount'], 0), number_format($s3[2]['amount'], 0)),
                'today_amount' => $s3[0]['amount'],
                'total_amount' => $coursePrice,
                'installments' => 3,
                'schedule'     => [
                    ['label' => 'Today', 'amount' => $s3[0]['amount'], 'due' => date('d M Y', strtotime($s3[0]['due_date']))],
                    ['label' => 'After 15 Days', 'amount' => $s3[1]['amount'], 'due' => date('d M Y', strtotime($s3[1]['due_date']))],
                    ['label' => 'After 30 Days', 'amount' => $s3[2]['amount'], 'due' => date('d M Y', strtotime($s3[2]['due_date']))],
                ],
            ];
        }

        return $plans;
    }

    /**
     * Create an installment plan and its schedules inside a transaction.
     *
     * @param array<int,array{amount:float,due_date:string,notes?:string}> $milestones
     * @return array{plan_id:int,schedule_ids:list<int>}
     */
    public static function createPlan(
        int $parentOrderId,
        int $userId,
        int $courseId,
        float $totalCourseFee,
        int $totalInstallments,
        array $milestones = []
    ): array {
        return Database::transaction(function () use ($parentOrderId, $userId, $courseId, $totalCourseFee, $totalInstallments, $milestones) {
            if (empty($milestones)) {
                $milestones = self::calculateSchedule($totalCourseFee, $totalInstallments);
            }

            $upfrontAmount = (float) $milestones[0]['amount'];

            // 1. Create installment_plans record
            $planId = InstallmentPlan::create([
                'user_id'            => $userId,
                'course_id'          => $courseId,
                'parent_order_id'    => $parentOrderId,
                'total_amount'       => $totalCourseFee,
                'upfront_amount'     => $upfrontAmount,
                'total_installments' => $totalInstallments,
                'paid_amount'        => 0.00,
                'remaining_amount'   => $totalCourseFee,
                'currency'           => 'INR',
                'status'             => 'active',
            ]);

            // 2. Create installment_schedules records
            $scheduleIds = [];
            foreach ($milestones as $idx => $m) {
                $num = $idx + 1;
                $id = InstallmentSchedule::create([
                    'installment_plan_id' => $planId,
                    'parent_order_id'     => $parentOrderId,
                    'user_id'             => $userId,
                    'course_id'           => $courseId,
                    'installment_number'  => $num,
                    'total_installments'  => $totalInstallments,
                    'amount'              => (float) $m['amount'],
                    'due_date'            => $m['due_date'],
                    'status'              => 'pending',
                    'notes'               => $m['notes'] ?? "Installment $num of $totalInstallments",
                ]);
                $scheduleIds[] = $id;
            }

            // 3. Update parent order
            Database::run('
                UPDATE orders SET
                    is_installment = 1,
                    total_installments = ?,
                    remaining_amount = ?
                WHERE id = ?
            ', [$totalInstallments, $totalCourseFee, $parentOrderId]);

            return [
                'plan_id'      => $planId,
                'schedule_ids' => $scheduleIds,
            ];
        });
    }

    /**
     * Generate checkout parameters / link for paying a specific installment milestone.
     *
     * @return array{order_number:string,order_id:int,txnid:string,amount:float,params:array<string,string>,endpoint:string}
     */
    public static function generateInstallmentCheckout(int $scheduleId, array $studentDetails): array
    {
        $schedule = InstallmentSchedule::find($scheduleId);
        if (!$schedule) {
            throw new \InvalidArgumentException('Installment milestone not found.');
        }

        if ($schedule['status'] === 'paid') {
            throw new \InvalidArgumentException('This installment milestone has already been paid.');
        }

        $course = Course::find((int) $schedule['course_id']);
        $courseTitle = $course ? (string) $course['title'] : 'Course Fee';

        $txnid = PayU::generateTxnId('TCM_INST');
        $orderNumber = 'TCM-INST-' . date('Ymd') . '-' . $schedule['id'] . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        // Create or update installment order record
        $orderId = Order::create([
            'user_id'            => (int) $schedule['user_id'],
            'order_number'       => $orderNumber,
            'item_type'          => 'course',
            'item_id'            => (int) $schedule['course_id'],
            'item_title'         => sprintf('%s (Installment %d of %d)', $courseTitle, $schedule['installment_number'], $schedule['total_installments']),
            'amount'             => (float) $schedule['amount'],
            'paid_amount'        => 0.00,
            'remaining_amount'   => (float) $schedule['amount'],
            'currency'           => 'INR',
            'status'             => 'pending',
            'gateway_status'     => 'initiated',
            'payment_method'     => 'payu',
            'payment_ref'        => $txnid,
            'txnid'              => $txnid,
            'is_installment'     => 1,
            'installment_number' => (int) $schedule['installment_number'],
            'total_installments' => (int) $schedule['total_installments'],
        ]);

        // Link order ID to schedule
        InstallmentSchedule::update($scheduleId, [
            'installment_order_id' => $orderId,
            'txnid'                => $txnid,
        ]);

        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $surl   = $appUrl . '/payment/payu/success';
        $furl   = $appUrl . '/payment/payu/failure';

        $cleanPhone = preg_replace('/[^\d]/', '', (string) ($studentDetails['phone'] ?? '9999999999')) ?? '9999999999';

        $payuParams = [
            'key'         => PayU::key(),
            'txnid'       => $txnid,
            'amount'      => number_format((float) $schedule['amount'], 2, '.', ''),
            'productinfo' => substr(sprintf('Installment %d/%d - %s', $schedule['installment_number'], $schedule['total_installments'], $courseTitle), 0, 95),
            'firstname'   => (string) ($studentDetails['name'] ?? 'Student'),
            'email'       => (string) ($studentDetails['email'] ?? 'student@thecodemunk.in'),
            'phone'       => $cleanPhone,
            'surl'        => $surl,
            'furl'        => $furl,
            'udf1'        => 'installment',
            'udf2'        => (string) $schedule['course_id'],
            'udf3'        => (string) $scheduleId,
            'udf4'        => (string) $schedule['user_id'],
            'udf5'        => $orderNumber,
        ];

        $hash = PayU::generateRequestHash($payuParams);
        $payuParams['hash'] = $hash;

        return [
            'order_number' => $orderNumber,
            'order_id'     => $orderId,
            'txnid'        => $txnid,
            'amount'       => (float) $schedule['amount'],
            'params'       => $payuParams,
            'endpoint'     => PayU::endpoint(),
        ];
    }

    /**
     * Mark an installment milestone as paid, update balances, and link enrollment.
     */
    public static function markInstallmentPaid(
        int $scheduleId,
        string $txnid,
        string $mihpayid,
        float $amountPaid
    ): void {
        Database::transaction(function () use ($scheduleId, $txnid, $mihpayid, $amountPaid) {
            $schedule = InstallmentSchedule::find($scheduleId);
            if (!$schedule) {
                return;
            }

            // Mark milestone paid
            InstallmentSchedule::update($scheduleId, [
                'status'      => 'paid',
                'paid_at'     => date('Y-m-d H:i:s'),
                'txnid'       => $txnid,
                'payment_ref' => $mihpayid,
            ]);

            // Update parent plan if linked
            $planId = (int) ($schedule['installment_plan_id'] ?? 0);
            if ($planId <= 0 && !empty($schedule['parent_order_id'])) {
                $foundPlan = InstallmentPlan::findByParentOrder((int) $schedule['parent_order_id']);
                if ($foundPlan) {
                    $planId = (int) $foundPlan['id'];
                }
            }

            if ($planId > 0) {
                $plan = InstallmentPlan::find($planId);
                if ($plan) {
                    $newPlanPaid = (float) $plan['paid_amount'] + $amountPaid;
                    $newPlanRemaining = max(0.0, (float) $plan['total_amount'] - $newPlanPaid);
                    $newPlanStatus = ($newPlanRemaining <= 0.01) ? 'completed' : 'active';

                    InstallmentPlan::update($planId, [
                        'paid_amount'      => $newPlanPaid,
                        'remaining_amount' => $newPlanRemaining,
                        'status'           => $newPlanStatus,
                    ]);
                }
            }

            // Update parent order balances
            if (!empty($schedule['parent_order_id'])) {
                $parent = Order::find((int) $schedule['parent_order_id']);
                if ($parent) {
                    $newPaid = (float) $parent['paid_amount'] + $amountPaid;
                    $newRemaining = max(0.0, (float) $parent['amount'] - $newPaid);
                    $newStatus = ($newRemaining <= 0.01) ? 'paid' : $parent['status'];

                    Order::update((int) $parent['id'], [
                        'paid_amount'      => $newPaid,
                        'remaining_amount' => $newRemaining,
                        'status'           => $newStatus,
                    ]);
                }
            }
        });
    }

    /**
     * Reschedule a future installment milestone due date.
     */
    public static function rescheduleMilestone(
        int $scheduleId,
        string $newDueDate,
        string $reason,
        int $adminId
    ): bool {
        $schedule = InstallmentSchedule::find($scheduleId);
        if (!$schedule) {
            throw new \InvalidArgumentException('Installment milestone not found.');
        }

        if ($schedule['status'] === 'paid') {
            throw new \InvalidArgumentException('Cannot reschedule a milestone that has already been paid.');
        }

        $newDueTimestamp = strtotime($newDueDate);
        if (!$newDueTimestamp || $newDueTimestamp < strtotime(date('Y-m-d'))) {
            throw new \InvalidArgumentException('The new due date must be today or a future date.');
        }

        $oldDate = (string) $schedule['due_date'];
        $cleanReason = trim($reason) ?: 'Administrative extension';
        $updatedNotes = trim(($schedule['notes'] ?? '') . " | Rescheduled from {$oldDate} to {$newDueDate}: {$cleanReason}");

        InstallmentSchedule::update($scheduleId, [
            'due_date'       => date('Y-m-d', $newDueTimestamp),
            'status'         => 'pending',
            'notes'          => substr($updatedNotes, 0, 250),
            'reminder_stage' => 0, // Reset reminder stage for the new due date
        ]);

        // Audit log the reschedule
        WebhookAuditLog::logEvent(
            'installment_rescheduled',
            '/admin/installments/reschedule',
            'POST',
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            null,
            json_encode([
                'schedule_id' => $scheduleId,
                'old_due'     => $oldDate,
                'new_due'     => $newDueDate,
                'reason'      => $cleanReason,
                'admin_id'    => $adminId,
            ]),
            200,
            'verified',
            "Admin #{$adminId} rescheduled milestone #{$scheduleId} from {$oldDate} to {$newDueDate}"
        );

        return true;
    }

    /**
     * Get configurable installment settings.
     * @return array<string,mixed>
     */
    public static function getSettings(): array
    {
        $keys = [
            'installment_enabled',
            'installment_min_amount',
            'installment_max_installments',
            'installment_min_upfront_pct',
            'installment_interval_days',
            'installment_plans_available',
            'installment_interest_rate',
            'installment_reminder_email_enabled',
        ];

        $settings = [];
        foreach ($keys as $k) {
            $settings[$k] = (string) (Database::scalar('SELECT `value` FROM settings WHERE `key` = ?', [$k]) ?? '');
        }

        return [
            'enabled'            => ($settings['installment_enabled'] ?? '1') === '1',
            'min_amount'         => max(self::STRICT_MIN_ELIGIBLE_AMOUNT, (float) ($settings['installment_min_amount'] ?: self::STRICT_MIN_ELIGIBLE_AMOUNT)),
            'max_installments'   => min(self::STRICT_MAX_INSTALLMENTS, (int) ($settings['installment_max_installments'] ?: self::STRICT_MAX_INSTALLMENTS)),
            'min_upfront_pct'    => max(self::STRICT_MIN_UPFRONT_PERCENT, (int) ($settings['installment_min_upfront_pct'] ?: self::STRICT_MIN_UPFRONT_PERCENT)),
            'interval_days'      => max(7, (int) ($settings['installment_interval_days'] ?: 15)),
            'available_plans'    => $settings['installment_plans_available'] ?: '2,3',
            'interest_rate'      => 0.0, // Strictly 0%
            'email_reminders'    => ($settings['installment_reminder_email_enabled'] ?? '1') === '1',
        ];
    }

    /**
     * Update installment settings with strict constraint enforcement.
     *
     * @param array<string,mixed> $data
     */
    public static function saveSettings(array $data): void
    {
        $enabled = !empty($data['enabled']) ? '1' : '0';
        $minAmount = max(self::STRICT_MIN_ELIGIBLE_AMOUNT, (float) ($data['min_amount'] ?? self::STRICT_MIN_ELIGIBLE_AMOUNT));
        $maxInst = min(self::STRICT_MAX_INSTALLMENTS, max(2, (int) ($data['max_installments'] ?? 3)));
        $minUpfront = max(self::STRICT_MIN_UPFRONT_PERCENT, min(80, (int) ($data['min_upfront_pct'] ?? 40)));
        $intervalDays = max(7, min(60, (int) ($data['interval_days'] ?? 15)));
        $availablePlans = ($maxInst === 2) ? '2' : '2,3';
        $emailReminders = !empty($data['email_reminders']) ? '1' : '0';

        $updates = [
            'installment_enabled'                => $enabled,
            'installment_min_amount'             => number_format($minAmount, 2, '.', ''),
            'installment_max_installments'       => (string) $maxInst,
            'installment_min_upfront_pct'        => (string) $minUpfront,
            'installment_interval_days'          => (string) $intervalDays,
            'installment_plans_available'        => $availablePlans,
            'installment_interest_rate'          => '0',
            'installment_reminder_email_enabled' => $emailReminders,
        ];

        foreach ($updates as $k => $v) {
            Database::run(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                [$k, $v]
            );
        }
    }

    /**
     * Run overdue detection on pending installments.
     * @return int Count of marked overdue items
     */
    public static function refreshOverdueStatuses(): int
    {
        return Database::run("
            UPDATE installment_schedules
            SET status = 'overdue'
            WHERE status = 'pending' AND due_date < CURDATE()
        ")->rowCount();
    }
}
