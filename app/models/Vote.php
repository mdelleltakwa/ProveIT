<?php
require_once __DIR__ . '/Database.php';

class Vote {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function vote($submission_id, $user_id) {
        if ($this->hasVoted($submission_id, $user_id)) return false;
        $stmt = $this->conn->prepare("INSERT INTO votes (submission_id, user_id) VALUES (?,?)");
        return $stmt->execute([$submission_id, $user_id]);
    }

    public function hasVoted($submission_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT 1 FROM votes WHERE submission_id=? AND user_id=? LIMIT 1");
        $stmt->execute([$submission_id, $user_id]);
        return $stmt->fetch() !== false;
    }

    public function countBySubmission($submission_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM votes WHERE submission_id=?");
        $stmt->execute([$submission_id]);
        return (int)$stmt->fetchColumn();
    }

    public function countAll() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM votes")->fetchColumn();
    }

    public function countByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM votes WHERE user_id=?");
        $stmt->execute([$user_id]);
        return (int)$stmt->fetchColumn();
    }
}
