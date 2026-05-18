-- Static Seed Data for MarketSphere

-- Locations
INSERT INTO locations (country, state, city) VALUES 
('India', 'Maharashtra', 'Mumbai'),
('India', 'Delhi', 'Delhi'),
('India', 'Karnataka', 'Bangalore'),
('India', 'Telangana', 'Hyderabad'),
('India', 'Gujarat', 'Ahmedabad'),
('India', 'Tamil Nadu', 'Chennai'),
('India', 'West Bengal', 'Kolkata'),
('India', 'Maharashtra', 'Pune'),
('India', 'Rajasthan', 'Jaipur'),
('India', 'Uttar Pradesh', 'Lucknow');

-- Categories
INSERT INTO categories (name, slug, icon) VALUES 
('Electronics', 'electronics', 'fa-laptop'),
('Mobiles', 'mobiles', 'fa-mobile'),
('Vehicles', 'vehicles', 'fa-car'),
('Furniture', 'furniture', 'fa-couch'),
('Fashion', 'fashion', 'fa-tshirt'),
('Books & Education', 'books-education', 'fa-book'),
('Home Appliances', 'home-appliances', 'fa-blender'),
('Real Estate', 'real-estate', 'fa-home'),
('Jobs', 'jobs', 'fa-briefcase'),
('Sports & Fitness', 'sports-fitness', 'fa-dumbbell');

-- Sample Users
INSERT INTO users (name, email, password_hash, is_verified, created_at) VALUES 
('Demo Seller', 'seller@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW()),
('Demo Buyer', 'buyer@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW());

-- Run public/seeder.php for full dataset generation (400+ Ads)
