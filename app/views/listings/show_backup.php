<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto mt-8 px-4 pb-12" x-data="{ showReportModal: false }">
    <?php flash('ad_message'); ?>

    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="<?php echo URL_ROOT; ?>/listings" class="hover:text-indigo-600">Home</a>
                <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                    <path
                        d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                </svg>
            </li>
            <li class="flex items-center">
                <span class="text-gray-700"><?php echo $data['ad']->title; ?></span>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="md:flex">
            <!-- Image Gallery -->
            <div class="md:w-3/5 bg-gray-100" x-data="{ activeImage: '' }" x-init="
                <?php
                $images = $data['ad']->images;
                // Handle JSON or raw string
                if (is_string($images)) {
                    $decoded = json_decode($images, true);
                    $images = $decoded ?? [$images];
                } elseif (!is_array($images)) {
                    $images = [];
                }

                // Ensure we have at least one valid image
                if (empty($images)) {
                    $images = ['https://via.placeholder.com/600x400?text=No+Image'];
                }

                // Process images to ensure full URLs
                $processedImages = array_map(function ($img) {
                    if (strpos($img, 'http') === 0)
                        return $img;
                    return URL_ROOT . '/' . ltrim($img, '/');
                }, $images);

                $initialImage = $processedImages[0];
                ?>
                activeImage: '<?php echo $initialImage; ?>'
                ">

                <!-- Main Image -->
                <div
                    class="h-[500px] w-full flex items-center justify-center overflow-hidden bg-black/5 relative group">
                    <img src="<?php echo htmlspecialchars($initialImage); ?>" :src="activeImage" alt="<?php echo htmlspecialchars($data['ad']->title); ?>"
                        class="max-w-full max-h-full object-contain transition-opacity duration-300">
                </div>

                <!-- Thumbnails -->
                <?php if (count($processedImages) > 1): ?>
                    <div class="flex p-4 space-x-3 overflow-x-auto bg-white border-t border-gray-100">
                        <?php foreach ($processedImages as $img): ?>
                            <div @click="activeImage = '<?php echo $img; ?>'"
                                class="w-20 h-20 flex-shrink-0 cursor-pointer rounded-lg border-2 border-transparent hover:border-indigo-500 overflow-hidden transition-all duration-200"
                                :class="{ 'border-indigo-500 ring-2 ring-indigo-200': activeImage === '<?php echo $img; ?>' }">
                                <img src="<?php echo $img; ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Ad Details -->
            <div class="md:w-2/5 p-8 flex flex-col">
                <div class="flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight">
                            <?php echo $data['ad']->title; ?>
                        </h1>
                    </div>

                    <div class="flex items-center space-x-4 mb-6">
                        <span class="text-3xl font-bold text-indigo-700 font-mono">
                            ₹ <?php echo number_format($data['ad']->price); ?>
                        </span>
                        <?php if ($data['ad']->condition_type): ?>
                            <span
                                class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold capitalize border border-indigo-100">
                                <?php echo $data['ad']->condition_type; ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center text-gray-500 text-sm mb-6 pb-6 border-b border-gray-100 space-x-6">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13 21.314l-4.657-4.657a8 8 0 010-11.314z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <?php echo $data['ad']->city ? $data['ad']->city : 'Unknown Location'; ?>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Posted <?php echo date('M d, Y', strtotime($data['ad']->created_at)); ?>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-3 block">Description</h3>
                        <p class="text-gray-600 leading-relaxed whitespace-pre-line text-base">
                            <?php echo $data['ad']->description; ?>
                        </p>
                    </div>
                </div>

                <!-- Seller Info & CTA -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 mt-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Seller Information</h3>
                    <div class="flex items-center mb-6">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mr-4 shadow-md">
                            <?php echo strtoupper(substr($data['user']->name, 0, 1)); ?>
                        </div>
                        <div>
                            <p class="font-bold text-lg text-gray-900">
                                <?php echo $data['user']->name; ?>
                            </p>
                            <p class="text-gray-500 text-sm">Member since
                                <?php echo date('Y', strtotime($data['user']->created_at)); ?>
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3" x-data="{ isFavorite: <?php echo json_encode($data['isFavorite']); ?> }">
                        <?php if (isLoggedIn()): ?>
                            <!-- Chat Button -->
                            <?php if ($_SESSION['user_id'] != $data['ad']->user_id): ?>
                                <button
                                    @click="$dispatch('open-chat', { adId: <?php echo $data['ad']->id; ?>, receiverId: <?php echo $data['ad']->user_id; ?> })"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 flex items-center justify-center transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Chat with Seller
                                </button>
                            <?php else: ?>
                                <div
                                    class="w-full bg-gray-200 text-gray-500 font-bold py-3.5 px-4 rounded-xl text-center cursor-not-allowed">
                                    This is your Ad
                                </div>
                            <?php endif; ?>

                            <!-- Favorite Button -->
                            <button @click="
                                fetch('<?php echo URL_ROOT; ?>/listings/toggle_favorite/<?php echo $data['ad']->id; ?>', {
                                    method: 'POST'
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if(data.status === 'success') {
                                        isFavorite = !isFavorite;
                                    }
                                })
                            "
                                class="w-full bg-white hover:bg-gray-50 text-gray-700 font-bold py-3.5 px-4 rounded-xl border border-gray-300 transition-all duration-200 flex items-center justify-center"
                                :class="{ 'text-red-500 border-red-200 bg-red-50': isFavorite }">
                                <svg class="w-5 h-5 mr-2 transition-colors"
                                    :class="{ 'fill-current': isFavorite, 'text-red-500': isFavorite, 'text-gray-400': !isFavorite }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span x-text="isFavorite ? 'Remove from Favorites' : 'Add to Favorites'"></span>
                            </button>

                            <!-- Report Button -->
                            <button @click="showReportModal = true"
                                class="w-full text-center text-gray-400 hover:text-red-600 text-sm font-medium mt-4 transition-colors">
                                Report this Ad
                            </button>

                        <?php else: ?>
                            <a href="<?php echo URL_ROOT; ?>/login"
                                class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl w-full shadow-lg transition duration-200">
                                Login to Contact
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div x-show="showReportModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black opacity-50" @click="showReportModal = false"></div>

        <!-- Modal Content -->
        <div
            class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full relative z-10 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Report Ad</h3>
                <button @click="showReportModal = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="<?php echo URL_ROOT; ?>/reports/add/<?php echo $data['ad']->id; ?>" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Why are you reporting this ad?</label>
                    <textarea name="reason" rows="4"
                        class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-indigo-500"
                        placeholder="Describe the issue (e.g., spam, offensive content, sold item)..."
                        required></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showReportModal = false"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Submit
                        Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>