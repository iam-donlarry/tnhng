<?php
// api/admin_actions.php - Admin operations, promoter creation, audit logs & CSV export
session_start();
require_once __DIR__ . '/../config/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// Helper to check admin/supervisor authorization
function checkAuth($allowedRoles = ['admin', 'supervisor']) {
    if (empty($_SESSION['user']) || !in_array($_SESSION['user']['role'], $allowedRoles)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin access required.']);
        exit;
    }
}

// 1. Export CSV (Handles download directly)
if ($action === 'export_points_csv') {
    checkAuth(['admin', 'supervisor']);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=tnh_points_allocation_log_' . date('Y-m-d_His') . '.csv');

    $output = fopen('php://output', 'w');
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Headers
    fputcsv($output, [
        'Log ID',
        'Timestamp',
        'Clan ID',
        'Participant Name',
        'Participant Phone',
        'Outlet Where Allocated',
        'Allocated By (Staff)',
        'Staff Code',
        'Challenge Category',
        'Challenge Name',
        'Points Awarded',
        'Notes'
    ]);

    $query = "SELECT pl.id, pl.created_at, p.clan_id, p.full_name, p.phone, pl.outlet_name, 
                     pr.name AS promoter_name, pr.staff_code, pl.category, pl.challenge_name, 
                     pl.points, pl.notes
              FROM points_log pl
              LEFT JOIN participants p ON pl.participant_id = p.id
              LEFT JOIN promoters pr ON pl.promoter_id = pr.id
              ORDER BY pl.created_at DESC";
    $stmt = $pdo->query($query);
    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            $row['id'],
            $row['created_at'],
            $row['clan_id'],
            $row['full_name'],
            $row['phone'],
            $row['outlet_name'],
            $row['promoter_name'] ?: 'System',
            $row['staff_code'] ?: 'N/A',
            $row['category'],
            $row['challenge_name'],
            $row['points'],
            $row['notes']
        ]);
    }
    fclose($output);
    exit;
}

if ($action === 'export_participants_csv') {
    checkAuth(['admin', 'supervisor']);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=tnh_1pd_participants_' . date('Y-m-d_His') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, [
        'Participant ID',
        'Clan ID',
        'Full Name',
        'Phone Number',
        'Email Address',
        'Date of Birth',
        'Instagram Handle',
        'Registered Outlet',
        'Registered By (Promoter)',
        'Promoter Code',
        'Total Points',
        'Qualified for Grand Finale',
        'Registration Date'
    ]);

    $query = "SELECT p.id, p.clan_id, p.full_name, p.phone, p.email, p.dob, p.instagram,
                     o.name AS registered_outlet, pr.name AS registered_by_name, pr.staff_code,
                     p.total_points, p.is_qualified, p.created_at
              FROM participants p
              LEFT JOIN outlets o ON p.outlet_id = o.id
              LEFT JOIN promoters pr ON p.registered_by_promoter_id = pr.id
              ORDER BY p.total_points DESC, p.created_at DESC";
    $stmt = $pdo->query($query);
    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            $row['id'],
            $row['clan_id'],
            $row['full_name'],
            $row['phone'],
            $row['email'],
            $row['dob'],
            $row['instagram'],
            $row['registered_outlet'] ?: 'Direct / Web',
            $row['registered_by_name'] ?: 'Self-Registered (Web)',
            $row['staff_code'] ?: 'N/A',
            $row['total_points'],
            $row['is_qualified'] ? 'YES (100+ Pts)' : 'NO',
            $row['created_at']
        ]);
    }
    fclose($output);
    exit;
}

// All remaining actions return JSON
header('Content-Type: application/json');

// 2. Dashboard KPIs & Overview
if ($action === 'dashboard_metrics') {
    checkAuth(['admin', 'supervisor']);

    $totalParticipants = $pdo->query("SELECT COUNT(*) FROM participants")->fetchColumn();
    $totalPoints = $pdo->query("SELECT COALESCE(SUM(points), 0) FROM points_log")->fetchColumn();
    $qualifiedCount = $pdo->query("SELECT COUNT(*) FROM participants WHERE total_points >= 100")->fetchColumn();
    $totalPromoters = $pdo->query("SELECT COUNT(*) FROM promoters WHERE role != 'admin'")->fetchColumn();
    $totalOutlets = $pdo->query("SELECT COUNT(*) FROM outlets WHERE status = 'active'")->fetchColumn();

    // Top 5 Promoters by participants registered & points awarded
    $topPromoters = $pdo->query("SELECT pr.id, pr.name, pr.staff_code, o.name AS outlet_name,
        (SELECT COUNT(*) FROM participants WHERE registered_by_promoter_id = pr.id) AS registered_count,
        (SELECT COALESCE(SUM(points), 0) FROM points_log WHERE promoter_id = pr.id) AS total_points_allocated
        FROM promoters pr
        LEFT JOIN outlets o ON pr.outlet_id = o.id
        WHERE pr.role != 'admin'
        ORDER BY registered_count DESC, total_points_allocated DESC
        LIMIT 5")->fetchAll();

    // Outlets Activity breakdown
    $outletStats = $pdo->query("SELECT pl.outlet_name, 
        COUNT(DISTINCT pl.participant_id) AS unique_participants,
        COUNT(pl.id) AS total_games_played,
        SUM(pl.points) AS total_points_allocated
        FROM points_log pl
        GROUP BY pl.outlet_name
        ORDER BY total_points_allocated DESC")->fetchAll();

    echo json_encode([
        'success' => true,
        'metrics' => [
            'total_participants' => intval($totalParticipants),
            'total_points' => intval($totalPoints),
            'qualified_count' => intval($qualifiedCount),
            'total_promoters' => intval($totalPromoters),
            'total_outlets' => intval($totalOutlets),
        ],
        'top_promoters' => $topPromoters,
        'outlet_stats' => $outletStats
    ]);
    exit;
}

// 3. Create New Promoter / Registrar
if ($action === 'create_promoter') {
    checkAuth(['admin']);

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $outletId = !empty($_POST['outlet_id']) ? intval($_POST['outlet_id']) : null;
    $role = in_array($_POST['role'] ?? '', ['promoter', 'supervisor']) ? $_POST['role'] : 'promoter';

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields (Name, Email, Phone, Password) are required.']);
        exit;
    }

    // Check duplicate email
    $chk = $pdo->prepare("SELECT id FROM promoters WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetch()) {
        echo json_encode(['success' => false, 'message' => 'A staff member with this email already exists.']);
        exit;
    }

    // Auto-generate Staff Code (e.g. PRM-201)
    $nextNum = intval($pdo->query("SELECT MAX(id) FROM promoters")->fetchColumn()) + 101;
    $staffCode = 'PRM-' . $nextNum;

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO promoters (staff_code, name, email, phone, password_hash, outlet_id, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$staffCode, $name, $email, $phone, $passwordHash, $outletId, $role]);
        $newId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => "Promoter {$name} created successfully with code {$staffCode}.",
            'promoter' => [
                'id' => $newId,
                'staff_code' => $staffCode,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => $role
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error creating promoter: ' . $e->getMessage()]);
    }
    exit;
}

