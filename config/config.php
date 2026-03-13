<?php
// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false)
            continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!empty($name) && !array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
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

// Dynamic BASE_URL detection
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '/Game_Store/index.php';
$base_dir = str_replace('index.php', '', $script_name);
define('BASE_URL', $protocol . '://' . $host . $base_dir);

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
    if ($input === null || $input === '')
        return true;
    // Check for SQL injection patterns
    $dangerous = ['union', 'select', 'insert', 'update', 'delete', 'drop', 'exec', 'script', '<script', 'javascript:', 'onerror=', 'onload='];
    $input_lower = strtolower((string) $input);
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

    require_once __DIR__ . '/../models/UserToken.php';
    $tokenModel = new UserToken();

    // 1. Ưu tiên kiểm tra token đang lưu trong session (nếu có)
    if (!empty($_SESSION['auth_token'])) {
        $user = $tokenModel->validateToken($_SESSION['auth_token']);
        if ($user) {
            // Đồng bộ lại thông tin user vào session (phòng trường hợp user đổi tên/avatar)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? 'default-avatar.png';
            return true;
        }

        // Token trong session không còn hợp lệ (bị xóa khi đăng nhập nơi khác / hết hạn)
        $_SESSION = [];
        // Clear cookie từ cả 2 path
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

    // 2. Nếu chưa có auth_token trong session, fallback sang cookie
    if (empty($_COOKIE['auth_token'])) {
        return false;
    }

    $user = $tokenModel->validateToken($_COOKIE['auth_token']);

    if ($user === false) {
        // Token invalid or expired, clear session & cookie
        $_SESSION = [];
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

    // 3. Token từ cookie hợp lệ -> cập nhật session
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

    require_once __DIR__ . '/../models/UserToken.php';
    $tokenModel = new UserToken();

    // 1. Nếu có auth_token trong session thì validate trực tiếp
    if (!empty($_SESSION['auth_token'])) {
        $user = $tokenModel->validateToken($_SESSION['auth_token']);
        if ($user) {
            // Đồng bộ lại session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? 'default-avatar.png';

            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar'] ?? 'default-avatar.png',
                'status' => $user['status'] ?? 1,
            ];
        }

        // Token trong session không còn hợp lệ
        $_SESSION = [];
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
        return null;
    }

    // 2. Nếu không có auth_token trong session, thử từ cookie
    if (empty($_COOKIE['auth_token'])) {
        return null;
    }

    $user = $tokenModel->validateToken($_COOKIE['auth_token']);
    if ($user === false) {
        return null;
    }

    // Lưu lại vào session để những lần sau dùng nhanh hơn
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_avatar'] = $user['avatar'] ?? 'default-avatar.png';
    $_SESSION['auth_token'] = $_COOKIE['auth_token'];

    return [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'avatar' => $user['avatar'] ?? 'default-avatar.png',
        'status' => $user['status'] ?? 1,
    ];
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