<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class InstallmentPlan extends Model
{
    protected static string $table = 'installment_plans';

    /**
     * Get all installment plans for a student with course and milestones details.
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        $plans = Database::all(
            'SELECT p.*, c.title AS course_title, c.slug AS course_slug, c.price AS course_price,
                    o.order_number, o.receipt_number
             FROM installment_plans p
             LEFT JOIN courses c ON p.course_id = c.id
             LEFT JOIN orders o ON p.parent_order_id = o.id
             WHERE p.user_id = ?
             ORDER BY p.id DESC',
            [$userId]
        );

        foreach ($plans as &$plan) {
            $plan['milestones'] = Database::all(
                'SELECT * FROM installment_schedules
                 WHERE (installment_plan_id = ? OR parent_order_id = ?)
                 ORDER BY installment_number ASC',
                [$plan['id'], $plan['parent_order_id']]
            );
        }
        unset($plan);

        return $plans;
    }

    /**
     * Find plan by parent order ID.
     */
    public static function findByParentOrder(int $parentOrderId): ?array
    {
        return self::findBy(['parent_order_id' => $parentOrderId]);
    }

    /**
     * Find plan by enrollment ID.
     */
    public static function findByEnrollment(int $enrollmentId): ?array
    {
        return self::findBy(['enrollment_id' => $enrollmentId]);
    }

    /**
     * Calculate administrative metrics for installments.
     * @return array{
     *   total_enrollments: int,
     *   total_collected: float,
     *   total_outstanding: float,
     *   upcoming_payments: int,
     *   overdue_payments: int,
     *   completed_plans: int
     * }
     */
    public static function stats(): array
    {
        $totalEnrollments = (int) (Database::scalar('SELECT COUNT(*) FROM installment_plans') ?: 0);
        $totalCollected = (float) (Database::scalar('SELECT SUM(paid_amount) FROM installment_plans') ?: 0.0);
        $totalOutstanding = (float) (Database::scalar('SELECT SUM(remaining_amount) FROM installment_plans WHERE status="active"') ?: 0.0);
        $completedPlans = (int) (Database::scalar('SELECT COUNT(*) FROM installment_plans WHERE status="completed"') ?: 0);

        $upcomingPayments = (int) (Database::scalar(
            'SELECT COUNT(*) FROM installment_schedules WHERE status="pending" AND due_date >= CURDATE()'
        ) ?: 0);

        $overduePayments = (int) (Database::scalar(
            'SELECT COUNT(*) FROM installment_schedules WHERE (status="overdue" OR (status="pending" AND due_date < CURDATE()))'
        ) ?: 0);

        return [
            'total_enrollments' => $totalEnrollments,
            'total_collected'   => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'upcoming_payments' => $upcomingPayments,
            'overdue_payments'  => $overduePayments,
            'completed_plans'   => $completedPlans,
        ];
    }

    /**
     * List installment plans for admin with filters and search.
     * @param array{status?:string,q?:string} $filters
     * @return list<array<string,mixed>>
     */
    public static function listForAdmin(array $filters = [], int $limit = 100): array
    {
        $sql = 'SELECT p.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone,
                       c.title AS course_title, c.slug AS course_slug,
                       o.order_number
                FROM installment_plans p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN courses c ON p.course_id = c.id
                LEFT JOIN orders o ON p.parent_order_id = o.id
                WHERE 1=1';
        $params = [];

        $status = strtolower(trim((string)($filters['status'] ?? 'all')));
        if ($status !== '' && $status !== 'all') {
            if ($status === 'overdue') {
                $sql .= ' AND p.id IN (SELECT DISTINCT installment_plan_id FROM installment_schedules WHERE (status="overdue" OR (status="pending" AND due_date < CURDATE())))';
            } elseif ($status === 'partially_paid') {
                $sql .= ' AND p.status = "active" AND p.paid_amount > 0 AND p.remaining_amount > 0';
            } elseif ($status === 'pending') {
                $sql .= ' AND p.status = "active" AND p.paid_amount = 0';
            } else {
                $sql .= ' AND p.status = ?';
                $params[] = $status;
            }
        }

        $search = trim((string)($filters['q'] ?? ''));
        if ($search !== '') {
            $sql .= ' AND (u.name LIKE ? OR u.email LIKE ? OR c.title LIKE ? OR o.order_number LIKE ?)';
            $like = "%{$search}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= ' ORDER BY p.id DESC LIMIT ?';
        $params[] = $limit;

        $plans = Database::all($sql, $params);

        foreach ($plans as &$plan) {
            $milestones = Database::all(
                'SELECT * FROM installment_schedules
                 WHERE (installment_plan_id = ? OR parent_order_id = ?)
                 ORDER BY installment_number ASC',
                [$plan['id'], $plan['parent_order_id']]
            );
            $plan['milestones'] = $milestones;

            // Compute next due date & paid count
            $paidCount = 0;
            $nextDueDate = null;
            $hasOverdue = false;

            foreach ($milestones as $m) {
                if ($m['status'] === 'paid') {
                    $paidCount++;
                } else {
                    if ($nextDueDate === null) {
                        $nextDueDate = $m['due_date'];
                    }
                    if ($m['status'] === 'overdue' || strtotime((string)$m['due_date']) < strtotime(date('Y-m-d'))) {
                        $hasOverdue = true;
                    }
                }
            }

            $plan['paid_count'] = $paidCount;
            $plan['next_due_date'] = $nextDueDate;
            $plan['has_overdue'] = $hasOverdue;
        }
        unset($plan);

        return $plans;
    }
}
