<?php
require_once __DIR__ . '/config/database.php';
$id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? 'active';

if ($id) {
    $stmt = $pdo->prepare("UPDATE schools SET status = ? WHERE id = ?");
    $res = $stmt->execute([$status, $id]);
    echo $res ? "Success: ID $id set to $status" : "Failed";
} else {
    echo "Provide id and status via GET";
}
