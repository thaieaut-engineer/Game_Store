<?php
require_once __DIR__ . '/BaseModel.php';

class Cart extends BaseModel {
    public function getOrCreateCart($userId) {
        $query = "SELECT * FROM carts WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cart) {
            $query = "INSERT INTO carts (user_id) VALUES (:user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':user_id', $userId);
            $stmt->execute();
            $cartId = $this->conn->lastInsertId();
            
            $query = "SELECT * FROM carts WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $cartId);
            $stmt->execute();
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $cart;
    }
    
    public function addItem($cartId, $gameId, $quantity = 1) {
        // Check if item already exists
        $query = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND game_id = :game_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cart_id', $cartId);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            $query = "UPDATE cart_items SET quantity = quantity + :quantity WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':quantity', $quantity);
            $stmt->bindValue(':id', $existing['id']);
            return $stmt->execute();
        } else {
            $query = "INSERT INTO cart_items (cart_id, game_id, quantity) VALUES (:cart_id, :game_id, :quantity)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':cart_id', $cartId);
            $stmt->bindValue(':game_id', $gameId);
            $stmt->bindValue(':quantity', $quantity);
            return $stmt->execute();
        }
    }
    
    public function getCartItems($cartId) {
        $query = "SELECT ci.*, g.title, g.price, g.sale_price, g.slug, 
                  (SELECT image_url FROM game_images WHERE game_id = g.id LIMIT 1) as image
                  FROM cart_items ci 
                  JOIN games g ON ci.game_id = g.id 
                  WHERE ci.cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cart_id', $cartId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateQuantity($itemId, $quantity) {
        $query = "UPDATE cart_items SET quantity = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':quantity', $quantity);
        $stmt->bindValue(':id', $itemId);
        return $stmt->execute();
    }
    
    public function removeItem($itemId) {
        $query = "DELETE FROM cart_items WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $itemId);
        return $stmt->execute();
    }
    
    public function clearCart($cartId) {
        $query = "DELETE FROM cart_items WHERE cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cart_id', $cartId);
        return $stmt->execute();
    }
    
    public function getCartTotal($cartId) {
        $items = $this->getCartItems($cartId);
        $total = 0;
        foreach ($items as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $total += $price * $item['quantity'];
        }
        return $total;
    }
}
?>
