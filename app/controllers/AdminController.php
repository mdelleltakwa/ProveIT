<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Hackathon.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Vote.php';

class AdminController {
    private $userModel;
    private $hackathonModel;
    private $submissionModel;
    private $commentModel;
    private $voteModel;

    public function __construct() {
        $this->userModel = new User();
        $this->hackathonModel = new Hackathon();
        $this->submissionModel = new Submission();
        $this->commentModel = new Comment();
        $this->voteModel = new Vote();

        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit;
        }
    }

    public function dashboard() {
        $users = $this->userModel->getAll();
        $hackathons = $this->hackathonModel->getAll();
        $submissions = $this->submissionModel->getAll();
        $comments = $this->commentModel->getAll();

        $totalUsers = count($users);
        $totalHackathons = count($hackathons);
        $totalSubmissions = count($submissions);
        $totalComments = count($comments);
        $totalVotes = $this->voteModel->countAll();
        $activeHackathons = $this->hackathonModel->countActive();

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function deleteUser() {
        if (isset($_GET['id'])) $this->userModel->delete(intval($_GET['id']));
        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }

    public function deleteHackathon() {
        if (isset($_GET['id'])) $this->hackathonModel->delete(intval($_GET['id']));
        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }

    public function deleteSubmission() {
        if (isset($_GET['id'])) $this->submissionModel->delete(intval($_GET['id']));
        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }

    public function deleteComment() {
        if (isset($_GET['id'])) $this->commentModel->delete(intval($_GET['id']));
        header('Location: index.php?controller=Admin&action=dashboard');
        exit;
    }
}
