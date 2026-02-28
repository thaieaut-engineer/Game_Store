<?php
require_once __DIR__ . '/BaseModel.php';

class GameImage extends BaseModel {
    public function create($gameId, $imageUrl) {
        $query = "INSERT INTO game_images (game_id, image_url) VALUES (:game_id, :image_url)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->bindValue(':image_url', $imageUrl);
        return $stmt->execute();
    }
    
    public function getByGameId($gameId) {
        $query = "SELECT * FROM game_images WHERE game_id = :game_id ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteByGameId($gameId) {
        $query = "DELETE FROM game_images WHERE game_id = :game_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':game_id', $gameId);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM game_images WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>
