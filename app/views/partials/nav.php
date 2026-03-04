<?php
$currentUser = $_SESSION['user'] ?? null;
$currentAction = $_GET['action'] ?? '';
$currentController = $_GET['controller'] ?? '';
?>
<nav class="pi-nav">
    <div class="pi-nav-inner">
        <a href="index.php?controller=Hackathon&action=list" class="pi-logo">
            <span>Prove</span><span class="accent">it</span>
        </a>

        <?php if ($currentUser): ?>
        <div class="pi-nav-links">
            <a href="index.php?controller=Hackathon&action=list" class="<?= $currentController === 'Hackathon' && $currentAction === 'list' ? 'active' : '' ?>">
                Hackathons
            </a>
            <a href="index.php?controller=User&action=leaderboard" class="<?= $currentAction === 'leaderboard' ? 'active' : '' ?>">
                Leaderboard
            </a>
            <?php if ($currentUser['role'] === 'admin'): ?>
            <a href="index.php?controller=Admin&action=dashboard" class="<?= $currentController === 'Admin' ? 'active' : '' ?>">
                Admin
            </a>
            <?php endif; ?>
        </div>

        <div class="pi-nav-user">
            <span class="pi-xp-badge"><?= (int)($currentUser['xp'] ?? 0) ?> XP</span>
            <a href="index.php?controller=User&action=profile" class="pi-avatar" title="<?= htmlspecialchars($currentUser['name']) ?>">
                <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
            </a>
            <a href="index.php?controller=User&action=logout" class="pi-btn pi-btn-ghost pi-btn-sm">Logout</a>
        </div>
        <?php endif; ?>
    </div>
</nav>
