<?php
require_once __DIR__ . '/config/database.php';
echo "DB in test_status: " . $pdo->query("SELECT DATABASE()")->fetchColumn() . "\n";
$id = 2;
$pdo->exec("UPDATE schools SET status = 'inactive' WHERE id = 1"); // Try updating ID 1 instead
echo "DB Check for ID 1: " . $pdo->query("SELECT status FROM schools WHERE id = 1")->fetchColumn() . "\n";
