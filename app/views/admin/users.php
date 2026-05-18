<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto px-4 mt-8 pb-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Users</h1>

        <!-- Admin Nav -->
        <div class="space-x-4">
            <a href="<?php echo URL_ROOT; ?>/admin" class="text-gray-500 hover:text-indigo-600">Overview</a>
            <a href="<?php echo URL_ROOT; ?>/admin/ads" class="text-gray-500 hover:text-indigo-600">Manage Ads</a>
            <a href="<?php echo URL_ROOT; ?>/admin/users"
                class="text-indigo-600 font-bold border-b-2 border-indigo-600">Manage Users</a>
        </div>
    </div>

    <?php flash('admin_message'); ?>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">ID</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Name</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Email</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Joined</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['users'] as $user): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-b-0">
                        <td class="py-3 px-4 text-sm text-gray-500">
                            #<?php echo $user->id; ?>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center font-bold text-xs mr-3">
                                    <?php echo strtoupper(substr($user->name, 0, 1)); ?>
                                </div>
                                <span class="text-gray-700 font-medium"><?php echo $user->name; ?></span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <?php echo $user->email; ?>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-500">
                            <?php echo date('M d, Y', strtotime($user->created_at)); ?>
                        </td>
                        <td class="py-3 px-4">
                            <?php if ($user->is_banned): ?>
                                <a href="<?php echo URL_ROOT; ?>/admin/unban_user/<?php echo $user->id; ?>"
                                    class="text-green-500 hover:text-green-700 text-sm font-medium mr-2">Unban</a>
                            <?php else: ?>
                                <a href="<?php echo URL_ROOT; ?>/admin/ban_user/<?php echo $user->id; ?>"
                                    onclick="return confirm('Suspend this user?');"
                                    class="text-orange-500 hover:text-orange-700 text-sm font-medium mr-2">Ban</a>
                            <?php endif; ?>
                            <a href="<?php echo URL_ROOT; ?>/admin/delete_user/<?php echo $user->id; ?>"
                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all their ads.');"
                                class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($data['users'])): ?>
            <div class="text-center py-8 text-gray-500">No users found.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>