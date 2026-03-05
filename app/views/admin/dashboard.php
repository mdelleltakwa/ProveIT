<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>
<div class="pi-container">
    <div class="pi-page-header animate-in">
        <h1>Admin <span class="accent">Dashboard</span></h1>
        <p>Vue d'ensemble de la plateforme et outils de gestion.</p>
    </div>

    <div class="pi-stats-grid animate-in">
        <div class="pi-stat-card"><div class="value"><?= (int)$totalUsers ?></div><div class="label">Utilisateurs</div></div>
        <div class="pi-stat-card"><div class="value" style="color:var(--green);"><?= (int)$activeHackathons ?></div><div class="label">Hackathons actifs</div></div>
        <div class="pi-stat-card"><div class="value"><?= (int)$totalHackathons ?></div><div class="label">Total Hackathons</div></div>
        <div class="pi-stat-card"><div class="value" style="color:var(--purple);"><?= (int)$totalSubmissions ?></div><div class="label">Projets</div></div>
        <div class="pi-stat-card"><div class="value" style="color:var(--orange);"><?= (int)$totalComments ?></div><div class="label">Commentaires</div></div>
        <div class="pi-stat-card"><div class="value" style="color:var(--green);"><?= (int)$totalVotes ?></div><div class="label">Votes</div></div>
    </div>

    <div class="pi-card mb-3 animate-in">
        <div class="pi-section-title">📊 Vue d'ensemble</div>
        <canvas id="statsChart" height="100"></canvas>
    </div>

    <!-- Users -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">👥 Utilisateurs</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>XP</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u):
                    $roleColor = match($u['role']) {
                        'admin' => 'var(--red)',
                        'organisateur' => 'var(--purple)',
                        'candidat' => 'var(--green)',
                        default => 'var(--text-muted)'
                    };
                ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="pi-rank-badge" style="background:<?= $roleColor ?>15;color:<?= $roleColor ?>;"><?= htmlspecialchars($u['role']) ?></span></td>
                    <td class="mono text-accent"><?= (int)($u['xp'] ?? 0) ?></td>
                    <td><a href="index.php?controller=Admin&action=deleteUser&id=<?= (int)$u['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Supprimer ?');">Supprimer</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hackathons -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">🏁 Hackathons</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead><tr><th>ID</th><th>Titre</th><th>Catégorie</th><th>Deadline</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($hackathons as $h): ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$h['id'] ?></td>
                    <td><a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>"><?= htmlspecialchars($h['title']) ?></a></td>
                    <td class="text-muted"><?= htmlspecialchars($h['category']) ?></td>
                    <td class="mono text-xs"><?= htmlspecialchars($h['deadline'] ?? '') ?></td>
                    <td><a href="index.php?controller=Admin&action=deleteHackathon&id=<?= (int)$h['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Supprimer ?');">Supprimer</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Submissions -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">📦 Projets</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead><tr><th>ID</th><th>Hackathon</th><th>User</th><th>Description</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($submissions as $s): ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$s['id'] ?></td>
                    <td><?= (int)$s['hackathon_id'] ?></td>
                    <td><?= (int)$s['user_id'] ?></td>
                    <td class="desc-cell text-muted"><?= htmlspecialchars($s['description']) ?></td>
                    <td><a href="index.php?controller=Admin&action=deleteSubmission&id=<?= (int)$s['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Supprimer ?');">Supprimer</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comments -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">💬 Commentaires</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead><tr><th>ID</th><th>Projet</th><th>User</th><th>Contenu</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($comments as $cm): ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$cm['id'] ?></td>
                    <td><?= (int)$cm['submission_id'] ?></td>
                    <td><?= (int)$cm['user_id'] ?></td>
                    <td class="desc-cell text-muted"><?= htmlspecialchars($cm['content']) ?></td>
                    <td><a href="index.php?controller=Admin&action=deleteComment&id=<?= (int)$cm['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Supprimer ?');">Supprimer</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statsChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Utilisateurs', 'Hackathons', 'Projets', 'Commentaires', 'Votes'],
                datasets: [{
                    data: [<?= (int)$totalUsers ?>, <?= (int)$totalHackathons ?>, <?= (int)$totalSubmissions ?>, <?= (int)$totalComments ?>, <?= (int)$totalVotes ?>],
                    backgroundColor: ['#3b9eff', '#00d68f', '#a855f7', '#ffa502', '#ff4757'],
                    borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#1e1e2a' }, ticks: { color: '#555566' } },
                    x: { grid: { display: false }, ticks: { color: '#8888a0' } }
                }
            }
        });
    }
});
</script>
</body>
</html>
