<?php
// dashboard/index.php
require_once __DIR__ . '/../config/auth.php';
require_login();

// Redirect user to their specific dashboard based on role
header('Location: ' . get_dashboard_url());
exit();
?>