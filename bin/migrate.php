<?php
/**
 * TCM One: Automated Database Migration & Catalogue Synchronizer
 * Safe, idempotent CLI script for production deployments and local setup.
 *
 * Usage:
 *   php bin/migrate.php
 */

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use TCM\Core\Database;
use TCM\Core\WorkshopData;
use TCM\Models\Course;

date_default_timezone_set('Asia/Kolkata');

echo "=== TCM One: Starting Automated Database Migration ===\n";

try {
    $pdo = Database::connection();

    $ensureColumn = static function (\PDO $pdo, string $table, string $column, string $definition): void {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE " . $pdo->quote($column));
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN $definition");
            echo "✓ Added column {$table}.{$column}\n";
        }
    };

    // -----------------------------------------------------------------
    // 1. Ensure Missing Columns
    // -----------------------------------------------------------------
    $ensureColumn($pdo, 'courses', 'brochure_file', "brochure_file VARCHAR(255) DEFAULT NULL AFTER thumbnail");
    $ensureColumn($pdo, 'courses', 'brochure_name', "brochure_name VARCHAR(255) DEFAULT NULL AFTER brochure_file");

    // -----------------------------------------------------------------
    // 2. Ensure Supporting Tables Exist
    // -----------------------------------------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS workshop_leads (
            id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            workshop_id             BIGINT UNSIGNED DEFAULT NULL,
            workshop_slug           VARCHAR(190)    NOT NULL,
            workshop_title          VARCHAR(220)    NOT NULL,
            full_name               VARCHAR(150)    NOT NULL,
            email                   VARCHAR(190)    NOT NULL,
            phone                   VARCHAR(30)     NOT NULL,
            current_status          ENUM('student','working','other') NOT NULL DEFAULT 'student',
            college                 VARCHAR(190)    DEFAULT NULL,
            city                    VARCHAR(120)    DEFAULT NULL,
            learning_goal           TEXT            DEFAULT NULL,
            selected_workshop_price DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
            lead_status             ENUM('new','contacted','interested','payment_pending','enrolled','not_interested','lost') NOT NULL DEFAULT 'new',
            payment_status          ENUM('not_initiated','pending','paid','failed','refunded') NOT NULL DEFAULT 'not_initiated',
            enrollment_status       ENUM('pending','confirmed','waitlist','cancelled') NOT NULL DEFAULT 'pending',
            utm_source              VARCHAR(120)    DEFAULT NULL,
            utm_medium              VARCHAR(120)    DEFAULT NULL,
            utm_campaign            VARCHAR(120)    DEFAULT NULL,
            utm_content             VARCHAR(120)    DEFAULT NULL,
            landing_page            VARCHAR(255)    DEFAULT NULL,
            referrer                VARCHAR(255)    DEFAULT NULL,
            admin_notes             TEXT            DEFAULT NULL,
            consent_timestamp       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_ws_leads_status (lead_status),
            KEY idx_ws_leads_pay_status (payment_status),
            KEY idx_ws_leads_slug (workshop_slug),
            KEY idx_ws_leads_email (email),
            KEY idx_ws_leads_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_batches (
            id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            item_type               ENUM('course','workshop') NOT NULL DEFAULT 'course',
            item_id                 BIGINT UNSIGNED NOT NULL,
            batch_code              VARCHAR(60)     NOT NULL,
            batch_title             VARCHAR(190)    NOT NULL,
            registration_opens_at   DATETIME        DEFAULT NULL,
            registration_closes_at  DATETIME        DEFAULT NULL,
            starts_at               DATETIME        NOT NULL,
            ends_at                 DATETIME        DEFAULT NULL,
            capacity                INT             NOT NULL DEFAULT 30,
            seats_left              INT             NOT NULL DEFAULT 30,
            status                  ENUM('upcoming','registration_open','registration_closed','in_progress','completed','cancelled') NOT NULL DEFAULT 'upcoming',
            is_featured             TINYINT(1)      NOT NULL DEFAULT 0,
            timezone                VARCHAR(40)     NOT NULL DEFAULT 'Asia/Kolkata',
            created_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_batches_item (item_type, item_id),
            KEY idx_batches_status (status),
            KEY idx_batches_starts (starts_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS post_reactions (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            reaction VARCHAR(20) NOT NULL DEFAULT 'like',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_post_user_reaction (post_id, user_id, reaction),
            KEY idx_post_reactions_post (post_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS post_comments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            body TEXT NOT NULL,
            status ENUM('pending','approved','hidden') NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_post_comments_post_status (post_id, status, created_at),
            KEY idx_post_comments_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "✓ Supporting database tables verified.\n";

    // -----------------------------------------------------------------
    // 3. Ensure Canonical Categories
    // -----------------------------------------------------------------
    $canonicalCategories = [
        ['name' => 'Programming Foundations',    'slug' => 'programming-foundations',    'desc' => 'Core programming logic, languages and fundamentals',     'icon' => 'bi-code-square',     'audience' => 'beginners', 'order' => 1],
        ['name' => 'Web & Mobile Development',   'slug' => 'web-mobile-development',     'desc' => 'Modern full stack, frontend, backend and mobile apps',    'icon' => 'bi-window-stack',    'audience' => 'college',   'order' => 2],
        ['name' => 'Data & Analytics',           'slug' => 'data-analytics',             'desc' => 'Data analysis, SQL, Power BI, Python and ML foundations', 'icon' => 'bi-graph-up',        'audience' => 'college',   'order' => 3],
        ['name' => 'AI & Automation',            'slug' => 'ai-automation',              'desc' => 'AI tools, Generative AI, AI agents and workflow automation','icon' => 'bi-robot',          'audience' => 'working',   'order' => 4],
        ['name' => 'Cloud & DevOps',             'slug' => 'cloud-devops',               'desc' => 'Cloud infrastructure, Docker, CI/CD, AWS and security',   'icon' => 'bi-cloud-check',     'audience' => 'working',   'order' => 5],
        ['name' => 'Career & DSA',               'slug' => 'career-dsa',                 'desc' => 'Data structures, algorithms, problem solving and career', 'icon' => 'bi-briefcase',       'audience' => 'college',   'order' => 6],
        ['name' => 'Design & Business',          'slug' => 'design-business',            'desc' => 'UI/UX design with Figma, digital marketing and growth',   'icon' => 'bi-palette',         'audience' => 'beginners', 'order' => 7],
    ];

    $catMap = [];
    foreach ($canonicalCategories as $cat) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? OR name = ? LIMIT 1");
        $stmt->execute([$cat['slug'], $cat['name']]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $catMap[$cat['slug']] = (int)$existingId;
            $catMap[$cat['name']] = (int)$existingId;
            $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, icon = ?, audience = ?, sort_order = ?, status = 'active' WHERE id = ?")
                ->execute([$cat['name'], $cat['slug'], $cat['desc'], $cat['icon'], $cat['audience'], $cat['order'], $existingId]);
        } else {
            $pdo->prepare("INSERT INTO categories (name, slug, description, icon, audience, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, 'active')")
                ->execute([$cat['name'], $cat['slug'], $cat['desc'], $cat['icon'], $cat['audience'], $cat['order']]);
            $newId = (int)$pdo->lastInsertId();
            $catMap[$cat['slug']] = $newId;
            $catMap[$cat['name']] = $newId;
        }
    }

    // Deactivate empty legacy categories (e.g. 'College Special', 'Beginners & Non-IT', 'Working Professionals')
    $pdo->exec("
        UPDATE categories c
        SET status = 'inactive'
        WHERE slug IN ('college-special', 'beginners-non-it', 'working-professionals')
          AND NOT EXISTS (SELECT 1 FROM courses co WHERE co.category_id = c.id AND co.status = 'published')
    ");

    echo "✓ Canonical categories active: " . count($canonicalCategories) . "\n";

    // -----------------------------------------------------------------
    // 4. Synchronize Course Catalogue (24+ Courses)
    // -----------------------------------------------------------------
    $cataloguePath = dirname(__DIR__) . '/database/course_catalogue_2026_10_13.json';
    if (is_file($cataloguePath)) {
        $catData = json_decode((string)file_get_contents($cataloguePath), true);
        if (isset($catData['courses']) && is_array($catData['courses'])) {
            $categoryNameMap = [
                'Programming Foundations' => 'programming-foundations',
                'Web & Mobile'            => 'web-mobile-development',
                'Web & Mobile Development'=> 'web-mobile-development',
                'Data & Analytics'        => 'data-analytics',
                'AI & Automation'         => 'ai-automation',
                'Cloud & DevOps'          => 'cloud-devops',
                'Career & DSA'            => 'career-dsa',
                'Design & Business'       => 'design-business',
            ];

            $legacySlugMap = [
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

            $courseCount = 0;
            foreach ($catData['courses'] as $c) {
                $rawSlug = (string)($c['slug'] ?? '');
                $slug = $legacySlugMap[$rawSlug] ?? $rawSlug;
                $title = (string)($c['title'] ?? '');
                $catRaw = (string)($c['category'] ?? '');
                $catSlug = $categoryNameMap[$catRaw] ?? 'programming-foundations';
                $catId = $catMap[$catSlug] ?? 1;

                $level = 'beginner';
                $rawLvl = strtolower((string)($c['level'] ?? ''));
                if (str_contains($rawLvl, 'advanced')) $level = 'advanced';
                elseif (str_contains($rawLvl, 'intermediate')) $level = 'intermediate';

                $price = (float)($c['price'] ?? 3999);
                $origPrice = (float)($c['original_price'] ?? 6999);
                $duration = (string)($c['duration'] ?? '8 Weeks');
                $rating = (float)($c['rating'] ?? 4.8);
                $reviewsCount = (int)($c['reviews_count'] ?? 120);
                $studentsCount = (int)($c['students_count'] ?? 450);
                $thumbnail = (string)($c['thumbnail'] ?? 'coding-fullstack.gif');
                $subtitle = (string)($c['card_description'] ?? '');
                $description = (string)($c['full_description'] ?? $subtitle);

                // Guaranteed non-colliding lookup:
                // 1. Check if course already exists with canonical slug
                $stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = ? LIMIT 1");
                $stmt->execute([$slug]);
                $existingCourseId = $stmt->fetchColumn() ?: null;

                // 2. Fallback to exact title
                if (!$existingCourseId) {
                    $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = ? LIMIT 1");
                    $stmt->execute([$title]);
                    $existingCourseId = $stmt->fetchColumn() ?: null;
                }

                // 3. Fallback to legacy/raw slug only if canonical slug is vacant
                if (!$existingCourseId && $rawSlug !== $slug) {
                    $stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = ? LIMIT 1");
                    $stmt->execute([$rawSlug]);
                    $existingCourseId = $stmt->fetchColumn() ?: null;
                }

                if ($existingCourseId) {
                    $pdo->prepare("
                        UPDATE courses SET
                            category_id = ?,
                            title = ?,
                            slug = ?,
                            subtitle = ?,
                            description = ?,
                            level = ?,
                            duration = ?,
                            price = ?,
                            original_price = ?,
                            rating = ?,
                            reviews_count = ?,
                            students_count = ?,
                            thumbnail = ?,
                            status = 'published'
                        WHERE id = ?
                    ")->execute([
                        $catId, $title, $slug, $subtitle, $description,
                        $level, $duration, $price, $origPrice,
                        $rating, $reviewsCount, $studentsCount, $thumbnail,
                        $existingCourseId
                    ]);
                    $cId = (int)$existingCourseId;
                } else {
                    $pdo->prepare("
                        INSERT INTO courses (
                            category_id, instructor_id, title, slug, subtitle, description,
                            icon, thumbnail, level, duration, price, original_price,
                            rating, reviews_count, students_count, total_seats, seats_left,
                            schedule, starts_at, is_featured, is_bestseller, status
                        ) VALUES (
                            ?, 1, ?, ?, ?, ?,
                            'bi-code-slash', ?, ?, ?, ?, ?,
                            ?, ?, ?, 24, 8,
                            '7:00 PM - 8:30 PM', '2026-11-01', 0, 0, 'published'
                        )
                    ")->execute([
                        $catId, $title, $slug, $subtitle, $description,
                        $thumbnail, $level, $duration, $price, $origPrice,
                        $rating, $reviewsCount, $studentsCount
                    ]);
                    $cId = (int)$pdo->lastInsertId();
                }

                // If course has no modules yet, seed basic modules from modules field
                $hasModules = (int)$pdo->query("SELECT COUNT(*) FROM course_modules WHERE course_id = $cId")->fetchColumn();
                if ($hasModules === 0 && !empty($c['modules'])) {
                    $modules = array_filter(array_map('trim', explode(';', (string)$c['modules'])));
                    $pos = 1;
                    foreach ($modules as $mTitle) {
                        $pdo->prepare("INSERT INTO course_modules (course_id, title, summary, position) VALUES (?, ?, 'Guided learning sessions and assignments', ?)")
                            ->execute([$cId, $mTitle, $pos]);
                        $mId = (int)$pdo->lastInsertId();
                        $pdo->prepare("INSERT INTO course_lessons (module_id, title, type, position, is_preview) VALUES (?, ?, 'live', 1, ?)")
                            ->execute([$mId, $mTitle . ' — Live Practical Session', $pos === 1 ? 1 : 0]);
                        $pos++;
                    }
                }
                $courseCount++;
            }
            echo "✓ Synchronized {$courseCount} courses.\n";
        }
    }

    // -----------------------------------------------------------------
    // 5. Synchronize Editorial Blog Posts (Tech Updates)
    // -----------------------------------------------------------------
    $postSqlFile = dirname(__DIR__) . '/database/seed_it_industry_situation_post.sql';
    if (is_file($postSqlFile)) {
        $postSql = (string)file_get_contents($postSqlFile);
        if (trim($postSql) !== '') {
            $pdo->exec($postSql);
            echo "✓ Synchronized editorial blog post: IT Industry Situation.\n";
        }
    }

    // -----------------------------------------------------------------
    // 6. Verify Total Published Courses & Categories
    // -----------------------------------------------------------------
    $totalCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn();
    $totalCategories = (int)$pdo->query("
        SELECT COUNT(DISTINCT c.id) 
        FROM categories c 
        JOIN courses co ON co.category_id = c.id 
        WHERE co.status = 'published'
    ")->fetchColumn();

    echo "=== Migration Complete: {$totalCourses} published courses across {$totalCategories} active categories ===\n";
    exit(0);

} catch (\Throwable $e) {
    fwrite(STDERR, "[!] Migration failed: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
    exit(1);
}
