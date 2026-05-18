<nav class="bg-gray-100 border-b border-gray-200 sticky top-0 z-50">
    <!-- Top Bar -->
    <div class="bg-gray-100 py-2 hidden md:block">
        <div class="container mx-auto px-4 flex justify-between items-center text-xs text-gray-500">
            <div class="flex items-center space-x-4">
                <a href="<?php echo URL_ROOT; ?>/pages/corporate" class="hover:text-gray-800"><?= htmlspecialchars(get_setting('site_name', 'MarketSphere')) ?> Corporate
                    Information</a>
                <a href="<?php echo URL_ROOT; ?>/pages/help" class="hover:text-gray-800">Help</a>
            </div>
            <!-- Language (Static for now) -->
            <div class="flex items-center font-bold text-gray-700">
                <span class="mr-2">English</span>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <div class="bg-white shadow-sm py-3">
        <div class="container mx-auto px-4 flex flex-wrap md:flex-nowrap items-center justify-between gap-4">

            <!-- Logo -->
            <a href="<?php echo URL_ROOT; ?>" class="flex-shrink-0">
                <h1 class="text-3xl font-extrabold tracking-tighter text-gray-900">
                    <?= htmlspecialchars(get_setting('site_name', 'MarketSphere')) ?>
                </h1>
            </a>

            <!-- Location Search (Mockup) -->
            <div
                class="hidden md:flex items-center bg-white border-2 border-gray-800 rounded px-2 py-2.5 w-64 hover:border-indigo-500 transition-colors cursor-pointer group">
                <svg class="w-5 h-5 text-gray-800 group-hover:text-indigo-500 mr-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" value="India"
                    class="w-full text-gray-800 font-medium focus:outline-none cursor-pointer" readonly>
                <svg class="w-5 h-5 text-gray-800 group-hover:text-indigo-500 ml-2 transform transition-transform group-hover:rotate-180"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <!-- Main Search -->
            <div class="flex-grow order-last md:order-none w-full md:w-auto flex h-12 mt-3 md:mt-0">
                <div class="flex-grow relative">
                    <input type="text" placeholder="Find Cars, Mobile Phones and more..."
                        class="w-full h-full border-2 border-gray-800 rounded-l px-4 text-gray-800 placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                        x-data
                        @input.debounce.500ms="$store.listings.filters.search = $el.value; $store.listings.fetchAds(true)">
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-800 px-6 rounded-r text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>

            <!-- User Actions -->
            <div class="flex items-center space-x-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="relative group" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center space-x-1 text-gray-800 font-bold hover:text-indigo-600">
                            <!-- Avatar -->
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-1 overflow-hidden">
                                <?php if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar']): ?>
                                    <img src="<?php echo URL_ROOT . '/' . $_SESSION['user_avatar']; ?>"
                                        class="w-full h-full object-cover">
                                <?php else: ?>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" x-transition
                            class="absolute right-0 top-full mt-2 w-56 bg-white rounded shadow-xl border border-gray-100 z-50 overflow-hidden"
                            style="display: none;">
                            <div class="p-4 border-b border-gray-100">
                                <p class="text-sm text-gray-500">Hello,</p>
                                <p class="font-bold text-lg text-gray-900 truncate"><?php echo $_SESSION['user_name']; ?>
                                </p>
                                <a href="<?php echo URL_ROOT; ?>/dashboard"
                                    class="text-xs text-indigo-600 hover:underline mt-1 block">View and edit profile</a>
                            </div>
                            <a href="<?php echo URL_ROOT; ?>/dashboard"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 text-gray-700">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                                My Ads
                            </a>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                <a href="<?php echo URL_ROOT; ?>/admin"
                                    class="flex items-center px-4 py-3 hover:bg-gray-50 text-indigo-700 font-bold bg-indigo-50">
                                    <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Admin Panel
                                </a>
                            <?php endif; ?>
                            <button @click="$dispatch('open-chat', { adId: null, receiverId: null })"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 text-gray-700 w-full text-left relative"
                                x-data="{ unreadCount: 0 }" x-init="
                                    fetch('<?php echo URL_ROOT; ?>/chat/api_get_unread_count')
                                        .then(res => res.json())
                                        .then(data => unreadCount = data.count);
                                    setInterval(() => {
                                        fetch('<?php echo URL_ROOT; ?>/chat/api_get_unread_count')
                                            .then(res => res.json())
                                            .then(data => unreadCount = data.count);
                                    }, 10000);
                                ">
                                <div class="relative">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                        </path>
                                    </svg>
                                    <span x-show="unreadCount > 0" x-text="unreadCount"
                                        class="absolute -top-2 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white"></span>
                                </div>
                                Chat
                            </button>
                            <a href="<?php echo URL_ROOT; ?>/logout"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 text-gray-700 border-t border-gray-100">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo URL_ROOT; ?>/login"
                        class="font-bold text-gray-800 hover:text-indigo-600 hover:underline underline-offset-4">Login</a>
                <?php endif; ?>

                <!-- Sell Button -->
                <a href="<?php echo URL_ROOT; ?>/listings/create" class="relative group inline-block">
                    <div
                        class="flex items-center justify-center w-24 h-12 bg-white rounded-full border-4 border-t-indigo-500 border-l-indigo-600 border-b-indigo-700 border-r-indigo-500 shadow-lg group-hover:shadow-xl transition-all transform group-hover:-translate-y-0.5">
                        <span class="font-bold text-lg text-gray-800 uppercase tracking-widest">+ SELL</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-2 flex items-center gap-4 text-sm font-medium overflow-x-auto no-scrollbar">
        <div
            class="flex items-center bg-indigo-600 text-white font-bold uppercase cursor-pointer whitespace-nowrap px-4 py-2 rounded-full shadow-md hover:bg-indigo-700 transition-colors mr-2">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
            ALL CATEGORIES
        </div>

        <!-- Cars -->
        <a href="<?php echo URL_ROOT; ?>/listings?category=cars"
            class="text-gray-800 hover:text-indigo-600 hover:underline whitespace-nowrap px-2">Cars</a>

        <!-- Motorcycles -->
        <a href="<?php echo URL_ROOT; ?>/listings?category=motorcycles"
            class="text-gray-800 hover:text-indigo-600 hover:underline whitespace-nowrap px-2">Motorcycles</a>

        <!-- Mobile Phones -->
        <a href="<?php echo URL_ROOT; ?>/listings?category=mobile-phones"
            class="text-gray-800 hover:text-indigo-600 hover:underline whitespace-nowrap px-2">Mobile Phones</a>

        <!-- For Sale: Houses & Apartments -->
        <a href="<?php echo URL_ROOT; ?>/listings?category=for-sale-houses-apartments"
            class="text-gray-800 hover:text-indigo-600 hover:underline whitespace-nowrap px-2">For Sale: Houses &
            Apartments</a>

        <!-- Scooters -->
        <a href="<?php echo URL_ROOT; ?>/listings?category=scooters"
            class="text-gray-800 hover:text-indigo-600 hover:underline whitespace-nowrap px-2">Scooters</a>

        <!-- Commercial & Other Vehicles -->
        <a href="<?php echo URL_ROOT; ?>/listings?category=commercial-vehicles"
            class="text-gray-800 hover:text-indigo-600 hover:underline whitespace-nowrap px-2">Commercial & Other
            Vehicles</a>

        <!-- For Rent: Houses & Apartments -->
        <a href="<?php echo URL_ROOT; ?>/listings?category=for-rent-houses-apartments"
            class="text-gray-800 hover:text-indigo-600 hover:underline whitespace-nowrap px-2">For Rent: Houses &
            Apartments</a>
    </div>
</nav>