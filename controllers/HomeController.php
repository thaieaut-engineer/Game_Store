<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController
{
    private $gameModel;
    private $categoryModel;

    public function __construct()
    {
        $this->gameModel = new Game();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $recommendedGames = $this->gameModel->getRecommended(8);
        $upcomingGames = $this->gameModel->getUpcoming(8);
        $saleGames = $this->gameModel->getOnSale(8);
        $popularCategories = $this->categoryModel->getPopular(4);

        $ownedGameIds = [];
        if (isLoggedIn()) {
            $user = getCurrentUser();
            require_once __DIR__ . '/../models/Library.php';
            $libraryModel = new Library();
            $ownedGameIds = $libraryModel->getUserOwnedGameIds($user['id']);
        }

        require_once __DIR__ . '/../views/home/index.php';
    }
}
?>