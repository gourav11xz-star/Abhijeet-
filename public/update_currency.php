<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$db->query("UPDATE ads SET currency = 'INR'");
if ($db->execute()) {
    echo "Successfully updated currency to INR for all ads.\n";
} else {
    echo "Failed to update currency.\n";
}
