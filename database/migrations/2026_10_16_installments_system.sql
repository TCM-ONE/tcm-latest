-- =====================================================================
-- Migration: TCM One Custom Installment Payment System
-- Date: 2026-10-16
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. Create installment_plans Table ─────────────────────────────────
CREATE TABLE IF NOT EXISTS `installment_plans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enrollment_id` BIGINT UNSIGNED DEFAULT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `course_id` BIGINT UNSIGNED NOT NULL,
  `parent_order_id` BIGINT UNSIGNED NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `upfront_amount` DECIMAL(10,2) NOT NULL,
  `total_installments` INT UNSIGNED NOT NULL,
  `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'INR',
  `status` ENUM('active','completed','defaulted','cancelled') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_user` (`user_id`),
  KEY `idx_ip_course` (`course_id`),
  KEY `idx_ip_order` (`parent_order_id`),
  KEY `idx_ip_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. Enhance installment_schedules Table ─────────────────────────────
ALTER TABLE `installment_schedules` ADD COLUMN IF NOT EXISTS `installment_plan_id` BIGINT UNSIGNED DEFAULT NULL AFTER `parent_order_id`;
ALTER TABLE `installment_schedules` ADD COLUMN IF NOT EXISTS `reminder_stage` TINYINT NOT NULL DEFAULT 0 AFTER `reminded_at`;
ALTER TABLE `installment_schedules` ADD COLUMN IF NOT EXISTS `reminder_sent_at` DATETIME DEFAULT NULL AFTER `reminder_stage`;

-- ── 3. Create installment_reminders_log Table ─────────────────────────
CREATE TABLE IF NOT EXISTS `installment_reminders_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `schedule_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `channel` VARCHAR(30) NOT NULL DEFAULT 'email',
  `stage` VARCHAR(30) NOT NULL,
  `recipient` VARCHAR(150) NOT NULL,
  `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('sent','failed') NOT NULL DEFAULT 'sent',
  `error_message` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_schedule_channel_stage` (`schedule_id`, `channel`, `stage`),
  KEY `idx_irl_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. Enhance courses Table ──────────────────────────────────────────
ALTER TABLE `courses` ADD COLUMN IF NOT EXISTS `allow_installments` TINYINT(1) NOT NULL DEFAULT 1 AFTER `price`;

-- ── 5. Enhance payment_transactions Table ─────────────────────────────
ALTER TABLE `payment_transactions` ADD COLUMN IF NOT EXISTS `installment_schedule_id` BIGINT UNSIGNED DEFAULT NULL AFTER `order_id`;
ALTER TABLE `payment_transactions` ADD COLUMN IF NOT EXISTS `installment_plan_id` BIGINT UNSIGNED DEFAULT NULL AFTER `installment_schedule_id`;
ALTER TABLE `payment_transactions` ADD COLUMN IF NOT EXISTS `refund_reference` VARCHAR(100) DEFAULT NULL AFTER `bank_ref_num`;

-- ── 6. Seed Default Installment Settings ──────────────────────────────
INSERT INTO `settings` (`key`, `value`) VALUES
  ('installment_enabled', '1'),
  ('installment_min_amount', '5000.00'),
  ('installment_max_installments', '3'),
  ('installment_min_upfront_pct', '40'),
  ('installment_interval_days', '15'),
  ('installment_plans_available', '2,3'),
  ('installment_interest_rate', '0'),
  ('installment_reminder_email_enabled', '1')
ON DUPLICATE KEY UPDATE `key` = `key`;

SET FOREIGN_KEY_CHECKS = 1;
