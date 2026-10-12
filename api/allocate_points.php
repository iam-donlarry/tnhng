<?php
// api/allocate_points.php - Award points to a participant at an outlet
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Ensure caller is an authenticated promoter or supervisor
if (empty($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in as a Brand Ambassador or Supervisor.']);
    exit;
}

$promoterId = $_SESSION['user']['id'];
$participantId = intval($_POST['participant_id'] ?? 0);
$clanId = trim($_POST['clan_id'] ?? '');

// If clan_id or identifier is provided without participant_id, resolve it
if ($participantId === 0 && !empty($clanId)) {
    $cStmt = $pdo->prepare("SELECT id FROM participants WHERE clan_id = ? OR LOWER(email) = ? OR phone = ?");
    $cStmt->execute([strtoupper($clanId), strtolower($clanId), $clanId]);
    $participantId = intval($cStmt->fetchColumn());
}

if ($participantId === 0) {
    echo json_encode(['success' => false, 'message' => 'Participant not found. Please provide valid Participant ID or Clan ID.']);
    exit;
}

$challengeId = !empty($_POST['challenge_id']) ? intval($_POST['challenge_id']) : null;
$challengeName = trim($_POST['challenge_name'] ?? '');
$category = trim($_POST['category'] ?? 'General');
$points = intval($_POST['points'] ?? 0);
$notes = trim($_POST['notes'] ?? '');

// Resolve challenge details from official scorecard - strictly non-editable
if ($challengeId) {
    $chStmt = $pdo->prepare("SELECT * FROM challenges WHERE id = ?");
    $chStmt->execute([$challengeId]);
    $chData = $chStmt->fetch();
    if ($chData) {
        $challengeName = $chData['title'];
        $category = $chData['category'];
        // Points are locked strictly to official scorecard value
        $points = intval($chData['points']);
    }
}

if (empty($challengeName)) {
    echo json_encode(['success' => false, 'message' => 'Please select or specify a challenge.']);
    exit;
}

if ($points <= 0) {
    echo json_encode(['success' => false, 'message' => 'Points to allocate must be greater than 0.']);
    exit;
}

// Determine Outlet where points are being allocated (MANDATORY REQUIREMENT)
$outletId = !empty($_POST['outlet_id']) ? intval($_POST['outlet_id']) : ($_SESSION['user']['outlet_id'] ?? null);
$outletName = trim($_POST['outlet_name'] ?? '');

if (empty($outletName) && $outletId) {
    $outStmt = $pdo->prepare("SELECT name FROM outlets WHERE id = ?");
    $outStmt->execute([$outletId]);
    $outletName = $outStmt->fetchColumn() ?: 'On-Site Outlet';
}

if (empty($outletName)) {
    $outletName = 'Highland Arena';
}

try {
    $pdo->beginTransaction();

    // 1. Insert into points_log with outlet details
    $insStmt = $pdo->prepare("INSERT INTO points_log 
        (participant_id, promoter_id, outlet_id, outlet_name, challenge_id, challenge_name, category, points, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insStmt->execute([
        $participantId,
        $promoterId,
        $outletId,
        $outletName,
        $challengeId,
        $challengeName,
        $category,
        $points,
        $notes
    ]);
    $logId = $pdo->lastInsertId();

    // 2. Fetch current points and update
    $selPart = $pdo->prepare("SELECT total_points, is_qualified, full_name, clan_id FROM participants WHERE id = ? FOR UPDATE");
    $selPart->execute([$participantId]);
    $currentPart = $selPart->fetch();

    if (!$currentPart) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Participant record does not exist.']);
        exit;
    }

    $oldPoints = intval($currentPart['total_points']);
    $newPoints = $oldPoints + $points;
    $wasQualified = intval($currentPart['is_qualified']);
    $isQualifiedNow = ($newPoints >= 100) ? 1 : $wasQualified;
    $justQualified = ($wasQualified === 0 && $isQualifiedNow === 1);

    $updStmt = $pdo->prepare("UPDATE participants SET total_points = ?, is_qualified = ? WHERE id = ?");
    $updStmt->execute([$newPoints, $isQualifiedNow, $participantId]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => "+{$points} points allocated successfully at {$outletName}!",
        'log_id' => $logId,
        'allocated' => [
            'points' => $points,
            'challenge' => $challengeName,
            'category' => $category,
            'outlet_name' => $outletName,
            'promoter_name' => $_SESSION['user']['name']
        ],
        'participant' => [
            'id' => $participantId,
            'clan_id' => $currentPart['clan_id'],
            'full_name' => $currentPart['full_name'],
            'previous_points' => $oldPoints,
            'total_points' => $newPoints,
            'is_qualified' => $isQualifiedNow,
            'just_qualified' => $justQualified,
            'points_needed' => max(0, 100 - $newPoints)
        ]
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Failed to allocate points: ' . $e->getMessage()]);
}
?>
