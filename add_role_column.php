<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database;

// Check if column exists
$db->query("SHOW COLUMNS FROM users LIKE 'role'");
$result = $db->single();

if (!$result) {
    echo "Adding 'role' column...\n";
    $db->query("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user'");
    if ($db->execute()) {
        echo "Column 'role' added successfully.\n";
    } else {
        echo "Failed to add column.\n";
    }
} else {
    echo "Column 'role' already exists.\n";
}
