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
    echo "0";
    exit;
}

$result = $conn->query("SELECT COUNT(*) AS cnt FROM queue");
$row = $result->fetch_assoc();
$conn->close();

echo $row['cnt'];
