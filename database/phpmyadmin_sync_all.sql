-- =====================================================================
-- The Code Munk (TCM One) - Production Database Sync SQL
-- Generated for 1-Click Import in Hostinger phpMyAdmin
-- Synchronizes 30 Live Courses (PDF Pricing), Categories, Events & Engagement Tables
-- Date: 2026-10-16
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. Ensure Table Columns & Missing Engagement Tables ───────────
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `txnid` VARCHAR(100) DEFAULT NULL AFTER `payment_ref`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `raw_response` TEXT DEFAULT NULL AFTER `txnid`;
ALTER TABLE `courses` ADD COLUMN IF NOT EXISTS `seats_filled` INT NOT NULL DEFAULT 0 AFTER `seats_left`;
ALTER TABLE `courses` ADD COLUMN IF NOT EXISTS `brochure_file` VARCHAR(255) DEFAULT NULL AFTER `thumbnail`;
ALTER TABLE `courses` ADD COLUMN IF NOT EXISTS `brochure_name` VARCHAR(255) DEFAULT NULL AFTER `brochure_file`;

CREATE TABLE IF NOT EXISTS `post_reactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `reaction` VARCHAR(20) NOT NULL DEFAULT 'like',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_post_user_reaction` (`post_id`, `user_id`, `reaction`),
  KEY `idx_post_reactions_post` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `post_comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `body` TEXT NOT NULL,
  `status` ENUM('pending','approved','hidden') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_post_comments_post_status` (`post_id`, `status`, `created_at`),
  KEY `idx_post_comments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. Clean & Sync Canonical Categories ─────────────────────────
DELETE FROM `categories`;
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `audience`, `sort_order`, `status`) VALUES
(1, 'Programming Foundations',    'programming-foundations',    'Core programming logic, languages and fundamentals',     'bi-code-square',     'beginners', 1, 'active'),
(2, 'Web & Mobile Development',   'web-mobile-development',     'Modern full stack, frontend, backend and mobile apps',    'bi-window-stack',    'college',   2, 'active'),
(3, 'Data & Analytics',           'data-analytics',             'Data analysis, SQL, Power BI, Python and ML foundations', 'bi-graph-up',        'college',   3, 'active'),
(4, 'AI & Automation',            'ai-automation',              'AI tools, Generative AI, AI agents and workflow automation','bi-robot',          'working',   4, 'active'),
(5, 'Cloud & DevOps',             'cloud-devops',               'Cloud infrastructure, Docker, CI/CD, AWS and security',   'bi-cloud-check',     'working',   5, 'active'),
(6, 'Career & DSA',               'career-dsa',                 'Data structures, algorithms, problem solving and career', 'bi-briefcase',       'college',   6, 'active'),
(7, 'Design & Business',          'design-business',            'UI/UX design with Figma, digital marketing and growth',   'bi-palette',         'beginners', 7, 'active')
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`), `slug` = VALUES(`slug`), `description` = VALUES(`description`),
  `icon` = VALUES(`icon`), `audience` = VALUES(`audience`), `sort_order` = VALUES(`sort_order`), `status` = VALUES(`status`);

-- ── 3. Clean Legacy & Previous Courses ─────────────────────────────
DELETE FROM `course_modules` WHERE `course_id` > 0;
DELETE FROM `courses`;

