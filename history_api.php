<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';
$conn = getDB();
$result = pg_query($conn, "SELECT id, queue_number, served_at FROM history ORDER BY served_at DESC LIMIT 100");
$rows = [];
while ($row = pg_fetch_assoc($result)) {
    $rows[] = $row;
}
echo json_encode($rows);
