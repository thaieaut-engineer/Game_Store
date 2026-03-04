<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Game.php';

class CartController
{
    private $cartModel;
    private $gameModel;

    public function __construct()
    {
        $this->cartModel = new Cart();
        $this->gameModel = new Game();
    }

    private function requireLogin()
    {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            redirect('auth/login');
        }
    }

    public function index()
    {
        $this->requireLogin();
        $user = getCurrentUser();
        $cart = $this->cartModel->getOrCreateCart($user['id']);
        $items = $this->cartModel->getCartItems($cart['id']);
        $total = $this->cartModel->getCartTotal($cart['id']);

        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function add()
    {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng', 'redirect' => BASE_URL . 'auth/login']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gameId = intval($_POST['game_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);

            error_log("Cart/Add: GameID=$gameId, Quantity=$quantity");

            if ($gameId <= 0 || $quantity <= 0) {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }

            $game = $this->gameModel->findById($gameId);
            if (!$game) {
                echo json_encode(['success' => false, 'message' => 'Game không tồn tại']);
                exit;
            }

            // Check stock
            if ($game['stock'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Số lượng không đủ. Còn lại: ' . $game['stock']]);
                exit;
            }

            $user = getCurrentUser();
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Phiên đăng nhập đã hết hạn', 'redirect' => BASE_URL . 'auth/login']);
                exit;
            }

            $cart = $this->cartModel->getOrCreateCart($user['id']);

            if ($this->cartModel->addItem($cart['id'], $gameId, $quantity)) {
                // Get updated cart count
                $items = $this->cartModel->getCartItems($cart['id']);
                $cartCount = count($items);

                echo json_encode([
                    'success' => true,
                    'message' => 'Đã thêm "' . $game['title'] . '" vào giỏ hàng',
                    'cart_count' => $cartCount
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = $_POST['item_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;

            error_log("Cart/Update: ItemID=$itemId, Quantity=$quantity");

            if (!validateInput($itemId) || !validateInput($quantity) || $quantity < 1) {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }

            if ($this->cartModel->updateQuantity($itemId, $quantity)) {
                $user = getCurrentUser();
                $cart = $this->cartModel->getOrCreateCart($user['id']);
                $total = $this->cartModel->getCartTotal($cart['id']);
                echo json_encode(['success' => true, 'total' => $total]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
            }
        }
    }

    public function remove()
    {
        header('Content-Type: application/json');
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = $_POST['item_id'] ?? 0;

            error_log("Cart/Remove: ItemID=$itemId");

            if (!validateInput($itemId)) {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                exit;
            }

            if ($this->cartModel->removeItem($itemId)) {
                $user = getCurrentUser();
                $cart = $this->cartModel->getOrCreateCart($user['id']);
                $total = $this->cartModel->getCartTotal($cart['id']);
                echo json_encode(['success' => true, 'total' => $total, 'message' => 'Đã xóa sản phẩm']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
            }
        }
    }
}
?>