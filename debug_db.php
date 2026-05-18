<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database;

echo "--- Columns in 'users' table ---\n";
$db->query("SHOW COLUMNS FROM users");
$columns = $db->resultSet();
foreach ($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}

echo "\n--- All Users ---\n";
$db->query("SELECT id, name, email, role FROM users");
$users = $db->resultSet();
foreach ($users as $u) {
    echo "ID: " . $u->id . " | Name: " . $u->name . " | Email: [" . $u->email . "] | Role: " . ($u->role ?? 'N/A') . "\n";
}
