<?php
require_once __DIR__ . '/config/auth.php';
header('Content-Type: application/json');
echo json_encode($_SESSION);
