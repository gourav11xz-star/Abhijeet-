<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="max-w-4xl mx-auto bg-white p-8 rounded shadow-md mt-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Ad</h2>
        <a href="<?php echo URL_ROOT; ?>/dashboard" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">Back
            to Dashboard</a>
    </div>

    <?php if (!empty($data['errors'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                <?php foreach ($data['errors'] as $error): ?>
                    <li>
                        <?php echo $error; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo URL_ROOT; ?>/listings/update/<?php echo $data['id']; ?>" method="POST"
        enctype="multipart/form-data">
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
                        <option value="<?php echo $category->id; ?>" <?php echo $data['category_id'] == $category->id ? 'selected' : ''; ?>>
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
                        <option value="<?php echo $location->id; ?>" <?php echo $data['location_id'] == $location->id ? 'selected' : ''; ?>>
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
                    <option value="used" <?php echo $data['condition'] == 'used' ? 'selected' : ''; ?>>Used</option>
                    <option value="new" <?php echo $data['condition'] == 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="refurbished" <?php echo $data['condition'] == 'refurbished' ? 'selected' : ''; ?>
                        >Refurbished</option>
                </select>
            </div>
        </div>

        <!-- Current Images -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Current Images</label>
            <?php
            $images = json_decode($data['current_images'], true);
            if (!empty($images)):
                ?>
                <div class="flex flex-wrap gap-4 mb-2">
                    <?php foreach ($images as $img):
                        // Fix URL logic same as show.php if needed, but assuming stored path is usable or needs URL_ROOT
                        $imgUrl = (strpos($img, 'http') === 0) ? $img : URL_ROOT . '/' . $img;
                        ?>
                        <div class="w-24 h-24 rounded overflow-hidden border">
                            <img src="<?php echo $imgUrl; ?>" class="w-full h-full object-cover">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="clear_images" id="clear_images" class="mr-2">
                    <label for="clear_images" class="text-sm text-red-600 font-bold">Delete all existing images and use only
                        new uploads</label>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm">No images uploaded.</p>
            <?php endif; ?>
        </div>

        <div class="mb-6">
            <label for="images" class="block text-gray-700 text-sm font-bold mb-2">Upload New Images (Appends to
                existing unless checked above)</label>
            <input type="file" name="images[]" id="images"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" multiple accept="image/*">
        </div>

        <div class="flex items-center justify-between">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Update Ad
            </button>
        </div>
    </form>
</div>


<script>
(function () {
    const input = document.getElementById('images');
    if (!input) return;

    const preview = document.createElement('div');
    preview.id = 'image-preview-box';
    preview.className = 'mt-4 grid grid-cols-2 md:grid-cols-5 gap-3';
    input.parentNode.appendChild(preview);

    input.addEventListener('change', function () {
        preview.innerHTML = '';

        if (this.files.length > 5) {
            alert('Maximum 5 images allowed.');
            this.value = '';
            return;
        }

        Array.from(this.files).forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-24 object-cover rounded-lg border';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
})();
</script>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>