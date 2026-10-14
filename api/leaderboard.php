<?php
// api/leaderboard.php - Public and Live Leaderboard
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$outletFilter = !empty($_GET['outlet_id']) ? intval($_GET['outlet_id']) : null;
$search = trim($_GET['search'] ?? '');
$limit = !empty($_GET['limit']) ? min(100, intval($_GET['limit'])) : 50;

$whereClauses = ["1=1"];
$params = [];

if ($outletFilter) {
    // Participants who either registered at or have points at this outlet
    $whereClauses[] = "(p.outlet_id = ? OR p.id IN (SELECT DISTINCT participant_id FROM points_log WHERE outlet_id = ?))";
    $params[] = $outletFilter;
    $params[] = $outletFilter;
}

if (!empty($search)) {
    $whereClauses[] = "(p.clan_id LIKE ? OR p.email LIKE ? OR p.full_name LIKE ? OR p.phone LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$whereSql = implode(' AND ', $whereClauses);

$query = "SELECT p.id, p.clan_id, p.full_name, p.phone, p.email, p.total_points, p.is_qualified, p.created_at,
                 o.name AS registered_outlet, o.city AS registered_city,
                 pr.name AS registered_by_name, pr.staff_code AS registered_by_code,
                 (SELECT COUNT(DISTINCT pl.outlet_name) FROM points_log pl WHERE pl.participant_id = p.id) AS outlets_visited_count,
                 (SELECT COUNT(*) FROM points_log pl WHERE pl.participant_id = p.id) AS challenges_count
          FROM participants p
          LEFT JOIN outlets o ON p.outlet_id = o.id
          LEFT JOIN promoters pr ON p.registered_by_promoter_id = pr.id
          WHERE {$whereSql}
          ORDER BY p.total_points DESC, p.created_at ASC
          LIMIT {$limit}";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Add rank and masked name option for privacy if needed
$rankedList = [];
$rank = 1;
foreach ($rows as $row) {
    $row['rank'] = $rank++;
    $row['is_qualified'] = intval($row['total_points']) >= 100 ? 1 : 0;
    
    // Mask majority of name for public privacy (e.g. "Tu**** B*****")
    $nameParts = explode(' ', trim($row['full_name']));
    $maskedParts = [];
    foreach ($nameParts as $part) {
        $len = strlen($part);
        if ($len <= 2) {
            $maskedParts[] = substr($part, 0, 1) . '*';
        } else {
            $keep = min(2, max(1, intval($len / 3)));
            $maskedParts[] = substr($part, 0, $keep) . str_repeat('*', max(3, $len - $keep));
        }
    }
    $row['display_name'] = implode(' ', $maskedParts);
    $rankedList[] = $row;
}

// Stats summary
$statsQuery = $pdo->query("SELECT 
    COUNT(*) AS total_participants,
    SUM(total_points) AS total_points_distributed,
    SUM(CASE WHEN total_points >= 100 THEN 1 ELSE 0 END) AS total_qualified_finalists,
    MAX(total_points) AS highest_score
FROM participants")->fetch();

echo json_encode([
    'success' => true,
    'leaderboard' => $rankedList,
    'stats' => [
        'total_participants' => intval($statsQuery['total_participants'] ?? 0),
        'total_points_distributed' => intval($statsQuery['total_points_distributed'] ?? 0),
        'total_qualified_finalists' => intval($statsQuery['total_qualified_finalists'] ?? 0),
        'highest_score' => intval($statsQuery['highest_score'] ?? 0),
    ]
]);
?>
