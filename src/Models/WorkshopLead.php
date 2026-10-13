<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;
use TCM\Core\FirebaseNotification;
use TCM\Models\Notification;

final class WorkshopLead extends Model
{
    protected static string $table = 'workshop_leads';

    /**
     * Capture a new workshop lead safely with duplicate detection.
     *
     * @param array<string,mixed> $data
     * @return array{id: int, is_duplicate: bool}
     */
    public static function capture(array $data): array
    {
        $email = strtolower(trim((string)($data['email'] ?? '')));
        $slug  = trim((string)($data['workshop_slug'] ?? ''));

        // Duplicate guard: check if same email applied for this workshop in the past 60 minutes
        $recent = Database::first(
            "SELECT id FROM workshop_leads 
             WHERE email = ? AND workshop_slug = ? AND created_at >= (NOW() - INTERVAL 60 MINUTE)
             LIMIT 1",
            [$email, $slug]
        );

        if ($recent) {
            return [
                'id' => (int)$recent['id'],
                'is_duplicate' => true,
            ];
        }

        $id = self::create([
            'workshop_id'             => !empty($data['workshop_id']) ? (int)$data['workshop_id'] : null,
            'workshop_slug'           => $slug,
            'workshop_title'          => trim((string)($data['workshop_title'] ?? 'Live Workshop')),
            'full_name'               => trim((string)($data['full_name'] ?? 'Student')),
            'email'                   => $email,
            'phone'                   => trim((string)($data['phone'] ?? '')),
            'current_status'          => in_array($data['current_status'] ?? '', ['student', 'working', 'other'], true) ? $data['current_status'] : 'student',
            'college'                 => trim((string)($data['college'] ?? '')),
            'city'                    => trim((string)($data['city'] ?? '')),
            'learning_goal'           => trim((string)($data['learning_goal'] ?? '')),
            'selected_workshop_price' => (float)($data['selected_workshop_price'] ?? 0.0),
            'lead_status'             => 'new',
            'payment_status'          => 'not_initiated',
            'enrollment_status'       => 'pending',
            'utm_source'              => trim((string)($data['utm_source'] ?? '')),
            'utm_medium'              => trim((string)($data['utm_medium'] ?? '')),
            'utm_campaign'            => trim((string)($data['utm_campaign'] ?? '')),
            'utm_content'             => trim((string)($data['utm_content'] ?? '')),
            'landing_page'            => trim((string)($data['landing_page'] ?? '')),
            'referrer'                => trim((string)($data['referrer'] ?? '')),
            'consent_timestamp'       => date('Y-m-d H:i:s'),
        ]);

        // In-app & Push notification for Admins
        try {
            $wsTitle = (string)($data['workshop_title'] ?? 'Workshop');
            $priceText = !empty($data['selected_workshop_price']) ? ' (₹' . (int)$data['selected_workshop_price'] . ')' : '';
            Notification::ensureTable();
            Notification::toAdmins(
                '🎟️ New Workshop Lead — ' . ($data['full_name'] ?? 'Student'),
                ($data['full_name'] ?? 'Student') . ' registered interest for ' . $wsTitle . $priceText . '.',
                '🎟️',
                base_url('/admin/workshop-leads')
            );
            FirebaseNotification::notifyAdmins(
                '🎟️ New Workshop Lead — ' . ($data['full_name'] ?? 'Student'),
                ($data['full_name'] ?? 'Student') . ' registered interest for ' . $wsTitle . $priceText . '.',
                ['tag' => 'ws-lead-new'],
                base_url('/admin/workshop-leads')
            );
        } catch (\Throwable) {
            // Non-blocking notification failure
        }

        return [
            'id' => $id,
            'is_duplicate' => false,
        ];
    }

    /**
     * Search and filter workshop leads with pagination.
     *
     * @param array<string,mixed> $filters
     * @return list<array<string,mixed>>
     */
    public static function search(array $filters = []): array
    {
        $sql = "SELECT * FROM workshop_leads WHERE 1";
        $params = [];

        if (!empty($filters['lead_status']) && $filters['lead_status'] !== 'all') {
            $sql .= " AND lead_status = ?";
            $params[] = $filters['lead_status'];
        }

        if (!empty($filters['workshop_slug']) && $filters['workshop_slug'] !== 'all') {
            $sql .= " AND workshop_slug = ?";
            $params[] = $filters['workshop_slug'];
        }

        if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
            $sql .= " AND payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['search'])) {
            $like = '%' . $filters['search'] . '%';
            $sql .= " AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR city LIKE ? OR college LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY created_at DESC";
        return Database::all($sql, $params);
    }

    /**
     * Aggregate lead metrics for admin dashboard.
     *
     * @return array<string,int|float>
     */
    public static function stats(): array
    {
        $total = (int)Database::first("SELECT COUNT(*) as c FROM workshop_leads")['c'];
        $new = (int)Database::first("SELECT COUNT(*) as c FROM workshop_leads WHERE lead_status = 'new'")['c'];
        $interested = (int)Database::first("SELECT COUNT(*) as c FROM workshop_leads WHERE lead_status = 'interested'")['c'];
        $paymentPending = (int)Database::first("SELECT COUNT(*) as c FROM workshop_leads WHERE lead_status = 'payment_pending' OR payment_status = 'pending'")['c'];
        $enrolled = (int)Database::first("SELECT COUNT(*) as c FROM workshop_leads WHERE lead_status = 'enrolled' OR payment_status = 'paid'")['c'];

        $w99 = (int)Database::first("SELECT COUNT(*) as c FROM workshop_leads WHERE selected_workshop_price = 99")['c'];
        $w999 = (int)Database::first("SELECT COUNT(*) as c FROM workshop_leads WHERE selected_workshop_price = 999")['c'];

        $conversionRate = $total > 0 ? round(($enrolled / $total) * 100, 1) : 0.0;

        return [
            'total'             => $total,
            'new'               => $new,
            'w99'               => $w99,
            'w999'              => $w999,
            'interested'        => $interested,
            'payment_pending'   => $paymentPending,
            'enrolled'          => $enrolled,
            'conversion_rate'   => $conversionRate,
        ];
    }
}
