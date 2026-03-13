<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Game.php';

class OrderController
{
    private $cartModel;
    private $orderModel;
    private $gameModel;

    public function __construct()
    {
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->gameModel = new Game();
    }

    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentMethod = sanitize($_POST['payment_method'] ?? '');
            $buyNowId = intval($_POST['buy_now'] ?? 0);

            if (empty($paymentMethod)) {
                $_SESSION['error'] = 'Vui lòng chọn phương thức thanh toán';
                $redirectUrl = $buyNowId > 0 ? "order/checkout?buy_now=$buyNowId" : "cart";
                redirect($redirectUrl);
            }

            $user = getCurrentUser();

            if ($buyNowId > 0) {
                // Buy Now flow - single item
                $game = $this->gameModel->findById($buyNowId);
                if (!$game) {
                    $_SESSION['error'] = 'Game không tồn tại';
                    redirect('');
                }

                $price = $game['sale_price'] ?? $game['price'];
                $total = $price; // quantity is 1 for buy now

                $orderId = $this->orderModel->create($user['id'], $total, $paymentMethod);
                $this->orderModel->addItem($orderId, $buyNowId, $price);
            } else {
                // Regular cart flow
                $cart = $this->cartModel->getOrCreateCart($user['id']);
                $items = $this->cartModel->getCartItems($cart['id']);

                if (empty($items)) {
                    $_SESSION['error'] = 'Giỏ hàng trống';
                    redirect('cart');
                }

                $total = $this->cartModel->getCartTotal($cart['id']);

                // Create order
                $orderId = $this->orderModel->create($user['id'], $total, $paymentMethod);

                // Add order items
                foreach ($items as $item) {
                    $price = $item['sale_price'] ?? $item['price'];
                    $this->orderModel->addItem($orderId, $item['game_id'], $price);
                }

                // Clear cart
                $this->cartModel->clearCart($cart['id']);
            }

            $_SESSION['success'] = 'Đặt hàng thành công!';
            redirect('order/history');
        } else {
            $buyNowId = $_GET['buy_now'] ?? 0;
            if ($buyNowId > 0) {
                if (!isLoggedIn()) {
                    $currentUrl = urlencode($_SERVER['REQUEST_URI']);
                    redirect("auth/login?redirect=$currentUrl");
                }

                $game = $this->gameModel->findById($buyNowId);
                if (!$game) {
                    $_SESSION['error'] = 'Game không tồn tại';
                    redirect('');
                }

                $items = [
                    [
                        'game_id' => $game['id'],
                        'title' => $game['title'],
                        'price' => $game['price'],
                        'sale_price' => $game['sale_price'],
                        'quantity' => 1
                    ]
                ];
                $total = $game['sale_price'] ?? $game['price'];
            } else {
                if (!isLoggedIn()) {
                    $currentUrl = urlencode($_SERVER['REQUEST_URI']);
                    redirect("auth/login?redirect=$currentUrl");
                }

                $user = getCurrentUser();
                $cart = $this->cartModel->getOrCreateCart($user['id']);
                $items = $this->cartModel->getCartItems($cart['id']);
                $total = $this->cartModel->getCartTotal($cart['id']);

                if (empty($items)) {
                    redirect('cart');
                }
            }

            require_once __DIR__ . '/../views/order/checkout.php';
        }
    }

    public function history()
    {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        $page = $_GET['page'] ?? 1;
        $user = getCurrentUser();
        // Chỉ hiển thị đơn chờ duyệt và bị từ chối
        $result = $this->orderModel->getByUserId($user['id'], $page, 10, ['pending', 'cancelled']);

        require_once __DIR__ . '/../views/order/history.php';
    }

    public function detail()
    {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        $id = $_GET['id'] ?? 0;
        $user = getCurrentUser();

        $order = $this->orderModel->findById($id);
        if (!$order || $order['user_id'] != $user['id']) {
            redirect('order/history');
        }

        $items = $this->orderModel->getItems($id);

        require_once __DIR__ . '/../views/order/detail.php';
    }
}
?>