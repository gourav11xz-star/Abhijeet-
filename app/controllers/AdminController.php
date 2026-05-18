<?php
class AdminController extends Controller
{
    private $adModel;
    private $userModel;
    private $categoryModel;
    private $settingModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        // Admin Role Check
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            redirect('');
        }

        $this->adModel = $this->model('Ad');
        $this->userModel = $this->model('User');
        $this->categoryModel = $this->model('Category');
        $this->settingModel = $this->model('Setting');
    }

    public function index()
    {
        $stats = $this->adModel->getStats();

        $data = [
            'stats' => $stats
        ];

        $this->view('admin/index', $data);
    }

    public function ads()
    {
        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 10; // Ads per page
        $offset = ($page - 1) * $limit;

        // Get total ads count for pagination
        $stats = $this->adModel->getStats();
        $totalAds = $stats['total_ads'];
        $totalPages = ceil($totalAds / $limit);

        // Get paginated ads
        $ads = $this->adModel->getAllAds($limit, $offset);

        $data = [
            'ads' => $ads,
            'page' => $page,
            'total_pages' => $totalPages
        ];

        $this->view('admin/ads', $data);
    }

    public function reports()
    {
        $this->view('admin/reports');
    }

    public function users()
    {
        $users = $this->userModel->getUsers();

        $data = [
            'users' => $users
        ];

        $this->view('admin/users', $data);
    }

    public function approve_ad($id)
    {
        if ($this->adModel->updateStatus($id, 'active')) {
            flash('admin_message', 'Ad Approved');
        } else {
            flash('admin_message', 'Something went wrong', 'alert-danger');
        }
        redirect('admin/ads');
    }

    public function reject_ad($id)
    {
        if ($this->adModel->updateStatus($id, 'rejected')) {
            flash('admin_message', 'Ad Rejected');
        } else {
            flash('admin_message', 'Something went wrong', 'alert-danger');
        }
        redirect('admin/ads');
    }

    public function delete_user($id)
    {
        if ($this->userModel->deleteUser($id)) {
            flash('admin_message', 'User Deleted');
        } else {
            flash('admin_message', 'Could not delete user', 'alert-danger');
        }
        redirect('admin/users');
    }

    public function ban_user($id)
    {
        if ($this->userModel->banUser($id)) {
            flash('admin_message', 'User Banned');
        } else {
            flash('admin_message', 'Something went wrong', 'alert-danger');
        }
        redirect('admin/users');
    }

    public function unban_user($id)
    {
        if ($this->userModel->unbanUser($id)) {
            flash('admin_message', 'User Unbanned');
        } else {
            flash('admin_message', 'Something went wrong', 'alert-danger');
        }
        redirect('admin/users');
    }

    // Category Management
    public function categories()
    {
        $categories = $this->categoryModel->getCategories();

        $data = [
            'categories' => $categories
        ];

        $this->view('admin/categories', $data);
    }

    public function add_category()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'name' => trim($_POST['name']),
                'slug' => strtolower(str_replace(' ', '-', trim($_POST['name']))), // Simple slugify
                'icon' => trim($_POST['icon']) // Expecting FA class or similar
            ];

            if (!empty($data['name'])) {
                if ($this->categoryModel->addCategory($data)) {
                    flash('admin_message', 'Category Added');
                } else {
                    flash('admin_message', 'Something went wrong', 'alert-danger');
                }
            } else {
                flash('admin_message', 'Category name is required', 'alert-danger');
            }
            redirect('admin/categories');
        } else {
            redirect('admin/categories');
        }
    }

    public function delete_category($id)
    {
        if ($this->categoryModel->deleteCategory($id)) {
            flash('admin_message', 'Category Deleted');
        } else {
            flash('admin_message', 'Could not delete category. It might be in use.', 'alert-danger');
        }
        redirect('admin/categories');
    }

    // Site Settings
    public function settings()
    {
        $settings = $this->settingModel->getSettings();

        $data = [
            'settings' => $settings
        ];

        $this->view('admin/settings', $data);
    }

    public function update_settings()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // Loop through POST data and update each setting
            foreach ($_POST as $key => $value) {
                // Skip non-setting fields if any (though loop is safe if form inputs match keys)
                // We assume input names match setting keys
                $this->settingModel->updateSetting($key, $value);
            }

            flash('admin_message', 'Settings Updated');
            redirect('admin/settings');
        } else {
            redirect('admin/settings');
        }
    }
}
