<?php

declare(strict_types=1);

namespace TCM\Controllers\Api;

use TCM\Core\Controller;
use TCM\Core\Auth;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Core\Validator;
use TCM\Core\WhatsApp;
use TCM\Core\Mailer;
use TCM\Models\Course;
use TCM\Models\Event;
use TCM\Models\Lead;
use TCM\Models\Post;
use TCM\Models\Program;
use TCM\Models\Testimonial;

/**
 * Read-only + lightweight write endpoints consumed by the public static site.
 */
final class PublicController extends Controller
{
    /**
     * Auth state + site config for the static front-end (auth-aware nav, WhatsApp).
     */
    public function me(): void
    {
        $user = Auth::user();
        Response::success([
            'authenticated'   => $user !== null,
            'name'            => $user['name'] ?? null,
            'role'            => $user['role'] ?? null,
            'dashboard_url'   => $user === null ? null : ($user['role'] === 'admin' ? '/admin' : '/student'),
            'whatsapp_number' => WhatsApp::number(),
            'csrf_token'      => \TCM\Core\Csrf::token(),
        ], 'OK');
    }

    public function courses(): void
    {
        $courses = Course::listWithCategory([
            'status'   => 'published',
            'audience' => Request::string('audience') ?: null,
            'search'   => Request::string('q') ?: null,
            'featured' => Request::string('featured') ?: null,
        ]);
        foreach ($courses as &$course) {
            $course['seats_filled']     = Course::seatsFilled($course);
            $course['discount_percent'] = Course::discountPercent($course);
            $course['thumbnail_url']    = course_thumbnail_url($course['thumbnail'] ?? null, $course['slug'] ?? null);
            $course['thumbnail']        = $course['thumbnail_url'];
        }
        Response::success($courses, 'Courses fetched.');
    }

    public function course(array $params): void
    {
        $course = Course::findBySlug($params['slug']);
        if ($course === null) {
            Response::error('Course not found.', 404);
        }
        // Ensure seats_filled is always available
        $course['seats_filled']     = Course::seatsFilled($course);
        $course['discount_percent'] = Course::discountPercent($course);
        $course['thumbnail_url']    = course_thumbnail_url($course['thumbnail'] ?? null, $course['slug'] ?? null);
        $course['thumbnail']        = $course['thumbnail_url'];
        $course['curriculum']       = Course::curriculum((int) $course['id']);
        $course['reviews'] = Database::all(
            "SELECT cr.rating, cr.comment, cr.created_at, u.name
             FROM course_reviews cr JOIN users u ON u.id = cr.user_id
             WHERE cr.course_id = ? AND cr.status = 'approved' ORDER BY cr.created_at DESC",
            [(int) $course['id']]
        );
        Response::success($course, 'Course fetched.');
    }

    public function events(): void
    {
        $events = Event::search([
            'status'   => Request::string('status') ?: null,
            'type'     => Request::string('type') ?: null,
            'category' => Request::string('category') ?: null,
            'search'   => Request::string('q') ?: null,
        ]);
        foreach ($events as &$event) {
            $event['seats_left'] = Event::seatsLeft($event);
        }
        Response::success($events, 'Events fetched.');
    }

    public function event(array $params): void
    {
        $event = Event::findBySlug($params['slug']);
        if ($event === null) {
            Response::error('Event not found.', 404);
        }
        $event['seats_left'] = Event::seatsLeft($event);
        Response::success($event, 'Event fetched.');
    }

    public function programs(): void
    {
        $programs = Program::search([
            'status' => 'published',
            'type'   => Request::string('type') ?: null,
            'search' => Request::string('q') ?: null,
        ]);
        Response::success($programs, 'Programs fetched.');
    }

    public function posts(): void
    {
        Response::success(Post::published(), 'Posts fetched.');
    }

    public function testimonials(): void
    {
        Response::success(Testimonial::active(), 'Testimonials fetched.');
    }

