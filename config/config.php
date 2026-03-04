<?php
// config.php - ProveIt global configuration

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'proveit');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site
define('BASE_URL', 'http://localhost/proveit/');
define('SITE_NAME', 'ProveIt');

// Roles
define('DEFAULT_ROLE', 'user');
define('ADMIN_ROLE', 'admin');

// XP rewards
define('XP_JOIN', 20);        // Join a hackathon
define('XP_SUBMIT', 30);      // Submit a project
define('XP_RECEIVE_VOTE', 10); // Receive a vote
define('XP_TOP3', 50);        // Finish top 3
define('XP_WINNER', 100);     // Win a hackathon

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF helpers
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify() {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// Badge helpers
function get_badge_info($type) {
    $badges = [
        'first_join'   => ['icon' => '🚀', 'label' => 'First Steps', 'desc' => 'Joined first hackathon'],
        'submitter'    => ['icon' => '📦', 'label' => 'Builder', 'desc' => 'Submitted a project'],
        'top3'         => ['icon' => '🥉', 'label' => 'Top 3', 'desc' => 'Finished in top 3'],
        'winner'       => ['icon' => '🏆', 'label' => 'Champion', 'desc' => 'Won a hackathon'],
        'voter'        => ['icon' => '👍', 'label' => 'Supporter', 'desc' => 'Voted on 10+ projects'],
        'streak3'      => ['icon' => '🔥', 'label' => 'On Fire', 'desc' => '3 hackathons in a row'],
        'xp500'        => ['icon' => '⭐', 'label' => 'Rising Star', 'desc' => 'Reached 500 XP'],
        'xp1000'       => ['icon' => '💎', 'label' => 'Diamond', 'desc' => 'Reached 1000 XP'],
    ];
    return $badges[$type] ?? ['icon' => '🎖️', 'label' => $type, 'desc' => ''];
}

function get_rank_from_xp($xp) {
    if ($xp >= 1000) return ['rank' => 'Legend', 'color' => '#f59e0b'];
    if ($xp >= 500) return ['rank' => 'Expert', 'color' => '#a855f7'];
    if ($xp >= 200) return ['rank' => 'Hacker', 'color' => '#3b82f6'];
    if ($xp >= 50) return ['rank' => 'Coder', 'color' => '#10b981'];
    return ['rank' => 'Rookie', 'color' => '#6b7280'];
}

function time_remaining($deadline) {
    $now = new DateTime();
    $end = new DateTime($deadline);
    if ($now >= $end) return ['text' => 'Ended', 'urgent' => true, 'ended' => true];
    $diff = $now->diff($end);
    if ($diff->days > 0) return ['text' => $diff->days . 'd ' . $diff->h . 'h left', 'urgent' => false, 'ended' => false];
    if ($diff->h > 0) return ['text' => $diff->h . 'h ' . $diff->i . 'm left', 'urgent' => $diff->h < 6, 'ended' => false];
    return ['text' => $diff->i . 'm left', 'urgent' => true, 'ended' => false];
}
