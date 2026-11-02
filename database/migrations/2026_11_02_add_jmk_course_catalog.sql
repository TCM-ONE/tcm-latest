-- Add the additional courses shown on JMK Education Centre's supplied signboard.
-- Safe to re-run: existing partner courses are left unchanged.

INSERT INTO partner_courses (
    partner_id, title, slug, category, description, admissions_status, review_status, reviewed_at
)
SELECT p.id, seed.title, seed.slug, seed.category, seed.description, 'open', 'approved', NOW()
FROM partners p
JOIN (
    SELECT
        'ADCA · Advanced Diploma in Computer Applications' AS title,
        'adca-advanced-diploma-computer-applications' AS slug,
        'Computer Applications' AS category,
        'Advanced Diploma in Computer Applications. Contact JMK for the current syllabus, duration, and fees.' AS description
    UNION ALL SELECT
        'PGDCA · Post Graduate Diploma in Computer Applications',
        'pgdca-post-graduate-diploma-computer-applications',
        'Computer Applications',
        'Post Graduate Diploma in Computer Applications. Contact JMK for eligibility, the current syllabus, duration, and fees.'
    UNION ALL SELECT
        'Tally Advanced',
        'tally-advanced',
        'Accounting Software',
        'Advanced Tally training. Contact JMK for the current syllabus, duration, and fees.'
    UNION ALL SELECT
        'Programming Courses',
        'programming-courses',
        'Programming',
        'Programming courses at JMK Education Centre. Contact the centre for available languages, syllabus, duration, and fees.'
    UNION ALL SELECT
        'Hindi & English Typing',
        'hindi-english-typing',
        'Typing',
        'Typing practice in Hindi and English. Contact JMK for batch availability and details.'
    UNION ALL SELECT
        'Computer Operator Exam Preparation',
        'computer-operator-exam-preparation',
        'Exam Preparation',
        'Preparation for computer operator examinations. Contact JMK for current exam coverage, batches, and fees.'
    UNION ALL SELECT
        'Spoken English & Legal English',
        'spoken-english-legal-english',
        'Language Skills',
        'Spoken English and Legal English classes. Contact JMK for learner eligibility, course details, and schedules.'
) AS seed
WHERE p.slug = 'jmk-computer-education-center'
  AND NOT EXISTS (
      SELECT 1
      FROM partner_courses existing
      WHERE existing.partner_id = p.id AND existing.slug = seed.slug
  );
