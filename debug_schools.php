<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=school_system;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$out = [];

$stmt = $pdo->query('DESCRIBE schools');
$cols = $stmt->fetchAll();
$out['schools_columns'] = $cols;

$stmt = $pdo->query('SELECT id, name, status FROM schools LIMIT 5');
$out['schools_data'] = $stmt->fetchAll();

// Test update
$test_results = [];
if (!empty($out['schools_data'])) {
    $id = $out['schools_data'][0]['id'];
    try {
        $stmt = $pdo->prepare('UPDATE schools SET status = ? WHERE id = ?');
        $stmt->execute(['inactive', $id]);
        $test_results['update'] = 'SUCCESS';
        $stmt->execute(['active', $id]); // revert
    } catch (Exception $e) {
        $test_results['update'] = 'FAIL: ' . $e->getMessage();
    }
}

// FK constraints
$stmt = $pdo->query("SELECT TABLE_NAME, DELETE_RULE, CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE REFERENCED_TABLE_NAME = 'schools' AND CONSTRAINT_SCHEMA = 'school_system'");
$out['fk_constraints'] = $stmt->fetchAll();

// Activity logs columns
$stmt = $pdo->query('DESCRIBE activity_logs');
$out['activity_logs_columns'] = $stmt->fetchAll();

$out['test_results'] = $test_results;

file_put_contents(__DIR__ . '/debug_result.json', json_encode($out, JSON_PRETTY_PRINT));
echo "DONE";
