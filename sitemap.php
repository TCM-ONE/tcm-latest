<?php

declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

use TCM\Core\Database;
use TCM\Core\WorkshopData;

$appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
$today = (new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d');

$urls = [
    ['loc' => '/',                      'lastmod' => $today, 'changefreq' => 'weekly',  'priority' => '1.0'],
    ['loc' => '/courses',                'lastmod' => $today, 'changefreq' => 'weekly',  'priority' => '0.9'],
    ['loc' => '/workshops',              'lastmod' => $today, 'changefreq' => 'weekly',  'priority' => '0.9'],
    ['loc' => '/tech-updates',           'lastmod' => $today, 'changefreq' => 'daily',   'priority' => '0.8'],
    ['loc' => '/for-institutions',       'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['loc' => '/about',                  'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => '/contact',                'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => '/learners-reviews.html',  'lastmod' => $today, 'changefreq' => 'weekly',  'priority' => '0.6'],
    ['loc' => '/community.html',         'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => '/partner-policy.html',    'lastmod' => $today, 'changefreq' => 'yearly',  'priority' => '0.4'],
    ['loc' => '/privacy.html',           'lastmod' => $today, 'changefreq' => 'yearly',  'priority' => '0.3'],
    ['loc' => '/terms.html',             'lastmod' => $today, 'changefreq' => 'yearly',  'priority' => '0.3'],
    ['loc' => '/refund-policy', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/internship-placement-policy', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/certificate-policy', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/course-information', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => '/sitemap.html',           'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.3'],
];

// Published Courses from DB (Single source of truth)
try {
    $courses = Database::all("SELECT slug, updated_at FROM courses WHERE status = 'published' ORDER BY id ASC");
    foreach ($courses as $c) {
        $slug = (string) ($c['slug'] ?? '');
        if ($slug === '') continue;
        $mod = substr((string) ($c['updated_at'] ?? $today), 0, 10) ?: $today;
        $urls[] = [
            'loc' => '/courses/' . rawurlencode($slug),
            'lastmod' => $mod,
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];
    }
} catch (Throwable) {
    // Graceful fallback if database connection is temporarily unavailable
}

// Live Workshops from WorkshopData
try {
    $workshops = WorkshopData::all();
    foreach ($workshops as $w) {
        $slug = (string) ($w['slug'] ?? '');
        if ($slug === '') continue;
        $urls[] = [
            'loc' => '/workshops/' . rawurlencode($slug),
            'lastmod' => $today,
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];
    }
} catch (Throwable) {
    // Graceful fallback
}

// Published Blog & Tech Updates from DB ("blogs bhi seo hone chahiye")
try {
    $posts = Database::all("SELECT slug, published_at, updated_at FROM posts WHERE status = 'published' ORDER BY published_at DESC");
    foreach ($posts as $p) {
        $slug = (string) ($p['slug'] ?? '');
        if ($slug === '') continue;
        $timestamp = $p['updated_at'] ?: $p['published_at'] ?: $today;
        $mod = substr((string) $timestamp, 0, 10) ?: $today;
        $urls[] = [
            'loc' => '/tech-updates/' . rawurlencode($slug),
            'lastmod' => $mod,
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ];
    }
} catch (Throwable) {
    // Graceful fallback
}

if (!headers_sent()) {
    header('Content-Type: application/xml; charset=UTF-8');
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($urls as $url) {
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($appUrl . $url['loc'], ENT_XML1) . "</loc>\n";
    echo '    <lastmod>' . htmlspecialchars((string) $url['lastmod'], ENT_XML1) . "</lastmod>\n";
    echo '    <changefreq>' . htmlspecialchars((string) $url['changefreq'], ENT_XML1) . "</changefreq>\n";
    echo '    <priority>' . htmlspecialchars((string) $url['priority'], ENT_XML1) . "</priority>\n";
    echo "  </url>\n";
}
echo "</urlset>\n";
