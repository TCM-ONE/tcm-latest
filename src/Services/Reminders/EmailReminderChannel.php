<?php

declare(strict_types=1);

namespace TCM\Services\Reminders;

use TCM\Core\Mailer;

final class EmailReminderChannel implements ReminderChannelInterface
{
    public function getChannelName(): string
    {
        return 'email';
    }

    public function send(
        array $student,
        array $course,
        array $schedule,
        string $stage,
        string $payUrl
    ): bool {
        $studentEmail = trim((string) ($student['email'] ?? ''));
        if ($studentEmail === '' || !filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $studentName = (string) ($student['name'] ?? 'Student');
        $courseTitle = (string) ($course['title'] ?? 'TCM One Course');
        $instNum     = (int) ($schedule['installment_number'] ?? 1);
        $totalInst   = (int) ($schedule['total_installments'] ?? 3);
        $amountFmt   = number_format((float) ($schedule['amount'] ?? 0), 2);
        $dueDateFmt  = date('d M Y', strtotime((string) ($schedule['due_date'] ?? date('Y-m-d'))));

        $headline = match ($stage) {
            '3_days_before' => 'Your upcoming TCM One course installment is due in 3 days.',
            '1_day_before'  => 'Reminder: Your course installment is due tomorrow.',
            'due_date'      => 'Your TCM One installment is due today. Complete your payment to keep your account up to date.',
            'overdue'       => 'Your installment is overdue. Please complete the pending payment.',
            default         => 'Reminder: Your TCM One course installment is pending.',
        };

        $subject = match ($stage) {
            '3_days_before' => sprintf('Upcoming Installment Due in 3 Days — %s', $courseTitle),
            '1_day_before'  => sprintf('Reminder: Installment Due Tomorrow — %s', $courseTitle),
            'due_date'      => sprintf('Due Today: Course Installment — %s', $courseTitle),
            'overdue'       => sprintf('Important: Overdue Course Installment — %s', $courseTitle),
            default         => sprintf('Installment Payment Notice — %s', $courseTitle),
        };

        $bannerBg = ($stage === 'overdue') ? '#FEF2F2' : (($stage === 'due_date') ? '#FFFBEB' : '#F8FAFC');
        $bannerBorder = ($stage === 'overdue') ? '#FECACA' : (($stage === 'due_date') ? '#FDE68A' : '#E2E8F0');
        $bannerColor = ($stage === 'overdue') ? '#991B1B' : (($stage === 'due_date') ? '#92400E' : '#1E293B');

        $html = sprintf(
            '<div style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif;max-width:560px;margin:0 auto;color:#111;padding:24px;border:1px solid #E5E7EB;border-radius:0;">' .
            '<div style="border-bottom:2px solid #111;padding-bottom:16px;margin-bottom:20px;">' .
            '<h1 style="font-size:18px;font-weight:800;letter-spacing:-0.02em;margin:0;text-transform:uppercase;">TCM ONE</h1>' .
            '<span style="font-size:11px;letter-spacing:0.08em;color:#666;text-transform:uppercase;">Student Payment Notification</span>' .
            '</div>' .
            '<div style="background:%s;border:1px solid %s;color:%s;padding:14px 18px;margin-bottom:20px;font-weight:600;font-size:14px;border-radius:0;">' .
            '%s' .
            '</div>' .
            '<p style="font-size:14px;line-height:1.6;margin:0 0 16px 0;">Dear <strong>%s</strong>,</p>' .
            '<p style="font-size:14px;line-height:1.6;margin:0 0 20px 0;">This is a notification regarding your interest-free fee installment for <strong>%s</strong>.</p>' .
            '<table style="width:100%%;border-collapse:collapse;margin-bottom:24px;font-size:13.5px;">' .
            '<tr><td style="padding:10px 0;border-bottom:1px solid #EEE;color:#666;">Milestone:</td><td style="padding:10px 0;border-bottom:1px solid #EEE;font-weight:700;text-align:right;">Installment %d of %d</td></tr>' .
            '<tr><td style="padding:10px 0;border-bottom:1px solid #EEE;color:#666;">Amount Due:</td><td style="padding:10px 0;border-bottom:1px solid #EEE;font-weight:800;font-size:16px;text-align:right;color:#111;">₹%s</td></tr>' .
            '<tr><td style="padding:10px 0;border-bottom:1px solid #EEE;color:#666;">Due Date:</td><td style="padding:10px 0;border-bottom:1px solid #EEE;font-weight:700;text-align:right;">%s</td></tr>' .
            '<tr><td style="padding:10px 0;border-bottom:1px solid #EEE;color:#666;">Interest Rate:</td><td style="padding:10px 0;border-bottom:1px solid #EEE;font-weight:700;text-align:right;color:#16A34A;">0%% (Interest Free)</td></tr>' .
            '</table>' .
            '<div style="text-align:center;margin:28px 0;">' .
            '<a href="%s" style="background:#111;color:#FFF;padding:14px 28px;text-decoration:none;font-weight:700;font-size:14px;display:inline-block;border-radius:0;text-transform:uppercase;letter-spacing:0.04em;">Pay Milestone Online Now</a>' .
            '</div>' .
            '<p style="font-size:12px;color:#666;line-height:1.5;margin:20px 0 0 0;border-top:1px solid #EEE;padding-top:16px;">' .
            'Payments are securely processed via 256-bit encrypted PayU Gateway supporting UPI, Debit/Credit Cards, and NetBanking. Once paid, your dashboard and balance are updated immediately.' .
            '</p>' .
            '</div>',
            $bannerBg,
            $bannerBorder,
            $bannerColor,
            htmlspecialchars($headline, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($courseTitle, ENT_QUOTES, 'UTF-8'),
            $instNum,
            $totalInst,
            $amountFmt,
            $dueDateFmt,
            htmlspecialchars($payUrl, ENT_QUOTES, 'UTF-8')
        );

        if (!Mailer::isConfigured()) {
            return true;
        }

        $sent = Mailer::send($studentEmail, $subject, $html);
        if (!$sent && config('app.debug', false)) {
            return true;
        }

        return $sent;
    }
}
