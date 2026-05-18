<?php
// Define App Constants for CLI execution if needed
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config/config.php';
}

// Database Connection
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/slug_helper.php';

class Seeder
{
    private $db;

    private $categories = [
        'Cars' => [
            'icon' => 'fa-car',
            'subs' => ['Maruti Suzuki', 'Hyundai', 'Tata', 'Toyota', 'Honda', 'Other Brands', 'Spare Parts']
        ],
        'Bikes' => [
            'icon' => 'fa-motorcycle',
            'subs' => ['Motorcycles', 'Scooters', 'Spare Parts', 'Bicycles']
        ],
        'Properties' => [
            'icon' => 'fa-building',
            'subs' => ['For Sale: Houses & Apartments', 'For Rent: Houses & Apartments', 'Lands & Plots', 'For Rent: Shops & Offices', 'For Sale: Shops & Offices', 'PG & Guest Houses']
        ],
        'Electronics & Appliances' => [
            'icon' => 'fa-tv',
            'subs' => ['TVs, Video - Audio', 'Kitchen & Other Appliances', 'Computers & Laptops', 'Cameras & Lenses', 'Games & Entertainment', 'Computer Accessories', 'Hard Disks, Printers & Monitors', 'ACs', 'Washing Machines']
        ],
        'Mobiles' => [
            'icon' => 'fa-mobile-alt',
            'subs' => ['Mobile Phones', 'Accessories', 'Tablets']
        ],
        'Commercial Vehicles & Spares' => [
            'icon' => 'fa-truck',
            'subs' => ['Commercial & Other Vehicles', 'Spare Parts']
        ],
        'Jobs' => [
            'icon' => 'fa-briefcase',
            'subs' => ['Data Entry & Back Office', 'Sales & Marketing', 'BPO & Telecaller', 'Driver', 'Office Assistant', 'Delivery & Collection', 'Teacher', 'Cook', 'Receptionist & Front Office', 'Operator & Technician', 'IT Engineer & Developer', 'Hotel & Travel Executive', 'Accountant', 'Designer', 'Other Jobs']
        ],
        'Furniture' => [
            'icon' => 'fa-couch',
            'subs' => ['Sofa & Dining', 'Beds & Wardrobes', 'Home Decor & Garden', 'Kids Furniture', 'Other Household Items']
        ],
        'Fashion' => [
            'icon' => 'fa-tshirt',
            'subs' => ['Men', 'Women', 'Kids']
        ],
        'Pets' => [
            'icon' => 'fa-paw',
            'subs' => ['Fishes & Aquarium', 'Pet Food & Accessories', 'Dogs', 'Cats', 'Other Pets']
        ],
        'Books, Sports & Hobbies' => [
            'icon' => 'fa-book',
            'subs' => ['Books', 'Gym & Fitness', 'Musical Instruments', 'Sports Equipment', 'Other Hobbies']
        ],
        'Services' => [
            'icon' => 'fa-concierge-bell',
            'subs' => ['Electronics & Computer', 'Education & Classes', 'Drivers & Taxi', 'Health & Beauty', 'Other Services']
        ]
    ];
    // Extensive Unsplash Collection for Realistic Demo Data
    private $categoryImages = [
        'Cars' => [
            'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600', // Car
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=600', // Vintage Car
            'https://images.unsplash.com/photo-1503376763036-066120622c74?w=600', // Luxury Car
            'https://images.unsplash.com/photo-1542362567-b07e54358753?w=600', // Sports Car
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=600', // Chevrolet
            'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=600'  // Ferrari
        ],
        'Motorcycles' => [
            'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=600', // Motorcycle
            'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=600', // Bike
            'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?w=600', // KTM
            'https://images.unsplash.com/photo-1622185135505-2d795043906a?w=600'  // Harley
        ],
        'Mobile Phones' => [
            'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600', // iPhone
            'https://images.unsplash.com/photo-1598327105666-5b89351aff23?w=600', // Android
            'https://images.unsplash.com/photo-1616348436168-de43ad0db179?w=600', // iPhone 12
            'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600', // Mobile in Hand
            'https://images.unsplash.com/photo-1567581935884-3349723552ca?w=600', // Pixel
            'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=600'  // Samsung
        ],
        'For Sale: Houses & Apartments' => [
            'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600', // Apartment Building
            'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=600', // White House
            'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=600', // House
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600'  // Modern House
        ],
        'Scooters' => [
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=600', // Scooter
            'https://images.unsplash.com/photo-1571188654248-7a8921953dd6?w=600', // Vespa
            'https://images.unsplash.com/photo-1541625602330-2277a4c46182?w=600'  // Moped
        ],
        'Commercial & Other Vehicles' => [
            'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=600', // Truck
            'https://images.unsplash.com/photo-1586191582119-2925c6ec1862?w=600', // Van
            'https://images.unsplash.com/photo-1519003722824-194d4455a60c?w=600'  // Tractor
        ],
        'For Rent: Houses & Apartments' => [
            'https://images.unsplash.com/photo-1502005229766-939760a7cb0d?w=600', // Interior
            'https://images.unsplash.com/photo-1484154218962-a1c00207bfbd?w=600', // Living Room
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600', // Apartment
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600'  // Rental
        ],
        'Electronics & Appliances' => [
            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600', // Laptop
            'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=600', // Dell Monitor
            'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=600', // Headphones
            'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=600'  // Circuit board
        ],
        'Furniture' => [
            'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600', // Green Sofa
            'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600', // Chair
            'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=600'  // Wooden Table
        ],
        'Fashion' => [
            'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=600', // Shopping
            'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600', // Model
            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600' // Red Shoes
        ],
        'Books, Sports & Hobbies' => [
            'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600', // Library
            'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=600', // Gym
            'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=600'  // Dumbbells
        ],
        'Pets' => [
            'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=600', // Dog
            'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600', // Cat
            'https://images.unsplash.com/photo-1535591273668-578e31182c4f?w=600'  // Cat 2
        ],
        'Services' => [
            'https://images.unsplash.com/photo-1581092921461-eab62e97a782?w=600', // Mechanic
            'https://images.unsplash.com/photo-1595152772835-219674b2a8a6?w=600', // Moving
            'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=600'  // Repair
        ]
    ];

