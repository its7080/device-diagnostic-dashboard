<?php
// config.php
// Put DB credentials here. Move this file outside webroot in production if possible.

$db_host = 'sql108.infinityfree.com'; // or your database host sql108.infinityfree.com
$db_name = 'if0_39666949_keyboard_test'; // if0_39666949_keyboard_test
$db_user = 'if0_39666949'; // if0_39666949
$db_pass = 'V2K5FNBFUKL1C'; // set your MySQL password V2K5FNBFUKL1C

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}