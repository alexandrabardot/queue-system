<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");
require 'db.php';
$conn = getDB();
$result = pg_query($conn, "SELECT COUNT(*) AS cnt FROM queue");
$row = pg_fetch_assoc($result);
echo (int)$row['cnt'];
