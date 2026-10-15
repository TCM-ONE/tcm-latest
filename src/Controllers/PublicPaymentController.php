<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Mailer;
use TCM\Core\PayU;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Core\WorkshopData;
use TCM\Models\Course;
use TCM\Models\Event;
use TCM\Models\Order;
use TCM\Models\User;
use TCM\Models\WorkshopLead;

final class PublicPaymentController extends Controller
{
    /**
     * POST /payment/initiate
     * Create order and output or return PayU hosted checkout form parameters.
     */
    public function initiate(): void
    {
        $itemType = strtolower(trim(Request::string('item_type', 'course')));
        $itemId   = Request::int('item_id');
        $slug     = trim(Request::string('slug') ?: Request::string('item_slug'));
        $name     = trim(Request::string('name') ?: Request::string('full_name'));
        $email    = strtolower(trim(Request::string('email')));
        $phone    = trim(Request::string('phone'));

        if ($name === '') {
            $this->abortWithError('Please provide your full name.');
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->abortWithError('Please provide a valid email address.');
        }
        $cleanPhone = preg_replace('/[^\d]/', '', $phone) ?? '';
        if (strlen($cleanPhone) < 10) {
            $this->abortWithError('Please provide a valid 10-digit mobile number.');
        }

        // Resolve Item & Price
        $itemTitle = '';
        $amount = 0.0;
        $resolvedSlug = $slug;

        if ($itemType === 'workshop' || $itemType === 'event') {
            $itemType = 'workshop';
            $workshop = null;
            if ($itemId > 0) {
                $workshop = Event::find($itemId);
            }
            if ($workshop === null && $slug !== '') {
                $resolvedSlug = WorkshopData::resolveSlug($slug);
                $workshop = Event::findBySlug($resolvedSlug);

                // Self-healing fallback: Check WorkshopData registry
                if ($workshop === null) {
                    $wsDef = WorkshopData::find($resolvedSlug);
                    if ($wsDef !== null) {
                        try {
                            Database::run("
                                INSERT INTO events (title, slug, subtitle, description, category, type, status, price, mode, duration, total_seats, seats_filled)
                                VALUES (?, ?, ?, ?, 'general', ?, 'upcoming', ?, 'online', ?, 50, 0)
                                ON DUPLICATE KEY UPDATE title = VALUES(title), price = VALUES(price)
                            ", [
                                $wsDef['title'],
                                $resolvedSlug,
                                $wsDef['tagline'] ?? $wsDef['short_title'] ?? '',
                                $wsDef['description'] ?? '',
                                ((float) $wsDef['price'] > 0 ? 'paid' : 'free'),
                                (float) $wsDef['price'],
                                $wsDef['duration'] ?? 'Live Workshop'
                            ]);
                            $workshop = Event::findBySlug($resolvedSlug);
                        } catch (\Throwable $e) {
                            // Virtual event fallback if DB table insert is restricted
                            $workshop = [
                                'id'    => 9999,
                                'slug'  => $resolvedSlug,
                                'title' => $wsDef['title'],
                                'price' => (float) $wsDef['price'],
                            ];
                        }
                    }
                }
            }
            if ($workshop === null) {
                $this->abortWithError('Workshop not found.');
            }
            $itemId = (int) ($workshop['id'] ?? 9999);
            $resolvedSlug = (string) ($workshop['slug'] ?? $slug);
            $itemTitle = (string) ($workshop['title'] ?? 'Live Workshop');
            $amount = (float) ($workshop['price'] ?? 0.0);
        } else {
            $itemType = 'course';
            $course = null;
            if ($itemId > 0) {
                $course = Course::find($itemId);
            }
            if ($course === null && $slug !== '') {
                $course = Course::findBySlug($slug);
            }
            if ($course === null) {
                $this->abortWithError('Course not found.');
            }
            $itemId = (int) $course['id'];
            $resolvedSlug = (string) $course['slug'];
            $itemTitle = (string) $course['title'];
            $amount = (float) $course['price'];
        }

        // 1. Resolve or Create User
        $user = Auth::user();
        $userId = $user ? (int) $user['id'] : 0;

