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

if ($conn->connect_error) {
    http_response_code(500);
    echo "DB_ERROR: " . $conn->connect_error;
    exit;
}

$result = $conn->query("SELECT COUNT(*) AS cnt FROM queue");
$row = $result->fetch_assoc();
$next_num = $row['cnt'] + 1;
$queue_number = 'A' . str_pad($next_num, 3, '0', STR_PAD_LEFT);

$stmt = $conn->prepare("INSERT INTO queue (queue_number, status) VALUES (?, 'waiting')");
$stmt->bind_param("s", $queue_number);
$stmt->execute();
$stmt->close();
$conn->close();

echo $queue_number;
