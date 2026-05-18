<?php
class ReportsController extends Controller
{
    private $reportModel;
    private $adModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $this->reportModel = $this->model('Report');
        $this->adModel = $this->model('Ad');
    }

    public function index()
    {
        // Admin check usually here
        $reports = $this->reportModel->getReports();

        $data = [
            'reports' => $reports
        ];

        $this->view('reports/index', $data);
    }

    public function add($ad_id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'user_id' => $_SESSION['user_id'],
                'ad_id' => $ad_id,
                'reason' => trim($_POST['reason'])
            ];

            // Validate
            if (empty($data['reason'])) {
                flash('ad_message', 'Please enter a reason for reporting', 'alert-danger');
                redirect('listings/' . $ad_id);
            }

            // Check if user already reported this ad? (Optional, skipping for simplicity)

            if ($this->reportModel->addReport($data)) {
                flash('ad_message', 'Report submitted. We will review it shortly.');
                redirect('listings/' . $ad_id);
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('listings/' . $ad_id);
        }
    }

    // Admin Actions
    public function resolve($id)
    {
        if ($this->reportModel->updateStatus($id, 'resolved')) {
            flash('report_message', 'Report marked as resolved');
        } else {
            flash('report_message', 'Something went wrong', 'alert-danger');
        }
        redirect('reports');
    }

    public function delete($id)
    {
        if ($this->reportModel->deleteReport($id)) {
            flash('report_message', 'Report deleted');
        } else {
            flash('report_message', 'Something went wrong', 'alert-danger');
        }
        redirect('reports');
    }
}
