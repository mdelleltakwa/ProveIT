<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Hackathon – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>
<div class="pi-container pi-container-sm">
    <a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$hackathon['id'] ?>" class="pi-btn pi-btn-ghost pi-btn-sm mb-3">← Retour</a>
    <div class="pi-card animate-in">
        <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">Modifier le Hackathon</h2>
        <form method="POST">
            <?= csrf_field() ?>
            <div class="pi-form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" class="pi-input" value="<?= htmlspecialchars($hackathon['title']) ?>" required>
            </div>
            <div class="pi-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="pi-textarea" required><?= htmlspecialchars($hackathon['description']) ?></textarea>
            </div>
            <div class="pi-form-group">
                <label for="category">Catégorie</label>
                <input type="text" id="category" name="category" class="pi-input" value="<?= htmlspecialchars($hackathon['category']) ?>" required>
            </div>
            <div class="pi-form-group">
                <label for="deadline">Date limite</label>
                <input type="datetime-local" id="deadline" name="deadline" class="pi-input" value="<?= date('Y-m-d\TH:i', strtotime($hackathon['deadline'])) ?>">
            </div>
            <div class="pi-form-group">
                <label for="image">URL de l'image de couverture</label>
                <input type="url" id="image" name="image" class="pi-input" value="<?= htmlspecialchars($hackathon['image'] ?? '') ?>">
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block">Mettre à jour le Hackathon</button>
        </form>
    </div>
</div>
</body>
</html>
