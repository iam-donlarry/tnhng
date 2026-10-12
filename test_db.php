<?php
require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();
if ($pdo) {
    echo "CONNECTED_SUCCESSFULLY\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLES: " . implode(', ', $tables) . "\n";
    $cnt = $pdo->query("SELECT COUNT(*) FROM participants")->fetchColumn();
    echo "PARTICIPANTS: " . $cnt . "\n";
} else {
    echo "FAILED_TO_CONNECT\n";
}
