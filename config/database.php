<?php
// config/database.php

$host = '127.0.0.1';
$db = 'school_system';
$user = 'root';
$pass = ''; // Default for local installations, update as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

// Global Settings Cache
$settings = [];
try {
     $stmt = $pdo->query("SELECT * FROM settings");
     while ($row = $stmt->fetch()) {
          $settings[$row['setting_key']] = $row['setting_value'];
     }
} catch (Exception $e) {
}

function get_setting($key, $default = '')
{
     global $settings;
     return $settings[$key] ?? $default;
}
