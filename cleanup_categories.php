<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database;

echo "Scanning for duplicate categories...\n";

// Get all categories
$db->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $db->resultSet();

$seen = [];
$duplicates = [];

foreach ($categories as $cat) {
    // Normalize name for comparison (trim, lowercase)
    $name = strtolower(trim($cat->name));

    if (isset($seen[$name])) {
        $duplicates[] = [
            'to_delete' => $cat,
            'keep_id' => $seen[$name]
        ];
    } else {
        $seen[$name] = $cat->id;
    }
}

if (empty($duplicates)) {
    echo "No duplicates found.\n";
    exit;
}

echo "Found " . count($duplicates) . " duplicates.\n";

$deletedCount = 0;
foreach ($duplicates as $dup) {
    $delId = $dup['to_delete']->id;
    $keepId = $dup['keep_id'];
    $name = $dup['to_delete']->name;

    echo "Processing '$name' (Delete ID: $delId, Keep ID: $keepId)...\n";

    // 1. Move Ads to the 'keep' category
    $db->query("UPDATE ads SET category_id = :keep_id WHERE category_id = :del_id");
    $db->bind(':keep_id', $keepId);
    $db->bind(':del_id', $delId);
    if ($db->execute()) {
        // echo "  - Ads migrated.\n";
    } else {
        echo "  - FAILED to migrate ads.\n";
        continue; // Don't delete if we can't migrate
    }

    // 2. Delete the duplicate category
    $db->query("DELETE FROM categories WHERE id = :id");
    $db->bind(':id', $delId);
    if ($db->execute()) {
        $deletedCount++;
        echo "  - Category deleted.\n";
    } else {
        echo "  - FAILED to delete category.\n";
    }
}

echo "Cleanup complete. Deleted $deletedCount duplicate categories.\n";
