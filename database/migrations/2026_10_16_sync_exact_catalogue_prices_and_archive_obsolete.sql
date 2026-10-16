-- Migration: Sync exact catalogue prices, durations, archive obsolete courses, and update admin credentials
-- Date: 2026-10-16

-- 1. Ensure admin user exists with admin@thecodemunk.in
INSERT INTO users (id, name, email, password_hash, role, status, email_verified, onboarded)
VALUES (1, 'TCM Admin', 'admin@thecodemunk.in', '$2y$10$QjdOPqj0PXTWneBe.K7MrO2G6UuPdIAhLYqTqbYE/JJaFYfxwcEJu', 'admin', 'active', 1, 1)
ON DUPLICATE KEY UPDATE
    name = 'TCM Admin',
    email = 'admin@thecodemunk.in',
    password_hash = VALUES(password_hash),
    role = 'admin',
    status = 'active',
    email_verified = 1,
    onboarded = 1;

-- 2. Archive 23 obsolete/test 0-module courses
UPDATE courses 
SET status = 'archived', is_featured = 0 
WHERE id IN (15, 16, 17, 18, 19, 20, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 44, 45);

-- 3. Update exact catalogue fees, original cut-prices, durations and featured status for 31 approved master courses
UPDATE courses SET price = 12999.00, original_price = 19999.00, duration = '16 Weeks', status = 'published', is_featured = 1 WHERE slug = 'full-stack-web-development' OR id = 13;
UPDATE courses SET price = 6999.00, original_price = 9999.00, duration = '8 Weeks', status = 'published', is_featured = 1 WHERE slug = 'frontend-development-with-react' OR id = 14;
UPDATE courses SET price = 7999.00, original_price = 11999.00, duration = '8 Weeks', status = 'published', is_featured = 0 WHERE slug = 'react-nextjs-frontend-engineering' OR id = 55;
UPDATE courses SET price = 7999.00, original_price = 11999.00, duration = '8 Weeks', status = 'published', is_featured = 0 WHERE slug = 'backend-development-nodejs-apis' OR id = 56;
UPDATE courses SET price = 9999.00, original_price = 14999.00, duration = '10 Weeks', status = 'published', is_featured = 0 WHERE slug = 'mobile-app-development-react-native' OR id = 57;
UPDATE courses SET price = 2499.00, original_price = 3999.00, duration = '4 Weeks', status = 'published', is_featured = 0 WHERE slug = 'build-your-first-website' OR id = 50;
UPDATE courses SET price = 5999.00, original_price = 8999.00, duration = '6 Weeks', status = 'published', is_featured = 0 WHERE slug = 'software-testing-qa-playwright' OR id = 61;
UPDATE courses SET price = 5999.00, original_price = 8999.00, duration = '8 Weeks', status = 'published', is_featured = 1 WHERE slug = 'python-programming-automation' OR id = 46;
UPDATE courses SET price = 3499.00, original_price = 4999.00, duration = '4 Weeks', status = 'published', is_featured = 0 WHERE slug = 'sql-database-fundamentals' OR id = 47;
UPDATE courses SET price = 4999.00, original_price = 7499.00, duration = '6 Weeks', status = 'published', is_featured = 0 WHERE slug = 'core-java-object-oriented-programming' OR id = 63;
UPDATE courses SET price = 2499.00, original_price = 3999.00, duration = '30 Days', status = 'published', is_featured = 0 WHERE slug = 'python-for-ai-ml' OR id = 39;
UPDATE courses SET price = 1999.00, original_price = 3499.00, duration = '4 Weeks', status = 'published', is_featured = 0 WHERE slug = 'digital-skills-computer-fundamentals' OR id = 49;
UPDATE courses SET price = 9999.00, original_price = 14999.00, duration = '10 Weeks', status = 'published', is_featured = 1 WHERE slug = 'data-analytics-excel-sql-powerbi' OR id = 54;
UPDATE courses SET price = 12999.00, original_price = 18999.00, duration = '14 Weeks', status = 'published', is_featured = 1 WHERE slug = 'data-science-machine-learning-foundations' OR id = 64;
UPDATE courses SET price = 2999.00, original_price = 4999.00, duration = '40 Days', status = 'published', is_featured = 1 WHERE slug = 'machine-learning-python-projects' OR id = 42;
UPDATE courses SET price = 14999.00, original_price = 22999.00, duration = '16 Weeks', status = 'published', is_featured = 1 WHERE slug = 'artificial-intelligence-machine-learning' OR id = 67;
UPDATE courses SET price = 12999.00, original_price = 19999.00, duration = '12 Weeks', status = 'published', is_featured = 1 WHERE slug = 'generative-ai-agent-engineering' OR id = 58;
UPDATE courses SET price = 6999.00, original_price = 9999.00, duration = '6 Weeks', status = 'published', is_featured = 0 WHERE slug = 'ai-automation-nocode-tools' OR id = 53;
UPDATE courses SET price = 2999.00, original_price = 4999.00, duration = '45 Days', status = 'published', is_featured = 1 WHERE slug = 'ai-machine-learning-fundamentals' OR id = 37;
UPDATE courses SET price = 2999.00, original_price = 4999.00, duration = '30 Days', status = 'published', is_featured = 1 WHERE slug = 'ai-agents-workflow-automation' OR id = 40;
UPDATE courses SET price = 1499.00, original_price = 2499.00, duration = '20 Days', status = 'published', is_featured = 1 WHERE slug = 'prompt-engineering-generative-ai' OR id = 38;
UPDATE courses SET price = 1999.00, original_price = 3499.00, duration = '3 Weeks', status = 'published', is_featured = 0 WHERE slug = 'ai-tools-for-study-work' OR id = 51;
UPDATE courses SET price = 999.00, original_price = 1999.00, duration = '15 Days', status = 'published', is_featured = 1 WHERE slug = 'chatgpt-gemini-ai-productivity-tools' OR id = 41;
UPDATE courses SET price = 5999.00, original_price = 8999.00, duration = '6 Weeks', status = 'published', is_featured = 0 WHERE slug = 'aws-cloud-deployment-fundamentals' OR id = 48;
UPDATE courses SET price = 8999.00, original_price = 12999.00, duration = '8 Weeks', status = 'published', is_featured = 0 WHERE slug = 'docker-devops-cicd-essentials' OR id = 59;
UPDATE courses SET price = 7999.00, original_price = 11999.00, duration = '8 Weeks', status = 'published', is_featured = 0 WHERE slug = 'cybersecurity-ethical-security-foundations' OR id = 66;
UPDATE courses SET price = 7999.00, original_price = 11999.00, duration = '12 Weeks', status = 'published', is_featured = 1 WHERE slug = 'dsa-problem-solving-java' OR id = 21;
UPDATE courses SET price = 5999.00, original_price = 8999.00, duration = '4 Weeks', status = 'published', is_featured = 0 WHERE slug = 'tech-career-launchpad' OR id = 52;
UPDATE courses SET price = 9999.00, original_price = 14999.00, duration = '8 Weeks', status = 'published', is_featured = 0 WHERE slug = 'system-design-microservices-foundations' OR id = 65;
UPDATE courses SET price = 5999.00, original_price = 8999.00, duration = '6 Weeks', status = 'published', is_featured = 0 WHERE slug = 'ui-ux-design-with-figma' OR id = 60;
UPDATE courses SET price = 5999.00, original_price = 8999.00, duration = '6 Weeks', status = 'published', is_featured = 0 WHERE slug = 'digital-marketing-seo-analytics' OR id = 62;

-- 4. Update settings count
INSERT INTO settings (`key`, `value`) VALUES ('courses_count', '31') ON DUPLICATE KEY UPDATE `value` = '31';
