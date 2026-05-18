<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database;

// Create settings table
$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$db->query($sql);
if ($db->execute()) {
    echo "Settings table created.\n";
} else {
    echo "Failed to create table.\n";
}

// Seed default settings
$defaults = [
    [
        'key' => 'site_name',
        'value' => 'MarketSphere',
        'desc' => 'The name of the website.'
    ],
    [
        'key' => 'site_currency',
        'value' => '₹',
        'desc' => 'Currency symbol displayed.'
    ],
    [
        'key' => 'maintenance_mode',
        'value' => '0',
        'desc' => 'Set to 1 to enable maintenance mode.'
    ],
    [
        'key' => 'support_email',
        'value' => 'support@example.com',
        'desc' => 'Contact email for support.'
    ]
];

foreach ($defaults as $setting) {
    $db->query("SELECT id FROM settings WHERE setting_key = :key");
    $db->bind(':key', $setting['key']);

    if (!$db->single()) {
        $db->query("INSERT INTO settings (setting_key, setting_value, description) VALUES (:key, :value, :desc)");
        $db->bind(':key', $setting['key']);
        $db->bind(':value', $setting['value']);
        $db->bind(':desc', $setting['desc']);
        $db->execute();
        echo "Inserted " . $setting['key'] . "\n";
    }
}
