<?php
require_once __DIR__ . '/config/database.php';

try {
    // Drop the problematic enum column if it exists correctly or incorrectly
    $pdo->exec("ALTER TABLE schools DROP COLUMN status");
    echo "Dropped column status.\n";

    // Add it as a simple VARCHAR for now to ensure it works
    $pdo->exec("ALTER TABLE schools ADD COLUMN status VARCHAR(20) DEFAULT 'active' AFTER logo");
    echo "Added column status as VARCHAR(20) with default 'active'.\n";

    // Fix existing data if any rows were created without default (though unlikely right after drop/add)
    $pdo->exec("UPDATE schools SET status = 'active' WHERE status IS NULL OR status = ''");
    echo "Reset all statuses to active.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
