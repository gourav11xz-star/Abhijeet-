<?php
// Route definitions

// Home
$router->get('/', 'HomeController@index');

// Auth
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@authenticate');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@store');
$router->get('/logout', 'AuthController@logout');

// Pages
$router->get('/pages/corporate', 'PagesController@corporate');
$router->get('/pages/help', 'PagesController@help');

// Listings
$router->get('/listings', 'ListingController@index');
$router->get('/listings/fetch', 'ListingController@fetch');
$router->get('/listings/create', 'ListingController@create');
$router->post('/listings/create', 'ListingController@store');
$router->get('/listings/([0-9]+)', function ($id) {
    require_once '../app/controllers/ListingController.php';
    $controller = new ListingController();
    $controller->show($id);
});
$router->post('/listings/toggle_favorite/([0-9]+)', function ($id) {
    require_once '../app/controllers/ListingController.php';
    $controller = new ListingController();
    $controller->toggle_favorite($id);
});
$router->get('/listings/edit/([0-9]+)', function ($id) {
    require_once '../app/controllers/ListingController.php';
    $controller = new ListingController();
    $controller->edit($id);
});
$router->post('/listings/update/([0-9]+)', function ($id) {
    require_once '../app/controllers/ListingController.php';
    $controller = new ListingController();
    $controller->update($id);
});

// Admin Routes
$router->get('/admin', 'AdminController@index');
$router->get('/admin/login', 'AdminController@login');
$router->post('/admin/authenticate', 'AdminController@authenticate');
$router->get('/admin/ads', 'AdminController@ads');
$router->get('/admin/all_ads', 'AdminController@all_ads');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/reports', 'AdminController@reports');

$router->get('/admin/categories', 'AdminController@categories');

// Admin Action Routes
$router->post('/admin/add_category', 'AdminController@add_category');
$router->get('/admin/delete_category/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->delete_category($id);
});
$router->get('/admin/delete_user/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->delete_user($id);
});
$router->get('/admin/delete_ad/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->delete_ad($id);
});

$router->get('/admin/approve_ad/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->approve_ad($id);
});
$router->get('/admin/reject_ad/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->reject_ad($id);
});

// Admin User Management
$router->get('/admin/ban_user/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->ban_user($id);
});
$router->get('/admin/unban_user/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->unban_user($id);
});


$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings', 'AdminController@update_settings');

// Admin Category Management
$router->get('/admin/categories', 'AdminController@categories');
$router->post('/admin/add_category', 'AdminController@add_category');
$router->get('/admin/delete_category/([0-9]+)', function ($id) {
    require_once '../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->delete_category($id);
});


