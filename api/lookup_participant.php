<?php
// api/lookup_participant.php - Lookup participant and their full points log
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$query = trim($_GET['query'] ?? ($_POST['query'] ?? ''));

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a Clan ID, Email, or Phone number to search.']);
    exit;
}

$cleanQuery = preg_replace('/[^0-9+]/', '', $query);

$stmt = $pdo->prepare("SELECT p.*, o.name AS registered_outlet_name, o.city AS registered_outlet_city, 
                              pr.name AS registered_by_name, pr.staff_code AS registered_by_code
                       FROM participants p 
                       LEFT JOIN outlets o ON p.outlet_id = o.id 
                       LEFT JOIN promoters pr ON p.registered_by_promoter_id = pr.id 
                       WHERE p.clan_id = ? OR LOWER(p.email) = ? OR p.phone = ? OR p.phone = ?
                       LIMIT 1");
$stmt->execute([strtoupper($query), strtolower($query), $query, $cleanQuery]);
$participant = $stmt->fetch();

if (!$participant) {
    echo json_encode(['success' => false, 'message' => 'Participant not found. Please verify Clan ID, Email, or Phone number.']);
    exit;
}

// Fetch complete points allocation history with outlet & promoter details
$logStmt = $pdo->prepare("SELECT pl.*, pr.name AS promoter_name, pr.staff_code AS promoter_staff_code 
                          FROM points_log pl 
                          LEFT JOIN promoters pr ON pl.promoter_id = pr.id 
                          WHERE pl.participant_id = ? 
                          ORDER BY pl.created_at DESC");
$logStmt->execute([$participant['id']]);
$history = $logStmt->fetchAll();

// Calculate stats
$totalPoints = intval($participant['total_points']);
$isQualified = ($totalPoints >= 100) ? 1 : 0;
$progressPercent = min(100, $totalPoints);
$pointsNeeded = max(0, 100 - $totalPoints);

// Count distinct outlets visited
$outletsVisited = [];
foreach ($history as $h) {
    if (!empty($h['outlet_name']) && !in_array($h['outlet_name'], $outletsVisited)) {
        $outletsVisited[] = $h['outlet_name'];
    }
}

// Compute rank in the entire campaign
$rankStmt = $pdo->prepare("SELECT COUNT(*) + 1 AS rank FROM participants WHERE total_points > ?");
$rankStmt->execute([$totalPoints]);
$rank = $rankStmt->fetchColumn();

echo json_encode([
    'success' => true,
    'participant' => $participant,
    'stats' => [
        'total_points' => $totalPoints,
        'rank' => $rank,
        'is_qualified' => $isQualified,
        'progress_percent' => $progressPercent,
        'points_needed' => $pointsNeeded,
        'outlets_visited_count' => count($outletsVisited),
        'outlets_visited' => $outletsVisited,
        'total_challenges_played' => count($history)
    ],
    'history' => $history
]);
?>
