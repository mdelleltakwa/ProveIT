<?php
$currentUser = $_SESSION['user'] ?? null;
$currentAction = $_GET['action'] ?? '';
$currentController = $_GET['controller'] ?? '';
?>

<nav class="pi-nav">
    <div class="pi-nav-inner">

        <!-- Logo -->
        <a href="index.php?controller=Hackathon&action=list" class="pi-logo">
            <span>Prove</span><span class="accent">it</span>
        </a>

        <?php if ($currentUser): ?>

        <!-- Navigation Links -->
        <div class="pi-nav-links">
            <a href="index.php?controller=Hackathon&action=list"
               class="<?= $currentController === 'Hackathon' && $currentAction === 'list' ? 'active' : '' ?>">
                Hackathons
            </a>

            <a href="index.php?controller=User&action=leaderboard"
               class="<?= $currentAction === 'leaderboard' ? 'active' : '' ?>">
                Leaderboard
            </a>

            <?php if ($currentUser['role'] === 'admin'): ?>
            <a href="index.php?controller=Admin&action=dashboard"
               class="<?= $currentController === 'Admin' ? 'active' : '' ?>">
                Admin
            </a>
            <?php endif; ?>
        </div>

        <!-- User Section -->
        <div class="pi-nav-user">

            <!-- XP Badge -->
            <span class="pi-xp-badge">
                <?= (int)($currentUser['xp'] ?? 0) ?> XP
            </span>

            <?php
                $navInitial = strtoupper(mb_substr($currentUser['name'] ?? '', 0, 1));
                $navAvatar  = $currentUser['avatar_url'] ?? '';
            ?>

            <!-- Avatar -->
            <a href="index.php?controller=User&action=profile"
               class="pi-avatar"
               title="<?= htmlspecialchars($currentUser['name']) ?>"
               style="overflow:hidden;">

                <?php if (!empty($navAvatar)): ?>
                    <img
                        src="<?= BASE_URL . htmlspecialchars($navAvatar) ?>"
                        alt="Avatar"
                        style="width:100%;height:100%;object-fit:cover;display:block;">
                <?php else: ?>
                    <?= $navInitial ?>
                <?php endif; ?>

            </a>

            <!-- Logout -->
            <a href="index.php?controller=User&action=logout"
               class="pi-btn pi-btn-ghost pi-btn-sm">
                Logout
            </a>

        </div>

        <?php endif; ?>

    </div>
</nav>