<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto px-4 mt-8 pb-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Ads</h1>

        <!-- Admin Nav -->
        <div class="space-x-4">
            <a href="<?php echo URL_ROOT; ?>/admin" class="text-gray-500 hover:text-indigo-600">Overview</a>
            <a href="<?php echo URL_ROOT; ?>/admin/ads"
                class="text-indigo-600 font-bold border-b-2 border-indigo-600">Manage Ads</a>
            <a href="<?php echo URL_ROOT; ?>/admin/users" class="text-gray-500 hover:text-indigo-600">Manage Users</a>
        </div>
    </div>

    <?php flash('admin_message'); ?>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Title</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">User</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Price</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Status</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Date</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['ads'] as $ad): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-b-0">
                        <td class="py-3 px-4">
                            <a href="<?php echo URL_ROOT; ?>/listings/<?php echo $ad->id; ?>" target="_blank"
                                class="font-medium text-indigo-600 hover:text-indigo-800">
                                <?php echo $ad->title; ?>
                            </a>
                            <div class="text-xs text-gray-400"><?php echo $ad->category_name; ?></div>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-700">
                            <?php echo $ad->user_name; ?>
                        </td>
                        <td class="py-3 px-4 text-sm font-bold text-gray-800">
                            <?php echo $ad->currency . ' ' . number_format($ad->price); ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php
                                echo $ad->status == 'active' ? 'bg-green-100 text-green-800' :
                                    ($ad->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                        ($ad->status == 'sold' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'));
                                ?>">
                                <?php echo ucfirst($ad->status); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-500">
                            <?php echo date('M d, Y', strtotime($ad->created_at)); ?>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <?php if ($ad->status == 'pending'): ?>
                                    <a href="<?php echo URL_ROOT; ?>/admin/approve_ad/<?php echo $ad->id; ?>"
                                        class="text-green-600 hover:text-green-900 text-xs font-bold border border-green-200 px-2 py-1 rounded bg-green-50">Approve</a>
                                    <a href="<?php echo URL_ROOT; ?>/admin/reject_ad/<?php echo $ad->id; ?>"
                                        class="text-red-600 hover:text-red-900 text-xs font-bold border border-red-200 px-2 py-1 rounded bg-red-50">Reject</a>
                                <?php elseif ($ad->status == 'active'): ?>
                                    <a href="<?php echo URL_ROOT; ?>/admin/reject_ad/<?php echo $ad->id; ?>"
                                        class="text-red-600 hover:text-red-900 text-xs font-bold border border-red-200 px-2 py-1 rounded bg-red-50">Reject</a>
                                <?php elseif ($ad->status == 'rejected'): ?>
                                    <a href="<?php echo URL_ROOT; ?>/admin/approve_ad/<?php echo $ad->id; ?>"
                                        class="text-green-600 hover:text-green-900 text-xs font-bold border border-green-200 px-2 py-1 rounded bg-green-50">Activate</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($data['ads'])): ?>
            <div class="text-center py-8 text-gray-500">No ads found.</div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($data['total_pages'] > 1): ?>
        <div class="mt-6 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <!-- Previous -->
                <?php if ($data['page'] > 1): ?>
                    <a href="<?php echo URL_ROOT; ?>/admin/ads?page=<?php echo $data['page'] - 1; ?>"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php
                $range = 2; // Pages to show +/- current page
                $total = $data['total_pages'];
                $current = $data['page'];

                // Algorithm to determine valid page numbers to show
                // We always show 1, Total, and Current +/- Range
            
                // 1. Always show Page 1
                echo '<a href="' . URL_ROOT . '/admin/ads?page=1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ' . ($current == 1 ? 'text-indigo-600 bg-indigo-50 border-indigo-500 z-10' : 'text-gray-500 hover:bg-gray-50') . '">1</a>';

                // 2. Window Logic
                $start = max(2, $current - $range);
                $end = min($total - 1, $current + $range);

                // Leading gap
                if ($start > 2) {
                    echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                }

                // Middle Pages
                for ($i = $start; $i <= $end; $i++) {
                    echo '<a href="' . URL_ROOT . '/admin/ads?page=' . $i . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ' . ($current == $i ? 'text-indigo-600 bg-indigo-50 border-indigo-500 z-10' : 'text-gray-500 hover:bg-gray-50') . '">' . $i . '</a>';
                }

                // Trailing gap
                if ($end < $total - 1) {
                    echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                }

                // 3. Always show Last Page (if > 1)
                if ($total > 1) {
                    echo '<a href="' . URL_ROOT . '/admin/ads?page=' . $total . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ' . ($current == $total ? 'text-indigo-600 bg-indigo-50 border-indigo-500 z-10' : 'text-gray-500 hover:bg-gray-50') . '">' . $total . '</a>';
                }
                ?>

                <!-- Next -->
                <?php if ($data['page'] < $data['total_pages']): ?>
                    <a href="<?php echo URL_ROOT; ?>/admin/ads?page=<?php echo $data['page'] + 1; ?>"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>