<?php
// fix_activity_logs.php — One-time migration to ensure activity_logs has school_id column
// Run this once from the browser, then delete it.
require_once __DIR__ . '/config/database.php';

echo "<pre>";

try {
    // Check if school_id column exists in activity_logs
    $stmt = $pdo->query("SHOW COLUMNS FROM activity_logs LIKE 'school_id'");
    $col = $stmt->fetch();

    if (!$col) {
        $pdo->exec("ALTER TABLE activity_logs ADD COLUMN school_id INT(11) DEFAULT NULL AFTER id");
        echo "✅ Added 'school_id' column to activity_logs.\n";
    } else {
        echo "✅ 'school_id' column already exists in activity_logs.\n";
    }

    // Verify final structure
    $result = $pdo->query("DESCRIBE activity_logs")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nFinal activity_logs structure:\n";
    foreach ($result as $row) {
        echo "  {$row['Field']} | {$row['Type']} | Null:{$row['Null']} | Default:{$row['Default']}\n";
    }

    echo "\n✅ Migration complete. You can now delete this file.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