    public function contact(): void
    {
        $data = Request::all();
        $validator = Validator::make($data, [
            'name'    => 'required|max:120',
            'email'   => 'required|email',
            'message' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            Response::error('Please check your details.', 422, $validator->errors());
        }

        Database::insert('contact_messages', [
            'name'    => Request::string('name'),
            'email'   => Request::string('email'),
            'subject' => Request::string('subject'),
            'message' => Request::string('message'),
            'status'  => 'new',
        ]);
        Lead::capture([
            'name'          => Request::string('name'),
            'email'         => Request::string('email'),
            'phone'         => Request::string('phone') ?: null,
            'interest_type' => 'contact',
            'message'       => Request::string('message'),
            'source'        => 'contact-form',
        ]);

        $name = Request::string('name');
        $email = Request::string('email');
        $admin = (string) config('mail.admin_email');
        if ($admin !== '') {
            Mailer::send(
                $admin,
                'New contact message from ' . $name,
                Mailer::template('New contact message', sprintf(
                    '<p><strong>%s</strong> &lt;%s&gt; %s</p><p><em>%s</em></p><p>%s</p>',
                    e($name), e($email), e(Request::string('phone') ?: ''),
                    e(Request::string('subject') ?: 'General'),
                    nl2br(e(Request::string('message')))
                ))
            );
        }
        Mailer::send(
            $email,
            'We received your message — The Code Munk',
            Mailer::template('Thanks for reaching out, ' . $name . '!',
                '<p>We have received your message and will reply within 24 hours.</p>')
        );

        Response::success(null, 'Thanks for reaching out! We will reply soon.');
    }

    public function subscribe(): void
    {
        $email = strtolower(Request::string('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('A valid email is required.', 422);
        }
        Database::run(
            'INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)',
            [$email]
        );
        Response::success(null, 'Subscribed! Watch your inbox for updates.');
    }

    /**
     * POST /api/courses/brochure-lead
     * Capture student lead (name, email, phone) before downloading course brochure.
     */
    public function courseBrochureLead(): void
    {
        $name  = trim(Request::string('name'));
        $email = trim(Request::string('email'));
        $phone = trim(Request::string('phone'));
        $slug  = trim(Request::string('course_slug'));

        if ($name === '') {
            Response::error('Please enter your full name.', 422);
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Please enter a valid email address.', 422);
        }
        // Normalize phone: strip non-digits
        $cleanPhone = preg_replace('/[^\d]/', '', $phone) ?? '';
        if (strlen($cleanPhone) < 10) {
            Response::error('Please enter a valid 10-digit phone number.', 422);
        }

        $course = !empty($slug) ? Course::findBySlug($slug) : null;
        $courseTitle = $course['title'] ?? ($slug !== '' ? ucwords(str_replace('-', ' ', $slug)) : 'General Course');
        $courseId = $course['id'] ?? null;

        // Capture lead in database
        $user = Auth::user();
        $leadId = Lead::capture([
            'user_id'        => $user['id'] ?? null,
            'name'           => $name,
            'email'          => $email,
            'phone'          => $phone,
            'interest_type'  => 'course',
            'interest_id'    => $courseId,
            'interest_title' => $courseTitle,
            'message'        => 'Requested Course Brochure PDF for ' . $courseTitle,
            'source'         => 'course_brochure_modal',
        ]);

        // Send email alert to admin if configured
        try {
            $adminEmail = (string) config('mail.admin_email');
            if ($adminEmail !== '') {
                Mailer::send(
                    $adminEmail,
                    'Brochure Download Lead: ' . $name . ' (' . $courseTitle . ')',
                    Mailer::template(
                        'New Brochure Download Lead',
                        sprintf(
                            '<p>A student requested the brochure for <strong>%s</strong>:</p>' .
                            '<ul><li><strong>Name:</strong> %s</li><li><strong>Email:</strong> %s</li><li><strong>Phone:</strong> %s</li></ul>',
                            e($courseTitle),
                            e($name),
                            e($email),
                            e($phone)
                        )
                    )
                );
            }
        } catch (\Throwable) {
            // Non-blocking
        }

        // Return download URL
        $downloadUrl = !empty($slug)
            ? base_url('/courses/' . rawurlencode($slug) . '/brochure')
            : base_url('/download-brochure');

        Response::success([
            'lead_id'      => $leadId,
            'course_title' => $courseTitle,
            'download_url' => $downloadUrl,
        ], 'Thank you! Your brochure download will start automatically.');
    }
}

