<?php
/**
 * Cron Job: TCM One Automated Installment Reminders
 *
 * Runs daily to process milestone due date reminders (3 days before, 1 day before,
 * on due date, and overdue) and update overdue statuses.
 *
 * Crontab usage:
 * 0 9 * * * cd /path/to/tcm && C:\xampp\php\php.exe cron-installment-reminders.php
 */

declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

use TCM\Commands\SendInstallmentReminders;

try {
    $command = new SendInstallmentReminders();
    $command->run();
    exit(0);
} catch (\Throwable $e) {
    echo "❌ Installment Reminder Cron Error: " . $e->getMessage() . "\n";
    error_log("Installment Reminder Cron Error: " . $e->getMessage());
    exit(1);
}
