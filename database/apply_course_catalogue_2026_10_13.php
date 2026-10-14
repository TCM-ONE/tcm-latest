<?php
/**
 * TCM One production catalogue deployer.
 *
 * Applies the exact 24-course catalogue from course_catalogue_2026_10_13.json
 * into the backend database. It updates existing rows by canonical slug,
 * legacy slug, or exact title to preserve course IDs and enrolments.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Core/Database.php';

use TCM\Core\Database;

date_default_timezone_set('Asia/Kolkata');

$cataloguePath = __DIR__ . '/course_catalogue_2026_10_13.json';

$categoryMap = [
    'Programming Foundations' => ['slug' => 'programming-foundations', 'icon' => 'bi-code-square', 'audience' => 'beginners', 'order' => 1],
    'Web & Mobile'            => ['slug' => 'web-mobile-development', 'icon' => 'bi-window-stack', 'audience' => 'college', 'order' => 2],
    'Data & Analytics'        => ['slug' => 'data-analytics', 'icon' => 'bi-graph-up', 'audience' => 'college', 'order' => 3],
    'AI & Automation'         => ['slug' => 'ai-automation', 'icon' => 'bi-robot', 'audience' => 'working', 'order' => 4],
    'Cloud & DevOps'          => ['slug' => 'cloud-devops', 'icon' => 'bi-cloud-check', 'audience' => 'working', 'order' => 5],
    'Career & DSA'            => ['slug' => 'career-dsa', 'icon' => 'bi-briefcase', 'audience' => 'college', 'order' => 6],
    'Design & Business'       => ['slug' => 'design-business', 'icon' => 'bi-palette', 'audience' => 'beginners', 'order' => 7],
];

$legacySlugs = [
    'full-stack-web-development-mern' => 'full-stack-web-development',
    'frontend-development-react' => 'frontend-development-with-react',
    'dsa-with-java' => 'dsa-problem-solving-java',
    'aws-cloud-fundamentals' => 'aws-cloud-deployment-fundamentals',
    'digital-skills-computer-basics' => 'digital-skills-computer-fundamentals',
    'web-development-basics' => 'build-your-first-website',
    'ai-productivity-tools' => 'ai-tools-for-study-work',
    'ai-automation-no-code' => 'ai-automation-nocode-tools',
    'react-nextjs-advanced' => 'react-nextjs-frontend-engineering',
    'nodejs-backend-api' => 'backend-development-nodejs-apis',
    'react-native-app-development' => 'mobile-app-development-react-native',
    'genai-ai-agents' => 'generative-ai-agent-engineering',
    'docker-devops-cicd' => 'docker-devops-cicd-essentials',
    'uiux-figma' => 'ui-ux-design-with-figma',
    'qa-automation-playwright' => 'software-testing-qa-playwright',
    'digital-marketing-seo' => 'digital-marketing-seo-analytics',
    'core-java-oop' => 'core-java-object-oriented-programming',
    'data-science-machine-learning' => 'data-science-machine-learning-foundations',
    'system-design-microservices' => 'system-design-microservices-foundations',
    'cybersecurity-essentials' => 'cybersecurity-ethical-security-foundations',
];

$thumbnails = [
    'full-stack-web-development-mern' => 'coding-fullstack.gif',
    'frontend-development-react' => 'coding-frontend.gif',
    'python-programming-automation' => 'coding-python.gif',
    'sql-database-fundamentals' => 'coding-database.gif',
    'dsa-with-java' => 'coding-algorithm.gif',
    'aws-cloud-fundamentals' => 'coding-cloud-new.gif',
    'digital-skills-computer-basics' => 'coding-basics.gif',
    'web-development-basics' => 'coding-webdev.gif',
    'ai-productivity-tools' => 'coding-ai.gif',
    'tech-career-launchpad' => 'coding-career.gif',
    'ai-automation-no-code' => 'coding-automation.gif',
    'data-analytics-excel-sql-powerbi' => 'coding-analytics.gif',
    'react-nextjs-advanced' => 'coding-frontend.gif',
    'nodejs-backend-api' => 'coding-backend.gif',
    'react-native-app-development' => 'coding-mobile.gif',
    'genai-ai-agents' => 'coding-ai.gif',
    'docker-devops-cicd' => 'coding-cloud-new.gif',
    'uiux-figma' => 'coding-design.gif',
    'qa-automation-playwright' => 'coding-testing.gif',
    'digital-marketing-seo' => 'coding-marketing.gif',
    'core-java-oop' => 'coding-java.gif',
    'data-science-machine-learning' => 'coding-datascience.gif',
    'system-design-microservices' => 'coding-backend.gif',
    'cybersecurity-essentials' => 'coding-terminal.gif',
    'artificial-intelligence-machine-learning' => 'ai-machine-learning.svg',
    'ai-machine-learning-fundamentals' => 'ai-machine-learning.svg',
    'prompt-engineering-generative-ai' => 'genai-ai-agents.svg',
    'python-for-ai-ml' => 'coding-python.gif',
    'ai-agents-workflow-automation' => 'coding-automation.gif',
    'chatgpt-gemini-ai-productivity-tools' => 'coding-ai.gif',
    'machine-learning-python-projects' => 'coding-algorithm.gif',
];

$icons = [
    'Web & Mobile' => 'bi-window-stack',
    'Programming Foundations' => 'bi-code-square',
    'Career & DSA' => 'bi-diagram-3',
    'Cloud & DevOps' => 'bi-cloud-check',
    'AI & Automation' => 'bi-robot',
    'Data & Analytics' => 'bi-graph-up',
    'Design & Business' => 'bi-palette',
];

function ensureColumn(PDO $pdo, string $table, string $column, string $definition): void
{
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
    $stmt->execute([$column]);
    if (!$stmt->fetchColumn()) {
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN $definition");
        echo "Added missing column {$table}.{$column}\n";
    }
}

function courseLevel(string $level): string
{
    $value = strtolower($level);
    if (str_contains($value, 'advanced')) {
        return 'advanced';
    }
    if (str_contains($value, 'intermediate')) {
        return 'intermediate';
    }
    return 'beginner';
}

function courseStatus(string $displayStatus): string
{
    return stripos($displayStatus, 'publish') !== false ? 'published' : 'draft';
}

function splitModules(string $modules): array
{
    $parts = array_filter(array_map('trim', explode(';', $modules)));
    return array_values($parts);
}

echo "=== Applying TCM One 24-course catalogue (2026-10-13) ===\n";

if (!is_file($cataloguePath)) {
    fwrite(STDERR, "Missing catalogue JSON: {$cataloguePath}\n");
    exit(1);
}

$data = json_decode((string) file_get_contents($cataloguePath), true);
if (!is_array($data) || !isset($data['courses']) || !is_array($data['courses'])) {
    fwrite(STDERR, "Invalid course catalogue JSON.\n");
    exit(1);
}

try {
    $pdo = Database::connection();
    $pdo->beginTransaction();

    ensureColumn($pdo, 'courses', 'brochure_file', "brochure_file VARCHAR(255) DEFAULT NULL AFTER thumbnail");
    ensureColumn($pdo, 'courses', 'brochure_name', "brochure_name VARCHAR(255) DEFAULT NULL AFTER brochure_file");

    $catIds = [];
    foreach ($categoryMap as $name => $meta) {
        $existing = $pdo->prepare('SELECT id FROM categories WHERE slug = ? LIMIT 1');
        $existing->execute([$meta['slug']]);
        $id = $existing->fetchColumn();
        if ($id) {
            $pdo->prepare('UPDATE categories SET name = ?, description = ?, icon = ?, audience = ?, sort_order = ?, status = "active" WHERE id = ?')
                ->execute([$name, $name . ' courses', $meta['icon'], $meta['audience'], $meta['order'], $id]);
            $catIds[$name] = (int) $id;
        } else {
            $pdo->prepare('INSERT INTO categories (name, slug, description, icon, audience, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, "active")')
                ->execute([$name, $meta['slug'], $name . ' courses', $meta['icon'], $meta['audience'], $meta['order']]);
            $catIds[$name] = (int) $pdo->lastInsertId();
        }
    }

    $created = 0;
    $updated = 0;

    foreach ($data['courses'] as $course) {
        $slug = (string) $course['slug'];
        $legacySlug = $legacySlugs[$slug] ?? null;
        $publicSlug = $legacySlug ?? $slug;
        $title = (string) $course['title'];
        $category = (string) $course['category'];
        $catId = $catIds[$category] ?? null;
        $status = courseStatus((string) $course['display_status']);
        $moduleParts = splitModules((string) ($course['modules'] ?? ''));

        $lookupSql = 'SELECT id FROM courses WHERE slug = ? OR title = ?';
        $lookupParams = [$slug, $title];
        if ($legacySlug !== null) {
            $lookupSql .= ' OR slug = ?';
            $lookupParams[] = $legacySlug;
        }
        $lookupSql .= ' ORDER BY FIELD(slug, ?, ?) DESC, id ASC LIMIT 1';
        $lookupParams[] = $publicSlug;
        $lookupParams[] = $slug;

        $stmt = $pdo->prepare($lookupSql);
        $stmt->execute($lookupParams);
        $courseId = $stmt->fetchColumn();

        $payload = [
            'category_id' => $catId,
            'title' => $title,
            'slug' => $publicSlug,
            'subtitle' => (string) $course['card_description'],
            'description' => (string) $course['full_description'],
            'icon' => $icons[$category] ?? 'bi-journal-code',
            'thumbnail' => $thumbnails[$slug] ?? 'coding-fullstack.gif',
            'level' => courseLevel((string) $course['level']),
            'language' => 'Hindi + English',
            'duration' => ((int) $course['duration_weeks']) . ' Weeks',
            'price' => (float) $course['proposed_fee_inr'],
            'original_price' => null,
            'total_seats' => 0,
            'seats_left' => 0,
            'certificate' => 1,
            'schedule' => 'Subject to scheduled batch',
            'starts_at' => null,
            'is_featured' => $status === 'published' ? 1 : 0,
            'is_bestseller' => 0,
            'status' => $status,
        ];

        if ($courseId) {
            $sets = implode(', ', array_map(static fn ($key) => "`$key` = :$key", array_keys($payload)));
            $payload['id'] = (int) $courseId;
            $pdo->prepare("UPDATE courses SET {$sets} WHERE id = :id")->execute($payload);
            $updated++;
        } else {
            $columns = array_keys($payload);
            $columnSql = '`' . implode('`, `', $columns) . '`';
            $valueSql = ':' . implode(', :', $columns);
            $pdo->prepare("INSERT INTO courses ({$columnSql}) VALUES ({$valueSql})")->execute($payload);
            $courseId = (int) $pdo->lastInsertId();
            $created++;
        }

        $pdo->prepare('DELETE FROM course_modules WHERE course_id = ?')->execute([(int) $courseId]);

        $position = 1;
        foreach ($moduleParts as $moduleTitle) {
            $pdo->prepare('INSERT INTO course_modules (course_id, title, summary, position) VALUES (?, ?, ?, ?)')
                ->execute([(int) $courseId, $moduleTitle, 'From the approved 2026-10-13 course catalogue.', $position]);
            $moduleId = (int) $pdo->lastInsertId();
            $pdo->prepare('INSERT INTO course_lessons (module_id, title, type, duration_minutes, position, is_preview) VALUES (?, ?, "live", 60, 1, ?)')
                ->execute([$moduleId, $moduleTitle . ' live practice and assignment', $position === 1 ? 1 : 0]);
            $position++;
        }
    }

    $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('courses_count', '24') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)")
        ->execute();

    $pdo->commit();

    echo "Created: {$created}\n";
    echo "Updated: {$updated}\n";
    echo "Catalogue applied successfully.\n";
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
    exit(1);
}
