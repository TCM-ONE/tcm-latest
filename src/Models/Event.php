<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class Event extends Model
{
    protected static string $table = 'events';

    /**
     * @param array<string,mixed> $filters status, type, category, search
     * @return list<array<string,mixed>>
     */
    public static function search(array $filters = []): array
    {
        $sql = 'SELECT * FROM events WHERE 1';
        $params = [];

        foreach (['status', 'type', 'category'] as $field) {
            if (!empty($filters[$field]) && $filters[$field] !== 'all') {
                $sql .= " AND $field = ?";
                $params[] = $filters[$field];
            }
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND title LIKE ?';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY FIELD(status,'ongoing','upcoming','past'), event_date ASC";
        return Database::all($sql, $params);
    }

    /**
     * @return array<string,mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::findBy(['slug' => $slug]);
    }

    /** Create the event record for a workshop listed in the public catalog. */
    public static function registerCatalogWorkshop(array $workshop): ?array
    {
        Database::run("
            INSERT INTO events (title, slug, description, category, type, status, price, mode, total_seats, seats_filled)
            VALUES (?, ?, ?, 'general', ?, 'upcoming', ?, 'online', 50, 0)
            ON DUPLICATE KEY UPDATE slug = VALUES(slug)
        ", [
            $workshop['title'],
            $workshop['slug'],
            $workshop['description'] ?? '',
            ((float) $workshop['price'] > 0 ? 'paid' : 'free'),
            (float) $workshop['price'],
        ]);

        return self::findBySlug($workshop['slug']);
    }

    public static function seatsLeft(array $event): int
    {
        // Support both seats_filled column and calculated from total-seats_filled
        if (isset($event['seats_filled'])) {
            return max(0, (int) $event['total_seats'] - (int) $event['seats_filled']);
        }
        return max(0, (int) $event['total_seats'] - (int) $event['seats_filled']);
    }

    public static function isFull(array $event): bool
    {
        return self::seatsLeft($event) <= 0;
    }
}
