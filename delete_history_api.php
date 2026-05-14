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

if ($conn->connect_error) { http_response_code(500); echo json_encode(['error' => 'DB error']); exit; }

// Clear all
if (isset($_GET['clear_all'])) {
    $conn->query("DELETE FROM history");
    $conn->close();
    echo json_encode(['ok' => true]);
    exit;
}

// Delete single
$id = intval($_GET['id'] ?? 0);
if (!$id) { http_response_code(400); echo json_encode(['error' => 'Invalid ID']); exit; }

$stmt = $conn->prepare("DELETE FROM history WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['ok' => true]);
