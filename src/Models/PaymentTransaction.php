<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class PaymentTransaction extends Model
{
    protected static string $table = 'payment_transactions';

    /**
     * Find transaction by PayU transaction ID (txnid).
     */
    public static function findByTxnid(string $txnid): ?array
    {
        return self::findBy(['txnid' => $txnid]);
    }

    /**
     * Find transaction by PayU mihpayid.
     */
    public static function findByMihpayid(string $mihpayid): ?array
    {
        return self::findBy(['mihpayid' => $mihpayid]);
    }

    /**
     * Get all transactions for an order.
     * @return list<array<string,mixed>>
     */
    public static function forOrder(int $orderId): array
    {
        return Database::all(
            'SELECT * FROM payment_transactions WHERE order_id = ? ORDER BY id DESC',
            [$orderId]
        );
    }

    /**
     * Get all transactions for a user.
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT pt.*, o.order_number, o.item_title, o.item_type 
             FROM payment_transactions pt
             JOIN orders o ON pt.order_id = o.id
             WHERE pt.user_id = ?
             ORDER BY pt.created_at DESC',
            [$userId]
        );
    }

    /**
     * Get recent transactions with order and user info for Admin.
     * @return list<array<string,mixed>>
     */
    public static function listForAdmin(int $limit = 100): array
    {
        return Database::all(
            'SELECT pt.*, o.order_number, o.item_title, o.item_type, o.receipt_number,
                    u.name AS user_name, u.email AS user_email
             FROM payment_transactions pt
             LEFT JOIN orders o ON pt.order_id = o.id
             LEFT JOIN users u ON pt.user_id = u.id
             ORDER BY pt.id DESC
             LIMIT ?',
            [$limit]
        );
    }
}
