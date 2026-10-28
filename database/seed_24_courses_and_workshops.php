<?php
/**
 * TCM One: Master Course & Workshop Seeder (34 Courses + 5 Workshops)
 * Idempotent: Can be run multiple times safely without duplicates or data loss.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Core/Database.php';

use TCM\Core\Database;

date_default_timezone_set('Asia/Kolkata');

echo "=== TCM One Master Seeder: 34 Courses + 5 Workshops ===\n";

try {
    $pdo = Database::connection();

    $ensureColumn = static function (\PDO $pdo, string $table, string $column, string $definition): void {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE " . $pdo->quote($column));
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN $definition");
            echo "✓ Added missing column {$table}.{$column}\n";
        }
    };

    // Production compatibility: admin course forms can save custom brochures.
    $ensureColumn($pdo, 'courses', 'brochure_file', "brochure_file VARCHAR(255) DEFAULT NULL AFTER thumbnail");
    $ensureColumn($pdo, 'courses', 'brochure_name', "brochure_name VARCHAR(255) DEFAULT NULL AFTER brochure_file");

    // -------------------------------------------------------------
    // 1. Ensure Categories
    // -------------------------------------------------------------
    $categories = [
        ['name' => 'Programming Foundations',    'slug' => 'programming-foundations',    'desc' => 'Core programming logic, languages and fundamentals',     'icon' => 'bi-code-square',     'audience' => 'beginners', 'order' => 1],
        ['name' => 'Web & Mobile Development',   'slug' => 'web-mobile-development',     'desc' => 'Modern full stack, frontend, backend and mobile apps',    'icon' => 'bi-window-stack',    'audience' => 'college',   'order' => 2],
        ['name' => 'Data & Analytics',           'slug' => 'data-analytics',             'desc' => 'Data analysis, SQL, Power BI, Python and ML foundations', 'icon' => 'bi-graph-up',        'audience' => 'college',   'order' => 3],
        ['name' => 'AI & Automation',            'slug' => 'ai-automation',              'desc' => 'AI tools, Generative AI, AI agents and workflow automation','icon' => 'bi-robot',          'audience' => 'working',   'order' => 4],
        ['name' => 'Cloud & DevOps',             'slug' => 'cloud-devops',               'desc' => 'Cloud infrastructure, Docker, CI/CD, AWS and security',   'icon' => 'bi-cloud-check',     'audience' => 'working',   'order' => 5],
        ['name' => 'Career & DSA',               'slug' => 'career-dsa',                 'desc' => 'Data structures, algorithms, problem solving and career', 'icon' => 'bi-briefcase',       'audience' => 'college',   'order' => 6],
        ['name' => 'Design & Business',          'slug' => 'design-business',            'desc' => 'UI/UX design with Figma, digital marketing and growth',   'icon' => 'bi-palette',         'audience' => 'beginners', 'order' => 7],
    ];

    $catMap = [];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$cat['slug']]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $catMap[$cat['slug']] = (int)$existingId;
            $pdo->prepare("UPDATE categories SET name = ?, description = ?, icon = ?, sort_order = ? WHERE id = ?")
                ->execute([$cat['name'], $cat['desc'], $cat['icon'], $cat['order'], $existingId]);
        } else {
            $pdo->prepare("INSERT INTO categories (name, slug, description, icon, audience, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, 'active')")
                ->execute([$cat['name'], $cat['slug'], $cat['desc'], $cat['icon'], $cat['audience'], $cat['order']]);
            $catMap[$cat['slug']] = (int)$pdo->lastInsertId();
        }
    }
    echo "✓ 7 Standard Categories ensured.\n";

    // -------------------------------------------------------------
    // 2. 34 Master Courses Definition
    // -------------------------------------------------------------
    $courses = [
        [
            'title' => 'Full Stack Web Development (MERN)',
            'slug' => 'full-stack-web-development',
            'subtitle' => 'MongoDB, Express.js, React & Node.js with Live Production Deployments',
            'cat' => 'web-mobile-development',
            'level' => 'intermediate',
            'duration' => '16 Weeks',
            'hours' => 120,
            'price' => 12999.00,
            'original_price' => 19999.00,
            'icon' => 'bi-layers',
            'thumbnail' => 'coding-fullstack.gif',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Master end-to-end full stack web application development using the MERN stack. Build production-ready REST APIs, integrate authentications, design responsive React user interfaces, manage MongoDB databases, and deploy live full-stack projects with CI/CD.',
            'modules' => [
                ['title' => 'HTML5, Modern CSS & Tailwind Framework', 'lessons' => ['Web Fundamentals & Semantic Markup', 'Flexbox & CSS Grid Mastering', 'Tailwind CSS Component Architecture', 'Project: Responsive Landing Page']],
                ['title' => 'JavaScript Deep Dive & Asynchronous Programming', 'lessons' => ['ES6+ Syntax, Scope & Closures', 'Promises, Async/Await & Fetch API', 'DOM Manipulation & Event Loop', 'Project: Real-time Weather App']],
                ['title' => 'React.js State Management & UI Systems', 'lessons' => ['Components, Props & Lifecycle', 'React Hooks (useState, useEffect, useMemo)', 'React Router v6 Navigation', 'Context API & State Management', 'Project: E-Commerce Storefront']],
                ['title' => 'Node.js, Express & RESTful APIs', 'lessons' => ['Node.js Runtime & Architecture', 'Express Server Setup & Middleware', 'JWT Authentication & Security', 'API Rate Limiting & Validation']],
                ['title' => 'Database Engineering with MongoDB', 'lessons' => ['NoSQL Data Modeling with Mongoose', 'Aggregation Pipelines & Indexing', 'CRUD Operations & Relationships', 'Project: Collaborative Task Manager API']],
                ['title' => 'Full Stack Capstone Project & Cloud Deployment', 'lessons' => ['Connecting React with Node Backend', 'Stateful Checkout & Payment Webhooks', 'Docker Containerization & Render/Vercel Deploy', 'Final Capstone Project Presentation']]
            ]
        ],
        [
            'title' => 'Frontend Development with React',
            'slug' => 'frontend-development-with-react',
            'subtitle' => 'Modern React, Component Architecture, Hooks & Tailwind CSS',
            'cat' => 'web-mobile-development',
            'level' => 'beginner',
            'duration' => '8 Weeks',
            'hours' => 60,
            'price' => 6999.00,
            'original_price' => 9999.00,
            'icon' => 'bi-window',
            'thumbnail' => 'coding-frontend.gif',
            'is_featured' => 1,
            'is_bestseller' => 0,
            'desc' => 'Transform from beginner to proficient frontend engineer. Learn modern HTML, CSS, JavaScript ES6+, and build dynamic, component-driven single page applications with React, React Router, and Tailwind CSS.',
            'modules' => [
                ['title' => 'Web Foundations & Responsive Layouts', 'lessons' => ['Semantic HTML5 & Modern CSS3', 'Mobile-First Layouts with Flexbox & Grid', 'Responsive Breakpoints & Media Queries']],
                ['title' => 'JavaScript for Modern Frontend', 'lessons' => ['Core JS, Array Methods & Destructuring', 'DOM Manipulation & Event Listeners', 'Async JavaScript & REST API Integration']],
                ['title' => 'React Core Essentials', 'lessons' => ['JSX & Component Hierarchy', 'State & Props Handling', 'React Hooks: useState, useEffect, useRef']],
                ['title' => 'Production React Project', 'lessons' => ['Building Reusable Design Systems', 'Performance Tuning & Lazy Loading', 'Deploying to Vercel/Netlify']]
            ]
        ],
        [
            'title' => 'Full Stack Project Lab: React & Node.js',
            'slug' => 'full-stack-project-lab-react-node',
            'subtitle' => 'Build a portfolio-ready React app, Node.js REST API and deployed full-stack project',
            'cat' => 'web-mobile-development',
            'level' => 'beginner',
            'duration' => '4 Weeks',
            'hours' => 24,
            'price' => 6999.00,
            'original_price' => 6999.00,
            'icon' => 'bi-layers',
            'thumbnail' => null,
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Build one portfolio-ready full stack web project in a focused online cohort. Connect a responsive React frontend to a Node.js and Express REST API, validate data, handle common errors, test key flows and deploy a working demo. This short project lab complements the longer Full Stack curriculum.',
            'modules' => [
                ['title' => 'Plan the App & Build the React Interface', 'lessons' => ['Choose a Small Project Scope and Map User Flows', 'Build Reusable React Screens, Forms and UI States', 'Create a Responsive Layout for Mobile and Desktop']],
                ['title' => 'Create the Node.js REST API', 'lessons' => ['Set Up Express Routes and Request Validation', 'Implement CRUD Operations and Clear Error Responses', 'Connect the API to Persistent Project Data']],
                ['title' => 'Integrate, Test & Deploy the Project', 'lessons' => ['Connect React Forms and Views to the REST API', 'Check Main User Flows and Fix Integration Bugs', 'Deploy the Demo and Present the Project Decisions']]
            ]
        ],
        [
            'title' => 'Frontend Portfolio Builder: HTML, CSS & JavaScript',
            'slug' => 'frontend-portfolio-builder-html-css-js',
            'subtitle' => 'Create a responsive web portfolio with semantic HTML, modern CSS and JavaScript',
            'cat' => 'web-mobile-development',
            'level' => 'beginner',
            'duration' => '4 Weeks',
            'hours' => 24,
            'price' => 5999.00,
            'original_price' => 5999.00,
            'icon' => 'bi-window',
            'thumbnail' => null,
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Practise frontend development by designing and publishing a personal portfolio site. Use semantic HTML, accessible page structure, CSS Flexbox and Grid, responsive breakpoints and small JavaScript interactions to create a polished project you can share. This project-focused course complements the longer React programme.',
            'modules' => [
                ['title' => 'Portfolio Structure & Semantic HTML', 'lessons' => ['Plan Sections, Content and Navigation', 'Build Accessible HTML for Projects and Contact Details', 'Add Responsive Navigation and Useful Page Links']],
                ['title' => 'Modern CSS & Responsive Design', 'lessons' => ['Style with Typography, Spacing and Colour Tokens', 'Build Layouts with Flexbox and CSS Grid', 'Adapt the Portfolio for Mobile, Tablet and Desktop']],
                ['title' => 'JavaScript, Quality & Publishing', 'lessons' => ['Add Small Interactions and Form Feedback', 'Check Keyboard Access, Contrast and Browser Layouts', 'Publish the Site and Review the Finished Portfolio']]
            ]
        ],
        [
            'title' => 'Data Analysis Dashboard Sprint: Excel & Power BI',
            'slug' => 'data-analysis-dashboard-sprint-excel-powerbi',
            'subtitle' => 'Practice data analysis with Excel, Power Query and Power BI by building a business dashboard',
            'cat' => 'data-analytics',
            'level' => 'beginner',
            'duration' => '4 Weeks',
            'hours' => 24,
            'price' => 5999.00,
            'original_price' => 5999.00,
            'icon' => 'bi-bar-chart-line',
            'thumbnail' => null,
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'This beginner data analysis course uses Excel and Power BI to complete one guided dashboard project. Clean and validate a small dataset, summarize it with formulas and PivotTables, shape data with Power Query, define useful business KPIs and present results in an interactive report. It is a focused dashboard sprint, narrower than the full Data Analytics programme.',
            'modules' => [
                ['title' => 'Understand & Clean the Dataset in Excel', 'lessons' => ['Read a Business Dataset and Identify Data Quality Issues', 'Clean Values, Dates and Categories with Excel Tools', 'Summarize Records with Formulas and PivotTables']],
                ['title' => 'Prepare Data & Define Useful KPIs', 'lessons' => ['Transform Tables with Power Query', 'Choose Measures and KPIs That Match the Questions', 'Organize Data for a Clear Report']],
                ['title' => 'Build & Present the Power BI Dashboard', 'lessons' => ['Create Visuals, Filters and an Interactive Report Page', 'Check Labels, Accessibility and Chart Readability', 'Present the Dashboard and Explain the Findings']]
            ]
        ],
        [
            'title' => 'Python Programming & Automation',
            'slug' => 'python-programming-automation',
            'subtitle' => 'From Syntax Foundations to Workflow Automation & Scripting',
            'cat' => 'programming-foundations',
            'level' => 'beginner',
            'duration' => '8 Weeks',
            'hours' => 60,
            'price' => 5999.00,
            'original_price' => 8999.00,
            'icon' => 'bi-filetype-py',
            'thumbnail' => 'coding-python.gif',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Learn Python programming from scratch. Build logical problem-solving foundations and write automated scripts for web scraping, file manipulation, spreadsheets, and workflow automation.',
            'modules' => [
                ['title' => 'Python Basics & Logic Building', 'lessons' => ['Data Types, Variables & Conditionals', 'Loops, Functions & Scope', 'Data Structures: Lists, Dictionaries, Sets']],
                ['title' => 'Object Oriented Python', 'lessons' => ['Classes, Objects & Methods', 'Inheritance & Encapsulation', 'Error Handling & Custom Exceptions']],
                ['title' => 'Scripting & Automation', 'lessons' => ['File I/O & OS Scripting', 'Excel & CSV Automation with Pandas', 'Web Scraping with BeautifulSoup & Requests']],
                ['title' => 'Automated Project Build', 'lessons' => ['Building an Automated Email/Notification Bot', 'API Interaction & JSON Parsing', 'Final Project Review']]
            ]
        ],
        [
            'title' => 'SQL & Database Fundamentals',
            'slug' => 'sql-database-fundamentals',
            'subtitle' => 'Relational Schema Design, Complex Queries, Joins & Normalization',
            'cat' => 'data-analytics',
            'level' => 'beginner',
            'duration' => '4 Weeks',
            'hours' => 30,
            'price' => 3499.00,
            'original_price' => 4999.00,
            'icon' => 'bi-database',
            'thumbnail' => 'coding-database.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Master relational databases from ground up. Learn SQL querying, complex joins, subqueries, indexing, schema normalization, and database optimization using MySQL and PostgreSQL.',
            'modules' => [
                ['title' => 'Database Fundamentals & DDL', 'lessons' => ['Relational DB Architecture', 'Tables, Primary Keys & Constraints', 'Data Types & Schema Creation']],
                ['title' => 'SQL Querying Mastery', 'lessons' => ['SELECT, Filtering & Sorting', 'Aggregate Functions & GROUP BY', 'INNER, LEFT, RIGHT & FULL Joins']],
                ['title' => 'Advanced Queries & Optimization', 'lessons' => ['Subqueries & Common Table Expressions (CTEs)', 'Window Functions (RANK, ROW_NUMBER)', 'Indexes & Query Execution Plans']]
            ]
        ],
        [
            'title' => 'DSA & Problem Solving with Java',
            'slug' => 'dsa-problem-solving-java',
            'subtitle' => 'Core Data Structures, Algorithms & Technical Interview Prep',
            'cat' => 'career-dsa',
            'level' => 'intermediate',
            'duration' => '12 Weeks',
            'hours' => 90,
            'price' => 7999.00,
            'original_price' => 11999.00,
            'icon' => 'bi-cpu',
            'thumbnail' => 'coding-dsa.gif',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Prepare for top tech coding rounds. Master arrays, strings, linked lists, stacks, queues, trees, graphs, dynamic programming, and algorithm optimization with Java.',
            'modules' => [
                ['title' => 'Complexity Analysis & Arrays', 'lessons' => ['Time & Space Complexity (Big-O)', 'Two Pointers & Sliding Window Techniques', 'Binary Search & Sorting Algorithms']],
                ['title' => 'Linear Data Structures', 'lessons' => ['Singly & Doubly Linked Lists', 'Stacks, Queues & Monotonic Stacks', 'Hashing & Hash Tables']],
                ['title' => 'Trees & Graphs', 'lessons' => ['Binary Trees & Binary Search Trees', 'Tree Traversals (DFS, BFS)', 'Graph Representations & Dijkstra Algorithm']],
                ['title' => 'Advanced Problem Solving', 'lessons' => ['Recursion & Backtracking', 'Dynamic Programming Fundamentals', 'Mock Coding Interview Practice']]
            ]
        ],
        [
            'title' => 'AWS Cloud & Deployment Fundamentals',
            'slug' => 'aws-cloud-deployment-fundamentals',
            'subtitle' => 'EC2, S3, VPC, IAM, Serverless & Production Architecture',
            'cat' => 'cloud-devops',
            'level' => 'beginner',
            'duration' => '6 Weeks',
            'hours' => 45,
            'price' => 5999.00,
            'original_price' => 8999.00,
            'icon' => 'bi-cloud',
            'thumbnail' => 'coding-cloud.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Understand cloud computing architecture on Amazon Web Services. Deploy live web apps using EC2, manage scalable storage with S3, configure secure VPCs, and master IAM policies.',
            'modules' => [
                ['title' => 'Cloud Concepts & AWS Core', 'lessons' => ['Cloud Computing Models & Regions', 'Identity & Access Management (IAM)', 'EC2 Instances & Security Groups']],
                ['title' => 'Storage & Networking', 'lessons' => ['S3 Buckets & CloudFront CDN', 'Virtual Private Cloud (VPC) & Subnets', 'Elastic Load Balancing (ELB)']],
                ['title' => 'Serverless & Deployment', 'lessons' => ['AWS Lambda & API Gateway', 'RDS Databases & Backups', 'Live Web App Deployment Project']]
            ]
        ],
        [
            'title' => 'Digital Skills & Computer Fundamentals',
            'slug' => 'digital-skills-computer-fundamentals',
            'subtitle' => 'Essential Computer Proficiency, Web Literacy & Productive Tools',
            'cat' => 'programming-foundations',
            'level' => 'beginner',
            'duration' => '4 Weeks',
            'hours' => 30,
            'price' => 1999.00,
            'original_price' => 3499.00,
            'icon' => 'bi-laptop',
            'thumbnail' => 'coding-basics.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Build rock-solid digital confidence. Learn operating system essentials, command line basics, internet architecture, cloud storage, Google Workspace, and safety best practices.',
            'modules' => [
                ['title' => 'Computer Architecture & OS', 'lessons' => ['Hardware, OS & File Systems', 'Keyboard Shortcuts & Productivity', 'Command Prompt / Terminal Basics']],
                ['title' => 'Web & Cloud Essentials', 'lessons' => ['How the Internet Works & DNS', 'Cloud Drives & Collaboration Tools', 'Cyber Safety & Password Security']]
            ]
        ],
        [
            'title' => 'Build Your First Website',
            'slug' => 'build-your-first-website',
            'subtitle' => 'Step-by-Step Hands-On Guide to HTML, CSS & Publishing Live',
            'cat' => 'web-mobile-development',
            'level' => 'beginner',
            'duration' => '4 Weeks',
            'hours' => 30,
            'price' => 2499.00,
            'original_price' => 3999.00,
            'icon' => 'bi-globe',
            'thumbnail' => 'coding-webdev.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Create and publish your personal portfolio and project website from zero. Learn HTML structure, CSS styling, responsive layout design, and host your site online with a live link.',
            'modules' => [
                ['title' => 'HTML & Semantic Structure', 'lessons' => ['Tags, Attributes, Headings & Images', 'Forms, Inputs & Links', 'Structuring a Clean Portfolio Page']],
                ['title' => 'CSS Styling & Design', 'lessons' => ['Colors, Typography & Box Model', 'Flexbox Alignment & Layouts', 'Free Web Hosting & Going Live']]
            ]
        ],
        [
            'title' => 'AI Tools for Study & Work',
            'slug' => 'ai-tools-for-study-work',
            'subtitle' => 'Prompt Engineering, ChatGPT, Claude, Research & Daily Productivity',
            'cat' => 'ai-automation',
            'level' => 'beginner',
            'duration' => '3 Weeks',
            'hours' => 24,
            'price' => 1999.00,
            'original_price' => 3499.00,
            'icon' => 'bi-stars',
            'thumbnail' => 'coding-ai.gif',
            'is_featured' => 1,
            'is_bestseller' => 0,
            'desc' => 'Harness generative AI to 10x your learning, research, and work productivity. Learn effective prompt engineering, document analysis, presentation creation, and automation with modern AI tools.',
            'modules' => [
                ['title' => 'Prompt Engineering Foundations', 'lessons' => ['LLM Mechanics & Effective Prompt Frameworks', 'Role-Based Prompting & Few-Shot Learning', 'Iterative Prompt Refinement']],
                ['title' => 'AI in Daily Work & Research', 'lessons' => ['Synthesizing Research Papers & Books', 'Creating Presentations & Reports with AI', 'Coding Assistance & Debugging Workflows']]
            ]
        ],
        [
            'title' => 'Tech Career Launchpad',
            'slug' => 'tech-career-launchpad',
            'subtitle' => 'ATS Resume Building, LinkedIn Optimization & Interview Prep',
            'cat' => 'career-dsa',
            'level' => 'beginner',
            'duration' => '4 Weeks',
            'hours' => 30,
            'price' => 5999.00,
            'original_price' => 8999.00,
            'icon' => 'bi-person-badge',
            'thumbnail' => 'coding-career.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Accelerate your transition into the software industry. Build a compelling ATS-proof resume, establish a magnetic LinkedIn presence, build portfolio repositories, and master behavioral and technical interviews.',
            'modules' => [
                ['title' => 'Resume & Profile Engineering', 'lessons' => ['ATS Resume Optimization & Action Verbs', 'LinkedIn Profile Crafting & Networking Strategy', 'GitHub Profile & Project Showcase']],
                ['title' => 'Interview Mastery & Negotiation', 'lessons' => ['Behavioral STAR Method Answers', 'Technical Screening Walkthroughs', 'Salary Negotiation & Offer Assessment']]
            ]
        ],
        [
            'title' => 'AI Automation with No-Code Tools',
            'slug' => 'ai-automation-nocode-tools',
            'subtitle' => 'Automate Workflows using Zapier, Make.com & OpenAI APIs',
            'cat' => 'ai-automation',
            'level' => 'intermediate',
            'duration' => '6 Weeks',
            'hours' => 45,
            'price' => 6999.00,
            'original_price' => 9999.00,
            'icon' => 'bi-lightning-charge',
            'thumbnail' => 'coding-automation.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Automate manual business tasks without writing complex code. Connect CRM, emails, spreadsheets, and databases using Make.com, Zapier, Webhooks, and OpenAI API integrations.',
            'modules' => [
                ['title' => 'No-Code Automation Foundations', 'lessons' => ['APIs, Webhooks & Triggers Explained', 'Make.com & Zapier Flow Design', 'Data Routing, Filtering & Transforms']],
                ['title' => 'AI Integrations & End-to-End Bots', 'lessons' => ['OpenAI API Automation for Emails & Reports', 'Building an Automated Social Media Agent', 'CRM & Payment Notification Pipelines']]
            ]
        ],
        [
            'title' => 'Data Analytics with Excel, SQL & Power BI',
            'slug' => 'data-analytics-excel-sql-powerbi',
            'subtitle' => 'Turn Raw Business Data into Visual Dashboards & Actionable Insights',
            'cat' => 'data-analytics',
            'level' => 'intermediate',
            'duration' => '10 Weeks',
            'hours' => 75,
            'price' => 9999.00,
            'original_price' => 14999.00,
            'icon' => 'bi-bar-chart-line',
            'thumbnail' => 'coding-analytics.gif',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Become a business data analyst. Clean and analyze business data using Advanced Excel, write complex SQL queries, and build executive dashboards using Power BI and DAX.',
            'modules' => [
                ['title' => 'Advanced Microsoft Excel for Analysts', 'lessons' => ['XLOOKUP, INDEX/MATCH & Data Cleaning', 'Pivot Tables, Slicers & Dynamic Charts', 'Power Query Data Transformation']],
                ['title' => 'SQL for Business Analysis', 'lessons' => ['Relational Data Extraction & Filtering', 'Multi-table Joins & Grouped Metrics', 'Window Functions for Cohort Analysis']],
                ['title' => 'Power BI & Visual Dashboards', 'lessons' => ['Data Modeling (Star Schema)', 'DAX Measures & Time Intelligence', 'Publishing Interactive Business Dashboards']]
            ]
        ],
        [
            'title' => 'React & Next.js Frontend Engineering',
            'slug' => 'react-nextjs-frontend-engineering',
            'subtitle' => 'Server Components, SSR, App Router & Modern Web Performance',
            'cat' => 'web-mobile-development',
            'level' => 'intermediate',
            'duration' => '8 Weeks',
            'hours' => 60,
            'price' => 7999.00,
            'original_price' => 11999.00,
            'icon' => 'bi-boxes',
            'thumbnail' => 'coding-monitor.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Take your frontend skills to enterprise level. Build high-performance applications with React 19, Next.js App Router, Server Components, SEO optimization, and Tailwind CSS.',
            'modules' => [
                ['title' => 'Next.js App Router & Architecture', 'lessons' => ['Server vs Client Components', 'File-based Routing & Layouts', 'Server Actions & Form Handling']],
                ['title' => 'Data Fetching & Performance', 'lessons' => ['Static vs Dynamic Rendering (ISR, SSR)', 'Optimistic UI & Suspense', 'SEO, OpenGraph & Core Web Vitals']],
                ['title' => 'Production SaaS Frontend', 'lessons' => ['Authentication Integration with NextAuth', 'Payment Integration & State Management', 'Production Deployment on Vercel']]
            ]
        ],
        [
            'title' => 'Backend Development with Node.js & APIs',
            'slug' => 'backend-development-nodejs-apis',
            'subtitle' => 'REST APIs, Microservices Architecture, Auth, Redis & Postgres',
            'cat' => 'web-mobile-development',
            'level' => 'intermediate',
            'duration' => '8 Weeks',
            'hours' => 60,
            'price' => 7999.00,
            'original_price' => 11999.00,
            'icon' => 'bi-server',
            'thumbnail' => 'coding-terminal.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Architect robust, high-scale backend services. Learn Node.js internals, Express & NestJS patterns, PostgreSQL relational storage, Redis caching, and JWT authentication.',
            'modules' => [
                ['title' => 'Node.js Runtime & RESTful Standards', 'lessons' => ['Event Loop, Streams & Buffer Internals', 'Express Clean Architecture & Error Handlers', 'Validation, Logging & Environment Config']],
                ['title' => 'Data Persistence & Caching', 'lessons' => ['PostgreSQL Schema & Prisma ORM', 'Redis In-Memory Caching & Session Storage', 'Database Transactions & Concurrency']],
                ['title' => 'Production API Hardening', 'lessons' => ['Rate Limiting, Helmet & Security Headers', 'Unit & Integration Testing with Jest', 'Dockerizing & Hosting on AWS EC2']]
            ]
        ],
        [
            'title' => 'Mobile App Development with React Native',
            'slug' => 'mobile-app-development-react-native',
            'subtitle' => 'Cross-Platform iOS & Android Apps with Expo, Navigation & Native APIs',
            'cat' => 'web-mobile-development',
            'level' => 'intermediate',
            'duration' => '10 Weeks',
            'hours' => 75,
            'price' => 9999.00,
            'original_price' => 14999.00,
            'icon' => 'bi-phone',
            'thumbnail' => 'coding-webdev.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Build fluid native mobile applications for iOS and Android from a single codebase using React Native and Expo. Handle navigation, gestures, camera, push notifications, and app publishing.',
            'modules' => [
                ['title' => 'React Native Core & Layout', 'lessons' => ['Expo Tooling & Project Setup', 'Native Components & Flexbox Layouts', 'Lists, ScrollViews & FlatList Performance']],
                ['title' => 'Navigation & Device Hardware', 'lessons' => ['React Navigation (Stack & Tabs)', 'Camera, Geolocation & Local Storage', 'Push Notifications Integration']],
                ['title' => 'State, Backend & Publishing', 'lessons' => ['Global State Management (Zustand)', 'Offline Synchronization & Cache', 'Building APKs & Play Store Preparation']]
            ]
        ],
        [
            'title' => 'Generative AI & AI Agent Engineering',
            'slug' => 'generative-ai-agent-engineering',
            'subtitle' => 'LangChain, LlamaIndex, Vector Databases, RAG & Autonomous Agents',
            'cat' => 'ai-automation',
            'level' => 'advanced',
            'duration' => '12 Weeks',
            'hours' => 90,
            'price' => 12999.00,
            'original_price' => 19999.00,
            'icon' => 'bi-cpu-fill',
            'thumbnail' => 'coding-ai.gif',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Engineer production-grade Generative AI systems. Build Retrieval-Augmented Generation (RAG) pipelines with Vector DBs (Chroma/Pinecone), orchestrate multi-step AI agents with LangGraph, and deploy autonomous workflows.',
            'modules' => [
                ['title' => 'LLM Engineering Foundations', 'lessons' => ['Embeddings, Tokenization & API Architectures', 'Chunking Strategies & Context Windows', 'OpenAI & Anthropic Function Calling']],
                ['title' => 'Production RAG Architectures', 'lessons' => ['Vector Databases & Similarity Search', 'HyDE & Re-Ranking Retrievers', 'Evaluating RAG with Ragas Framework']],
                ['title' => 'Autonomous AI Agents', 'lessons' => ['Tool Calling & Planning Loops', 'LangGraph Multi-Agent Workflows', 'Deploying Agent APIs to Cloud']]
            ]
        ],
        [
            'title' => 'Docker, DevOps & CI/CD Essentials',
            'slug' => 'docker-devops-cicd-essentials',
            'subtitle' => 'Containers, GitHub Actions, Linux Servers & Cloud Pipelines',
            'cat' => 'cloud-devops',
            'level' => 'intermediate',
            'duration' => '8 Weeks',
            'hours' => 60,
            'price' => 8999.00,
            'original_price' => 12999.00,
            'icon' => 'bi-terminal',
            'thumbnail' => 'coding-cloud-new.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Bridge the gap between code and production. Master Linux server management, Docker containerization, multi-stage builds, Docker Compose, automated CI/CD pipelines with GitHub Actions, and NGINX reverse proxies.',
            'modules' => [
                ['title' => 'Linux Administration & Shell Scripting', 'lessons' => ['Linux File System, Permissions & SSH', 'Bash Scripting for Automation', 'Process & Package Management']],
                ['title' => 'Docker & Containerization', 'lessons' => ['Dockerfile Optimization & Multi-Stage Builds', 'Docker Networking & Persistent Volumes', 'Docker Compose Multi-Container Stacks']],
                ['title' => 'CI/CD & Automated Pipelines', 'lessons' => ['GitHub Actions Workflows & Secrets', 'Automated Testing & Build Verification', 'Zero-Downtime Deployment with NGINX']]
            ]
        ],
        [
            'title' => 'UI/UX Design with Figma',
            'slug' => 'ui-ux-design-with-figma',
            'subtitle' => 'Wireframing, Design Systems, Auto-Layout & Interactive Prototyping',
            'cat' => 'design-business',
            'level' => 'beginner',
            'duration' => '6 Weeks',
            'hours' => 45,
            'price' => 5999.00,
            'original_price' => 8999.00,
            'icon' => 'bi-palette',
            'thumbnail' => 'coding.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Learn professional digital product design. Master user research, wireframing, color theory, typography, Figma Auto-Layout, interactive component variants, design systems, and developer handoff.',
            'modules' => [
                ['title' => 'UX Fundamentals & Research', 'lessons' => ['User Personas, Journey Maps & Information Architecture', 'Low-Fidelity Wireframing & Usability Testing']],
                ['title' => 'Figma Mastery & UI Design', 'lessons' => ['Auto-Layout & Responsive Components', 'Design Systems (Tokens, Colors, Typography)', 'Interactive Micro-interactions & Prototypes']]
            ]
        ],
        [
            'title' => 'Software Testing & QA with Playwright',
            'slug' => 'software-testing-qa-playwright',
            'subtitle' => 'Automated End-to-End Testing, API Testing & Test Automation Frameworks',
            'cat' => 'web-mobile-development',
            'level' => 'intermediate',
            'duration' => '6 Weeks',
            'hours' => 45,
            'price' => 5999.00,
            'original_price' => 8999.00,
            'icon' => 'bi-check-all',
            'thumbnail' => 'coding-terminal.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Become a modern QA automation engineer. Master manual testing principles, automated end-to-end testing with Playwright, cross-browser validation, API test suites, and CI/CD test reporting.',
            'modules' => [
                ['title' => 'Testing Foundations & Strategy', 'lessons' => ['Test Cases, Bug Life Cycle & Test Plans', 'Unit vs Integration vs E2E Testing']],
                ['title' => 'Playwright Automation Suite', 'lessons' => ['Locators, Assertions & Page Object Model (POM)', 'API Testing & Mocking Responses', 'Running Headless Tests in CI/CD']]
            ]
        ],
        [
            'title' => 'Digital Marketing, SEO & Analytics',
            'slug' => 'digital-marketing-seo-analytics',
            'subtitle' => 'Search Engine Optimization, Google Analytics 4, Meta Ads & Funnel Growth',
            'cat' => 'design-business',
            'level' => 'beginner',
            'duration' => '6 Weeks',
            'hours' => 45,
            'price' => 5999.00,
            'original_price' => 8999.00,
            'icon' => 'bi-megaphone',
            'thumbnail' => 'coding-analytics.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Drive qualified organic and paid traffic. Master technical and on-page SEO, Google Search Console, Google Analytics 4 (GA4), conversion rate optimization, and high-converting Meta advertising campaigns.',
            'modules' => [
                ['title' => 'Search Engine Optimization (SEO)', 'lessons' => ['Keyword Research & Search Intent', 'On-Page Optimization & Technical SEO Audits', 'Link Building & Content Strategy']],
                ['title' => 'Performance Marketing & Analytics', 'lessons' => ['GA4 Event Tracking & Funnels', 'Meta Ads Campaign Architecture & Targeting', 'Conversion Rate Optimization (CRO)']]
            ]
        ],
        [
            'title' => 'Core Java & Object-Oriented Programming',
            'slug' => 'core-java-object-oriented-programming',
            'subtitle' => 'OOP Principles, Collections Framework, Multithreading & Clean Code',
            'cat' => 'programming-foundations',
            'level' => 'beginner',
            'duration' => '6 Weeks',
            'hours' => 45,
            'price' => 4999.00,
            'original_price' => 7499.00,
            'icon' => 'bi-filetype-java',
            'thumbnail' => 'coding-basics.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Establish a solid foundation in enterprise software engineering with Java. Learn OOP fundamentals, inheritance, interfaces, exception handling, the Collections Framework, and file handling.',
            'modules' => [
                ['title' => 'Java Foundations & Control Flow', 'lessons' => ['JVM Architecture & Syntax Basics', 'Conditionals, Loops & Methods', 'Arrays & String Manipulation']],
                ['title' => 'Object-Oriented Design & Collections', 'lessons' => ['Encapsulation, Polymorphism & Abstraction', 'Java Collections (List, Set, Map)', 'File I/O & Exception Handling']]
            ]
        ],
        [
            'title' => 'Data Science & Machine Learning Foundations',
            'slug' => 'data-science-machine-learning-foundations',
            'subtitle' => 'NumPy, Pandas, Scikit-Learn, Regression, Classification & Model Tuning',
            'cat' => 'data-analytics',
            'level' => 'intermediate',
            'duration' => '14 Weeks',
            'hours' => 100,
            'price' => 12999.00,
            'original_price' => 18999.00,
            'icon' => 'bi-diagram-3',
            'thumbnail' => 'coding-algorithm.gif',
            'is_featured' => 1,
            'is_bestseller' => 0,
            'desc' => 'Launch your data science journey. Clean and explore data with Pandas, build mathematical intuition, train supervised and unsupervised models with Scikit-Learn, and evaluate model performance.',
            'modules' => [
                ['title' => 'Data Science Math & Python Stack', 'lessons' => ['Linear Algebra & Statistics Essentials', 'NumPy Numerical Arrays & Vectorization', 'Pandas Data Wrangling & Exploratory Data Analysis']],
                ['title' => 'Supervised Machine Learning', 'lessons' => ['Linear & Logistic Regression', 'Decision Trees & Random Forests', 'Feature Engineering & Cross Validation']],
                ['title' => 'Unsupervised ML & Model Deployment', 'lessons' => ['K-Means Clustering & PCA', 'Deploying ML Models with Streamlit & FastAPI', 'Capstone Predictive Modeling Project']]
            ]
        ],
        [
            'title' => 'System Design & Microservices Foundations',
            'slug' => 'system-design-microservices-foundations',
            'subtitle' => 'Scalability, Load Balancing, Caching, Message Queues & Distributed Systems',
            'cat' => 'career-dsa',
            'level' => 'advanced',
            'duration' => '8 Weeks',
            'hours' => 60,
            'price' => 9999.00,
            'original_price' => 14999.00,
            'icon' => 'bi-diagram-2',
            'thumbnail' => 'coding-cloud.gif',
            'is_featured' => 0,
            'is_bestseller' => 1,
            'desc' => 'Learn how large-scale tech companies handle millions of concurrent users. Master high-level and low-level system design, horizontal scaling, database sharding, caching strategies, and event-driven microservices.',
            'modules' => [
                ['title' => 'Scalability Fundamentals', 'lessons' => ['Vertical vs Horizontal Scaling', 'Load Balancing & Consistent Hashing', 'Caching Strategies (Write-Through, Cache-Aside)']],
                ['title' => 'Data Architecture & Storage', 'lessons' => ['Database Replication & Sharding', 'CAP Theorem & Eventual Consistency', 'Message Queues (Kafka, RabbitMQ)']],
                ['title' => 'Real-World System Case Studies', 'lessons' => ['Designing a URL Shortener (TinyURL)', 'Designing a Rate Limiter', 'Designing a Live Chat System (WhatsApp)']]
            ]
        ],
        [
            'title' => 'Cybersecurity & Ethical Security Foundations',
            'slug' => 'cybersecurity-ethical-security-foundations',
            'subtitle' => 'Network Security, Web Application Penetration Testing & OWASP Top 10',
            'cat' => 'cloud-devops',
            'level' => 'intermediate',
            'duration' => '8 Weeks',
            'hours' => 60,
            'price' => 7999.00,
            'original_price' => 11999.00,
            'icon' => 'bi-shield-check',
            'thumbnail' => 'coding-terminal.gif',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'desc' => 'Understand the offensive and defensive principles of cybersecurity. Learn computer networking fundamentals, reconnaissance, vulnerability assessment, OWASP Top 10 web exploits, and defensive security controls.',
            'modules' => [
                ['title' => 'Networking & Security Foundations', 'lessons' => ['TCP/IP, OSI Model & Port Scanning (Nmap)', 'Wireshark Packet Analysis', 'Authentication & Cryptography Basics']],
                ['title' => 'Web App Security & OWASP Top 10', 'lessons' => ['SQL Injection & Broken Access Control', 'Cross-Site Scripting (XSS) & CSRF', 'Security Hardening & Remediation']]
            ]
        ],
        [
            'title' => 'Artificial Intelligence & Machine Learning (AI & ML) Masterclass',
            'slug' => 'artificial-intelligence-machine-learning',
            'subtitle' => 'From Math & Python Foundations to Deep Learning, Computer Vision, NLP & Generative AI Production Deployment',
            'cat' => 'ai-automation',
            'level' => 'intermediate',
            'duration' => '16 Weeks',
            'hours' => 120,
            'price' => 14999.00,
            'original_price' => 22999.00,
            'icon' => 'bi-cpu-fill',
            'thumbnail' => 'ai-machine-learning.svg',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Master end-to-end Artificial Intelligence and Machine Learning engineering. Build strong foundations in Python and mathematics, train supervised and unsupervised ML algorithms, architect deep neural networks with PyTorch, build Computer Vision & NLP applications with Transformers, implement Retrieval-Augmented Generation (RAG) agents, and deploy production ML systems with FastAPI and Docker.',
            'modules' => [
                [
                    'title' => 'Python for AI & Mathematical Foundations',
                    'lessons' => [
                        'Python for High-Performance Computing: NumPy, Vectorization & Matrix Math',
                        'Data Wrangling & Feature Exploration: Pandas, Seaborn & Matplotlib',
                        'Essential Mathematics for Machine Learning: Linear Algebra & Vector Calculus',
                        'Probability Distributions, Hypothesis Testing & Inferential Statistics',
                        'Project: Exploratory Data Analysis (EDA) on Real-World Financial & Health Datasets'
                    ]
                ],
                [
                    'title' => 'Supervised & Classical Machine Learning',
                    'lessons' => [
                        'Linear Regression, Gradient Descent Optimization & Regularization (Lasso/Ridge)',
                        'Classification Algorithms: Logistic Regression, Decision Trees & SVMs',
                        'Ensemble Learning: Random Forests, AdaBoost & Extreme Gradient Boosting (XGBoost)',
                        'Distance-Based & Probabilistic Models: K-Nearest Neighbors & Naive Bayes',
                        'Project: Customer Churn & Loan Default Prediction Engine'
                    ]
                ],
                [
                    'title' => 'Unsupervised Learning & Feature Engineering',
                    'lessons' => [
                        'Clustering Architectures: K-Means, Hierarchical & DBSCAN Algorithms',
                        'Dimensionality Reduction: Principal Component Analysis (PCA) & t-SNE',
                        'Feature Engineering, Handling Imbalanced Data (SMOTE) & Outlier Detection',
                        'Project: E-Commerce Customer Segmentation & Market Basket Analysis'
                    ]
                ],
                [
                    'title' => 'Deep Learning & Neural Networks with PyTorch',
                    'lessons' => [
                        'Neural Network Foundations: Perceptrons, Multi-Layer Perceptrons & Backpropagation',
                        'Activation Functions, Loss Formulations & Modern Optimizers (AdamW, RMSprop)',
                        'PyTorch Deep Dive: Tensors, Autograd, Custom Datasets & Training Pipelines',
                        'Project: Deep Neural Network for High-Dimensional Disease Classification'
                    ]
                ],
                [
                    'title' => 'Computer Vision & Natural Language Processing (NLP)',
                    'lessons' => [
                        'Convolutional Neural Networks (CNNs) & Feature Map Visualizations',
                        'Transfer Learning with Vision Models: ResNet, MobileNet & Real-Time Object Detection',
                        'NLP Foundations: Tokenization, Word Embeddings (Word2Vec) & Sequence Modeling',
                        'Transformer Architectures: Self-Attention, BERT & Hugging Face Ecosystem'
                    ]
                ],
                [
                    'title' => 'Generative AI, RAG & MLOps Production Deployment',
                    'lessons' => [
                        'LLM Engineering: Prompt Design, Token Economics & Vector Databases (Chroma/Pinecone)',
                        'Production Retrieval-Augmented Generation (RAG) & LangChain Agents',
                        'Model Packaging, FastAPI Microservice Serving & Docker Containerization',
                        'Capstone Project: Production-Ready Multi-Modal AI Assistant with Live Inference API'
                    ]
                ]
            ]
        ],
        [
            'title' => 'AI & Machine Learning Fundamentals',
            'slug' => 'ai-machine-learning-fundamentals',
            'subtitle' => 'Foundations of Artificial Intelligence, Core ML Algorithms & Hands-On Python',
            'cat' => 'ai-automation',
            'level' => 'beginner',
            'duration' => '45 Days',
            'hours' => 50,
            'price' => 2999.00,
            'original_price' => 4999.00,
            'icon' => 'bi-cpu',
            'thumbnail' => 'ai-machine-learning.svg',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Kickstart your AI & ML journey from scratch. Learn foundational machine learning concepts, data preprocessing with NumPy and Pandas, core regression and classification algorithms, clustering, and hands-on Python model training.',
            'modules' => [
                ['title' => 'Introduction to AI & Data Exploration', 'lessons' => ['What is AI vs ML vs Deep Learning', 'Python & Jupyter Environment Setup', 'NumPy Arrays & Mathematical Operations', 'Pandas DataFrames & Real-world Data Cleaning']],
                ['title' => 'Supervised Learning Algorithms', 'lessons' => ['Linear Regression & Cost Functions', 'Logistic Regression & Binary Classification', 'Decision Trees & Model Overfitting', 'Model Evaluation (Precision, Recall, F1-Score)']],
                ['title' => 'Unsupervised Learning & Clustering', 'lessons' => ['K-Means Clustering Fundamentals', 'Customer Segmentation Analysis', 'Dimensionality Reduction Basics']],
                ['title' => 'Hands-on ML Projects & Deployment', 'lessons' => ['Building a House Price Predictor', 'Building a Loan Eligibility Classifier', 'Deploying ML Models with Streamlit']]
            ]
        ],
        [
            'title' => 'Prompt Engineering & Generative AI',
            'slug' => 'prompt-engineering-generative-ai',
            'subtitle' => 'Mastering Advanced Prompting, ChatGPT, Claude, Midjourney & GenAI Workflows',
            'cat' => 'ai-automation',
            'level' => 'beginner',
            'duration' => '20 Days',
            'hours' => 24,
            'price' => 1499.00,
            'original_price' => 2499.00,
            'icon' => 'bi-chat-left-dots-fill',
            'thumbnail' => 'genai-ai-agents.svg',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Master prompt engineering for top LLMs including ChatGPT, Claude, and Gemini. Learn role prompting, zero-shot, few-shot, chain-of-thought, system prompts, structured JSON outputs, image generation, and multi-modal generative AI.',
            'modules' => [
                ['title' => 'Core Prompt Engineering Techniques', 'lessons' => ['LLM Tokenization, Temperature & Top-P', 'Zero-shot, Few-shot & Role-based Prompting', 'Chain-of-Thought & Self-Consistency Prompting', 'Handling Hallucinations & Fact Verification']],
                ['title' => 'Advanced System Prompts & Structured Outputs', 'lessons' => ['Writing Robust System Prompts', 'Enforcing JSON & Markdown Output Schemas', 'Prompt Injection & Security Guardrails']],
                ['title' => 'Multi-Modal GenAI & Media Creation', 'lessons' => ['Image Generation with Midjourney & DALL-E', 'Audio, Voice & Video AI Generation Tools', 'Multi-Modal Reasoning with Gemini & Claude']],
                ['title' => 'Capstone Project: Custom GPTs & Prompt Library', 'lessons' => ['Building Custom Specialized AI Assistants', 'Creating a Reusable Personal Prompt Repository', 'Showcase Presentation & Evaluation']]
            ]
        ],
        [
            'title' => 'Python for AI & ML',
            'slug' => 'python-for-ai-ml',
            'subtitle' => 'Modern Python Programming, NumPy, Pandas, Matplotlib & Math for AI',
            'cat' => 'programming-foundations',
            'level' => 'beginner',
            'duration' => '30 Days',
            'hours' => 36,
            'price' => 2499.00,
            'original_price' => 3999.00,
            'icon' => 'bi-filetype-py',
            'thumbnail' => 'coding-python.gif',
            'is_featured' => 0,
            'is_bestseller' => 1,
            'desc' => 'The definitive Python course designed specifically for aspiring Artificial Intelligence and Machine Learning developers. Master core Python, data structures, NumPy matrix math, Pandas data manipulation, and Matplotlib visualizations.',
            'modules' => [
                ['title' => 'Python Programming Essentials', 'lessons' => ['Variables, Data Types, Control Flow & Loops', 'Lists, Dictionaries, Tuples & Sets', 'Functions, Lambdas & List Comprehensions', 'Object-Oriented Programming (OOP) in Python']],
                ['title' => 'NumPy for Scientific & Vectorized Computing', 'lessons' => ['Ndarray Creation, Indexing & Slicing', 'Vectorized Operations & Broadcasting', 'Matrix Multiplication & Linear Algebra Functions']],
                ['title' => 'Data Wrangling with Pandas', 'lessons' => ['Series & DataFrames Essentials', 'Filtering, GroupBy & Pivot Tables', 'Handling Missing Data, Imputation & Merging']],
                ['title' => 'Data Visualization & Exploratory Data Analysis (EDA)', 'lessons' => ['Matplotlib Line, Bar & Scatter Plots', 'Seaborn Heatmaps & Statistical Distributions', 'Hands-on EDA Project on a Real-world Dataset']]
            ]
        ],
        [
            'title' => 'AI Agents & Workflow Automation',
            'slug' => 'ai-agents-workflow-automation',
            'subtitle' => 'Autonomous AI Agents, LangChain, Tool Calling, Webhooks & No-Code Automations',
            'cat' => 'ai-automation',
            'level' => 'intermediate',
            'duration' => '30 Days',
            'hours' => 36,
            'price' => 2999.00,
            'original_price' => 4999.00,
            'icon' => 'bi-robot',
            'thumbnail' => 'coding-automation.gif',
            'is_featured' => 1,
            'is_bestseller' => 0,
            'desc' => 'Build intelligent autonomous AI agents that can browse the web, execute tasks, call APIs, and automate business operations using LangChain, LangGraph, Zapier, Make.com, and OpenAI Function Calling.',
            'modules' => [
                ['title' => 'Autonomous Agent Architecture', 'lessons' => ['ReAct (Reason + Act) Agent Framework', 'Memory, State Management & Scratchpads', 'OpenAI & Claude Function Calling']],
                ['title' => 'Tool Calling & External Integrations', 'lessons' => ['Connecting Agents to Google Search & Scraping', 'Database Querying Agents (SQL Agent)', 'Error Handling & Human-in-the-Loop Safeguards']],
                ['title' => 'No-Code & Low-Code Workflow Automation', 'lessons' => ['Make.com & Zapier Integration Patterns', 'Webhooks & Automated Multi-Step Triggers', 'Automated Lead Qualification & CRM Sync']],
                ['title' => 'Capstone Project: Autonomous Research & Report Agent', 'lessons' => ['Building an Automated Market Research Agent', 'Generating Formatted Reports & Sending Email Alerts', 'Deploying the AI Agent Workflow']]
            ]
        ],
        [
            'title' => 'ChatGPT, Gemini & AI Productivity Tools',
            'slug' => 'chatgpt-gemini-ai-productivity-tools',
            'subtitle' => '10x Your Productivity in Work & Study with Modern Generative AI Tools',
            'cat' => 'ai-automation',
            'level' => 'beginner',
            'duration' => '15 Days',
            'hours' => 18,
            'price' => 999.00,
            'original_price' => 1999.00,
            'icon' => 'bi-lightning-charge-fill',
            'thumbnail' => 'coding-ai.gif',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => 'Supercharge your daily efficiency. Learn how students, job seekers, and working professionals use ChatGPT, Google Gemini, Perplexity, and AI productivity tools to write, research, analyze spreadsheets, and draft presentations in minutes.',
            'modules' => [
                ['title' => 'Mastering Everyday AI Assistants', 'lessons' => ['ChatGPT vs Gemini vs Perplexity: When to Use Which', 'Prompting Frameworks for Clear, Accurate Answers', 'Synthesizing Long PDFs, Articles & Research Papers']],
                ['title' => 'Workplace & Academic Efficiency', 'lessons' => ['Drafting High-Impact Professional Emails & Reports', 'Excel & Google Sheets Formulas with AI', 'Instant Slide Decks & Presentation Generation']],
                ['title' => 'Creative Tools & Practical AI Workflows', 'lessons' => ['Audio Transcription & Meeting Summarization', 'Resume Optimization & Cover Letter Personalization', 'Building Your Personal Daily Productivity System']]
            ]
        ],
        [
            'title' => 'Machine Learning with Python Projects',
            'slug' => 'machine-learning-python-projects',
            'subtitle' => 'Build 5 Real-World Industry ML Projects from Scikit-Learn to Production APIs',
            'cat' => 'data-analytics',
            'level' => 'intermediate',
            'duration' => '40 Days',
            'hours' => 48,
            'price' => 2999.00,
            'original_price' => 4999.00,
            'icon' => 'bi-diagram-3-fill',
            'thumbnail' => 'coding-algorithm.gif',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'desc' => '100% project-based Machine Learning training. Build, tune, and evaluate 5 real-world ML portfolio projects using Scikit-Learn, Pandas, and Python, and package them as REST APIs for production deployment.',
            'modules' => [
                ['title' => 'Industrial ML Pipelines & Preprocessing', 'lessons' => ['Scikit-Learn Pipeline & ColumnTransformer', 'Feature Engineering & Cross-Validation Strategy', 'Hyperparameter Tuning with GridSearchCV & Optuna']],
                ['title' => 'Projects 1 & 2: Predictive & Risk Modeling', 'lessons' => ['Project 1: Real Estate Price Prediction with XGBoost', 'Model Evaluation with RMSE & R-Squared', 'Project 2: Credit Card Fraud & Risk Scoring System', 'Handling Imbalanced Datasets with SMOTE']],
                ['title' => 'Projects 3 & 4: Classification & Sentiment NLP', 'lessons' => ['Project 3: Medical Diagnostics Classification Model', 'Project 4: Customer Sentiment & Review Classification', 'TF-IDF Vectorization & Classification Metrics']],
                ['title' => 'Project 5 & Cloud API Deployment', 'lessons' => ['Project 5: Movie & Product Recommendation System', 'Serializing Models with Joblib / ONNX', 'Building a FastAPI Inference Endpoint & Deploying Live']]
            ]
        ]
    ];

    $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));

    foreach ($courses as $c) {
        $catId = $catMap[$c['cat']] ?? null;

        $stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = ?");
        $stmt->execute([$c['slug']]);
        $courseId = $stmt->fetchColumn();

        if ($courseId) {
            $pdo->prepare("UPDATE courses SET 
                title = ?, subtitle = ?, description = ?, category_id = ?, 
                level = ?, duration = ?, price = ?, original_price = ?, 
                icon = ?, thumbnail = ?, is_featured = ?, is_bestseller = ?, 
                status = 'published' WHERE id = ?")
                ->execute([
                    $c['title'], $c['subtitle'], $c['desc'], $catId,
                    $c['level'], $c['duration'], $c['price'], $c['original_price'],
                    $c['icon'], $c['thumbnail'], $c['is_featured'], $c['is_bestseller'],
                    $courseId
                ]);
            echo " - Updated course: {$c['title']} (ID: $courseId)\n";
        } else {
            $pdo->prepare("INSERT INTO courses (
                title, slug, subtitle, description, category_id, 
                level, duration, price, original_price, icon, 
                thumbnail, is_featured, is_bestseller, status, 
                total_seats, seats_left, rating, reviews_count, students_count
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', 30, 18, 4.8, 120, 450)")
                ->execute([
                    $c['title'], $c['slug'], $c['subtitle'], $c['desc'], $catId,
                    $c['level'], $c['duration'], $c['price'], $c['original_price'], $c['icon'],
                    $c['thumbnail'], $c['is_featured'], $c['is_bestseller']
                ]);
            $courseId = (int)$pdo->lastInsertId();
            echo " + Created course: {$c['title']} (ID: $courseId)\n";
        }

        // Modules & Lessons
        if (!empty($c['modules'])) {
            // Delete old auto-seeded modules for clean idempotency
            $pdo->prepare("DELETE FROM course_modules WHERE course_id = ?")->execute([$courseId]);

            foreach ($c['modules'] as $mIndex => $mod) {
                $pdo->prepare("INSERT INTO course_modules (course_id, title, position) VALUES (?, ?, ?)")
                    ->execute([$courseId, $mod['title'], $mIndex + 1]);
                $modId = (int)$pdo->lastInsertId();

                if (!empty($mod['lessons'])) {
                    foreach ($mod['lessons'] as $lIndex => $lesTitle) {
                        $pdo->prepare("INSERT INTO course_lessons (module_id, title, type, duration_minutes, position, is_preview) VALUES (?, ?, 'live', 60, ?, ?)")
                            ->execute([$modId, $lesTitle, $lIndex + 1, $lIndex === 0 ? 1 : 0]);
                    }
                }
            }
        }

        // Ensure active batch
        $batchCode = strtoupper(substr($c['slug'], 0, 8)) . '-B1';
        $batchStart = (clone $now)->modify('+5 days')->setTime(19, 0, 0);
        $batchRegClose = (clone $batchStart)->modify('-1 day')->setTime(23, 59, 59);

        $stmt = $pdo->prepare("SELECT id FROM course_batches WHERE batch_code = ?");
        $stmt->execute([$batchCode]);
        if (!$stmt->fetchColumn()) {
            $pdo->prepare("INSERT INTO course_batches (
                item_type, item_id, batch_code, batch_title, 
                registration_opens_at, registration_closes_at, starts_at, 
                capacity, seats_left, status, is_featured, timezone
            ) VALUES ('course', ?, ?, ?, ?, ?, ?, 30, 14, 'registration_open', 1, 'Asia/Kolkata')")
                ->execute([
                    $courseId, $batchCode, $c['title'] . ' — Batch 1',
                    $now->format('Y-m-d H:i:s'),
                    $batchRegClose->format('Y-m-d H:i:s'),
                    $batchStart->format('Y-m-d H:i:s')
                ]);
        }
    }
    echo "✓ All " . count($courses) . " Courses seeded with modules & active batches.\n";

    // -------------------------------------------------------------
    // 3. 5 Workshop Records (Featured & Live Cohorts)
    // -------------------------------------------------------------
    $workshops = [
        [
            'title' => 'Python Starter Workshop',
            'slug' => 'python-starter-workshop',
            'subtitle' => 'Advanced Python, Web Scraping & Automation',
            'price' => 99.00,
            'duration' => '3 Days',
            'level' => 'intermediate',
            'type' => 'paid',
            'status' => 'upcoming',
            'is_featured_home' => 1,
            'home_sort_order' => 1,
            'desc' => 'Master Advanced Python, Web Scraping with BeautifulSoup, REST APIs, and automated data pipelines. Build real-world data harvesting bots from scratch with live mentor feedback.'
        ],
        [
            'title' => '7-Day Frontend Website Challenge',
            'slug' => 'frontend-website-challenge-7-days',
            'subtitle' => 'Build Your First Responsive Website',
            'price' => 999.00,
            'duration' => '7 Days',
            'level' => 'beginner',
            'type' => 'paid',
            'status' => 'upcoming',
            'is_featured_home' => 1,
            'home_sort_order' => 2,
            'desc' => 'Learn HTML, CSS and JavaScript through practical live sessions. Build and publish your first responsive mini website from scratch with expert mentor guidance.'
        ],
        [
            'title' => 'Data Analysis Masterclass | Dussehra Special',
            'slug' => 'data-analysis-workshop',
            'subtitle' => 'Excel, SQL, Python & Power BI in 2 Weeks',
            'price' => 999.00,
            'duration' => '2 Weeks',
            'level' => 'beginner',
            'type' => 'paid',
            'status' => 'upcoming',
            'is_featured_home' => 1,
            'home_sort_order' => 3,
            'desc' => 'Master Excel analytics, advanced SQL multi-table queries, Python Pandas data wrangling, and interactive Power BI executive dashboards in 2 weeks of live mentored classes.'
        ],
        [
            'title' => 'AI Tools for Everyday Work',
            'slug' => 'ai-tools-everyday-work-workshop',
            'subtitle' => 'Work Faster and Smarter with Modern AI',
            'price' => 199.00,
            'duration' => '2 Days',
            'level' => 'beginner',
            'type' => 'paid',
            'status' => 'upcoming',
            'is_featured_home' => 0,
            'home_sort_order' => 4,
            'desc' => 'Discover how to apply generative AI in your daily college and office workflow. Learn effective prompt engineering and practical automated tasks.'
        ],
        [
            'title' => 'Find Your Tech Career Path',
            'slug' => 'find-your-tech-career-path-workshop',
            'subtitle' => 'Roadmap to Software Engineering & Placements',
            'price' => 0.00,
            'duration' => '1 Day',
            'level' => 'beginner',
            'type' => 'free',
            'status' => 'upcoming',
            'is_featured_home' => 0,
            'home_sort_order' => 5,
            'desc' => 'An insightful career navigation session covering web development, data, AI, cloud, and how to prepare for placement drives.'
        ]
    ];

    foreach ($workshops as $ws) {
        $stmt = $pdo->prepare("SELECT id FROM events WHERE slug = ?");
        $stmt->execute([$ws['slug']]);
        $wsId = $stmt->fetchColumn();

        $wsDate = (clone $now)->modify('+4 days')->format('Y-m-d');
        $wsTime = '19:00:00';

        if ($wsId) {
            $pdo->prepare("UPDATE events SET 
                title = ?, subtitle = ?, description = ?, price = ?, 
                duration = ?, level = ?, type = ?, status = ?, 
                is_featured_home = ?, home_sort_order = ?, event_date = ?, event_time = ? 
                WHERE id = ?")
                ->execute([
                    $ws['title'], $ws['subtitle'], $ws['desc'], $ws['price'],
                    $ws['duration'], $ws['level'], $ws['type'], $ws['status'],
                    $ws['is_featured_home'], $ws['home_sort_order'], $wsDate, $wsTime,
                    $wsId
                ]);
            echo " - Updated workshop: {$ws['title']} (ID: $wsId)\n";
        } else {
            $pdo->prepare("INSERT INTO events (
                title, slug, subtitle, description, price, 
                duration, level, type, status, is_featured_home, 
                home_sort_order, event_date, event_time, mode, total_seats, seats_filled
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'online', 50, 18)")
                ->execute([
                    $ws['title'], $ws['slug'], $ws['subtitle'], $ws['desc'], $ws['price'],
                    $ws['duration'], $ws['level'], $ws['type'], $ws['status'],
                    $ws['is_featured_home'], $ws['home_sort_order'], $wsDate, $wsTime
                ]);
            $wsId = (int)$pdo->lastInsertId();
            echo " + Created workshop: {$ws['title']} (ID: $wsId)\n";
        }

        // Workshop batch
        $batchCode = 'WS-' . strtoupper(substr($ws['slug'], 0, 8));
        $batchStart = (clone $now)->modify('+4 days')->setTime(19, 0, 0);
        $batchRegClose = (clone $batchStart)->modify('-6 hours');

        $stmt = $pdo->prepare("SELECT id FROM course_batches WHERE batch_code = ?");
        $stmt->execute([$batchCode]);
        if (!$stmt->fetchColumn()) {
            $pdo->prepare("INSERT INTO course_batches (
                item_type, item_id, batch_code, batch_title, 
                registration_opens_at, registration_closes_at, starts_at, 
                capacity, seats_left, status, is_featured, timezone
            ) VALUES ('workshop', ?, ?, ?, ?, ?, ?, 50, 24, 'registration_open', 1, 'Asia/Kolkata')")
                ->execute([
                    $wsId, $batchCode, $ws['title'] . ' — Cohort 1',
                    $now->format('Y-m-d H:i:s'),
                    $batchRegClose->format('Y-m-d H:i:s'),
                    $batchStart->format('Y-m-d H:i:s')
                ]);
        }
    }
    echo "✓ All " . count($workshops) . " Workshops seeded with live batches.\n";

    $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('courses_count', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)")
        ->execute([(string) count($courses)]);
    echo "✓ Site settings updated for " . count($courses) . "-course catalogue.\n";

    echo "\n=== Seeding Completed Successfully! ===\n";

} catch (\Throwable $e) {
    echo "ERROR during seeding: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
