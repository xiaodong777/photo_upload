<?php
// create_database.php

// 连接到SQLite数据库
$db = new SQLite3('photo_upload.db');

// 创建表
$query = "CREATE TABLE IF NOT EXISTS uploads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    photo TEXT NOT NULL,
    remark TEXT,
    timestamp TEXT NOT NULL,
    computer_config TEXT,
    usage_years TEXT,
    asset_number TEXT
)";

$db->exec($query);

echo "Database and table created successfully.";

$db->close();
?>
