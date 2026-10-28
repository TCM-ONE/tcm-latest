-- Add three focused project courses for the Diwali 2026 offer.
-- Additive and re-runnable: this migration inserts only missing course slugs,
-- then seeds their curriculum without changing existing courses or learners.
-- Regular fees remain in courses.price; Diwali prices are configured in
-- src/Services/DiwaliOffer.php and expire with the existing campaign.

START TRANSACTION;

INSERT INTO courses (
    category_id, instructor_id, title, slug, subtitle, description, icon, thumbnail,
    level, language, duration, price, original_price, rating, reviews_count,
    students_count, total_seats, seats_left, schedule, starts_at,
    is_featured, is_bestseller, status
)
SELECT category.id, NULL,
       'Full Stack Project Lab: React & Node.js',
       'full-stack-project-lab-react-node',
       'Build a portfolio-ready React app, Node.js REST API and deployed full-stack project',
       'Build one portfolio-ready full stack web project in a focused online cohort. Connect a responsive React frontend to a Node.js and Express REST API, validate data, handle common errors, test key flows and deploy a working demo. This short project lab complements the longer Full Stack curriculum.',
       'bi-layers', NULL, 'beginner', 'Hindi + English', '4 Weeks',
       6999.00, 6999.00, 0.0, 0, 0, 0, 0, NULL, NULL, 0, 0, 'published'
FROM categories AS category
WHERE category.slug = 'web-mobile-development'
  AND NOT EXISTS (SELECT 1 FROM courses WHERE slug = 'full-stack-project-lab-react-node')
LIMIT 1;

INSERT INTO courses (
    category_id, instructor_id, title, slug, subtitle, description, icon, thumbnail,
    level, language, duration, price, original_price, rating, reviews_count,
    students_count, total_seats, seats_left, schedule, starts_at,
    is_featured, is_bestseller, status
)
SELECT category.id, NULL,
       'Frontend Portfolio Builder: HTML, CSS & JavaScript',
       'frontend-portfolio-builder-html-css-js',
       'Create a responsive web portfolio with semantic HTML, modern CSS and JavaScript',
       'Practise frontend development by designing and publishing a personal portfolio site. Use semantic HTML, accessible page structure, CSS Flexbox and Grid, responsive breakpoints and small JavaScript interactions to create a polished project you can share. This project-focused course complements the longer React programme.',
       'bi-window', NULL, 'beginner', 'Hindi + English', '4 Weeks',
       5999.00, 5999.00, 0.0, 0, 0, 0, 0, NULL, NULL, 0, 0, 'published'
FROM categories AS category
WHERE category.slug = 'web-mobile-development'
  AND NOT EXISTS (SELECT 1 FROM courses WHERE slug = 'frontend-portfolio-builder-html-css-js')
LIMIT 1;

INSERT INTO courses (
    category_id, instructor_id, title, slug, subtitle, description, icon, thumbnail,
    level, language, duration, price, original_price, rating, reviews_count,
    students_count, total_seats, seats_left, schedule, starts_at,
    is_featured, is_bestseller, status
)
SELECT category.id, NULL,
       'Data Analysis Dashboard Sprint: Excel & Power BI',
       'data-analysis-dashboard-sprint-excel-powerbi',
       'Practice data analysis with Excel, Power Query and Power BI by building a business dashboard',
       'This beginner data analysis course uses Excel and Power BI to complete one guided dashboard project. Clean and validate a small dataset, summarize it with formulas and PivotTables, shape data with Power Query, define useful business KPIs and present results in an interactive report. It is a focused dashboard sprint, narrower than the full Data Analytics programme.',
       'bi-bar-chart-line', NULL, 'beginner', 'Hindi + English', '4 Weeks',
       5999.00, 5999.00, 0.0, 0, 0, 0, 0, NULL, NULL, 0, 0, 'published'
FROM categories AS category
WHERE category.slug = 'data-analytics'
  AND NOT EXISTS (SELECT 1 FROM courses WHERE slug = 'data-analysis-dashboard-sprint-excel-powerbi')
LIMIT 1;

