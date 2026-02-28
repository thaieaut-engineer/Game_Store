<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Game.php';

class OrderController {
    private $cartModel;
    private $orderModel;
    private $gameModel;
    
    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->gameModel = new Game();
    }
    
    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentMethod = sanitize($_POST['payment_method'] ?? '');
            
            if (empty($paymentMethod)) {
                $_SESSION['error'] = 'Vui lòng chọn phương thức thanh toán';
                redirect('cart');
            }
            
            $user = getCurrentUser();
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
                $this->orderModel->addItem($orderId, $item['game_id'], $price, $item['quantity']);
                
                // Update game sales
                $this->gameModel->incrementSales($item['game_id'], $item['quantity']);
            }
            
            // Clear cart
            $this->cartModel->clearCart($cart['id']);
            
            $_SESSION['success'] = 'Đặt hàng thành công!';
            redirect('order/history');
        } else {
            $user = getCurrentUser();
            $cart = $this->cartModel->getOrCreateCart($user['id']);
            $items = $this->cartModel->getCartItems($cart['id']);
            $total = $this->cartModel->getCartTotal($cart['id']);
            
            if (empty($items)) {
                redirect('cart');
            }
            
            require_once __DIR__ . '/../views/order/checkout.php';
        }
    }
    
    public function history() {
        $page = $_GET['page'] ?? 1;
        $user = getCurrentUser();
        // Chỉ hiển thị đơn chờ duyệt và bị từ chối
        $result = $this->orderModel->getByUserId($user['id'], $page, 10, ['pending', 'cancelled']);
        
        require_once __DIR__ . '/../views/order/history.php';
    }
    
    public function detail() {
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
