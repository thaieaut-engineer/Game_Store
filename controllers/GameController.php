<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/GameImage.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/GameCategoryMap.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Library.php';

class GameController
{
    private $gameModel;
    private $imageModel;
    private $categoryModel;
    private $mapModel;
    private $reviewModel;
    private $libraryModel;
    private $cartModel;

    public function __construct()
    {
        $this->gameModel = new Game();
        $this->imageModel = new GameImage();
        $this->categoryModel = new Category();
        $this->mapModel = new GameCategoryMap();
        $this->reviewModel = new Review();
        $this->libraryModel = new Library();
        $this->cartModel = new Cart();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $categoryId = $_GET['category'] ?? null;
        $type = $_GET['type'] ?? '';

        // Dùng để render bộ lọc
        $allCategories = $this->categoryModel->getAllNoPagination();

        if (!empty($search)) {
            $result = $this->gameModel->search($search, $page, 12);
        } elseif ($type === 'recommended') {
            $result = ['data' => $this->gameModel->getRecommended(50), 'total_pages' => 1, 'page' => 1];
            $pageTitle = 'Game Đề Xuất';
        } elseif ($type === 'sale') {
            $result = ['data' => $this->gameModel->getOnSale(50), 'total_pages' => 1, 'page' => 1];
            $pageTitle = 'Game Đang Giảm Giá';
        } elseif ($type === 'upcoming') {
            $result = ['data' => $this->gameModel->getUpcoming(50), 'total_pages' => 1, 'page' => 1];
            $pageTitle = 'Game Sắp Ra Mắt';
        } else {
            $result = $this->gameModel->getAll($page, 12, $search, $categoryId);
            $pageTitle = 'Danh sách Game';
        }

        $currentCategory = null;
        if (!empty($categoryId)) {
            $currentCategory = $this->categoryModel->findById($categoryId);
        }

        $ownedGameIds = [];
        $cartGameIds = [];
        if (isLoggedIn()) {
            $user = getCurrentUser();
            $ownedGameIds = $this->libraryModel->getUserOwnedGameIds($user['id']);
            $cartGameIds = $this->cartModel->getUserCartGameIds($user['id']);
        }

        require_once __DIR__ . '/../views/game/index.php';
    }

    public function detail()
    {
        $slug = $_GET['slug'] ?? '';

        if (empty($slug)) {
            redirect('');
        }

        $game = $this->gameModel->findBySlug($slug);
        if (!$game) {
            redirect('');
        }

        $images = $this->imageModel->getByGameId($game['id']);
        $categories = $this->mapModel->getCategoriesByGameId($game['id']);
        $reviews = $this->reviewModel->getByGameId($game['id'], 1, 10);
        $ratingStats = $this->reviewModel->getAverageRating($game['id']);

        // Kiểm tra user đã sở hữu game chưa (Library)
        $hasInLibrary = false;
        $ownedGameIds = [];
        $cartGameIds = [];
        if (isLoggedIn()) {
            $currentUser = getCurrentUser();
            if ($currentUser) {
                $ownedGameIds = $this->libraryModel->getUserOwnedGameIds($currentUser['id']);
                $hasInLibrary = in_array($game['id'], $ownedGameIds);
                $cartGameIds = $this->cartModel->getUserCartGameIds($currentUser['id']);
            }
        }

        // Get related games - lấy game có cùng bất kỳ chủ đề nào
        $relatedGames = [];
        if (!empty($categories)) {
            // Lấy tất cả category IDs của game này
            $categoryIds = array_column($categories, 'id');
            // Lấy game có cùng bất kỳ chủ đề nào
            $relatedGames = $this->gameModel->getByCategories($categoryIds, 8, $game['id']);
        }

        require_once __DIR__ . '/../views/game/detail.php';
    }
}
?>