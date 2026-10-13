<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;
use DateTime;
use DateTimeZone;

final class Batch extends Model
{
    protected static string $table = 'course_batches';

    /**
     * Get the active or next available batch for a given item (course or workshop).
     * Automatically handles deadline expiration checks (Asia/Kolkata).
     *
     * @param string $itemType 'course' | 'workshop'
     * @param int $itemId
     * @return array<string,mixed>|null
     */
    public static function activeFor(string $itemType, int $itemId): ?array
    {
        $tz = new DateTimeZone('Asia/Kolkata');
        $nowStr = (new DateTime('now', $tz))->format('Y-m-d H:i:s');

        // Look for open registration batch whose registration deadline hasn't passed
        $batch = Database::first(
            "SELECT * FROM course_batches 
             WHERE item_type = ? AND item_id = ? 
               AND status IN ('registration_open', 'upcoming')
               AND (registration_closes_at IS NULL OR registration_closes_at > ?)
             ORDER BY starts_at ASC 
             LIMIT 1",
            [$itemType, $itemId, $nowStr]
        );

        if ($batch) {
            return self::enrich($batch);
        }

        // Fallback: next upcoming confirmed batch even if registration closed or pending
        $nextBatch = Database::first(
            "SELECT * FROM course_batches 
             WHERE item_type = ? AND item_id = ? 
               AND starts_at > ?
             ORDER BY starts_at ASC 
             LIMIT 1",
            [$itemType, $itemId, $nowStr]
        );

        return $nextBatch ? self::enrich($nextBatch) : null;
    }

    /**
     * Get all upcoming batches for an item.
     *
     * @param string $itemType
     * @param int $itemId
     * @return list<array<string,mixed>>
     */
    public static function listFor(string $itemType, int $itemId): array
    {
        $rows = Database::all(
            "SELECT * FROM course_batches 
             WHERE item_type = ? AND item_id = ? 
             ORDER BY starts_at ASC",
            [$itemType, $itemId]
        );

        return array_map([self::class, 'enrich'], $rows);
    }

    /**
     * Enrich batch with countdown, formatted dates, and status badges.
     *
     * @param array<string,mixed> $batch
     * @return array<string,mixed>
     */
    public static function enrich(array $batch): array
    {
        $tz = new DateTimeZone('Asia/Kolkata');
        $now = new DateTime('now', $tz);

        $startsAt = !empty($batch['starts_at']) ? new DateTime($batch['starts_at'], $tz) : null;
        $closesAt = !empty($batch['registration_closes_at']) ? new DateTime($batch['registration_closes_at'], $tz) : null;

        $batch['is_closed'] = $closesAt ? ($now >= $closesAt) : false;
        $batch['is_started'] = $startsAt ? ($now >= $startsAt) : false;

        $batch['formatted_start_date'] = $startsAt ? $startsAt->format('M j, Y') : 'Announced Soon';
        $batch['formatted_start_time'] = $startsAt ? $startsAt->format('g:i A') . ' IST' : '';
        $batch['formatted_deadline']   = $closesAt ? $closesAt->format('M j, Y g:i A') . ' IST' : null;

        if ($closesAt && $closesAt > $now) {
            $diff = $now->diff($closesAt);
            $batch['countdown'] = [
                'days'    => $diff->days,
                'hours'   => $diff->h,
                'minutes' => $diff->i,
                'seconds' => $diff->s,
                'human'   => sprintf('%dd %dh %dm left', $diff->days, $diff->h, $diff->i),
            ];
        } else {
            $batch['countdown'] = null;
        }

        $seatsLeft = (int)($batch['seats_left'] ?? 0);
        $batch['is_filling_fast'] = $seatsLeft > 0 && $seatsLeft <= 10;
        $batch['is_sold_out'] = $seatsLeft <= 0;

        return $batch;
    }
}