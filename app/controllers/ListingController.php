<?php
class ListingController extends Controller
{
    private $adModel;
    private $categoryModel;
    private $locationModel;

    public function __construct()
    {
        $this->adModel = $this->model('Ad');
        $this->categoryModel = $this->model('Category');
        $this->locationModel = $this->model('Location');
    }

    public function index()
    {
        // Initial load only needs structure
        $categories = $this->categoryModel->getCategories();
        $locations = $this->locationModel->getLocations();

        $data = [
            'categories' => $categories,
            'locations' => $locations
        ];

        $this->view('listings/index', $data);
    }

    // AJAX Endpoint for fetching listings
    public function fetch()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit;
        }

        $filter = [
            'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'category_id' => isset($_GET['category_id']) ? (int) $_GET['category_id'] : null,
            'location_id' => isset($_GET['location_id']) ? (int) $_GET['location_id'] : null,
            'min_price' => isset($_GET['min_price']) ? (float) $_GET['min_price'] : null,
            'max_price' => isset($_GET['max_price']) ? (float) $_GET['max_price'] : null,
            'limit' => isset($_GET['limit']) ? (int) $_GET['limit'] : 24,
            'offset' => isset($_GET['offset']) ? (int) $_GET['offset'] : 0,
        ];

        // Remove nulls or empty strings
        $filter = array_filter($filter, fn($value) => !is_null($value) && $value !== '');

        $ads = $this->adModel->getAds($filter);

        // Add is_favorite status
        if (isLoggedIn()) {
            foreach ($ads as $ad) {
                $ad->is_favorite = $this->adModel->isFavorite($ad->id, $_SESSION['user_id']);
            }
        } else {
            foreach ($ads as $ad) {
                $ad->is_favorite = false;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['ads' => $ads]);
        exit;
    }

    public function create()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $categories = $this->categoryModel->getCategories();
        $locations = $this->locationModel->getLocations();

        $data = [
            'title' => '',
            'description' => '',
            'price' => '',
            'currency' => 'USD',
            'category_id' => '',
            'location_id' => '',
            'condition' => 'used',
            'categories' => $categories,
            'locations' => $locations,
            'errors' => []
        ];

        $this->view('listings/create', $data);
    }

    public function store()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF
            if (!verify_csrf_token($_POST['csrf_token'])) {
                die('CSRF Validation Failed');
            }

            // Sanitize
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'category_id' => trim($_POST['category_id']),
                'subcategory_id' => null, // Todo
                'location_id' => trim($_POST['location_id']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'currency' => 'INR',
                'condition' => trim($_POST['condition']),
                'slug' => createSlug($_POST['title']),
                'status' => 'pending', // Default to pending for moderation
                'images' => '[]',
                'errors' => []
            ];

            // Validate
            if (empty($data['title'])) {
                $data['errors']['title'] = 'Please enter title';
            }
            if (empty($data['description'])) {
                $data['errors']['description'] = 'Please enter description';
            }
            if (empty($data['price'])) {
                $data['errors']['price'] = 'Please enter price';
            }
            if (empty($data['category_id'])) {
                $data['errors']['category_id'] = 'Please select category';
            }
            if (empty($data['location_id'])) {
                $data['errors']['location_id'] = 'Please select location';
            }

            // Handle Image Upload
            $uploadedImages = [];
            if (isset($_FILES['images'])) {
                // If any files selected
                if (!empty($_FILES['images']['name'][0])) {
                    // Use Helper
                    $uploadedImages = ImageHelper::uploadImages($_FILES['images']);
                }
            }

            $data['images'] = json_encode($uploadedImages);


            if (empty($data['errors'])) {
                if ($this->adModel->addAd($data)) {
                    flash('ad_message', 'Ad Posted Successfully');
                    redirect('listings');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Reload with errors
                $data['categories'] = $this->categoryModel->getCategories();
                $data['locations'] = $this->locationModel->getLocations();
                $this->view('listings/create', $data);
            }

        } else {
            $this->create();
        }
    }

    public function show($id)
    {
        $ad = $this->adModel->getAdById($id);

        // Check if ad exists
        if (!$ad) {
            redirect('listings');
        }

        $relatedAds = [];

        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $viewStmt = $pdo->prepare("UPDATE ads SET view_count = COALESCE(view_count, 0) + 1 WHERE id = :id");
            $viewStmt->execute([':id' => $id]);

            $countStmt = $pdo->prepare("SELECT COALESCE(view_count, 0) FROM ads WHERE id = :id");
            $countStmt->execute([':id' => $id]);
            $ad->view_count = (int)$countStmt->fetchColumn();

            $relStmt = $pdo->prepare("
                SELECT id, title, price, currency, images, created_at
                FROM ads
                WHERE category_id = :category_id
                  AND id != :id
                  AND status = 'active'
                ORDER BY created_at DESC
                LIMIT 4
            ");
            $relStmt->execute([
                ':category_id' => $ad->category_id,
                ':id' => $id
            ]);
            $relatedAds = $relStmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $relatedAds = [];
            if (!isset($ad->view_count)) {
                $ad->view_count = 0;
            }
        }

        $user = $this->model('User')->getUserById($ad->user_id);

        // Ensure user obj exists
        if (!$user) {
            $user = (object) ['name' => 'Unknown User', 'created_at' => date('Y-m-d H:i:s')];
        }

        $isFavorite = false;
        if (isLoggedIn()) {
            $isFavorite = $this->adModel->isFavorite($id, $_SESSION['user_id']);
        }

        $data = [
            'ad' => $ad,
            'user' => $user,
            'isFavorite' => $isFavorite,
              'related_ads' => $relatedAds
        ];

        $this->view('listings/show', $data);
    }

    public function edit($id)
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $ad = $this->adModel->getAdById($id);

        // Check ownership
        if ($ad->user_id != $_SESSION['user_id']) {
            redirect('listings');
        }

        $categories = $this->categoryModel->getCategories();
        $locations = $this->locationModel->getLocations();

        $data = [
            'id' => $id,
            'title' => $ad->title,
            'description' => $ad->description,
            'price' => $ad->price,
            'category_id' => $ad->category_id,
            'location_id' => $ad->location_id,
            'condition' => $ad->condition_type,
            'current_images' => $ad->images, // Pass existing images
            'categories' => $categories,
            'locations' => $locations,
            'errors' => []
        ];

        $this->view('listings/edit', $data);
    }

    public function update($id)
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF
            if (!verify_csrf_token($_POST['csrf_token'])) {
                die('CSRF Validation Failed');
            }

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $ad = $this->adModel->getAdById($id);
            if ($ad->user_id != $_SESSION['user_id']) {
                redirect('listings');
            }

            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'category_id' => trim($_POST['category_id']),
                'location_id' => trim($_POST['location_id']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'condition' => trim($_POST['condition']),
                'slug' => createSlug($_POST['title']),
                'status' => 'pending', // Reset to pending on update
                'user_id' => $_SESSION['user_id'],
                'images' => $ad->images, // Default to existing
                'errors' => []
            ];

            // Validate (Simple check)
            if (empty($data['title']))
                $data['errors']['title'] = 'Please enter title';
            if (empty($data['description']))
                $data['errors']['description'] = 'Please enter description';
            if (empty($data['price']))
                $data['errors']['price'] = 'Please enter price';

            // Handle New Image Upload
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploadedImages = ImageHelper::uploadImages($_FILES['images']);
                if (!empty($uploadedImages)) {
                    // Logic: Append or Replace? 
                    // User said "not allow any photos to update", implying replace or add is desired.
                    // Let's Replace for simplicity if new ones are uploaded, OR merge if specialized UI existed.
                    // For typical "Edit", if user uploads new files, they usually expect them to serve as the new set or append.
                    // Given the simple file input, let's Append new to old, up to a limit, OR Replace completely.
                    // Most user-friendly simple logic: If you upload new, ADD them. If you want to delete, we need a mechanism.
                    // Let's implement REPLACE for now as it's cleaner without a complex UI to delete individual existing ones.
                    // Wait, user said "update". If I just append, they can't remove old ones.
                    // If I replace, they lose all old ones if they just wanted to add one.
                    // Let's go with: NEW uploads REPLACE old ones. If they want to keep old ones, they shouldn't upload new ones (or we need a delete UI).
                    // Actually, a "Delete previous images?" checkbox is easier.
                    // But standard simple behavior: If new files uploaded, assume they want those.
                    // Let's keep existing and add new for now. Unless a "clear_images" flag is sent.

                    // Revised: Append. AND add a checkbox in View to "Clear existing images".

                    $currentImages = json_decode($ad->images, true) ?? [];
                    if (isset($_POST['clear_images'])) {
                        $currentImages = [];
                    }

                    $data['images'] = json_encode(array_merge($currentImages, $uploadedImages));
                }
            } else {
                if (isset($_POST['clear_images'])) {
                    $data['images'] = '[]';
                }
            }


            if (empty($data['errors'])) {
                if ($this->adModel->updateAd($data)) {
                    // Also reset status to pending manually via separate call if updateAd doesn't do it?
                    // Or modify updateAd? 
                    // Let's modify Ad model to update status too, OR call updateStatus here.
                    // Calling separate method is easier for now to avoid changing Ad model signature too much if not passed.
                    $this->adModel->updateStatus($id, 'pending');

                    flash('ad_message', 'Ad Updated. It is now pending approval.');
                    redirect('listings/' . $id);
                } else {
                    die('Something went wrong');
                }
            } else {
                // Reload with errors
                $data['categories'] = $this->categoryModel->getCategories();
                $data['locations'] = $this->locationModel->getLocations();
                $data['current_images'] = $ad->images; // Keep showing original on error
                $this->view('listings/edit', $data);
            }

        } else {
            $this->edit($id);
        }
    }

    public function toggle_favorite($id)
    {
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        // Verify POST (optional but good practice)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->adModel->toggleFavorite($id, $_SESSION['user_id']);
            echo json_encode(['status' => 'success', 'action' => $result]);
        }
    }
}
