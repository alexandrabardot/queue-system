<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");
require 'db.php';
$conn = getDB();
$result = pg_query($conn, "SELECT queue_number FROM current_serving WHERE id=1");
$row = pg_fetch_assoc($result);
echo $row ? $row['queue_number'] : '---';