-- ── 4. Insert/Update 30 Live Production Courses (Exact PDF Pricing) ─
INSERT INTO `courses` (`id`, `category_id`, `instructor_id`, `title`, `slug`, `subtitle`, `description`, `icon`, `thumbnail`, `level`, `duration`, `price`, `original_price`, `rating`, `reviews_count`, `students_count`, `total_seats`, `seats_left`, `seats_filled`, `certificate`, `schedule`, `starts_at`, `is_featured`, `is_bestseller`, `status`) VALUES
(13, 2, NULL, 'Full Stack Web Development (MERN)', 'full-stack-web-development', 'HTML · CSS · JavaScript · React · Node.js · Express · MongoDB · REST APIs', 'Master responsive frontend development, modern JavaScript, React, REST APIs, Node.js, Express, MongoDB, authentication and deployment in a mentor-led, project-based cohort.', 'bi-layers', 'coding-fullstack.gif', 'intermediate', '16 Weeks', 12999.00, 19999.00, 4.9, 148, 520, 30, 12, 18, 1, NULL, NULL, 1, 1, 'published'),
(14, 2, NULL, 'Frontend Development with React', 'frontend-development-with-react', 'HTML · CSS · JavaScript · React · components · hooks · routing · APIs', 'Master responsive websites, JavaScript and React through real projects. Build reusable React components, hooks, routing and API integration.', 'bi-window', 'coding-fullstack.gif', 'beginner', '8 Weeks', 6999.00, 9999.00, 4.8, 112, 410, 30, 15, 15, 1, NULL, NULL, 1, 0, 'published'),
(15, 2, NULL, 'Web Development Fundamentals', 'web-development-fundamentals', 'HTML · CSS · JavaScript · responsive design · Git basics', 'Understand website structure, semantic HTML, modern styling, responsive layouts, core JavaScript and beginner-friendly Git publishing.', 'bi-globe', 'coding-fullstack.gif', 'beginner', '6 Weeks', 3999.00, 5999.00, 4.8, 96, 340, 35, 20, 15, 1, NULL, NULL, 0, 0, 'published'),
(16, 2, NULL, 'React JS', 'react-js', 'JSX · props · state · events · conditional rendering · hooks · reusable components · API calls', 'Deep dive into React JS library. Master JSX, props, state, event handling, hooks, reusable UI components and dynamic REST API calls.', 'bi-boxes', 'coding-fullstack.gif', 'beginner', '6 Weeks', 4999.00, 7999.00, 4.8, 88, 290, 30, 14, 16, 1, NULL, NULL, 0, 0, 'published'),
(17, 2, NULL, 'Backend Development with Node.js', 'backend-development-with-nodejs', 'Node.js · Express · REST APIs · MongoDB · authentication · validation · deployment', 'Build robust backend architectures with Node.js, Express, MongoDB, secure JWT authentication, input validation, error handling and cloud deployment.', 'bi-server', 'coding-fullstack.gif', 'intermediate', '8 Weeks', 6999.00, 9999.00, 4.8, 104, 380, 30, 16, 14, 1, NULL, NULL, 0, 0, 'published'),
(18, 1, NULL, 'Python Programming', 'python-programming', 'Variables · data types · conditions · loops · functions · OOP · files · practical exercises', 'Build solid programming fundamentals in Python. Learn data types, conditionals, loops, functions, object-oriented programming, file I/O and practical exercises.', 'bi-filetype-py', 'coding-fullstack.gif', 'beginner', '6 Weeks', 2999.00, 4999.00, 4.9, 135, 480, 40, 18, 22, 1, NULL, NULL, 1, 1, 'published'),
(19, 2, NULL, 'Python with Django', 'python-with-django', 'Python · Django · models · views · templates · forms · authentication · database', 'Build full-fledged web applications with Python and Django. Master models, views, templates, forms, authentication and relational databases.', 'bi-filetype-py', 'coding-fullstack.gif', 'intermediate', '8 Weeks', 4999.00, 7999.00, 4.8, 76, 260, 30, 15, 15, 1, NULL, NULL, 0, 0, 'published'),
(20, 1, NULL, 'Core Java Programming', 'core-java-programming', 'Java syntax · input · operators · conditions · loops · arrays · strings · OOP', 'Start with Java syntax, variables, conditions, loops, methods, arrays, strings and OOP principles before building console applications.', 'bi-filetype-java', 'coding-fullstack.gif', 'beginner', '6 Weeks', 3499.00, 5999.00, 4.8, 82, 310, 35, 19, 16, 1, NULL, NULL, 0, 0, 'published'),
(21, 6, NULL, 'DSA with Java', 'dsa-with-java', 'Time complexity · arrays · strings · recursion · linked lists · stacks · queues · trees', 'Strengthen data structures, algorithms and interview problem-solving with Java. Covers Big-O, recursion, linked lists, stacks, queues, trees and binary search.', 'bi-cpu', 'coding-fullstack.gif', 'intermediate', '8 Weeks', 3499.00, 5999.00, 4.9, 142, 560, 35, 11, 24, 1, NULL, NULL, 1, 1, 'published'),
(22, 3, NULL, 'Data Analysis', 'data-analysis', 'Excel · SQL · Python · Pandas · data cleaning · visualization · dashboards · reporting', 'Turn raw data into business insights. Learn advanced Excel, SQL querying, Python with Pandas, data cleaning, interactive visualization and KPI reporting.', 'bi-bar-chart-line', 'coding-fullstack.gif', 'beginner', '8 Weeks', 7999.00, 11999.00, 4.8, 118, 420, 30, 13, 17, 1, NULL, NULL, 1, 1, 'published'),
(23, 3, NULL, 'Data Science', 'data-science', 'Python · statistics · Pandas · visualization · machine learning fundamentals · model evaluation', 'Learn Python, statistics, Pandas, exploratory data analysis, introductory machine learning algorithms, Scikit-learn and model evaluation.', 'bi-diagram-3', 'coding-fullstack.gif', 'intermediate', '12 Weeks', 14999.00, 21999.00, 4.9, 125, 490, 25, 9, 16, 1, NULL, NULL, 1, 0, 'published'),
(24, 7, NULL, 'UI/UX Design', 'ui-ux-design', 'User research · wireframes · Figma · prototypes · design systems · usability testing', 'Design intuitive mobile and web experiences in Figma. Learn design thinking, user flows, wireframes, component design systems, prototyping and usability testing.', 'bi-palette', 'coding-fullstack.gif', 'beginner', '6 Weeks', 5999.00, 8999.00, 4.8, 89, 310, 30, 14, 16, 1, NULL, NULL, 0, 0, 'published'),
(25, 1, NULL, 'Git & GitHub Essentials', 'git-github-essentials', 'Version control · branches · commits · pull requests · collaboration', 'Master Git version control from terminal basics to branch management, resolving merge conflicts, creating pull requests and collaborating on GitHub.', 'bi-git', 'coding-fullstack.gif', 'beginner', '3 Weeks', 1499.00, 2499.00, 4.8, 64, 280, 40, 22, 18, 1, NULL, NULL, 0, 0, 'published'),
(26, 2, NULL, 'REST API Development', 'rest-api-development', 'HTTP · API design · CRUD · authentication · Postman · error handling · API documentation', 'Design, build and test production REST APIs. Master HTTP methods, CRUD logic, token authentication, Postman automation, error handling and Swagger documentation.', 'bi-hdd-network', 'coding-fullstack.gif', 'intermediate', '6 Weeks', 3999.00, 5999.00, 4.8, 78, 290, 30, 16, 14, 1, NULL, NULL, 0, 0, 'published'),
(27, 4, NULL, 'AI Tools for Coding', 'ai-tools-for-coding', 'AI-assisted coding · prompting basics · debugging · code review · responsible AI usage', 'Multiply your programming output with AI coding tools. Learn AI prompting for code generation, automated debugging, test creation, code review and responsible AI usage.', 'bi-stars', 'coding-fullstack.gif', 'beginner', '4 Weeks', 1999.00, 3499.00, 4.8, 95, 380, 35, 15, 20, 1, NULL, NULL, 1, 0, 'published'),
(28, 6, NULL, 'Resume & Interview Preparation', 'resume-interview-preparation', 'Resume building · GitHub portfolio · technical interviews · mock interview practice', 'Prepare standout resumes, optimize your GitHub portfolio, practice behavioral and technical interview questions, and participate in guided mock interviews.', 'bi-person-badge', 'coding-fullstack.gif', 'beginner', '4 Weeks', 1999.00, 3499.00, 4.8, 110, 430, 40, 18, 22, 1, NULL, NULL, 0, 0, 'published'),
(29, 2, NULL, 'Advanced JavaScript & TypeScript', 'advanced-javascript-typescript', 'ES6+ · Async JavaScript · TypeScript · Debugging · Mini Projects', 'Level up to modern JS and TypeScript. Master ES6+, event loops, asynchronous programming, TypeScript types, interfaces, generics and strict type-safe development.', 'bi-filetype-js', 'coding-fullstack.gif', 'intermediate', '8 Weeks', 5999.00, 8999.00, 4.8, 84, 320, 30, 14, 16, 1, NULL, NULL, 0, 0, 'published'),
(30, 2, NULL, 'Next.js & Modern React', 'nextjs-modern-react', 'Next.js · App Router · SSR · API Routes · SEO · Deployment', 'Build production web applications with Next.js App Router, Server-Side Rendering (SSR), Static Site Generation (SSG), API routes, SEO optimization and deployment.', 'bi-boxes', 'coding-fullstack.gif', 'intermediate', '8 Weeks', 7999.00, 11999.00, 4.8, 92, 360, 30, 12, 18, 1, NULL, NULL, 0, 0, 'published'),
(31, 2, NULL, 'MERN Stack Projects', 'mern-stack-projects', 'MongoDB · Express · React · Node.js · Authentication · Deployment', '100% project-based full stack cohort. Build full-stack applications with MongoDB, Express, React, Node.js, user authentication, payment flows and live deployment.', 'bi-layers', 'coding-fullstack.gif', 'intermediate', '10 Weeks', 11999.00, 16999.00, 4.9, 130, 470, 30, 10, 20, 1, NULL, NULL, 1, 1, 'published'),
(32, 3, NULL, 'SQL & Database Management', 'sql-database-management', 'SQL · Joins · Normalization · PostgreSQL · Queries · Database Design', 'Query, design and manage relational databases with SQL. Learn data modelling, filtering, joins, aggregations, normalization, indexes and PostgreSQL workflows.', 'bi-database', 'coding-fullstack.gif', 'beginner', '6 Weeks', 3999.00, 5999.00, 4.8, 105, 390, 35, 17, 18, 1, NULL, NULL, 0, 0, 'published'),
(33, 3, NULL, 'Excel for Data Analytics', 'excel-for-data-analytics', 'Excel Formulas · Pivot Tables · Data Cleaning · Charts · Dashboards', 'Master spreadsheet analysis for business. Learn formulas, VLOOKUP, XLOOKUP, Pivot Tables, conditional formatting, data cleaning, charts and KPI dashboards.', 'bi-file-earmark-spreadsheet', 'coding-fullstack.gif', 'beginner', '4 Weeks', 2499.00, 3999.00, 4.8, 88, 340, 40, 20, 20, 1, NULL, NULL, 0, 0, 'published'),
(34, 3, NULL, 'Power BI & Business Intelligence', 'power-bi-business-intelligence', 'Power Query · DAX · Data Modeling · Dashboards · Reports', 'Turn data into interactive executive reports. Master Power Query transformations, DAX expressions, relational data modeling and publish interactive Power BI dashboards.', 'bi-bar-chart-line', 'coding-fullstack.gif', 'beginner', '6 Weeks', 5999.00, 8999.00, 4.8, 96, 370, 30, 13, 17, 1, NULL, NULL, 0, 0, 'published'),
(35, 3, NULL, 'Machine Learning with Python', 'machine-learning-with-python', 'NumPy · Pandas · Scikit-learn · Regression · Classification · Model Evaluation', 'Train, evaluate and deploy machine learning models with Python. Learn NumPy, Pandas, Scikit-learn, regression, classification, clustering and model evaluation.', 'bi-diagram-3', 'coding-fullstack.gif', 'intermediate', '10 Weeks', 12999.00, 18999.00, 4.9, 128, 460, 25, 8, 17, 1, NULL, NULL, 1, 1, 'published'),
(36, 4, NULL, 'Generative AI & Prompt Engineering', 'generative-ai-prompt-engineering', 'Engineering LLMs · Prompt Engineering · AI APIs · RAG Fundamentals · AI Projects', 'Master LLM development, prompt engineering patterns, OpenAI & Gemini APIs, vector databases, RAG architecture and deploy custom AI assistants.', 'bi-cpu-fill', 'coding-fullstack.gif', 'intermediate', '8 Weeks', 6999.00, 9999.00, 4.9, 140, 510, 30, 9, 21, 1, NULL, NULL, 1, 1, 'published'),
(37, 4, NULL, 'AI & Machine Learning Fundamentals', 'ai-machine-learning-fundamentals', 'AI concepts · supervised learning · datasets · training · evaluation · mini project', 'Kickstart your AI & ML journey from scratch. Learn foundational machine learning concepts, data preprocessing with NumPy and Pandas, core regression and classification algorithms.', 'bi-cpu', 'coding-fullstack.gif', 'beginner', '45 Days', 2999.00, 4999.00, 4.8, 115, 430, 35, 16, 19, 1, NULL, NULL, 1, 0, 'published'),
(38, 4, NULL, 'Prompt Engineering & Generative AI', 'prompt-engineering-generative-ai', 'Prompt patterns · ChatGPT · Gemini · LLMs · RAG introduction · practical prompts', 'Master prompt engineering for top LLMs including ChatGPT, Claude and Gemini. Learn role prompting, zero-shot, few-shot, chain-of-thought, system prompts and structured JSON outputs.', 'bi-chat-left-dots-fill', 'coding-fullstack.gif', 'beginner', '20 Days', 1499.00, 2499.00, 4.8, 130, 520, 40, 14, 26, 1, NULL, NULL, 1, 1, 'published'),
(39, 1, NULL, 'Python for AI & ML', 'python-for-ai-ml', 'Python · NumPy · Pandas · Matplotlib · Scikit-learn · guided exercises', 'The definitive Python course designed specifically for aspiring AI and ML developers. Master core Python, data structures, NumPy matrix math, Pandas data manipulation and Matplotlib.', 'bi-filetype-py', 'coding-fullstack.gif', 'beginner', '30 Days', 2499.00, 3999.00, 4.8, 98, 390, 35, 17, 18, 1, NULL, NULL, 0, 1, 'published'),
(40, 4, NULL, 'AI Agents & Workflow Automation', 'ai-agents-workflow-automation', 'Agent concepts · APIs · tool calling · n8n workflows · chatbot prototype', 'Build intelligent autonomous AI agents that can browse the web, execute tasks, call APIs, and automate business operations using tool calling and workflow automation.', 'bi-robot', 'coding-fullstack.gif', 'intermediate', '30 Days', 2999.00, 4999.00, 4.8, 102, 410, 30, 12, 18, 1, NULL, NULL, 1, 0, 'published'),
(41, 4, NULL, 'ChatGPT, Gemini & AI Productivity Tools', 'chatgpt-gemini-ai-productivity-tools', 'AI research · content creation · workflows · productivity · responsible usage', 'Supercharge your daily efficiency. Learn how students and working professionals use ChatGPT, Google Gemini, Perplexity and AI productivity tools to write, research and analyze.', 'bi-lightning-charge-fill', 'coding-fullstack.gif', 'beginner', '15 Days', 999.00, 1999.00, 4.9, 160, 640, 50, 18, 32, 1, NULL, NULL, 1, 1, 'published'),
(42, 3, NULL, 'Machine Learning with Python Projects', 'machine-learning-python-projects', 'Data preparation · regression · classification · metrics · practical ML projects', '100% project-based Machine Learning training. Build, tune and evaluate real-world ML portfolio projects using Scikit-Learn, Pandas and Python, packaged as REST APIs.', 'bi-diagram-3-fill', 'coding-fullstack.gif', 'intermediate', '40 Days', 2999.00, 4999.00, 4.9, 145, 530, 30, 11, 19, 1, NULL, NULL, 1, 1, 'published')
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`), `title` = VALUES(`title`), `slug` = VALUES(`slug`),
  `subtitle` = VALUES(`subtitle`), `description` = VALUES(`description`), `level` = VALUES(`level`),
  `duration` = VALUES(`duration`), `price` = VALUES(`price`), `original_price` = VALUES(`original_price`),
  `rating` = VALUES(`rating`), `total_seats` = VALUES(`total_seats`), `seats_left` = VALUES(`seats_left`),
  `seats_filled` = VALUES(`seats_filled`), `status` = VALUES(`status`), `is_featured` = VALUES(`is_featured`),
  `is_bestseller` = VALUES(`is_bestseller`);

-- ── 5. Clean Dummy Blogs & Insert Editorial Post ───────────────────
DELETE FROM `posts` WHERE `slug` != 'what-is-the-current-it-industry-situation';
-- ── 6. Sync Live Canonical Cohort Workshops (Events - Base Columns) ─
INSERT INTO `events` (`id`, `title`, `slug`, `description`, `category`, `type`, `status`, `price`, `mode`, `total_seats`, `seats_filled`) VALUES
(10, 'Python Starter Workshop | Advanced Live Coding & Web Scraping', 'python-starter-workshop', 'Join the 3-day Python Workshop from TCM One for ₹99. Master advanced Python logic, Web Scraping with BeautifulSoup, REST APIs, and automated data extraction.', 'python', 'paid', 'upcoming', 99.00, 'online', 50, 14),
(11, '7-Day Frontend Website Challenge | TCM One', 'frontend-website-challenge-7-days', 'Join the 7-Day Frontend Website Challenge from TCM One for ₹999. Build a responsive website with HTML, CSS, JavaScript and mentor guidance.', 'frontend', 'paid', 'upcoming', 999.00, 'online', 40, 8),
(12, 'AI Tools for Everyday Work | TCM One Workshop', 'ai-tools-everyday-work-workshop', 'Learn practical AI tools for everyday study and work with TCM One. Beginner-friendly workshop covering prompting, research and productivity workflows.', 'general', 'paid', 'upcoming', 199.00, 'online', 50, 12),
(13, 'Find Your Tech Career Path | TCM One Workshop', 'find-your-tech-career-path-workshop', 'A beginner-friendly TCM One workshop to help students compare tech career paths and choose the right learning roadmap.', 'career', 'free', 'upcoming', 0.00, 'online', 100, 35)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`), `slug` = VALUES(`slug`), `description` = VALUES(`description`),
  `category` = VALUES(`category`), `type` = VALUES(`type`), `status` = VALUES(`status`),
  `price` = VALUES(`price`), `mode` = VALUES(`mode`), `total_seats` = VALUES(`total_seats`),
  `seats_filled` = VALUES(`seats_filled`);