        if ($userId <= 0) {
            $existingUser = Database::first('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
            if ($existingUser) {
                $userId = (int) $existingUser['id'];
            } else {
                $tempPassword = bin2hex(random_bytes(6));
                $hash = password_hash($tempPassword, PASSWORD_DEFAULT);
                Database::run(
                    "INSERT INTO users (name, email, password_hash, role, status, email_verified, onboarded)
                     VALUES (?, ?, ?, 'student', 'active', 1, 0)",
                    [$name, $email, $hash]
                );
                $userId = (int) Database::scalar('SELECT LAST_INSERT_ID()');
                Database::run("INSERT IGNORE INTO student_profiles (user_id, headline) VALUES (?, 'TCM Student')", [$userId]);
            }
        }

        // Handle Free Registration (Amount <= 0) - Instant Confirmation without PayU Gateway
        if ($amount <= 0) {
            $orderNumber = Order::generateNumber();
            Database::run("
                INSERT INTO orders (user_id, order_number, item_type, item_id, item_title, amount, currency, status, payment_method, payment_ref)
                VALUES (?, ?, ?, ?, ?, 0.00, 'INR', 'paid', 'free', 'FREE_ENROLL')
            ", [
                $userId,
                $orderNumber,
                $itemType === 'workshop' ? 'event' : 'course',
                $itemId,
                $itemTitle
            ]);

            if ($itemType === 'workshop') {
                WorkshopLead::capture([
                    'workshop_id'             => $itemId,
                    'workshop_slug'           => $resolvedSlug,
                    'workshop_title'          => $itemTitle,
                    'full_name'               => $name,
                    'email'                   => $email,
                    'phone'                   => $phone,
                    'selected_workshop_price' => 0.00,
                    'payment_status'          => 'paid',
                    'lead_status'             => 'enrolled',
                    'enrollment_status'       => 'confirmed',
                ]);
            } else {
                Database::run("
                    INSERT INTO enrollments (user_id, course_id, status, progress, enrolled_at)
                    VALUES (?, ?, 'active', 0, NOW())
                    ON DUPLICATE KEY UPDATE status = 'active'
                ", [$userId, $itemId]);
            }

            $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
            $successUrl = $appUrl . '/payment/success?order=' . rawurlencode($orderNumber);

            if (Request::isJson() || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
                Response::success([
                    'free'         => true,
                    'redirect'     => $successUrl,
                    'order_number' => $orderNumber,
                ], 'Registration successful! Your seat is reserved.');
            }

            Response::redirect($successUrl);
            return;
        }

        // 2. Create Order in Database
        $orderNumber = Order::generateNumber();
        $txnid = PayU::generateTxnId($itemType === 'course' ? 'TCM_CR' : 'TCM_WS');

        Database::run("
            INSERT INTO orders (user_id, order_number, item_type, item_id, item_title, amount, currency, status, payment_method, payment_ref, txnid)
            VALUES (?, ?, ?, ?, ?, ?, 'INR', 'pending', 'payu', ?, ?)
        ", [
            $userId,
            $orderNumber,
            $itemType === 'workshop' ? 'event' : 'course',
            $itemId,
            $itemTitle,
            $amount,
            $txnid,
            $txnid,
        ]);

        // Capture workshop lead if workshop
        if ($itemType === 'workshop') {
            WorkshopLead::capture([
                'workshop_id'             => $itemId,
                'workshop_slug'           => $resolvedSlug,
                'workshop_title'          => $itemTitle,
                'full_name'               => $name,
                'email'                   => $email,
                'phone'                   => $phone,
                'selected_workshop_price' => $amount,
                'payment_status'          => 'pending',
                'lead_status'             => 'payment_pending',
            ]);
        }

        // 3. Prepare PayU hosted checkout fields
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $surl   = $appUrl . '/payment/payu/success';
        $furl   = $appUrl . '/payment/payu/failure';

        $payuParams = [
            'key'         => PayU::key(),
            'txnid'       => $txnid,
            'amount'      => number_format($amount, 2, '.', ''),
            'productinfo' => substr($itemTitle, 0, 95),
            'firstname'   => $name,
            'email'       => $email,
            'phone'       => $cleanPhone,
            'surl'        => $surl,
            'furl'        => $furl,
            'udf1'        => $itemType,
            'udf2'        => (string) $itemId,
            'udf3'        => $resolvedSlug,
            'udf4'        => (string) $userId,
            'udf5'        => $orderNumber,
        ];

        $hash = PayU::generateRequestHash($payuParams);
        $payuParams['hash'] = $hash;

        // If AJAX request, return JSON payload for client form submit
        if (Request::isJson() || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
            Response::success([
                'action'       => PayU::endpoint(),
                'params'       => $payuParams,
                'order_number' => $orderNumber,
            ], 'Redirecting to secure payment gateway...');
        }

