<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<?php
$reports = [];
$filter = $_GET['status'] ?? 'all';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $where = "";
    if (in_array($filter, ['pending', 'resolved', 'reviewed', 'dismissed'])) {
        $where = "WHERE r.status = " . $pdo->quote($filter);
    }

    $reports = $pdo->query("
        SELECT 
            r.id, r.reporter_id, r.ad_id, r.reason, r.comments, r.status, r.created_at,
            a.title AS ad_title,
            a.status AS ad_status,
            a.user_id AS seller_id,
            seller.name AS seller_name,
            u.name AS reporter_name,
            u.email AS reporter_email
        FROM reports r
        LEFT JOIN ads a ON r.ad_id = a.id
        LEFT JOIN users u ON r.reporter_id = u.id
        LEFT JOIN users seller ON a.user_id = seller.id
        $where
        ORDER BY r.created_at DESC
    ")->fetchAll(PDO::FETCH_OBJ);

    $pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetchColumn();
    $resolvedCount = (int)$pdo->query("SELECT COUNT(*) FROM reports WHERE status='resolved'")->fetchColumn();
    $totalCount = (int)$pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();

} catch (Exception $e) {
    echo '<div class="max-w-7xl mx-auto mt-6 bg-red-100 text-red-700 p-4 rounded">Report Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $pendingCount = $resolvedCount = $totalCount = 0;
}
?>

<div class="container mx-auto px-4 mt-8 pb-12">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Reports Management</h1>
            <p class="text-sm text-gray-500 mt-1">Review reports, hide ads, and ban sellers.</p>
        </div>

        <div class="space-x-4">
            <a href="<?php echo URL_ROOT; ?>/admin" class="text-gray-500 hover:text-indigo-600">Overview</a>
            <a href="<?php echo URL_ROOT; ?>/admin/ads" class="text-gray-500 hover:text-indigo-600">Ads</a>
            <a href="<?php echo URL_ROOT; ?>/admin/users" class="text-gray-500 hover:text-indigo-600">Users</a>
            <a href="<?php echo URL_ROOT; ?>/admin/reports" class="text-indigo-600 font-bold border-b-2 border-indigo-600">
                Reports
                <?php if ($pendingCount > 0): ?>
                    <span class="bg-red-600 text-white text-xs rounded-full px-2 py-0.5"><?php echo $pendingCount; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <?php flash('admin_message'); ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow border">
            <div class="text-gray-500 text-sm">Total Reports</div>
            <div class="text-3xl font-extrabold"><?php echo $totalCount; ?></div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow border">
            <div class="text-gray-500 text-sm">Pending Reports</div>
            <div class="text-3xl font-extrabold text-yellow-600"><?php echo $pendingCount; ?></div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow border">
            <div class="text-gray-500 text-sm">Resolved Reports</div>
            <div class="text-3xl font-extrabold text-green-600"><?php echo $resolvedCount; ?></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow border p-4 mb-6 flex flex-wrap gap-2">
        <a href="<?php echo URL_ROOT; ?>/admin/reports?status=all"
           class="px-3 py-2 rounded-lg text-sm font-bold <?php echo $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'; ?>">All</a>
        <a href="<?php echo URL_ROOT; ?>/admin/reports?status=pending"
           class="px-3 py-2 rounded-lg text-sm font-bold <?php echo $filter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700'; ?>">Pending</a>
        <a href="<?php echo URL_ROOT; ?>/admin/reports?status=resolved"
           class="px-3 py-2 rounded-lg text-sm font-bold <?php echo $filter === 'resolved' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700'; ?>">Resolved</a>
        <a href="<?php echo URL_ROOT; ?>/admin/reports?status=reviewed"
           class="px-3 py-2 rounded-lg text-sm font-bold <?php echo $filter === 'reviewed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'; ?>">Reviewed</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">ID</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Ad</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Reporter</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Seller</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Reason</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Status</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Date</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $r): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-b-0">
                        <td class="py-3 px-4 font-bold">#<?php echo (int)$r->id; ?></td>

                        <td class="py-3 px-4 min-w-64">
                            <a href="<?php echo URL_ROOT; ?>/listings/<?php echo (int)$r->ad_id; ?>" target="_blank"
                               class="font-bold text-indigo-600 hover:text-indigo-800">
                                <?php echo htmlspecialchars($r->ad_title ?? 'Deleted Ad'); ?>
                            </a>
                            <div class="text-xs text-gray-400">
                                Ad ID: <?php echo (int)$r->ad_id; ?> | <?php echo htmlspecialchars($r->ad_status ?? 'unknown'); ?>
                            </div>
                        </td>

                        <td class="py-3 px-4 text-sm">
                            <div class="font-bold"><?php echo htmlspecialchars($r->reporter_name ?? 'Unknown'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($r->reporter_email ?? ''); ?></div>
                        </td>

                        <td class="py-3 px-4 text-sm font-bold">
                            <?php echo htmlspecialchars($r->seller_name ?? 'Unknown Seller'); ?>
                        </td>

                        <td class="py-3 px-4 text-sm">
                            <div class="font-semibold"><?php echo htmlspecialchars($r->reason); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($r->comments ?: '-'); ?></div>
                        </td>

                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full
                                <?php echo $r->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    ($r->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'); ?>">
                                <?php echo ucfirst($r->status); ?>
                            </span>
                        </td>

                        <td class="py-3 px-4 text-sm text-gray-500">
                            <?php echo date('M d, Y H:i', strtotime($r->created_at)); ?>
                        </td>

                        <td class="py-3 px-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="<?php echo URL_ROOT; ?>/admin/reports/resolve/<?php echo (int)$r->id; ?>"
                                   class="text-green-600 hover:text-green-900 text-xs font-bold border border-green-200 px-2 py-1 rounded bg-green-50">Resolve</a>

                                <?php if ($r->ad_id): ?>
                                    <a href="<?php echo URL_ROOT; ?>/admin/reject_ad/<?php echo (int)$r->ad_id; ?>"
                                       class="text-orange-600 hover:text-orange-900 text-xs font-bold border border-orange-200 px-2 py-1 rounded bg-orange-50">Hide Ad</a>

                                    <a href="<?php echo URL_ROOT; ?>/admin/reports/ban_seller/<?php echo (int)$r->id; ?>"
                                       onclick="return confirm('Ban this seller and hide all their ads?');"
                                       class="text-red-700 hover:text-red-900 text-xs font-bold border border-red-300 px-2 py-1 rounded bg-red-50">Ban Seller</a>
                                <?php endif; ?>

                                <a href="<?php echo URL_ROOT; ?>/admin/reports/delete/<?php echo (int)$r->id; ?>"
                                   onclick="return confirm('Delete this report?');"
                                   class="text-gray-600 hover:text-gray-900 text-xs font-bold border border-gray-300 px-2 py-1 rounded bg-gray-50">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-500">No reports found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
