<?php
require_once __DIR__ . '/BaseModel.php';

class Library extends BaseModel
{
    public function addFromOrder($orderId)
    {
        // Lấy user và game từ order_items
        $query = "SELECT o.user_id, oi.game_id
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.id
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':order_id', $orderId);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            return false;
        }

        $insert = "INSERT IGNORE INTO libraries (user_id, game_id, order_id)
                   VALUES (:user_id, :game_id, :order_id)";
        $insertStmt = $this->conn->prepare($insert);

        foreach ($items as $item) {
            $insertStmt->bindValue(':user_id', $item['user_id']);
            $insertStmt->bindValue(':game_id', $item['game_id']);
            $insertStmt->bindValue(':order_id', $orderId);
            $insertStmt->execute();
        }

        return true;
    }

    public function getByUser($userId, $page = 1, $perPage = 12)
    {
        $query = "SELECT l.*, g.title, g.slug, g.price, g.sale_price,
                         (SELECT image_url FROM game_images WHERE game_id = g.id LIMIT 1) as image
                  FROM libraries l
                  JOIN games g ON l.game_id = g.id
                  WHERE l.user_id = :user_id
                  ORDER BY l.added_at DESC";
        $params = [':user_id' => $userId];
        return $this->paginate($query, $params, $page, $perPage);
    }

    public function userOwnsGame($userId, $gameId)
    {
        $query = "SELECT 1 FROM libraries WHERE user_id = :user_id AND game_id = :game_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function getUserOwnedGameIds($userId)
    {
        $query = "SELECT game_id FROM libraries WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>