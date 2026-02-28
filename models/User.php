<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    public function create($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashedPassword);
        return $stmt->execute();
    }
    
    public function findByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findById($id) {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $name, $avatar = null) {
        if ($avatar) {
            $query = "UPDATE users SET name = :name, avatar = :avatar WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':avatar', $avatar);
            $stmt->bindValue(':id', $id);
        } else {
            $query = "UPDATE users SET name = :name WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':id', $id);
        }
        return $stmt->execute();
    }
    
    public function updatePassword($id, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function getAll($page = 1, $perPage = 10) {
        $query = "SELECT id, name, email, avatar, role, status, created_at FROM users ORDER BY created_at DESC";
        return $this->paginate($query, [], $page, $perPage);
    }
    
    public function updateStatus($id, $status) {
        $query = "UPDATE users SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function updateRole($id, $role) {
        $query = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function createWithRole($name, $email, $password, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':role', $role);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>
