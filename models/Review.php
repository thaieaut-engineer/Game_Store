<?php
require_once __DIR__ . '/BaseModel.php';

class Review extends BaseModel {
    public function create($userId, $gameId, $rating, $comment) {
        // Check if user already reviewed this game
        $existing = $this->getByUserAndGame($userId, $gameId);
        if ($existing) {
            return $this->update($existing['id'], $rating, $comment);
        }
        
        $query = "INSERT INTO reviews (user_id, game_id, rating, comment) 
                  VALUES (:user_id, :game_id, :rating, :comment)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->bindValue(':rating', $rating);
        $stmt->bindValue(':comment', $comment);
        return $stmt->execute();
    }
    
    public function update($id, $rating, $comment) {
        $query = "UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':rating', $rating);
        $stmt->bindValue(':comment', $comment);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function getByGameId($gameId, $page = 1, $perPage = 10) {
        $query = "SELECT r.*, u.name as user_name, u.avatar 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.game_id = :game_id 
                  ORDER BY r.created_at DESC";
        $params = [':game_id' => $gameId];
        return $this->paginate($query, $params, $page, $perPage);
    }
    
    public function getByUserAndGame($userId, $gameId) {
        $query = "SELECT * FROM reviews WHERE user_id = :user_id AND game_id = :game_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll($page = 1, $perPage = 10, $rating = null) {
        $query = "SELECT r.*, u.name as user_name, g.title as game_title 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  JOIN games g ON r.game_id = g.id 
                  WHERE 1=1";
        $params = [];
        
        if ($rating) {
            $query .= " AND r.rating = :rating";
            $params[':rating'] = $rating;
        }
        
        $query .= " ORDER BY r.created_at DESC";
        return $this->paginate($query, $params, $page, $perPage);
    }
    
    public function getAverageRating($gameId) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                  FROM reviews WHERE game_id = :game_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function delete($id) {
        $query = "DELETE FROM reviews WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>
