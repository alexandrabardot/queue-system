<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");
require 'db.php';
$conn = getDB();

// If last_seen is older than 5 minutes, auto reset
$result = pg_query($conn, "SELECT last_seen FROM current_serving WHERE id=1");
$row = pg_fetch_assoc($result);

if ($row && $row['last_seen']) {
    $last = strtotime($row['last_seen']);
    $diff = time() - $last;
    if ($diff > 300) { // 5 minutes
        pg_query($conn, "DELETE FROM queue");
        pg_query($conn, "DELETE FROM push_subscriptions");
        pg_query($conn, "UPDATE current_serving SET queue_number='---' WHERE id=1");
        echo "AUTO_RESET";
        exit;
    }
}
echo "OK";
