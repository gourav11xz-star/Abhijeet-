<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$db->query("SELECT currency, count(*) as count FROM ads GROUP BY currency");
$results = $db->resultSet();

foreach ($results as $row) {
    echo "Currency: " . $row->currency . " - Count: " . $row->count . "\n";
}
