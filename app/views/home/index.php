<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<!-- Hero Section -->
<div class="relative bg-indigo-600 overflow-hidden">
    <div class="absolute inset-0">
        <img class="w-full h-full object-cover opacity-20"
            src="https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
            alt="Background">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 mix-blend-multiply"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8 flex flex-col items-center text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl mb-6">
            Buy & Sell <span class="text-yellow-300">Anything</span> in Your City
        </h1>
        <p class="mt-2 max-w-xl mx-auto text-xl text-indigo-100 mb-10">
            Join the largest community marketplace. Find great deals on cars, phones, furniture, and more.
        </p>

        <!-- Search Bar -->
        <div class="w-full max-w-3xl">
            <form action="<?php echo URL_ROOT; ?>/listings" method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search"
                        class="block w-full pl-10 pr-3 py-4 border border-transparent rounded-lg leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:bg-white focus:ring-2 focus:ring-white focus:border-white sm:text-lg shadow-lg"
                        placeholder="What are you looking for?" required>
                </div>
                <div class="relative sm:w-1/3">
                    <select name="location_id"
                        class="block w-full py-4 px-4 border border-transparent rounded-lg leading-5 bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-white focus:border-white sm:text-lg shadow-lg appearance-none">
                        <option value="">All Locations</option>
                        <!-- Ideally populate this dynamically, but for hero clean look, leave generic or fetch if possible. 
                             We didn't pass locations to Home, so let's stick to simple text or just Search button. -->
                        <option value="1">Mumbai</option>
                        <option value="2">Delhi</option>
                        <option value="3">Bangalore</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
                <button type="submit"
                    class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-bold rounded-lg text-indigo-700 bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transition-transform transform hover:scale-105">
                    Search
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">Browse Categories</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <?php foreach ($data['categories'] as $category): ?>
            <a href="<?php echo URL_ROOT; ?>/listings?category_id=<?php echo $category->id; ?>" class="group block">
                <div
                    class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all duration-200 group-hover:-translate-y-1">
                    <div
                        class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-200">
                        <!-- Icon Placeholder (dynamic based on name ideally) -->
                        <span class="text-2xl font-bold">
                            <?php echo substr($category->name, 0, 1); ?>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-700 group-hover:text-indigo-600">
                        <?php echo $category->name; ?>
                    </h3>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Fresh Recommendations -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Fresh Recommendations</h2>
            <a href="<?php echo URL_ROOT; ?>/listings"
                class="text-indigo-600 font-bold hover:text-indigo-800 flex items-center">
                View All
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                    </path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($data['recent'] as $ad): ?>
                <?php
                // Image handling
                $images = json_decode($ad->images);
                $imgSrc = !empty($images) ? (strpos($images[0], 'http') === 0 ? $images[0] : URL_ROOT . '/' . $images[0]) : 'https://via.placeholder.com/300x200?text=No+Image';
                ?>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100 group">
                    <a href="<?php echo URL_ROOT; ?>/listings/<?php echo $ad->id; ?>">
                        <div class="relative h-48 bg-gray-200 overflow-hidden">
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo $ad->title; ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php if ($ad->is_featured): ?>
                                <span
                                    class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded">FEATURED</span>
                            <?php endif; ?>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent p-4">
                                <span class="text-white font-bold text-lg">
                                    <?php echo $ad->currency . ' ' . number_format($ad->price); ?>
                                </span>
                            </div>
                        </div>
                    </a>
                    <div class="p-4">
                        <a href="<?php echo URL_ROOT; ?>/listings/<?php echo $ad->id; ?>" class="block">
                            <h3
                                class="text-lg font-bold text-gray-900 truncate mb-1 group-hover:text-indigo-600 transition-colors">
                                <?php echo $ad->title; ?>
                            </h3>
                        </a>
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-3">
                            <span class="truncate pr-2">
                                <?php echo $ad->location_city ?? 'Unknown City'; ?>
                            </span>
                            <span class="whitespace-nowrap">
                                <?php echo date('M d', strtotime($ad->created_at)); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Features / Trust Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="p-6">
                <div
                    class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Verified Sellers</h3>
                <p class="text-gray-600">Shop with confidence knowing our community is safe and moderated.</p>
            </div>
            <div class="p-6">
                <div
                    class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Secure Transactions</h3>
                <p class="text-gray-600">Connect directly with sellers/buyers and negotiate securely on your terms.</p>
            </div>
            <div class="p-6">
                <div
                    class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Fast & Easy</h3>
                <p class="text-gray-600">Post an ad in seconds and reach thousands of buyers instantly.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>