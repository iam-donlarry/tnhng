<?php
// api/auth.php - Session and Authentication for Promoters and Admins
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please check MySQL service.']);
    exit;
}

if ($action === 'login') {
    $identifier = trim($_POST['identifier'] ?? ''); // Email or Staff Code
    $password = trim($_POST['password'] ?? '');

    if (empty($identifier) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please provide staff code/email and password.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT p.*, o.name AS outlet_name, o.city AS outlet_city 
                           FROM promoters p 
                           LEFT JOIN outlets o ON p.outlet_id = o.id 
                           WHERE (p.email = ? OR p.staff_code = ?) AND p.status = 'active' LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $promoter = $stmt->fetch();

    if ($promoter && password_verify($password, $promoter['password_hash'])) {
        // Allow promoter to override current outlet for the shift if passed
        $currentOutletId = !empty($_POST['current_outlet_id']) ? intval($_POST['current_outlet_id']) : $promoter['outlet_id'];
        
        $outletStmt = $pdo->prepare("SELECT name, city FROM outlets WHERE id = ?");
        $outletStmt->execute([$currentOutletId]);
        $currentOutlet = $outletStmt->fetch();

        $_SESSION['user'] = [
            'id' => $promoter['id'],
            'staff_code' => $promoter['staff_code'],
            'name' => $promoter['name'],
            'email' => $promoter['email'],
            'role' => $promoter['role'],
            'outlet_id' => $currentOutletId,
            'outlet_name' => $currentOutlet['name'] ?? $promoter['outlet_name'] ?? 'General Outlet',
            'outlet_city' => $currentOutlet['city'] ?? $promoter['outlet_city'] ?? 'Lagos',
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => $_SESSION['user']
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid credentials or inactive account.']);
    exit;
}

if ($action === 'check') {
    if (!empty($_SESSION['user'])) {
        echo json_encode(['success' => true, 'authenticated' => true, 'user' => $_SESSION['user']]);
    } else {
        echo json_encode(['success' => true, 'authenticated' => false]);
    }
    exit;
}

if ($action === 'switch_outlet') {
    if (empty($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
        exit;
    }
    $newOutletId = intval($_POST['outlet_id'] ?? 0);
    $outletStmt = $pdo->prepare("SELECT name, city FROM outlets WHERE id = ?");
    $outletStmt->execute([$newOutletId]);
    $outlet = $outletStmt->fetch();
    if ($outlet) {
        $_SESSION['user']['outlet_id'] = $newOutletId;
        $_SESSION['user']['outlet_name'] = $outlet['name'];
        $_SESSION['user']['outlet_city'] = $outlet['city'];
        echo json_encode(['success' => true, 'message' => 'Outlet switched successfully', 'user' => $_SESSION['user']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid outlet ID.']);
    }
    exit;
}

if ($action === 'logout') {
    unset($_SESSION['user']);
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
