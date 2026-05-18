<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto px-4 mt-8 pb-12">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">My Dashboard</h1>

    <?php flash('profile_message'); ?>
    <?php flash('ad_message'); ?>

    <div class="flex flex-col md:flex-row gap-8">

        <!-- Profile Section -->
        <aside class="w-full md:w-1/3">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold mb-4">Profile Settings</h3>
                <form action="<?php echo URL_ROOT; ?>/dashboard/update_profile" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="flex justify-center mb-6">
                        <div class="relative w-24 h-24">
                            <?php if (!empty($data['user']->avatar)): ?>
                                <img src="<?php echo URL_ROOT . '/' . $data['user']->avatar; ?>"
                                    class="w-full h-full rounded-full object-cover border-2 border-indigo-100">
                            <?php else: ?>
                                <div
                                    class="w-full h-full rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-2xl font-bold">
                                    <?php echo strtoupper(substr($data['user']->name, 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <label for="avatar"
                                class="absolute bottom-0 right-0 bg-indigo-600 text-white p-1 rounded-full cursor-pointer hover:bg-indigo-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </label>
                            <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" name="name" value="<?php echo $data['user']->name; ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                        <input type="text" name="phone" value="<?php echo $data['user']->phone; ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Profile
                    </button>
                </form>
            </div>
        </aside>

        <!-- My Ads Section -->
        <main class="w-full md:w-2/3">
            <!-- My Ads Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">My Listings</h3>
                    <a href="<?php echo URL_ROOT; ?>/listings/create"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded">
                        + Post New Ad
                    </a>
                </div>

                <?php if (empty($data['ads'])): ?>
                    <p class="text-gray-500 text-center py-8">You haven't posted any ads yet.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Title</th>
                                    <th class="py-2 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Price</th>
                                    <th class="py-2 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Status</th>
                                    <th class="py-2 px-4 bg-gray-50 font-bold text-sm text-gray-600 border-b">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['ads'] as $ad): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b">
                                            <a href="<?php echo URL_ROOT; ?>/listings/<?php echo $ad->id; ?>"
                                                class="font-medium text-indigo-600 hover:text-indigo-800">
                                                <?php echo $ad->title; ?>
                                            </a>
                                            <div class="text-xs text-gray-400 mt-1">
                                                <?php echo date('M d, Y', strtotime($ad->created_at)); ?>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <?php echo $ad->currency . ' ' . number_format($ad->price, 2); ?>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php
                                                echo $ad->status == 'active' ? 'bg-green-100 text-green-800' :
                                                    ($ad->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                        ($ad->status == 'sold' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'));
                                                ?>">
                                                <?php echo ucfirst($ad->status); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <div class="flex space-x-2">
                                                <!-- Edit -->
                                                <a href="<?php echo URL_ROOT; ?>/listings/edit/<?php echo $ad->id; ?>"
                                                    class="text-gray-500 hover:text-indigo-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <!-- Delete -->
                                                <form
                                                    action="<?php echo URL_ROOT; ?>/dashboard/delete_ad/<?php echo $ad->id; ?>"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this ad?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <button type="submit"
                                                        class="text-gray-500 hover:text-red-600 focus:outline-none">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- My Wishlist Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold mb-6">My Wishlist</h3>

                <?php if (empty($data['favorites'])): ?>
                    <p class="text-gray-500 text-center py-8">You haven't saved any items yet.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <?php foreach ($data['favorites'] as $ad): ?>
                            <a href="<?php echo URL_ROOT; ?>/listings/<?php echo $ad->id; ?>"
                                class="flex bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <div class="w-1/3 bg-gray-200 relative">
                                    <?php
                                    $images = json_decode($ad->images);
                                    $imgSrc = !empty($images) ? (strpos($images[0], 'http') === 0 ? $images[0] : URL_ROOT . '/' . $images[0]) : 'https://via.placeholder.com/150';
                                    ?>
                                    <img src="<?php echo $imgSrc; ?>" class="absolute inset-0 w-full h-full object-cover">
                                </div>
                                <div class="w-2/3 p-4 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-gray-900 truncate"><?php echo $ad->title; ?></h4>
                                        <p class="text-indigo-600 font-bold">
                                            <?php echo $ad->currency . ' ' . number_format($ad->price); ?></p>
                                    </div>
                                    <div class="flex justify-between items-end mt-2">
                                        <span class="text-xs text-gray-500"><?php echo $ad->city; ?></span>
                                        <!-- Remove from favorites button logic could go here, for now just view -->
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>