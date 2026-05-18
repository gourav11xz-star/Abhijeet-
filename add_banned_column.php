<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database;

// Check if column exists
$db->query("SHOW COLUMNS FROM users LIKE 'is_banned'");
$result = $db->single();

if (!$result) {
    echo "Adding 'is_banned' column...\n";
    $db->query("ALTER TABLE users ADD COLUMN is_banned TINYINT(1) DEFAULT 0");
    if ($db->execute()) {
        echo "Column 'is_banned' added successfully.\n";
    } else {
        echo "Failed to add column.\n";
    }
} else {
    echo "Column 'is_banned' already exists.\n";
}
