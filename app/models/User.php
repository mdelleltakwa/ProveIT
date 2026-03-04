<?php
require_once __DIR__ . '/Database.php';

class User {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function create($name, $email, $password, $role = 'user') {
        $stmt = $this->conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
        return $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
    }

    public function getByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
    }

    public function update($id, $name, $email) {
        $stmt = $this->conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        return $stmt->execute([$name, $email, $id]);
    }

    public function updateProfile($id, $name, $email, $bio = null, $avatar_url = null) {
        $stmt = $this->conn->prepare("UPDATE users SET name=?, email=?, bio=?, avatar_url=? WHERE id=?");
        return $stmt->execute([$name, $email, $bio, $avatar_url, $id]);
    }

    public function updatePassword($id, $newPassword) {
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE id=?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }

    // XP System
    public function getXPLog($userId, $limit = 20) {
    $limit = (int)$limit;

    $stmt = $this->conn->prepare(
        "SELECT * FROM xp_log 
         WHERE user_id = ? 
         ORDER BY created_at DESC 
         LIMIT $limit"
    );

    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

    // Leaderboard
    public function getLeaderboard($limit = 50) {
    $limit = (int) $limit;
    $stmt = $this->conn->query(
        "SELECT id, name, email, xp, role, avatar_url
         FROM users
         ORDER BY xp DESC
         LIMIT $limit"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Badges
    public function getBadges($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM badges WHERE user_id=? ORDER BY awarded_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function hasBadge($userId, $badgeType) {
        $stmt = $this->conn->prepare("SELECT 1 FROM badges WHERE user_id=? AND badge_type=? LIMIT 1");
        $stmt->execute([$userId, $badgeType]);
        return $stmt->fetch() !== false;
    }

    public function awardBadge($userId, $badgeType, $hackathonId = null) {
        if ($this->hasBadge($userId, $badgeType)) return false;
        $stmt = $this->conn->prepare("INSERT INTO badges (user_id, badge_type, hackathon_id) VALUES (?,?,?)");
        return $stmt->execute([$userId, $badgeType, $hackathonId]);
    }

    public function countAll() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
}
