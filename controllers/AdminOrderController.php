<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Game.php';

class AdminOrderController
{
    private $orderModel;
    private $gameModel;

    public function __construct()
    {
        if (!isAdmin()) {
            redirect('');
        }
        $this->orderModel = new Order();
        $this->gameModel = new Game();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? null;
        $result = $this->orderModel->getAll($page, 10, $status);

        require_once __DIR__ . '/../views/admin/order/index.php';
    }

    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $order = $this->orderModel->findById($id);

        if (!$order) {
            redirect('admin/order');
        }

        $items = $this->orderModel->getItems($id);

        require_once __DIR__ . '/../views/admin/order/detail.php';
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['order_id'] ?? 0;
            $status = $_POST['status'] ?? '';

            if (in_array($status, ['pending', 'approved', 'completed', 'cancelled'])) {
                if ($this->orderModel->updateStatus($id, $status)) {
                    // Nếu admin duyệt (approved) thì thêm game vào Library
                    if ($status === 'approved') {
                        require_once __DIR__ . '/../models/Library.php';
                        $libraryModel = new Library();
                        $libraryModel->addFromOrder($id);

                        // Tăng lượt mua cho từng game trong đơn hàng
                        $items = $this->orderModel->getItems($id);
                        foreach ($items as $item) {
                            $this->gameModel->incrementSales($item['game_id'], $item['quantity']);
                        }
                    }
                    $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra';
                }
            }

            redirect('admin/order');
        }
    }
}
?>