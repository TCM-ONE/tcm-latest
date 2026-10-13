<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Controller;
use TCM\Core\Database;
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
            'description' => 'Fast-track your tech skills with 7-day live online coding workshops starting at ₹999. Web Development, Python, AI, and DSA.',
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

        if ($course === null) {
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

        $this->view('public/courses/show', [
            'course'     => $course,
            'curriculum' => $curriculum,
            'title'      => $course['title'] . ' | TCM One Live Course',
            'desc'       => $course['subtitle'] ?: $course['description'],
            'canonical'  => $canonical,
            'thumbUrl'   => $thumbUrl,
            'appUrl'     => $appUrl,
        ], null);
    }

    /**
     * GET /download-brochure
     * Public endpoint to download the latest official institute / course brochure.
     */
    public function downloadBrochure(): void
    {
        $brochureFile = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_file'");
        $brochureName = Database::scalar("SELECT `value` FROM settings WHERE `key` = 'brochure_name'");

        $filePath = null;
        $downloadName = $brochureName ?: 'The-Code-Munk-Official-Brochure.pdf';

        if (!empty($brochureFile)) {
            $candidate = config('uploads.path') . '/' . $brochureFile;
            if (is_file($candidate)) {
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

        if ($filePath === null || !is_file($filePath)) {
            flash('info', 'Brochure is being updated. You can explore our programs or contact us directly on WhatsApp!');
            redirect('/programs.html');
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

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
}
