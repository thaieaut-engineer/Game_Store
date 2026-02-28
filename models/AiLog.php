<?php
class AiLog extends BaseModel
{
    public function create($userId, $question, $answer, $type)
    {
        $query = "INSERT INTO ai_logs (user_id, question, answer, type) VALUES (:user_id, :question, :answer, :type)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':question', $question);
        $stmt->bindValue(':answer', $answer);
        $stmt->bindValue(':type', $type);
        return $stmt->execute();
    }

    public function getLogsByUser($userId, $limit = 10)
    {
        $query = "SELECT * FROM ai_logs WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>