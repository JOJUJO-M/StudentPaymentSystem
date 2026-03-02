<?php
require_once __DIR__ . '/config/database.php';
$stmt = $pdo->query("SELECT id, name, status FROM schools");
print_r($stmt->fetchAll());
