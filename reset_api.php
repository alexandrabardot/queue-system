<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");
require 'db.php';
$conn = getDB();
pg_query($conn, "DELETE FROM queue");
pg_query($conn, "DELETE FROM push_subscriptions");
pg_query($conn, "UPDATE current_serving SET queue_number='---' WHERE id=1");
echo "RESET_OK";
