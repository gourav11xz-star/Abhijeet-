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

// 404
$router->set404(function () {
    require_once '../app/views/404.php';
});
