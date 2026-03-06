<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($hackathon['title']) ?> – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php';
$timeInfo = $hackathon['time_info'];
$timerClass = $timeInfo['ended'] ? 'ended' : ($timeInfo['urgent'] ? 'urgent' : 'active');
$isOwner = (int)$hackathon['created_by'] === (int)$_SESSION['user']['id'];
$isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
$hasJoined = $hackathon['has_joined'];
?>

<div class="pi-container pi-container-md">
    <a href="index.php?controller=Hackathon&action=list" class="pi-btn pi-btn-ghost pi-btn-sm mb-3" style="display:inline-flex;">← Retour aux hackathons</a>

    <!-- Header -->
    <div class="pi-card mb-3 animate-in" style="position:relative;overflow:hidden;">
        <?php if (!empty($hackathon['image'])): ?>
            <div style="margin:-1.5rem -1.5rem 1.25rem;height:200px;overflow:hidden;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                <img src="<?= htmlspecialchars($hackathon['image']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
            </div>
        <?php endif; ?>

        <div class="flex items-center gap-2 mb-2 flex-wrap">
            <span class="category-tag" style="display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;background:var(--accent-dim);color:var(--accent);"><?= htmlspecialchars($hackathon['category']) ?></span>
            <span class="pi-timer <?= $timerClass ?>"><?= $timerClass === 'ended' ? '⏹' : '⏱' ?> <?= htmlspecialchars($timeInfo['text']) ?></span>
        </div>

        <h1 style="font-size:1.75rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:0.5rem;"><?= htmlspecialchars($hackathon['title']) ?></h1>
        <p class="text-secondary" style="line-height:1.7;margin-bottom:1rem;"><?= nl2br(htmlspecialchars($hackathon['description'])) ?></p>

        <div class="pi-hackathon-meta">
            <span class="meta-item">🎯 Créé par <strong><?= htmlspecialchars($hackathon['creator_name'] ?? 'Inconnu') ?></strong></span>
            <span class="meta-item">👥 <?= (int)$hackathon['participants_count'] ?> participants</span>
            <span class="meta-item">📅 Date limite : <?= htmlspecialchars($hackathon['deadline']) ?></span>
        </div>

        <div class="flex items-center gap-1 flex-wrap mt-2">
            <?php if (!$timeInfo['ended'] && !$hasJoined): ?>
                <form method="POST" action="index.php?controller=Hackathon&action=join" class="d-inline">
                    <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                    <button type="submit" class="pi-btn pi-btn-success">🚀 Rejoindre le Hackathon (+<?= XP_JOIN ?>XP)</button>
                </form>
            <?php elseif ($hasJoined): ?>
                <span class="pi-btn pi-btn-ghost" style="color:var(--green);cursor:default;">✓ Vous avez rejoint</span>
            <?php endif; ?>
            <?php if ($isOwner || $isAdmin): ?>
                <a href="index.php?controller=Hackathon&action=edit&id=<?= (int)$hackathon['id'] ?>" class="pi-btn pi-btn-outline pi-btn-sm">Modifier</a>
                <a href="index.php?controller=Hackathon&action=delete&id=<?= (int)$hackathon['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Supprimer ce hackathon ?');">Supprimer</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Participants -->
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

    <!-- Submit Project Form -->
    <?php if ($hasJoined && !$timeInfo['ended']): ?>
    <div class="pi-card mb-3 animate-in">
        <div class="pi-section-title">📤 Soumettre votre projet</div>
        <form method="POST" action="index.php?controller=Submission&action=create">
            <?= csrf_field() ?>
            <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
            <div class="pi-form-group">
                <label for="title">Titre du projet</label>
                <input type="text" id="title" name="title" class="pi-input" placeholder="Mon super projet" required>
            </div>
            <div class="pi-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="pi-textarea" placeholder="Qu'avez-vous construit ? Comment ça marche ?" required></textarea>
            </div>
            <div class="flex gap-2">
                <div class="pi-form-group" style="flex:1">
                    <label for="github_link">Lien GitHub</label>
                    <input type="url" id="github_link" name="github_link" class="pi-input" placeholder="https://github.com/...">
                </div>
                <div class="pi-form-group" style="flex:1">
                    <label for="demo_link">Lien de démo</label>
                    <input type="url" id="demo_link" name="demo_link" class="pi-input" placeholder="https://...">
                </div>
            </div>
            <button type="submit" class="pi-btn pi-btn-primary">Soumettre le projet (+<?= XP_SUBMIT ?>XP)</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Submissions / Rankings -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">🏆 Soumissions <span class="text-xs text-muted">(classées par votes)</span></div>

        <?php if (empty($submissions)): ?>
            <div class="pi-card text-center" style="padding:2rem">
                <p class="text-muted">Aucune soumission encore. Soyez le premier à soumettre !</p>
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
                        <a href="<?= htmlspecialchars($s['demo_link']) ?>" target="_blank" class="pi-btn pi-btn-outline pi-btn-sm">Démo ↗</a>
                    <?php endif; ?>
                    <?php if ((int)$s['user_id'] === (int)$_SESSION['user']['id']): ?>
                        <a href="index.php?controller=Submission&action=edit&id=<?= (int)$s['id'] ?>" class="pi-btn pi-btn-outline pi-btn-sm">Modifier</a>
                        <a href="index.php?controller=Submission&action=delete&id=<?= (int)$s['id'] ?>" class="pi-btn pi-btn-ghost pi-btn-sm" style="color:var(--red)" onclick="return confirm('Supprimer ?');">Supprimer</a>
                    <?php endif; ?>
                </div>

                <!-- Comments -->
                <div style="border-top:1px solid var(--border);padding-top:0.75rem;">
                    <p class="text-xs text-muted mb-1" style="font-weight:600;">COMMENTAIRES</p>
                    <?php $comments = $s['comments'] ?? []; ?>
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $cm): ?>
                        <div class="pi-comment">
                            <div class="pi-comment-avatar"><?= strtoupper(substr($cm['author_name'],0,1)) ?></div>
                            <div class="pi-comment-body">
                                <span class="author"><?= htmlspecialchars($cm['author_name']) ?></span>
                                <span class="time"><?= date('M j, H:i', strtotime($cm['created_at'] ?? 'now')) ?></span>
                                <?php if ((int)($cm['user_id']??0) === (int)$_SESSION['user']['id'] || $isAdmin): ?>
                                    <a href="index.php?controller=Comment&action=delete&id=<?= (int)$cm['id'] ?>&hackathon_id=<?= (int)$hackathon['id'] ?>" class="text-xs" style="color:var(--red);margin-left:0.5rem;">supprimer</a>
                                <?php endif; ?>
                                <p class="text-sm mt-1" style="color:var(--text-secondary);"><?= htmlspecialchars($cm['content']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-xs text-muted">Aucun commentaire encore.</p>
                    <?php endif; ?>

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
</body>
</html>
