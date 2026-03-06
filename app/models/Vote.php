<?php
require_once __DIR__ . '/Database.php';

class Vote {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    // Vote standard (1 vote par submission)
    public function vote($submission_id, $user_id) {
        if ($this->hasVoted($submission_id, $user_id)) return false;

        $stmt = $this->conn->prepare("INSERT INTO votes (submission_id, user_id) VALUES (?, ?)");
        return $stmt->execute([(int)$submission_id, (int)$user_id]);
    }

    public function hasVoted($submission_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT 1 FROM votes WHERE submission_id=? AND user_id=? LIMIT 1");
        $stmt->execute([(int)$submission_id, (int)$user_id]);
        return $stmt->fetch() !== false;
    }

    // 1 vote par hackathon (pour candidat) : on récupère hackathon_id via submissions
    public function hasVotedInHackathon($hackathon_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT 1
            FROM votes v
            JOIN submissions s ON s.id = v.submission_id
            WHERE v.user_id = ? AND s.hackathon_id = ?
            LIMIT 1
        ");
        $stmt->execute([(int)$user_id, (int)$hackathon_id]);
        return $stmt->fetch() !== false;
    }

    public function getVotedSubmissionInHackathon($hackathon_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT v.submission_id
            FROM votes v
            JOIN submissions s ON s.id = v.submission_id
            WHERE v.user_id = ? AND s.hackathon_id = ?
            LIMIT 1
        ");
        $stmt->execute([(int)$user_id, (int)$hackathon_id]);
        $row = $stmt->fetch();
        return $row ? (int)$row['submission_id'] : null;
    }

    public function countBySubmission($submission_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM votes WHERE submission_id=?");
        $stmt->execute([(int)$submission_id]);
        return (int)$stmt->fetchColumn();
    }

    public function countAll() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM votes")->fetchColumn();
    }

    public function countByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM votes WHERE user_id=?");
        $stmt->execute([(int)$user_id]);
        return (int)$stmt->fetchColumn();
    }
}