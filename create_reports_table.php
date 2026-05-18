<?php
// Fix paths
require_once 'config/config.php';
require_once 'config/database.php';

// Mock Config constants if missing (should be in config.php)
if (!defined('DB_HOST'))
    die('Config not loaded properly');

$db = new Database;
$sql = file_get_contents('reports_table.sql');

// Clean SQL (remove comments/multiline issues if any)
// actually file_get_contents is fine for simple SQL.

$db->query($sql);
if ($db->execute()) {
    echo "Reports Table Created Successfully\n";
} else {
    echo "Failed to create table\n";
}
