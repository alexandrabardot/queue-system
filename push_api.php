<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$conn = new mysqli(
    getenv('MYSQLHOST') ?: 'mysql.railway.internal',
    getenv('MYSQLUSER') ?: 'root',
    getenv('MYSQLPASSWORD') ?: 'EACKHmjerPaLzPXVcmPXZgjApYplgdzo',
    getenv('MYSQLDATABASE') ?: 'railway',
    getenv('MYSQLPORT') ?: 3306
);

if ($conn->connect_error) { http_response_code(500); echo json_encode(['error' => 'DB error']); exit; }

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['endpoint'])) { http_response_code(400); echo json_encode(['error' => 'Invalid']); exit; }

$endpoint  = $data['endpoint'];
$p256dh    = $data['keys']['p256dh'] ?? '';
$auth      = $data['keys']['auth'] ?? '';
$queue_num = $data['queue_number'] ?? '';

// Upsert subscription
$stmt = $conn->prepare("INSERT INTO push_subscriptions (endpoint, p256dh, auth, queue_number)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE p256dh=VALUES(p256dh), auth=VALUES(auth), queue_number=VALUES(queue_number)");
$stmt->bind_param("ssss", $endpoint, $p256dh, $auth, $queue_num);
$stmt->execute();
$stmt->close();
$conn->close();
echo json_encode(['ok' => true]);
