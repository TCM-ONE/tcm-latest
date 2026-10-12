-- =====================================================================
-- The Code Munk — Update Course Thumbnails to Animated GIFs
-- Run this in phpMyAdmin or via MySQL CLI on the server
-- =====================================================================

UPDATE courses SET thumbnail = 'coding-fullstack.gif'  WHERE slug = 'full-stack-development';
UPDATE courses SET thumbnail = 'coding-monitor.gif'    WHERE slug = 'frontend-development';
UPDATE courses SET thumbnail = 'coding-python.gif'     WHERE slug = 'python-development';
UPDATE courses SET thumbnail = 'coding-database.gif'   WHERE slug = 'database-systems';
UPDATE courses SET thumbnail = 'coding-algorithm.gif'  WHERE slug = 'dsa-problem-solving';
UPDATE courses SET thumbnail = 'coding-frontend.gif'   WHERE slug = 'cloud-fundamentals';
UPDATE courses SET thumbnail = 'coding-basics.gif'     WHERE slug = 'computer-basics';
UPDATE courses SET thumbnail = 'coding-webdev.gif'     WHERE slug = 'web-development-basics';
UPDATE courses SET thumbnail = 'coding-ai.gif'         WHERE slug = 'ai-tools-mastery';
UPDATE courses SET thumbnail = 'coding-career.gif'     WHERE slug = 'career-transition-program';
UPDATE courses SET thumbnail = 'coding-automation.gif' WHERE slug = 'ai-automation';
UPDATE courses SET thumbnail = 'coding-analytics.gif'  WHERE slug = 'data-analytics';

-- Fallback for any other course with empty or NULL thumbnail
UPDATE courses SET thumbnail = 'coding-fullstack.gif' WHERE thumbnail IS NULL OR thumbnail = '';
