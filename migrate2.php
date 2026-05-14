<?php
header("Content-Type: text/html");
require 'db.php';
$conn = getDB();

$queries = [
    "add last_seen column" => "ALTER TABLE current_serving ADD COLUMN IF NOT EXISTS last_seen TIMESTAMP DEFAULT NULL",
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
echo "</ul><p style='font-family:Arial;color:green'><b>✅ Done! Delete migrate2.php after this.</b></p>";
