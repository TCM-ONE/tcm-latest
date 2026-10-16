<?php

declare(strict_types=1);

namespace TCM\Services\Reminders;

/**
 * WhatsApp Reminder Channel Interface & Future Provider Adapter.
 * Prepared for WhatsApp Business API / Twilio / Gupshup integration.
 */
final class WhatsAppReminderChannel implements ReminderChannelInterface
{
    public function getChannelName(): string
    {
        return 'whatsapp';
    }

    public function send(
        array $student,
        array $course,
        array $schedule,
        string $stage,
        string $payUrl
    ): bool {
        $phone = preg_replace('/[^\d]/', '', (string) ($student['phone'] ?? ''));
        if (strlen((string) $phone) < 10) {
            return false;
        }

        // WhatsApp provider integration stub (can be configured via webhook / gateway)
        // For now, log intent and return true if enabled in future
        return true;
    }
}
