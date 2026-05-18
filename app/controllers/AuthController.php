<?php
class AuthController extends Controller
{
    private $userModel;
    private $loginAttemptModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->loginAttemptModel = $this->model('LoginAttempt');
    }

    public function register()
    {
        if (isLoggedIn()) {
            redirect('');
        }
        $data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];
        $this->view('auth/register', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF
            if (!verify_csrf_token($_POST['csrf_token'])) {
                die('CSRF Validation Failed');
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate Name
            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter name';
            }

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Make sure errors are empty
            if (empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Validated

                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register User
                if ($this->userModel->register($data)) {
                    flash('register_success', 'You are registered and can log in');
                    redirect('login');
                } else {
                    die('Something went wrong');
                }

            } else {
                // Load view with errors
                flash('register_error', 'Please fix the errors below', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4');
                $this->view('auth/register', $data);
            }

        } else {
            $this->register();
        }
    }

    public function login()
    {
        if (isLoggedIn()) {
            redirect('');
        }
        $data = [
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];
        $this->view('auth/login', $data);
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF
            if (!verify_csrf_token($_POST['csrf_token'])) {
                die('CSRF Validation Failed');
            }

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // Check for too many login attempts
            $ip = $_SERVER['REMOTE_ADDR'];
            $attempts = $this->loginAttemptModel->countAttempts($data['email'], $ip);

            if ($attempts >= 5) {
                flash('login_error', 'Too many failed login attempts. Please try again in 15 minutes.', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4');
                $this->view('auth/login', $data);
                return;
            }

            // Check for user/email
            if ($this->userModel->findUserByEmail($data['email'])) {
                // User found
            } else {
                $data['email_err'] = 'No user found';
            }

            // Make sure errors are empty
            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    // Check for Ban
                    if ($loggedInUser->is_banned == 1) {
                        flash('login_error', 'Your account has been suspended. Contact support.', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4');
                        $this->view('auth/login', $data);
                        return;
                    }

                    // Create Session
                    $this->loginAttemptModel->clearAttempts($data['email'], $ip);
                    $this->createUserSession($loggedInUser);
                } else {
                    $this->loginAttemptModel->recordAttempt($data['email'], $ip);
                    flash('login_error', 'Password incorrect', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4');
                    $this->view('auth/login', $data);
                }
            } else {
                flash('login_error', 'No user found with that email', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4');
                $this->view('auth/login', $data);
            }

        } else {
            $this->login();
        }
    }

    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role ?? 'user'; // Default to user if not set
        redirect('');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('login');
    }
}