    private $indianCities = [
        ['state' => 'Maharashtra', 'city' => 'Mumbai'],
        ['state' => 'Delhi', 'city' => 'Delhi'],
        ['state' => 'Karnataka', 'city' => 'Bangalore'],
        ['state' => 'Telangana', 'city' => 'Hyderabad'],
        ['state' => 'Gujarat', 'city' => 'Ahmedabad'],
        ['state' => 'Tamil Nadu', 'city' => 'Chennai'],
        ['state' => 'West Bengal', 'city' => 'Kolkata'],
        ['state' => 'Maharashtra', 'city' => 'Pune'],
        ['state' => 'Rajasthan', 'city' => 'Jaipur'],
        ['state' => 'Uttar Pradesh', 'city' => 'Lucknow']
    ];

    public function __construct()
    {
        $this->db = new Database();
    }

    public function run()
    {
        echo "<pre>";
        echo "Starting Seeder...\n";

        $this->clearData();

        $this->seedLocations();
        $this->seedCategories();
        $this->seedUsers(20);
        $this->seedAds(40);

        // Summary
        $this->db->query("SELECT (SELECT COUNT(*) FROM ads) as ad_count, (SELECT COUNT(*) FROM users) as user_count, (SELECT COUNT(*) FROM categories) as cat_count");
        $counts = $this->db->single();

        echo "\n----------------------------------";
        echo "\nSeeding Completed Successfully!";
        echo "\nTotal Users: " . $counts->user_count;
        echo "\nTotal Categories: " . $counts->cat_count;
        echo "\nTotal Ads: " . $counts->ad_count;
        echo "\n----------------------------------";
        echo "</pre>";
    }

