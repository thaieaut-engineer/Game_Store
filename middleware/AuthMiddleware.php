<?php
require_once __DIR__ . '/../config/config.php';

class AuthMiddleware {
    public static function requireLogin() {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            redirect('auth/login');
        }
    }
    
    public static function requireAdmin() {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            redirect('auth/login');
        }
        
        if (!isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
            redirect('');
        }
    }
    
    public static function requireGuest() {
        if (isLoggedIn()) {
            redirect('');
        }
    }
}
?>
