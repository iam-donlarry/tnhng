-- ==============================================================================
-- The Next Highlandah (TNH Season 3) - Database Schema
-- William Lawson's Promotional Campaign
-- Character Set: utf8mb4 | Collation: utf8mb4_unicode_ci | Engine: InnoDB
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. Table: `outlets`
-- Activation venues (bars, clubs, lounges) hosting campaign activations
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `outlets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL COMMENT 'Venue / bar / lounge trade name',
  `city` VARCHAR(100) NOT NULL COMMENT 'City (e.g. Lagos, Abuja, Port Harcourt)',
  `address` VARCHAR(255) NULL COMMENT 'Physical street address',
  `event_date` VARCHAR(100) NULL COMMENT 'Scheduled activation date and time',
  `status` ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Venue status',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_city` (`city`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Table: `promoters`
-- Brand ambassadors, game masters, and campaign supervisors
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `promoters` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `staff_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Unique ambassador ID (e.g. PRM-101)',
  `name` VARCHAR(150) NOT NULL COMMENT 'Full legal name',
  `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Login email address',
  `phone` VARCHAR(50) NOT NULL COMMENT 'Contact phone number',
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'Bcrypt password hash',
  `outlet_id` INT NULL COMMENT 'Currently assigned venue',
  `role` ENUM('promoter', 'supervisor', 'admin') DEFAULT 'promoter' COMMENT 'Access role',
  `status` ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Account status',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_outlet_id` (`outlet_id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_staff_code` (`staff_code`),
  INDEX `idx_role` (`role`),
  CONSTRAINT `fk_promoters_outlet` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Table: `participants`
-- Registered clan members accumulating points across activation outlets
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `participants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `clan_id` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Unique Clan ID (e.g. TNH-7701)',
  `full_name` VARCHAR(150) NOT NULL COMMENT 'Participant name',
  `phone` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Phone number for verification',
  `email` VARCHAR(150) NOT NULL COMMENT 'Participant email address',
  `dob` DATE NOT NULL COMMENT 'Date of birth (18+ verification)',
  `outlet_id` INT NULL COMMENT 'Origin registration outlet',
  `registered_by_promoter_id` INT NULL COMMENT 'Ambassador who registered participant',
  `total_points` INT NOT NULL DEFAULT 0 COMMENT 'Total verified locked points',
  `is_qualified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if >= 100 points, else 0',
  `instagram` VARCHAR(100) NULL COMMENT 'Optional Instagram handle',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_clan_id` (`clan_id`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_email` (`email`),
  INDEX `idx_total_points` (`total_points`),
  INDEX `idx_is_qualified` (`is_qualified`),
  INDEX `idx_outlet_id` (`outlet_id`),
  INDEX `idx_promoter_id` (`registered_by_promoter_id`),
  CONSTRAINT `fk_participants_outlet` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_participants_promoter` FOREIGN KEY (`registered_by_promoter_id`) REFERENCES `promoters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Table: `challenges`
-- Fixed master scorecard challenges and locked point values
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `challenges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50) NOT NULL COMMENT 'Category: Bold, Good humored, Unconventional',
  `title` VARCHAR(150) NOT NULL COMMENT 'Challenge name',
  `description` TEXT NOT NULL COMMENT 'Challenge rules and instructions',
  `points` INT NOT NULL DEFAULT 0 COMMENT 'Strictly locked point allocation',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT '1 = active on wheel/console, 0 = disabled',
  INDEX `idx_category` (`category`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Table: `points_log`
-- Immutable audit ledger logging each point allocation event
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `points_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `participant_id` INT NOT NULL COMMENT 'Target clan participant',
  `promoter_id` INT NOT NULL COMMENT 'Ambassador / user allocating points',
  `outlet_id` INT NULL COMMENT 'Venue where challenge was completed',
  `outlet_name` VARCHAR(150) NOT NULL COMMENT 'Denormalized venue name for audit records',
  `challenge_id` INT NULL COMMENT 'Challenge reference ID',
  `challenge_name` VARCHAR(150) NOT NULL COMMENT 'Denormalized challenge title',
  `category` VARCHAR(50) NOT NULL COMMENT 'Challenge category',
  `points` INT NOT NULL COMMENT 'Points credited',
  `notes` VARCHAR(255) NULL COMMENT 'Optional game master remarks',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_participant_id` (`participant_id`),
  INDEX `idx_promoter_id` (`promoter_id`),
  INDEX `idx_outlet_id` (`outlet_id`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_points_log_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_points_log_promoter` FOREIGN KEY (`promoter_id`) REFERENCES `promoters` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_points_log_outlet` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_points_log_challenge` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
