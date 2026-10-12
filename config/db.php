<?php
// config/db.php - Database connection & Auto-Schema Initializer

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'microsite');

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Ensure clean image aliases exist
        $imagesDir = dirname(__DIR__) . '/images';
        if (file_exists($imagesDir . '/image.png') && !file_exists($imagesDir . '/highlandahs-only.png')) {
            @copy($imagesDir . '/image.png', $imagesDir . '/highlandahs-only.png');
        }
        if (file_exists($imagesDir . '/image copy.png') && !file_exists($imagesDir . '/earn-it.png')) {
            @copy($imagesDir . '/image copy.png', $imagesDir . '/earn-it.png');
        }

        // Connect to MySQL server first to ensure database exists
        $pdoServer = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Now connect to the microsite database
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Auto-initialize schema if needed
        initializeSchema($pdo);

        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

function initializeSchema($pdo) {
    // 1. Outlets Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `outlets` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `city` VARCHAR(100) NOT NULL,
        `address` VARCHAR(255) NULL,
        `event_date` VARCHAR(100) NULL,
        `status` ENUM('active', 'inactive') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 2. Promoters (Staff / Brand Ambassadors / Supervisors) Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `promoters` (
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
        INDEX(`outlet_id`),
        INDEX(`email`),
        INDEX(`staff_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 3. Participants Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `participants` (
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
        INDEX(`clan_id`),
        INDEX(`phone`),
        INDEX(`total_points`),
        INDEX(`registered_by_promoter_id`),
        INDEX(`outlet_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 4. Challenges Master Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `challenges` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `category` VARCHAR(50) NOT NULL,
        `title` VARCHAR(150) NOT NULL,
        `description` TEXT NOT NULL,
        `points` INT NOT NULL DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 5. Points Allocation Audit Log (Stores outlet where each point was allocated!)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `points_log` (
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
        INDEX(`participant_id`),
        INDEX(`promoter_id`),
        INDEX(`outlet_id`),
        INDEX(`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed default outlets if empty
    $chkOutlets = $pdo->query("SELECT COUNT(*) FROM `outlets`")->fetchColumn();
    if ($chkOutlets == 0) {
        $outlets = [
            ['Highland Arena - Victoria Island', 'Lagos', 'Plot 12, Adeola Odeku St, VI', 'Oct 18, 2026 - 6:00 PM'],
            ['The Highlander Lounge - Ikeja', 'Lagos', 'Isaac John St, GRA Ikeja', 'Oct 19, 2026 - 7:00 PM'],
            ['The Clan Vault - Lekki Phase 1', 'Lagos', 'Admiralty Way, Lekki', 'Oct 25, 2026 - 5:00 PM'],
            ['Braveheart Sports Bar - Abuja', 'Abuja', 'Wuse 2, Aminu Kano Crescent', 'Oct 26, 2026 - 6:30 PM'],
            ['Royal Stag Club - Port Harcourt', 'Port Harcourt', 'GRA Phase 2, PHC', 'Nov 02, 2026 - 6:00 PM'],
            ['TNH Grand Finale Pavilion - Eko Atlantic', 'Lagos', 'Eko Atlantic City Ground', 'Nov 15, 2026 - 4:00 PM (Grand Finale)'],
        ];
        $stmt = $pdo->prepare("INSERT INTO `outlets` (`name`, `city`, `address`, `event_date`) VALUES (?, ?, ?, ?)");
        foreach ($outlets as $out) {
            $stmt->execute($out);
        }
    }

    // Seed default challenges if empty
    $chkChallenges = $pdo->query("SELECT COUNT(*) FROM `challenges`")->fetchColumn();
    if ($chkChallenges == 0) {
        $challengesData = [
            ['Good humored', 'Football trivia', 'Answer 5 football related questions without failing.', 5],
            ['Unconventional', 'Sing your fav song', 'Sing your favorite song with bold energy', 5],
            ['Unconventional', 'Do squats', 'Do squats for 15 secs', 5],
            ['Bold', 'Stand on one foot', 'Stand on one foot for 15 secs', 5],
            ['Bold', 'Do a Freestyle Rap', 'Do a freestyle rap about William Lawson', 6],
            ['Bold', 'No blinking', 'Go a whole minute without blinking', 3],
            ['Bold', 'Lemon challenge', 'Participants to lick lemon without a facial expression', 3],
            ['Good humored', 'Spin the bottle', 'Spin the bottle and answer a question or do a challenge', 2],
            ['Good humored', 'Spell WL Backwards', 'Spell William Lawson backward without misspelling', 2],
            ['Good humored', 'Imitate a celebrity', 'Do an impression of a celebrity', 3],
            ['Unconventional', 'Spin around and walk on a straight line', 'Spin around 5 times and walk in a straight line without tripping', 3],
            ['Bold', 'Push-up', 'Highest number of push up within 15 secs', 5],
            ['Bold', 'Planking', 'Participants to maintain their body in a straight line for 60 secs', 5],
            ['Bold', 'Foot ball leg juggling', 'Juggle 50 times with your legs', 5],
            ['Good humored', 'Spin again', 'Spin the bottle again for another challenge', 0],
        ];

        $stmt = $pdo->prepare("INSERT INTO `challenges` (`category`, `title`, `description`, `points`) VALUES (?, ?, ?, ?)");
        foreach ($challengesData as $row) {
            $stmt->execute($row);
        }
    }

    // Seed default admin and sample promoters if empty
    $chkPromoters = $pdo->query("SELECT COUNT(*) FROM `promoters`")->fetchColumn();
    if ($chkPromoters == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $promoterPass = password_hash('promoter123', PASSWORD_DEFAULT);

        $pdo->prepare("INSERT INTO `promoters` (`staff_code`, `name`, `email`, `phone`, `password_hash`, `outlet_id`, `role`) VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute(['ADM-001', 'Campaign Lead (Admin)', 'admin@tnhng.com', '+2348011223344', $adminPass, 1, 'admin']);

        $samplePromoters = [
            ['PRM-101', 'Emeka Nnamdi', 'emeka@tnhng.com', '+2348022334455', $promoterPass, 1, 'promoter'],
            ['PRM-102', 'Folake Adebayo', 'folake@tnhng.com', '+2348033445566', $promoterPass, 2, 'promoter'],
            ['PRM-103', 'Chinedu Okeke', 'chinedu@tnhng.com', '+2348044556677', $promoterPass, 3, 'promoter'],
            ['PRM-104', 'Fatima Bello', 'fatima@tnhng.com', '+2348055667788', $promoterPass, 4, 'promoter'],
        ];
        $pStmt = $pdo->prepare("INSERT INTO `promoters` (`staff_code`, `name`, `email`, `phone`, `password_hash`, `outlet_id`, `role`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($samplePromoters as $p) {
            $pStmt->execute($p);
        }
    }

    // Seed sample participants if empty
    $chkParticipants = $pdo->query("SELECT COUNT(*) FROM `participants`")->fetchColumn();
    if ($chkParticipants == 0) {
        $sampleParticipants = [
            ['TNH-7701', 'Tunde "The Titan" Bakare', '+2348099887711', 'tunde.b@example.com', '1996-04-12', 1, 2, 105, 1, '@tundetitan'],
            ['TNH-6422', 'Amaka "Valkyrie" Eze', '+2348099887722', 'amaka.eze@example.com', '1998-08-23', 2, 3, 100, 1, '@amakaze'],
            ['TNH-5190', 'Oluwaseun "BoldSoul" Davies', '+2348099887733', 'seun.d@example.com', '1994-11-05', 1, 2, 85, 0, '@seun_davies'],
            ['TNH-4820', 'Ibrahim "Highlander" Sani', '+2348099887744', 'ibrahim.s@example.com', '1997-02-17', 4, 5, 76, 0, '@ibrahimsani'],
            ['TNH-3914', 'Blessing "IronWill" Okoro', '+2348099887755', 'blessing.o@example.com', '1999-06-30', 3, 4, 62, 0, '@blessing_o'],
            ['TNH-2845', 'David "Thunder" Adeleke', '+2348099887766', 'david.ad@example.com', '1995-09-14', 1, 2, 45, 0, '@davidadels'],
            ['TNH-1992', 'Zainab "Lioness" Lawal', '+2348099887777', 'zainab.l@example.com', '1998-12-01', 2, 3, 30, 0, '@zainab_lwl'],
        ];

        $insPart = $pdo->prepare("INSERT INTO `participants` (`clan_id`, `full_name`, `phone`, `email`, `dob`, `outlet_id`, `registered_by_promoter_id`, `total_points`, `is_qualified`, `instagram`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insLog = $pdo->prepare("INSERT INTO `points_log` (`participant_id`, `promoter_id`, `outlet_id`, `outlet_name`, `challenge_id`, `challenge_name`, `category`, `points`, `notes`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $outletsLookup = [
            1 => 'Highland Arena - Victoria Island',
            2 => 'The Highlander Lounge - Ikeja',
            3 => 'The Clan Vault - Lekki Phase 1',
            4 => 'Braveheart Sports Bar - Abuja',
        ];

        foreach ($sampleParticipants as $p) {
            $insPart->execute($p);
            $partId = $pdo->lastInsertId();
            $outletName = $outletsLookup[$p[5]] ?? 'Highland Arena - Victoria Island';

            // Seed audit logs
            $insLog->execute([$partId, $p[6], $p[5], $outletName, 5, 'Do a Freestyle Rap', 'Bold', 6, 'Hyped up the crowd with a bold freestyle!']);
            $insLog->execute([$partId, $p[6], $p[5], $outletName, 1, 'Football trivia', 'Good humored', 5, '5/5 questions correct']);
            if ($p[7] >= 100) {
                // Award points at a second outlet to showcase multi-outlet point collection!
                $secondOutletId = ($p[5] == 1) ? 2 : 1;
                $secondOutletName = $outletsLookup[$secondOutletId];
                $secondPromoterId = ($p[6] == 2) ? 3 : 2;
                $insLog->execute([$partId, $secondPromoterId, $secondOutletId, $secondOutletName, 12, 'Push-up', 'Bold', 5, '35 pushups in 15s - Crossed 100 points threshold!']);
            }
        }
    }
}
?>
