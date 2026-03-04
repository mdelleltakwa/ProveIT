<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Submission.php';
require_once __DIR__ . '/../models/Vote.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                $error = 'Invalid request. Please try again.';
                require __DIR__ . '/../views/user/register.php';
                return;
            }
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $errors = [];
            if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
            if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
            if (isset($_POST['confirm_password']) && $password !== $_POST['confirm_password']) $errors[] = 'Passwords do not match.';
            if ($this->userModel->getByEmail($email)) $errors[] = 'Email already registered.';
            if (!empty($errors)) {
                $error = implode(' ', $errors);
                require __DIR__ . '/../views/user/register.php';
                return;
            }
            $this->userModel->create($name, $email, $password);
            header('Location: index.php?controller=User&action=login&registered=1');
            exit;
        }
        require __DIR__ . '/../views/user/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                $error = 'Invalid request. Please try again.';
                require __DIR__ . '/../views/user/login.php';
                return;
            }
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = $this->userModel->getByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                if ($user['role'] === 'admin') {
                    header('Location: index.php?controller=Admin&action=dashboard');
                } else {
                    header('Location: index.php?controller=Hackathon&action=list');
                }
                exit;
            }
            $error = "Invalid email or password";
        }
        require __DIR__ . '/../views/user/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=User&action=login');
            exit;
        }
        $userId = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user']['id'];
        $user = $this->userModel->getById($userId);
        if (!$user) {
            header('Location: index.php?controller=Hackathon&action=list');
            exit;
        }
        $badges = $this->userModel->getBadges($userId);
        $xpLog = $this->userModel->getXPLog($userId, 10);
        $submissions = (new Submission())->getByUser($userId);
        $rankInfo = get_rank_from_xp($user['xp']);
        $isOwn = ($userId == $_SESSION['user']['id']);
        require __DIR__ . '/../views/user/profile.php';
    }

    public function editProfile() {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?controller=User&action=login');
        exit;
    }

    $user = $this->userModel->getById($_SESSION['user']['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify()) {
            $error = 'Invalid request.';
            require __DIR__ . '/../views/user/editProfile.php';
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $new_password = $_POST['new_password'] ?? '';

        $errors = [];
        if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';

        $existing = $this->userModel->getByEmail($email);
        if ($existing && (int)$existing['id'] !== (int)$user['id']) $errors[] = 'Email already in use.';
        if ($new_password !== '' && strlen($new_password) < 6) $errors[] = 'Password must be 6+ characters.';

        // ====== AVATAR UPLOAD ======
        $avatar_url = $user['avatar_url'] ?? null; // garder l'ancien

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur upload avatar.";
            } else {
                $tmp = $_FILES['avatar']['tmp_name'];

                // Vérifier image
                $info = @getimagesize($tmp);
                if ($info === false) {
                    $errors[] = "Fichier avatar invalide (pas une image).";
                } else {
                    // Extension autorisée
                    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($ext, $allowed)) {
                        $errors[] = "Format non supporté (jpg, png, gif, webp).";
                    } else {
                        // Taille max (ex: 2MB)
                        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                            $errors[] = "Image trop grande (max 2MB).";
                        } else {
                            $filename = "avatar_" . (int)$user['id'] . "_" . time() . "." . $ext;

                            $destDir = __DIR__ . "/../../public/uploads/avatars/";
                            if (!is_dir($destDir)) {
                                mkdir($destDir, 0777, true);
                            }

                            $destPath = $destDir . $filename;

                            if (!move_uploaded_file($tmp, $destPath)) {
                                $errors[] = "Impossible d'enregistrer l'image.";
                            } else {
                                // Chemin web stocké en DB
                                $avatar_url = "public/uploads/avatars/" . $filename;
                            }
                        }
                    }
                }
            }
        }
        // ====== FIN AVATAR UPLOAD ======

        if (!empty($errors)) {
            $error = implode(' ', $errors);
            require __DIR__ . '/../views/user/editProfile.php';
            return;
        }

        $this->userModel->updateProfile($user['id'], $name, $email, $bio, $avatar_url);

        if ($new_password !== '') {
            $this->userModel->updatePassword($user['id'], $new_password);
        }

        $_SESSION['user'] = $this->userModel->getById($user['id']);
        header('Location: index.php?controller=User&action=profile');
        exit;
    }

    require __DIR__ . '/../views/user/editProfile.php';
}

    public function leaderboard() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=User&action=login');
            exit;
        }
        $leaderboard = $this->userModel->getLeaderboard(50);
        require __DIR__ . '/../views/user/leaderboard.php';
    }

    public function deleteAccount() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin') {
            header('Location: index.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
            $this->userModel->delete($_SESSION['user']['id']);
            session_destroy();
            header('Location: index.php');
            exit;
        }
        header('Location: index.php?controller=User&action=profile');
        exit;
    }
}
