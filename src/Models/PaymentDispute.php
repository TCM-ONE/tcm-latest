<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class PaymentDispute extends Model
{
    protected static string $table = 'payment_disputes';

    /**
     * Find dispute by PayU dispute ID.
     */
    public static function findByPayuDisputeId(string $disputeId): ?array
    {
        return self::findBy(['payu_dispute_id' => $disputeId]);
    }

    /**
     * Get disputes for an order.
     * @return list<array<string,mixed>>
     */
    public static function forOrder(int $orderId): array
    {
        return Database::all(
            'SELECT * FROM payment_disputes WHERE order_id = ? ORDER BY id DESC',
            [$orderId]
        );
    }

    /**
     * List all disputes for admin dashboard.
     * @return list<array<string,mixed>>
     */
    public static function listForAdmin(int $limit = 100): array
    {
        return Database::all(
            'SELECT pd.*, o.order_number, o.item_title, u.name AS user_name, u.email AS user_email
             FROM payment_disputes pd
             LEFT JOIN orders o ON pd.order_id = o.id
             LEFT JOIN users u ON pd.user_id = u.id
             ORDER BY pd.id DESC
             LIMIT ?',
            [$limit]
        );
    }
}
