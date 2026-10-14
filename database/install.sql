-- ==============================================================================
-- The Next Highlandah (TNH Season 3) - Complete All-In-One Database Installation
-- William Lawson's Promotional Campaign (Nigeria)
-- 
-- Quick Import Instructions:
-- 1. Create a MySQL database (e.g. `microsite` or `tnhng_db`)
-- 2. Import this file:
--    mysql -u [username] -p [database_name] < database/install.sql
--    OR upload via phpMyAdmin / cPanel Database Import tab.
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. Table: `outlets`
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `points_log`;
DROP TABLE IF EXISTS `participants`;
DROP TABLE IF EXISTS `promoters`;
DROP TABLE IF EXISTS `challenges`;
DROP TABLE IF EXISTS `outlets`;

CREATE TABLE `outlets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `address` VARCHAR(255) NULL,
  `event_date` VARCHAR(100) NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_city` (`city`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Table: `promoters`
-- ------------------------------------------------------------------------------
CREATE TABLE `promoters` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `staff_code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `outlet_id` INT NULL,
  `role` ENUM('promoter', 'supervisor', 'admin') DEFAULT 'promoter',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_outlet_id` (`outlet_id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_staff_code` (`staff_code`),
  INDEX `idx_role` (`role`),
  CONSTRAINT `fk_promoters_outlet` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Table: `participants`
-- ------------------------------------------------------------------------------
CREATE TABLE `participants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `clan_id` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL,
  `dob` DATE NOT NULL,
  `outlet_id` INT NULL,
  `registered_by_promoter_id` INT NULL,
  `total_points` INT NOT NULL DEFAULT 0,
  `is_qualified` TINYINT(1) NOT NULL DEFAULT 0,
  `instagram` VARCHAR(100) NULL,
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
-- ------------------------------------------------------------------------------
CREATE TABLE `challenges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `points` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  INDEX `idx_category` (`category`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Table: `points_log`
-- ------------------------------------------------------------------------------
CREATE TABLE `points_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `participant_id` INT NOT NULL,
  `promoter_id` INT NOT NULL,
  `outlet_id` INT NULL,
  `outlet_name` VARCHAR(150) NOT NULL,
  `challenge_id` INT NULL,
  `challenge_name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `points` INT NOT NULL,
  `notes` VARCHAR(255) NULL,
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

-- ==============================================================================
-- SEED DATA
-- ==============================================================================

-- Outlets
INSERT INTO `outlets` (`id`, `name`, `city`, `address`, `event_date`, `status`) VALUES
(1, 'Highland Arena - Victoria Island', 'Lagos', 'Plot 12, Adeola Odeku St, VI', 'Oct 18, 2026 - 6:00 PM', 'active'),
(2, 'The Highlander Lounge - Ikeja', 'Lagos', 'Isaac John St, GRA Ikeja', 'Oct 19, 2026 - 7:00 PM', 'active'),
(3, 'The Clan Vault - Lekki Phase 1', 'Lagos', 'Admiralty Way, Lekki', 'Oct 25, 2026 - 5:00 PM', 'active'),
(4, 'Braveheart Sports Bar - Abuja', 'Abuja', 'Wuse 2, Aminu Kano Crescent', 'Oct 26, 2026 - 6:30 PM', 'active'),
(5, 'Royal Stag Club - Port Harcourt', 'Port Harcourt', 'GRA Phase 2, PHC', 'Nov 02, 2026 - 6:00 PM', 'active'),
(6, 'TNH Grand Finale Pavilion - Eko Atlantic', 'Lagos', 'Eko Atlantic City Ground', 'Nov 15, 2026 - 4:00 PM (Grand Finale)', 'active');

-- Challenges
INSERT INTO `challenges` (`id`, `category`, `title`, `description`, `points`, `is_active`) VALUES
(1, 'Good humored', 'Football trivia', 'Answer 5 football related questions without failing.', 5, 1),
(2, 'Unconventional', 'Sing your fav song', 'Sing your favorite song with bold energy', 5, 1),
(3, 'Unconventional', 'Do squats', 'Do squats for 15 secs', 5, 1),
(4, 'Bold', 'Stand on one foot', 'Stand on one foot for 15 secs', 5, 1),
(5, 'Bold', 'Do a Freestyle Rap', 'Do a freestyle rap about William Lawson', 6, 1),
(6, 'Bold', 'No blinking', 'Go a whole minute without blinking', 3, 1),
(7, 'Bold', 'Lemon challenge', 'Participants to lick lemon without a facial expression', 3, 1),
(8, 'Good humored', 'Spin the bottle', 'Spin the bottle and answer a question or do a challenge', 2, 1),
(9, 'Good humored', 'Spell WL Backwards', 'Spell William Lawson backward without misspelling', 2, 1),
(10, 'Good humored', 'Imitate a celebrity', 'Do an impression of a celebrity', 3, 1),
(11, 'Unconventional', 'Spin around and walk on a straight line', 'Spin around 5 times and walk in a straight line without tripping', 3, 1),
(12, 'Bold', 'Push-up', 'Highest number of push up within 15 secs', 5, 1),
(13, 'Bold', 'Planking', 'Participants to maintain their body in a straight line for 60 secs', 5, 1),
(14, 'Bold', 'Foot ball leg juggling', 'Juggle 50 times with your legs', 5, 1),
(15, 'Good humored', 'Spin again', 'Spin the bottle again for another challenge', 0, 1);

-- Default Admin and Promoters
-- Passwords: admin123 (for admin), promoter123 (for promoters)
INSERT INTO `promoters` (`id`, `staff_code`, `name`, `email`, `phone`, `password_hash`, `outlet_id`, `role`, `status`) VALUES
(1, 'ADM-001', 'Campaign Lead (Admin)', 'admin@tnhng.com', '+2348011223344', '$2y$10$wB5kUkc/88hH5yZ1Nq8sCO9gOaUaQG1p9z0jW9hNlFqU2g2k7Lq3u', 1, 'admin', 'active'),
(2, 'PRM-101', 'Emeka Nnamdi', 'emeka@tnhng.com', '+2348022334455', '$2y$10$zY7uWd7G0O0u7I9g8A2S0.j0jW9hNlFqU2g2k7Lq3u8A2S0.j0jW9', 1, 'promoter', 'active'),
(3, 'PRM-102', 'Folake Adebayo', 'folake@tnhng.com', '+2348033445566', '$2y$10$zY7uWd7G0O0u7I9g8A2S0.j0jW9hNlFqU2g2k7Lq3u8A2S0.j0jW9', 2, 'promoter', 'active'),
(4, 'PRM-103', 'Chinedu Okeke', 'chinedu@tnhng.com', '+2348044556677', '$2y$10$zY7uWd7G0O0u7I9g8A2S0.j0jW9hNlFqU2g2k7Lq3u8A2S0.j0jW9', 3, 'promoter', 'active'),
(5, 'PRM-104', 'Fatima Bello', 'fatima@tnhng.com', '+2348055667788', '$2y$10$zY7uWd7G0O0u7I9g8A2S0.j0jW9hNlFqU2g2k7Lq3u8A2S0.j0jW9', 4, 'promoter', 'active');

-- Sample Participants
INSERT INTO `participants` (`id`, `clan_id`, `full_name`, `phone`, `email`, `dob`, `outlet_id`, `registered_by_promoter_id`, `total_points`, `is_qualified`, `instagram`) VALUES
(1, 'TNH-7701', 'Tunde Bakare', '+2348099887711', 'tunde.b@example.com', '1996-04-12', 1, 2, 105, 1, '@tundetitan'),
(2, 'TNH-6422', 'Amaka Eze', '+2348099887722', 'amaka.eze@example.com', '1998-08-23', 2, 3, 100, 1, '@amakaze'),
(3, 'TNH-5190', 'Oluwaseun Davies', '+2348099887733', 'seun.d@example.com', '1994-11-05', 1, 2, 85, 0, '@seun_davies'),
(4, 'TNH-4820', 'Ibrahim Sani', '+2348099887744', 'ibrahim.s@example.com', '1997-02-17', 4, 5, 76, 0, '@ibrahimsani'),
(5, 'TNH-3914', 'Blessing Okoro', '+2348099887755', 'blessing.o@example.com', '1999-06-30', 3, 4, 62, 0, '@blessing_o'),
(6, 'TNH-2845', 'David Adeleke', '+2348099887766', 'david.ad@example.com', '1995-09-14', 1, 2, 45, 0, '@davidadels'),
(7, 'TNH-1992', 'Zainab Lawal', '+2348099887777', 'zainab.l@example.com', '1998-12-01', 2, 3, 30, 0, '@zainab_lwl');

-- Sample Points Allocation Log
INSERT INTO `points_log` (`participant_id`, `promoter_id`, `outlet_id`, `outlet_name`, `challenge_id`, `challenge_name`, `category`, `points`, `notes`) VALUES
(1, 2, 1, 'Highland Arena - Victoria Island', 5, 'Do a Freestyle Rap', 'Bold', 6, 'Hyped up the crowd with a bold freestyle!'),
(1, 2, 1, 'Highland Arena - Victoria Island', 1, 'Football trivia', 'Good humored', 5, '5/5 questions correct'),
(1, 3, 2, 'The Highlander Lounge - Ikeja', 12, 'Push-up', 'Bold', 5, '35 pushups in 15s - Crossed 100 points threshold!'),
(2, 3, 2, 'The Highlander Lounge - Ikeja', 13, 'Planking', 'Bold', 5, 'Solid 60 seconds plank'),
(2, 3, 2, 'The Highlander Lounge - Ikeja', 14, 'Foot ball leg juggling', 'Bold', 5, '50 juggles on-stage'),
(3, 2, 1, 'Highland Arena - Victoria Island', 4, 'Stand on one foot', 'Bold', 5, 'Completed challenge without wobble');

SET FOREIGN_KEY_CHECKS = 1;
