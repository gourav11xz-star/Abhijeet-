<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database;
$email = 'admin@example.com';

$db->query("SELECT * FROM users WHERE email = :email");
$db->bind(':email', $email);
$user = $db->single();

if ($user) {
    echo "User found: " . $user->name . " | Role: " . $user->role . " | Banned: " . $user->is_banned . "\n";
} else {
    echo "User NOT found.\n";
}
