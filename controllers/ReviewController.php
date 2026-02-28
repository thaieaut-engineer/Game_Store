<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Game.php';

class ReviewController {
    private $reviewModel;
    private $gameModel;
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        $this->reviewModel = new Review();
        $this->gameModel = new Game();
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gameId = $_POST['game_id'] ?? 0;
            $rating = $_POST['rating'] ?? 0;
            $comment = sanitize($_POST['comment'] ?? '');
            
            if (!validateInput($gameId) || !validateInput($rating) || !validateInput($comment)) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ';
                redirect('game/detail?slug=' . ($_POST['game_slug'] ?? ''));
            }
            
            if ($rating < 1 || $rating > 10) {
                $_SESSION['error'] = 'Đánh giá phải từ 1 đến 10';
                redirect('game/detail?slug=' . ($_POST['game_slug'] ?? ''));
            }
            
            $game = $this->gameModel->findById($gameId);
            if (!$game) {
                redirect('');
            }
            
            $user = getCurrentUser();
            
            if ($this->reviewModel->create($user['id'], $gameId, $rating, $comment)) {
                $_SESSION['success'] = 'Đánh giá thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            redirect('game/detail?slug=' . $game['slug']);
        }
    }
}
?>
