<?php
header("Content-Type: text/html");
require 'db.php';
$conn = getDB();

$queries = [
    "queue table" => "CREATE TABLE IF NOT EXISTS queue (
        id SERIAL PRIMARY KEY,
        queue_number VARCHAR(10) NOT NULL,
        status VARCHAR(10) DEFAULT 'waiting',
        device_id VARCHAR(64) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT NOW()
    )",
    "current_serving table" => "CREATE TABLE IF NOT EXISTS current_serving (
        id INT PRIMARY KEY DEFAULT 1,
        queue_number VARCHAR(10) DEFAULT '---',
        updated_at TIMESTAMP DEFAULT NOW()
    )",
    "push_subscriptions table" => "CREATE TABLE IF NOT EXISTS push_subscriptions (
        id SERIAL PRIMARY KEY,
        endpoint TEXT NOT NULL UNIQUE,
        p256dh VARCHAR(255),
        auth VARCHAR(100),
        queue_number VARCHAR(10),
        created_at TIMESTAMP DEFAULT NOW()
    )",
    "history table" => "CREATE TABLE IF NOT EXISTS history (
        id SERIAL PRIMARY KEY,
        queue_number VARCHAR(10) NOT NULL,
        served_at TIMESTAMP NOT NULL
    )",
    "insert current_serving row" => "INSERT INTO current_serving (id, queue_number) VALUES (1, '---') ON CONFLICT DO NOTHING",
];

echo "<h2 style='font-family:Arial'>🛠 Running migrations...</h2><ul style='font-family:Arial;line-height:2'>";
foreach ($queries as $label => $sql) {
    $result = pg_query($conn, $sql);
    if ($result) {
        echo "<li>✅ <b>" . htmlspecialchars($label) . "</b> — OK</li>";
    } else {
        echo "<li>❌ <b>" . htmlspecialchars($label) . "</b> — " . htmlspecialchars(pg_last_error($conn)) . "</li>";
    }
}
echo "</ul><p style='font-family:Arial;color:green'><b>✅ Done! Delete migrate.php after this.</b></p>";
