<?php

declare(strict_types=1);

namespace TCM\Commands;

use TCM\Services\InstallmentService;
use TCM\Services\Reminders\InstallmentReminderService;

final class SendInstallmentReminders
{
    public function run(): void
    {
        echo "=== TCM One Installment Reminders Engine ===\n";
        echo "Time: " . date('Y-m-d H:i:s') . "\n";

        // 1. Refresh overdue statuses
        $overdueCount = InstallmentService::refreshOverdueStatuses();
        echo "Refreshed overdue milestones: {$overdueCount}\n";

        // 2. Dispatch scheduled reminders
        $service = new InstallmentReminderService();
        $stats = $service->runScheduledReminders();

        echo sprintf(
            "Reminders Complete — Scanned: %d, Sent: %d, Skipped: %d, Errors: %d\n",
            $stats['scanned'],
            $stats['sent'],
            $stats['skipped'],
            $stats['errors']
        );
    }
}
