-- =====================================================================
-- Migration: PayU Payment Webhooks, Transactions, Refunds, Disputes & Installments
-- Date: 2026-10-16
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. Enhance orders Table ──────────────────────────────────────────
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `remaining_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `paid_amount`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `is_installment` TINYINT(1) NOT NULL DEFAULT 0 AFTER `remaining_amount`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `installment_number` INT UNSIGNED DEFAULT NULL AFTER `is_installment`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `total_installments` INT UNSIGNED DEFAULT NULL AFTER `installment_number`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `receipt_number` VARCHAR(60) DEFAULT NULL AFTER `payment_ref`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `verified_by_api` TINYINT(1) NOT NULL DEFAULT 0 AFTER `receipt_number`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `gateway_status` VARCHAR(50) DEFAULT NULL AFTER `status`;

-- ── 2. Create payment_transactions Table ─────────────────────────────
CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `txnid` VARCHAR(100) NOT NULL,
  `mihpayid` VARCHAR(100) DEFAULT NULL,
  `bank_ref_num` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
  `status` ENUM('pending','success','failure','refunded','disputed') NOT NULL DEFAULT 'pending',
  `payment_mode` VARCHAR(50) DEFAULT NULL,
  `card_type` VARCHAR(50) DEFAULT NULL,
  `bank_code` VARCHAR(50) DEFAULT NULL,
  `error_code` VARCHAR(100) DEFAULT NULL,
  `error_message` VARCHAR(255) DEFAULT NULL,
  `hash_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `api_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `raw_payload` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_txnid` (`txnid`),
  KEY `idx_pt_order` (`order_id`),
  KEY `idx_pt_user` (`user_id`),
  KEY `idx_pt_mihpayid` (`mihpayid`),
  KEY `idx_pt_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3. Create webhook_audit_logs Table ───────────────────────────────
CREATE TABLE IF NOT EXISTS `webhook_audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_type` VARCHAR(50) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `http_method` VARCHAR(10) NOT NULL DEFAULT 'POST',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `headers` TEXT DEFAULT NULL,
  `raw_payload` LONGTEXT DEFAULT NULL,
  `response_status` INT NOT NULL DEFAULT 200,
  `verification_status` ENUM('verified','hash_mismatch','api_mismatch','invalid_order','error') NOT NULL DEFAULT 'verified',
  `error_details` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wal_event` (`event_type`),
  KEY `idx_wal_status` (`verification_status`),
  KEY `idx_wal_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. Create payment_refunds Table ──────────────────────────────────
CREATE TABLE IF NOT EXISTS `payment_refunds` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_transaction_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `payu_refund_id` VARCHAR(100) DEFAULT NULL,
  `refund_txnid` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
  `status` ENUM('pending','success','failed') NOT NULL DEFAULT 'pending',
  `reason` VARCHAR(255) DEFAULT NULL,
  `raw_response` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_order` (`order_id`),
  KEY `idx_pr_user` (`user_id`),
  KEY `idx_pr_payu_id` (`payu_refund_id`),
  KEY `idx_pr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 5. Create payment_disputes Table ─────────────────────────────────
CREATE TABLE IF NOT EXISTS `payment_disputes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_transaction_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `payu_dispute_id` VARCHAR(100) DEFAULT NULL,
  `dispute_type` VARCHAR(80) DEFAULT NULL,
  `dispute_status` VARCHAR(80) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
  `reason` VARCHAR(255) DEFAULT NULL,
  `due_date` DATE DEFAULT NULL,
  `review_notes` TEXT DEFAULT NULL,
  `reviewed_by` BIGINT UNSIGNED DEFAULT NULL,
  `raw_payload` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pd_order` (`order_id`),
  KEY `idx_pd_user` (`user_id`),
  KEY `idx_pd_dispute_id` (`payu_dispute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 6. Create installment_schedules Table ────────────────────────────
CREATE TABLE IF NOT EXISTS `installment_schedules` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_order_id` BIGINT UNSIGNED DEFAULT NULL,
  `installment_order_id` BIGINT UNSIGNED DEFAULT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `course_id` BIGINT UNSIGNED NOT NULL,
  `installment_number` INT UNSIGNED NOT NULL,
  `total_installments` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `due_date` DATE NOT NULL,
  `status` ENUM('pending','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` DATETIME DEFAULT NULL,
  `payment_ref` VARCHAR(100) DEFAULT NULL,
  `txnid` VARCHAR(100) DEFAULT NULL,
  `notes` VARCHAR(255) DEFAULT NULL,
  `reminded_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_user` (`user_id`),
  KEY `idx_is_course` (`course_id`),
  KEY `idx_is_parent` (`parent_order_id`),
  KEY `idx_is_order` (`installment_order_id`),
  KEY `idx_is_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
