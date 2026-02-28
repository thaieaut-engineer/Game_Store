<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController {
    private $gameModel;
    private $categoryModel;
    
    public function __construct() {
        $this->gameModel = new Game();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        $recommendedGames = $this->gameModel->getRecommended(8);
        $upcomingGames = $this->gameModel->getUpcoming(8);
        $saleGames = $this->gameModel->getOnSale(8);
        $popularCategories = $this->categoryModel->getPopular(4);
        
        require_once __DIR__ . '/../views/home/index.php';
    }
}
?>
