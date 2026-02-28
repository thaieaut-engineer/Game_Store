<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Category.php';

class AdminCategoryController {
    private $categoryModel;
    
    public function __construct() {
        if (!isAdmin()) {
            redirect('');
        }
        $this->categoryModel = new Category();
    }
    
    public function index() {
        $page = $_GET['page'] ?? 1;
        $result = $this->categoryModel->getAll($page, 10);
        
        require_once __DIR__ . '/../views/admin/category/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            $slug = $this->createSlug($name);
            $image = null;
            
            if (empty($name)) {
                $_SESSION['error'] = 'Tên chủ đề không được để trống';
                redirect('admin/category/create');
            }
            
            if (!validateInput($name)) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ';
                redirect('admin/category/create');
            }
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../uploads/categories/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $image = uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
                    $image = 'uploads/categories/' . $image;
                }
            }
            
            if ($this->categoryModel->create($name, $slug, $image)) {
                $_SESSION['success'] = 'Thêm chủ đề thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            redirect('admin/category');
        } else {
            require_once __DIR__ . '/../views/admin/category/create.php';
        }
    }
    
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $category = $this->categoryModel->findById($id);
        
        if (!$category) {
            redirect('admin/category');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name'] ?? '');
            $slug = $this->createSlug($name);
            $image = $category['image'];
            
            if (empty($name)) {
                $_SESSION['error'] = 'Tên chủ đề không được để trống';
                redirect('admin/category/edit?id=' . $id);
            }
            
            // Handle new image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../uploads/categories/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $image = uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
                    $image = 'uploads/categories/' . $image;
                }
            }
            
            if ($this->categoryModel->update($id, $name, $slug, $image)) {
                $_SESSION['success'] = 'Cập nhật chủ đề thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            redirect('admin/category');
        } else {
            require_once __DIR__ . '/../views/admin/category/edit.php';
        }
    }
    
    public function delete() {
        $id = $_GET['id'] ?? 0;
        if ($this->categoryModel->delete($id)) {
            $_SESSION['success'] = 'Xóa chủ đề thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra';
        }
        redirect('admin/category');
    }
    
    private function createSlug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }
}
?>
