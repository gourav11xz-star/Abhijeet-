<aside class="w-full md:w-1/4 bg-white rounded-lg shadow p-6 h-fit">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Admin Panel</h2>
    <nav class="space-y-2">
        <a href="<?php echo URL_ROOT; ?>/admin"
            class="block px-4 py-2 rounded transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/ads') === false && strpos($_SERVER['REQUEST_URI'], '/users') === false && strpos($_SERVER['REQUEST_URI'], '/categories') === false) ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600'; ?>">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>

        <a href="<?php echo URL_ROOT; ?>/admin/ads"
            class="block px-4 py-2 rounded transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/ads') !== false) ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600'; ?>">
            <i class="fas fa-clipboard-check mr-2"></i> Ad Approvals
            <?php if (isset($data['stats']['pending_ads']) && $data['stats']['pending_ads'] > 0): ?>
                <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    <?php echo $data['stats']['pending_ads']; ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="<?php echo URL_ROOT; ?>/admin/all_ads"
            class="block px-4 py-2 rounded transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/all_ads') !== false) ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600'; ?>">
            <i class="fas fa-folder-open mr-2"></i> All Listings
        </a>

        <a href="<?php echo URL_ROOT; ?>/admin/users"
            class="block px-4 py-2 rounded transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600'; ?>">
            <i class="fas fa-users mr-2"></i> Users
        </a>

        <a href="<?php echo URL_ROOT; ?>/admin/categories"
            class="block px-4 py-2 rounded transition <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false) ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600'; ?>">
            <i class="fas fa-tags mr-2"></i> Categories
        </a>

        <div class="pt-4 mt-4 border-t border-gray-100">
            <a href="<?php echo URL_ROOT; ?>/logout"
                class="block px-4 py-2 rounded text-red-600 hover:bg-red-50 transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </nav>
</aside>