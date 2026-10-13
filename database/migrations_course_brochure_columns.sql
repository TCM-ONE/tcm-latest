-- Add per-course brochure columns used by admin course create/update forms.
-- Safe to run multiple times on MySQL/MariaDB via INFORMATION_SCHEMA checks.

SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE courses ADD COLUMN brochure_file VARCHAR(255) DEFAULT NULL AFTER thumbnail',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'courses'
      AND COLUMN_NAME = 'brochure_file'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE courses ADD COLUMN brochure_name VARCHAR(255) DEFAULT NULL AFTER brochure_file',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'courses'
      AND COLUMN_NAME = 'brochure_name'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
