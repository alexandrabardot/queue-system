<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

$conn = new mysqli("sql300.infinityfree.com", "if0_41789653", "gargantiel09", "if0_41789653_queue_system");

if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed");
}

$result = $conn->query("SELECT queue_num FROM queues WHERE status='serving' ORDER BY id DESC LIMIT 1");

if ($result->num_rows === 0) {
    echo "---";
} else {
    $row = $result->fetch_assoc();
    echo $row['queue_num'];
}

$conn->close();
?>
