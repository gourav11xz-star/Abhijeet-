<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database;
    // We need to access the PDO instance directly to get error info if needed, 
    // but the Database class wrapper might hide it. 
    // However, Database::execute() usually returns false on failure.

    $name = 'Admin';
    $email = 'admin@example.com';
    $password = 'admin123';
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = 'admin';

    echo "Attempting to create user: $email\n";

    // 1. Delete if exists (to be sure)
    $db->query("DELETE FROM users WHERE email = :email");
    $db->bind(':email', $email);
    $db->execute();

    // 2. Insert
    $db->query("INSERT INTO users (name, email, password_hash, role, is_verified) VALUES(:name, :email, :password, :role, 1)");
    $db->bind(':name', $name);
    $db->bind(':email', $email);
    $db->bind(':password', $hashed);
    $db->bind(':role', $role);

    if ($db->execute()) {
        echo "SUCCESS: Admin user created.\n";
        echo "ID: " . $db->lastInsertId() . "\n";
    } else {
        echo "FAILURE: Could not create user.\n";
        // Attempt to print error info if possible (depends on Database class implementation)
    }

} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
