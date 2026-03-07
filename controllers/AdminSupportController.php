<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Support.php';

class AdminSupportController
{
    private $supportModel;

    public function __construct()
    {
        if (!isAdmin()) {
            redirect('');
        }
        $this->supportModel = new Support();
    }

    public function index()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 10;
        $supportData = $this->supportModel->getAll($page, $perPage);

        $requests = $supportData['data'];
        $totalPages = $supportData['total_pages'];
        $currentPage = $supportData['page'];

        require_once __DIR__ . '/../views/admin/support/index.php';
    }

    public function detail()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $request = $this->supportModel->getById($id);

        if (!$request) {
            $_SESSION['error'] = "Không tìm thấy yêu cầu hỗ trợ.";
            redirect('admin/support');
        }

        require_once __DIR__ . '/../views/admin/support/detail.php';
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $status = $_POST['status'] ?? 'open';

            if ($this->supportModel->updateStatus($id, $status)) {
                $_SESSION['success'] = "Cập nhật trạng thái thành công.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật trạng thái.";
            }

            redirect('admin/support/detail?id=' . $id);
        }
    }

    public function delete()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($this->supportModel->delete($id)) {
            $_SESSION['success'] = "Xóa yêu cầu thành công.";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi xóa yêu cầu.";
        }

        redirect('admin/support');
    }
}
?>