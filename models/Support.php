<?php
require_once __DIR__ . '/BaseModel.php';

class Support extends BaseModel
{
    public function create($data)
    {
        $sql = "INSERT INTO support_requests (user_id, name, email, subject, message) 
                VALUES (:user_id, :name, :email, :subject, :message)";

        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':user_id' => $data['user_id'] ?? null,
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':subject' => $data['subject'],
                ':message' => $data['message']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating support request: " . $e->getMessage());
            return false;
        }
    }

    public function getAll($page = 1, $perPage = 10)
    {
        $query = "SELECT s.*, u.name as user_name 
                  FROM support_requests s 
                  LEFT JOIN users u ON s.user_id = u.id 
                  ORDER BY s.created_at DESC";
        return $this->paginate($query, [], $page, $perPage);
    }

    public function getById($id)
    {
        $sql = "SELECT s.*, u.name as user_name, u.email as user_email 
                FROM support_requests s 
                LEFT JOIN users u ON s.user_id = u.id 
                WHERE s.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE support_requests SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM support_requests WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>