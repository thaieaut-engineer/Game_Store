<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/AiService.php';

class AdminController
{
    private $gameModel;
    private $categoryModel;
    private $userModel;
    private $orderModel;
    private $reviewModel;

    public function __construct()
    {
        if (!isAdmin()) {
            redirect('');
        }
        $this->gameModel = new Game();
        $this->categoryModel = new Category();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->reviewModel = new Review();
    }

    public function dashboard()
    {
        $gameCount = $this->gameModel->getCount();
        $categoryCount = $this->categoryModel->getCount();
        $userCount = $this->userModel->getAll(1, 1)['total'];
        $orderCount = $this->orderModel->getCount();
        $revenueStats = $this->orderModel->getRevenueStats(30);
        $categorySales = $this->orderModel->getCategorySalesStats();

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function generateAiRevenueReport()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $revenueStats = $this->orderModel->getRevenueStats(30);
        $categorySales = $this->orderModel->getCategorySalesStats();

        $revenueData = "Doanh thu 30 ngày gần nhất:\n" . json_encode($revenueStats) . "\n\nDoanh thu theo chủ đề:\n" . json_encode($categorySales);

        $aiService = new AiService();
        $result = $aiService->analyzeRevenue($revenueData);

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
?>