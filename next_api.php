<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");
require 'db.php';
$conn = getDB();

pg_query($conn, "UPDATE queue SET status='done' WHERE status='serving'");

$result = pg_query($conn, "SELECT id, queue_number FROM queue WHERE status='waiting' ORDER BY id ASC LIMIT 1");
if (!$result || pg_num_rows($result) === 0) {
    pg_query($conn, "UPDATE current_serving SET queue_number='---' WHERE id=1");
    echo "NO_QUEUE";
    exit;
}

$row = pg_fetch_assoc($result);
$id = $row['id'];
$queue_number = $row['queue_number'];

pg_query_params($conn, "UPDATE queue SET status='serving' WHERE id=$1", [$id]);
pg_query_params($conn, "UPDATE current_serving SET queue_number=$1 WHERE id=1", [$queue_number]);
pg_query_params($conn, "INSERT INTO history (queue_number, served_at) VALUES ($1, NOW())", [$queue_number]);

echo $queue_number;
