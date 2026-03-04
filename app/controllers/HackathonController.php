<?php
require_once __DIR__ . '/../models/Hackathon.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Vote.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/User.php';

class HackathonController {
    private $hackathonModel;
    private $submissionModel;
    private $voteModel;
    private $commentModel;
    private $userModel;

    public function __construct() {
        $this->hackathonModel = new Hackathon();
        $this->submissionModel = new Submission();
        $this->voteModel = new Vote();
        $this->commentModel = new Comment();
        $this->userModel = new User();

        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=User&action=login');
            exit;
        }
    }

    // List all hackathons
    public function list() {
        $keyword = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');
        $sort = $_GET['sort'] ?? 'newest';
        $status = $_GET['status'] ?? '';

        $hackathons = $this->hackathonModel->getFiltered($keyword, $category, $sort, $status);
        $categories = $this->hackathonModel->getCategories();
        $currentUserId = (int)$_SESSION['user']['id'];

        foreach ($hackathons as &$h) {
            $h['has_joined'] = $this->hackathonModel->hasJoined($h['id'], $currentUserId);
            $h['time_info'] = time_remaining($h['deadline']);
        }

        $search = $keyword;
        require __DIR__ . '/../views/hackathon/list.php';
    }

    // View single hackathon detail
    public function detail() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        $id = intval($_GET['id']);
        $hackathon = $this->hackathonModel->getById($id);
        if (!$hackathon) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }

        $currentUserId = (int)$_SESSION['user']['id'];
        $hackathon['has_joined'] = $this->hackathonModel->hasJoined($id, $currentUserId);
        $hackathon['time_info'] = time_remaining($hackathon['deadline']);
        $participants = $this->hackathonModel->getParticipants($id);

        $submissions = $this->submissionModel->getAllByHackathon($id);
        foreach ($submissions as &$s) {
            $s['has_voted'] = $this->voteModel->hasVoted($s['id'], $currentUserId);
            $s['comments'] = $this->commentModel->getAllBySubmission($s['id']);
        }

        require __DIR__ . '/../views/hackathon/detail.php';
    }

    // Join a hackathon
    public function join() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hackathon_id'])) {
            $hackathonId = intval($_POST['hackathon_id']);
            $userId = $_SESSION['user']['id'];

            $hackathon = $this->hackathonModel->getById($hackathonId);
            if ($hackathon && $hackathon['deadline'] > date('Y-m-d H:i:s')) {
                if ($this->hackathonModel->join($hackathonId, $userId)) {
                    // Award XP for joining
                    $this->userModel->addXP($userId, XP_JOIN, 'Joined hackathon: ' . $hackathon['title']);
                    // Award first_join badge
                    $this->userModel->awardBadge($userId, 'first_join', $hackathonId);
                    // Check XP milestones
                    $this->checkXPBadges($userId);
                }
            }
            header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathonId);
            exit;
        }
        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }

    // Create hackathon
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                require __DIR__ . '/../views/hackathon/create.php';
                return;
            }
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $image = trim($_POST['image'] ?? '') ?: null;

            // Auto 48h deadline or custom
            $deadline_type = $_POST['deadline_type'] ?? '48h';
            if ($deadline_type === '48h') {
                $deadline = date('Y-m-d H:i:s', strtotime('+48 hours'));
            } else {
                $deadline = $_POST['deadline'] ?? date('Y-m-d H:i:s', strtotime('+48 hours'));
            }

            if ($title !== '' && $description !== '' && $category !== '') {
                $this->hackathonModel->create($_SESSION['user']['id'], $title, $description, $category, $deadline, $image);
            }
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        require __DIR__ . '/../views/hackathon/create.php';
    }

    // Edit hackathon (owner or admin)
    public function edit() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        $id = intval($_GET['id']);
        $hackathon = $this->hackathonModel->getById($id);
        if (!$hackathon) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        $isOwner = (int)$hackathon['created_by'] === (int)$_SESSION['user']['id'];
        $isAdmin = $_SESSION['user']['role'] === 'admin';
        if (!$isOwner && !$isAdmin) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                require __DIR__ . '/../views/hackathon/edit.php';
                return;
            }
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $deadline = $_POST['deadline'] ?? $hackathon['deadline'];
            $image = trim($_POST['image'] ?? '') ?: null;
            if ($title !== '' && $description !== '' && $category !== '') {
                $this->hackathonModel->update($id, $title, $description, $category, $deadline, $image);
            }
            header('Location: index.php?controller=Hackathon&action=detail&id=' . $id);
            exit;
        }
        require __DIR__ . '/../views/hackathon/edit.php';
    }

    // Delete hackathon (owner or admin)
    public function delete() {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $hackathon = $this->hackathonModel->getById($id);
            if ($hackathon) {
                $isOwner = (int)$hackathon['created_by'] === (int)$_SESSION['user']['id'];
                $isAdmin = $_SESSION['user']['role'] === 'admin';
                if ($isOwner || $isAdmin) {
                    $this->hackathonModel->delete($id);
                }
            }
        }
        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }

    private function checkXPBadges($userId) {
        $user = $this->userModel->getById($userId);
        if ($user['xp'] >= 500) $this->userModel->awardBadge($userId, 'xp500');
        if ($user['xp'] >= 1000) $this->userModel->awardBadge($userId, 'xp1000');
    }
}
