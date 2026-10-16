<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Order extends Model
{
    protected static string $table = 'orders';

    /**
     * Create an order record for a course or event purchase.
     */
    public static function place(
        int $userId,
        string $itemType,
        int $itemId,
        string $itemTitle,
        float $amount,
        string $status = 'paid',
        string $paymentMethod = 'demo'
    ): int {
        return self::create([
            'user_id'        => $userId,
            'order_number'   => self::generateNumber(),
            'item_type'      => $itemType,
            'item_id'        => $itemId,
            'item_title'     => $itemTitle,
            'amount'         => $amount,
            'currency'       => 'INR',
            'status'         => $status,
            'payment_method' => $paymentMethod,
            'payment_ref'    => strtoupper(substr(bin2hex(random_bytes(8)), 0, 12)),
        ]);
    }

    public static function generateNumber(): string
    {
        return 'TCM-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    /**
     * Find order by PayU txnid.
     */
    public static function findByTxnid(string $txnid): ?array
    {
        return self::findBy(['txnid' => $txnid]);
    }

    /**
     * Generate sequential/unique official receipt number.
     */
    public static function generateReceiptNumber(int $orderId): string
    {
        return 'TCM-REC-' . date('Ym') . '-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT);
    }

    /**
     * List all orders for admin.
     * @return list<array<string,mixed>>
     */
    public static function forAdmin(int $limit = 100): array
    {
        return Database::all(
            'SELECT o.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             ORDER BY o.id DESC
             LIMIT ?',
            [$limit]
        );
    }
}
