<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
$conn = new mysqli("sql300.infinityfree.com", "if0_41789653", "gargantiel09", "if0_41789653_queue_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT COUNT(*) as total FROM queues");
$row = $result->fetch_assoc();

$number = $row['total'] + 1;
$queueNum = "A" . str_pad($number, 3, "0", STR_PAD_LEFT);

$conn->query("INSERT INTO queues (queue_num, status) VALUES ('$queueNum', 'waiting')");

echo $queueNum;
?>