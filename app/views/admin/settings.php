<?php
$pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k,:v)
        ON DUPLICATE KEY UPDATE setting_value=:v2");
        $stmt->execute([
            ':k' => $key,
            ':v' => $value,
            ':v2' => $value
        ]);
    }
    header("Location: " . URL_ROOT . "/admin/settings");
    exit;
}

$rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

function val($key, $rows) {
    return htmlspecialchars($rows[$key] ?? '');
}
?>
<!doctype html>
<html>
<head>
<title>Site Settings</title>
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
      <a href="<?= URL_ROOT ?>/admin/settings">Settings</a>
      <a href="<?= URL_ROOT ?>/logout">Logout</a>
    </div>
  </div>
</div>

<div class="container mx-auto p-6">
<h1 class="text-3xl font-bold mb-6">Site Settings</h1>

<form method="POST" class="bg-white p-6 rounded shadow max-w-2xl">
  <label class="block mb-2 font-bold">Site Name</label>
  <input name="site_name" value="<?= val('site_name',$rows) ?>" class="w-full border p-2 mb-4 rounded">

  <label class="block mb-2 font-bold">Logo URL</label>
  <input name="site_logo" value="<?= val('site_logo',$rows) ?>" class="w-full border p-2 mb-4 rounded">

  <label class="block mb-2 font-bold">Currency Symbol</label>
  <input name="currency" value="<?= val('currency',$rows) ?>" class="w-full border p-2 mb-4 rounded">

  <label class="block mb-2 font-bold">Contact Email</label>
  <input name="contact_email" value="<?= val('contact_email',$rows) ?>" class="w-full border p-2 mb-4 rounded">

  <label class="block mb-2 font-bold">Phone</label>
  <input name="phone" value="<?= val('phone',$rows) ?>" class="w-full border p-2 mb-4 rounded">

  <label class="block mb-2 font-bold">Footer Text</label>
  <textarea name="footer_text" class="w-full border p-2 mb-4 rounded"><?= val('footer_text',$rows) ?></textarea>

  <label class="block mb-2 font-bold">Maintenance Mode</label>
  <select name="maintenance_mode" class="w-full border p-2 mb-4 rounded">
    <option value="off" <?= (($rows['maintenance_mode'] ?? '') == 'off') ? 'selected' : '' ?>>Off</option>
    <option value="on" <?= (($rows['maintenance_mode'] ?? '') == 'on') ? 'selected' : '' ?>>On</option>
  </select>

  <button class="bg-indigo-600 text-white px-6 py-2 rounded">Save Settings</button>
</form>
</div>
</body>
</html>
