<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

$conn = new mysqli(
    getenv('MYSQLHOST') ?: 'mysql.railway.internal',
    getenv('MYSQLUSER') ?: 'root',
    getenv('MYSQLPASSWORD') ?: 'EACKHmjerPaLzPXVcmPXZgjApYplgdzo',
    getenv('MYSQLDATABASE') ?: 'railway',
    getenv('MYSQLPORT') ?: 3306
);

if ($conn->connect_error) {
    http_response_code(500);
    echo "DB_ERROR: " . $conn->connect_error;
    exit;
}

// Mark current serving as done
$conn->query("UPDATE queue SET status='done' WHERE status='serving'");

// Get next waiting
$result = $conn->query("SELECT id, queue_number FROM queue WHERE status='waiting' ORDER BY id ASC LIMIT 1");

if ($result->num_rows === 0) {
    // Update display
    $conn->query("UPDATE current_serving SET queue_number='---' WHERE id=1");
    $conn->close();
    echo "NO_QUEUE";
    exit;
}

$row = $result->fetch_assoc();
$id = $row['id'];
$queue_number = $row['queue_number'];

// Mark as serving
$stmt = $conn->prepare("UPDATE queue SET status='serving' WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Update current display
$stmt2 = $conn->prepare("UPDATE current_serving SET queue_number=? WHERE id=1");
$stmt2->bind_param("s", $queue_number);
$stmt2->execute();
$stmt2->close();

$conn->close();
echo $queue_number;
