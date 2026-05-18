<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="max-w-4xl mx-auto bg-white p-8 rounded shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center">Post a New Ad</h2>

    <?php if (!empty($data['errors'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?php echo $error; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo URL_ROOT; ?>/listings/create" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
            <input type="text" name="title" id="title"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                value="<?php echo $data['title']; ?>" required>
        </div>

        <div class="mb-4 flex space-x-4">
            <div class="w-1/2">
                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                <select name="category_id" id="category_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    <option value="">Select Category</option>
                    <?php foreach ($data['categories'] as $category): ?>
                        <option value="<?php echo $category->id; ?>">
                            <?php echo $category->name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-1/2">
                <label for="location_id" class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                <select name="location_id" id="location_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    <option value="">Select Location</option>
                    <?php foreach ($data['locations'] as $location): ?>
                        <option value="<?php echo $location->id; ?>">
                            <?php echo $location->city . ', ' . $location->state; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea name="description" id="description" rows="5"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                required><?php echo $data['description']; ?></textarea>
        </div>

        <div class="mb-4 flex space-x-4">
            <div class="w-1/2">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price (₹)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₹</span>
                    <input type="number" step="1" name="price" id="price"
                        class="shadow appearance-none border rounded w-full py-2 pl-8 pr-3 text-gray-700"
                        value="<?php echo $data['price']; ?>" required>
                </div>
            </div>
            <div class="w-1/2">
                <label for="condition" class="block text-gray-700 text-sm font-bold mb-2">Condition</label>
                <select name="condition" id="condition"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    <option value="used" selected>Used</option>
                    <option value="new">New</option>
                    <option value="refurbished">Refurbished</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="images" class="block text-gray-700 text-sm font-bold mb-2">Upload Images (Max 5)</label>
            <input type="file" name="images[]" id="images"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" multiple accept="image/*">
        </div>

        <div class="flex items-center justify-between">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Post Ad Now
            </button>
        </div>
    </form>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>