// 4. List All Promoters with Live Performance Metrics
if ($action === 'list_promoters') {
    checkAuth(['admin', 'supervisor']);

    $stmt = $pdo->query("SELECT pr.id, pr.staff_code, pr.name, pr.email, pr.phone, pr.role, pr.status, pr.created_at,
                                o.name AS outlet_name, o.city AS outlet_city,
                                (SELECT COUNT(*) FROM participants WHERE registered_by_promoter_id = pr.id) AS participants_registered,
                                (SELECT COUNT(*) FROM points_log WHERE promoter_id = pr.id) AS points_logged_count,
                                (SELECT COALESCE(SUM(points), 0) FROM points_log WHERE promoter_id = pr.id) AS total_points_allocated
                         FROM promoters pr
                         LEFT JOIN outlets o ON pr.outlet_id = o.id
                         ORDER BY pr.role ASC, pr.created_at DESC");
    $promoters = $stmt->fetchAll();

    echo json_encode(['success' => true, 'promoters' => $promoters]);
    exit;
}

// 5. Toggle Promoter Active/Inactive
if ($action === 'toggle_promoter_status') {
    checkAuth(['admin']);
    $promoterId = intval($_POST['promoter_id'] ?? 0);
    $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';

    $stmt = $pdo->prepare("UPDATE promoters SET status = ? WHERE id = ?");
    $stmt->execute([$status, $promoterId]);

    echo json_encode(['success' => true, 'message' => "Promoter status updated to {$status}."]);
    exit;
}

// 6. Audit Trail: Points Log Table with filters
if ($action === 'get_points_log') {
    checkAuth(['admin', 'supervisor']);

    $outletFilter = !empty($_GET['outlet_id']) ? intval($_GET['outlet_id']) : null;
    $promoterFilter = !empty($_GET['promoter_id']) ? intval($_GET['promoter_id']) : null;
    $search = trim($_GET['search'] ?? '');
    $limit = !empty($_GET['limit']) ? min(200, intval($_GET['limit'])) : 100;

    $where = ["1=1"];
    $params = [];

    if ($outletFilter) {
        $where[] = "pl.outlet_id = ?";
        $params[] = $outletFilter;
    }
    if ($promoterFilter) {
        $where[] = "pl.promoter_id = ?";
        $params[] = $promoterFilter;
    }
    if (!empty($search)) {
        $where[] = "(p.clan_id LIKE ? OR p.full_name LIKE ? OR pl.outlet_name LIKE ? OR pr.name LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    $whereSql = implode(' AND ', $where);

    $stmt = $pdo->prepare("SELECT pl.*, p.clan_id, p.full_name AS participant_name, p.phone AS participant_phone,
                                  pr.name AS promoter_name, pr.staff_code
                           FROM points_log pl
                           LEFT JOIN participants p ON pl.participant_id = p.id
                           LEFT JOIN promoters pr ON pl.promoter_id = pr.id
                           WHERE {$whereSql}
                           ORDER BY pl.created_at DESC
                           LIMIT {$limit}");
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    echo json_encode(['success' => true, 'logs' => $logs]);
    exit;
}

// 7. Manage Outlets: Add or list
if ($action === 'list_outlets') {
    $outlets = $pdo->query("SELECT * FROM outlets ORDER BY id ASC")->fetchAll();
    echo json_encode(['success' => true, 'outlets' => $outlets]);
    exit;
}

if ($action === 'create_outlet') {
    checkAuth(['admin']);
    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');

    if (empty($name) || empty($city)) {
        echo json_encode(['success' => false, 'message' => 'Outlet Name and City are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO outlets (name, city, address, event_date, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->execute([$name, $city, $address, $eventDate]);

    echo json_encode(['success' => true, 'message' => 'Outlet added successfully.', 'id' => $pdo->lastInsertId()]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
?>
