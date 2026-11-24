-- Keep every published course priced at ₹4,999 or more and two starter courses.
-- Archive instead of deleting, preserving enrollment and other foreign-key references.
UPDATE courses
SET status = 'archived', is_featured = 0
WHERE status = 'published'
  AND price < 4999.00
  AND slug IN (
    'ai-machine-learning-fundamentals',
    'prompt-engineering-generative-ai',
    'python-for-ai-ml',
    'ai-agents-workflow-automation',
    'chatgpt-gemini-ai-productivity-tools',
    'machine-learning-python-projects',
    'sql-database-fundamentals',
    'digital-skills-computer-fundamentals'
  );
