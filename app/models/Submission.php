<?php
require_once __DIR__ . '/Database.php';

class Submission {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function create($hackathon_id, $user_id, $title, $description, $github_link = null, $demo_link = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO submissions (hackathon_id, user_id, title, description, github_link, demo_link) VALUES (?,?,?,?,?,?)"
        );
        return $stmt->execute([$hackathon_id, $user_id, $title, $description, $github_link, $demo_link]);
    }

    // All submissions for a hackathon (for organisateur view)
    public function getAllByHackathon($hackathon_id) {
        $stmt = $this->conn->prepare("
            SELECT s.*, u.name AS user_name, u.avatar_url, u.xp AS user_xp,
            (SELECT COUNT(*) FROM votes v WHERE v.submission_id = s.id) AS votes_count
            FROM submissions s JOIN users u ON s.user_id = u.id
            WHERE s.hackathon_id = ?
            ORDER BY votes_count DESC, s.id DESC
        ");
        $stmt->execute([$hackathon_id]);
        return $stmt->fetchAll();
    }

    // Get a candidat's own submission in a hackathon
    public function getByUserAndHackathon($user_id, $hackathon_id) {
        $stmt = $this->conn->prepare("
            SELECT s.*, u.name AS user_name
            FROM submissions s JOIN users u ON s.user_id = u.id
            WHERE s.user_id = ? AND s.hackathon_id = ?
            LIMIT 1
        ");
        $stmt->execute([$user_id, $hackathon_id]);
        return $stmt->fetch() ?: null;
    }

    // Other submissions in hackathon: only title + author (no details) for candidat view
    public function getOthersInHackathon($hackathon_id, $exclude_user_id) {
        $stmt = $this->conn->prepare("
            SELECT s.id, s.title, s.hackathon_id, s.user_id, u.name AS user_name, u.avatar_url,
            (SELECT COUNT(*) FROM votes v WHERE v.submission_id = s.id) AS votes_count
            FROM submissions s JOIN users u ON s.user_id = u.id
            WHERE s.hackathon_id = ? AND s.user_id != ?
            ORDER BY votes_count DESC, s.id DESC
        ");
        $stmt->execute([$hackathon_id, $exclude_user_id]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM submissions WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $title, $description, $github_link = null, $demo_link = null) {
        $stmt = $this->conn->prepare("UPDATE submissions SET title=?, description=?, github_link=?, demo_link=? WHERE id=?");
        return $stmt->execute([$title, $description, $github_link, $demo_link, $id]);
    }

    public function delete($id) {
        $this->conn->prepare("DELETE FROM submissions WHERE id=?")->execute([$id]);
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM submissions ORDER BY id DESC")->fetchAll();
    }

    public function countAll() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
    }

    public function getByUser($userId) {
        $stmt = $this->conn->prepare("
            SELECT s.*, h.title AS hackathon_title,
            (SELECT COUNT(*) FROM votes v WHERE v.submission_id = s.id) AS votes_count
            FROM submissions s JOIN hackathons h ON s.hackathon_id = h.id
            WHERE s.user_id=? ORDER BY s.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
