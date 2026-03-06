<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['name']) ?> – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php';
$roleLabel = match($user['role']) {
    'organisateur' => '🎯 Organisateur',
    'candidat' => '  Candidat',
    'admin' => '⚙️ Admin',
    default => $user['role']
};
$roleColor = match($user['role']) {
    'organisateur' => 'var(--purple)',
    'candidat' => 'var(--green)',
    'admin' => 'var(--red)',
    default => 'var(--text-muted)'
};
?>

<div class="pi-container pi-container-md">
    <div class="pi-profile-header animate-in">
        <div class="pi-profile-avatar" style="overflow:hidden;">
            <?php if (!empty($user['avatar_url'])): ?>
                <img src="<?= BASE_URL . htmlspecialchars($user['avatar_url']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
            <?php else: ?>
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div class="pi-profile-info" style="flex:1;">
            <div class="flex items-center gap-2 flex-wrap">
                <h2><?= htmlspecialchars($user['name']) ?></h2>
                <span class="pi-rank-badge" style="background:<?= $roleColor ?>15;color:<?= $roleColor ?>;border:1px solid <?= $roleColor ?>30;"><?= $roleLabel ?></span>
                <?php if ($user['role'] === 'candidat'): ?>
                    <span class="pi-rank-badge" style="background:<?= $rankInfo['color'] ?>22;color:<?= $rankInfo['color'] ?>;border:1px solid <?= $rankInfo['color'] ?>33;"><?= $rankInfo['rank'] ?></span>
                <?php endif; ?>
            </div>
            <p class="email" style="color:var(--text-muted);font-size:.85rem;"><?= htmlspecialchars($user['email']) ?></p>
            <?php if (!empty($user['bio'])): ?>
                <p class="text-sm text-secondary mt-1"><?= htmlspecialchars($user['bio']) ?></p>
            <?php endif; ?>
            <?php if ($user['role'] === 'candidat'): ?>
            <div class="pi-profile-stats">
                <div class="pi-profile-stat"><div class="value">⚡ <?= (int)$user['xp'] ?></div><div class="label">XP</div></div>
                <div class="pi-profile-stat"><div class="value"><?= count($submissions ?? []) ?></div><div class="label">Projets</div></div>
                <div class="pi-profile-stat"><div class="value"><?= count($badges ?? []) ?></div><div class="label">Badges</div></div>
            </div>
            <?php endif; ?>
        </div>
        <?php if ($isOwn): ?>
        <div><a href="index.php?controller=User&action=editProfile" class="pi-btn pi-btn-outline pi-btn-sm">Modifier le profil</a></div>
        <?php endif; ?>
    </div>

    <?php if ($user['role'] === 'candidat' && !empty($badges)): ?>
    <div class="pi-section animate-in">
        <div class="pi-section-title">🎖 Badges</div>
        <div class="pi-badges-grid">
            <?php foreach ($badges as $b):
                $info = get_badge_info($b['badge_type']);
            ?>
            <div class="pi-badge-item">
                <span class="icon"><?= $info['icon'] ?></span>
                <div><div class="label"><?= htmlspecialchars($info['label']) ?></div><div class="desc"><?= htmlspecialchars($info['desc']) ?></div></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($user['role'] === 'candidat' && !empty($xpLog) && $isOwn): ?>
    <div class="pi-section animate-in">
        <div class="pi-section-title">⚡ Activité XP</div>
        <div class="pi-card" style="padding:0;">
            <?php foreach ($xpLog as $log): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.65rem 1.25rem;border-bottom:1px solid var(--border);">
                <span class="text-sm"><?= htmlspecialchars($log['reason']) ?></span>
                <span class="mono text-sm" style="color:var(--green);font-weight:600;">+<?= (int)$log['amount'] ?> XP</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($submissions)): ?>
    <div class="pi-section animate-in">
        <div class="pi-section-title">📦 Projets</div>
        <?php foreach ($submissions as $s): ?>
        <div class="pi-submission">
            <h4><?= htmlspecialchars($s['title'] ?: 'Sans titre') ?></h4>
            <p class="text-xs text-muted mb-1">dans <?= htmlspecialchars($s['hackathon_title'] ?? '') ?> · <?= (int)$s['votes_count'] ?> votes</p>
            <p class="text-sm text-secondary"><?= htmlspecialchars(mb_substr($s['description'], 0, 200)) ?>...</p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($isOwn && $user['role'] !== 'admin'): ?>
    <div class="pi-section animate-in">
        <div class="pi-card" style="border-color:rgba(255,71,87,0.2);">
            <div class="pi-section-title" style="color:var(--red);">⚠ Zone dangereuse</div>
            <p class="text-sm text-secondary mb-2">Supprimer définitivement votre compte et toutes vos données.</p>
            <form method="POST" action="index.php?controller=User&action=deleteAccount" onsubmit="return confirm('Êtes-vous absolument sûr ? Cette action est irréversible.');">
                <?= csrf_field() ?>
                <button type="submit" class="pi-btn pi-btn-danger pi-btn-sm">Supprimer mon compte</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
