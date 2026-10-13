<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;
use TCM\Core\FirebaseNotification;
use TCM\Models\Notification;
use TCM\Models\Lead;

final class PartnerApplication extends Model
{
    protected static string $table = 'partner_applications';

    /**
     * Ensure partner tables exist in the database.
     */
    public static function ensureTables(): void
    {
        Database::exec("
            CREATE TABLE IF NOT EXISTS partner_applications (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                institution_name VARCHAR(255) NOT NULL,
                institution_type VARCHAR(100) DEFAULT NULL,
                city VARCHAR(100) NOT NULL,
                state VARCHAR(100) DEFAULT NULL,
                website VARCHAR(255) DEFAULT NULL,
                contact_person VARCHAR(150) NOT NULL,
                designation VARCHAR(150) DEFAULT NULL,
                phone VARCHAR(30) NOT NULL,
                email VARCHAR(190) NOT NULL,
                partnership_model VARCHAR(100) NOT NULL,
                expected_students INT UNSIGNED DEFAULT 0,
                expected_batches INT UNSIGNED DEFAULT 1,
                has_own_mentors VARCHAR(100) DEFAULT NULL,
                has_lab_facility VARCHAR(100) DEFAULT NULL,
                expected_start_date DATE DEFAULT NULL,
                courses_of_interest TEXT DEFAULT NULL,
                requirements TEXT DEFAULT NULL,
                calculator_estimates JSON DEFAULT NULL,
                status ENUM('new', 'in_review', 'meeting_scheduled', 'approved', 'declined') DEFAULT 'new',
                source VARCHAR(100) DEFAULT 'partner_page',
                ip_address VARCHAR(45) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_partner_email (email),
                INDEX idx_partner_phone (phone),
                INDEX idx_partner_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        Database::exec("
            CREATE TABLE IF NOT EXISTS partner_meetings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                institution_name VARCHAR(255) NOT NULL,
                contact_person VARCHAR(150) NOT NULL,
                designation VARCHAR(150) DEFAULT NULL,
                phone VARCHAR(30) NOT NULL,
                email VARCHAR(190) NOT NULL,
                partnership_model VARCHAR(100) DEFAULT 'Academic Partnership',
                preferred_date DATE NOT NULL,
                preferred_time VARCHAR(50) NOT NULL,
                notes TEXT DEFAULT NULL,
                status ENUM('pending', 'confirmed', 'completed', 'rescheduled', 'cancelled') DEFAULT 'pending',
                meeting_link VARCHAR(255) DEFAULT NULL,
                ip_address VARCHAR(45) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_meeting_date (preferred_date),
                INDEX idx_meeting_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Submit a new institutional partner application.
     *
     * @param array<string,mixed> $data
     * @return array{id: int, ref_code: string}
     */
    public static function submit(array $data): array
    {
        self::ensureTables();

        $calcJson = null;
        if (!empty($data['calculator_estimates'])) {
            $calcJson = is_string($data['calculator_estimates'])
                ? $data['calculator_estimates']
                : json_encode($data['calculator_estimates'], JSON_UNESCAPED_UNICODE);
        }

        $id = Database::insert('partner_applications', [
            'institution_name'    => (string) ($data['institution_name'] ?? 'Institution'),
            'institution_type'    => (string) ($data['institution_type'] ?? 'College'),
            'city'                => (string) ($data['city'] ?? ''),
            'state'               => (string) ($data['state'] ?? ''),
            'website'             => (string) ($data['website'] ?? ''),
            'contact_person'      => (string) ($data['contact_person'] ?? ''),
            'designation'         => (string) ($data['designation'] ?? 'Coordinator'),
            'phone'               => (string) ($data['phone'] ?? ''),
            'email'               => (string) ($data['email'] ?? ''),
            'partnership_model'   => (string) ($data['partnership_model'] ?? 'Academic Partnership'),
            'expected_students'   => (int) ($data['expected_students'] ?? 0),
            'expected_batches'    => (int) ($data['expected_batches'] ?? 1),
            'has_own_mentors'     => (string) ($data['has_own_mentors'] ?? 'Not Specified'),
            'has_lab_facility'    => (string) ($data['has_lab_facility'] ?? 'Available'),
            'expected_start_date' => !empty($data['expected_start_date']) ? (string) $data['expected_start_date'] : null,
            'courses_of_interest' => (string) ($data['courses_of_interest'] ?? ''),
            'requirements'        => (string) ($data['requirements'] ?? ''),
            'calculator_estimates'=> $calcJson,
            'source'              => (string) ($data['source'] ?? 'partner_page'),
            'ip_address'          => (string) ($data['ip_address'] ?? ''),
        ]);

        $refCode = 'TCM-PTR-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);

        // Also capture into general leads table so it appears in the admin leads view
        try {
            Lead::capture([
                'name'           => $data['contact_person'] . ' (' . $data['institution_name'] . ')',
                'email'          => $data['email'],
                'phone'          => $data['phone'],
                'interest_type'  => 'partner',
                'interest_title' => 'Partnership: ' . ($data['partnership_model'] ?? 'Institutional'),
                'message'        => 'Ref: ' . $refCode . ' | City: ' . ($data['city'] ?? '') . ' | Students: ' . ($data['expected_students'] ?? 0) . ' | Requirements: ' . ($data['requirements'] ?? ''),
                'source'         => 'partner_page',
            ]);
        } catch (\Throwable $e) {
            error_log('Partner lead capture error: ' . $e->getMessage());
        }

        // Notify admins in-app and push
        Notification::ensureTable();
        Notification::toAdmins(
            '🏛️ New Partner Application: ' . $data['institution_name'],
            $data['contact_person'] . ' applied for ' . ($data['partnership_model'] ?? 'Partnership') . ' (' . $data['city'] . '). Ref: ' . $refCode,
            '🏛️',
            base_url('/admin/leads')
        );

        FirebaseNotification::notifyAdmins(
            '🏛️ New Partner Application: ' . $data['institution_name'],
            $data['contact_person'] . ' from ' . $data['city'] . ' applied for ' . ($data['partnership_model'] ?? 'Partnership'),
            ['tag' => 'partner-new'],
            base_url('/admin/leads')
        );

        return ['id' => $id, 'ref_code' => $refCode];
    }

    /**
     * Book a partnership consultation meeting request.
     *
     * @param array<string,mixed> $data
     * @return array{id: int, ref_code: string}
     */
    public static function bookMeeting(array $data): array
    {
        self::ensureTables();

        $id = Database::insert('partner_meetings', [
            'institution_name'  => (string) ($data['institution_name'] ?? 'Institution'),
            'contact_person'    => (string) ($data['contact_person'] ?? ''),
            'designation'       => (string) ($data['designation'] ?? 'Coordinator'),
            'phone'             => (string) ($data['phone'] ?? ''),
            'email'             => (string) ($data['email'] ?? ''),
            'partnership_model' => (string) ($data['partnership_model'] ?? 'Academic Partnership'),
            'preferred_date'    => (string) ($data['preferred_date'] ?? date('Y-m-d', strtotime('+1 day'))),
            'preferred_time'    => (string) ($data['preferred_time'] ?? '11:00 AM - 12:00 PM'),
            'notes'             => (string) ($data['notes'] ?? ''),
            'ip_address'        => (string) ($data['ip_address'] ?? ''),
        ]);

        $refCode = 'TCM-CALL-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);

        try {
            Lead::capture([
                'name'           => $data['contact_person'] . ' (' . $data['institution_name'] . ')',
                'email'          => $data['email'],
                'phone'          => $data['phone'],
                'interest_type'  => 'partner_call',
                'interest_title' => 'Meeting: ' . ($data['preferred_date'] ?? '') . ' ' . ($data['preferred_time'] ?? ''),
                'message'        => 'Ref: ' . $refCode . ' | Preferred Date: ' . ($data['preferred_date'] ?? '') . ' | Notes: ' . ($data['notes'] ?? ''),
                'source'         => 'partner_meeting_booking',
            ]);
        } catch (\Throwable $e) {
            error_log('Partner meeting lead capture error: ' . $e->getMessage());
        }

        Notification::ensureTable();
        Notification::toAdmins(
            '📅 Partner Call Requested: ' . $data['institution_name'],
            $data['contact_person'] . ' requested a call on ' . $data['preferred_date'] . ' at ' . $data['preferred_time'],
            '📅',
            base_url('/admin/leads')
        );

        FirebaseNotification::notifyAdmins(
            '📅 Partner Call Requested: ' . $data['institution_name'],
            $data['contact_person'] . ' requested a call on ' . $data['preferred_date'],
            ['tag' => 'partner-call'],
            base_url('/admin/leads')
        );

        return ['id' => $id, 'ref_code' => $refCode];
    }
}
