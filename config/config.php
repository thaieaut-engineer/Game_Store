<?php
// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/Game_Store/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('TOKEN_EXPIRY', 30 * 60); // 30 minutes in seconds

// Load Composer Autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Email configuration for password reset
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: 'your_email@gmail.com');
define('SMTP_PASS', getenv('SMTP_PASS') ?: 'your_app_password');
define('SMTP_FROM', getenv('SMTP_FROM') ?: 'your_email@gmail.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'Game Store');

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../config/',
        __DIR__ . '/../middleware/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helper functions
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateInput($input)
{
    // Check for SQL injection patterns
    $dangerous = ['union', 'select', 'insert', 'update', 'delete', 'drop', 'exec', 'script', '<script', 'javascript:', 'onerror=', 'onload='];
    $input_lower = strtolower($input);
    foreach ($dangerous as $pattern) {
        if (strpos($input_lower, $pattern) !== false) {
            return false;
        }
    }
    return true;
}

function isLoggedIn()
{
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check session first (faster and works immediately after login)
    // Check for user_id and user_name (set immediately after login)
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && !empty($_SESSION['user_id'])) {
        // If we have session data, user is logged in
        // Optionally verify token if it exists
        if (isset($_SESSION['auth_token'])) {
            require_once __DIR__ . '/../models/UserToken.php';
            $tokenModel = new UserToken();
            $user = $tokenModel->validateToken($_SESSION['auth_token']);
            if ($user !== false) {
                return true;
            }
        }
        // Even without token validation, if we have session user data, consider logged in
        // This handles the case right after login before cookie is fully set
        return true;
    }

    // Fallback to cookie check
    if (!isset($_COOKIE['auth_token'])) {
        return false;
    }

    require_once __DIR__ . '/../models/UserToken.php';
    $tokenModel = new UserToken();
    $user = $tokenModel->validateToken($_COOKIE['auth_token']);

    if ($user === false) {
        // Token invalid or expired, clear session
        $_SESSION = [];
        // Clear cookie from both paths
        if (PHP_VERSION_ID >= 70300) {
            setcookie('auth_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            setcookie('auth_token', '', [
                'expires' => time() - 3600,
                'path' => '/Game_Store/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        } else {
            setcookie('auth_token', '', time() - 3600, '/');
            setcookie('auth_token', '', time() - 3600, '/Game_Store/');
        }
        return false;
    }

    // Update session with user data and token for faster future checks
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_avatar'] = $user['avatar'] ?? 'default-avatar.png';
    $_SESSION['auth_token'] = $_COOKIE['auth_token'];

    return true;
}

function getCurrentUser()
{
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check session first for performance (works immediately after login)
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && !empty($_SESSION['user_id'])) {
        // Build user from session - this is fastest and works immediately
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'user',
            'avatar' => $_SESSION['user_avatar'] ?? 'default-avatar.png',
            'status' => 1
        ];

        // If we have token in session, verify it's still valid (optional)
        if (isset($_SESSION['auth_token'])) {
            require_once __DIR__ . '/../models/UserToken.php';
            $tokenModel = new UserToken();
            $validatedUser = $tokenModel->validateToken($_SESSION['auth_token']);
            if ($validatedUser && $validatedUser['id'] == $_SESSION['user_id']) {
                // Return validated user from database (has all fields)
                return $validatedUser;
            }
        }

        // Return user built from session
        return $user;
    }

    // Fallback to cookie check
    if (!isset($_COOKIE['auth_token'])) {
        return null;
    }

    require_once __DIR__ . '/../models/UserToken.php';
    $tokenModel = new UserToken();
    $user = $tokenModel->validateToken($_COOKIE['auth_token']);

    // Cache user info in session if valid
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? 'default-avatar.png';
        $_SESSION['auth_token'] = $_COOKIE['auth_token'];
    }

    return $user;
}

function isAdmin()
{
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit();
}
?>