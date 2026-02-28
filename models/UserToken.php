<?php
require_once __DIR__ . '/BaseModel.php';

class UserToken extends BaseModel {
    public function create($userId, $token) {
        $expiredAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRY);
        
        // Delete old tokens for this user
        $this->deleteByUserId($userId);
        
        $query = "INSERT INTO user_tokens (user_id, token, expired_at) VALUES (:user_id, :token, :expired_at)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':expired_at', $expiredAt);
        return $stmt->execute();
    }
    
    public function validateToken($token) {
        $query = "SELECT ut.*, u.* FROM user_tokens ut 
                  JOIN users u ON ut.user_id = u.id 
                  WHERE ut.token = :token AND ut.expired_at > NOW() AND u.status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function deleteToken($token) {
        $query = "DELETE FROM user_tokens WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':token', $token);
        return $stmt->execute();
    }
    
    public function deleteByUserId($userId) {
        $query = "DELETE FROM user_tokens WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        return $stmt->execute();
    }
    
    public function cleanExpiredTokens() {
        $query = "DELETE FROM user_tokens WHERE expired_at < NOW()";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>
