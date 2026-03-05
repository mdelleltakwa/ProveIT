<?php
$currentUser = $_SESSION['user'] ?? null;
$currentAction = $_GET['action'] ?? '';
$currentController = $_GET['controller'] ?? '';
$userRole = $currentUser['role'] ?? '';
?>
<nav class="pi-nav">
    <div class="pi-nav-inner">
        <a href="index.php?controller=Hackathon&action=list" class="pi-logo">
            <span>Prove</span><span class="accent">it</span>
        </a>

        <?php if ($currentUser): ?>
        <div class="pi-nav-links">
            <a href="index.php?controller=Hackathon&action=list"
               class="<?= $currentController === 'Hackathon' && $currentAction === 'list' ? 'active' : '' ?>">
                Hackathons
            </a>
            <a href="index.php?controller=User&action=leaderboard"
               class="<?= $currentAction === 'leaderboard' ? 'active' : '' ?>">
                Leaderboard
            </a>
            <?php if ($userRole === 'admin'): ?>
            <a href="index.php?controller=Admin&action=dashboard"
               class="<?= $currentController === 'Admin' ? 'active' : '' ?>">
                Admin
            </a>
            <?php endif; ?>
        </div>

        <div class="pi-nav-user">
            <!-- Role indicator -->
            <?php
            $roleLabel = match($userRole) {
                'organisateur' => '🎯 Organisateur',
                'candidat' => '💻 Candidat',
                'admin' => '⚙️ Admin',
                default => $userRole
            };
            $roleColor = match($userRole) {
                'organisateur' => 'var(--purple)',
                'candidat' => 'var(--green)',
                'admin' => 'var(--red)',
                default => 'var(--text-muted)'
            };
            ?>
            <span class="pi-xp-badge" style="background:<?= $roleColor ?>15;border-color:<?= $roleColor ?>30;color:<?= $roleColor ?>;">
                <?= $roleLabel ?>
            </span>

            <?php if ($userRole === 'candidat'): ?>
            <span class="pi-xp-badge"><?= (int)($currentUser['xp'] ?? 0) ?> XP</span>
            <?php endif; ?>

            <?php
                $navInitial = strtoupper(mb_substr($currentUser['name'] ?? '', 0, 1));
                $navAvatar  = $currentUser['avatar_url'] ?? '';
            ?>
            <a href="index.php?controller=User&action=profile" class="pi-avatar" title="<?= htmlspecialchars($currentUser['name']) ?>" style="overflow:hidden;">
                <?php if (!empty($navAvatar)): ?>
                    <img src="<?= BASE_URL . htmlspecialchars($navAvatar) ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                <?php else: ?>
                    <?= $navInitial ?>
                <?php endif; ?>
            </a>
            <a href="index.php?controller=User&action=logout" class="pi-btn pi-btn-ghost pi-btn-sm">Déconnexion</a>
        </div>
        <?php endif; ?>
    </div>
</nav>
