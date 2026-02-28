<?php
require_once __DIR__ . '/BaseModel.php';

class GameCategoryMap extends BaseModel {
    public function addCategoryToGame($gameId, $categoryId) {
        $query = "INSERT INTO game_category_map (game_id, category_id) VALUES (:game_id, :category_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->bindValue(':category_id', $categoryId);
        return $stmt->execute();
    }
    
    public function removeCategoryFromGame($gameId, $categoryId) {
        $query = "DELETE FROM game_category_map WHERE game_id = :game_id AND category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->bindValue(':category_id', $categoryId);
        return $stmt->execute();
    }
    
    public function getCategoriesByGameId($gameId) {
        $query = "SELECT gc.* FROM game_categories gc 
                  JOIN game_category_map gcm ON gc.id = gcm.category_id 
                  WHERE gcm.game_id = :game_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteByGameId($gameId) {
        $query = "DELETE FROM game_category_map WHERE game_id = :game_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        return $stmt->execute();
    }
}
?>
