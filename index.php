<?php
require_once __DIR__ . '/config/config.php';

// Clean expired tokens
require_once __DIR__ . '/models/UserToken.php';
$tokenModel = new UserToken();
$tokenModel->cleanExpiredTokens();

// Get route from URL
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = str_replace('index.php', '', $scriptName);
$route = str_replace($basePath, '', $requestUri);
$route = trim($route, '/');
$route = explode('?', $route)[0]; // Remove query string

// Route mapping
$routes = [
    '' => ['HomeController', 'index'],
    'game' => ['GameController', 'index'],
    'category' => ['CategoryController', 'index'],
    'game/detail' => ['GameController', 'detail'],
    'about' => ['AboutController', 'index'],
    'support' => ['SupportController', 'index'],
    'tutorial' => ['TutorialController', 'index'],
    'tutorial/ask' => ['TutorialController', 'ask'],
    'auth/login' => ['AuthController', 'login'],
    'auth/register' => ['AuthController', 'register'],
    'auth/logout' => ['AuthController', 'logout'],
    'auth/forgot-password' => ['AuthController', 'forgotPassword'],
    'auth/reset-password' => ['AuthController', 'resetPassword'],
    'cart' => ['CartController', 'index'],
    'cart/add' => ['CartController', 'add'],
    'cart/update' => ['CartController', 'update'],
    'cart/remove' => ['CartController', 'remove'],
    'order/checkout' => ['OrderController', 'checkout'],
    'order/history' => ['OrderController', 'history'],
    'order/detail' => ['OrderController', 'detail'],
    'review/create' => ['ReviewController', 'create'],
    'profile' => ['ProfileController', 'index'],
    'profile/update' => ['ProfileController', 'update'],
    'profile/change-password' => ['ProfileController', 'changePassword'],
    'admin/dashboard' => ['AdminController', 'dashboard'],
    'admin/game' => ['AdminGameController', 'index'],
    'admin/game/create' => ['AdminGameController', 'create'],
    'admin/game/edit' => ['AdminGameController', 'edit'],
    'admin/game/delete' => ['AdminGameController', 'delete'],
    'admin/category' => ['AdminCategoryController', 'index'],
    'admin/category/create' => ['AdminCategoryController', 'create'],
    'admin/category/edit' => ['AdminCategoryController', 'edit'],
    'admin/category/delete' => ['AdminCategoryController', 'delete'],
    'admin/user' => ['AdminUserController', 'index'],
    'admin/user/create' => ['AdminUserController', 'create'],
    'admin/user/edit' => ['AdminUserController', 'edit'],
    'admin/user/delete' => ['AdminUserController', 'delete'],
    'admin/order' => ['AdminOrderController', 'index'],
    'admin/order/detail' => ['AdminOrderController', 'detail'],
    'admin/order/update-status' => ['AdminOrderController', 'updateStatus'],
    'admin/review' => ['AdminReviewController', 'index'],
    'admin/review/delete' => ['AdminReviewController', 'delete'],
    'library' => ['LibraryController', 'index']
];

// Find matching route
$controller = null;
$action = null;

if (isset($routes[$route])) {
    $controller = $routes[$route][0];
    $action = $routes[$route][1];
} else {
    // Try to match dynamic routes
    if (preg_match('/^game\/detail/', $route)) {
        $controller = 'GameController';
        $action = 'detail';
    } else {
        http_response_code(404);
        die('404 - Page not found');
    }
}

// Load and execute controller
if ($controller && $action) {
    $controllerFile = __DIR__ . '/controllers/' . $controller . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerInstance = new $controller();
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            http_response_code(404);
            die('404 - Action not found');
        }
    } else {
        http_response_code(404);
        die('404 - Controller not found');
    }
} else {
    http_response_code(404);
    die('404 - Route not found');
}
?>