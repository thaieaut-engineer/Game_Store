<?php
require_once __DIR__ . '/BaseModel.php';

class Game extends BaseModel {
    public function create($data) {
        $query = "INSERT INTO games (title, slug, price, sale_price, video_url, short_description, description, system_requirements, stock, release_date, is_upcoming) 
                  VALUES (:title, :slug, :price, :sale_price, :video_url, :short_description, :description, :system_requirements, :stock, :release_date, :is_upcoming)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':slug', $data['slug']);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':sale_price', $data['sale_price'] ?? null);
        $stmt->bindValue(':video_url', $data['video_url'] ?? null);
        $stmt->bindValue(':short_description', $data['short_description'] ?? null);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':system_requirements', $data['system_requirements'] ?? null);
        $stmt->bindValue(':stock', $data['stock'] ?? 9999);
        $stmt->bindValue(':release_date', $data['release_date'] ?? null);
        $stmt->bindValue(':is_upcoming', $data['is_upcoming'] ?? 0);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    
    public function update($id, $data) {
        $query = "UPDATE games SET title = :title, slug = :slug, price = :price, sale_price = :sale_price, 
                  video_url = :video_url, short_description = :short_description, description = :description, 
                  system_requirements = :system_requirements, stock = :stock, release_date = :release_date, 
                  is_upcoming = :is_upcoming";
        
        if (isset($data['total_sales'])) {
            $query .= ", total_sales = :total_sales";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':slug', $data['slug']);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':sale_price', $data['sale_price'] ?? null);
        $stmt->bindValue(':video_url', $data['video_url'] ?? null);
        $stmt->bindValue(':short_description', $data['short_description'] ?? null);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':system_requirements', $data['system_requirements'] ?? null);
        $stmt->bindValue(':stock', $data['stock'] ?? 9999);
        $stmt->bindValue(':release_date', $data['release_date'] ?? null);
        $stmt->bindValue(':is_upcoming', $data['is_upcoming'] ?? 0);
        
        if (isset($data['total_sales'])) {
            $stmt->bindValue(':total_sales', $data['total_sales']);
        }
        
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function incrementSales($id, $quantity) {
        $query = "UPDATE games SET total_sales = total_sales + :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':quantity', $quantity);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function findById($id) {
        $query = "SELECT * FROM games WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findBySlug($slug) {
        $query = "SELECT * FROM games WHERE slug = :slug";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll($page = 1, $perPage = 10, $search = '', $categoryId = null) {
        $params = [];

        // Nếu có categoryId thì join bảng map để lọc đúng chủ đề
        if (!empty($categoryId)) {
            $query = "SELECT DISTINCT g.* 
                      FROM games g
                      JOIN game_category_map gcm ON g.id = gcm.game_id
                      WHERE gcm.category_id = :category_id";
            $params[':category_id'] = (int)$categoryId;
        } else {
            $query = "SELECT * FROM games WHERE 1=1";
        }
        
        if (!empty($search)) {
            $query .= " AND (title LIKE :search OR short_description LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $query .= " ORDER BY created_at DESC";
        return $this->paginate($query, $params, $page, $perPage);
    }
    
    public function getRecommended($limit = 8) {
        $query = "SELECT * FROM games WHERE is_upcoming = 0 ORDER BY total_sales DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUpcoming($limit = 8) {
        $query = "SELECT * FROM games WHERE is_upcoming = 1 ORDER BY release_date ASC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getOnSale($limit = 8) {
        $query = "SELECT * FROM games WHERE sale_price IS NOT NULL AND sale_price < price ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByCategory($categoryId, $limit = 8, $excludeId = null) {
        $query = "SELECT g.* FROM games g 
                  JOIN game_category_map gcm ON g.id = gcm.game_id 
                  WHERE gcm.category_id = :category_id";
        $params = [':category_id' => $categoryId];
        
        if ($excludeId) {
            $query .= " AND g.id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $query .= " ORDER BY g.created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByCategories($categoryIds, $limit = 8, $excludeId = null) {
        if (empty($categoryIds)) {
            return [];
        }
        
        // Create placeholders for IN clause
        $placeholders = [];
        $params = [];
        foreach ($categoryIds as $index => $categoryId) {
            $key = ':category_id_' . $index;
            $placeholders[] = $key;
            $params[$key] = $categoryId;
        }
        
        $query = "SELECT DISTINCT g.* FROM games g 
                  JOIN game_category_map gcm ON g.id = gcm.game_id 
                  WHERE gcm.category_id IN (" . implode(',', $placeholders) . ")";
        
        if ($excludeId) {
            $query .= " AND g.id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $query .= " ORDER BY g.total_sales DESC, g.created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function search($keyword, $page = 1, $perPage = 12) {
        $query = "SELECT * FROM games WHERE title LIKE :keyword OR short_description LIKE :keyword";
        $params = [':keyword' => "%$keyword%"];
        $query .= " ORDER BY total_sales DESC";
        return $this->paginate($query, $params, $page, $perPage);
    }
    
    public function delete($id) {
        $query = "DELETE FROM games WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM games";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>
