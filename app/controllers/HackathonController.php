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

    // List all hackathons (all roles)
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
        $role = current_role();
        $hackathon['has_joined'] = $this->hackathonModel->hasJoined($id, $currentUserId);
        $hackathon['time_info'] = time_remaining($hackathon['deadline']);
        $participants = $this->hackathonModel->getParticipants($id);

        if ($role === 'organisateur' || $role === 'admin') {
            // ORGANISATEUR/ADMIN: sees ALL submissions with full details, votes, comments
            $submissions = $this->submissionModel->getAllByHackathon($id);
            foreach ($submissions as &$s) {
                $s['has_voted'] = $this->voteModel->hasVoted($s['id'], $currentUserId);
                $s['comments'] = $this->commentModel->getAllBySubmission($s['id']);
            }
            require __DIR__ . '/../views/hackathon/detail_organisateur.php';
        } else {
            // CANDIDAT: sees only OWN submission, list of other participants (names only), can vote once
            $mySubmission = $this->submissionModel->getByUserAndHackathon($currentUserId, $id);
            if ($mySubmission) {
                $mySubmission['comments'] = $this->commentModel->getAllBySubmission($mySubmission['id']);
                $mySubmission['votes_count'] = $this->voteModel->countBySubmission($mySubmission['id']);
            }

            // Other submissions: only title + author name (no descriptions/links)
            $otherSubmissions = $this->submissionModel->getOthersInHackathon($id, $currentUserId);

            // Check if candidat already voted in this hackathon
            $hasVotedInHackathon = $this->voteModel->hasVotedInHackathon($id, $currentUserId);
            $votedSubmissionId = $this->voteModel->getVotedSubmissionInHackathon($id, $currentUserId);

            require __DIR__ . '/../views/hackathon/detail_candidat.php';
        }
    }

    // Join a hackathon (candidat only)
    public function join() {
        if (!is_candidat()) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hackathon_id'])) {
            $hackathonId = intval($_POST['hackathon_id']);
            $userId = $_SESSION['user']['id'];

            $hackathon = $this->hackathonModel->getById($hackathonId);
            if ($hackathon && $hackathon['deadline'] > date('Y-m-d H:i:s')) {
                if ($this->hackathonModel->join($hackathonId, $userId)) {
                    $this->userModel->addXP($userId, XP_JOIN, 'Rejoint le hackathon: ' . $hackathon['title']);
                    $this->userModel->awardBadge($userId, 'first_join', $hackathonId);
                    $this->checkXPBadges($userId);
                }
            }
            header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathonId);
            exit;
        }
        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }

    // Create hackathon (organisateur/admin only)
    public function create() {
        if (!is_organisateur() && !is_admin()) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                require __DIR__ . '/../views/hackathon/create.php';
                return;
            }
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $image = trim($_POST['image'] ?? '') ?: null;

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

    // Edit hackathon (owner organisateur or admin)
    public function edit() {
        if (!is_organisateur() && !is_admin()) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
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
        if (!$isOwner && !is_admin()) {
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

    // Delete hackathon (owner organisateur or admin)
    public function delete() {
        if (!is_organisateur() && !is_admin()) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $hackathon = $this->hackathonModel->getById($id);
            if ($hackathon) {
                $isOwner = (int)$hackathon['created_by'] === (int)$_SESSION['user']['id'];
                if ($isOwner || is_admin()) {
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
