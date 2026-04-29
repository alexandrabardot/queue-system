<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

$conn = new mysqli("sql300.infinityfree.com", "if0_41789653", "gargantiel09", "if0_41789653_queue_system");

if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed");
}

// Find the next 'waiting' queue entry
$result = $conn->query("SELECT id, queue_num FROM queues WHERE status='waiting' ORDER BY id ASC LIMIT 1");

if ($result->num_rows === 0) {
    echo "NO QUEUE";
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$id = $row['id'];
$queueNum = $row['queue_num'];

// Mark it as 'serving'
$stmt = $conn->prepare("UPDATE queues SET status='serving' WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

// Return ONLY the queue number — no HTML, no extra text
echo $queueNum;
?>
