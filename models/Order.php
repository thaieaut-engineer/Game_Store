<?php
require_once __DIR__ . '/BaseModel.php';

class Order extends BaseModel {
    public function create($userId, $totalAmount, $paymentMethod) {
        $query = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
                  VALUES (:user_id, :total_amount, :payment_method, 'pending')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':total_amount', $totalAmount);
        $stmt->bindValue(':payment_method', $paymentMethod);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    
    public function addItem($orderId, $gameId, $price, $quantity) {
        $query = "INSERT INTO order_items (order_id, game_id, price, quantity) 
                  VALUES (:order_id, :game_id, :price, :quantity)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':order_id', $orderId);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':quantity', $quantity);
        return $stmt->execute();
    }
    
    public function findById($id) {
        $query = "SELECT o.*, u.name as user_name, u.email FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  WHERE o.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getByUserId($userId, $page = 1, $perPage = 10, $statuses = null) {
        $query = "SELECT * FROM orders WHERE user_id = :user_id";
        $params = [':user_id' => $userId];

        if (is_array($statuses) && !empty($statuses)) {
            // Tạo danh sách placeholder cho IN (...)
            $placeholders = [];
            foreach ($statuses as $index => $st) {
                $key = ':status_' . $index;
                $placeholders[] = $key;
                $params[$key] = $st;
            }
            $query .= " AND status IN (" . implode(',', $placeholders) . ")";
        }

        $query .= " ORDER BY created_at DESC";
        return $this->paginate($query, $params, $page, $perPage);
    }
    
    public function getAll($page = 1, $perPage = 10, $status = null) {
        $query = "SELECT o.*, u.name as user_name FROM orders o 
                  JOIN users u ON o.user_id = u.id WHERE 1=1";
        $params = [];
        
        if ($status) {
            $query .= " AND o.status = :status";
            $params[':status'] = $status;
        }
        
        $query .= " ORDER BY o.created_at DESC";
        return $this->paginate($query, $params, $page, $perPage);
    }
    
    public function getItems($orderId) {
        $query = "SELECT oi.*, g.title, g.slug 
                  FROM order_items oi 
                  JOIN games g ON oi.game_id = g.id 
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status) {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM orders";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getRevenueStats($days = 30) {
        $query = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue 
                  FROM orders 
                  WHERE status IN ('approved', 'completed') 
                  AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                  GROUP BY DATE(created_at) 
                  ORDER BY date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategorySalesStats() {
        $query = "SELECT gc.name, SUM(oi.price * oi.quantity) as total_sales 
                  FROM order_items oi 
                  JOIN games g ON oi.game_id = g.id 
                  JOIN game_category_map gcm ON g.id = gcm.game_id 
                  JOIN game_categories gc ON gcm.category_id = gc.id 
                  JOIN orders o ON oi.order_id = o.id 
                  WHERE o.status IN ('approved', 'completed')
                  GROUP BY gc.id, gc.name 
                  ORDER BY total_sales DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