        // Otherwise output auto-submitting POST form
        echo PayU::renderAutoSubmitForm($payuParams);
        exit;
    }

    /**
     * POST /payment/payu/success
     * PayU hosted checkout success callback.
     */
    public function payuSuccess(): void
    {
        $data = $_POST;
        if (empty($data)) {
            Response::redirect('/payment/failure?reason=' . urlencode('No payment response data received.'));
        }

        $isValid = PayU::verifyResponseHash($data);
        $txnid = (string) ($data['txnid'] ?? '');
        $orderNumber = (string) ($data['udf5'] ?? '');

        // Find Order
        $order = Database::first('SELECT * FROM orders WHERE txnid = ? OR order_number = ? LIMIT 1', [$txnid, $orderNumber]);

        if (!$order) {
            Response::redirect('/payment/failure?reason=' . urlencode('Associated order record not found.'));
        }

        if (!$isValid) {
            Database::run(
                "UPDATE orders SET status = 'failed', raw_response = ? WHERE id = ?",
                [json_encode($data, JSON_UNESCAPED_SLASHES), $order['id']]
            );
            Response::redirect('/payment/failure?order=' . urlencode((string)$order['order_number']) . '&reason=' . urlencode('Cryptographic verification failed.'));
        }

        $status = strtolower((string) ($data['status'] ?? ''));
        if ($status !== 'success') {
            Database::run(
                "UPDATE orders SET status = 'failed', raw_response = ? WHERE id = ?",
                [json_encode($data, JSON_UNESCAPED_SLASHES), $order['id']]
            );
            Response::redirect('/payment/failure?order=' . urlencode((string)$order['order_number']) . '&reason=' . urlencode('Payment was not completed.'));
        }

        $mihpayid = (string) ($data['mihpayid'] ?? $txnid);
        $bankRef  = (string) ($data['bank_ref_num'] ?? '');
        $userId   = (int) $order['user_id'];
        $itemType = (string) ($data['udf1'] ?? ($order['item_type'] === 'event' ? 'workshop' : 'course'));
        $itemId   = (int) ($data['udf2'] ?? $order['item_id']);
        $itemSlug = (string) ($data['udf3'] ?? '');

        // 1. Mark Order Paid
        Database::run("
            UPDATE orders SET
                status = 'paid',
                payment_ref = ?,
                raw_response = ?,
                updated_at = NOW()
            WHERE id = ?
        ", [$mihpayid, json_encode($data, JSON_UNESCAPED_SLASHES), $order['id']]);

        // 2. Grant Access / Enrollment
        if ($itemType === 'course') {
            Database::run("
                INSERT INTO enrollments (user_id, course_id, order_id, status, progress, enrolled_at)
                VALUES (?, ?, ?, 'active', 0, NOW())
                ON DUPLICATE KEY UPDATE status = 'active', order_id = VALUES(order_id)
            ", [$userId, $itemId, $order['id']]);
        } else {
            // Workshop: update lead status
            Database::run("
                UPDATE workshop_leads SET
                    payment_status = 'paid',
                    enrollment_status = 'confirmed',
                    lead_status = 'enrolled',
                    updated_at = NOW()
                WHERE (workshop_id = ? OR workshop_slug = ?) AND email = ?
                ORDER BY id DESC LIMIT 1
            ", [$itemId, $itemSlug, (string) ($data['email'] ?? '')]);
        }

        // 3. Record in payment_submissions table for admin tracking
        try {
            $hasSubmissions = (bool) Database::scalar(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'payment_submissions'"
            );
            if ($hasSubmissions) {
                Database::run("
                    INSERT INTO payment_submissions (user_id, item_type, item_id, item_title, amount, method, status, notes, verified_at, created_at)
                    VALUES (?, ?, ?, ?, ?, 'online', 'approved', ?, NOW(), NOW())
                ", [
                    $userId,
                    $itemType,
                    $itemId,
                    $order['item_title'],
                    $order['amount'],
                    "PayU ID: {$mihpayid} | Bank Ref: {$bankRef} | Txn: {$txnid}",
                ]);
            }
        } catch (\Throwable) {}

        // 4. Send Confirmation Email
        try {
            $studentEmail = (string) ($data['email'] ?? '');
            $studentName  = (string) ($data['firstname'] ?? 'Student');
            $itemTitle    = (string) $order['item_title'];
            $amount       = number_format((float) $order['amount'], 2);

            if ($studentEmail !== '') {
                $emailBody = sprintf(
                    '<div style="font-family:sans-serif;line-height:1.6;color:#111;">' .
                    '<h2>Payment Confirmed! Welcome to %s</h2>' .
                    '<p>Hi %s,</p>' .
                    '<p>Thank you for enrolling with <strong>The Code Munk (TCM One)</strong>. We have received your payment of <strong>₹%s</strong>.</p>' .
                    '<table style="width:100%%;max-width:500px;border-collapse:collapse;margin:20px 0;">' .
                    '<tr><td style="padding:8px 0;border-bottom:1px solid #EEE;"><strong>Order Number:</strong></td><td style="padding:8px 0;border-bottom:1px solid #EEE;text-align:right;">%s</td></tr>' .
                    '<tr><td style="padding:8px 0;border-bottom:1px solid #EEE;"><strong>Program / Item:</strong></td><td style="padding:8px 0;border-bottom:1px solid #EEE;text-align:right;">%s</td></tr>' .
                    '<tr><td style="padding:8px 0;border-bottom:1px solid #EEE;"><strong>Transaction ID:</strong></td><td style="padding:8px 0;border-bottom:1px solid #EEE;text-align:right;">%s</td></tr>' .
                    '<tr><td style="padding:8px 0;border-bottom:1px solid #EEE;"><strong>Amount Paid:</strong></td><td style="padding:8px 0;border-bottom:1px solid #EEE;text-align:right;">₹%s</td></tr>' .
                    '</table>' .
                    '<p>You can access your cohort details, timetable, and study materials on your student dashboard:</p>' .
                    '<p><a href="https://thecodemunk.in/student" style="display:inline-block;padding:12px 24px;background:#111;color:#FFF;text-decoration:none;border-radius:6px;font-weight:700;">Open Student Portal</a></p>' .
                    '<p>Need assistance? Connect with our academic mentor directly on WhatsApp at <a href="https://wa.me/919238695500">+91 92386 95500</a>.</p>' .
                    '</div>',
                    e($itemTitle),
                    e($studentName),
                    e($amount),
                    e((string)$order['order_number']),
                    e($itemTitle),
                    e($mihpayid),
                    e($amount)
                );
                Mailer::send($studentEmail, "Enrollment & Payment Confirmed — {$itemTitle}", $emailBody);
            }

            // Admin alert
            $adminEmail = (string) config('mail.admin_email');
            if ($adminEmail !== '') {
                Mailer::send(
                    $adminEmail,
                    "💰 New Payment Received: ₹{$amount} from {$studentName} ({$itemTitle})",
                    "<p>New online payment verified on PayU:</p><p><strong>Student:</strong> {$studentName} ({$studentEmail})</p><p><strong>Item:</strong> {$itemTitle}</p><p><strong>Amount:</strong> ₹{$amount}</p><p><strong>PayU Ref:</strong> {$mihpayid}</p>"
                );
            }
        } catch (\Throwable) {}

        // 5. Auto login student if session not already active
        if (!Auth::check()) {
            $userRow = Database::first('SELECT * FROM users WHERE id = ? LIMIT 1', [$userId]);
            if ($userRow) {
                $_SESSION['user_id'] = (int) $userRow['id'];
                $_SESSION['user_role'] = (string) $userRow['role'];
                $_SESSION['user_name'] = (string) $userRow['name'];
                $_SESSION['user_email'] = (string) $userRow['email'];
            }
        }

        Response::redirect('/payment/success?order=' . urlencode((string) $order['order_number']));
    }

    /**
     * POST /payment/payu/failure
     * PayU hosted checkout failure callback.
     */
    public function payuFailure(): void
    {
        $data = $_POST;
        $txnid = (string) ($data['txnid'] ?? '');
        $orderNumber = (string) ($data['udf5'] ?? '');
        $errorMsg = (string) ($data['error_Message'] ?? $data['errorMessage'] ?? 'Transaction was declined or cancelled.');

        if ($txnid !== '' || $orderNumber !== '') {
            Database::run("
                UPDATE orders SET
                    status = 'failed',
                    raw_response = ?,
                    updated_at = NOW()
                WHERE txnid = ? OR order_number = ?
            ", [json_encode($data, JSON_UNESCAPED_SLASHES), $txnid, $orderNumber]);
        }

        Response::redirect('/payment/failure?order=' . urlencode($orderNumber) . '&reason=' . urlencode($errorMsg));
    }

    /**
     * GET /payment/success
     * Render payment success page.
     */
    public function successView(): void
    {
        $orderNumber = Request::string('order');
        $order = null;
        if ($orderNumber !== '') {
            $order = Database::first('SELECT * FROM orders WHERE order_number = ? LIMIT 1', [$orderNumber]);
        }

        $this->view('public/payment/success', [
            'title'       => 'Payment Confirmed — TCM One',
            'description' => 'Your enrollment has been verified and confirmed.',
            'order'       => $order,
        ], null);
    }

    /**
     * GET /payment/failure
     * Render payment failure page.
     */
    public function failureView(): void
    {
        $orderNumber = Request::string('order');
        $reason      = Request::string('reason', 'Payment could not be completed.');
        $order       = null;

        if ($orderNumber !== '') {
            $order = Database::first('SELECT * FROM orders WHERE order_number = ? LIMIT 1', [$orderNumber]);
        }

        $this->view('public/payment/failure', [
            'title'       => 'Payment Incomplete — TCM One',
            'description' => 'Payment transaction was not completed.',
            'order'       => $order,
            'reason'      => $reason,
        ], null);
    }

    private function abortWithError(string $message): void
    {
        if (Request::isJson() || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
            Response::error($message, 422);
        }
        flash('error', $message);
        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/courses');
    }
}
