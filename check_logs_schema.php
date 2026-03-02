<?php
require_once __DIR__ . '/config/database.php';
$stmt = $pdo->prepare("DESCRIBE activity_logs");
$stmt->execute();
print_r($stmt->fetchAll());
