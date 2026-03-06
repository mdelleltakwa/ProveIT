<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="pi-container pi-container-md">
    <div class="pi-page-header animate-in">
        <h1>🏆 Classement</span></h1>
        <p>Les meilleurs candidats classés par XP. Participez, soumettez et récoltez des votes pour monter !</p>
    </div>

    <?php
    // Filter only candidats for leaderboard
    $candidats = array_filter($leaderboard, fn($e) => $e['role'] === 'candidat');
    $pos = 1;
    foreach ($candidats as $entry):
        $topClass = $pos === 1 ? 'top1' : ($pos === 2 ? 'top2' : ($pos === 3 ? 'top3' : ''));
        $entryRank = get_rank_from_xp($entry['xp']);
        $medal = $pos === 1 ? '🥇' : ($pos === 2 ? '🥈' : ($pos === 3 ? '🥉' : ''));
    ?>
    <div class="pi-leaderboard-row <?= $topClass ?> animate-in" style="animation-delay:<?= min($pos * 0.03, 0.5) ?>s;">
        <div class="pi-leaderboard-rank"><?= $medal ?: '#' . $pos ?></div>
        <div class="pi-leaderboard-user">
            <div class="pi-avatar" style="width:36px;height:36px;font-size:0.85rem;overflow:hidden;">
                <?php if (!empty($entry['avatar_url'])): ?>
                    <img src="<?= BASE_URL . htmlspecialchars($entry['avatar_url']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                <?php else: ?>
                    <?= strtoupper(substr($entry['name'],0,1)) ?>
                <?php endif; ?>
            </div>
            <div>
                <a href="index.php?controller=User&action=profile&id=<?= (int)$entry['id'] ?>" style="color:var(--text-primary);font-weight:600;"><?= htmlspecialchars($entry['name']) ?></a>
                <span class="pi-rank-badge" style="margin-left:0.5rem;background:<?= $entryRank['color'] ?>22;color:<?= $entryRank['color'] ?>;border:1px solid <?= $entryRank['color'] ?>33;"><?= $entryRank['rank'] ?></span>
            </div>
        </div>
        <div class="pi-leaderboard-xp">⚡ <?= number_format($entry['xp']) ?> XP</div>
    </div>
    <?php $pos++; endforeach; ?>

    <?php if (empty($candidats)): ?>
        <div class="pi-card text-center" style="padding:3rem;">
            <p class="text-muted">Aucun candidat encore. Soyez le premier !</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
