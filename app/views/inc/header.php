<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo APP_NAME; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo URL_ROOT; ?>/css/premium-categories.css">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <?php require_once 'navbar.php'; ?>
    <div class="container mx-auto px-4 mt-8">

<?php if (function_exists('isAdmin') && isAdmin()): ?>
<?php
$adminPendingReports = 0;
try {
    $adminPdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $adminPendingReports = (int)$adminPdo->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetchColumn();
} catch (Exception $e) {}
?>
<a href="<?php echo URL_ROOT; ?>/admin/reports"
   title="Pending Reports"
   style="position:fixed;top:85px;right:18px;z-index:9999;background:#111827;color:#fff;padding:10px 14px;border-radius:999px;box-shadow:0 10px 25px rgba(0,0,0,.25);font-weight:800;text-decoration:none;">
   🔔
   <?php if ($adminPendingReports > 0): ?>
       <span style="background:#dc2626;color:#fff;border-radius:999px;padding:2px 7px;margin-left:4px;font-size:12px;"><?php echo $adminPendingReports; ?></span>
   <?php endif; ?>
</a>
<?php endif; ?>
