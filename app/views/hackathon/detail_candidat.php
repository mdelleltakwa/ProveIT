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
$hasJoined = $hackathon['has_joined'];
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
            <span style="display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.7rem;font-weight:600;background:var(--green-dim);color:var(--green);">  Vue Candidat</span>
        </div>

        <h1 style="font-size:1.75rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:0.5rem;"><?= htmlspecialchars($hackathon['title']) ?></h1>
        <p class="text-secondary" style="line-height:1.7;margin-bottom:1rem;"><?= nl2br(htmlspecialchars($hackathon['description'])) ?></p>

        <div class="pi-hackathon-meta">
            <span class="meta-item">🎯 Organisé par <strong><?= htmlspecialchars($hackathon['creator_name'] ?? 'Inconnu') ?></strong></span>
            <span class="meta-item">👥 <?= (int)$hackathon['participants_count'] ?> participants</span>
            <span class="meta-item">📅 Deadline: <?= htmlspecialchars($hackathon['deadline']) ?></span>
        </div>

        <div class="flex items-center gap-1 flex-wrap mt-2">
            <?php if (!$timeInfo['ended'] && !$hasJoined): ?>
                <form method="POST" action="index.php?controller=Hackathon&action=join" class="d-inline">
                    <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                    <button type="submit" class="pi-btn pi-btn-success">🚀 Rejoindre (+<?= XP_JOIN ?>XP)</button>
                </form>
            <?php elseif ($hasJoined): ?>
                <span class="pi-btn pi-btn-ghost" style="color:var(--green);cursor:default;">✓ Vous participez</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($hasJoined): ?>

    <!-- ═══ MY SUBMISSION ═══ -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">📦 Mon projet</div>

        <?php if ($mySubmission): ?>
            <div class="pi-card" style="border-color:var(--accent);border-width:2px;">
                <h4 style="font-size:1.1rem;font-weight:700;margin-bottom:0.5rem;"><?= htmlspecialchars($mySubmission['title'] ?: 'Sans titre') ?></h4>
                <p class="text-sm text-secondary" style="line-height:1.6;margin-bottom:0.75rem;"><?= nl2br(htmlspecialchars($mySubmission['description'])) ?></p>

                <div class="flex items-center gap-1 flex-wrap mb-2">
                    <?php if (!empty($mySubmission['github_link'])): ?>
                        <a href="<?= htmlspecialchars($mySubmission['github_link']) ?>" target="_blank" class="pi-btn pi-btn-outline pi-btn-sm">GitHub ↗</a>
                    <?php endif; ?>
                    <?php if (!empty($mySubmission['demo_link'])): ?>
                        <a href="<?= htmlspecialchars($mySubmission['demo_link']) ?>" target="_blank" class="pi-btn pi-btn-outline pi-btn-sm">Demo ↗</a>
                    <?php endif; ?>

                    <span class="mono text-sm" style="color:var(--green);font-weight:600;">▲ <?= (int)$mySubmission['votes_count'] ?> votes</span>

                    <?php if (!$timeInfo['ended']): ?>
                        <a href="index.php?controller=Submission&action=edit&id=<?= (int)$mySubmission['id'] ?>" class="pi-btn pi-btn-ghost pi-btn-sm">✏️ Modifier</a>

                        <!-- ✅ DELETE VIA POST + CSRF -->
                        <form method="POST"
                              action="index.php?controller=Submission&action=delete"
                              class="d-inline"
                              style="display:inline;"
                              onsubmit="return confirm('Supprimer votre projet ?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$mySubmission['id'] ?>">
                            <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                            <button type="submit" class="pi-btn pi-btn-ghost pi-btn-sm" style="color:var(--red);">
                                🗑️ Supprimer
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Comments on my submission -->
                <div style="border-top:1px solid var(--border);padding-top:0.75rem;">
                    <p class="text-xs text-muted mb-1" style="font-weight:600;">COMMENTAIRES SUR MON PROJET</p>
                    <?php $myComments = $mySubmission['comments'] ?? []; ?>
                    <?php foreach ($myComments as $cm): ?>
                    <div class="pi-comment">
                        <div class="pi-comment-avatar"><?= strtoupper(substr($cm['author_name'],0,1)) ?></div>
                        <div class="pi-comment-body">
                            <span class="author"><?= htmlspecialchars($cm['author_name']) ?></span>
                            <span class="time"><?= date('d/m H:i', strtotime($cm['created_at'] ?? 'now')) ?></span>
                            <?php if ((int)($cm['user_id']??0) === (int)$_SESSION['user']['id']): ?>
                                <a href="index.php?controller=Comment&action=delete&id=<?= (int)$cm['id'] ?>&hackathon_id=<?= (int)$hackathon['id'] ?>" class="text-xs" style="color:var(--red);margin-left:0.5rem;">supprimer</a>
                            <?php endif; ?>
                            <p class="text-sm mt-1" style="color:var(--text-secondary);"><?= htmlspecialchars($cm['content']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($myComments)): ?>
                        <p class="text-xs text-muted">Aucun commentaire pour le moment.</p>
                    <?php endif; ?>

                    <form method="POST" action="index.php?controller=Comment&action=create" class="pi-comment-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="submission_id" value="<?= (int)$mySubmission['id'] ?>">
                        <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                        <input type="text" name="content" class="pi-input" placeholder="Ajouter un commentaire..." required>
                        <button type="submit" class="pi-btn pi-btn-outline pi-btn-sm">Envoyer</button>
                    </form>
                </div>
            </div>

        <?php elseif (!$timeInfo['ended']): ?>
            <!-- Submit form -->
            <div class="pi-card">
                <p class="text-secondary mb-2">Vous n'avez pas encore soumis de projet. Soumettez votre travail avant la deadline !</p>
                <form method="POST" action="index.php?controller=Submission&action=create">
                    <?= csrf_field() ?>
                    <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                    <div class="pi-form-group">
                        <label for="title">Titre du projet</label>
                        <input type="text" id="title" name="title" class="pi-input" placeholder="Mon projet génial" required>
                    </div>
                    <div class="pi-form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="pi-textarea" placeholder="Décrivez votre projet, les technologies utilisées..." required></textarea>
                    </div>
                    <div class="flex gap-2">
                        <div class="pi-form-group" style="flex:1">
                            <label for="github_link">Lien GitHub</label>
                            <input type="url" id="github_link" name="github_link" class="pi-input" placeholder="https://github.com/...">
                        </div>
                        <div class="pi-form-group" style="flex:1">
                            <label for="demo_link">Lien Demo</label>
                            <input type="url" id="demo_link" name="demo_link" class="pi-input" placeholder="https://...">
                        </div>
                    </div>
                    <button type="submit" class="pi-btn pi-btn-primary">📤 Soumettre (+<?= XP_SUBMIT ?>XP)</button>
                </form>
            </div>
        <?php else: ?>
            <div class="pi-card text-center" style="padding:2rem;">
                <p class="text-muted">Le hackathon est terminé. Vous n'avez pas soumis de projet.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ═══ OTHER PARTICIPANTS ═══ -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">👥 Autres participants
            <span class="text-xs text-muted" style="font-weight:400;margin-left:0.5rem;">
                Votez pour <strong>1 seul</strong> candidat
            </span>
        </div>

        <?php if ($hasVotedInHackathon): ?>
            <div class="pi-alert" style="background:var(--green-dim);color:var(--green);border:1px solid rgba(0,214,143,0.2);margin-bottom:1rem;">
                ✓ Vous avez déjà utilisé votre vote pour ce hackathon.
            </div>
        <?php endif; ?>

        <?php if (empty($otherSubmissions)): ?>
            <div class="pi-card text-center" style="padding:2rem;">
                <p class="text-muted">Aucun autre participant n'a encore soumis de projet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($otherSubmissions as $os): ?>
            <div class="pi-submission" style="<?= ($votedSubmissionId === (int)$os['id']) ? 'border-color:var(--green);' : '' ?>">
                <div class="pi-submission-header">
                    <div class="pi-avatar" style="width:32px;height:32px;font-size:0.75rem;flex-shrink:0;">
                        <?= strtoupper(substr($os['user_name'],0,1)) ?>
                    </div>
                    <div style="flex:1">
                        <h4 style="font-size:0.95rem;"><?= htmlspecialchars($os['title'] ?: 'Projet sans titre') ?></h4>
                        <span class="text-xs text-muted">par <?= htmlspecialchars($os['user_name']) ?></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <?php if ($votedSubmissionId === (int)$os['id']): ?>
                            <span class="pi-vote-btn voted">✓ Voté</span>
                        <?php elseif (!$hasVotedInHackathon): ?>
                            <form method="POST" action="index.php?controller=Vote&action=vote" class="d-inline">
                                <input type="hidden" name="submission_id" value="<?= (int)$os['id'] ?>">
                                <input type="hidden" name="hackathon_id" value="<?= (int)$hackathon['id'] ?>">
                                <button type="submit" class="pi-vote-btn" onclick="return confirm('Voter pour <?= htmlspecialchars(addslashes($os['user_name'])) ?> ? Vous ne pouvez voter qu\'une seule fois.');">▲ Voter</button>
                            </form>
                        <?php else: ?>
                            <span class="pi-vote-btn" style="opacity:0.4;cursor:default;">—</span>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="text-xs text-muted" style="margin-top:0.5rem;font-style:italic;">
                    🔒 Les détails du projet sont masqués pour préserver la compétition.
                </p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- Not joined yet -->
    <div class="pi-card text-center animate-in" style="padding:3rem;">
        <p style="font-size:1.1rem;font-weight:600;margin-bottom:0.5rem;">Rejoignez ce hackathon pour participer !</p>
        <p class="text-muted text-sm">Inscrivez-vous pour soumettre votre projet et voter.</p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>