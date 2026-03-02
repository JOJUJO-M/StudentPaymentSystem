<?php
require_once __DIR__ . '/config/database.php';
echo "Autocommit: " . $pdo->query("SELECT @@autocommit")->fetchColumn() . "\n";
echo "Storage Engine: " . $pdo->query("SELECT ENGINE FROM information_schema.tables WHERE table_name = 'schools'")->fetchColumn() . "\n";
