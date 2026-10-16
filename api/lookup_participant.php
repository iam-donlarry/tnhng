<?php
// api/lookup_participant.php - Robust Participant Lookup & History Service
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please check database credentials.']);
    exit;
}

$query = trim($_GET['query'] ?? ($_POST['query'] ?? ''));

if ($query === '') {
    echo json_encode(['success' => false, 'message' => 'Please provide a Clan ID, Database ID, Email, or Phone number to search.']);
    exit;
}

$cleanDigits = preg_replace('/[^0-9]/', '', $query);
$numId       = (is_numeric($query) && intval($query) > 0) ? intval($query) : 0;
$upperQuery  = strtoupper($query);
$lowerQuery  = strtolower($query);
$likeQuery   = '%' . $query . '%';

$participant = null;

// 1. Primary Lookup with Outlets & Promoters Details
try {
    $stmt = $pdo->prepare("SELECT p.*, 
                                  o.name AS registered_outlet_name, 
                                  o.city AS registered_outlet_city, 
                                  pr.name AS registered_by_name, 
                                  pr.staff_code AS registered_by_code
                           FROM participants p 
                           LEFT JOIN outlets o ON p.outlet_id = o.id 
                           LEFT JOIN promoters pr ON p.registered_by_promoter_id = pr.id 
                           WHERE p.clan_id = ? 
                              OR p.clan_id LIKE ? 
                              OR LOWER(p.email) = ? 
                              OR p.phone = ? 
                              OR (? != '' AND p.phone LIKE ?) 
                              OR (? > 0 AND p.id = ?)
                              OR p.full_name LIKE ?
                           ORDER BY (p.clan_id = ?) DESC, 
                                    (LOWER(p.email) = ?) DESC, 
                                    (p.id = ?) DESC, 
                                    p.id DESC 
                           LIMIT 1");
    $stmt->execute([
        $upperQuery,
        $likeQuery,
        $lowerQuery,
        $query,
        $cleanDigits, '%' . $cleanDigits,
        $numId, $numId,
        $likeQuery,
        $upperQuery,
        $lowerQuery,
        $numId
    ]);
    $participant = $stmt->fetch();
} catch (Throwable $e1) {
    error_log("Primary lookup error, attempting fallback: " . $e1->getMessage());
    // Fallback 1: Simple query without JOINs in case of production schema/column variance
    try {
        $stmt2 = $pdo->prepare("SELECT * FROM participants 
                                WHERE clan_id = ? 
                                   OR clan_id LIKE ? 
                                   OR LOWER(email) = ? 
                                   OR phone = ? 
                                   OR (? > 0 AND id = ?)
                                LIMIT 1");
        $stmt2->execute([$upperQuery, $likeQuery, $lowerQuery, $query, $numId, $numId]);
        $participant = $stmt2->fetch();
    } catch (Throwable $e2) {
        error_log("Fallback lookup error: " . $e2->getMessage());
        echo json_encode(['success' => false, 'message' => 'Query error: ' . $e2->getMessage()]);
        exit;
    }
}

if (!$participant) {
    echo json_encode([
        'success' => false, 
        'message' => 'No participant found matching "' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '". Please verify Clan ID, Email, Phone, or register them on-site.'
    ]);
    exit;
}

// Ensure essential participant fields have safe defaults
$participantId = intval($participant['id'] ?? 0);
$totalPoints   = intval($participant['total_points'] ?? 0);

// Self-heal: ensure participants.total_points reflects sum from points_log if points_log is higher
if ($participantId > 0) {
    try {
        $sumStmt = $pdo->prepare("SELECT COALESCE(SUM(points), 0) FROM points_log WHERE participant_id = ?");
        $sumStmt->execute([$participantId]);
        $loggedSum = intval($sumStmt->fetchColumn() ?: 0);
        if ($loggedSum > $totalPoints) {
            $totalPoints = $loggedSum;
            $participant['total_points'] = $totalPoints;
            $pdo->prepare("UPDATE participants SET total_points = ? WHERE id = ?")->execute([$totalPoints, $participantId]);
        }
    } catch (Throwable $eSum) {
        // Continue if points_log table is temporarily unreachable
    }
}

$isQualified   = ($totalPoints >= 100 || !empty($participant['is_qualified'])) ? 1 : 0;
$progressPercent = min(100, $totalPoints);
$pointsNeeded    = max(0, 100 - $totalPoints);

// 2. Fetch History Safely
$history = [];
$outletsVisited = [];
if ($participantId > 0) {
    try {
        $logStmt = $pdo->prepare("SELECT pl.*, 
                                         pr.name AS promoter_name, 
                                         pr.staff_code AS promoter_staff_code 
                                  FROM points_log pl 
                                  LEFT JOIN promoters pr ON pl.promoter_id = pr.id 
                                  WHERE pl.participant_id = ? 
                                  ORDER BY pl.created_at DESC, pl.id DESC");
        $logStmt->execute([$participantId]);
        $history = $logStmt->fetchAll() ?: [];
    } catch (Throwable $eHist1) {
        // Fallback: simple query on points_log without JOIN
        try {
            $logStmt2 = $pdo->prepare("SELECT * FROM points_log WHERE participant_id = ? ORDER BY id DESC");
            $logStmt2->execute([$participantId]);
            $history = $logStmt2->fetchAll() ?: [];
        } catch (Throwable $eHist2) {
            $history = [];
        }
    }

    foreach ($history as $h) {
        if (!empty($h['outlet_name']) && !in_array($h['outlet_name'], $outletsVisited)) {
            $outletsVisited[] = $h['outlet_name'];
        }
    }
}

// 3. Compute Campaign Rank Safely (Matching Leaderboard Order Exactly: total_points DESC, created_at ASC, id ASC)
$rank = 1;
if ($participantId > 0) {
    $rankFound = false;

    // Method A: Accurate ROW_NUMBER() window function (MySQL 8.0+, MariaDB 10.2+)
    try {
        $rankStmt = $pdo->prepare("SELECT rank_val FROM (
            SELECT id, ROW_NUMBER() OVER (
                ORDER BY COALESCE(total_points, 0) DESC, 
                         COALESCE(created_at, '9999-12-31 23:59:59') ASC, 
                         id ASC
            ) AS rank_val 
            FROM participants
        ) ranked WHERE id = ?");
        $rankStmt->execute([$participantId]);
        $rankVal = $rankStmt->fetchColumn();
        if ($rankVal !== false && $rankVal !== null && intval($rankVal) > 0) {
            $rank = intval($rankVal);
            $rankFound = true;
        }
    } catch (Throwable $eRankWindow) {
        $rankFound = false;
    }

    // Method B: Tie-breaker count fallback (works on MySQL 5.5, 5.6, 5.7, MariaDB, SQLite)
    if (!$rankFound) {
        try {
            $createdAt = !empty($participant['created_at']) ? $participant['created_at'] : '9999-12-31 23:59:59';
            $rankStmt2 = $pdo->prepare("SELECT COUNT(*) + 1 AS rank FROM participants 
                WHERE COALESCE(total_points, 0) > ? 
                   OR (COALESCE(total_points, 0) = ? AND COALESCE(created_at, '9999-12-31 23:59:59') < ?)
                   OR (COALESCE(total_points, 0) = ? AND COALESCE(created_at, '9999-12-31 23:59:59') = ? AND id < ?)");
            $rankStmt2->execute([$totalPoints, $totalPoints, $createdAt, $totalPoints, $createdAt, $participantId]);
            $rankVal2 = $rankStmt2->fetchColumn();
            if ($rankVal2 !== false && $rankVal2 !== null && intval($rankVal2) > 0) {
                $rank = intval($rankVal2);
            }
        } catch (Throwable $eRankCount) {
            error_log("Failed to compute rank count: " . $eRankCount->getMessage());
            $rank = 1;
        }
    }
}

// 4. Return Output JSON
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
