<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto px-4 mt-8 pb-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Categories</h1>

        <!-- Admin Nav -->
        <div class="space-x-4">
            <a href="<?php echo URL_ROOT; ?>/admin" class="text-gray-500 hover:text-indigo-600">Overview</a>
            <a href="<?php echo URL_ROOT; ?>/admin/ads" class="text-gray-500 hover:text-indigo-600">Manage Ads</a>
            <a href="<?php echo URL_ROOT; ?>/admin/users" class="text-gray-500 hover:text-indigo-600">Manage Users</a>
            <a href="<?php echo URL_ROOT; ?>/admin/categories"
                class="text-indigo-600 font-bold border-b-2 border-indigo-600">Categories</a>
        </div>
    </div>

    <?php flash('admin_message'); ?>

    <!-- Add Category Form -->
    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-8">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Add New Category</h3>
        <form action="<?php echo URL_ROOT; ?>/admin/add_category" method="POST" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="name" placeholder="Category Name"
                class="flex-grow px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required>
            <input type="text" name="icon" placeholder="Icon Class (e.g., fa-car)"
                class="flex-grow px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700">Add</button>
        </form>
    </div>

    <!-- Categories List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">ID</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Name</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Slug</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Icon</th>
                    <th class="py-3 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['categories'] as $category): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-b-0">
                        <td class="py-3 px-4 text-sm text-gray-500">
                            #<?php echo $category->id; ?>
                        </td>
                        <td class="py-3 px-4 font-bold text-gray-800">
                            <?php echo $category->name; ?>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <?php echo $category->slug; ?>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <?php echo $category->icon; ?>
                        </td>
                        <td class="py-3 px-4">
                            <a href="<?php echo URL_ROOT; ?>/admin/delete_category/<?php echo $category->id; ?>"
                                onclick="return confirm('Are you sure? This might fail if ads exist in this category.');"
                                class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($data['categories'])): ?>
            <div class="text-center py-8 text-gray-500">No categories found.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>