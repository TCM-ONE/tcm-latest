<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class PaymentRefund extends Model
{
    protected static string $table = 'payment_refunds';

    /**
     * Find refund by PayU refund ID.
     */
    public static function findByPayuRefundId(string $refundId): ?array
    {
        return self::findBy(['payu_refund_id' => $refundId]);
    }

    /**
     * Get refunds for an order.
     * @return list<array<string,mixed>>
     */
    public static function forOrder(int $orderId): array
    {
        return Database::all(
            'SELECT * FROM payment_refunds WHERE order_id = ? ORDER BY id DESC',
            [$orderId]
        );
    }

    /**
     * List all refunds for admin dashboard.
     * @return list<array<string,mixed>>
     */
    public static function listForAdmin(int $limit = 100): array
    {
        return Database::all(
            'SELECT pr.*, o.order_number, o.item_title, u.name AS user_name, u.email AS user_email
             FROM payment_refunds pr
             LEFT JOIN orders o ON pr.order_id = o.id
             LEFT JOIN users u ON pr.user_id = u.id
             ORDER BY pr.id DESC
             LIMIT ?',
            [$limit]
        );
    }
}
