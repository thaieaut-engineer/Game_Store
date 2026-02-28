<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Review.php';

class AdminController {
    private $gameModel;
    private $categoryModel;
    private $userModel;
    private $orderModel;
    private $reviewModel;
    
    public function __construct() {
        if (!isAdmin()) {
            redirect('');
        }
        $this->gameModel = new Game();
        $this->categoryModel = new Category();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->reviewModel = new Review();
    }
    
    public function dashboard() {
        $gameCount = $this->gameModel->getCount();
        $categoryCount = $this->categoryModel->getCount();
        $userCount = $this->userModel->getAll(1, 1)['total'];
        $orderCount = $this->orderModel->getCount();
        $revenueStats = $this->orderModel->getRevenueStats(30);
        $categorySales = $this->orderModel->getCategorySalesStats();
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
}
?>
