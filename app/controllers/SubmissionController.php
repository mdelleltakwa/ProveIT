<?php
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Hackathon.php';
require_once __DIR__ . '/../models/User.php';

class SubmissionController {
    private $submissionModel;
    private $hackathonModel;
    private $userModel;

    public function __construct() {
        $this->submissionModel = new Submission();
        $this->hackathonModel = new Hackathon();
        $this->userModel = new User();

        // Start session safely
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Redirect to login if not logged in
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=User&action=login');
            exit;
        }

        // Generate CSRF token if missing
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // --- CSRF helper ---
    private function csrf_verify(): bool {
        $token = $_POST['csrf_token'] ?? '';
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // --- CREATE submission ---
    public function create() {
        if (!is_candidat()) {
            header("Location: index.php?controller=Hackathon&action=list");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->csrf_verify() || !isset($_POST['hackathon_id'])) {
                header("Location: index.php?controller=Hackathon&action=list");
                exit;
            }

            $hackathon_id = intval($_POST['hackathon_id']);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $github_link = trim($_POST['github_link'] ?? '') ?: null;
            $demo_link = trim($_POST['demo_link'] ?? '') ?: null;
            $userId = $_SESSION['user']['id'];

            // Must have joined
            if (!$this->hackathonModel->hasJoined($hackathon_id, $userId)) {
                header("Location: index.php?controller=Hackathon&action=detail&id=" . $hackathon_id);
                exit;
            }

            // --- Check if submission already exists ---
            $existing = $this->submissionModel->getByUserAndHackathon($userId, $hackathon_id);
            if ($existing) {
                // Optional: flash message here
                header("Location: index.php?controller=Hackathon&action=detail&id=" . $hackathon_id);
                exit;
            }

            // Only create if title & description are filled
            if ($description !== '' && $title !== '') {
                $this->submissionModel->create($hackathon_id, $userId, $title, $description, $github_link, $demo_link);
                $hackathon = $this->hackathonModel->getById($hackathon_id);
                $this->userModel->addXP($userId, XP_SUBMIT, 'Projet soumis: ' . ($hackathon['title'] ?? 'hackathon'));
                $this->userModel->awardBadge($userId, 'submitter', $hackathon_id);
            }

            header("Location: index.php?controller=Hackathon&action=detail&id=" . $hackathon_id);
            exit;
        }

        header("Location: index.php?controller=Hackathon&action=list");
        exit;
    }

    // --- EDIT submission ---
    public function edit() {
        if (!is_candidat()) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }

        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }

        $id = intval($_GET['id']);
        $submission = $this->submissionModel->getById($id);
        if (!$submission || (int)$submission['user_id'] !== (int)$_SESSION['user']['id']) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->csrf_verify()) {
                require __DIR__ . '/../views/submission/edit.php';
                return;
            }

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $github_link = trim($_POST['github_link'] ?? '') ?: null;
            $demo_link = trim($_POST['demo_link'] ?? '') ?: null;

            if ($description !== '') {
                $this->submissionModel->update($id, $title, $description, $github_link, $demo_link);
            }

            header('Location: index.php?controller=Hackathon&action=detail&id=' . $submission['hackathon_id']);
            exit;
        }

        require __DIR__ . '/../views/submission/edit.php';
    }

    // --- DELETE submission (POST only + CSRF) ---
    public function delete() {
        if (!is_candidat()) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->csrf_verify()) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $hackathon_id = (int)($_POST['hackathon_id'] ?? 0);

        if (!$id) {
            header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
            exit;
        }

        $submission = $this->submissionModel->getById($id);

        // Only owner can delete
        if (!$submission || (int)$submission['user_id'] !== (int)$_SESSION['user']['id']) {
            header('Location: index.php?controller=Hackathon&action=detail&id=' . ($hackathon_id ?: 0));
            exit;
        }

        // Use hackathon_id from submission if missing
        if (!$hackathon_id) $hackathon_id = (int)$submission['hackathon_id'];

        $this->submissionModel->delete($id);

        // Optional: regenerate CSRF token after deletion
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
        exit;
    }
}