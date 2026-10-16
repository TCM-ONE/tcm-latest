<?php

declare(strict_types=1);

namespace TCM\Services;

use TCM\Core\Database;
use TCM\Core\PayU;
use TCM\Models\InstallmentSchedule;
use TCM\Models\Order;
use TCM\Models\Course;
use TCM\Models\User;

final class InstallmentService
{
    /**
     * Create a multi-part installment plan for a student and course.
     *
     * @param array<int,array{amount:float,due_date:string,notes?:string}> $milestones
     * @return list<int> Created schedule IDs
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
            $scheduleIds = [];

            // If specific milestones not passed, split evenly over 30-day intervals
            if (empty($milestones)) {
                $perInstallment = round($totalCourseFee / $totalInstallments, 2);
                $remaining = $totalCourseFee;

                for ($i = 1; $i <= $totalInstallments; $i++) {
                    $amt = ($i === $totalInstallments) ? $remaining : $perInstallment;
                    $remaining -= $amt;
                    $dueDate = date('Y-m-d', strtotime('+' . (($i - 1) * 30) . ' days'));
                    $milestones[] = [
                        'amount'   => $amt,
                        'due_date' => $dueDate,
                        'notes'    => "Installment $i of $totalInstallments",
                    ];
                }
            }

            foreach ($milestones as $idx => $m) {
                $num = $idx + 1;
                $id = InstallmentSchedule::create([
                    'parent_order_id'    => $parentOrderId,
                    'user_id'            => $userId,
                    'course_id'          => $courseId,
                    'installment_number' => $num,
                    'total_installments' => $totalInstallments,
                    'amount'             => (float) $m['amount'],
                    'due_date'           => $m['due_date'],
                    'status'             => 'pending',
                    'notes'              => $m['notes'] ?? "Installment $num of $totalInstallments",
                ]);
                $scheduleIds[] = $id;
            }

            // Update parent order
            Database::run("
                UPDATE orders SET
                    is_installment = 1,
                    total_installments = ?,
                    remaining_amount = ?
                WHERE id = ?
            ", [$totalInstallments, $totalCourseFee, $parentOrderId]);

            return $scheduleIds;
        });
    }

    /**
     * Generate checkout parameters / link for paying a specific installment milestone.
     *
     * @return array{order_number:string,txnid:string,amount:float,params:array<string,string>,endpoint:string}
     */
    public static function generateInstallmentCheckout(int $scheduleId, array $studentDetails): array
    {
        $schedule = InstallmentSchedule::find($scheduleId);
        if (!$schedule) {
            throw new \InvalidArgumentException('Installment schedule not found.');
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
     * Mark an installment paid and update parent order balance.
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

            InstallmentSchedule::update($scheduleId, [
                'status'      => 'paid',
                'paid_at'     => date('Y-m-d H:i:s'),
                'txnid'       => $txnid,
                'payment_ref' => $mihpayid,
            ]);

            // If there is a parent order, update its balances
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