$router->post('/chat/delete_conversation', function () {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Login required']);
        return;
    }

    $adId = (int)($_POST['ad_id'] ?? 0);
    $otherUserId = (int)($_POST['other_user_id'] ?? 0);
    $userId = (int)$_SESSION['user_id'];

    if ($adId <= 0 || $otherUserId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        return;
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("
            DELETE FROM messages
            WHERE ad_id = :ad_id
            AND (
                (sender_id = :user_id AND receiver_id = :other_user_id)
                OR
                (sender_id = :other_user_id AND receiver_id = :user_id)
            )
        ");

        $stmt->execute([
            ':ad_id' => $adId,
            ':user_id' => $userId,
            ':other_user_id' => $otherUserId
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Chat deleted for both sides']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
});


$router->post('/user/ping_status', function () {
    header('Content-Type: application/json');

    if (!isLoggedIn()) {
        echo json_encode(['status' => 'error']);
        return;
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("UPDATE users SET last_seen = NOW() WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error']);
    }
});

$router->get('/user/status', function () {
    header('Content-Type: application/json');

    $userId = (int)($_GET['user_id'] ?? 0);

    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'online' => false]);
        return;
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("
            SELECT last_seen,
            CASE 
                WHEN last_seen IS NOT NULL AND last_seen >= (NOW() - INTERVAL 2 MINUTE) 
                THEN 1 ELSE 0 
            END AS online_status
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        echo json_encode([
            'status' => 'success',
            'online' => $user && (int)$user->online_status === 1,
            'last_seen' => $user ? $user->last_seen : null
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'online' => false]);
    }
});


$router->get('/listings/mark_sold/([0-9]+)', function ($id) {
    if (!isLoggedIn()) {
        redirect('login');
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("UPDATE ads SET status = 'sold' WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $id,
            ':user_id' => $_SESSION['user_id']
        ]);

        flash('ad_message', 'Ad marked as sold');
    } catch (Exception $e) {
        flash('ad_message', 'Could not mark as sold', 'alert-danger');
    }

    redirect('dashboard');
});


$router->post('/reports/add/([0-9]+)', function ($adId) {
    if (!isLoggedIn()) {
        redirect('login');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('listings/' . $adId);
    }

    $reason = trim($_POST['reason'] ?? '');
    $comments = trim($_POST['comments'] ?? '');

    if ($reason === '') {
        flash('ad_message', 'Please select a reason for reporting', 'alert-danger');
        redirect('listings/' . $adId);
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("
            INSERT INTO reports (reporter_id, ad_id, reason, comments, status, created_at, updated_at)
            VALUES (:reporter_id, :ad_id, :reason, :comments, 'pending', NOW(), NOW())
        ");

        $stmt->execute([
            ':reporter_id' => $_SESSION['user_id'],
            ':ad_id' => $adId,
            ':reason' => $reason,
            ':comments' => $comments
        ]);

        flash('ad_message', 'Report submitted successfully');
    } catch (Exception $e) {
        flash('ad_message', 'Could not submit report: ' . $e->getMessage(), 'alert-danger');
    }

    redirect('listings/' . $adId);
});

$router->get('/admin/reports/resolve/([0-9]+)', function ($id) {
    if (!isAdmin()) {
        redirect('login');
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("UPDATE reports SET status='resolved', updated_at=NOW() WHERE id=:id");
        $stmt->execute([':id' => $id]);

        flash('admin_message', 'Report resolved');
    } catch (Exception $e) {
        flash('admin_message', 'Could not resolve report', 'alert-danger');
    }

    redirect('admin/reports');
});

$router->get('/admin/reports/delete/([0-9]+)', function ($id) {
    if (!isAdmin()) {
        redirect('login');
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("DELETE FROM reports WHERE id=:id");
        $stmt->execute([':id' => $id]);

        flash('admin_message', 'Report deleted');
    } catch (Exception $e) {
        flash('admin_message', 'Could not delete report', 'alert-danger');
    }

    redirect('admin/reports');
});

// 404
$router->get('/chat', 'ChatController@index');
$router->get('/chat/poll', 'ChatController@poll');
$router->get('/chat/conversation/([0-9]+)/([0-9]+)', function ($adId, $otherUserId) {
    require_once '../app/controllers/ChatController.php';
    $controller = new ChatController();
    $controller->conversation($adId, $otherUserId);
});
$router->post('/chat/send', 'ChatController@send');
$router->get('/chat/unread_count', 'ChatController@unread_count');

// Chat Popup API
$router->get('/chat/api_get_messages', 'ChatController@api_get_messages');
$router->post('/chat/api_send_message', 'ChatController@api_send_message');
$router->get('/chat/api_get_conversations', 'ChatController@api_get_conversations');
$router->get('/chat/api_get_unread_count', 'ChatController@api_get_unread_count');
$router->get('/dashboard', 'DashboardController@index');
$router->post('/dashboard/update_profile', 'DashboardController@update_profile');
$router->post('/dashboard/delete_ad/([0-9]+)', function ($id) {
    require_once '../app/controllers/DashboardController.php';
    $controller = new DashboardController();
    $controller->delete_ad($id);
});










$router->get('/listings/mark_sold/([0-9]+)', function ($id) {
    if (!isLoggedIn()) {
        redirect('login');
    }

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("UPDATE ads SET status = 'sold' WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $id,
            ':user_id' => $_SESSION['user_id']
        ]);

        flash('ad_message', 'Ad marked as sold');
    } catch (Exception $e) {
        flash('ad_message', 'Could not mark as sold', 'alert-danger');
    }

    redirect('dashboard');
});








// 404
$router->set404(function () {
    require_once '../app/views/404.php';
});
