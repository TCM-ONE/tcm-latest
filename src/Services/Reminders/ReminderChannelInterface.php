<?php

declare(strict_types=1);

namespace TCM\Services\Reminders;

interface ReminderChannelInterface
{
    /**
     * Send installment fee reminder.
     *
     * @param array<string,mixed> $student Details including name, email, phone
     * @param array<string,mixed> $course Details including title, slug
     * @param array<string,mixed> $schedule Milestone details including amount, due_date, installment_number, total_installments
     * @param string $stage e.g. '3_days_before', '1_day_before', 'due_date', 'overdue'
     * @param string $payUrl Direct URL to pay this milestone
     * @return bool True if successfully dispatched
     */
    public function send(
        array $student,
        array $course,
        array $schedule,
        string $stage,
        string $payUrl
    ): bool;

    /**
     * Unique identifier for the channel (e.g. 'email', 'whatsapp', 'cai').
     */
    public function getChannelName(): string;
}
