<?php
require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel {
    public function create($name, $slug, $image = null) {
        $query = "INSERT INTO game_categories (name, slug, image) VALUES (:name, :slug, :image)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':slug', $slug);
        $stmt->bindValue(':image', $image);
        return $stmt->execute();
    }
    
    public function update($id, $name, $slug, $image = null) {
        if ($image) {
            $query = "UPDATE game_categories SET name = :name, slug = :slug, image = :image WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':slug', $slug);
            $stmt->bindValue(':image', $image);
            $stmt->bindValue(':id', $id);
        } else {
            $query = "UPDATE game_categories SET name = :name, slug = :slug WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':slug', $slug);
            $stmt->bindValue(':id', $id);
        }
        return $stmt->execute();
    }
    
    public function findById($id) {
        $query = "SELECT * FROM game_categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll($page = 1, $perPage = 10) {
        $query = "SELECT * FROM game_categories ORDER BY created_at DESC";
        return $this->paginate($query, [], $page, $perPage);
    }
    
    public function getAllNoPagination() {
        $query = "SELECT * FROM game_categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPopular($limit = 4) {
        $query = "SELECT gc.*, COUNT(gcm.game_id) as game_count 
                  FROM game_categories gc 
                  LEFT JOIN game_category_map gcm ON gc.id = gcm.category_id 
                  GROUP BY gc.id 
                  ORDER BY game_count DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function delete($id) {
        $query = "DELETE FROM game_categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM game_categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>