DROP TEMPORARY TABLE IF EXISTS tmp_diwali_2026_course_modules;
CREATE TEMPORARY TABLE tmp_diwali_2026_course_modules (
    course_slug VARCHAR(200) NOT NULL,
    position INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    summary VARCHAR(255) NOT NULL,
    PRIMARY KEY (course_slug, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO tmp_diwali_2026_course_modules (course_slug, position, title, summary) VALUES
('full-stack-project-lab-react-node', 1, 'Plan the App & Build the React Interface', 'Scope a small web project and create responsive React screens, forms and UI states.'),
('full-stack-project-lab-react-node', 2, 'Create the Node.js REST API', 'Build Express routes, validate requests, handle errors and persist project data.'),
('full-stack-project-lab-react-node', 3, 'Integrate, Test & Deploy the Project', 'Connect the UI and API, verify key user flows and deploy a working demo.'),
('frontend-portfolio-builder-html-css-js', 1, 'Portfolio Structure & Semantic HTML', 'Plan sections and build accessible markup for projects, navigation and contact details.'),
('frontend-portfolio-builder-html-css-js', 2, 'Modern CSS & Responsive Design', 'Use typography, Flexbox, Grid and responsive breakpoints for a polished portfolio.'),
('frontend-portfolio-builder-html-css-js', 3, 'JavaScript, Quality & Publishing', 'Add interactions, check accessibility and publish the finished portfolio site.'),
('data-analysis-dashboard-sprint-excel-powerbi', 1, 'Understand & Clean the Dataset in Excel', 'Identify data-quality issues, clean values and summarize records with formulas and PivotTables.'),
('data-analysis-dashboard-sprint-excel-powerbi', 2, 'Prepare Data & Define Useful KPIs', 'Transform data with Power Query and choose measures that answer practical business questions.'),
('data-analysis-dashboard-sprint-excel-powerbi', 3, 'Build & Present the Power BI Dashboard', 'Create an interactive report, check chart readability and explain the findings.');

INSERT INTO course_modules (course_id, title, summary, position)
SELECT course.id, seed.title, seed.summary, seed.position
FROM tmp_diwali_2026_course_modules AS seed
JOIN courses AS course ON course.slug = seed.course_slug
WHERE NOT EXISTS (
    SELECT 1 FROM course_modules AS existing
    WHERE existing.course_id = course.id AND existing.position = seed.position
);

DROP TEMPORARY TABLE IF EXISTS tmp_diwali_2026_course_lessons;
CREATE TEMPORARY TABLE tmp_diwali_2026_course_lessons (
    course_slug VARCHAR(200) NOT NULL,
    module_position INT NOT NULL,
    position INT NOT NULL,
    title VARCHAR(220) NOT NULL,
    lesson_type ENUM('live','video','project','reading','quiz') NOT NULL,
    duration_minutes INT NOT NULL,
    is_preview TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (course_slug, module_position, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO tmp_diwali_2026_course_lessons
    (course_slug, module_position, position, title, lesson_type, duration_minutes, is_preview) VALUES
('full-stack-project-lab-react-node', 1, 1, 'Choose a Small Project Scope and Map User Flows', 'live', 60, 1),
('full-stack-project-lab-react-node', 1, 2, 'Build Reusable React Screens, Forms and UI States', 'project', 90, 0),
('full-stack-project-lab-react-node', 1, 3, 'Create a Responsive Layout for Mobile and Desktop', 'project', 90, 0),
('full-stack-project-lab-react-node', 2, 1, 'Set Up Express Routes and Request Validation', 'live', 60, 0),
('full-stack-project-lab-react-node', 2, 2, 'Implement CRUD Operations and Clear Error Responses', 'project', 90, 0),
('full-stack-project-lab-react-node', 2, 3, 'Connect the API to Persistent Project Data', 'project', 90, 0),
('full-stack-project-lab-react-node', 3, 1, 'Connect React Forms and Views to the REST API', 'live', 60, 0),
('full-stack-project-lab-react-node', 3, 2, 'Check Main User Flows and Fix Integration Bugs', 'project', 90, 0),
('full-stack-project-lab-react-node', 3, 3, 'Deploy the Demo and Present the Project Decisions', 'project', 90, 0),
('frontend-portfolio-builder-html-css-js', 1, 1, 'Plan Sections, Content and Navigation', 'live', 60, 1),
('frontend-portfolio-builder-html-css-js', 1, 2, 'Build Accessible HTML for Projects and Contact Details', 'project', 90, 0),
('frontend-portfolio-builder-html-css-js', 1, 3, 'Add Responsive Navigation and Useful Page Links', 'project', 90, 0),
('frontend-portfolio-builder-html-css-js', 2, 1, 'Style with Typography, Spacing and Colour Tokens', 'live', 60, 0),
('frontend-portfolio-builder-html-css-js', 2, 2, 'Build Layouts with Flexbox and CSS Grid', 'project', 90, 0),
('frontend-portfolio-builder-html-css-js', 2, 3, 'Adapt the Portfolio for Mobile, Tablet and Desktop', 'project', 90, 0),
('frontend-portfolio-builder-html-css-js', 3, 1, 'Add Small Interactions and Form Feedback', 'live', 60, 0),
('frontend-portfolio-builder-html-css-js', 3, 2, 'Check Keyboard Access, Contrast and Browser Layouts', 'project', 90, 0),
('frontend-portfolio-builder-html-css-js', 3, 3, 'Publish the Site and Review the Finished Portfolio', 'project', 90, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 1, 1, 'Read a Business Dataset and Identify Data Quality Issues', 'live', 60, 1),
('data-analysis-dashboard-sprint-excel-powerbi', 1, 2, 'Clean Values, Dates and Categories with Excel Tools', 'project', 90, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 1, 3, 'Summarize Records with Formulas and PivotTables', 'project', 90, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 2, 1, 'Transform Tables with Power Query', 'live', 60, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 2, 2, 'Choose Measures and KPIs That Match the Questions', 'project', 90, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 2, 3, 'Organize Data for a Clear Report', 'project', 90, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 3, 1, 'Create Visuals, Filters and an Interactive Report Page', 'live', 60, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 3, 2, 'Check Labels, Accessibility and Chart Readability', 'project', 90, 0),
('data-analysis-dashboard-sprint-excel-powerbi', 3, 3, 'Present the Dashboard and Explain the Findings', 'project', 90, 0);

INSERT INTO course_lessons (module_id, title, type, duration_minutes, position, is_preview)
SELECT module.id, seed.title, seed.lesson_type, seed.duration_minutes, seed.position, seed.is_preview
FROM tmp_diwali_2026_course_lessons AS seed
JOIN courses AS course ON course.slug = seed.course_slug
JOIN course_modules AS module ON module.course_id = course.id AND module.position = seed.module_position
WHERE NOT EXISTS (
    SELECT 1 FROM course_lessons AS existing
    WHERE existing.module_id = module.id AND existing.position = seed.position
);

DROP TEMPORARY TABLE tmp_diwali_2026_course_lessons;
DROP TEMPORARY TABLE tmp_diwali_2026_course_modules;

COMMIT;
