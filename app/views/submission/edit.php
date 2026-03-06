<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Soumission – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>
<div class="pi-container pi-container-sm">
    <a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$submission['hackathon_id'] ?>" class="pi-btn pi-btn-ghost pi-btn-sm mb-3">← Retour</a>
    <div class="pi-card animate-in">
        <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">Modifier la Soumission</h2>
        <form method="POST">
            <?= csrf_field() ?>
            <div class="pi-form-group">
                <label for="title">Titre du projet</label>
                <input type="text" id="title" name="title" class="pi-input" value="<?= htmlspecialchars($submission['title'] ?? '') ?>" required>
            </div>
            <div class="pi-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="pi-textarea" required><?= htmlspecialchars($submission['description']) ?></textarea>
            </div>
            <div class="pi-form-group">
                <label for="github_link">Lien GitHub</label>
                <input type="url" id="github_link" name="github_link" class="pi-input" value="<?= htmlspecialchars($submission['github_link'] ?? '') ?>">
            </div>
            <div class="pi-form-group">
                <label for="demo_link">Lien de démo</label>
                <input type="url" id="demo_link" name="demo_link" class="pi-input" value="<?= htmlspecialchars($submission['demo_link'] ?? '') ?>">
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block">Mettre à jour</button>
        </form>
    </div>
</div>
</body>
</html>
