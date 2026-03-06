<?php
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Submission.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new Comment();
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=User&action=login');
            exit;
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submission_id = intval($_POST['submission_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            $hackathon_id = intval($_POST['hackathon_id'] ?? 0);

            if ($submission_id && $content !== '') {
                // Candidat can only comment on their own submission
                if (is_candidat()) {
                    $submission = (new Submission())->getById($submission_id);
                    if (!$submission || (int)$submission['user_id'] !== (int)$_SESSION['user']['id']) {
                        header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
                        exit;
                    }
                }
                $this->commentModel->create($submission_id, $_SESSION['user']['id'], $content);
            }
            if ($hackathon_id) {
                header('Location: index.php?controller=Hackathon&action=detail&id=' . $hackathon_id);
                exit;
            }
        }
        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $comment = $this->commentModel->getById($id);
            if ($comment) {
                $isAuthor = (int)$comment['user_id'] === (int)$_SESSION['user']['id'];
                if ($isAuthor || is_admin() || is_organisateur()) {
                    $this->commentModel->delete($id);
                }
            }
        }
        $hackathon_id = $_GET['hackathon_id'] ?? '';
        if ($hackathon_id) {
            header('Location: index.php?controller=Hackathon&action=detail&id=' . intval($hackathon_id));
            exit;
        }
        header('Location: index.php?controller=Hackathon&action=list');
        exit;
    }
}
