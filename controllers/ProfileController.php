<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    private $userModel;
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        $this->userModel = new User();
    }
    
    public function index() {
        $user = getCurrentUser();
        
        // Get full user data from database to ensure we have all fields
        if ($user && isset($user['id'])) {
            $fullUser = $this->userModel->findById($user['id']);
            if ($fullUser) {
                // Merge database data with session data
                $user = array_merge($user, $fullUser);
            }
        }
        
        require_once __DIR__ . '/../views/profile/index.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            $avatar = null;
            
            if (empty($name)) {
                $_SESSION['error'] = 'Tên không được để trống';
                redirect('profile');
            }
            
            if (!validateInput($name)) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ';
                redirect('profile');
            }
            
            // Handle avatar upload
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../uploads/avatars/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $avatar = uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $avatar);
                }
            }
            
            $user = getCurrentUser();
            if ($this->userModel->update($user['id'], $name, $avatar)) {
                $_SESSION['success'] = 'Cập nhật thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            redirect('profile');
        }
    }
    
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
                redirect('profile');
            }
            
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'Mật khẩu xác nhận không khớp';
                redirect('profile');
            }
            
            if (strlen($newPassword) < 6) {
                $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
                redirect('profile');
            }
            
            $user = getCurrentUser();
            $userData = $this->userModel->findById($user['id']);
            
            if (!password_verify($currentPassword, $userData['password'])) {
                $_SESSION['error'] = 'Mật khẩu hiện tại không đúng';
                redirect('profile');
            }
            
            if ($this->userModel->updatePassword($user['id'], $newPassword)) {
                $_SESSION['success'] = 'Đổi mật khẩu thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            redirect('profile');
        }
    }
}
?>
