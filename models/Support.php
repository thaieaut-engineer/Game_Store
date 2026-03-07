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
}
?>