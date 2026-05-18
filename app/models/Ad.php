<?php
class Ad extends Model
{
    public function getAds($filter = [])
    {
        $sql = "SELECT ads.*, 
                       users.name as user_name, 
                       users.avatar as user_avatar,
                       categories.name as category_name, 
                       subcategories.name as subcategory_name,
                       locations.city as location_city,
                       locations.state as location_state
                FROM ads
                JOIN users ON ads.user_id = users.id
                JOIN categories ON ads.category_id = categories.id
                LEFT JOIN subcategories ON ads.subcategory_id = subcategories.id
                LEFT JOIN locations ON ads.location_id = locations.id
                WHERE ads.deleted_at IS NULL AND ads.status = 'active'";

        // Apply filters
        if (!empty($filter['category_id'])) {
            $sql .= " AND ads.category_id = :category_id";
        }
        if (!empty($filter['location_id'])) {
            $sql .= " AND ads.location_id = :location_id";
        }
        if (!empty($filter['min_price'])) {
            $sql .= " AND ads.price >= :min_price";
        }
        if (!empty($filter['max_price'])) {
            $sql .= " AND ads.price <= :max_price";
        }
        if (!empty($filter['search'])) {
            $sql .= " AND (ads.title LIKE :search OR ads.description LIKE :search)";
        }
        // Location Text Search (City/State)
        if (!empty($filter['location_text'])) {
            $sql .= " AND (locations.city LIKE :location_text OR locations.state LIKE :location_text)";
        }

        // Sorting
        $sql .= " ORDER BY ads.is_featured DESC, ads.created_at DESC";

        // Pagination
        if (isset($filter['limit'])) {
            $sql .= " LIMIT " . (int) $filter['limit'];
            if (isset($filter['offset'])) {
                $sql .= " OFFSET " . (int) $filter['offset'];
            }
        }

        $this->db->query($sql);

        // Bind filter params
        if (!empty($filter['category_id'])) {
            $this->db->bind(':category_id', $filter['category_id']);
        }
        if (!empty($filter['location_id'])) {
            $this->db->bind(':location_id', $filter['location_id']);
        }
        if (!empty($filter['min_price'])) {
            $this->db->bind(':min_price', $filter['min_price']);
        }
        if (!empty($filter['max_price'])) {
            $this->db->bind(':max_price', $filter['max_price']);
        }
        if (!empty($filter['search'])) {
            $this->db->bind(':search', '%' . $filter['search'] . '%');
        }
        if (!empty($filter['location_text'])) {
            $this->db->bind(':location_text', '%' . $filter['location_text'] . '%');
        }

        return $this->db->resultSet();
    }

    public function addAd($data)
    {
        $this->db->query('INSERT INTO ads (user_id, category_id, subcategory_id, location_id, title, slug, description, price, currency, condition_type, status, images, expires_at) VALUES (:user_id, :category_id, :subcategory_id, :location_id, :title, :slug, :description, :price, :currency, :condition, :status, :images, :expires_at)');

        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':subcategory_id', $data['subcategory_id']);
        $this->db->bind(':location_id', $data['location_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':currency', $data['currency']);
        $this->db->bind(':condition', $data['condition']);
        $this->db->bind(':status', 'active'); // Default active for now
        $this->db->bind(':images', $data['images']);

        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        $this->db->bind(':expires_at', $expires);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getAdById($id)
    {
        $this->db->query('SELECT ads.*, users.name as user_name, users.created_at as user_created_at, locations.city, locations.state 
                          FROM ads 
                          JOIN users ON ads.user_id = users.id 
                          LEFT JOIN locations ON ads.location_id = locations.id
                          WHERE ads.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get all ads by a specific user (including pending, sold, etc)
    public function getAdsByUserId($userId)
    {
        $sql = "SELECT ads.*, 
                       categories.name as category_name
                FROM ads
                JOIN categories ON ads.category_id = categories.id
                WHERE ads.user_id = :user_id AND ads.deleted_at IS NULL
                ORDER BY ads.created_at DESC";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function deleteAd($id)
    {
        // Soft delete
        $this->db->query('UPDATE ads SET deleted_at = NOW(), status = "rejected" WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Check if ad is favorited by user
    public function isFavorite($adId, $userId)
    {
        $this->db->query('SELECT id FROM favorites WHERE ad_id = :ad_id AND user_id = :user_id');
        $this->db->bind(':ad_id', $adId);
        $this->db->bind(':user_id', $userId);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    // Toggle favorite
    public function toggleFavorite($adId, $userId)
    {
        if ($this->isFavorite($adId, $userId)) {
            // Remove
            $this->db->query('DELETE FROM favorites WHERE ad_id = :ad_id AND user_id = :user_id');
            $this->db->bind(':ad_id', $adId);
            $this->db->bind(':user_id', $userId);
            $this->db->execute();
            return 'removed';
        } else {
            // Add
            $this->db->query('INSERT INTO favorites (ad_id, user_id) VALUES (:ad_id, :user_id)');
            $this->db->bind(':ad_id', $adId);
            $this->db->bind(':user_id', $userId);
            $this->db->execute();
            return 'added';
        }
    }

    // Get all ads favorited by a user
    public function getFavorites($userId)
    {
        $this->db->query('SELECT ads.*, 
                                 categories.name as category_name, 
                                 users.name as user_name,
                                 locations.city, 
                                 locations.state
                          FROM favorites
                          JOIN ads ON favorites.ad_id = ads.id
                          JOIN users ON ads.user_id = users.id
                          JOIN categories ON ads.category_id = categories.id
                          LEFT JOIN locations ON ads.location_id = locations.id
                          WHERE favorites.user_id = :user_id AND ads.deleted_at IS NULL
                          ORDER BY favorites.created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function updateAd($data)
    {
        $this->db->query('UPDATE ads SET 
                        title = :title, 
                        slug = :slug, 
                        category_id = :category_id, 
                        location_id = :location_id, 
                        description = :description, 
                        price = :price, 
                        condition_type = :condition, 
                        images = :images 
                        WHERE id = :id');

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':location_id', $data['location_id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':condition', $data['condition']);
        $this->db->bind(':images', $data['images']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Admin Methods
    public function getPendingAds()
    {
        $this->db->query("SELECT ads.*, users.name as user_name, categories.name as category_name 
                          FROM ads 
                          JOIN users ON ads.user_id = users.id 
                          JOIN categories ON ads.category_id = categories.id 
                          WHERE ads.status = 'pending' 
                          ORDER BY ads.created_at ASC");
        return $this->db->resultSet();
    }

    public function getAllAds($limit = null, $offset = 0)
    {
        $sql = "SELECT ads.*, users.name as user_name, categories.name as category_name 
                          FROM ads 
                          JOIN users ON ads.user_id = users.id 
                          JOIN categories ON ads.category_id = categories.id 
                          ORDER BY ads.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
        }

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function updateStatus($id, $status)
    {
        $this->db->query('UPDATE ads SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function getStats()
    {
        $stats = [];

        // Total Users
        $this->db->query('SELECT COUNT(*) as count FROM users');
        $stats['total_users'] = $this->db->single()->count;

        // Total Ads
        $this->db->query('SELECT COUNT(*) as count FROM ads');
        $stats['total_ads'] = $this->db->single()->count;

        // Pending Ads
        $this->db->query('SELECT COUNT(*) as count FROM ads WHERE status = "pending"');
        $stats['pending_ads'] = $this->db->single()->count;

        return $stats;
    }
}
