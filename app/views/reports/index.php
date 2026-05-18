<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto px-4 mt-8 pb-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Reports</h1>
        <a href="<?php echo URL_ROOT; ?>/admin" class="text-gray-500 hover:text-indigo-600">Back to Admin Panel</a>
    </div>

    <?php flash('report_message'); ?>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Reporter</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Ad</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Reason</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Status</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['reports'] as $report): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-b-0">
                        <td class="py-3 px-4 text-sm text-gray-700">
                            <?php echo $report->reporter_name; ?>
                        </td>
                        <td class="py-3 px-4">
                            <a href="<?php echo URL_ROOT; ?>/listings/<?php echo $report->ad_id; ?>" target="_blank"
                                class="font-medium text-indigo-600 hover:text-indigo-800">
                                <?php echo $report->ad_title; ?>
                            </a>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <?php echo $report->reason; ?>
                        </td>
                        <td class="py-3 px-4">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $report->status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo ucfirst($report->status); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <?php if ($report->status != 'resolved'): ?>
                                    <a href="<?php echo URL_ROOT; ?>/reports/resolve/<?php echo $report->id; ?>"
                                        class="text-green-600 hover:text-green-900 text-xs font-bold border border-green-200 px-2 py-1 rounded bg-green-50">Resolve</a>
                                <?php endif; ?>
                                <a href="<?php echo URL_ROOT; ?>/reports/delete/<?php echo $report->id; ?>"
                                    onclick="return confirm('Delete this report?');"
                                    class="text-red-600 hover:text-red-900 text-xs font-bold border border-red-200 px-2 py-1 rounded bg-red-50">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($data['reports'])): ?>
            <div class="text-center py-8 text-gray-500">No reports found.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>