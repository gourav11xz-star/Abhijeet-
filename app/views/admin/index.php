<?php
$err='';
$totalUsers=$todayUsers=$totalAds=$todayAds=0;
try{
$pdo=new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$totalUsers=$pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn();
$todayUsers=$pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at)=CURDATE() AND deleted_at IS NULL")->fetchColumn();
$totalAds=$pdo->query("SELECT COUNT(*) FROM ads WHERE deleted_at IS NULL")->fetchColumn();
$todayAds=$pdo->query("SELECT COUNT(*) FROM ads WHERE DATE(created_at)=CURDATE() AND deleted_at IS NULL")->fetchColumn();
$recentUser=$pdo->query("SELECT name,email,created_at FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_OBJ);
$recentAd=$pdo->query("SELECT title,created_at FROM ads WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_OBJ);
$recentLogin=$pdo->query("SELECT name,email,last_login FROM users WHERE last_login IS NOT NULL ORDER BY last_login DESC LIMIT 1")->fetch(PDO::FETCH_OBJ);
}catch(Exception $e){$err=$e->getMessage();}
?>
<!doctype html>
<html>
<head>
<title>Admin Dashboard</title>
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
<h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

<?php if($err): ?>
<div class="bg-red-100 text-red-700 p-4 mb-6 rounded">Error: <?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-white p-6 rounded shadow"><p>Total Accounts</p><h2 class="text-3xl font-bold"><?= $totalUsers ?></h2></div>
  <div class="bg-white p-6 rounded shadow"><p>Accounts Today</p><h2 class="text-3xl font-bold"><?= $todayUsers ?></h2></div>
  <div class="bg-white p-6 rounded shadow"><p>Total Posts</p><h2 class="text-3xl font-bold"><?= $totalAds ?></h2></div>
  <div class="bg-white p-6 rounded shadow"><p>Posts Today</p><h2 class="text-3xl font-bold"><?= $todayAds ?></h2></div>
</div>

<div class="bg-white p-6 rounded shadow mb-4">
<h2 class="text-xl font-bold mb-2">Last Login</h2>
<p><?= $recentLogin ? htmlspecialchars($recentLogin->name.' - '.$recentLogin->email.' - '.$recentLogin->last_login) : 'No login record yet' ?></p>
</div>

<div class="bg-white p-6 rounded shadow mb-4">
<h2 class="text-xl font-bold mb-2">Latest Account</h2>
<p><?= $recentUser ? htmlspecialchars($recentUser->name.' - '.$recentUser->email.' - '.$recentUser->created_at) : 'No account found' ?></p>
</div>

<div class="bg-white p-6 rounded shadow">
<h2 class="text-xl font-bold mb-2">Latest Post</h2>
<p><?= $recentAd ? htmlspecialchars($recentAd->title.' - '.$recentAd->created_at) : 'No post found' ?></p>
</div>
</div>
</body>
</html>
