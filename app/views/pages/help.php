<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">How can we help you?</h1>
        <div class="max-w-xl mx-auto relative">
            <input type="text" placeholder="Search for help topics..."
                class="w-full px-6 py-4 rounded-full shadow-md border-2 border-transparent focus:border-indigo-500 focus:outline-none text-gray-700">
            <button class="absolute right-2 top-2 bg-indigo-600 text-white rounded-full p-2 hover:bg-indigo-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <!-- Card 1 -->
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="font-bold text-lg mb-2">Account & Profile</h3>
            <p class="text-gray-500 text-sm mb-4">Managing your account settings, password reset, and profile info.</p>
            <a href="#" class="text-indigo-600 font-semibold hover:underline">View Articles &rarr;</a>
        </div>

        <!-- Card 2 -->
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
            <div
                class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <h3 class="font-bold text-lg mb-2">Buying & Selling</h3>
            <p class="text-gray-500 text-sm mb-4">How to post ads, contact sellers, and stay safe while trading.</p>
            <a href="#" class="text-indigo-600 font-semibold hover:underline">View Articles &rarr;</a>
        </div>

        <!-- Card 3 -->
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
            <div
                class="w-16 h-16 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h3 class="font-bold text-lg mb-2">Safety & Security</h3>
            <p class="text-gray-500 text-sm mb-4">Tips for safe transactions, reporting fraud, and policy info.</p>
            <a href="#" class="text-indigo-600 font-semibold hover:underline">View Articles &rarr;</a>
        </div>
    </div>

    <div
        class="mt-16 bg-white p-8 rounded-xl shadow-sm border border-indigo-100 flex flex-col md:flex-row items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Still need help?</h3>
            <p class="text-gray-600">Our support team is available 24/7 to assist you.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="#"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-transform transform hover:-translate-y-1">Contact
                Support</a>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>