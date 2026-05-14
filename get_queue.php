<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");
require 'db.php';
$conn = getDB();

$device_id = $_GET['device_id'] ?? '';
if ($device_id) {
    $result = pg_query_params($conn, "SELECT queue_number FROM queue WHERE device_id=$1 AND status IN ('waiting','serving') LIMIT 1", [$device_id]);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        echo "EXISTING:" . $row['queue_number'];
        exit;
    }
}

$result = pg_query($conn, "SELECT COUNT(*) AS cnt FROM queue");
$row = pg_fetch_assoc($result);
$next_num = (int)$row['cnt'] + 1;
$queue_number = 'A' . str_pad($next_num, 3, '0', STR_PAD_LEFT);

pg_query_params($conn, "INSERT INTO queue (queue_number, status, device_id) VALUES ($1, 'waiting', $2)", [$queue_number, $device_id]);
echo $queue_number;
