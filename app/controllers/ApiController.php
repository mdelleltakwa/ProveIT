<?php
require_once __DIR__ . '/../models/Hackathon.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Comment.php';

class ApiController {

    private $hackathonModel;
    private $submissionModel;
    private $commentModel;

    public function __construct() {
        $this->hackathonModel = new Hackathon();
        $this->submissionModel = new Submission();
        $this->commentModel = new Comment();
    }

    public function hackathons() {
        header('Content-Type: application/json');
        echo json_encode($this->hackathonModel->getAll());
    }

    public function submissions() {
        header('Content-Type: application/json');
        echo json_encode($this->submissionModel->getAll());
    }

    public function comments() {
        header('Content-Type: application/json');
        echo json_encode($this->commentModel->getAll());
    }
}