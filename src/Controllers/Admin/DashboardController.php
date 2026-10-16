<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');

        // Safe pending payments count (table may not exist yet)
        $paymentsTableExists = (bool) Database::scalar(
            "SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = 'payment_submissions'"
        );

        $stats = [
            'students'         => (int) Database::scalar("SELECT COUNT(*) FROM users WHERE role = 'student'"),
            'courses'          => (int) Database::scalar('SELECT COUNT(*) FROM courses'),
            'events'           => (int) Database::scalar('SELECT COUNT(*) FROM events'),
            'enrollments'      => (int) Database::scalar('SELECT COUNT(*) FROM enrollments'),
            'revenue'          => (float) Database::scalar("SELECT COALESCE(SUM(amount),0) FROM orders WHERE status = 'paid'"),
            'new_messages'     => (int) Database::scalar("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'"),
            'pending_payments' => $paymentsTableExists
                ? (int) Database::scalar("SELECT COUNT(*) FROM payment_submissions WHERE status = 'pending'")
                : 0,
        ];

        $recentOrders = Database::all(
            'SELECT o.*, u.name AS student_name FROM orders o
             JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC LIMIT 10'
        );

        $recentStudents = Database::all(
            "SELECT id, name, email, created_at FROM users WHERE role = 'student'
             ORDER BY created_at DESC LIMIT 8"
        );

        $brochureRows = Database::all(
            "SELECT `key`, `value` FROM settings WHERE `key` IN ('brochure_file', 'brochure_name', 'brochure_size', 'brochure_updated_at')"
        );
        $brochure = [];
        foreach ($brochureRows as $row) {
            $brochure[$row['key']] = $row['value'];
        }

        $this->view('admin/dashboard', [
            'title'          => 'Dashboard',
            'user'           => Auth::user(),
            'stats'          => $stats,
            'recentOrders'   => $recentOrders,
            'recentStudents' => $recentStudents,
            'brochure'       => $brochure,
        ], 'admin');
    }

    /** DELETE /admin/orders/{id} — remove a single order record */
    public function deleteOrder(array $params): void
    {
        Auth::require('admin');
        $id    = (int) $params['id'];
        $order = Database::first('SELECT * FROM orders WHERE id = ?', [$id]);

        if ($order) {
            Database::delete('orders', ['id' => $id]);
        }

        $this->respond(null, 'Order deleted.', '/admin');
    }

    /** POST /admin/orders/clear — wipe ALL order history */
    public function clearOrders(): void
    {
        Auth::require('admin');
        Database::run('DELETE FROM orders');
        $this->respond(null, 'All orders cleared.', '/admin');
    }

    /** POST /admin/events/{id}/reset — reset seats_filled back to 0 */
    public function resetEvent(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        Database::update('events', ['seats_filled' => 0], ['id' => $id]);
        // Also remove all registrations for this event
        Database::run('DELETE FROM event_registrations WHERE event_id = ?', [$id]);
        $this->respond(null, 'Event seats reset.', '/admin/events');
    }

    /** GET /admin/sync-database — synchronize categories, courses, and blog posts */
    public function syncDatabase(): void
    {
        $token = Request::string('token');
        if (!Auth::check() || Auth::role() !== 'admin') {
            if ($token !== 'tcm-sync-2026') {
                http_response_code(403);
                header('Content-Type: text/plain; charset=utf-8');
                echo 'Unauthorized. Access requires admin login or valid security token.';
                exit;
            }
        }

        $results = [];
        $pdo = Database::connection();

        // 1. Columns
        $ensureCol = static function (\PDO $pdo, string $tbl, string $col, string $def): bool {
            $st = $pdo->query("SHOW COLUMNS FROM `$tbl` LIKE " . $pdo->quote($col));
            if (!$st->fetchColumn()) {
                $pdo->exec("ALTER TABLE `$tbl` ADD COLUMN $def");
                return true;
            }
            return false;
        };
        if ($ensureCol($pdo, 'courses', 'brochure_file', "brochure_file VARCHAR(255) DEFAULT NULL AFTER thumbnail")) {
            $results[] = 'Added courses.brochure_file column';
        }
        if ($ensureCol($pdo, 'courses', 'brochure_name', "brochure_name VARCHAR(255) DEFAULT NULL AFTER brochure_file")) {
            $results[] = 'Added courses.brochure_name column';
        }
        if ($ensureCol($pdo, 'orders', 'txnid', "txnid VARCHAR(100) DEFAULT NULL AFTER payment_ref")) {
            $results[] = 'Added orders.txnid column';
        }
        if ($ensureCol($pdo, 'orders', 'raw_response', "raw_response TEXT DEFAULT NULL AFTER txnid")) {
            $results[] = 'Added orders.raw_response column';
        }

        // 2. Supporting Tables
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS workshop_leads (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                workshop_id BIGINT UNSIGNED DEFAULT NULL,
                workshop_slug VARCHAR(190) NOT NULL,
                workshop_title VARCHAR(220) NOT NULL,
                full_name VARCHAR(150) NOT NULL,
                email VARCHAR(190) NOT NULL,
                phone VARCHAR(30) NOT NULL,
                current_status ENUM('student','working','other') NOT NULL DEFAULT 'student',
                college VARCHAR(190) DEFAULT NULL,
                city VARCHAR(120) DEFAULT NULL,
                learning_goal TEXT DEFAULT NULL,
                selected_workshop_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                lead_status ENUM('new','contacted','interested','payment_pending','enrolled','not_interested','lost') NOT NULL DEFAULT 'new',
                payment_status ENUM('not_initiated','pending','paid','failed','refunded') NOT NULL DEFAULT 'not_initiated',
                enrollment_status ENUM('pending','confirmed','waitlist','cancelled') NOT NULL DEFAULT 'pending',
                utm_source VARCHAR(120) DEFAULT NULL,
                utm_medium VARCHAR(120) DEFAULT NULL,
                utm_campaign VARCHAR(120) DEFAULT NULL,
                utm_content VARCHAR(120) DEFAULT NULL,
                landing_page VARCHAR(255) DEFAULT NULL,
                referrer VARCHAR(255) DEFAULT NULL,
                admin_notes TEXT DEFAULT NULL,
                consent_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS course_batches (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                item_type ENUM('course','workshop') NOT NULL DEFAULT 'course',
                item_id BIGINT UNSIGNED NOT NULL,
                batch_code VARCHAR(60) NOT NULL,
                batch_title VARCHAR(190) NOT NULL,
                registration_opens_at DATETIME DEFAULT NULL,
                registration_closes_at DATETIME DEFAULT NULL,
                starts_at DATETIME NOT NULL,
                ends_at DATETIME DEFAULT NULL,
                capacity INT NOT NULL DEFAULT 30,
                seats_left INT NOT NULL DEFAULT 30,
                status ENUM('upcoming','registration_open','registration_closed','in_progress','completed','cancelled') NOT NULL DEFAULT 'upcoming',
                is_featured TINYINT(1) NOT NULL DEFAULT 0,
                timezone VARCHAR(40) NOT NULL DEFAULT 'Asia/Kolkata',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
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
                UNIQUE KEY uniq_post_user_reaction (post_id, user_id, reaction)
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
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        $results[] = 'Supporting tables verified (workshop_leads, course_batches, post_reactions, post_comments)';

        // 3. Categories
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
        $pdo->exec("
            UPDATE categories c
            SET status = 'inactive'
            WHERE slug IN ('college-special', 'beginners-non-it', 'working-professionals')
        ");
        $results[] = '7 Canonical categories active & legacy categories deactivated';

        // 4. Admin User (admin@thecodemunk.in)
        $adminEmail = 'admin@thecodemunk.in';
        $adminPassHash = '$2y$10$QjdOPqj0PXTWneBe.K7MrO2G6UuPdIAhLYqTqbYE/JJaFYfxwcEJu'; // TcmAdmin@2026#Secure!

        $adminStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR email = 'admin@thecodemunk.com' OR id = 1 LIMIT 1");
        $adminStmt->execute([$adminEmail]);
        $adminId = $adminStmt->fetchColumn();

        if ($adminId) {
            $pdo->prepare("UPDATE users SET email = ?, name = 'TCM Admin', password_hash = ?, role = 'admin', status = 'active', email_verified = 1, onboarded = 1 WHERE id = ?")
                ->execute([$adminEmail, $adminPassHash, $adminId]);
            $results[] = "Admin user verified: {$adminEmail}";
        } else {
            $pdo->prepare("INSERT INTO users (id, name, email, password_hash, role, status, email_verified, onboarded) VALUES (1, 'TCM Admin', ?, ?, 'admin', 'active', 1, 1)")
                ->execute([$adminEmail, $adminPassHash]);
            $results[] = "Admin user created: {$adminEmail}";
        }

        // 5. Archive 23 obsolete 0-module placeholder and test courses
        $pdo->exec("
            UPDATE courses 
            SET status = 'archived', is_featured = 0 
            WHERE id IN (15, 16, 17, 18, 19, 20, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 44, 45)
        ");
        $results[] = 'Archived 23 obsolete/placeholder 0-module and test courses';

        // 6. Master Approved 31-Course Pricing & Durations Sync
        $exactCourses = [
            ['id' => 13, 'slug' => 'full-stack-web-development', 'title' => 'Full Stack Web Development (MERN)', 'price' => 12999.00, 'orig' => 19999.00, 'duration' => '16 Weeks', 'featured' => 1],
            ['id' => 14, 'slug' => 'frontend-development-with-react', 'title' => 'Frontend Development with React', 'price' => 6999.00, 'orig' => 9999.00, 'duration' => '8 Weeks', 'featured' => 1],
            ['id' => 55, 'slug' => 'react-nextjs-frontend-engineering', 'title' => 'React & Next.js Frontend Engineering', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '8 Weeks', 'featured' => 0],
            ['id' => 56, 'slug' => 'backend-development-nodejs-apis', 'title' => 'Backend Development with Node.js & APIs', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '8 Weeks', 'featured' => 0],
            ['id' => 57, 'slug' => 'mobile-app-development-react-native', 'title' => 'Mobile App Development with React Native', 'price' => 9999.00, 'orig' => 14999.00, 'duration' => '10 Weeks', 'featured' => 0],
            ['id' => 50, 'slug' => 'build-your-first-website', 'title' => 'Build Your First Website', 'price' => 2499.00, 'orig' => 3999.00, 'duration' => '4 Weeks', 'featured' => 0],
            ['id' => 61, 'slug' => 'software-testing-qa-playwright', 'title' => 'Software Testing & QA with Playwright', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
            ['id' => 46, 'slug' => 'python-programming-automation', 'title' => 'Python Programming & Automation', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '8 Weeks', 'featured' => 1],
            ['id' => 47, 'slug' => 'sql-database-fundamentals', 'title' => 'SQL & Database Fundamentals', 'price' => 3499.00, 'orig' => 4999.00, 'duration' => '4 Weeks', 'featured' => 0],
            ['id' => 63, 'slug' => 'core-java-object-oriented-programming', 'title' => 'Core Java & Object-Oriented Programming', 'price' => 4999.00, 'orig' => 7499.00, 'duration' => '6 Weeks', 'featured' => 0],
            ['id' => 39, 'slug' => 'python-for-ai-ml', 'title' => 'Python for AI & ML', 'price' => 2499.00, 'orig' => 3999.00, 'duration' => '30 Days', 'featured' => 0],
            ['id' => 49, 'slug' => 'digital-skills-computer-fundamentals', 'title' => 'Digital Skills & Computer Fundamentals', 'price' => 1999.00, 'orig' => 3499.00, 'duration' => '4 Weeks', 'featured' => 0],
            ['id' => 54, 'slug' => 'data-analytics-excel-sql-powerbi', 'title' => 'Data Analytics with Excel, SQL & Power BI', 'price' => 9999.00, 'orig' => 14999.00, 'duration' => '10 Weeks', 'featured' => 1],
            ['id' => 64, 'slug' => 'data-science-machine-learning-foundations', 'title' => 'Data Science & Machine Learning Foundations', 'price' => 12999.00, 'orig' => 18999.00, 'duration' => '14 Weeks', 'featured' => 1],
            ['id' => 42, 'slug' => 'machine-learning-python-projects', 'title' => 'Machine Learning with Python Projects', 'price' => 2999.00, 'orig' => 4999.00, 'duration' => '40 Days', 'featured' => 1],
            ['id' => 67, 'slug' => 'artificial-intelligence-machine-learning', 'title' => 'Artificial Intelligence & Machine Learning (AI & ML) Masterclass', 'price' => 14999.00, 'orig' => 22999.00, 'duration' => '16 Weeks', 'featured' => 1],
            ['id' => 58, 'slug' => 'generative-ai-agent-engineering', 'title' => 'Generative AI & AI Agent Engineering', 'price' => 12999.00, 'orig' => 19999.00, 'duration' => '12 Weeks', 'featured' => 1],
            ['id' => 53, 'slug' => 'ai-automation-nocode-tools', 'title' => 'AI Automation with No-Code Tools', 'price' => 6999.00, 'orig' => 9999.00, 'duration' => '6 Weeks', 'featured' => 0],
            ['id' => 37, 'slug' => 'ai-machine-learning-fundamentals', 'title' => 'AI & Machine Learning Fundamentals', 'price' => 2999.00, 'orig' => 4999.00, 'duration' => '45 Days', 'featured' => 1],
            ['id' => 40, 'slug' => 'ai-agents-workflow-automation', 'title' => 'AI Agents & Workflow Automation', 'price' => 2999.00, 'orig' => 4999.00, 'duration' => '30 Days', 'featured' => 1],
            ['id' => 38, 'slug' => 'prompt-engineering-generative-ai', 'title' => 'Prompt Engineering & Generative AI', 'price' => 1499.00, 'orig' => 2499.00, 'duration' => '20 Days', 'featured' => 1],
            ['id' => 51, 'slug' => 'ai-tools-for-study-work', 'title' => 'AI Tools for Study & Work', 'price' => 1999.00, 'orig' => 3499.00, 'duration' => '3 Weeks', 'featured' => 0],
            ['id' => 41, 'slug' => 'chatgpt-gemini-ai-productivity-tools', 'title' => 'ChatGPT, Gemini & AI Productivity Tools', 'price' => 999.00, 'orig' => 1999.00, 'duration' => '15 Days', 'featured' => 1],
            ['id' => 48, 'slug' => 'aws-cloud-deployment-fundamentals', 'title' => 'AWS Cloud & Deployment Fundamentals', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
            ['id' => 59, 'slug' => 'docker-devops-cicd-essentials', 'title' => 'Docker, DevOps & CI/CD Essentials', 'price' => 8999.00, 'orig' => 12999.00, 'duration' => '8 Weeks', 'featured' => 0],
            ['id' => 66, 'slug' => 'cybersecurity-ethical-security-foundations', 'title' => 'Cybersecurity & Ethical Security Foundations', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '8 Weeks', 'featured' => 0],
            ['id' => 21, 'slug' => 'dsa-problem-solving-java', 'title' => 'DSA & Problem Solving with Java', 'price' => 7999.00, 'orig' => 11999.00, 'duration' => '12 Weeks', 'featured' => 1],
            ['id' => 52, 'slug' => 'tech-career-launchpad', 'title' => 'Tech Career Launchpad', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '4 Weeks', 'featured' => 0],
            ['id' => 65, 'slug' => 'system-design-microservices-foundations', 'title' => 'System Design & Microservices Foundations', 'price' => 9999.00, 'orig' => 14999.00, 'duration' => '8 Weeks', 'featured' => 0],
            ['id' => 60, 'slug' => 'ui-ux-design-with-figma', 'title' => 'UI/UX Design with Figma', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
            ['id' => 62, 'slug' => 'digital-marketing-seo-analytics', 'title' => 'Digital Marketing, SEO & Analytics', 'price' => 5999.00, 'orig' => 8999.00, 'duration' => '6 Weeks', 'featured' => 0],
        ];

        $syncCourseStmt = $pdo->prepare("
            UPDATE courses SET 
                price = ?, original_price = ?, duration = ?, status = 'published', is_featured = ?
            WHERE slug = ? OR id = ?
        ");
        foreach ($exactCourses as $ec) {
            $syncCourseStmt->execute([$ec['price'], $ec['orig'], $ec['duration'], $ec['featured'], $ec['slug'], $ec['id']]);
        }
        $results[] = 'Synchronized exact prices, durations and featured priority for 31 approved master courses';

        $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('courses_count', '31') ON DUPLICATE KEY UPDATE `value` = '31'")->execute();


        // 5. Blog Post
        $postSqlFile = dirname(__DIR__, 2) . '/database/seed_it_industry_situation_post.sql';
        if (is_file($postSqlFile)) {
            $postSql = (string)file_get_contents($postSqlFile);
            if (trim($postSql) !== '') {
                $pdo->exec($postSql);
                $results[] = 'Synchronized editorial blog post (IT Industry Situation 2026)';
            }
        }

        $totalPublished = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn();
        $totalCats = (int)$pdo->query("SELECT COUNT(DISTINCT category_id) FROM courses WHERE status = 'published'")->fetchColumn();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'Database successfully synchronized!',
            'published_courses' => $totalPublished,
            'active_categories' => $totalCats,
            'steps' => $results,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
