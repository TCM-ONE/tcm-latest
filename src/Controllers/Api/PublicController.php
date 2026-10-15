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
use TCM\Models\WorkshopLead;
use TCM\Models\Batch;

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
            $course['price_usd']        = round(((float)($course['price'] ?? 0)) / 83.5);
            $course['original_price_usd'] = !empty($course['original_price']) ? round(((float)$course['original_price']) / 83.5) : 0;
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
        $course['price_usd']        = round(((float)($course['price'] ?? 0)) / 83.5);
        $course['original_price_usd'] = !empty($course['original_price']) ? round(((float)$course['original_price']) / 83.5) : 0;
        $course['curriculum']       = Course::curriculum((int) $course['id']);
        $course['reviews'] = Database::all(
            "SELECT cr.rating, cr.comment, cr.created_at, u.name
             FROM course_reviews cr JOIN users u ON u.id = cr.user_id
             WHERE cr.course_id = ? AND cr.status = 'approved' ORDER BY cr.created_at DESC",
            [(int) $course['id']]
        );
        Response::success($course, 'Course fetched.');
    }

    /**
     * GET /api/categories
     * Return all active categories with published course counts, min price and tags.
     */
    public function categories(): void
    {
        $categories = Database::all('
            SELECT 
                cat.id, 
                cat.name, 
                cat.slug, 
                cat.description, 
                cat.icon,
                cat.audience,
                cat.sort_order,
                COUNT(c.id) as course_count, 
                MIN(c.price) as min_price
            FROM categories cat 
            INNER JOIN courses c ON c.category_id = cat.id 
            WHERE c.status = "published" 
            GROUP BY cat.id, cat.name, cat.slug, cat.description, cat.icon, cat.audience, cat.sort_order
            ORDER BY cat.sort_order ASC, course_count DESC
        ');

        $tagMap = [
            'ai-automation'           => ['Generative AI', 'LLM Engineering', 'AI Agents', 'Python', 'Prompt Engineering'],
            'web-mobile-development'  => ['Full Stack MERN', 'React.js', 'Node.js', 'TypeScript', 'Next.js', 'MongoDB'],
            'data-analytics'          => ['Data Analysis', 'SQL', 'Power BI', 'Pandas', 'Machine Learning'],
            'programming-foundations' => ['Python 3', 'OOP Concepts', 'REST APIs', 'Automation Scripts'],
            'cloud-devops'            => ['Docker', 'CI/CD Pipelines', 'AWS Cloud', 'Linux Architecture', 'Git'],
            'career-dsa'              => ['Data Structures', 'Algorithms', 'Java', 'Interview Problem Solving'],
            'design-business'         => ['UI/UX Design', 'Figma', 'Design Systems', 'Digital Strategy'],
        ];

        foreach ($categories as &$cat) {
            $cat['course_count'] = (int) $cat['course_count'];
            $cat['min_price']    = (float) $cat['min_price'];
            $cat['formatted_min_price'] = '₹' . number_format($cat['min_price'], 0);
            $cat['tags']         = $tagMap[$cat['slug']] ?? ['Practical Projects', 'Live Sessions', 'Industry Mentored'];
            $cat['url']          = base_url('/courses?cat=' . rawurlencode((string)$cat['name']));
        }

        Response::success($categories, 'Categories fetched.');
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

    /**
     * POST /api/demo-lead
     * Capture a free demo request for premium courses only.
     */
    public function demoLead(): void
    {
        $name      = trim(Request::string('name'));
        $email     = trim(Request::string('email'));
        $phone     = trim(Request::string('phone'));
        $slug      = trim(Request::string('course_slug'));
        $slot      = trim(Request::string('preferred_slot'));
        $goal      = trim(Request::string('goal'));

        if ($name === '') {
            Response::error('Please enter your full name.', 422);
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Please enter a valid email address.', 422);
        }
        $cleanPhone = preg_replace('/[^\d]/', '', $phone) ?? '';
        if (strlen($cleanPhone) < 10) {
            Response::error('Please enter a valid 10-digit phone number.', 422);
        }
        if ($slug === '') {
            Response::error('Please choose a course for your demo.', 422);
        }

        $course = Course::findBySlug($slug);
        if ($course === null) {
            Response::error('Selected course was not found.', 404);
        }

        $price = (float)($course['price'] ?? 0);

        $courseTitle = (string)($course['title'] ?? ucwords(str_replace('-', ' ', $slug)));
        $messageParts = [
            'Requested free demo for ' . $courseTitle,
            'Course price: ₹' . number_format($price, 0),
        ];
        if ($slot !== '') {
            $messageParts[] = 'Preferred slot: ' . $slot;
        }
        if ($goal !== '') {
            $messageParts[] = 'Goal: ' . $goal;
        }

        $user = Auth::user();
        $leadId = Lead::capture([
            'user_id'        => $user['id'] ?? null,
            'name'           => $name,
            'email'          => $email,
            'phone'          => $phone,
            'interest_type'  => 'course',
            'interest_id'    => (int)($course['id'] ?? 0),
            'interest_title' => $courseTitle,
            'message'        => implode("\n", $messageParts),
            'source'         => 'free_demo_modal',
        ]);

        try {
            $adminEmail = (string) config('mail.admin_email');
            if ($adminEmail !== '') {
                Mailer::send(
                    $adminEmail,
                    'Free Demo Request: ' . $name . ' (' . $courseTitle . ')',
                    Mailer::template(
                        'New Free Demo Request',
                        sprintf(
                            '<p><strong>%s</strong> requested a free demo for <strong>%s</strong>.</p>' .
                            '<ul><li><strong>Email:</strong> %s</li><li><strong>Phone:</strong> %s</li><li><strong>Preferred slot:</strong> %s</li><li><strong>Goal:</strong> %s</li></ul>',
                            e($name),
                            e($courseTitle),
                            e($email),
                            e($phone),
                            e($slot ?: 'Not specified'),
                            e($goal ?: 'Not specified')
                        )
                    )
                );
            }
        } catch (\Throwable) {
            // Non-blocking
        }

        Response::success([
            'lead_id'      => $leadId,
            'course_title' => $courseTitle,
        ], 'Thanks! Your demo request has been received.');
    }

    /**
     * GET /api/workshops/featured
     * Fetch active featured workshops (e.g. Python Starter & 7-Day Frontend) with active batch and seat info.
     */
    public function featuredWorkshops(): void
    {
        $workshops = Database::all(
            "SELECT * FROM events 
             WHERE is_featured_home = 1 AND status != 'cancelled'
             ORDER BY home_sort_order ASC, id ASC"
        );

        $result = [];
        foreach ($workshops as $ws) {
            $batch = Batch::activeFor('workshop', (int)$ws['id']);
            $seatsLeft = $batch ? (int)($batch['seats_left'] ?? 0) : Event::seatsLeft($ws);
            $totalSeats = $batch ? (int)($batch['capacity'] ?? 50) : (int)($ws['total_seats'] ?? 50);

            $result[] = [
                'id'              => (int)$ws['id'],
                'slug'            => $ws['slug'],
                'title'           => $ws['title'],
                'subtitle'        => $ws['subtitle'] ?? '',
                'description'     => $ws['description'] ?? '',
                'price'           => (float)$ws['price'],
                'formatted_price' => '₹' . number_format((float)$ws['price']),
                'duration'        => $ws['duration'] ?? 'Live Workshop',
                'level'           => ucfirst((string)($ws['level'] ?? 'Beginner')),
                'event_date'      => $ws['event_date'],
                'event_time'      => $ws['event_time'],
                'batch'           => $batch,
                'seats_left'      => $seatsLeft,
                'total_seats'     => $totalSeats,
                'is_filling_fast' => $seatsLeft > 0 && $seatsLeft <= 15,
                'is_sold_out'     => $seatsLeft <= 0,
            ];
        }

        Response::success($result, 'Featured workshops fetched.');
    }

    /**
     * POST /api/workshops/register
     * Process instant workshop registration lead with duplicate prevention and validation.
     */
    public function registerWorkshop(): void
    {
        $fullName = trim(Request::string('full_name') ?: Request::string('name'));
        $email    = strtolower(trim(Request::string('email')));
        $phone    = trim(Request::string('phone'));
        $slug     = trim(Request::string('workshop_slug'));

        if ($fullName === '') {
            Response::error('Please enter your full name.', 422);
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Please enter a valid email address.', 422);
        }

        $cleanPhone = preg_replace('/[^\d]/', '', $phone) ?? '';
        if (strlen($cleanPhone) < 10) {
            Response::error('Please enter a valid 10-digit mobile number.', 422);
        }

        $workshop = Event::findBySlug($slug);
        if (!$workshop && is_numeric($slug)) {
            $workshop = Event::find((int)$slug);
        }

        $workshopTitle = $workshop['title'] ?? ($slug !== '' ? ucwords(str_replace('-', ' ', $slug)) : 'Live Workshop');
        $workshopId    = $workshop['id'] ?? null;
        $workshopPrice = isset($workshop['price']) ? (float)$workshop['price'] : 0.0;

        $leadResult = WorkshopLead::capture([
            'workshop_id'             => $workshopId,
            'workshop_slug'           => $slug,
            'workshop_title'          => $workshopTitle,
            'full_name'               => $fullName,
            'email'                   => $email,
            'phone'                   => $phone,
            'current_status'          => Request::string('current_status', 'student'),
            'college'                 => Request::string('college'),
            'city'                    => Request::string('city'),
            'learning_goal'           => Request::string('learning_goal'),
            'selected_workshop_price' => $workshopPrice,
            'utm_source'              => Request::string('utm_source'),
            'utm_medium'              => Request::string('utm_medium'),
            'utm_campaign'            => Request::string('utm_campaign'),
            'utm_content'             => Request::string('utm_content'),
            'landing_page'            => Request::string('landing_page', '/index.html'),
            'referrer'                => Request::string('referrer', $_SERVER['HTTP_REFERER'] ?? ''),
        ]);

        // Send email alert to admin if configured
        try {
            $adminEmail = (string) config('mail.admin_email');
            if ($adminEmail !== '') {
                Mailer::send(
                    $adminEmail,
                    'New Workshop Lead: ' . $fullName . ' (' . $workshopTitle . ')',
                    Mailer::template(
                        'Workshop Registration Lead',
                        sprintf(
                            '<p>New registration received for <strong>%s</strong> (₹%s):</p>' .
                            '<ul><li><strong>Name:</strong> %s</li><li><strong>Email:</strong> %s</li><li><strong>Phone:</strong> %s</li><li><strong>College/City:</strong> %s</li></ul>',
                            e($workshopTitle),
                            e((string)$workshopPrice),
                            e($fullName),
                            e($email),
                            e($phone),
                            e(trim(Request::string('college') . ' ' . Request::string('city')))
                        )
                    )
                );
            }
        } catch (\Throwable) {
            // Non-blocking
        }

        $message = $leadResult['is_duplicate']
            ? 'We already received your request recently! Our team will contact you shortly.'
            : 'Seat reservation received! You will receive confirmation on WhatsApp & Email.';

        Response::success([
            'lead_id'        => $leadResult['id'],
            'is_duplicate'   => $leadResult['is_duplicate'],
            'workshop_title' => $workshopTitle,
            'price'          => $workshopPrice,
        ], $message);
    }
}

