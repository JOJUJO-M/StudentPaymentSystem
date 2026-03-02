<?php
require_once __DIR__ . '/config/database.php';

$id = 2; // Fixed ID for test
$status = 'inactive';

$rows = $pdo->exec("UPDATE schools SET status = '$status' WHERE id = $id");
echo "Rows updated: $rows.\n";

$check = $pdo->query("SELECT status FROM schools WHERE id = $id")->fetchColumn();
echo "Status now in DB: $check.\n";
