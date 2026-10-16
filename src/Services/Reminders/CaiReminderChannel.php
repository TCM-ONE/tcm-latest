<?php

declare(strict_types=1);

namespace TCM\Services\Reminders;

/**
 * Cai AI Agent Reminder Channel Adapter.
 * Prepared for Cai conversational agent nudges and automated student check-ins.
 */
final class CaiReminderChannel implements ReminderChannelInterface
{
    public function getChannelName(): string
    {
        return 'cai';
    }

    public function send(
        array $student,
        array $course,
        array $schedule,
        string $stage,
        string $payUrl
    ): bool {
        $userId = (int) ($student['id'] ?? 0);
        if ($userId <= 0) {
            return false;
        }

        // Cai assistant notification hook stub
        return true;
    }
}
