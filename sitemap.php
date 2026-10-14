<?php

declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

use TCM\Core\Database;

$appUrl = rtrim((string) config('app.url', 'https://thecodemunk.in'), '/');
$today = (new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d');

$approvedCourseSlugs = [
    'full-stack-web-development',
    'frontend-development-with-react',
    'python-programming-automation',
    'sql-database-fundamentals',
    'dsa-problem-solving-java',
    'aws-cloud-deployment-fundamentals',
    'digital-skills-computer-fundamentals',
    'build-your-first-website',
    'ai-tools-for-study-work',
    'tech-career-launchpad',
    'ai-automation-nocode-tools',
    'data-analytics-excel-sql-powerbi',
    'react-nextjs-frontend-engineering',
    'backend-development-nodejs-apis',
    'mobile-app-development-react-native',
    'generative-ai-agent-engineering',
    'docker-devops-cicd-essentials',
    'ui-ux-design-with-figma',
    'software-testing-qa-playwright',
    'digital-marketing-seo-analytics',
    'core-java-object-oriented-programming',
    'data-science-machine-learning-foundations',
    'system-design-microservices-foundations',
    'cybersecurity-ethical-security-foundations',
    'artificial-intelligence-machine-learning',
    'ai-machine-learning-fundamentals',
    'prompt-engineering-generative-ai',
    'python-for-ai-ml',
    'ai-agents-workflow-automation',
    'chatgpt-gemini-ai-productivity-tools',
    'machine-learning-python-projects',
];

$workshopSlugs = [
    'python-starter-workshop',
    'frontend-website-challenge-7-days',
    'ai-tools-everyday-work-workshop',
    'find-your-tech-career-path-workshop',
];

$urls = [
    ['loc' => '/', 'lastmod' => $today, 'changefreq' => 'weekly', 'priority' => '1.0'],
    ['loc' => '/programs.html', 'lastmod' => $today, 'changefreq' => 'weekly', 'priority' => '0.9'],
    ['loc' => '/workshops', 'lastmod' => $today, 'changefreq' => 'weekly', 'priority' => '0.9'],
    ['loc' => '/insights.html', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => '/community.html', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => '/learners-reviews.html', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => '/partner.html', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['loc' => '/partner-policy.html', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.4'],
    ['loc' => '/contact.html', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => '/privacy.html', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/terms.html', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/sitemap.html', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.3'],
];

try {
    $placeholders = implode(',', array_fill(0, count($approvedCourseSlugs), '?'));
    $courses = Database::all(
        "SELECT slug, updated_at FROM courses WHERE status = 'published' AND slug IN ($placeholders)",
        $approvedCourseSlugs
    );
    $courseLastmods = [];
    foreach ($courses as $course) {
        $courseLastmods[(string) $course['slug']] = substr((string) ($course['updated_at'] ?? $today), 0, 10) ?: $today;
    }
} catch (Throwable) {
    $courseLastmods = [];
}

foreach ($approvedCourseSlugs as $slug) {
    $urls[] = [
        'loc' => '/courses/' . rawurlencode($slug),
        'lastmod' => $courseLastmods[$slug] ?? $today,
        'changefreq' => 'weekly',
        'priority' => '0.8',
    ];
}

foreach ($workshopSlugs as $slug) {
    $urls[] = [
        'loc' => '/workshops/' . rawurlencode($slug),
        'lastmod' => $today,
        'changefreq' => 'weekly',
        'priority' => '0.8',
    ];
}

header('Content-Type: application/xml; charset=UTF-8');

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
