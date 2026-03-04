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
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=User&action=login');
            exit;
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify() || !isset($_POST['hackathon_id'])) {
                header("Location: index.php?controller=Hackathon&action=list");
                exit;
            }
            $hackathon_id = intval($_POST['hackathon_id']);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $github_link = trim($_POST['github_link'] ?? '') ?: null;
            $demo_link = trim($_POST['demo_link'] ?? '') ?: null;
            $userId = $_SESSION['user']['id'];

            // Must have joined the hackathon
            if (!$this->hackathonModel->hasJoined($hackathon_id, $userId)) {
                header("Location: index.php?controller=Hackathon&action=detail&id=" . $hackathon_id);
                exit;
            }

            if ($description !== '' && $title !== '') {
                $this->submissionModel->create($hackathon_id, $userId, $title, $description, $github_link, $demo_link);
                // Award XP
                $hackathon = $this->hackathonModel->getById($hackathon_id);
                $this->userModel->addXP($userId, XP_SUBMIT, 'Submitted project in: ' . ($hackathon['title'] ?? 'hackathon'));
                $this->userModel->awardBadge($userId, 'submitter', $hackathon_id);
            }
            header("Location: index.php?controller=Hackathon&action=detail&id=" . $hackathon_id);
            exit;
        }
        header("Location: index.php?controller=Hackathon&action=list");
        exit;
    }

    public function edit() {
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
            if (!csrf_verify()) {
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

    public function delete() {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $submission = $this->submissionModel->getById($id);
            if ($submission && (int)$submission['user_id'] === (int)$_SESSION['user']['id']) {
                $hackathon_id = $submission['hackathon_id'];
                $this->submissionModel->delete($id);
                header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
                exit;
            }
        }
        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }
}
