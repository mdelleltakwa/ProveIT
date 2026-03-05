<?php
require_once __DIR__ . '/../models/Vote.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Hackathon.php';
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

            $submission_id = (int)$_POST['submission_id'];
            $user_id = (int)($_SESSION['user']['id'] ?? 0);
            $role = current_role();

            $submission = (new Submission())->getById($submission_id);
            if (!$submission) {
                header('Location: index.php?controller=Hackathon&action=list');
                exit;
            }

            // hackathon id from submission
            $hackathon_id = (int)$submission['hackathon_id'];

            // Optional: block candidates from voting before deadline
            $hackathon = (new Hackathon())->getById($hackathon_id);
            $deadlinePassed = $hackathon && ($hackathon['deadline'] <= date('Y-m-d H:i:s'));

            // ✅ Candidate rules (1 vote per hackathon, can't vote self, optionally only after end)
            if ($role === CANDIDATE_ROLE) {

                // (Optionnel) ne vote qu'après fin du hackathon
                if (!$deadlinePassed) {
                    header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
                    exit;
                }

                // can't vote for yourself
                if ((int)$submission['user_id'] === $user_id) {
                    header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
                    exit;
                }

                // 1 vote total per hackathon
                if ($this->voteModel->hasVotedInHackathon($hackathon_id, $user_id)) {
                    header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
                    exit;
                }
            }

            // ✅ Vote insert: ONLY (submission_id, user_id)
            if ($this->voteModel->vote($submission_id, $user_id)) {

                // XP to owner (if not self)
                if ((int)$submission['user_id'] !== $user_id) {
                    $this->userModel->addXP($submission['user_id'], XP_RECEIVE_VOTE, 'A reçu un vote');
                }

                $voteCount = $this->voteModel->countByUser($user_id);
                if ($voteCount >= 10) {
                    $this->userModel->awardBadge($user_id, 'voter');
                }
            }

            header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
            exit;
        }

        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }
}