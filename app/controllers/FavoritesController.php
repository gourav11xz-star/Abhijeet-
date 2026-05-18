<?php
class FavoritesController extends Controller
{
    private $adModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            // For AJAX, we might return 401, but for now redirect or return error in method
        }
        $this->adModel = $this->model('Ad');
    }

    public function toggle()
    {
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'error', 'message' => 'Please login to favorite items']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $adId = isset($data['ad_id']) ? $data['ad_id'] : null;

            if ($adId) {
                $result = $this->adModel->toggleFavorite($adId, $_SESSION['user_id']);
                echo json_encode(['status' => 'success', 'action' => $result]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid Ad ID']);
            }
        }
    }
}
