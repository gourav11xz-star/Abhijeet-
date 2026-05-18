<?php require_once APP_ROOT . '/views/inc/header.php'; ?>



<div x-data="listingsApp()" x-init="initApp()" class="container mx-auto px-4 pb-12">
    <!-- Category Grid Section -->
    <div class="mb-12">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Browse Categories</h2>

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-6 gap-6">
            <?php
            // Define the specific categories we want to show and their images/labels
            $displayCategories = [
                'cars' => ['image' => 'cars.png', 'label' => 'Cars'],
                'bikes' => ['image' => 'bikes.png', 'label' => 'Bikes'],
                'properties' => ['image' => 'properties.png', 'label' => 'Properties'],
                'electronics-appliances' => ['image' => 'electronics-appliances.png', 'label' => 'Electronics & Appliances'],
                'mobiles' => ['image' => 'mobiles.png', 'label' => 'Mobiles'],
                'commercial-vehicles-spares' => ['image' => 'commercial-vehicles-spares.png', 'label' => 'Commercial Vehicles & Spares'],
                'furniture' => ['image' => 'furniture.png', 'label' => 'Furniture'],
                'fashion' => ['image' => 'fashion.png', 'label' => 'Fashion'],
                'books-sports-hobbies' => ['image' => 'books-sports-hobbies.png', 'label' => 'Books, Sports & Hobbies'],
                'pets' => ['image' => 'pets.png', 'label' => 'Pets'],
                'services' => ['image' => 'services.png', 'label' => 'Services'],
                'jobs' => ['image' => 'jobs.png', 'label' => 'Jobs']
            ];
            ?>
            <?php foreach ($displayCategories as $slug => $details): ?>
                <?php
                // Find the matching category object from the database data to get the ID (if needed for typical flow, though we use slugs in links)
                // We iterate through our DEFINED list ($displayCategories) to control the order and prevent duplicates 
                // The link will use the slug directly.
                ?>
                <a href="<?php echo URL_ROOT; ?>/listings?category=<?php echo $slug; ?>"
                    class="flex flex-col items-center group cursor-pointer transition-transform transform hover:-translate-y-1">
                    <div
                        class="w-20 h-20 md:w-24 md:h-24 bg-white rounded-2xl flex items-center justify-center overflow-hidden mb-3 shadow-sm border border-gray-100 group-hover:shadow-md group-hover:border-indigo-200 transition-all">
                        <img src="<?php echo URL_ROOT; ?>/assets/img/categories/<?php echo $details['image']; ?>"
                            alt="<?php echo $details['label']; ?>"
                            class="w-16 h-16 md:w-20 md:h-20 object-contain transition-transform duration-300 group-hover:scale-110">
                    </div>
                    <span
                        class="text-xs md:text-sm font-semibold text-gray-800 text-center leading-tight group-hover:text-indigo-700 max-w-[100px]">
                        <?php echo $details['label']; ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-8">

        <!-- Sidebar Filters -->
        <aside class="w-full md:w-1/4">
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 sticky top-24">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Filters</h3>
                    <button @click="resetFilters()"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Reset</button>
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                    <div class="relative">
                        <select x-model="filters.category_id" @change="fetchAds(true)"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50 appearance-none cursor-pointer">
                            <option value="">All Categories</option>
                            <?php foreach ($data['categories'] as $category): ?>
                                <option value="<?php echo $category->id; ?>">
                                    <?php echo $category->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                    <div class="relative">
                        <select x-model="filters.location_id" @change="fetchAds(true)"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50 appearance-none cursor-pointer">
                            <option value="">All India</option>
                            <?php foreach ($data['locations'] as $location): ?>
                                <option value="<?php echo $location->id; ?>">
                                    <?php echo $location->city; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Price Range (₹)</label>
                    <div class="flex items-center space-x-2">
                        <div class="relative w-1/2">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">₹</span>
                            <input x-model="filters.min_price" @input.debounce.500ms="fetchAds(true)" type="number"
                                placeholder="Min"
                                class="w-full pl-6 pr-2 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50 text-sm">
                        </div>
                        <span class="text-gray-400">-</span>
                        <div class="relative w-1/2">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">₹</span>
                            <input x-model="filters.max_price" @input.debounce.500ms="fetchAds(true)" type="number"
                                placeholder="Max"
                                class="w-full pl-6 pr-2 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-3/4">
            <!-- Header -->
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Fresh Recommendations</h2>
                    <p class="text-gray-500 text-sm mt-1">Handpicked deals for you</p>
                </div>
                <div class="text-gray-500 text-sm font-medium bg-gray-100 px-3 py-1 rounded-full">Showing <span
                        x-text="ads.length" class="text-indigo-600 font-bold"></span> results</div>
            </div>

            <!-- Loading State -->
            <div x-show="loading && ads.length === 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="i in 6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-pulse">
                        <div class="h-48 bg-gray-200"></div>
                        <div class="p-5">
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                            <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Ad Grid -->
            <div x-show="!loading || ads.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="ad in ads" :key="ad.id">
                    <a :href="'<?php echo URL_ROOT; ?>/listings/' + ad.id"
                        class="group bg-white rounded border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col h-full relative">

                        <!-- Featured Ribbon -->
                        <div x-show="ad.is_featured == 1"
                            class="absolute top-0 left-0 bg-yellow-400 text-xs font-bold px-2 py-1 z-10 uppercase tracking-wider shadow-sm">
                            Featured
                        </div>

                        <!-- Heart Icon -->
                        <div @click.prevent="toggleFavorite(ad.id)"
                            class="absolute top-3 right-3 z-10 bg-white rounded-full p-1.5 shadow-sm cursor-pointer hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 transition-colors duration-200"
                                :class="ad.is_favorite ? 'text-red-500 fill-current' : 'text-gray-400 hover:text-red-500'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                        </div>

                        <!-- Image Container (4:3 Aspect Ratio) -->
                        <div class="relative w-full pb-[75%] bg-gray-200"> <!-- pb-[75%] = 4:3 aspect ratio -->
                            <img :src="getImageUrl(ad.images)" class="absolute top-0 left-0 w-full h-full object-cover"
                                alt="Ad Image">
                        </div>

                        <!-- Content -->
                        <div
                            class="p-4 flex flex-col flex-grow border-l-4 border-l-transparent hover:border-l-indigo-500 transition-all">

                            <!-- Price -->
                            <h3 class="font-bold text-2xl text-gray-900 mb-1 font-sans">
                                <span x-text="formatPrice(ad.price)"></span>
                            </h3>

                            <!-- Title -->
                            <p class="text-gray-700 text-sm leading-snug mb-2 line-clamp-2" x-text="ad.title"></p>

                            <div class="mt-auto">
                                <!-- Meta Info (Location & Date) -->
                                <div
                                    class="flex justify-between items-center text-[10px] text-gray-500 uppercase tracking-wide mt-3 pt-3 border-t border-gray-100">
                                    <span class="truncate pr-2 max-w-[60%]" x-text="ad.city ? ad.city : 'INDIA'"></span>
                                    <span class="whitespace-nowrap" x-text="formatDate(ad.created_at)"></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && ads.length === 0"
                class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No results found</h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto">We couldn't find any listings matching your search. Try
                    different keywords or filters.</p>
                <button @click="resetFilters()"
                    class="text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg px-5 py-2.5 transition-colors">
                    Clear all filters
                </button>
            </div>

            <!-- Infinite Scroll & Loading State -->
            <div class="mt-8 mb-12">
                <!-- Loading Spinner (Visible whilst loading) -->
                <div x-show="loading" class="flex justify-center items-center py-4">
                    <div class="flex items-center space-x-2 text-indigo-600">
                        <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="font-bold text-lg">Loading more deals...</span>
                    </div>
                </div>

                <!-- Trigger (Visible when NOT loading and HAS more) -->
                <div x-show="!loading && hasMore" x-intersect.threshold.05="loadMore()"
                    class="h-24 flex justify-center items-center text-gray-400">
                    <span>Scroll for more results</span>
                </div>

                <!-- End of Results -->
                <div x-show="!loading && !hasMore && ads.length > 0" class="text-center text-gray-500 py-8">
                    <p>✨ You've seen all the deals!</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('listings', {
            fetchAds: null, // Will be linked
            filters: {}
        });
    });

    function listingsApp() {
        return {
            ads: [],
            loading: false,
            hasMore: true,
            filters: {
                search: '',
                category_id: '',
                location_id: '',
                min_price: '',
                max_price: '',
                offset: 0,
                limit: 24
            },

            initApp() {
                // Link store for global access (e.g. from Hero search)
                if (Alpine.store('listings')) {
                    Alpine.store('listings').fetchAds = this.fetchAds.bind(this);
                    Alpine.store('listings').filters = this.filters;
                }

                // Initialize Categories from PHP
                const categories = <?php echo json_encode($data['categories']); ?>;

                // Parse URL for Category Slug mapping
                const urlParams = new URLSearchParams(window.location.search);
                const categorySlug = urlParams.get('category');

                if (categorySlug) {
                    // Try to find category by slug (if available) or loosely by name
                    // Since we don't have slugs in the standard output yet, let's try to match by name or add slug support
                    // But wait, the seeder uses slugs. Let's assume the categories object has slugs if the DB supports it.
                    // If not, we map commonly used slugs to names.

                    const foundCategory = categories.find(c => {
                        // Create a slug from the name to compare
                        const nameSlug = c.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                        return nameSlug === categorySlug || (c.slug && c.slug === categorySlug);
                    });

                    if (foundCategory) {
                        this.filters.category_id = foundCategory.id;
                    }
                }

                this.fetchAds();
            },

            fetchAds(reset = false) {
                if (reset) {
                    this.filters.offset = 0;
                    this.ads = [];
                    this.hasMore = true;
                }

                if (this.loading || (!this.hasMore && !reset)) return;

                this.loading = true;

                // Build Query String
                const params = new URLSearchParams();
                for (const key in this.filters) {
                    if (this.filters[key] !== null && this.filters[key] !== '') {
                        params.append(key, this.filters[key]);
                    }
                }

                fetch('<?php echo URL_ROOT; ?>/listings/fetch?' + params.toString())
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error fetching ads:', data.error);
                            return;
                        }

                        if (data.ads.length < this.filters.limit) {
                            this.hasMore = false;
                        }

                        if (reset) {
                            this.ads = data.ads;
                        } else {
                            this.ads = [...this.ads, ...data.ads];
                        }

                        this.filters.offset += this.filters.limit;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.loading = false;
                    });
            },

            loadMore() {
                if (this.loading || !this.hasMore) return;
                this.filters.offset += this.filters.limit;
                this.fetchAds(false);
            },

            resetFilters() {
                this.filters.search = '';
                this.filters.category_id = '';
                this.filters.location_id = '';
                this.filters.min_price = '';
                this.filters.max_price = '';
                this.fetchAds(true);
            },

            getImageUrl(images) {
                if (!images) return 'https://via.placeholder.com/400x300?text=No+Image';
                try {
                    const imageArray = typeof images === 'string' ? JSON.parse(images) : images;
                    if (imageArray && imageArray.length > 0) {
                        // Check if it's a URL or a local path
                        let img = imageArray[0];
                        if (img.startsWith('http')) return img;

                        // Remove leading slash if present to avoid double slashes with URL_ROOT
                        if (img.startsWith('/')) img = img.substring(1);

                        // If it's just a filename (no uploads/ads prefix), assume uploads/ads/
                        // But wait, the helper saves as 'uploads/ads/filename'.
                        // Let's safe check.
                        return '<?php echo URL_ROOT; ?>/' + img;
                    }
                } catch (e) {
                    console.error('Error parsing images JSON', e);
                    return 'https://via.placeholder.com/400x300?text=No+Image';
                }
                return 'https://via.placeholder.com/400x300?text=No+Image';
            },

            formatPrice(price) {
                // Indian Rupee Formatting
                return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(price);
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffTime = Math.abs(now - date);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays <= 7) {
                    return diffDays === 1 ? 'Yesterday' : diffDays + ' days ago';
                }
                if (diffDays === 0) return 'Today';

                return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
            },

            isNew(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffTime = Math.abs(now - date);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                return diffDays <= 3;
            },

            async toggleFavorite(adId) {
                // Optimistic UI update could happen here, but strictly we should check login status first.
                // For now, let's call API.
                try {
                    const response = await fetch('<?php echo URL_ROOT; ?>/favorites/toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ ad_id: adId })
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        // Find the ad in the array and toggle its is_favorite property
                        // Note: Our current fetched ads might not have 'is_favorite' property initialized from DB yet.
                        // We need to ensure fetchAds includes it, or we just handle it locally.
                        // For now, let's toggle a local state if we had it, but standard grid redraws might lose it unless supported by data.

                        // Let's assume we want to just visually toggle the button clicked. 
                        // But since we are inside an x-for loop, we need to update the data source.
                        const adIndex = this.ads.findIndex(a => a.id == adId);
                        if (adIndex !== -1) {
                            // Initialize if undefined
                            this.ads[adIndex].is_favorite = (data.action === 'added');
                        }
                    } else {
                        if (data.message.includes('login')) {
                            window.location.href = '<?php echo URL_ROOT; ?>/login';
                        } else {
                            alert(data.message);
                        }
                    }
                } catch (error) {
                    console.error('Error toggling favorite:', error);
                }
            }
        }
    }
</script>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>