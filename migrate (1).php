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

echo "<h2 style='font-family:Arial'>🛠 Running migrations...</h2><ul style='font-family:Arial;line-height:2'>";

// Check if device_id column already exists before adding
$result = $conn->query("SHOW COLUMNS FROM queue LIKE 'device_id'");
if ($result->num_rows === 0) {
    if ($conn->query("ALTER TABLE queue ADD COLUMN device_id VARCHAR(64) DEFAULT NULL")) {
        echo "<li>✅ <b>device_id column</b> — Added</li>";
    } else {
        echo "<li>❌ <b>device_id column</b> — " . htmlspecialchars($conn->error) . "</li>";
    }
} else {
    echo "<li>✅ <b>device_id column</b> — Already exists</li>";
}

$conn->close();
echo "</ul><p style='font-family:Arial;color:green'><b>✅ Migration complete! You can delete migrate.php from your repo now.</b></p>";
