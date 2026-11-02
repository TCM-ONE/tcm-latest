-- Add JMK Computer Education Centre to the public partner directory.
-- Idempotent seed: re-running it updates the listing and does not delete data.
-- Run after 2026_10_25_partner_ecosystem.sql has been applied.

INSERT INTO partners (
    partner_code, legal_name, display_name, slug, institution_type,
    partnership_type, status, city, state, postal_code, address,
    logo_path, description, public_phone, whatsapp, map_url,
    public_status, profile_completeness
) VALUES (
    'TCM-P-JMK-001',
    'JMK Computer Education Centre',
    'JMK Computer Education Centre',
    'jmk-computer-education-center',
    'Computer education and tuition centre',
    'academic',
    'active',
    'Bilaspur',
    'Chhattisgarh',
    '495006',
    'Near DLS College Road, Ashok Nagar, Sarkanda, Bilaspur, Chhattisgarh 495006',
    'assets/partners/jmk-logo.svg',
    'JMK Computer Education Centre in Ashok Nagar, Bilaspur offers tuition for Classes 1 to 12 and computer education. Contact the centre for current subjects, programs, schedules, and fees.',
    '070007 27814',
    '917000727814',
    'https://www.google.com/maps/place/jmk+computer+education+center/data=!4m2!3m1!1s0x0:0x6a1b0bfa7e567bdd',
    'published',
    90
)
ON DUPLICATE KEY UPDATE
    display_name = VALUES(display_name),
    slug = VALUES(slug),
    institution_type = VALUES(institution_type),
    partnership_type = VALUES(partnership_type),
    status = VALUES(status),
    city = VALUES(city),
    state = VALUES(state),
    postal_code = VALUES(postal_code),
    address = VALUES(address),
    logo_path = VALUES(logo_path),
    description = VALUES(description),
    public_phone = VALUES(public_phone),
    whatsapp = VALUES(whatsapp),
    map_url = VALUES(map_url),
    public_status = VALUES(public_status),
    profile_completeness = VALUES(profile_completeness);

INSERT INTO partner_courses (
    partner_id, title, slug, category, description, admissions_status, review_status, reviewed_at
)
SELECT p.id, seed.title, seed.slug, seed.category, seed.description, 'open', 'approved', NOW()
FROM partners p
JOIN (
    SELECT
        'Tuition support · Classes 1–12' AS title,
        'tuition-classes-1-to-12' AS slug,
        'School tuition' AS category,
        'Tuition support for students from Classes 1 to 12. Contact JMK for subjects, current availability, and schedules.' AS description
    UNION ALL
    SELECT
        'Computer education programs',
        'computer-education-programs',
        'Computer education',
        'Computer training at JMK Computer Education Centre. Contact the institution for current programs, batches, and fee details.'
) AS seed
WHERE p.slug = 'jmk-computer-education-center'
  AND NOT EXISTS (
      SELECT 1 FROM partner_courses existing
      WHERE existing.partner_id = p.id AND existing.slug = seed.slug
  );
