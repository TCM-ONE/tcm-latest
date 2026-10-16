<?php

declare(strict_types=1);

namespace TCM\Models;

use TCM\Core\Database;

final class WebhookAuditLog extends Model
{
    protected static string $table = 'webhook_audit_logs';

    /**
     * Record a webhook attempt/event.
     */
    public static function logEvent(
        string $eventType,
        string $url,
        string $httpMethod,
        ?string $ipAddress,
        ?string $headers,
        ?string $rawPayload,
        int $responseStatus,
        string $verificationStatus,
        ?string $errorDetails = null
    ): int {
        return self::create([
            'event_type'          => $eventType,
            'url'                 => $url,
            'http_method'         => $httpMethod,
            'ip_address'          => $ipAddress,
            'headers'             => $headers,
            'raw_payload'         => $rawPayload,
            'response_status'     => $responseStatus,
            'verification_status' => $verificationStatus,
            'error_details'       => $errorDetails,
        ]);
    }

    /**
     * Recent audit logs for admin inspection.
     * @return list<array<string,mixed>>
     */
    public static function listRecent(int $limit = 100): array
    {
        return Database::all(
            'SELECT * FROM webhook_audit_logs ORDER BY id DESC LIMIT ?',
            [$limit]
        );
    }
}
