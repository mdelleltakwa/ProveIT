<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($hackathon['title']) ?> – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php';
$timeInfo = $hackathon['time_info'];
$timerClass = $timeInfo['ended'] ? 'ended' : ($timeInfo['urgent'] ? 'urgent' : 'active');
$isOwner = (int)$hackathon['created_by'] === (int)$_SESSION['user']['id'];
?>

<div class="pi-container pi-container-md">
    <a href="index.php?controller=Hackathon&action=list" class="pi-btn pi-btn-ghost pi-btn-sm mb-3" style="display:inline-flex;">← Retour aux hackathons</a>

    <!-- Hackathon Header -->
    <div class="pi-card mb-3 animate-in" style="position:relative;overflow:hidden;">
        <?php if (!empty($hackathon['image'])): ?>
            <div style="margin:-1.5rem -1.5rem 1.25rem;height:200px;overflow:hidden;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                <img src="<?= htmlspecialchars($hackathon['image']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
            </div>
        <?php endif; ?>

        <div class="flex items-center gap-2 mb-2 flex-wrap">
            <span style="display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;background:var(--accent-dim);color:var(--accent);"><?= htmlspecialchars($hackathon['category']) ?></span>
            <span class="pi-timer <?= $timerClass ?>"><?= $timerClass === 'ended' ? '⏹' : '⏱' ?> <?= htmlspecialchars($timeInfo['text']) ?></span>
            <span style="display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.7rem;font-weight:600;background:var(--purple-dim);color:var(--purple);">🎯 Vue Organisateur</span>
        </div>

        <h1 style="font-size:1.75rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:0.5rem;"><?= htmlspecialchars($hackathon['title']) ?></h1>
        <p class="text-secondary" style="line-height:1.7;margin-bottom:1rem;"><?= nl2br(htmlspecialchars($hackathon['description'])) ?></p>

        <div class="pi-hackathon-meta">
            <span class="meta-item">👥 <?= (int)$hackathon['participants_count'] ?> participants</span>
            <span class="meta-item">📦 <?= count($submissions) ?> projets soumis</span>
            <span class="meta-item">📅 Deadline: <?= htmlspecialchars($hackathon['deadline']) ?></span>
        </div>

        <?php if ($isOwner || is_admin()): ?>
        <div class="flex items-center gap-1 flex-wrap mt-2">
            <a href="index.php?controller=Hackathon&action=edit&id=<?= (int)$hackathon['id'] ?>" class="pi-btn pi-btn-outline pi-btn-sm">✏️ Modifier</a>
            <a href="index.php?controller=Hackathon&action=delete&id=<?= (int)$hackathon['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Supprimer ce hackathon ?');">🗑️ Supprimer</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistics -->
    <?php if (!empty($submissions)): ?>
    <div class="pi-card mb-3 animate-in">
        <div class="pi-section-title">📊 Statistiques</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1rem;">
            <div style="text-align:center;padding:1rem;background:var(--bg-elevated);border-radius:var(--radius-sm);">
                <div style="font-family:var(--font-mono);font-size:1.5rem;font-weight:700;color:var(--accent);"><?= count($participants) ?></div>
                <div class="text-xs text-muted" style="text-transform:uppercase;">Participants</div>
            </div>
            <div style="text-align:center;padding:1rem;background:var(--bg-elevated);border-radius:var(--radius-sm);">
                <div style="font-family:var(--font-mono);font-size:1.5rem;font-weight:700;color:var(--purple);"><?= count($submissions) ?></div>
                <div class="text-xs text-muted" style="text-transform:uppercase;">Projets</div>
            </div>
            <div style="text-align:center;padding:1rem;background:var(--bg-elevated);border-radius:var(--radius-sm);">
                <div style="font-family:var(--font-mono);font-size:1.5rem;font-weight:700;color:var(--green);"><?= array_sum(array_column($submissions, 'votes_count')) ?></div>
                <div class="text-xs text-muted" style="text-transform:uppercase;">Votes</div>
            </div>
        </div>
        <canvas id="votesChart" height="80"></canvas>
    </div>
    <?php endif; ?>

    <!-- Participants List -->
    <?php if (!empty($participants)): ?>
    <div class="pi-section animate-in">
        <div class="pi-section-title">👥 Participants</div>
        <div class="pi-participants">
            <?php foreach ($participants as $p): ?>
            <a href="index.php?controller=User&action=profile&id=<?= (int)$p['id'] ?>" class="pi-participant">
                <span class="pi-avatar" style="width:24px;height:24px;font-size:0.6rem;"><?= strtoupper(substr($p['name'],0,1)) ?></span>
                <?= htmlspecialchars($p['name']) ?>
                <span class="text-xs mono text-accent"><?= (int)$p['xp'] ?>XP</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ALL Submissions (Organisateur sees everything) -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">🏆 Tous les projets <span class="text-xs text-muted">(classement par votes)</span></div>

        <?php if (empty($submissions)): ?>
            <div class="pi-card text-center" style="padding:2rem">
                <p class="text-muted">Aucun projet soumis pour le moment.</p>
            </div>
        <?php else: ?>
            <?php $rank = 1; foreach ($submissions as $s):
                $rankClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
            ?>
            <div class="pi-submission">
                <div class="pi-submission-header">
                    <span class="rank <?= $rankClass ?>">#<?= $rank ?></span>
                    <div style="flex:1">
                        <h4><?= htmlspecialchars($s['title'] ?: 'Sans titre') ?></h4>
                        <div class="user-info">
                            <a href="index.php?controller=User&action=profile&id=<?= (int)$s['user_id'] ?>"><?= htmlspecialchars($s['user_name']) ?></a>
                            <span class="mono text-xs text-accent" style="margin-left:0.5rem;"><?= (int)$s['user_xp'] ?>XP</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <?php if ($s['has_voted']): ?>
                            <span class="pi-vote-btn voted">▲ <?= (int)$s['votes_count'] ?></span>
                        <?php else: ?>
                            <form method="POST" action="index.php?controller=Vote&action=vote" class="d-inline">
                                <input type="hidden" name="submission_id" value="<?= (int)$s['id'] ?>">
                                <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                                <button type="submit" class="pi-vote-btn">▲ <?= (int)$s['votes_count'] ?></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <p class="text-sm text-secondary" style="margin-bottom:0.75rem;line-height:1.6;"><?= nl2br(htmlspecialchars($s['description'])) ?></p>

                <div class="flex items-center gap-1 flex-wrap mb-2">
                    <?php if (!empty($s['github_link'])): ?>
                        <a href="<?= htmlspecialchars($s['github_link']) ?>" target="_blank" class="pi-btn pi-btn-outline pi-btn-sm">GitHub ↗</a>
                    <?php endif; ?>
                    <?php if (!empty($s['demo_link'])): ?>
                        <a href="<?= htmlspecialchars($s['demo_link']) ?>" target="_blank" class="pi-btn pi-btn-outline pi-btn-sm">Demo ↗</a>
                    <?php endif; ?>
                </div>

                <!-- Comments -->
                <div style="border-top:1px solid var(--border);padding-top:0.75rem;">
                    <p class="text-xs text-muted mb-1" style="font-weight:600;">COMMENTAIRES</p>
                    <?php $comments = $s['comments'] ?? []; ?>
                    <?php foreach ($comments as $cm): ?>
                    <div class="pi-comment">
                        <div class="pi-comment-avatar"><?= strtoupper(substr($cm['author_name'],0,1)) ?></div>
                        <div class="pi-comment-body">
                            <span class="author"><?= htmlspecialchars($cm['author_name']) ?></span>
                            <span class="time"><?= date('d/m H:i', strtotime($cm['created_at'] ?? 'now')) ?></span>
                            <?php if ((int)($cm['user_id']??0) === (int)$_SESSION['user']['id'] || is_admin()): ?>
                                <a href="index.php?controller=Comment&action=delete&id=<?= (int)$cm['id'] ?>&hackathon_id=<?= (int)$hackathon['id'] ?>" class="text-xs" style="color:var(--red);margin-left:0.5rem;">supprimer</a>
                            <?php endif; ?>
                            <p class="text-sm mt-1" style="color:var(--text-secondary);"><?= htmlspecialchars($cm['content']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($comments)): ?>
                        <p class="text-xs text-muted">Aucun commentaire.</p>
                    <?php endif; ?>

                    <!-- Organisateur can comment on any submission -->
                    <form method="POST" action="index.php?controller=Comment&action=create" class="pi-comment-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="submission_id" value="<?= (int)$s['id'] ?>">
                        <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                        <input type="text" name="content" class="pi-input" placeholder="Ajouter un commentaire..." required>
                        <button type="submit" class="pi-btn pi-btn-outline pi-btn-sm">Envoyer</button>
                    </form>
                </div>
            </div>
            <?php $rank++; endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($submissions)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('votesChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [<?= implode(',', array_map(function($s) { return "'" . addslashes($s['user_name']) . "'"; }, $submissions)) ?>],
                datasets: [{
                    label: 'Votes',
                    data: [<?= implode(',', array_column($submissions, 'votes_count')) ?>],
                    backgroundColor: '#3b9eff',
                    borderRadius: 6, borderSkipped: false
                }]
            },
            options: {
                responsive: true, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#1e1e2a' }, ticks: { color: '#555566' } },
                    y: { grid: { display: false }, ticks: { color: '#8888a0' } }
                }
            }
        });
    }
});
</script>
<?php endif; ?>
</body>
</html>
