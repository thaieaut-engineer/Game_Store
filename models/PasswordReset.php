<?php
require_once __DIR__ . '/BaseModel.php';

class PasswordReset extends BaseModel {
    public function create($email, $token) {
        $expiredAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        
        // Delete old tokens for this email
        $this->deleteByEmail($email);
        
        $query = "INSERT INTO password_resets (email, token, expired_at) VALUES (:email, :token, :expired_at)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':expired_at', $expiredAt);
        return $stmt->execute();
    }
    
    public function validateToken($token) {
        $query = "SELECT * FROM password_resets WHERE token = :token AND expired_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function deleteByEmail($email) {
        $query = "DELETE FROM password_resets WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email);
        return $stmt->execute();
    }
    
    public function deleteToken($token) {
        $query = "DELETE FROM password_resets WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':token', $token);
        return $stmt->execute();
    }
}
?>
