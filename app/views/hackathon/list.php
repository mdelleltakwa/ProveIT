<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
<title>Hackathons – ProveIt</title>
<style>
/* ── Pagination ── */
.pi-pagination { display:flex; align-items:center; justify-content:center; gap:.5rem; margin-top:2.5rem; flex-wrap:wrap; }
.pi-page-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:2.25rem; height:2.25rem; padding:0 .6rem;
    border:1.5px solid var(--pi-bd); border-radius:var(--pi-rs);
    background:transparent; color:var(--pi-tx); font-size:.875rem; font-weight:500;
    cursor:pointer; transition:all .15s; text-decoration:none; user-select:none;
    font-family:var(--pi-bd-font);
}
.pi-page-btn:hover:not(:disabled):not(.active) {
    background:var(--pi-s2); border-color:var(--pi-coral); color:var(--pi-coral); transform:translateY(-1px);
}
.pi-page-btn.active { background:var(--pi-coral); border-color:var(--pi-coral); color:#111114; pointer-events:none; font-weight:700; }
.pi-page-btn:disabled { opacity:.3; cursor:not-allowed; }
.pi-page-btn.arrow { font-size:1rem; }
.pi-page-ellipsis { display:inline-flex; align-items:center; justify-content:center; min-width:2.25rem; height:2.25rem; color:var(--pi-tx3); font-size:.875rem; letter-spacing:.1em; user-select:none; }
.pi-pagination-info { text-align:center; font-size:.78rem; color:var(--pi-tx3); margin-top:.75rem; }

/* Grid fade on page change */
.pi-grid.is-loading { opacity:0; transform:translateY(6px); pointer-events:none; }
.pi-grid { transition:opacity .2s ease, transform .2s ease; }
</style>
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<?php
/* ── Pagination config ── */
$perPage     = 6;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$totalItems  = count($hackathons ?? []);
$totalPages  = max(1, (int)ceil($totalItems / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset      = ($currentPage - 1) * $perPage;
$pageItems   = array_slice($hackathons ?? [], $offset, $perPage);
?>

<div class="container py-4" style="position:relative;z-index:1;max-width:1280px">

    <!-- Hero -->
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4 anim">
        <div>
            <h1 style="font-size:2.1rem;font-weight:800;letter-spacing:-.04em">
                <span style="color:var(--pi-tx)">Hack</span><span style="background:var(--pi-grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent">athons</span>
            </h1>
            <?php if (is_organisateur()): ?>
                <p class="text-secondary mb-0">Vos hackathons. Créez, gérez, évaluez les projets soumis.</p>
            <?php elseif (is_candidat()): ?>
                <p class="text-secondary mb-0">Design, tech, marketing, business — relevez le défi en 48h.</p>
            <?php else: ?>
                <p class="text-secondary mb-0">Vue d'ensemble de la plateforme.</p>
            <?php endif; ?>
        </div>
        <?php if (is_organisateur() || is_admin()): ?>
            <a href="index.php?controller=Hackathon&action=create" class="btn btn-coral px-4 py-2 rounded-3">
                <i class="bi bi-plus-circle me-1"></i>Créer un Hackathon
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="rounded-4 p-3 mb-4 anim ad1" style="background:var(--pi-s1);border:1px solid var(--pi-bd)">
        <form method="GET" action="index.php" id="filter-form">
            <input type="hidden" name="controller" value="Hackathon">
            <input type="hidden" name="action" value="list">
            <input type="hidden" name="page" value="1" id="page-input">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control bg-dark" style="border-color:var(--pi-bd);border-radius:var(--pi-rs);font-size:.85rem" placeholder="🔍 Rechercher..." value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select bg-dark" style="border-color:var(--pi-bd);border-radius:var(--pi-rs);font-size:.85rem">
                        <option value="">Catégories</option>
                        <?php foreach ($categories ?? [] as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($category) && $category === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select bg-dark" style="border-color:var(--pi-bd);border-radius:var(--pi-rs);font-size:.85rem">
                        <option value="">Tous</option>
                        <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Actifs</option>
                        <option value="ended" <?= ($status ?? '') === 'ended' ? 'selected' : '' ?>>Terminés</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select bg-dark" style="border-color:var(--pi-bd);border-radius:var(--pi-rs);font-size:.85rem">
                        <option value="newest" <?= ($sort ?? '') === 'newest' ? 'selected' : '' ?>>Récents</option>
                        <option value="popular" <?= ($sort ?? '') === 'popular' ? 'selected' : '' ?>>Populaires</option>
                        <option value="ending" <?= ($sort ?? '') === 'ending' ? 'selected' : '' ?>>Bientôt finis</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100" style="font-size:.85rem;border-color:var(--pi-bd2);border-radius:var(--pi-rs)">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if (empty($hackathons)): ?>
        <div class="text-center py-5 rounded-4" style="background:var(--pi-s1);border:1px solid var(--pi-bd)">
            <i class="bi bi-inbox display-4" style="color:var(--pi-tx3)"></i>
            <p class="mt-3" style="color:var(--pi-tx2)">Aucun hackathon trouvé.</p>
        </div>
    <?php else: ?>

        <!-- Grid -->
        <div class="row g-3 pi-grid" id="hackathon-grid">
            <?php $i = 0; foreach ($pageItems as $h):
                $ti = $h['time_info'];
                $tc = $ti['ended'] ? 'timer-end' : ($ti['urgent'] ? 'timer-urg' : 'timer-live');
                $tIcon = $ti['ended'] ? 'bi-stop-circle-fill' : ($ti['urgent'] ? 'bi-hourglass-split' : 'bi-play-circle-fill');
                $tagClass = category_tag_class($h['category']);
                $ad = min($i, 6);
            ?>
            <div class="col-md-6 col-xl-4">
                <div class="pi-card pi-card-lift rounded-4 h-100 overflow-hidden anim ad<?= $ad ?>">
                    <div class="grad-bar"></div>
                    <div class="card-body d-flex flex-column p-4" style="padding-top:1.5rem!important">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge rounded-pill <?= $tagClass ?>" style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:.3rem .55rem"><?= htmlspecialchars($h['category']) ?></span>
                            <span class="badge rounded-pill <?= $tc ?> <?= $ti['urgent'] && !$ti['ended'] ? 'timer-pulse' : '' ?>" style="font-size:.68rem;font-weight:600;font-family:var(--pi-mono);padding:.25rem .55rem">
                                <i class="bi <?= $tIcon ?> me-1"></i><?= htmlspecialchars($ti['text']) ?>
                            </span>
                        </div>

                        <h5 class="fw-bold mb-2" style="font-size:1.1rem">
                            <a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>" class="text-decoration-none" style="color:var(--pi-tx)"><?= htmlspecialchars($h['title']) ?></a>
                        </h5>
                        <p class="small mb-3" style="color:var(--pi-tx2);display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;line-height:1.55"><?= htmlspecialchars($h['description']) ?></p>

                        <?php if (!empty($h['image'])): ?>
                            <img src="<?= htmlspecialchars($h['image']) ?>" class="rounded-3 mb-3" style="width:100%;max-height:140px;object-fit:cover">
                        <?php endif; ?>

                        <div class="d-flex gap-3 small mb-3 mt-auto" style="color:var(--pi-tx3)">
                            <span><i class="bi bi-people-fill me-1"></i><?= (int)($h['participants_count'] ?? 0) ?></span>
                            <span><i class="bi bi-box-fill me-1"></i><?= (int)($h['submissions_count'] ?? 0) ?> projets</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3" style="border-top:1px solid var(--pi-bd)">
                            <span style="font-size:.72rem;color:var(--pi-tx3)">par <?= htmlspecialchars($h['creator_name'] ?? 'Inconnu') ?></span>
                            <div class="d-flex gap-2">
                                <?php if (is_candidat()): ?>
                                    <?php if (!$ti['ended'] && !$h['has_joined']): ?>
                                        <form method="POST" action="index.php?controller=Hackathon&action=join" class="d-inline">
                                            <input type="hidden" name="hackathon_id" value="<?= (int)$h['id'] ?>">
                                            <button class="btn btn-sm" style="background:rgba(108,192,112,.12);color:#6cc070;border:1px solid rgba(108,192,112,.2);font-size:.78rem;font-weight:600">
                                                <i class="bi bi-plus-circle me-1"></i>+<?= XP_JOIN ?>XP
                                            </button>
                                        </form>
                                    <?php elseif ($h['has_joined']): ?>
                                        <span class="badge" style="background:rgba(108,192,112,.1);color:#6cc070;font-size:.72rem;padding:.35rem .6rem">
                                            <i class="bi bi-check-circle me-1"></i>Inscrit
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (is_organisateur() && (int)($h['created_by'] ?? 0) === (int)$_SESSION['user']['id']): ?>
                                    <a href="index.php?controller=Hackathon&action=edit&id=<?= (int)$h['id'] ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;border-color:var(--pi-bd2)">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                <?php endif; ?>

                                <a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>" class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;border-color:var(--pi-bd2)">Voir <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $i++; endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="pi-pagination" id="pagination" aria-label="Pagination">
            <button class="pi-page-btn arrow" data-page="<?= $currentPage - 1 ?>" <?= $currentPage <= 1 ? 'disabled' : '' ?> aria-label="Page précédente">&#8592;</button>

            <?php
            $window = [];
            for ($p = 1; $p <= $totalPages; $p++) {
                if ($p === 1 || $p === $totalPages || abs($p - $currentPage) <= 1) {
                    $window[] = $p;
                }
            }
            $window = array_unique($window);
            sort($window);
            $prev = null;
            foreach ($window as $p):
                if ($prev !== null && $p - $prev > 1): ?>
                    <span class="pi-page-ellipsis" aria-hidden="true">…</span>
                <?php endif; ?>
                <button class="pi-page-btn <?= $p === $currentPage ? 'active' : '' ?>" data-page="<?= $p ?>" aria-label="Page <?= $p ?>" <?= $p === $currentPage ? 'aria-current="page"' : '' ?>><?= $p ?></button>
            <?php $prev = $p;
            endforeach; ?>

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
    const grid = document.getElementById('hackathon-grid');
    const pagination = document.getElementById('pagination');
    const pageInput = document.getElementById('page-input');
    const filterForm = document.getElementById('filter-form');
    if (!pagination || !grid) return;

    function goToPage(page) {
        grid.classList.add('is-loading');
        setTimeout(() => { pageInput.value = page; filterForm.submit(); }, 180);
    }

    pagination.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-page]');
        if (!btn || btn.disabled || btn.classList.contains('active')) return;
        goToPage(parseInt(btn.dataset.page, 10));
    });

    pagination.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const btn = e.target.closest('[data-page]');
        if (!btn || btn.disabled || btn.classList.contains('active')) return;
        e.preventDefault();
        goToPage(parseInt(btn.dataset.page, 10));
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page') && parseInt(urlParams.get('page'), 10) > 1) {
        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
})();
</script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>