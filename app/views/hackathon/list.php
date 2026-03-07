<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hackathons – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
    <style>
        /* ── Pagination ───────────────────────────────── */
        .pi-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }

        .pi-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 .6rem;
            border: 1.5px solid var(--border, #e2e8f0);
            border-radius: 8px;
            background: transparent;
            color: var(--text, #1e293b);
            font-size: .875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, border-color .15s, color .15s, transform .1s;
            text-decoration: none;
            user-select: none;
        }

        .pi-page-btn:hover:not(:disabled):not(.active) {
            background: var(--surface-2, #f1f5f9);
            border-color: var(--primary, #6366f1);
            color: var(--primary, #6366f1);
            transform: translateY(-1px);
        }

        .pi-page-btn.active {
            background: var(--primary, #6366f1);
            border-color: var(--primary, #6366f1);
            color: #fff;
            pointer-events: none;
        }

        .pi-page-btn:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .pi-page-btn.arrow {
            font-size: 1rem;
        }

        .pi-page-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            color: var(--text-muted, #94a3b8);
            font-size: .875rem;
            letter-spacing: .1em;
            user-select: none;
        }

        .pi-pagination-info {
            text-align: center;
            font-size: .8rem;
            color: var(--text-muted, #94a3b8);
            margin-top: .75rem;
        }

        /* ── Grid fade on page change ─────────────────── */
        .pi-hackathon-grid {
            transition: opacity .2s ease, transform .2s ease;
        }

        .pi-hackathon-grid.is-loading {
            opacity: 0;
            transform: translateY(6px);
            pointer-events: none;
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<?php
/* ── Pagination config ─────────────────────────────── */
$perPage      = 6;                        // cards per page
$currentPage  = max(1, (int)($_GET['page'] ?? 1));
$totalItems   = count($hackathons ?? []);
$totalPages   = max(1, (int)ceil($totalItems / $perPage));
$currentPage  = min($currentPage, $totalPages);
$offset       = ($currentPage - 1) * $perPage;
$pageItems    = array_slice($hackathons ?? [], $offset, $perPage);

/* Build a URL preserving all current filters + a new page number */
function pageUrl(int $p): string {
    $q = array_merge($_GET, ['page' => $p]);
    return 'index.php?' . http_build_query($q);
}
?>

<div class="pi-container">
    <div class="pi-page-header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h1>Hackathons</h1>
                <?php if (is_organisateur()): ?>
                    <p>Créez et gérez vos hackathons. Consultez les projets soumis.</p>
                <?php elseif (is_candidat()): ?>
                    <p>Rejoignez un hackathon de 48h. Codez. Prouvez votre talent.</p>
                <?php else: ?>
                    <p>48-hour challenges. Build fast. Prove yourself.</p>
                <?php endif; ?>
            </div>
            <?php if (is_organisateur() || is_admin()): ?>
                <a href="index.php?controller=Hackathon&action=create" class="pi-btn pi-btn-primary pi-btn-lg">+ Créer un Hackathon</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="index.php" class="pi-filter-bar" id="filter-form">
        <input type="hidden" name="controller" value="Hackathon">
        <input type="hidden" name="action" value="list">
        <input type="hidden" name="page" value="1" id="page-input">
        <input type="text" name="search" class="pi-input" placeholder="Rechercher..." value="<?= htmlspecialchars($search ?? '') ?>">
        <select name="category" class="pi-select">
            <option value="">Toutes catégories</option>
            <?php foreach ($categories ?? [] as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($category) && $category === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="pi-select">
            <option value="">Tous</option>
            <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Actifs</option>
            <option value="ended"  <?= ($status ?? '') === 'ended'  ? 'selected' : '' ?>>Terminés</option>
        </select>
        <select name="sort" class="pi-select">
            <option value="newest"  <?= ($sort ?? '') === 'newest'  ? 'selected' : '' ?>>Récents</option>
            <option value="popular" <?= ($sort ?? '') === 'popular' ? 'selected' : '' ?>>Populaires</option>
            <option value="ending"  <?= ($sort ?? '') === 'ending'  ? 'selected' : '' ?>>Bientôt finis</option>
        </select>
        <button type="submit" class="pi-btn pi-btn-outline">Filtrer</button>
    </form>

    <?php if (empty($hackathons)): ?>
        <div class="pi-card text-center" style="padding:3rem">
            <p class="text-secondary">Aucun hackathon trouvé.</p>
        </div>
    <?php else: ?>

        <!-- Grid -->
        <div class="pi-hackathon-grid" id="hackathon-grid">
            <?php foreach ($pageItems as $h):
                $timeInfo   = $h['time_info'];
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
                    <span class="meta-item">👥 <?= (int)($h['participants_count'] ?? 0) ?> participants</span>
                    <span class="meta-item">📦 <?= (int)($h['submissions_count'] ?? 0) ?> projets</span>
                </div>

                <?php if (!empty($h['image'])): ?>
                    <img src="<?= htmlspecialchars($h['image']) ?>" alt="" style="width:100%;border-radius:8px;margin-bottom:1rem;max-height:160px;object-fit:cover;">
                <?php endif; ?>

                <div class="pi-hackathon-footer">
                    <span class="text-xs text-muted">par <?= htmlspecialchars($h['creator_name'] ?? 'Inconnu') ?></span>
                    <div class="pi-link-group">
                        <?php if (is_candidat()): ?>
                            <?php if (!$timeInfo['ended'] && !$h['has_joined']): ?>
                                <form method="POST" action="index.php?controller=Hackathon&action=join" class="d-inline">
                                    <input type="hidden" name="hackathon_id" value="<?= (int)$h['id'] ?>">
                                    <button type="submit" class="pi-btn pi-btn-success pi-btn-sm">Rejoindre +<?= XP_JOIN ?>XP</button>
                                </form>
                            <?php elseif ($h['has_joined']): ?>
                                <span class="pi-btn pi-btn-ghost pi-btn-sm" style="color:var(--green);cursor:default;">✓ Inscrit</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (is_organisateur() && (int)($h['created_by'] ?? 0) === (int)$_SESSION['user']['id']): ?>
                            <a href="index.php?controller=Hackathon&action=edit&id=<?= (int)$h['id'] ?>" class="pi-btn pi-btn-outline pi-btn-sm">Modifier</a>
                        <?php endif; ?>

                        <a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>" class="pi-btn pi-btn-outline pi-btn-sm">Voir →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="pi-pagination" id="pagination" aria-label="Pagination">

            <!-- Previous -->
            <button class="pi-page-btn arrow" data-page="<?= $currentPage - 1 ?>" <?= $currentPage <= 1 ? 'disabled' : '' ?> aria-label="Page précédente">&#8592;</button>

            <?php
            // Build smart page window: always show first, last, current ±1
            $window = [];
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i === 1 || $i === $totalPages || abs($i - $currentPage) <= 1) {
                    $window[] = $i;
                }
            }
            $window = array_unique($window);
            sort($window);

            $prev = null;
            foreach ($window as $p):
                if ($prev !== null && $p - $prev > 1): ?>
                    <span class="pi-page-ellipsis" aria-hidden="true">…</span>
                <?php endif; ?>
                <button
                    class="pi-page-btn <?= $p === $currentPage ? 'active' : '' ?>"
                    data-page="<?= $p ?>"
                    aria-label="Page <?= $p ?>"
                    <?= $p === $currentPage ? 'aria-current="page"' : '' ?>
                ><?= $p ?></button>
            <?php
                $prev = $p;
            endforeach; ?>

            <!-- Next -->
            <button class="pi-page-btn arrow" data-page="<?= $currentPage + 1 ?>" <?= $currentPage >= $totalPages ? 'disabled' : '' ?> aria-label="Page suivante">&#8594;</button>
        </nav>

        <p class="pi-pagination-info">
            Affichage de <?= $offset + 1 ?>–<?= min($offset + $perPage, $totalItems) ?> sur <?= $totalItems ?> hackathon<?= $totalItems > 1 ? 's' : '' ?>
        </p>
        <?php endif; ?>

    <?php endif; ?>
</div>

<script>
(function () {
    const grid       = document.getElementById('hackathon-grid');
    const pagination = document.getElementById('pagination');
    const pageInput  = document.getElementById('page-input');
    const filterForm = document.getElementById('filter-form');

    if (!pagination || !grid) return;

    /* Navigate to a given page number */
    function goToPage(page) {
        /* Fade the grid out */
        grid.classList.add('is-loading');

        /* Small delay so the fade is visible, then navigate */
        setTimeout(() => {
            pageInput.value = page;
            filterForm.submit();
        }, 180);
    }

    /* Attach click listeners to every page button */
    pagination.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-page]');
        if (!btn || btn.disabled || btn.classList.contains('active')) return;
        goToPage(parseInt(btn.dataset.page, 10));
    });

    /* Keyboard support: Enter / Space on focused buttons */
    pagination.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const btn = e.target.closest('[data-page]');
        if (!btn || btn.disabled || btn.classList.contains('active')) return;
        e.preventDefault();
        goToPage(parseInt(btn.dataset.page, 10));
    });

    /* Scroll back to the grid top on load (useful after pagination click) */
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page') && parseInt(urlParams.get('page'), 10) > 1) {
        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
})();
</script>
</body>
</html>