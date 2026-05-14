<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;
require 'db.php';
$conn = getDB();
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['endpoint'])) { http_response_code(400); echo json_encode(['error' => 'Invalid']); exit; }
$endpoint  = $data['endpoint'];
$p256dh    = $data['keys']['p256dh'] ?? '';
$auth      = $data['keys']['auth'] ?? '';
$queue_num = $data['queue_number'] ?? '';
pg_query_params($conn,
    "INSERT INTO push_subscriptions (endpoint, p256dh, auth, queue_number)
     VALUES ($1,$2,$3,$4)
     ON CONFLICT (endpoint) DO UPDATE SET p256dh=$2, auth=$3, queue_number=$4",
    [$endpoint, $p256dh, $auth, $queue_num]);
echo json_encode(['ok' => true]);
