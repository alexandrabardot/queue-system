<?php
function getDB() {
    $url = parse_url(getenv('DATABASE_URL') ?: 'postgres://queue_db_lges_user:1wSQdAP3fFJXZsVZtSYA3GC68QQVuIzo@dpg-d8321q50lvsc73amssu0-a/queue_db_lges');
    $conn = pg_connect(sprintf(
        "host=%s port=%s dbname=%s user=%s password=%s sslmode=require",
        $url['host'],
        $url['port'] ?? 5432,
        ltrim($url['path'], '/'),
        $url['user'],
        $url['pass']
    ));
    if (!$conn) die("DB connection failed");
    return $conn;
}
