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
    $ensureColumn($pdo, 'courses', 'allow_installments', "allow_installments TINYINT(1) NOT NULL DEFAULT 1 AFTER price");
    $ensureColumn($pdo, 'orders', 'txnid', "txnid VARCHAR(100) DEFAULT NULL AFTER payment_ref");
    $ensureColumn($pdo, 'orders', 'raw_response', "raw_response TEXT DEFAULT NULL AFTER txnid");
    $ensureColumn($pdo, 'installment_schedules', 'installment_plan_id', "installment_plan_id BIGINT UNSIGNED DEFAULT NULL AFTER parent_order_id");
    $ensureColumn($pdo, 'installment_schedules', 'reminder_stage', "reminder_stage TINYINT NOT NULL DEFAULT 0 AFTER reminded_at");
    $ensureColumn($pdo, 'installment_schedules', 'reminder_sent_at', "reminder_sent_at DATETIME DEFAULT NULL AFTER reminder_stage");
    $ensureColumn($pdo, 'payment_transactions', 'installment_schedule_id', "installment_schedule_id BIGINT UNSIGNED DEFAULT NULL AFTER order_id");
    $ensureColumn($pdo, 'payment_transactions', 'installment_plan_id', "installment_plan_id BIGINT UNSIGNED DEFAULT NULL AFTER installment_schedule_id");
    $ensureColumn($pdo, 'payment_transactions', 'refund_reference', "refund_reference VARCHAR(100) DEFAULT NULL AFTER bank_ref_num");

    // -----------------------------------------------------------------
    // 2. Ensure Supporting Tables Exist
    // -----------------------------------------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `installment_plans` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `enrollment_id` BIGINT UNSIGNED DEFAULT NULL,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `course_id` BIGINT UNSIGNED NOT NULL,
            `parent_order_id` BIGINT UNSIGNED NOT NULL,
            `total_amount` DECIMAL(10,2) NOT NULL,
            `upfront_amount` DECIMAL(10,2) NOT NULL,
            `total_installments` INT UNSIGNED NOT NULL,
            `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `remaining_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
            `status` ENUM('active','completed','defaulted','cancelled') NOT NULL DEFAULT 'active',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_ip_user` (`user_id`),
            KEY `idx_ip_course` (`course_id`),
            KEY `idx_ip_order` (`parent_order_id`),
            KEY `idx_ip_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `installment_reminders_log` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `schedule_id` BIGINT UNSIGNED NOT NULL,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `channel` VARCHAR(30) NOT NULL DEFAULT 'email',
            `stage` VARCHAR(30) NOT NULL,
            `recipient` VARCHAR(150) NOT NULL,
            `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `status` ENUM('sent','failed') NOT NULL DEFAULT 'sent',
            `error_message` VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_schedule_channel_stage` (`schedule_id`, `channel`, `stage`),
            KEY `idx_irl_user` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        INSERT INTO `settings` (`key`, `value`) VALUES
            ('installment_enabled', '1'),
            ('installment_min_amount', '5000.00'),
            ('installment_max_installments', '3'),
            ('installment_min_upfront_pct', '40'),
            ('installment_interval_days', '15'),
            ('installment_plans_available', '2,3'),
            ('installment_interest_rate', '0'),
            ('installment_reminder_email_enabled', '1')
        ON DUPLICATE KEY UPDATE `key` = `key`;
    ");
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
    // 4. Ensure Admin User (admin@thecodemunk.in)
    // -----------------------------------------------------------------
    $adminEmail = 'admin@thecodemunk.in';
    $adminPassHash = '$2y$10$QjdOPqj0PXTWneBe.K7MrO2G6UuPdIAhLYqTqbYE/JJaFYfxwcEJu'; // TcmAdmin@2026#Secure!

    $adminStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR email = 'admin@thecodemunk.com' OR id = 1 LIMIT 1");
    $adminStmt->execute([$adminEmail]);
    $adminId = $adminStmt->fetchColumn();

    if ($adminId) {
        $pdo->prepare("UPDATE users SET email = ?, name = 'TCM Admin', password_hash = ?, role = 'admin', status = 'active', email_verified = 1, onboarded = 1 WHERE id = ?")
            ->execute([$adminEmail, $adminPassHash, $adminId]);
        echo "✓ Admin user updated: {$adminEmail}\n";
    } else {
        $pdo->prepare("INSERT INTO users (id, name, email, password_hash, role, status, email_verified, onboarded) VALUES (1, 'TCM Admin', ?, ?, 'admin', 'active', 1, 1)")
            ->execute([$adminEmail, $adminPassHash]);
        echo "✓ Admin user created: {$adminEmail}\n";
    }

    // -----------------------------------------------------------------
    // 5. Archive 23 obsolete 0-module placeholder and test courses
    // -----------------------------------------------------------------
    $pdo->exec("
        UPDATE courses 
        SET status = 'archived', is_featured = 0 
        WHERE id IN (15, 16, 17, 18, 19, 20, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 44, 45)
    ");
    echo "✓ Archived 23 obsolete/placeholder 0-module and test courses\n";

    // -----------------------------------------------------------------
    // 6. Synchronize Course Catalogue (24+ Courses)
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
    // 7. Master Approved 31-Course Pricing & Durations Sync
    // -----------------------------------------------------------------
    $exactCourses = [
        ['id' => 13, 'slug' => 'full-stack-web-development', 'price' => 12999.00, 'orig' => 19999.00, 'duration' => '16 Weeks', 'featured' => 1],
        ['id' => 14, 'slug' => 'frontend-development-with-react', 'price' => 6999.00, 'orig' => 9999.00, 'duration' => '8 Weeks', 'featured' => 1],
        ['id' => 55, 'slug' => 'react-nextjs-frontend-engineering', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '8 Weeks', 'featured' => 0],
        ['id' => 56, 'slug' => 'backend-development-nodejs-apis', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '8 Weeks', 'featured' => 0],
        ['id' => 57, 'slug' => 'mobile-app-development-react-native', 'price' => 9999.00, 'orig' => 14999.00, 'duration' => '10 Weeks', 'featured' => 0],
        ['id' => 50, 'slug' => 'build-your-first-website', 'price' => 2499.00, 'orig' => 3999.00, 'duration' => '4 Weeks', 'featured' => 0],
        ['id' => 61, 'slug' => 'software-testing-qa-playwright', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
        ['id' => 46, 'slug' => 'python-programming-automation', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '8 Weeks', 'featured' => 1],
        ['id' => 47, 'slug' => 'sql-database-fundamentals', 'price' => 3499.00, 'orig' => 4999.00, 'duration' => '4 Weeks', 'featured' => 0],
        ['id' => 63, 'slug' => 'core-java-object-oriented-programming', 'price' => 4999.00, 'orig' => 7499.00, 'duration' => '6 Weeks', 'featured' => 0],
        ['id' => 39, 'slug' => 'python-for-ai-ml', 'price' => 2499.00, 'orig' => 3999.00, 'duration' => '30 Days', 'featured' => 0],
        ['id' => 49, 'slug' => 'digital-skills-computer-fundamentals', 'price' => 1999.00, 'orig' => 3499.00, 'duration' => '4 Weeks', 'featured' => 0],
        ['id' => 54, 'slug' => 'data-analytics-excel-sql-powerbi', 'price' => 9999.00, 'orig' => 14999.00, 'duration' => '10 Weeks', 'featured' => 1],
        ['id' => 64, 'slug' => 'data-science-machine-learning-foundations', 'price' => 12999.00, 'orig' => 18999.00, 'duration' => '14 Weeks', 'featured' => 1],
        ['id' => 42, 'slug' => 'machine-learning-python-projects', 'price' => 2999.00, 'orig' => 4999.00, 'duration' => '40 Days', 'featured' => 1],
        ['id' => 67, 'slug' => 'artificial-intelligence-machine-learning', 'price' => 14999.00, 'orig' => 22999.00, 'duration' => '16 Weeks', 'featured' => 1],
        ['id' => 58, 'slug' => 'generative-ai-agent-engineering', 'price' => 12999.00, 'orig' => 19999.00, 'duration' => '12 Weeks', 'featured' => 1],
        ['id' => 53, 'slug' => 'ai-automation-nocode-tools', 'price' => 6999.00, 'orig' => 9999.00, 'duration' => '6 Weeks', 'featured' => 0],
        ['id' => 37, 'slug' => 'ai-machine-learning-fundamentals', 'price' => 2999.00, 'orig' => 4999.00, 'duration' => '45 Days', 'featured' => 1],
        ['id' => 40, 'slug' => 'ai-agents-workflow-automation', 'price' => 2999.00, 'orig' => 4999.00, 'duration' => '30 Days', 'featured' => 1],
        ['id' => 38, 'slug' => 'prompt-engineering-generative-ai', 'price' => 1499.00, 'orig' => 2499.00, 'duration' => '20 Days', 'featured' => 1],
        ['id' => 51, 'slug' => 'ai-tools-for-study-work', 'price' => 1999.00, 'orig' => 3499.00, 'duration' => '3 Weeks', 'featured' => 0],
        ['id' => 41, 'slug' => 'chatgpt-gemini-ai-productivity-tools', 'price' => 999.00, 'orig' => 1999.00, 'duration' => '15 Days', 'featured' => 1],
        ['id' => 48, 'slug' => 'aws-cloud-deployment-fundamentals', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
        ['id' => 59, 'slug' => 'docker-devops-cicd-essentials', 'price' => 8999.00, 'orig' => 12999.00, 'duration' => '8 Weeks', 'featured' => 0],
        ['id' => 66, 'slug' => 'cybersecurity-ethical-security-foundations', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '8 Weeks', 'featured' => 0],
        ['id' => 21, 'slug' => 'dsa-problem-solving-java', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '12 Weeks', 'featured' => 1],
        ['id' => 52, 'slug' => 'tech-career-launchpad', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '4 Weeks', 'featured' => 0],
        ['id' => 65, 'slug' => 'system-design-microservices-foundations', 'price' => 9999.00, 'orig' => 14999.00, 'duration' => '8 Weeks', 'featured' => 0],
        ['id' => 60, 'slug' => 'ui-ux-design-with-figma', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
        ['id' => 62, 'slug' => 'digital-marketing-seo-analytics', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
    ];

    $syncCourseStmt = $pdo->prepare("
        UPDATE courses SET 
            price = ?, original_price = ?, duration = ?, status = 'published', is_featured = ?
        WHERE slug = ? OR id = ?
    ");
    foreach ($exactCourses as $ec) {
        $syncCourseStmt->execute([$ec['price'], $ec['orig'], $ec['duration'], $ec['featured'], $ec['slug'], $ec['id']]);
    }
    echo "✓ Synchronized exact prices, durations and featured priority for 31 approved master courses\n";

    $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('courses_count', '31') ON DUPLICATE KEY UPDATE `value` = '31'")->execute();

    // -----------------------------------------------------------------
    // 8. Synchronize Editorial Blog Posts (Tech Updates)
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
