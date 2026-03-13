<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/GameImage.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/GameCategoryMap.php';
class AdminGameController
{
    private $gameModel;
    private $imageModel;
    private $categoryModel;
    private $mapModel;

    public function __construct()
    {
        if (!isAdmin()) {
            redirect('');
        }
        $this->gameModel = new Game();
        $this->imageModel = new GameImage();
        $this->categoryModel = new Category();
        $this->mapModel = new GameCategoryMap();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $result = $this->gameModel->getAll($page, 10, $search);

        require_once __DIR__ . '/../views/admin/game/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $price = $_POST['price'] ?? 0;
            $discountPercent = $_POST['discount_percent'] ?? 0;
            $salePrice = ($discountPercent > 0) ? ($price * (1 - $discountPercent / 100)) : null;

            $data = [
                'title' => sanitize($_POST['title'] ?? ''),
                'slug' => $this->createSlug($_POST['title'] ?? ''),
                'price' => $price,
                'discount_percent' => $discountPercent,
                'sale_price' => $salePrice,
                'video_url' => sanitize($_POST['video_url'] ?? ''),
                'short_description' => sanitize($_POST['short_description'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'system_requirements' => $_POST['system_requirements'] ?? '',
                'release_date' => $_POST['release_date'] ?? null,
                'is_upcoming' => isset($_POST['is_upcoming']) ? 1 : 0
            ];

            // Validation
            if (empty($data['title']) || empty($data['price'])) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
                redirect('admin/game/create');
            }

            if (!validateInput($data['title'])) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ';
                redirect('admin/game/create');
            }

            $gameId = $this->gameModel->create($data);

            // Handle images
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $this->uploadImages($gameId, $_FILES['images']);
            }

            // Handle categories
            if (isset($_POST['categories'])) {
                foreach ($_POST['categories'] as $categoryId) {
                    $this->mapModel->addCategoryToGame($gameId, $categoryId);
                }
            }

            $_SESSION['success'] = 'Thêm game thành công';
            redirect('admin/game');
        } else {
            $categories = $this->categoryModel->getAllNoPagination();
            require_once __DIR__ . '/../views/admin/game/create.php';
        }
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $game = $this->gameModel->findById($id);

        if (!$game) {
            redirect('admin/game');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $price = $_POST['price'] ?? 0;
            $discountPercent = $_POST['discount_percent'] ?? 0;
            $salePrice = ($discountPercent > 0) ? ($price * (1 - $discountPercent / 100)) : null;

            $data = [
                'title' => sanitize($_POST['title'] ?? ''),
                'slug' => $this->createSlug($_POST['title'] ?? ''),
                'price' => $price,
                'discount_percent' => $discountPercent,
                'sale_price' => $salePrice,
                'video_url' => sanitize($_POST['video_url'] ?? ''),
                'short_description' => sanitize($_POST['short_description'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'system_requirements' => $_POST['system_requirements'] ?? '',
                'release_date' => $_POST['release_date'] ?? null,
                'is_upcoming' => isset($_POST['is_upcoming']) ? 1 : 0
            ];

            if ($this->gameModel->update($id, $data)) {
                // Handle new images
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    // Xóa ảnh cũ trước khi upload ảnh mới
                    $oldImages = $this->imageModel->getByGameId($id);
                    foreach ($oldImages as $oldImage) {
                        $oldFilePath = __DIR__ . '/../' . $oldImage['image_url'];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    $this->imageModel->deleteByGameId($id);

                    $uploadResult = $this->uploadImages($id, $_FILES['images']);
                    if (!empty($uploadResult['errors'])) {
                        $_SESSION['error'] = 'Cập nhật game OK nhưng upload ảnh có lỗi: ' . implode(' | ', $uploadResult['errors']);
                    } elseif (($uploadResult['uploaded'] ?? 0) === 0) {
                        $_SESSION['error'] = 'Cập nhật game OK nhưng chưa upload được ảnh nào.';
                    }
                }

                // Update categories
                $this->mapModel->deleteByGameId($id);
                if (isset($_POST['categories'])) {
                    foreach ($_POST['categories'] as $categoryId) {
                        $this->mapModel->addCategoryToGame($id, $categoryId);
                    }
                }

                if (!isset($_SESSION['error'])) {
                    $_SESSION['success'] = 'Cập nhật game thành công';
                }
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }

            redirect('admin/game');
        } else {
            $images = $this->imageModel->getByGameId($id);
            $gameCategories = $this->mapModel->getCategoriesByGameId($id);
            $categories = $this->categoryModel->getAllNoPagination();
            require_once __DIR__ . '/../views/admin/game/edit.php';
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;

        // Xóa các tệp ảnh trong thư mục uploads trước khi xóa bản ghi
        $images = $this->imageModel->getByGameId($id);
        foreach ($images as $image) {
            $filePath = __DIR__ . '/../' . $image['image_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($this->gameModel->delete($id)) {
            $_SESSION['success'] = 'Xóa game thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra';
        }
        redirect('admin/game');
    }

    public function generateAiInfo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $gameName = $_POST['title'] ?? '';
        $categoryIds = $_POST['categories'] ?? [];

        if (empty($gameName)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tên game trước khi dùng AI']);
            return;
        }

        $categoryNames = [];
        if (!empty($categoryIds) && is_array($categoryIds)) {
            $categories = $this->categoryModel->getAllNoPagination();
            foreach ($categories as $cat) {
                if (in_array($cat['id'], $categoryIds)) {
                    $categoryNames[] = $cat['name'];
                }
            }
        }
        $categoryList = implode(', ', $categoryNames);

        $aiService = new AiService();
        $result = $aiService->generateGameInfo($gameName, $categoryList);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    private function createSlug($string)
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    private function uploadImages($gameId, $files)
    {
        $uploadDir = __DIR__ . '/../uploads/games/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $count = count($files['name']);
        $uploaded = 0;
        $errors = [];

        for ($i = 0; $i < $count; $i++) {
            if (!isset($files['error'][$i])) {
                continue;
            }

            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = 'Upload lỗi (code ' . $files['error'][$i] . ') với file: ' . ($files['name'][$i] ?? '');
                continue;
            }

            if (!is_uploaded_file($files['tmp_name'][$i])) {
                $errors[] = 'File upload không hợp lệ: ' . ($files['name'][$i] ?? '');
                continue;
            }

            // giới hạn 2MB / ảnh (bạn có thể chỉnh)
            if (!empty($files['size'][$i]) && $files['size'][$i] > 2 * 1024 * 1024) {
                $errors[] = 'File quá lớn (>2MB): ' . ($files['name'][$i] ?? '');
                continue;
            }

            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $errors[] = 'Định dạng không hỗ trợ: ' . ($files['name'][$i] ?? '');
                continue;
            }

            // kiểm tra thực sự là ảnh
            $imgInfo = @getimagesize($files['tmp_name'][$i]);
            if ($imgInfo === false) {
                $errors[] = 'File không phải ảnh: ' . ($files['name'][$i] ?? '');
                continue;
            }

            $filename = uniqid('game_', true) . '.' . $ext;
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                $ok = $this->imageModel->create($gameId, 'uploads/games/' . $filename);
                if ($ok) {
                    $uploaded++;
                } else {
                    $errors[] = 'Lưu DB thất bại cho file: ' . $filename;
                }
            } else {
                $errors[] = 'Không thể lưu file: ' . ($files['name'][$i] ?? '');
            }
        }

        return ['uploaded' => $uploaded, 'errors' => $errors];
    }
}
?>