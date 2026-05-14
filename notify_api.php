<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli(
    getenv('MYSQLHOST') ?: 'mysql.railway.internal',
    getenv('MYSQLUSER') ?: 'root',
    getenv('MYSQLPASSWORD') ?: 'EACKHmjerPaLzPXVcmPXZgjApYplgdzo',
    getenv('MYSQLDATABASE') ?: 'railway',
    getenv('MYSQLPORT') ?: 3306
);

if ($conn->connect_error) { http_response_code(500); exit; }

$queue_number = $_GET['queue_number'] ?? '';
if (!$queue_number) { http_response_code(400); exit; }

$stmt = $conn->prepare("SELECT endpoint, p256dh, auth FROM push_subscriptions WHERE queue_number = ?");
$stmt->bind_param("s", $queue_number);
$stmt->execute();
$result = $stmt->get_result();
$sent = 0;

while ($row = $result->fetch_assoc()) {
    $payload = json_encode([
        'title' => 'Your turn!',
        'body'  => 'Number ' . $queue_number . ' is now being served. Please proceed to the counter.',
        'number' => $queue_number
    ]);

    // Simple web push without VAPID (works for basic push)
    $ch = curl_init($row['endpoint']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'TTL: 60'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5
    ]);
    curl_exec($ch);
    curl_close($ch);
    $sent++;
}

$stmt->close();

// Clean up used subscription
$stmt2 = $conn->prepare("DELETE FROM push_subscriptions WHERE queue_number = ?");
$stmt2->bind_param("s", $queue_number);
$stmt2->execute();
$stmt2->close();
$conn->close();

echo json_encode(['sent' => $sent]);
