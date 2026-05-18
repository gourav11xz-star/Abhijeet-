<?php
class DashboardController extends Controller
{
    private $userModel;
    private $adModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }
        $this->userModel = $this->model('User');
        $this->adModel = $this->model('Ad');
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);

        // Fetch user's ads
        // We need a method in Ad model to get ads by user_id efficiently
        // For now, let's add a quick filter to getAds or a new method
        // Using existing getAds with user_id filter would be cleaner if implemented, 
        // but current getAds is for public listing constraints (active only).
        // So we need a specific method for "My Ads" including pending/sold.

        $myAds = $this->adModel->getAdsByUserId($userId);
        $myFavorites = $this->adModel->getFavorites($userId);

        $data = [
            'user' => $user,
            'ads' => $myAds,
            'favorites' => $myFavorites
        ];

        $this->view('dashboard/index', $data);
    }

    public function update_profile()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF
            if (!verify_csrf_token($_POST['csrf_token'])) {
                die('CSRF Validation Failed');
            }

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'id' => $_SESSION['user_id'],
                'name' => trim($_POST['name']),
                'phone' => trim($_POST['phone']),
                'avatar' => '',
                'completion_message' => ''
            ];

            // Handle Avatar Upload
            if (!empty($_FILES['avatar']['name'])) {
                $uploadedAvatar = ImageHelper::uploadImages($_FILES['avatar'], 'uploads/avatars/');
                if (!empty($uploadedAvatar)) {
                    $data['avatar'] = $uploadedAvatar[0];
                }
            }

            if ($this->userModel->updateProfile($data)) {
                // Update Session Name
                $_SESSION['user_name'] = $data['name'];
                flash('profile_message', 'Profile Updated Successfully');
                redirect('dashboard');
            } else {
                die('Something went wrong');
            }
        }
    }

    public function delete_ad($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF ?
            // For simple delete buttons, maybe just check ownership

            $ad = $this->adModel->getAdById($id);

            // Check ownership
            if ($ad->user_id != $_SESSION['user_id']) {
                redirect('dashboard');
            }

            if ($this->adModel->deleteAd($id)) {
                flash('ad_message', 'Ad Removed');
                redirect('dashboard');
            } else {
                die('Something went wrong');
            }
        }
    }
}
