<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\WorkshopData;
use TCM\Models\Course;

/**
 * Public, shareable, SEO-ready landing pages for TCM One workshops and courses.
 * Delivers server-rendered Open Graph & Twitter Card tags for crawlers,
 * deep linking, and ad attribution support.
 */
final class PublicWorkshopController extends Controller
{
    /**
     * GET /workshops
     * Public directory of all live workshops.
     */
    public function index(): void
    {
        $workshops = WorkshopData::all();
        $appUrl    = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');

        $this->view('public/workshops/index', [
            'title'       => 'Live Coding Workshops — TCM One',
            'description' => 'Explore beginner-friendly live workshops in Python, frontend web development, AI tools and tech career planning from TCM One.',
            'canonical'   => $appUrl . '/workshops',
            'ogImage'     => $appUrl . '/assets/tcm-og-share.png',
            'workshops'   => $workshops,
            'appUrl'      => $appUrl,
        ], null);
    }

    /**
     * GET /workshops/{slug}
     * Standalone shareable landing page for a specific workshop.
     */
    public function show(array $params): void
    {
        $rawSlug  = (string) ($params['slug'] ?? '');
        $slug     = WorkshopData::resolveSlug($rawSlug);
        $workshop = WorkshopData::find($slug);

        if ($workshop === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'title'   => 'Workshop Not Found — TCM One',
                'message' => 'The requested workshop was not found. Browse our available workshops below.',
            ], null);
            return;
        }

        $appUrl   = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $ogImage  = $appUrl . '/workshops/' . rawurlencode($workshop['slug']) . '/og-image';
        $canonical = $appUrl . '/workshops/' . rawurlencode($workshop['slug']);

        $this->view('public/workshops/show', [
            'workshop'  => $workshop,
            'title'     => $workshop['title'],
            'tagline'   => $workshop['tagline'],
            'desc'      => $workshop['description'],
            'canonical' => $canonical,
            'ogImage'   => $ogImage,
            'appUrl'    => $appUrl,
        ], null);
    }

    /**
     * GET /workshops/{slug}/og-image
     * High-contrast, 1200x630 branded SVG social preview card.
     */
    public function ogImage(array $params): void
    {
        $slug     = WorkshopData::resolveSlug((string) ($params['slug'] ?? ''));
        $workshop = WorkshopData::find($slug);

        $title    = htmlspecialchars($workshop['short_title'] ?? 'Live Coding Workshop', ENT_XML1);
        $tagline  = htmlspecialchars($workshop['tagline'] ?? 'Learn coding with TCM One live cohort', ENT_XML1);
        $price    = '₹' . number_format((float) ($workshop['price'] ?? 999), 0);
        $duration = htmlspecialchars($workshop['duration'] ?? '7-Day Live Workshop', ENT_XML1);
        $category = htmlspecialchars($workshop['category'] ?? 'TCM One Workshop', ENT_XML1);

        header('Content-Type: image/svg+xml; charset=utf-8');
        header('Cache-Control: public, max-age=86400'); // 24h caching

        echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <defs>
    <radialGradient id="bg" cx="80%" cy="20%" r="75%">
      <stop offset="0%" stop-color="#1f1f1f"/>
      <stop offset="100%" stop-color="#0a0a0a"/>
    </radialGradient>
    <pattern id="grid" width="28" height="28" patternUnits="userSpaceOnUse">
      <circle cx="2" cy="2" r="1.2" fill="rgba(255,255,255,0.06)"/>
    </pattern>
  </defs>

  <!-- Background -->
  <rect width="1200" height="630" fill="url(#bg)"/>
  <rect width="1200" height="630" fill="url(#grid)"/>

  <!-- Top Brand Row -->
  <g transform="translate(80, 72)">
    <rect width="52" height="52" fill="#ffffff" rx="0"/>
    <text x="26" y="36" font-family="system-ui, -apple-system, sans-serif" font-size="24" font-weight="900" fill="#000000" text-anchor="middle">T</text>
    <text x="68" y="26" font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="800" fill="#ffffff" letter-spacing="2">TCM ONE</text>
    <text x="68" y="46" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="rgba(255,255,255,0.5)" letter-spacing="1">LIVE WORKSHOP SERIES</text>
  </g>

  <!-- Category Badge -->
  <g transform="translate(80, 160)">
    <rect width="180" height="32" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>
    <text x="90" y="21" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="800" fill="#ffffff" text-anchor="middle" letter-spacing="1.5">{$category}</text>
  </g>

  <!-- Headline -->
  <text x="80" y="260" font-family="system-ui, -apple-system, sans-serif" font-size="52" font-weight="900" fill="#ffffff" letter-spacing="-1.5">{$title}</text>

  <!-- Tagline -->
  <text x="80" y="320" font-family="system-ui, -apple-system, sans-serif" font-size="22" font-weight="400" fill="rgba(255,255,255,0.65)">{$tagline}</text>

  <!-- Price & Feature Badges -->
  <g transform="translate(80, 420)">
    <!-- Price Pill -->
    <rect width="160" height="60" fill="#ffffff"/>
    <text x="80" y="41" font-family="system-ui, -apple-system, sans-serif" font-size="28" font-weight="900" fill="#000000" text-anchor="middle">{$price}</text>

    <!-- Duration Pill -->
    <g transform="translate(180, 0)">
      <rect width="260" height="60" fill="none" stroke="#ffffff" stroke-width="2"/>
      <text x="130" y="38" font-family="system-ui, -apple-system, sans-serif" font-size="18" font-weight="700" fill="#ffffff" text-anchor="middle">{$duration}</text>
    </g>

    <!-- Cohort Pill -->
    <g transform="translate(460, 0)">
      <rect width="240" height="60" fill="rgba(255,255,255,0.06)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
      <text x="120" y="38" font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" fill="rgba(255,255,255,0.8)" text-anchor="middle">100% Live Mentored</text>
    </g>
  </g>

  <!-- Bottom Accent -->
  <rect x="0" y="618" width="1200" height="12" fill="#ffffff"/>
</svg>
SVG;
        exit;
    }

    /**
     * GET /courses/{slug}
     * Clean public shareable landing page for courses.
     */
    public function showCourse(array $params): void
    {
        $slug   = (string) ($params['slug'] ?? '');
        $course = Course::findBySlug($slug);

        if ($course === null || ($course['status'] ?? '') !== 'published') {
            http_response_code(404);
            $this->view('errors/404', [
                'title'   => 'Course Not Found — TCM One',
                'message' => 'The requested course was not found.',
            ], null);
            return;
        }

        $appUrl   = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $curriculum = Course::curriculum((int) $course['id']);
        $thumbUrl = course_thumbnail_url($course['thumbnail'] ?? null, $course['slug']);
        $canonical = $appUrl . '/courses/' . rawurlencode($course['slug']);
        $desc = $this->metaDescription((string) ($course['subtitle'] ?: $course['description']));

        $this->view('public/courses/show', [
            'course'     => $course,
            'curriculum' => $curriculum,
            'title'      => $course['title'] . ' | Live Tech Course | TCM One',
            'desc'       => $desc,
            'canonical'  => $canonical,
            'thumbUrl'   => $thumbUrl,
            'appUrl'     => $appUrl,
        ], null);
    }

    private function metaDescription(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        if ($text === '') {
            return 'Explore this TCM One live technology course with practical projects, mentor support, curriculum details, fees and enrollment guidance.';
        }
        if (mb_strlen($text) <= 155) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, 152), " \t\n\r\0\x0B.,;:") . '...';
    }

    /**
     * GET /courses/{slug}/brochure
     * Public endpoint to download a specific course's brochure PDF (with fallback).
     */
    public function showCourseBrochure(array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $this->downloadCourseBrochureBySlug($slug);
    }

    /**
     * GET /download-brochure
     * Public endpoint to download the latest official institute / course brochure.
     */
    public function downloadBrochure(): void
    {
        $courseSlug = Request::string('course');
        if ($courseSlug !== '') {
            $this->downloadCourseBrochureBySlug($courseSlug);
            return;
        }

        $brochureFile = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_file'");
        $brochureName = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_name'");

        $filePath = null;
        $downloadName = $brochureName ?: 'The-Code-Munk-Official-Brochure.pdf';

        if (!empty($brochureFile)) {
            $candidate = config('uploads.path') . '/' . $brochureFile;
            if (is_file($candidate) && strtolower(pathinfo($candidate, PATHINFO_EXTENSION)) === 'pdf') {
                $filePath = $candidate;
            }
        }

        // Fallback to default starter brochure if custom brochure not uploaded or missing
        if ($filePath === null) {
            $defaultCandidates = [
                config('uploads.path') . '/brochure/tcm-brochure.pdf',
                BASE_PATH . '/assets/tcm-brochure.pdf',
            ];
            foreach ($defaultCandidates as $candidatePath) {
                if (is_file($candidatePath)) {
                    $filePath = $candidatePath;
                    $downloadName = 'The-Code-Munk-Official-Brochure.pdf';
                    break;
                }
            }
        }

        $this->streamFile($filePath, $downloadName);
    }

    /**
     * Helper to download course brochure by slug with graceful fallbacks.
     */
    private function downloadCourseBrochureBySlug(string $slug): void
    {
        $course = Course::findBySlug($slug);
        $filePath = null;
        $downloadName = null;

        if ($course !== null) {
            $titleSlug = slugify((string)($course['title'] ?? 'Course'));
            if (!empty($course['brochure_file'])) {
                $candidate = dirname(__DIR__, 2) . '/' . ltrim((string)$course['brochure_file'], '/');
                if (is_file($candidate) && strtolower(pathinfo($candidate, PATHINFO_EXTENSION)) === 'pdf') {
                    $filePath = $candidate;
                    $downloadName = !empty($course['brochure_name'])
                        ? $course['brochure_name']
                        : $titleSlug . '-Brochure.pdf';
                }
            }
            if ($downloadName === null) {
                $downloadName = $titleSlug . '-Brochure.pdf';
            }
        }

        // Fallback 1: Global brochure in settings
        if ($filePath === null) {
            $globalFile = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_file'");
            if (!empty($globalFile)) {
                $candidate = config('uploads.path') . '/' . $globalFile;
                if (is_file($candidate) && strtolower(pathinfo($candidate, PATHINFO_EXTENSION)) === 'pdf') {
                    $filePath = $candidate;
                }
            }
        }

        // Fallback 2: Default static brochure
        if ($filePath === null) {
            $defaultCandidates = [
                config('uploads.path') . '/brochure/tcm-brochure.pdf',
                BASE_PATH . '/assets/tcm-brochure.pdf',
            ];
            foreach ($defaultCandidates as $cand) {
                if (is_file($cand)) {
                    $filePath = $cand;
                    break;
                }
            }
        }

        if ($downloadName === null) {
            $downloadName = 'The-Code-Munk-Official-Brochure.pdf';
        }

        $this->streamFile($filePath, $downloadName);
    }

    /**
     * Stream a file to the browser with standard download headers.
     */
    private function streamFile(?string $filePath, string $downloadName): void
    {
        if ($filePath === null || !is_file($filePath)) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Brochure PDF is being updated. Please contact TCM One for the latest copy.';
            exit;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            http_response_code(415);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Only PDF brochures are available for public download.';
            exit;
        }
        $contentType = 'application/pdf';
        $nameExt = strtolower(pathinfo($downloadName, PATHINFO_EXTENSION));
        if ($nameExt !== '' && $nameExt !== 'pdf') {
            $downloadName = pathinfo($downloadName, PATHINFO_FILENAME) . '.pdf';
        } elseif (!str_ends_with(strtolower($downloadName), '.pdf')) {
            $downloadName .= '.pdf';
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . addslashes((string)$downloadName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * GET /courses
     * Public course catalog with search and filters.
     */
    public function coursesCatalog(): void
    {
        $appUrl     = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
        $courses    = Course::listWithCategory(['status' => 'published']);
        $categories = Database::all('
            SELECT cat.id, cat.name, cat.slug, cat.sort_order, COUNT(c.id) as course_count
            FROM categories cat
            INNER JOIN courses c ON c.category_id = cat.id AND c.status = "published"
            GROUP BY cat.id, cat.name, cat.slug, cat.sort_order
            ORDER BY cat.sort_order ASC, cat.name ASC
        ');

        $this->view('public/courses/index', [
            'title'       => 'All Courses & Career Paths — TCM One',
            'description' => 'Explore practical tech courses in Full Stack, Python, AI & Machine Learning, Data Analytics, and Cloud with live mentorship at TCM One.',
            'canonical'   => $appUrl . '/courses',
            'ogImage'     => $appUrl . '/assets/tcm-og-share.png',
            'courses'     => $courses,
            'categories'  => $categories,
            'appUrl'      => $appUrl,
        ], null);
    }

    /**
     * GET /for-institutions
     * B2B college & institutional partnerships page.
     */
    public function forInstitutions(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');

        $this->view('public/institutions/index', [
            'title'       => 'College & Training Institute Partnerships — TCM One',
            'description' => 'Partner with TCM One to offer structured technical training, practical projects, and career-focused learning to your students.',
            'canonical'   => $appUrl . '/for-institutions',
            'ogImage'     => $appUrl . '/assets/tcm-og-share.png',
            'appUrl'      => $appUrl,
        ], null);
    }

    /**
     * GET /about
     * TCM One story, mission, philosophy & credibility.
     */
    public function about(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');

        $this->view('public/about/index', [
            'title'       => 'About TCM One — Practical Education For Modern Careers',
            'description' => 'Discover the mission, teaching philosophy, and verified learning approach behind TCM One.',
            'canonical'   => $appUrl . '/about',
            'ogImage'     => $appUrl . '/assets/tcm-og-share.png',
            'appUrl'      => $appUrl,
        ], null);
    }

    /**
     * GET /contact
     * Dedicated contact page with direct phone, email, and inquiry form.
     */
    public function contact(): void
    {
        $appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');

        $this->view('public/about/contact', [
            'title'       => 'Contact Admissions & Mentors — TCM One',
            'description' => 'Get in touch with TCM One. Ask questions about courses, workshops, fees, and enrollment.',
            'canonical'   => $appUrl . '/contact',
            'ogImage'     => $appUrl . '/assets/tcm-og-share.png',
            'appUrl'      => $appUrl,
        ], null);
    }

    public function privacy(): void
    {
        $file = BASE_PATH . '/privacy.html';
        if (is_file($file)) {
            header('Content-Type: text/html; charset=utf-8');
            readfile($file);
            exit;
        }
        Response::redirect('/');
    }

    public function terms(): void
    {
        $file = BASE_PATH . '/terms.html';
        if (is_file($file)) {
            header('Content-Type: text/html; charset=utf-8');
            readfile($file);
            exit;
        }
        Response::redirect('/');
    }

    /** Serve only the explicitly allowed public information pages. */
    public function policy(): void
    {
        $pages = [
            '/refund-policy' => 'refund-policy.html',
            '/internship-placement-policy' => 'internship-placement-policy.html',
            '/certificate-policy' => 'certificate-policy.html',
            '/course-information' => 'course-information.html',
        ];
        $file = $pages[Request::path()] ?? null;
        if ($file === null || !is_file(BASE_PATH . '/' . $file)) {
            http_response_code(404);
            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        readfile(BASE_PATH . '/' . $file);
    }

    public function partnerPolicy(): void
    {
        $file = BASE_PATH . '/partner-policy.html';
        if (is_file($file)) {
            header('Content-Type: text/html; charset=utf-8');
            readfile($file);
            exit;
        }
        Response::redirect('/for-institutions');
    }

    public function sitemap(): void
    {
        $file = BASE_PATH . '/sitemap.html';
        if (is_file($file)) {
            header('Content-Type: text/html; charset=utf-8');
            readfile($file);
            exit;
        }
        Response::redirect('/');
    }
}


