<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/AiService.php';

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

    public function checkAi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $id = $_POST['id'] ?? 0;

        require_once __DIR__ . '/../config/database.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM reviews WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$review) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đánh giá']);
            return;
        }

        $aiService = new AiService();
        $result = $aiService->moderateReview($review['comment'], $review['rating']);

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
?>