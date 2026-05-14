<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';
$conn = getDB();
$queue_number = $_GET['queue_number'] ?? '';
if (!$queue_number) { http_response_code(400); exit; }
$result = pg_query_params($conn, "SELECT endpoint, p256dh, auth FROM push_subscriptions WHERE queue_number=$1", [$queue_number]);
$sent = 0;
while ($row = pg_fetch_assoc($result)) {
    $payload = json_encode(['title' => 'Your turn!', 'body' => 'Number ' . $queue_number . ' is now being served.', 'number' => $queue_number]);
    $ch = curl_init($row['endpoint']);
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'TTL: 60'], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
    curl_exec($ch);
    curl_close($ch);
    $sent++;
}
pg_query_params($conn, "DELETE FROM push_subscriptions WHERE queue_number=$1", [$queue_number]);
echo json_encode(['sent' => $sent]);
