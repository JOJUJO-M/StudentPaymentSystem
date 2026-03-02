<?php
require_once __DIR__ . '/config/database.php';

try {
    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM schools LIKE 'status'");
    $stmt->execute();
    $column = $stmt->fetch();

    if (!$column) {
        $pdo->exec("ALTER TABLE schools ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER logo");
        echo "Column 'status' added successfully to 'schools' table.\n";
    } else {
        echo "Column 'status' already exists.\n";
    }

    // Also check if reg_number exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM schools LIKE 'reg_number'");
    $stmt->execute();
    $column = $stmt->fetch();

    if (!$column) {
        $pdo->exec("ALTER TABLE schools ADD COLUMN reg_number VARCHAR(50) NOT NULL AFTER name");
        echo "Column 'reg_number' added successfully.\n";
    } else {
        echo "Column 'reg_number' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
