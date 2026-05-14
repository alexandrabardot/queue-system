<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';
$conn = getDB();
if (isset($_GET['clear_all'])) {
    pg_query($conn, "DELETE FROM history");
    echo json_encode(['ok' => true]);
    exit;
}
$id = intval($_GET['id'] ?? 0);
if (!$id) { http_response_code(400); echo json_encode(['error' => 'Invalid ID']); exit; }
pg_query_params($conn, "DELETE FROM history WHERE id=$1", [$id]);
echo json_encode(['ok' => true]);