-- ── 7. PayU Webhooks, Transactions, Refunds, Disputes & Installments ───
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `remaining_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `paid_amount`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `is_installment` TINYINT(1) NOT NULL DEFAULT 0 AFTER `remaining_amount`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `installment_number` INT UNSIGNED DEFAULT NULL AFTER `is_installment`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `total_installments` INT UNSIGNED DEFAULT NULL AFTER `installment_number`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `receipt_number` VARCHAR(60) DEFAULT NULL AFTER `payment_ref`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `verified_by_api` TINYINT(1) NOT NULL DEFAULT 0 AFTER `receipt_number`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `gateway_status` VARCHAR(50) DEFAULT NULL AFTER `status`;

CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `txnid` VARCHAR(100) NOT NULL,
  `mihpayid` VARCHAR(100) DEFAULT NULL,
  `bank_ref_num` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
  `status` ENUM('pending','success','failure','refunded','disputed') NOT NULL DEFAULT 'pending',
  `payment_mode` VARCHAR(50) DEFAULT NULL,
  `card_type` VARCHAR(50) DEFAULT NULL,
  `bank_code` VARCHAR(50) DEFAULT NULL,
  `error_code` VARCHAR(100) DEFAULT NULL,
  `error_message` VARCHAR(255) DEFAULT NULL,
  `hash_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `api_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `raw_payload` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_txnid` (`txnid`),
  KEY `idx_pt_order` (`order_id`),
  KEY `idx_pt_user` (`user_id`),
  KEY `idx_pt_mihpayid` (`mihpayid`),
  KEY `idx_pt_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `webhook_audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_type` VARCHAR(50) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `http_method` VARCHAR(10) NOT NULL DEFAULT 'POST',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `headers` TEXT DEFAULT NULL,
  `raw_payload` LONGTEXT DEFAULT NULL,
  `response_status` INT NOT NULL DEFAULT 200,
  `verification_status` ENUM('verified','hash_mismatch','api_mismatch','invalid_order','error') NOT NULL DEFAULT 'verified',
  `error_details` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wal_event` (`event_type`),
  KEY `idx_wal_status` (`verification_status`),
  KEY `idx_wal_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_refunds` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_transaction_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `payu_refund_id` VARCHAR(100) DEFAULT NULL,
  `refund_txnid` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
  `status` ENUM('pending','success','failed') NOT NULL DEFAULT 'pending',
  `reason` VARCHAR(255) DEFAULT NULL,
  `raw_response` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_order` (`order_id`),
  KEY `idx_pr_user` (`user_id`),
  KEY `idx_pr_payu_id` (`payu_refund_id`),
  KEY `idx_pr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_disputes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_transaction_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `payu_dispute_id` VARCHAR(100) DEFAULT NULL,
  `dispute_type` VARCHAR(80) DEFAULT NULL,
  `dispute_status` VARCHAR(80) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
  `reason` VARCHAR(255) DEFAULT NULL,
  `due_date` DATE DEFAULT NULL,
  `review_notes` TEXT DEFAULT NULL,
  `reviewed_by` BIGINT UNSIGNED DEFAULT NULL,
  `raw_payload` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pd_order` (`order_id`),
  KEY `idx_pd_user` (`user_id`),
  KEY `idx_pd_dispute_id` (`payu_dispute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `installment_schedules` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_order_id` BIGINT UNSIGNED DEFAULT NULL,
  `installment_order_id` BIGINT UNSIGNED DEFAULT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `course_id` BIGINT UNSIGNED NOT NULL,
  `installment_number` INT UNSIGNED NOT NULL,
  `total_installments` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `due_date` DATE NOT NULL,
  `status` ENUM('pending','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` DATETIME DEFAULT NULL,
  `payment_ref` VARCHAR(100) DEFAULT NULL,
  `txnid` VARCHAR(100) DEFAULT NULL,
  `notes` VARCHAR(255) DEFAULT NULL,
  `reminded_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_user` (`user_id`),
  KEY `idx_is_course` (`course_id`),
  KEY `idx_is_parent` (`parent_order_id`),
  KEY `idx_is_order` (`installment_order_id`),
  KEY `idx_is_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================================
-- End of Database Sync Script. Successfully Synchronized!
-- =====================================================================
