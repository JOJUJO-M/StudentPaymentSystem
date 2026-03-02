<?php
require_once __DIR__ . '/config/database.php';

try {
    // 1. Ensure columns exist and have correct defaults
    // reg_number
    $stmt = $pdo->query("SHOW COLUMNS FROM schools LIKE 'reg_number'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE schools ADD COLUMN reg_number VARCHAR(50) NOT NULL DEFAULT 'S000' AFTER name");
        echo "Added reg_number column.\n";
    }

    // status
    $stmt = $pdo->query("SHOW COLUMNS FROM schools LIKE 'status'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE schools ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER logo");
        echo "Added status column.\n";
    }

    // 2. Fix any NULL values that might have been created
    $pdo->exec("UPDATE schools SET status = 'active' WHERE status IS NULL OR status = ''");
    $pdo->exec("UPDATE schools SET reg_number = 'S001' WHERE reg_number IS NULL OR reg_number = ''");

    echo "Data migration/repair completed successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
