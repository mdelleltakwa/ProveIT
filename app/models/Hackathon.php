<?php
require_once __DIR__ . '/Database.php';

class Hackathon {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function create($created_by, $title, $description, $category, $deadline, $image = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO hackathons (created_by, title, description, category, deadline, image) VALUES (?,?,?,?,?,?)"
        );
        return $stmt->execute([$created_by, $title, $description, $category, $deadline, $image]);
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM hackathons ORDER BY id DESC")->fetchAll();
    }

    public function getFiltered($keyword = '', $category = '', $sort = 'newest', $status = '') {
        $sql = "SELECT DISTINCT h.*,
            (SELECT COUNT(*) FROM participations p WHERE p.hackathon_id = h.id) AS participants_count,
            (SELECT COUNT(*) FROM submissions s WHERE s.hackathon_id = h.id) AS submissions_count,
            u.name AS creator_name
            FROM hackathons h
            LEFT JOIN users u ON h.created_by = u.id
            WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (h.title LIKE ? OR h.description LIKE ?)";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }
        if ($category !== '') {
            $sql .= " AND h.category = ?";
            $params[] = $category;
        }
        if ($status === 'active') {
            $sql .= " AND h.deadline > NOW()";
        } elseif ($status === 'ended') {
            $sql .= " AND h.deadline <= NOW()";
        }

        switch ($sort) {
            case 'popular':
                $sql .= " ORDER BY participants_count DESC, h.id DESC";
                break;
            case 'ending':
                $sql .= " ORDER BY h.deadline ASC";
                break;
            default:
                $sql .= " ORDER BY h.id DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCategories() {
        return $this->conn->query("SELECT DISTINCT category FROM hackathons ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT h.*, u.name AS creator_name,
            (SELECT COUNT(*) FROM participations p WHERE p.hackathon_id = h.id) AS participants_count
            FROM hackathons h LEFT JOIN users u ON h.created_by = u.id WHERE h.id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // New method to prevent duplicates
    public function getByTitleAndCreator($title, $created_by) {
        $stmt = $this->conn->prepare("SELECT * FROM hackathons WHERE title = ? AND created_by = ? LIMIT 1");
        $stmt->execute([$title, $created_by]);
        return $stmt->fetch();
    }

    public function update($id, $title, $description, $category, $deadline, $image = null) {
        $stmt = $this->conn->prepare(
            "UPDATE hackathons SET title=?, description=?, category=?, deadline=?, image=? WHERE id=?"
        );
        return $stmt->execute([$title, $description, $category, $deadline, $image, $id]);
    }

    public function delete($id) {
        $this->conn->prepare("DELETE FROM hackathons WHERE id=?")->execute([$id]);
    }

    // Participations
    public function join($hackathonId, $userId) {
        if ($this->hasJoined($hackathonId, $userId)) return false;
        $stmt = $this->conn->prepare("INSERT INTO participations (user_id, hackathon_id) VALUES (?,?)");
        return $stmt->execute([$userId, $hackathonId]);
    }

    public function hasJoined($hackathonId, $userId) {
        $stmt = $this->conn->prepare("SELECT 1 FROM participations WHERE hackathon_id=? AND user_id=? LIMIT 1");
        $stmt->execute([$hackathonId, $userId]);
        return $stmt->fetch() !== false;
    }

    public function getParticipants($hackathonId) {
        $stmt = $this->conn->prepare(
            "SELECT u.id, u.name, u.xp, u.avatar_url FROM participations p JOIN users u ON p.user_id = u.id WHERE p.hackathon_id=?"
        );
        $stmt->execute([$hackathonId]);
        return $stmt->fetchAll();
    }

    public function countAll() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM hackathons")->fetchColumn();
    }

    public function countActive() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM hackathons WHERE deadline > NOW()")->fetchColumn();
    }
}