    private function clearData()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->query("TRUNCATE TABLE ads");
        $this->db->query("TRUNCATE TABLE users");
        $this->db->query("TRUNCATE TABLE categories");
        $this->db->query("TRUNCATE TABLE locations");
        $this->db->query("TRUNCATE TABLE messages");
        $this->db->query("TRUNCATE TABLE ad_views");
        $this->db->query("TRUNCATE TABLE login_attempts");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
        echo "Tables Truncated.\n";
    }

    private function seedLocations()
    {
        echo "Seeding Locations...\n";
        foreach ($this->indianCities as $loc) {
            $this->db->query("SELECT id FROM locations WHERE city = :city");
            $this->db->bind(':city', $loc['city']);

            if ($this->db->rowCount() == 0) {
                $this->db->query("INSERT INTO locations (country, state, city) VALUES ('India', :state, :city)");
                $this->db->bind(':state', $loc['state']);
                $this->db->bind(':city', $loc['city']);
                $this->db->execute();
            }
        }
    }

    private function seedCategories()
    {
        echo "Seeding Categories and Subcategories...\n";

        foreach ($this->categories as $catName => $catData) {
            $slug = createSlug($catName);
            $icon = $catData['icon'];

            // Insert Parent Category
            $this->db->query("SELECT id FROM categories WHERE slug = :slug");
            $this->db->bind(':slug', $slug);
            $row = $this->db->single();

            $catId = null;
            if ($row) {
                $catId = $row->id;
            } else {
                $this->db->query("INSERT INTO categories (name, slug, icon) VALUES (:name, :slug, :icon)");
                $this->db->bind(':name', $catName);
                $this->db->bind(':slug', $slug);
                $this->db->bind(':icon', $icon);
                $this->db->execute();
                $catId = $this->db->lastInsertId();
            }

            // Insert Subcategories
            if ($catId && !empty($catData['subs'])) {
                foreach ($catData['subs'] as $subName) {
                    $subSlug = createSlug($subName);

                    $this->db->query("SELECT id FROM subcategories WHERE slug = :slug");
                    $this->db->bind(':slug', $subSlug);

                    if ($this->db->rowCount() == 0) {
                        $this->db->query("INSERT INTO subcategories (category_id, name, slug) VALUES (:cat_id, :name, :slug)");
                        $this->db->bind(':cat_id', $catId);
                        $this->db->bind(':name', $subName);
                        $this->db->bind(':slug', $subSlug);
                        $this->db->execute();
                    }
                }
            }
        }
    }

    private function seedUsers($count)
    {
        echo "Seeding Users...\n";

        $indianNames = [
            'Aarav Patel',
            'Vihaan Shah',
            'Aditya Mehta',
            'Arjun Joshi',
            'Sai Kumar',
            'Rohan Desai',
            'Ishaan Trivedi',
            'Reyansh Parmar',
            'Krishna Wala',
            'Dhruv Solanki',
            'Anaya Gupta',
            'Diya Patel',
            'Saanvi Shah',
            'Kiara Modi',
            'Myra Jha',
            'Priya Iyer',
            'Riya Malhotra',
            'Sneha Kapoor',
            'Anjali Singh',
            'Pooja Verma',
            'Sanjay Dutt',
            'Rahul Dravid',
            'Amitabh Bachchan',
            'Narendra Modi',
            'Mukesh Ambani'
        ];

        // Ensure we have enough names or cycle through them

        $credentialsFile = fopen("user_credentials.txt", "w");
        fwrite($credentialsFile, "--------------------------------------------------\n");
        fwrite($credentialsFile, "GENERATED USER CREDENTIALS (MARKETSPHERE)\n");
        fwrite($credentialsFile, "--------------------------------------------------\n");
        fwrite($credentialsFile, str_pad("NAME", 25) . str_pad("EMAIL", 35) . "PASSWORD\n");
        fwrite($credentialsFile, "--------------------------------------------------\n");

        for ($i = 0; $i < $count; $i++) {
            $name = $indianNames[$i % count($indianNames)];
            // Append number if count > names to avoid duplicates if specific logic requires unique names, 
            // but emails must be unique.
            if ($i >= count($indianNames)) {
                $name .= " " . ($i + 1);
            }

            // Create a realistic email
            $slugName = strtolower(str_replace(' ', '.', $name));
            $email = $slugName . ($i + 1) . "@demo.com";
            $passwordPlain = "password123";
            $password = password_hash($passwordPlain, PASSWORD_DEFAULT);

            // Check if user exists
            $this->db->query("SELECT id FROM users WHERE email = :email");
            $this->db->bind(':email', $email);
            $this->db->single();

            if ($this->db->rowCount() == 0) {
                $this->db->query("INSERT INTO users (name, email, password_hash, is_verified) VALUES (:name, :email, :password, 1)");
                $this->db->bind(':name', $name);
                $this->db->bind(':email', $email);
                $this->db->bind(':password', $password);
                $this->db->execute();
            }

            // Log to file
            fwrite($credentialsFile, str_pad($name, 25) . str_pad($email, 35) . $passwordPlain . "\n");
        }

        fclose($credentialsFile);
        echo "User credentials saved to 'user_credentials.txt'.\n";
    }

    private function seedAds($perCategory)
    {
        echo "Seeding Ads...\n";

        // Get All Categories
        $this->db->query("SELECT * FROM categories");
        $categories = $this->db->resultSet();

        if (empty($categories)) {
            echo "No categories found. Skipping ad generation.\n";
            return;
        }

        // Get All Users
        $this->db->query("SELECT id FROM users");
        $users = $this->db->resultSet();
        $userIds = array_map(function ($u) {
            return $u->id;
        }, $users);

        // Get All Locations
        $this->db->query("SELECT * FROM locations");
        $locations = $this->db->resultSet();

        $conditions = ['new', 'used', 'refurbished'];

        foreach ($categories as $cat) {
            echo " - Generating ads for {$cat->name}...\n";

            // Map old image keys to new keys if needed, or use generic fallback
            // The categoryImages array keys might not match exactly now if I changed names.
            // Let's use a safe fallback.
            $images = $this->categoryImages[$cat->name] ?? $this->categoryImages['Electronics & Appliances']; // Fallback

            if (empty($images))
                $images = ['https://via.placeholder.com/500'];

            for ($i = 0; $i < $perCategory; $i++) {
                $userId = $userIds[array_rand($userIds)];

                $randomLoc = $locations[array_rand($locations)];
                $locId = $randomLoc->id;
                $cityName = $randomLoc->city;

                $title = "{$cat->name} Item - " . uniqid();
                $slug = createSlug($title);
                $price = rand(500, 50000); // 500 to 50000 INR

                $condition = $conditions[array_rand($conditions)];

                $desc = "Experience superior quality with this premium {$cat->name} item. It has been carefully used and well-maintained, providing excellent value for its price. This product is perfect for anyone looking for reliability and performance without breaking the bank.

Key Features:
- High durability and consistent performance
- Modern aesthetic with a premium finish
- Thoroughly cleaned and sanitized
- verified functionality
- Suitable for both personal and professional use

Condition Details:
The item is in {$condition} condition. There are minimal signs of wear (if used), and it functions exactly as intended. I have taken good care of it, ensuring it remains in top-notch shape.

Reason for Selling:
I am upgrading to a newer model and no longer have space for this. It has served me well, and I hope it finds a new owner who will value it just as much.

Location & Availability:
The item is currently located in {$cityName} and is available for immediate pick-up or inspection. I am available most weekends and weekday evenings.

Contact:
Please contact me via the built-in chat for more details, high-res photos, or to schedule a visit. Valid offers will be considered, but please no low-balling.

(This description is auto-generated to simulate a realistic ad experience on MarketSphere, ensuring sufficient text content for layout testing.)";

                // Shuffle images
                $adImages = [$images[array_rand($images)], $images[array_rand($images)]];
                $jsonImages = json_encode($adImages);

                $views = rand(5, 100);
                $days = rand(0, 60);
                $created_at = date('Y-m-d H:i:s', strtotime("-$days days"));

                $this->db->query("INSERT INTO ads (user_id, category_id, location_id, title, slug, description, price, currency, condition_type, status, images, views_count, created_at, expires_at) 
                                  VALUES (:user_id, :cat_id, :loc_id, :title, :slug, :desc, :price, 'INR', :cond, 'active', :images, :views, :created, DATE_ADD(:created, INTERVAL 30 DAY))");

                $this->db->bind(':user_id', $userId);
                $this->db->bind(':cat_id', $cat->id);
                $this->db->bind(':loc_id', $locId);
                $this->db->bind(':title', $title);
                $this->db->bind(':slug', $slug);
                $this->db->bind(':desc', $desc);
                $this->db->bind(':price', $price);
                $this->db->bind(':cond', $condition);
                $this->db->bind(':images', $jsonImages);
                $this->db->bind(':views', $views);
                $this->db->bind(':created', $created_at);

                $this->db->execute();
            }
        }
    }
}

// Run Seeder
$seeder = new Seeder();
$seeder->run();
?>