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
        $recommendedGames = $this->gameModel->getRecommended(18);
        $upcomingGames = $this->gameModel->getUpcoming(10);
        $saleGames = $this->gameModel->getOnSale(18);
        $popularCategories = $this->categoryModel->getPopular(10);

        $ownedGameIds = [];
        $cartGameIds = [];
        if (isLoggedIn()) {
            $user = getCurrentUser();
            require_once __DIR__ . '/../models/Library.php';
            $libraryModel = new Library();
            $ownedGameIds = $libraryModel->getUserOwnedGameIds($user['id']);

            require_once __DIR__ . '/../models/Cart.php';
            $cartModel = new Cart();
            $cartGameIds = $cartModel->getUserCartGameIds($user['id']);
        }

        require_once __DIR__ . '/../views/home/index.php';
    }
}
?>