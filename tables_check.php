<?php
require_once __DIR__ . '/config/database.php';
$stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
print_r($stmt->fetchAll());
$stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
print_r($stmt->fetchAll());
