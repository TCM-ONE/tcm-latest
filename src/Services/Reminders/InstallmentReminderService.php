<?php

declare(strict_types=1);

namespace TCM\Services\Reminders;

use TCM\Core\Database;
use TCM\Models\Course;
use TCM\Models\InstallmentSchedule;
use TCM\Models\User;

final class InstallmentReminderService
{
    /** @var list<ReminderChannelInterface> */
    private array $channels = [];

    public function __construct()
    {
        // Default to active Email channel
        $this->channels[] = new EmailReminderChannel();
    }

    /**
     * Add an additional channel (e.g. WhatsApp, Cai).
     */
    public function addChannel(ReminderChannelInterface $channel): self
    {
        $this->channels[] = $channel;
        return $this;
    }

    /**
     * Scan pending installments and dispatch scheduled reminders.
     * Stages:
     *   - 3 days before due date
     *   - 1 day before due date
     *   - on due date
     *   - overdue (after due date)
     *
     * @return array{scanned:int,sent:int,skipped:int,errors:int}
     */
    public function runScheduledReminders(): array
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $today = date('Y-m-d');
        $todayTime = strtotime($today);

        // Fetch pending or overdue schedules that are not paid
        $schedules = Database::all("
            SELECT s.*, u.id AS student_id, u.name AS student_name, u.email AS student_email, u.phone AS student_phone,
                   c.title AS course_title, c.slug AS course_slug
            FROM installment_schedules s
            JOIN users u ON s.user_id = u.id
            JOIN courses c ON s.course_id = c.id
            WHERE s.status IN ('pending', 'overdue')
            ORDER BY s.due_date ASC
        ");

        $stats = [
            'scanned' => count($schedules),
            'sent'    => 0,
            'skipped' => 0,
            'errors'  => 0,
        ];

        foreach ($schedules as $s) {
            $scheduleId = (int) $s['id'];
            $dueTime = strtotime((string) $s['due_date']);
            $diffDays = (int) round(($dueTime - $todayTime) / 86400);

            $stageInfo = match ($diffDays) {
                3       => ['stage' => '3_days_before', 'num' => 1],
                1       => ['stage' => '1_day_before',  'num' => 2],
                0       => ['stage' => 'due_date',      'num' => 3],
                default => ($diffDays < 0 ? ['stage' => 'overdue', 'num' => 4] : null),
            };

            if ($stageInfo === null) {
                $stats['skipped']++;
                continue;
            }

            $stage = $stageInfo['stage'];
            $stageNum = $stageInfo['num'];

            // Direct milestone checkout URL
            $payUrl = $appUrl . '/student/payments';

            $student = [
                'id'    => (int) $s['student_id'],
                'name'  => $s['student_name'],
                'email' => $s['student_email'],
                'phone' => $s['student_phone'],
            ];

            $course = [
                'title' => $s['course_title'],
                'slug'  => $s['course_slug'],
            ];

            foreach ($this->channels as $channel) {
                $channelName = $channel->getChannelName();

                // Idempotency check: has this specific stage already been dispatched via this channel?
                $alreadyLogged = (bool) Database::scalar('
                    SELECT COUNT(*) FROM installment_reminders_log
                    WHERE schedule_id = ? AND channel = ? AND stage = ?
                ', [$scheduleId, $channelName, $stage]);

                if ($alreadyLogged) {
                    $stats['skipped']++;
                    continue;
                }

                try {
                    $success = $channel->send($student, $course, $s, $stage, $payUrl);

                    if ($success) {
                        // Persist log to guarantee 0 duplicate reminders
                        Database::run('
                            INSERT INTO installment_reminders_log (schedule_id, user_id, channel, stage, recipient, sent_at, status)
                            VALUES (?, ?, ?, ?, ?, NOW(), "sent")
                            ON DUPLICATE KEY UPDATE sent_at = NOW(), status = "sent"
                        ', [
                            $scheduleId,
                            $student['id'],
                            $channelName,
                            $stage,
                            $student['email'] ?: $student['phone'] ?: 'user_' . $student['id'],
                        ]);

                        // Update schedule record
                        InstallmentSchedule::update($scheduleId, [
                            'reminded_at'      => date('Y-m-d H:i:s'),
                            'reminder_stage'   => $stageNum,
                            'reminder_sent_at' => date('Y-m-d H:i:s'),
                        ]);

                        $stats['sent']++;
                    } else {
                        $stats['errors']++;
                    }
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    Database::run('
                        INSERT INTO installment_reminders_log (schedule_id, user_id, channel, stage, recipient, sent_at, status, error_message)
                        VALUES (?, ?, ?, ?, ?, NOW(), "failed", ?)
                        ON DUPLICATE KEY UPDATE error_message = VALUES(error_message)
                    ', [
                        $scheduleId,
                        $student['id'],
                        $channelName,
                        $stage,
                        $student['email'] ?: 'unknown',
                        substr($e->getMessage(), 0, 250),
                    ]);
                }
            }
        }

        return $stats;
    }
}
