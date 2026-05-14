<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

$conn = new mysqli(
    getenv('MYSQLHOST') ?: 'mysql.railway.internal',
    getenv('MYSQLUSER') ?: 'root',
    getenv('MYSQLPASSWORD') ?: 'EACKHmjerPaLzPXVcmPXZgjApYplgdzo',
    getenv('MYSQLDATABASE') ?: 'railway',
    getenv('MYSQLPORT') ?: 3306
);

if ($conn->connect_error) { http_response_code(500); echo "DB_ERROR: " . $conn->connect_error; exit; }

// Check if this device already has an active number
$device_id = $_GET['device_id'] ?? '';
if ($device_id) {
    $stmt = $conn->prepare("SELECT queue_number FROM queue WHERE device_id=? AND status IN ('waiting','serving')");
    $stmt->bind_param("s", $device_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        echo "EXISTING:" . $row['queue_number'];
        exit;
    }
    $stmt->close();
}

// Generate new number
$result = $conn->query("SELECT COUNT(*) AS cnt FROM queue");
$row = $result->fetch_assoc();
$next_num = $row['cnt'] + 1;
$queue_number = 'A' . str_pad($next_num, 3, '0', STR_PAD_LEFT);

$stmt = $conn->prepare("INSERT INTO queue (queue_number, status, device_id) VALUES (?, 'waiting', ?)");
$stmt->bind_param("ss", $queue_number, $device_id);
$stmt->execute();
$stmt->close();
$conn->close();

echo $queue_number;
