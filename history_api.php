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

if ($conn->connect_error) { http_response_code(500); echo json_encode([]); exit; }

$result = $conn->query("SELECT id, queue_number, served_at FROM history ORDER BY served_at DESC LIMIT 100");
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$conn->close();
echo json_encode($rows);
