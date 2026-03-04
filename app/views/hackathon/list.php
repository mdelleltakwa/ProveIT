<!DOCTYPE html>
<html lang="en">
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
                <p>48-hour challenges. Build fast. Prove yourself.</p>
            </div>
            <a href="index.php?controller=Hackathon&action=create" class="pi-btn pi-btn-primary pi-btn-lg">
                + Launch Hackathon
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="index.php" class="pi-filter-bar">
        <input type="hidden" name="controller" value="Hackathon">
        <input type="hidden" name="action" value="list">
        <input type="text" name="search" class="pi-input" placeholder="Search hackathons..." value="<?= htmlspecialchars($search ?? '') ?>">
        <select name="category" class="pi-select">
            <option value="">All categories</option>
            <?php foreach ($categories ?? [] as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($category) && $category === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="pi-select">
            <option value="" <?= ($status ?? '') === '' ? 'selected' : '' ?>>All status</option>
            <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="ended" <?= ($status ?? '') === 'ended' ? 'selected' : '' ?>>Ended</option>
        </select>
        <select name="sort" class="pi-select">
            <option value="newest" <?= ($sort ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="popular" <?= ($sort ?? '') === 'popular' ? 'selected' : '' ?>>Most popular</option>
            <option value="ending" <?= ($sort ?? '') === 'ending' ? 'selected' : '' ?>>Ending soon</option>
        </select>
        <button type="submit" class="pi-btn pi-btn-outline">Filter</button>
    </form>

    <!-- Hackathon Grid -->
    <?php if (empty($hackathons)): ?>
        <div class="pi-card text-center" style="padding:3rem">
            <p class="text-secondary">No hackathons found. Be the first to launch one!</p>
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
                <span class="meta-item">👥 <?= (int)($h['participants_count'] ?? 0) ?> joined</span>
                <span class="meta-item">📦 <?= (int)($h['submissions_count'] ?? 0) ?> projects</span>
            </div>

            <?php if (!empty($h['image'])): ?>
                <img src="<?= htmlspecialchars($h['image']) ?>" alt="" style="width:100%;border-radius:8px;margin-bottom:1rem;max-height:160px;object-fit:cover;">
            <?php endif; ?>

            <div class="pi-hackathon-footer">
                <span class="text-xs text-muted">by <?= htmlspecialchars($h['creator_name'] ?? 'Unknown') ?></span>
                <div class="pi-link-group">
                    <?php if (!$timeInfo['ended'] && !$h['has_joined']): ?>
                        <form method="POST" action="index.php?controller=Hackathon&action=join" class="d-inline">
                            <input type="hidden" name="hackathon_id" value="<?= (int)$h['id'] ?>">
                            <button type="submit" class="pi-btn pi-btn-success pi-btn-sm">Join +<?= XP_JOIN ?>XP</button>
                        </form>
                    <?php elseif ($h['has_joined']): ?>
                        <span class="pi-btn pi-btn-ghost pi-btn-sm" style="color:var(--green);cursor:default;">✓ Joined</span>
                    <?php endif; ?>
                    <a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>" class="pi-btn pi-btn-outline pi-btn-sm">View →</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
