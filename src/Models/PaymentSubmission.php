<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;
use TCM\Core\FirebaseNotification;
use TCM\Core\Mailer;
use TCM\Models\Notification;
use TCM\Models\Wallet;

final class PaymentSubmission extends Model
{
    protected static string $table = 'payment_submissions';

    public const METHODS = [
        'cash'          => 'Cash',
        'upi'           => 'UPI / PhonePe / GPay',
        'bank_transfer' => 'Bank Transfer / NEFT',
        'online'        => 'Online (Razorpay etc.)',
        'other'         => 'Other',
    ];

    public const STATUSES = [
        'pending'  => 'Pending Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    /**
     * Check if the payment_submissions table exists in the database.
     */
    private static function tableExists(): bool
    {
        static $exists = null;
        if ($exists === null) {
            $exists = (int) Database::scalar(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'payment_submissions'"
            ) > 0;
        }
        return $exists;
    }

    public static function supportsInternationalDetails(): bool
    {
        static $available = null;
        return $available ??= (bool) Database::scalar(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE()
             AND table_name = 'payment_submissions' AND column_name = 'international_details'"
        );
    }

    /**
     * @return list<array<string,mixed>>
     */
    public static function forUser(int $userId): array
    {
        if (!self::tableExists()) return [];
        return Database::all(
            'SELECT * FROM payment_submissions WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    /**
     * @param array<string,mixed> $filters
     * @return list<array<string,mixed>>
     */
    public static function adminList(array $filters = []): array
    {
        if (!self::tableExists()) return [];

        // Check if student_id column exists (graceful before migration)
        static $hasStudentId = null;
        if ($hasStudentId === null) {
            $hasStudentId = (bool) Database::scalar(
                "SELECT COUNT(*) FROM information_schema.columns
                 WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'student_id'"
            );
        }

        $studentIdCol = $hasStudentId ? ', u.student_id' : '';

        $sql = "SELECT ps.*, 
                u.name AS student_name, 
                u.email AS student_email{$studentIdCol},
                ps.referral_code,
                ps.referrer_id,
                ref_user.name AS referrer_name,
                ref_user.referral_id AS referrer_code
                FROM payment_submissions ps
                JOIN users u ON u.id = ps.user_id
                LEFT JOIN users ref_user ON ref_user.id = ps.referrer_id
                WHERE 1";
        $params = [];

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $sql .= ' AND ps.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (u.name LIKE ? OR u.email LIKE ? OR ps.item_title LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $residence = $filters['residence'] ?? '';
        $discovery = $filters['discovery'] ?? '';
        if (($residence !== '' || $discovery !== '') && self::supportsInternationalDetails()) {
            $residenceField = "JSON_UNQUOTE(JSON_EXTRACT(ps.international_details, '$.Residence'))";
            if (in_array($residence, ['IN', 'US', 'AE'], true)) {
                $sql .= " AND $residenceField = ?";
                $params[] = $residence;
            } elseif ($residence === 'other') {
                $sql .= " AND $residenceField NOT IN ('IN','US','AE')";
            }
            if (in_array($discovery, ['Google Search', 'ChatGPT', 'Gemini', 'Perplexity', 'Microsoft Copilot'], true)) {
                $sql .= " AND JSON_UNQUOTE(JSON_EXTRACT(ps.international_details, '$.Discovery')) = ?";
                $params[] = $discovery;
            }
        }

        $sql .= ' ORDER BY FIELD(ps.status,\'pending\',\'approved\',\'rejected\'), ps.created_at DESC';
        return Database::all($sql, $params);
    }

    /**
     * Approve a payment: create enrollment + generate receipt number.
     */
    public static function approve(int $id, int $adminId, string $note = ''): void
    {
        if (!self::tableExists()) return;
        $sub = self::find($id);
        if ($sub === null || $sub['status'] === 'approved') {
            return;
        }

        $receiptNo = 'TCM-RCP-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

        $approval = [
            'status'         => 'approved',
            'admin_note'     => $note,
            'receipt_number' => $receiptNo,
            'reviewed_by'    => $adminId,
            'reviewed_at'    => date('Y-m-d H:i:s'),
        ];
        $isInternationalEnrollment = !empty($sub['international_details']);
        $complete = static function () use ($sub, $approval, $id, $isInternationalEnrollment): bool {
            $where = ['id' => $id];
            if ($isInternationalEnrollment) {
                $where['status'] = 'pending';
            }
            if (!Database::update('payment_submissions', $approval, $where)) {
                return false;
            }
            $orderId = Order::place(
                (int) $sub['user_id'],
                $sub['item_type'],
                (int) $sub['item_id'],
                $sub['item_title'],
                (float) $sub['amount'],
                'paid',
                $sub['payment_method']
            );
            if ($isInternationalEnrollment) {
                Enrollment::enroll((int) $sub['user_id'], (int) $sub['item_id'], $orderId);
            } else {
                self::enrollStudent($sub);
            }
            return true;
        };
        if (!($isInternationalEnrollment ? Database::transaction($complete) : $complete())) {
            return;
        }

        if ($isInternationalEnrollment) {
            $student = Database::first('SELECT name, email FROM users WHERE id = ?', [(int) $sub['user_id']]);
            if ($student && Mailer::isConfigured()) {
                Mailer::send($student['email'], 'Welcome to TCMOne — Your Enrollment Is Confirmed', Mailer::template(
                    'Welcome to TCMOne!',
                    '<p>Hi ' . e($student['name']) . ', your payment of INR ' . e(number_format((float) $sub['amount'], 2))
                    . ' for ' . e($sub['item_title']) . ' has been verified. Course access is active. Admissions will confirm your live cohort separately.</p>'
                    . '<p><a href="' . e(base_url('/student')) . '">Student dashboard</a> · <a href="'
                    . e(base_url('/student/payments/' . $id . '/receipt')) . '">Payment receipt</a> · <a href="'
                    . e(base_url('/contact')) . '">Student support</a></p>'
                ));
            }
        }

        // 🎁 Credit referral wallet if referral code was used
        Wallet::ensureTables();
        Wallet::creditReferral((int) $sub['user_id'], $id);

        // 🔔 In-app + push: notify student their payment was approved
        Notification::ensureTable();
        Notification::forUser(
            (int) $sub['user_id'],
            '✅ Payment Approved!',
            'Your payment for ' . $sub['item_title'] . ' has been verified. You now have full access!',
            '✅',
            base_url('/student/payments')
        );
        FirebaseNotification::notifyStudent(
            (int) $sub['user_id'],
            '✅ Payment Approved!',
            'Your payment for ' . $sub['item_title'] . ' has been verified. You now have full access!',
            ['tag' => 'payment-approved', 'receipt' => $receiptNo],
            base_url('/student/payments')
        );
    }

    /**
     * Reject a payment submission.
     */
    public static function reject(int $id, int $adminId, string $note = ''): void
    {
        if (!self::tableExists()) return;
        $sub = self::find($id);
        $where = ['id' => $id];
        if (!empty($sub['international_details'])) {
            $where['status'] = 'pending';
        }
        $updated = Database::update('payment_submissions', [
            'status'      => 'rejected',
            'admin_note'  => $note,
            'reviewed_by' => $adminId,
            'reviewed_at' => date('Y-m-d H:i:s'),
        ], $where);

        // 🔔 In-app: notify student their payment was rejected
        if ($sub !== null && ($updated || empty($sub['international_details']))) {
            Notification::ensureTable();
            Notification::forUser(
                (int) $sub['user_id'],
                '❌ Payment Rejected',
                'Your payment for ' . $sub['item_title'] . ' was not approved.' . ($note ? ' Note: ' . $note : ' Please contact support.'),
                '❌',
                base_url('/student/payments')
            );
        }
    }

    /**
     * @param array<string,mixed> $sub
     */
    private static function enrollStudent(array $sub): void
    {
        $userId = (int) $sub['user_id'];
        $itemId = (int) $sub['item_id'];

        match ($sub['item_type']) {
            'course'  => Enrollment::enroll($userId, $itemId),
            'event'   => Database::run(
                'INSERT IGNORE INTO event_registrations (event_id, user_id, status) VALUES (?, ?, ?)',
                [$itemId, $userId, 'registered']
            ),
            'program' => Database::run(
                'INSERT IGNORE INTO program_enrollments (user_id, program_id, status) VALUES (?, ?, ?)',
                [$userId, $itemId, 'active']
            ),
            default   => null,
        };
    }

    /**
     * Check if student already has a pending/approved submission for an item.
     */
    public static function hasPendingOrApproved(int $userId, string $itemType, int $itemId): bool
    {
        if (!self::tableExists()) return false;
        return (int) Database::scalar(
            "SELECT COUNT(*) FROM payment_submissions
             WHERE user_id = ? AND item_type = ? AND item_id = ? AND status IN ('pending','approved')",
            [$userId, $itemType, $itemId]
        ) > 0;
    }

    /**
     * Count submissions by status for admin stats.
     *
     * @return array<string,int>
     */
    public static function counts(): array
    {
        if (!self::tableExists()) return ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        $rows = Database::all(
            "SELECT status, COUNT(*) AS cnt FROM payment_submissions GROUP BY status"
        );
        $result = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($rows as $row) {
            $result[$row['status']] = (int) $row['cnt'];
        }
        return $result;
    }
}
