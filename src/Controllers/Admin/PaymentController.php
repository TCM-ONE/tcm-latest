<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Mailer;
use TCM\Core\Request;
use TCM\Models\Course;
use TCM\Models\InstallmentSchedule;
use TCM\Models\Order;
use TCM\Models\PaymentDispute;
use TCM\Models\PaymentRefund;
use TCM\Models\PaymentSubmission;
use TCM\Models\PaymentTransaction;
use TCM\Models\User;
use TCM\Models\WebhookAuditLog;
use TCM\Services\InstallmentService;

final class PaymentController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');

        $payments = PaymentSubmission::adminList([
            'status' => Request::string('status') ?: 'all',
            'search' => Request::string('q') ?: null,
        ]);

        $onlineOrders = PaymentTransaction::listForAdmin(100);
        $installments = InstallmentSchedule::listForAdmin(100);
        $refunds      = PaymentRefund::listForAdmin(100);
        $disputes     = PaymentDispute::listForAdmin(100);
        $auditLogs    = WebhookAuditLog::listRecent(100);

        // Stats
        $totalOnline = (float) (Database::scalar("SELECT SUM(amount) FROM payment_transactions WHERE status='success'") ?: 0.0);
        $totalOverdue = (int) (Database::scalar("SELECT COUNT(*) FROM installment_schedules WHERE status='pending' AND due_date < CURDATE()") ?: 0);
        $activeInstallments = (int) (Database::scalar("SELECT COUNT(*) FROM installment_schedules WHERE status='pending'") ?: 0);
        $totalRefunded = (float) (Database::scalar("SELECT SUM(amount) FROM payment_refunds WHERE status='success'") ?: 0.0);

        $courses = Database::all("SELECT id, title, price FROM courses WHERE status='published' ORDER BY title");
        $students = Database::all("SELECT id, name, email FROM users WHERE role='student' ORDER BY name");

        $this->view('admin/payments/index', [
            'title'              => 'Payments & Billing Management',
            'payments'           => $payments,
            'counts'             => PaymentSubmission::counts(),
            'onlineOrders'       => $onlineOrders,
            'installments'       => $installments,
            'refunds'            => $refunds,
            'disputes'           => $disputes,
            'auditLogs'          => $auditLogs,
            'courses'            => $courses,
            'students'           => $students,
            'activeTab'          => Request::string('tab') ?: 'online',
            'stats'              => [
                'total_online'        => $totalOnline,
                'total_overdue'       => $totalOverdue,
                'active_installments' => $activeInstallments,
                'total_refunded'      => $totalRefunded,
            ],
        ], 'admin');
    }

    public function show(array $params): void
    {
        Auth::require('admin');
        $payment = PaymentSubmission::find((int) $params['id']);
        if ($payment === null) {
            flash('error', 'Payment not found.');
            redirect('/admin/payments');
        }

        $this->view('admin/payments/show', [
            'title'   => 'Review Payment',
            'payment' => $payment,
        ], 'admin');
    }

    public function approve(array $params): void
    {
        $admin = Auth::require('admin');
        $note  = Request::string('admin_note');

        PaymentSubmission::approve((int) $params['id'], (int) $admin['id'], $note);

        flash('success', 'Payment approved and student enrollment activated.');
        redirect('/admin/payments?tab=manual');
    }

    public function reject(array $params): void
    {
        $admin = Auth::require('admin');
        $note  = Request::string('admin_note');

        PaymentSubmission::reject((int) $params['id'], (int) $admin['id'], $note);

        flash('success', 'Payment rejected. Student has been notified via their dashboard.');
        redirect('/admin/payments?tab=manual');
    }

    /**
     * Create a custom installment plan for a student.
     * POST /admin/installments/create
     */
    public function createInstallmentPlan(): void
    {
        Auth::require('admin');

        $userId = Request::int('user_id');
        $courseId = Request::int('course_id');
        $numInstallments = max(2, min(6, Request::int('num_installments', 2)));
        $totalFee = (float) Request::string('total_fee', '0');

        $user = User::find($userId);
        $course = Course::find($courseId);

        if (!$user || !$course) {
            flash('error', 'Please select a valid student and course.');
            redirect('/admin/payments?tab=installments');
        }

        if ($totalFee <= 0) {
            $totalFee = (float) $course['price'];
        }

        try {
            // Create Parent Order
            $orderNumber = Order::generateNumber();
            $parentOrderId = Order::create([
                'user_id'            => $userId,
                'order_number'       => $orderNumber,
                'item_type'          => 'course',
                'item_id'            => $courseId,
                'item_title'         => $course['title'],
                'amount'             => $totalFee,
                'paid_amount'        => 0.00,
                'remaining_amount'   => $totalFee,
                'currency'           => 'INR',
                'status'             => 'pending',
                'gateway_status'     => 'installment_plan',
                'payment_method'     => 'payu',
                'payment_ref'        => 'PLAN_' . bin2hex(random_bytes(4)),
                'is_installment'     => 1,
                'installment_number' => 1,
                'total_installments' => $numInstallments,
            ]);

            InstallmentService::createPlan($parentOrderId, $userId, $courseId, $totalFee, $numInstallments);

            // Send Email to Student
            $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
            $payUrl = $appUrl . '/student/payments';
            $subject = sprintf('Your Customized Installment Fee Plan for %s — TCM One', $course['title']);
            $body = sprintf(
                '<h2>Custom Fee Payment Plan Created</h2>' .
                '<p>Hi %s,</p>' .
                '<p>A tailored installment plan of <strong>%d milestones</strong> has been generated for your course <strong>%s</strong> (Total Fee: ₹%s).</p>' .
                '<p>You can pay your milestones securely via UPI, Cards, or NetBanking directly from your student dashboard:</p>' .
                '<p><a href="%s" style="background:#111;color:#fff;padding:10px 20px;text-decoration:none;font-weight:700;display:inline-block;">View Payment Plan & Pay Milestone</a></p>',
                htmlspecialchars((string)$user['name'], ENT_QUOTES, 'UTF-8'),
                $numInstallments,
                htmlspecialchars((string)$course['title'], ENT_QUOTES, 'UTF-8'),
                number_format($totalFee, 2),
                htmlspecialchars($payUrl, ENT_QUOTES, 'UTF-8')
            );
            Mailer::send((string)$user['email'], $subject, $body);

            flash('success', "Installment plan of {$numInstallments} milestones created successfully for {$user['name']}.");
        } catch (\Throwable $e) {
            flash('error', 'Failed to create installment plan: ' . $e->getMessage());
        }

        redirect('/admin/payments?tab=installments');
    }

    /**
     * Send reminder to student for pending/overdue installment.
     * POST /admin/installments/{id}/remind
     */
    public function sendInstallmentReminder(array $params): void
    {
        Auth::require('admin');
        $scheduleId = (int) $params['id'];
        $schedule = InstallmentSchedule::find($scheduleId);

        if (!$schedule) {
            flash('error', 'Installment schedule not found.');
            redirect('/admin/payments?tab=installments');
        }

        $user = User::find((int) $schedule['user_id']);
        $course = Course::find((int) $schedule['course_id']);

        if ($user && $schedule['status'] !== 'paid') {
            $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
            $payUrl = $appUrl . '/student/payments';
            $subject = sprintf('Fee Payment Reminder: Milestone %d for %s — TCM One', $schedule['installment_number'], $course['title'] ?? 'Course');
            $body = sprintf(
                '<h2>Payment Reminder: Installment Milestone %d</h2>' .
                '<p>Hi %s,</p>' .
                '<p>This is a reminder that Milestone %d of your course fee (₹%s) is due on <strong>%s</strong>.</p>' .
                '<p><a href="%s" style="background:#2563eb;color:#fff;padding:10px 20px;text-decoration:none;font-weight:700;display:inline-block;">Pay Milestone Online Now</a></p>',
                $schedule['installment_number'],
                htmlspecialchars((string)$user['name'], ENT_QUOTES, 'UTF-8'),
                $schedule['installment_number'],
                number_format((float)$schedule['amount'], 2),
                date('d M Y', strtotime((string)$schedule['due_date'])),
                htmlspecialchars($payUrl, ENT_QUOTES, 'UTF-8')
            );
            Mailer::send((string)$user['email'], $subject, $body);

            InstallmentSchedule::update($scheduleId, ['reminded_at' => date('Y-m-d H:i:s')]);
            flash('success', "Payment reminder email dispatched to {$user['email']}.");
        } else {
            flash('info', 'Installment is already paid or user not found.');
        }

        redirect('/admin/payments?tab=installments');
    }

    /**
     * View screenshot of the payment.
     */
    public function viewScreenshot(array $params): void
    {
        Auth::require('admin');
        $payment = PaymentSubmission::find((int) $params['id']);
        if ($payment === null || empty($payment['screenshot'])) {
            http_response_code(404);
            echo 'Screenshot not found.';
            exit;
        }

        $config = config('uploads');
        $file   = $config['path'] . '/payments/' . basename((string)$payment['screenshot']);

        if (!is_file($file)) {
            http_response_code(404);
            echo 'File not found.';
            exit;
        }

        $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="payment-' . (int) $params['id'] . '.' . $ext . '"');
        readfile($file);
        exit;
    }
}
