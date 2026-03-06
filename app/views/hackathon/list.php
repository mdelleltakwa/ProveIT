<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hackathons – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="pi-container">
    <div class="pi-page-header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h1>Hack<span class="accent">athons</span></h1>
                <p>Défis de 48 heures. Construisez rapidement. Prouvez-vous.</p>
            </div>
            <a href="index.php?controller=Hackathon&action=create" class="pi-btn pi-btn-primary pi-btn-lg">
                + Lancer un Hackathon
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="index.php" class="pi-filter-bar">
        <input type="hidden" name="controller" value="Hackathon">
        <input type="hidden" name="action" value="list">
        <input type="text" name="search" class="pi-input" placeholder="Rechercher des hackathons..." value="<?= htmlspecialchars($search ?? '') ?>">
        <select name="category" class="pi-select">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories ?? [] as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($category) && $category === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="pi-select">
            <option value="" <?= ($status ?? '') === '' ? 'selected' : '' ?>>Tous les statuts</option>
            <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Actif</option>
            <option value="ended" <?= ($status ?? '') === 'ended' ? 'selected' : '' ?>>Terminé</option>
        </select>
        <select name="sort" class="pi-select">
            <option value="newest" <?= ($sort ?? '') === 'newest' ? 'selected' : '' ?>>Plus récent</option>
            <option value="popular" <?= ($sort ?? '') === 'popular' ? 'selected' : '' ?>>Plus populaire</option>
            <option value="ending" <?= ($sort ?? '') === 'ending' ? 'selected' : '' ?>>Se terminant bientôt</option>
        </select>
        <button type="submit" class="pi-btn pi-btn-outline">Filtrer</button>
    </form>

    <!-- Hackathon Grid -->
    <?php if (empty($hackathons)): ?>
        <div class="pi-card text-center" style="padding:3rem">
            <p class="text-secondary">Aucun hackathon trouvé. Soyez le premier à en lancer un !</p>
        </div>
    <?php else: ?>
    <div class="pi-hackathon-grid">
        <?php foreach ($hackathons as $h):
            $timeInfo = $h['time_info'];
            $timerClass = $timeInfo['ended'] ? 'ended' : ($timeInfo['urgent'] ? 'urgent' : 'active');
        ?>
        <div class="pi-hackathon-card">
            <span class="category-tag"><?= htmlspecialchars($h['category']) ?></span>

            <h3><a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>"><?= htmlspecialchars($h['title']) ?></a></h3>

            <p class="description"><?= htmlspecialchars($h['description']) ?></p>

            <div class="pi-hackathon-meta">
                <span class="pi-timer <?= $timerClass ?>">
                    <?= $timerClass === 'ended' ? '⏹' : '⏱' ?> <?= htmlspecialchars($timeInfo['text']) ?>
                </span>
                <span class="meta-item">👥 <?= (int)($h['participants_count'] ?? 0) ?> rejoint</span>
                <span class="meta-item">📦 <?= (int)($h['submissions_count'] ?? 0) ?> projets</span>
            </div>

            <?php if (!empty($h['image'])): ?>
                <img src="<?= htmlspecialchars($h['image']) ?>" alt="" style="width:100%;border-radius:8px;margin-bottom:1rem;max-height:160px;object-fit:cover;">
            <?php endif; ?>

            <div class="pi-hackathon-footer">
                <span class="text-xs text-muted">par <?= htmlspecialchars($h['creator_name'] ?? 'Inconnu') ?></span>
                <div class="pi-link-group">
                    <?php if (!$timeInfo['ended'] && !$h['has_joined']): ?>
                        <form method="POST" action="index.php?controller=Hackathon&action=join" class="d-inline">
                            <input type="hidden" name="hackathon_id" value="<?= (int)$h['id'] ?>">
                            <button type="submit" class="pi-btn pi-btn-success pi-btn-sm">Rejoindre +<?= XP_JOIN ?>XP</button>
                        </form>
                    <?php elseif ($h['has_joined']): ?>
                        <span class="pi-btn pi-btn-ghost pi-btn-sm" style="color:var(--green);cursor:default;">✓ Rejoint</span>
                    <?php endif; ?>
                    <a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>" class="pi-btn pi-btn-outline pi-btn-sm">Voir →</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
