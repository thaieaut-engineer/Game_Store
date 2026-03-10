<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Review.php';

class AdminReviewController
{
    private $reviewModel;

    public function __construct()
    {
        if (!isAdmin()) {
            redirect('');
        }
        $this->reviewModel = new Review();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $rating = $_GET['rating'] ?? null;
        $result = $this->reviewModel->getAll($page, 10, $rating);

        require_once __DIR__ . '/../views/admin/review/index.php';
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($this->reviewModel->delete($id)) {
            $_SESSION['success'] = 'Xóa bình luận thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra';
        }
        redirect('admin/review');
    }
}
?>