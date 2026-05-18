<?php
$err = '';
$totalUsers = $todayUsers = $totalAds = $todayAds = 0;
$recentLogins = $recentUsers = $recentAds = [];

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn();
    $todayUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at)=CURDATE() AND deleted_at IS NULL")->fetchColumn();
    $totalAds = $pdo->query("SELECT COUNT(*) FROM ads WHERE deleted_at IS NULL")->fetchColumn();
    $todayAds = $pdo->query("SELECT COUNT(*) FROM ads WHERE DATE(created_at)=CURDATE() AND deleted_at IS NULL")->fetchColumn();

    $recentLogins = $pdo->query("SELECT name,email,role,last_login FROM users WHERE last_login IS NOT NULL ORDER BY last_login DESC LIMIT 10")->fetchAll(PDO::FETCH_OBJ);
    $recentUsers = $pdo->query("SELECT name,email,role,status,created_at FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_OBJ);

    $recentAds = $pdo->query("
        SELECT ads.title, ads.status, ads.created_at, users.name AS user_name, categories.name AS category_name
        FROM ads
        LEFT JOIN users ON ads.user_id = users.id
        LEFT JOIN categories ON ads.category_id = categories.id
        WHERE ads.deleted_at IS NULL
        ORDER BY ads.created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_OBJ);
} catch (Exception $e) {
    $err = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="bg-gray-900 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <h1 class="text-2xl font-bold">MarketSphere Admin</h1>
            <div class="space-x-4">
                <a href="<?= URL_ROOT ?>/admin" class="underline">Dashboard</a>
                <a href="<?= URL_ROOT ?>/admin/categories" class="underline">Categories</a>
                <a href="<?= URL_ROOT ?>/admin/users" class="underline">Users</a>
                <a href="<?= URL_ROOT ?>/admin/ads" class="underline">Ads</a>
                <a href="<?= URL_ROOT ?>/logout" class="underline">Logout</a>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-bold mb-6">Admin Dashboard</h2>

        <?php if ($err): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                Error: <?= htmlspecialchars($err) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded shadow">
                <p class="text-gray-500">Total Accounts</p>
                <h3 class="text-3xl font-bold"><?= htmlspecialchars($totalUsers) ?></h3>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <p class="text-gray-500">Accounts Today</p>
                <h3 class="text-3xl font-bold"><?= htmlspecialchars($todayUsers) ?></h3>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <p class="text-gray-500">Total Posts</p>
                <h3 class="text-3xl font-bold"><?= htmlspecialchars($totalAds) ?></h3>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <p class="text-gray-500">Posts Today</p>
                <h3 class="text-3xl font-bold"><?= htmlspecialchars($todayAds) ?></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-bold mb-4">Recent Logins</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><th class="text-left p-2">Name</th><th class="text-left p-2">Email</th><th class="text-left p-2">Role</th><th class="text-left p-2">Last Login</th></tr>
                <?php foreach ($recentLogins as $u): ?>
                    <tr class="border-b">
                        <td class="p-2"><?= htmlspecialchars($u->name) ?></td>
                        <td class="p-2"><?= htmlspecialchars($u->email) ?></td>
                        <td class="p-2"><?= htmlspecialchars($u->role) ?></td>
                        <td class="p-2"><?= htmlspecialchars($u->last_login) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentLogins)): ?>
                    <tr><td colspan="4" class="p-2 text-gray-500">No login records yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-bold mb-4">New Accounts</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><th class="text-left p-2">Name</th><th class="text-left p-2">Email</th><th class="text-left p-2">Role</th><th class="text-left p-2">Status</th><th class="text-left p-2">Created</th></tr>
                <?php foreach ($recentUsers as $u): ?>
                    <tr class="border-b">
                        <td class="p-2"><?= htmlspecialchars($u->name) ?></td>
                        <td class="p-2"><?= htmlspecialchars($u->email) ?></td>
                        <td class="p-2"><?= htmlspecialchars($u->role) ?></td>
                        <td class="p-2"><?= htmlspecialchars($u->status) ?></td>
                        <td class="p-2"><?= htmlspecialchars($u->created_at) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl font-bold mb-4">New Posts</h3>
            <table class="w-full text-sm">
                <tr class="border-b"><th class="text-left p-2">Title</th><th class="text-left p-2">Owner</th><th class="text-left p-2">Category</th><th class="text-left p-2">Status</th><th class="text-left p-2">Created</th></tr>
                <?php foreach ($recentAds as $ad): ?>
                    <tr class="border-b">
                        <td class="p-2"><?= htmlspecialchars($ad->title) ?></td>
                        <td class="p-2"><?= htmlspecialchars($ad->user_name ?? 'Unknown') ?></td>
                        <td class="p-2"><?= htmlspecialchars($ad->category_name ?? 'Unknown') ?></td>
                        <td class="p-2"><?= htmlspecialchars($ad->status) ?></td>
                        <td class="p-2"><?= htmlspecialchars($ad->created_at) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentAds)): ?>
                    <tr><td colspan="5" class="p-2 text-gray-500">No posts yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
