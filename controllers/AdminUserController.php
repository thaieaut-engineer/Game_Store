<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class AdminUserController {
    private $userModel;
    
    public function __construct() {
        if (!isAdmin()) {
            redirect('');
        }
        $this->userModel = new User();
    }
    
    public function index() {
        $page = $_GET['page'] ?? 1;
        $result = $this->userModel->getAll($page, 10);
        
        require_once __DIR__ . '/../views/admin/user/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            if (empty($name) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
                redirect('admin/user/create');
            }
            
            if (!validateInput($name) || !validateInput($email)) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ';
                redirect('admin/user/create');
            }
            
            if ($this->userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Email đã được sử dụng';
                redirect('admin/user/create');
            }
            
            if ($this->userModel->createWithRole($name, $email, $password, $role)) {
                $_SESSION['success'] = 'Thêm tài khoản thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            redirect('admin/user');
        } else {
            require_once __DIR__ . '/../views/admin/user/create.php';
        }
    }
    
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            redirect('admin/user');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            $status = $_POST['status'] ?? 1;
            $role = $_POST['role'] ?? 'user';
            
            if (empty($name)) {
                $_SESSION['error'] = 'Tên không được để trống';
                redirect('admin/user/edit?id=' . $id);
            }
            
            if ($this->userModel->update($id, $name) && 
                $this->userModel->updateStatus($id, $status) && 
                $this->userModel->updateRole($id, $role)) {
                $_SESSION['success'] = 'Cập nhật tài khoản thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            redirect('admin/user');
        } else {
            require_once __DIR__ . '/../views/admin/user/edit.php';
        }
    }
    
    public function delete() {
        $id = $_GET['id'] ?? 0;
        if ($this->userModel->delete($id)) {
            $_SESSION['success'] = 'Xóa tài khoản thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra';
        }
        redirect('admin/user');
    }
}
?>
