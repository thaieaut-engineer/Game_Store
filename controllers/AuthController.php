<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserToken.php';
require_once __DIR__ . '/../models/PasswordReset.php';

class AuthController
{
    private $userModel;
    private $tokenModel;
    private $resetModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->tokenModel = new UserToken();
        $this->resetModel = new PasswordReset();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($name) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
                redirect('auth/register');
            }

            if (!validateInput($name) || !validateInput($email)) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ';
                redirect('auth/register');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email không hợp lệ';
                redirect('auth/register');
            }

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Mật khẩu xác nhận không khớp';
                redirect('auth/register');
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
                redirect('auth/register');
            }

            // Check if email exists
            if ($this->userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Email đã được sử dụng';
                redirect('auth/register');
            }

            // Create user
            if ($this->userModel->create($name, $email, $password)) {
                $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập';
                redirect('auth/login');
            } else {
                $_SESSION['error'] = 'Đăng ký thất bại';
                redirect('auth/register');
            }
        } else {
            require_once __DIR__ . '/../views/auth/register.php';
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
                redirect('auth/login');
            }

            if (!validateInput($email)) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ';
                redirect('auth/login');
            }

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] == 0) {
                    $_SESSION['error'] = 'Tài khoản đã bị khóa';
                    redirect('auth/login');
                }

                // Create token
                $token = bin2hex(random_bytes(32));
                $this->tokenModel->create($user['id'], $token);

                // Ensure session is started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Store user info in session for quick access BEFORE redirect
                // This ensures header can read user info immediately after redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_avatar'] = $user['avatar'] ?? 'default-avatar.png';
                $_SESSION['login_time'] = time();
                $_SESSION['auth_token'] = $token; // Store in session as backup

                // Set success message
                $_SESSION['success'] = 'Đăng nhập thành công! Chào mừng ' . $user['name'] . ' trở lại!';

                // Set cookie (30 minutes) with secure options
                // Try both paths to ensure cookie is set
                if (PHP_VERSION_ID >= 70300) {
                    // Set cookie for root path
                    setcookie('auth_token', $token, [
                        'expires' => time() + TOKEN_EXPIRY,
                        'path' => '/',
                        'domain' => '',
                        'secure' => false, // Set to true if using HTTPS
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                    // Also set for Game_Store path
                    setcookie('auth_token', $token, [
                        'expires' => time() + TOKEN_EXPIRY,
                        'path' => '/Game_Store/',
                        'domain' => '',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                } else {
                    // Fallback for older PHP versions
                    setcookie('auth_token', $token, time() + TOKEN_EXPIRY, '/', '', false, true);
                    setcookie('auth_token', $token, time() + TOKEN_EXPIRY, '/Game_Store/', '', false, true);
                }

                // Also set in $_COOKIE for immediate access
                $_COOKIE['auth_token'] = $token;

                // DON'T close session - let it persist for the redirect
                // Session will be automatically saved when script ends

                if ($user['role'] === 'admin') {
                    redirect('admin/dashboard');
                } else {
                    $redirectUrl = $_GET['redirect'] ?? '';
                    if (!empty($redirectUrl)) {
                        header("Location: " . urldecode($redirectUrl));
                        exit();
                    }
                    redirect('');
                }
            } else {
                $_SESSION['error'] = 'Email hoặc mật khẩu không đúng';
                redirect('auth/login');
            }
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout()
    {
        if (isset($_COOKIE['auth_token'])) {
            $this->tokenModel->deleteToken($_COOKIE['auth_token']);
            // Xóa cookie trên cả 2 path để chắc chắn
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
        }

        // Clear session
        $_SESSION = [];
        session_destroy();

        redirect('auth/login');
    }

    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email không hợp lệ';
                redirect('auth/forgot-password');
            }

            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                $_SESSION['error'] = 'Email không tồn tại';
                redirect('auth/forgot-password');
            }

            $token = bin2hex(random_bytes(32));
            $this->resetModel->create($email, $token);

            // Construct reset link
            $resetLink = BASE_URL . "auth/reset-password?token=" . $token;

            // Send real email using PHPMailer
            $subject = "Đặt lại mật khẩu - Game Store";
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;'>
                    <h2 style='color: #1b2838;'>Yêu cầu đặt lại mật khẩu</h2>
                    <p>Chào bạn,</p>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại <strong>Game Store</strong>. Vui lòng nhấn vào nút dưới đây để thực hiện:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='$resetLink' style='display: inline-block; padding: 12px 24px; background-color: #22c55e; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>Đặt lại mật khẩu</a>
                    </div>
                    <p>Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này. Link này sẽ hết hạn sau 1 giờ.</p>
                    <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                    <p style='color: #6b7280; font-size: 12px;'>Đây là email tự động, vui lòng không phản hồi.</p>
                </div>
            ";

            if (Mail::send($email, $subject, $body)) {
                $_SESSION['success'] = 'Link đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư (bao gồm cả thư mục Spam).';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi gửi mail. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.';
            }
            redirect('auth/login');
        } else {
            require_once __DIR__ . '/../views/auth/forgot-password.php';
        }
    }

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION['error'] = 'Token không hợp lệ';
            redirect('auth/login');
        }

        $reset = $this->resetModel->validateToken($token);
        if (!$reset) {
            $_SESSION['error'] = 'Token đã hết hạn hoặc không hợp lệ';
            redirect('auth/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($password) || $password !== $confirmPassword) {
                $_SESSION['error'] = 'Mật khẩu không khớp';
                redirect('auth/reset-password?token=' . $token);
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
                redirect('auth/reset-password?token=' . $token);
            }

            $user = $this->userModel->findByEmail($reset['email']);
            if ($user && $this->userModel->updatePassword($user['id'], $password)) {
                $this->resetModel->deleteToken($token);
                $_SESSION['success'] = 'Đặt lại mật khẩu thành công';
                redirect('auth/login');
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
                redirect('auth/reset-password?token=' . $token);
            }
        } else {
            require_once __DIR__ . '/../views/auth/reset-password.php';
        }
    }
}
?>