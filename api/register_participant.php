<?php
// api/register_participant.php - Register new Clan participant
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$outletId = !empty($_POST['outlet_id']) ? intval($_POST['outlet_id']) : null;
$instagram = trim($_POST['instagram'] ?? '');

// Check promoter context if submitted by logged-in promoter
$promoterId = null;
if (!empty($_SESSION['user']) && $_SESSION['user']['role'] !== 'participant') {
    $promoterId = $_SESSION['user']['id'];
    if (empty($outletId)) {
        $outletId = $_SESSION['user']['outlet_id'];
    }
} elseif (!empty($_POST['registered_by_promoter_id'])) {
    $promoterId = intval($_POST['registered_by_promoter_id']);
}

// Basic Validation
if (empty($fullName) || empty($phone) || empty($email) || empty($dob)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Phone, Email, DOB).']);
    exit;
}

// Verify 18+ Age Gate
$birthDate = new DateTime($dob);
$today = new DateTime();
$age = $today->diff($birthDate)->y;
if ($age < 18) {
    echo json_encode(['success' => false, 'message' => 'You must be at least 18 years old to join The Next Highlandah Clan.']);
    exit;
}

// Normalize phone format (e.g. remove spaces/dashes)
$cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

// Check if phone already registered
$checkStmt = $pdo->prepare("SELECT p.*, o.name AS outlet_name, pr.name AS registered_by_name 
                            FROM participants p 
                            LEFT JOIN outlets o ON p.outlet_id = o.id 
                            LEFT JOIN promoters pr ON p.registered_by_promoter_id = pr.id 
                            WHERE p.phone = ? OR p.phone = ?");
$checkStmt->execute([$cleanPhone, $phone]);
$existing = $checkStmt->fetch();

if ($existing) {
    echo json_encode([
        'success' => true,
        'is_existing' => true,
        'message' => 'Welcome back! You are already part of The Clan.',
        'participant' => $existing
    ]);
    exit;
}

// Generate unique Clan ID (Format: TNH-XXXX)
do {
    $clanId = 'TNH-' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    $chkClan = $pdo->prepare("SELECT id FROM participants WHERE clan_id = ?");
    $chkClan->execute([$clanId]);
} while ($chkClan->fetch());

try {
    $stmt = $pdo->prepare("INSERT INTO participants 
        (clan_id, full_name, phone, email, dob, outlet_id, registered_by_promoter_id, total_points, is_qualified, instagram) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?)");
    $stmt->execute([
        $clanId,
        $fullName,
        $cleanPhone,
        $email,
        $dob,
        $outletId,
        $promoterId,
        $instagram
    ]);

    $newId = $pdo->lastInsertId();

    // Fetch complete newly created record
    $fetchStmt = $pdo->prepare("SELECT p.*, o.name AS outlet_name, pr.name AS registered_by_name 
                                FROM participants p 
                                LEFT JOIN outlets o ON p.outlet_id = o.id 
                                LEFT JOIN promoters pr ON p.registered_by_promoter_id = pr.id 
                                WHERE p.id = ?");
    $fetchStmt->execute([$newId]);
    $participant = $fetchStmt->fetch();

    echo json_encode([
        'success' => true,
        'is_existing' => false,
        'message' => 'Welcome to The Clan! Your Highlandah Pass is ready.',
        'participant' => $participant
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
}
?>
