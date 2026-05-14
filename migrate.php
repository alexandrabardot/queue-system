<?php
header("Content-Type: text/html");

$conn = new mysqli(
    getenv('MYSQLHOST') ?: 'mysql.railway.internal',
    getenv('MYSQLUSER') ?: 'root',
    getenv('MYSQLPASSWORD') ?: 'EACKHmjerPaLzPXVcmPXZgjApYplgdzo',
    getenv('MYSQLDATABASE') ?: 'railway',
    getenv('MYSQLPORT') ?: 3306
);

if ($conn->connect_error) {
    die("<p style='color:red'>❌ DB Connection failed: " . $conn->connect_error . "</p>");
}

$queries = [
    "push_subscriptions table" => "CREATE TABLE IF NOT EXISTS push_subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        endpoint TEXT NOT NULL,
        p256dh VARCHAR(255),
        auth VARCHAR(100),
        queue_number VARCHAR(10),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_endpoint (endpoint(255))
    )",
    "history table" => "CREATE TABLE IF NOT EXISTS history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        queue_number VARCHAR(10) NOT NULL,
        served_at DATETIME NOT NULL
    )",
    "device_id column on queue" => "ALTER TABLE queue ADD COLUMN IF NOT EXISTS device_id VARCHAR(64) DEFAULT NULL",
];

echo "<h2 style='font-family:Arial'>🛠 Running migrations...</h2><ul style='font-family:Arial;line-height:2'>";

foreach ($queries as $label => $sql) {
    if ($conn->query($sql)) {
        echo "<li>✅ <b>" . htmlspecialchars($label) . "</b> — OK</li>";
    } else {
        echo "<li>❌ <b>" . htmlspecialchars($label) . "</b> — " . htmlspecialchars($conn->error) . "</li>";
    }
}

$conn->close();
echo "</ul><p style='font-family:Arial;color:green'><b>✅ Migration complete! You can delete migrate.php from your repo now.</b></p>";
