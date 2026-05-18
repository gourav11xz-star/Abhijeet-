<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database;

$name = 'Admin';
$email = 'admin@example.com';
$password = 'admin123';
$hashed = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';

// Check if admin exists
$db->query("SELECT * FROM users WHERE email = :email");
$db->bind(':email', $email);
$existing = $db->single();

if ($existing) {
    echo "Admin user already exists. Updating role/password...\n";
    $db->query("UPDATE users SET role = 'admin', password_hash = :password WHERE email = :email");
    $db->bind(':password', $hashed);
    $db->bind(':email', $email);
    if ($db->execute())
        echo "Admin user updated.\n";
} else {
    echo "Creating admin user...\n";
    $db->query("INSERT INTO users (name, email, password_hash, role) VALUES(:name, :email, :password, :role)");
    $db->bind(':name', $name);
    $db->bind(':email', $email);
    $db->bind(':password', $hashed);
    $db->bind(':role', $role);
    if ($db->execute())
        echo "Admin user created.\n";
}
