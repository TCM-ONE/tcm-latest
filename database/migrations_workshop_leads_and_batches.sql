-- =====================================================================
-- TCM One: Workshop Leads, Course Batches & Smart Expiry Migration
-- =====================================================================

SET NAMES utf8mb4;

-- 1. Workshop Leads Table
CREATE TABLE IF NOT EXISTS workshop_leads (
    id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    workshop_id             BIGINT UNSIGNED DEFAULT NULL,
    workshop_slug           VARCHAR(190)    NOT NULL,
    workshop_title          VARCHAR(220)    NOT NULL,
    full_name               VARCHAR(150)    NOT NULL,
    email                   VARCHAR(190)    NOT NULL,
    phone                   VARCHAR(30)     NOT NULL,
    current_status          ENUM('student','working','other') NOT NULL DEFAULT 'student',
    college                 VARCHAR(190)    DEFAULT NULL,
    city                    VARCHAR(120)    DEFAULT NULL,
    learning_goal           TEXT            DEFAULT NULL,
    selected_workshop_price DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    lead_status             ENUM('new','contacted','interested','payment_pending','enrolled','not_interested','lost') NOT NULL DEFAULT 'new',
    payment_status          ENUM('not_initiated','pending','paid','failed','refunded') NOT NULL DEFAULT 'not_initiated',
    enrollment_status       ENUM('pending','confirmed','waitlist','cancelled') NOT NULL DEFAULT 'pending',
    utm_source              VARCHAR(120)    DEFAULT NULL,
    utm_medium              VARCHAR(120)    DEFAULT NULL,
    utm_campaign            VARCHAR(120)    DEFAULT NULL,
    utm_content             VARCHAR(120)    DEFAULT NULL,
    landing_page            VARCHAR(255)    DEFAULT NULL,
    referrer                VARCHAR(255)    DEFAULT NULL,
    admin_notes             TEXT            DEFAULT NULL,
    consent_timestamp       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ws_leads_status (lead_status),
    KEY idx_ws_leads_pay_status (payment_status),
    KEY idx_ws_leads_slug (workshop_slug),
    KEY idx_ws_leads_email (email),
    KEY idx_ws_leads_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Course & Workshop Batches Table
CREATE TABLE IF NOT EXISTS course_batches (
    id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    item_type               ENUM('course','workshop') NOT NULL DEFAULT 'course',
    item_id                 BIGINT UNSIGNED NOT NULL,
    batch_code              VARCHAR(60)     NOT NULL,
    batch_title             VARCHAR(190)    NOT NULL,
    registration_opens_at   DATETIME        DEFAULT NULL,
    registration_closes_at  DATETIME        DEFAULT NULL,
    starts_at               DATETIME        NOT NULL,
    ends_at                 DATETIME        DEFAULT NULL,
    capacity                INT             NOT NULL DEFAULT 30,
    seats_left              INT             NOT NULL DEFAULT 30,
    status                  ENUM('upcoming','registration_open','registration_closed','in_progress','completed','cancelled') NOT NULL DEFAULT 'upcoming',
    is_featured             TINYINT(1)      NOT NULL DEFAULT 0,
    timezone                VARCHAR(40)     NOT NULL DEFAULT 'Asia/Kolkata',
    created_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_batch_code (batch_code),
    KEY idx_batch_item (item_type, item_id),
    KEY idx_batch_status (status),
    KEY idx_batch_starts (starts_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Promotional Offers Table
CREATE TABLE IF NOT EXISTS promotional_offers (
    id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    item_type               ENUM('course','workshop','global') NOT NULL DEFAULT 'course',
    item_id                 BIGINT UNSIGNED DEFAULT NULL,
    offer_title             VARCHAR(190)    NOT NULL,
    offer_code              VARCHAR(60)     DEFAULT NULL,
    discount_price          DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    original_price          DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    banner_text             VARCHAR(255)    DEFAULT NULL,
    starts_at               DATETIME        NOT NULL,
    expires_at              DATETIME        NOT NULL,
    is_active               TINYINT(1)      NOT NULL DEFAULT 1,
    created_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_offers_active (is_active, expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
