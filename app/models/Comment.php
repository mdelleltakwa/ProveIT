<?php
require_once __DIR__ . '/Database.php';

class Comment {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function create($submission_id, $user_id, $content) {
        $stmt = $this->conn->prepare("INSERT INTO comments (submission_id, user_id, content) VALUES (?,?,?)");
        return $stmt->execute([$submission_id, $user_id, $content]);
    }

    public function getAllBySubmission($submission_id) {
        $stmt = $this->conn->prepare("
            SELECT c.*, u.name AS author_name, u.avatar_url
            FROM comments c JOIN users u ON c.user_id = u.id
            WHERE c.submission_id=? ORDER BY c.created_at ASC
        ");
        $stmt->execute([$submission_id]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM comments WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $this->conn->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM comments ORDER BY id DESC")->fetchAll();
    }

    public function countAll() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM comments")->fetchColumn();
    }
}
