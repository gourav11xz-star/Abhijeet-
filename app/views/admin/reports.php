<?php
$err = '';
$reports = [];

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $reports = $pdo->query("
        SELECT 
            r.id,
            r.ad_id,
            r.reporter_id,
            r.reason,
            r.comments,
            r.status,
            r.created_at,
            a.title AS ad_title,
            u.name AS reporter_name,
            u.email AS reporter_email
        FROM reports r
        LEFT JOIN ads a ON r.ad_id = a.id
        LEFT JOIN users u ON r.reporter_id = u.id
        ORDER BY r.created_at DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_OBJ);

} catch (Exception $e) {
    $err = $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
<title>Reports Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="bg-gray-900 text-white p-4">
  <div class="container mx-auto flex justify-between">
    <b>MarketSphere Admin</b>
    <div class="space-x-4">
      <a href="<?= URL_ROOT ?>/admin">Dashboard</a>
      <a href="<?= URL_ROOT ?>/admin/categories">Categories</a>
      <a href="<?= URL_ROOT ?>/admin/users">Users</a>
      <a href="<?= URL_ROOT ?>/admin/ads">Ads</a>
      <a href="<?= URL_ROOT ?>/admin/reports">Reports</a>
      <a href="<?= URL_ROOT ?>/logout">Logout</a>
    </div>
  </div>
</div>

<div class="container mx-auto p-6">
<h1 class="text-3xl font-bold mb-6">Reports Panel</h1>

<?php if ($err): ?>
<div class="bg-red-100 text-red-700 p-4 rounded mb-6">
Error: <?= htmlspecialchars($err) ?>
</div>
<?php endif; ?>

<div class="bg-white rounded shadow overflow-x-auto">
<table class="w-full text-sm">
<tr class="bg-gray-200">
  <th class="text-left p-3">ID</th>
  <th class="text-left p-3">Ad</th>
  <th class="text-left p-3">Reporter</th>
  <th class="text-left p-3">Reason</th>
  <th class="text-left p-3">Comments</th>
  <th class="text-left p-3">Status</th>
  <th class="text-left p-3">Date</th>
</tr>

<?php foreach ($reports as $r): ?>
<tr class="border-b">
  <td class="p-3"><?= htmlspecialchars($r->id) ?></td>
  <td class="p-3"><?= htmlspecialchars($r->ad_title ?? 'Unknown Ad') ?></td>
  <td class="p-3">
    <?= htmlspecialchars($r->reporter_name ?? 'Unknown User') ?><br>
    <span class="text-gray-500"><?= htmlspecialchars($r->reporter_email ?? '') ?></span>
  </td>
  <td class="p-3"><?= htmlspecialchars($r->reason ?? '') ?></td>
  <td class="p-3"><?= htmlspecialchars($r->comments ?? '') ?></td>
  <td class="p-3"><?= htmlspecialchars($r->status ?? 'pending') ?></td>
  <td class="p-3"><?= htmlspecialchars($r->created_at ?? '') ?></td>
</tr>
<?php endforeach; ?>

<?php if (empty($reports)): ?>
<tr><td colspan="7" class="p-4 text-gray-500">No reports found.</td></tr>
<?php endif; ?>
</table>
</div>
</div>
</body>
</html>
