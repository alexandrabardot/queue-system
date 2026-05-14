<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");
require 'db.php';
$conn = getDB();

// Update last seen timestamp
pg_query($conn, "UPDATE current_serving SET last_seen=NOW() WHERE id=1");
echo "OK";
