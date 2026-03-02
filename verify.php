<?php
// verify.php
require_once __DIR__ . '/config/database.php';

echo "Testing Database Connection...\n";
try {
    $stmt = $pdo->query("SELECT 1");
    echo "Database Connection: OK\n";
} catch (Exception $e) {
    echo "Database Connection: FAILED - " . $e->getMessage() . "\n";
    exit(1);
}

echo "Testing Roles Table...\n";
$roles = $pdo->query("SELECT name FROM roles")->fetchAll(PDO::FETCH_COLUMN);
echo "Roles: " . implode(", ", $roles) . "\n";

echo "Testing Theme Config...\n";
require_once __DIR__ . '/config/theme.php';
echo "Theme Primary Color: " . $theme['primary'] . "\n";

echo "Syntax check for key files...\n";
$files = [
    'login.php',
    'register.php',
    'dashboard/index.php',
    'api/auth.php',
    'api/crud.php'
];

foreach ($files as $file) {
    $php_path = 'C:\xampp\php\php.exe';
    $output = shell_exec("$php_path -l " . escapeshellarg($file));
    echo $output;
    if (strpos($output, 'No syntax errors detected') === false) {
        echo "SYNTAX ERROR IN $file\n";
        exit(1);
    }
}

echo "Verification COMPLETE\n";
