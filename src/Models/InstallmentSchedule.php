<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class InstallmentSchedule extends Model
{
    protected static string $table = 'installment_schedules';

    /**
     * Get all installments for a specific user.
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT s.*, c.title AS course_title, c.slug AS course_slug
             FROM installment_schedules s
             LEFT JOIN courses c ON s.course_id = c.id
             WHERE s.user_id = ?
             ORDER BY s.due_date ASC, s.installment_number ASC',
            [$userId]
        );
    }

    /**
     * Get all installments for a parent order.
     * @return list<array<string,mixed>>
     */
    public static function forParentOrder(int $parentOrderId): array
    {
        return Database::all(
            'SELECT * FROM installment_schedules WHERE parent_order_id = ? ORDER BY installment_number ASC',
            [$parentOrderId]
        );
    }

    /**
     * Get all installments for an installment plan.
     * @return list<array<string,mixed>>
     */
    public static function forPlan(int $planId): array
    {
        return Database::all(
            'SELECT * FROM installment_schedules WHERE installment_plan_id = ? ORDER BY installment_number ASC',
            [$planId]
        );
    }

    /**
     * Find schedule linked to an installment order ID.
     */
    public static function findByInstallmentOrder(int $installmentOrderId): ?array
    {
        return self::findBy(['installment_order_id' => $installmentOrderId]);
    }

    /**
     * List all installment schedules for admin.
     * @return list<array<string,mixed>>
     */
    public static function listForAdmin(int $limit = 100): array
    {
        return Database::all(
            'SELECT s.*, c.title AS course_title, u.name AS user_name, u.email AS user_email, u.phone AS user_phone
             FROM installment_schedules s
             LEFT JOIN courses c ON s.course_id = c.id
             LEFT JOIN users u ON s.user_id = u.id
             ORDER BY s.due_date ASC, s.id DESC
             LIMIT ?',
            [$limit]
        );
    }

    /**
     * Find overdue installments.
     * @return list<array<string,mixed>>
     */
    public static function getOverdue(): array
    {
        return Database::all(
            'SELECT s.*, c.title AS course_title, u.name AS user_name, u.email AS user_email, u.phone AS user_phone
             FROM installment_schedules s
             LEFT JOIN courses c ON s.course_id = c.id
             LEFT JOIN users u ON s.user_id = u.id
             WHERE (s.status = "overdue" OR (s.status = "pending" AND s.due_date < CURDATE()))'
        );
    }

    /**
     * Find pending milestones due on a specific target date.
     * @return list<array<string,mixed>>
     */
    public static function getDueOnDate(string $targetDate): array
    {
        return Database::all(
            'SELECT s.*, c.title AS course_title, u.name AS user_name, u.email AS user_email, u.phone AS user_phone
             FROM installment_schedules s
             LEFT JOIN courses c ON s.course_id = c.id
             LEFT JOIN users u ON s.user_id = u.id
             WHERE s.status = "pending" AND s.due_date = ?',
            [$targetDate]
        );
    }
}
