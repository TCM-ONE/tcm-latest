-- Clean existing workshop records seeded before the festival copy was removed.
UPDATE events
SET title = REPLACE(title, ' | Dussehra Special', ''),
    description = REPLACE(
        REPLACE(description, ' (Dussehra Special)', ''),
        '2-Week Dussehra Special Data Analysis Workshop',
        '2-week Data Analysis Workshop'
    )
WHERE slug = 'data-analysis-workshop'
  AND (title LIKE '%Dussehra Special%' OR description LIKE '%Dussehra Special%');
