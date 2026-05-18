<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto px-4 mt-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <?php require_once APP_ROOT . '/views/inc/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="w-full md:w-3/4">
            <?php flash('admin_message'); ?>

            <h1 class="text-3xl font-bold text-gray-800 mb-6">All Listings</h1>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ad Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Posted By</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($data['ads'] as $ad): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <!-- Thumbnail -->
                                        <?php
                                        // Safety check for images
                                        $imgSrc = 'https://via.placeholder.com/150';
                                        if (!empty($ad->images)) {
                                            $images = json_decode($ad->images);
                                            if (!empty($images) && isset($images[0])) {
                                                $imgSrc = (strpos($images[0], 'http') === 0 ? $images[0] : URL_ROOT . '/' . $images[0]);
                                            }
                                        }
                                        echo '<img src="' . $imgSrc . '" class="h-10 w-10 object-cover rounded mr-3">';
                                        ?>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 line-clamp-1">
                                                <?php echo $ad->title; ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?php echo date('M d, Y', strtotime($ad->created_at)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php
                                        switch ($ad->status) {
                                            case 'active':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'rejected':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                        }
                                        ?>">
                                        <?php echo ucfirst($ad->status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $ad->user_name; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo URL_ROOT; ?>/listings/<?php echo $ad->id; ?>" target="_blank"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    <a href="<?php echo URL_ROOT; ?>/admin/delete_ad/<?php echo $ad->id; ?>"
                                        onclick="return confirm('Are you sure you want to permanently delete this ad?');"
                                        class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>