<?php
require_once __DIR__ . '/../models/Vote.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/User.php';

class VoteController {
    private $voteModel;
    private $userModel;

    public function __construct() {
        $this->voteModel = new Vote();
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=User&action=login');
            exit;
        }
    }

    public function vote() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {
            $submission_id = intval($_POST['submission_id']);
            $user_id = $_SESSION['user']['id'];

            if ($this->voteModel->vote($submission_id, $user_id)) {
                // Give XP to submission owner
                $submission = (new Submission())->getById($submission_id);
                if ($submission && (int)$submission['user_id'] !== (int)$user_id) {
                    $this->userModel->addXP($submission['user_id'], XP_RECEIVE_VOTE, 'Received a vote');
                }
                // Check voter badge
                $voteCount = $this->voteModel->countByUser($user_id);
                if ($voteCount >= 10) {
                    $this->userModel->awardBadge($user_id, 'voter');
                }
            }

            $hackathon_id = $_POST['hackathon_id'] ?? '';
            if ($hackathon_id) {
                header('Location: index.php?controller=Hackathon&action=detail&id=' . intval($hackathon_id));
                exit;
            }
        }
        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }
}
