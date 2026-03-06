<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lancer un Hackathon – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>
<div class="pi-container pi-container-sm">
    <a href="index.php?controller=Hackathon&action=list" class="pi-btn pi-btn-ghost pi-btn-sm mb-3">← Retour</a>
    <div class="pi-card animate-in">
        <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">🚀 Lancer un Hackathon</h2>
        <form method="POST">
            <?= csrf_field() ?>
            <div class="pi-form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" class="pi-input" placeholder="ex. Défi Chatbot IA" required>
            </div>
            <div class="pi-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="pi-textarea" placeholder="Décrivez le défi, les règles, et ce que les participants doivent construire..." required></textarea>
            </div>
            <div class="pi-form-group">
                <label for="category">Catégorie</label>
                <input type="text" id="category" name="category" class="pi-input" placeholder="ex. IA, Web, Mobile, Design" required>
            </div>
            <div class="pi-form-group">
                <label>Date limite</label>
                <div class="flex gap-2 items-center">
                    <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;color:var(--text-secondary);text-transform:none;letter-spacing:0;">
                        <input type="radio" name="deadline_type" value="48h" checked> Auto 48h
                    </label>
                    <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;color:var(--text-secondary);text-transform:none;letter-spacing:0;">
                        <input type="radio" name="deadline_type" value="custom"> Personnalisé
                    </label>
                </div>
                <input type="datetime-local" id="deadline" name="deadline" class="pi-input mt-1" style="display:none;">
            </div>
            <div class="pi-form-group">
                <label for="image">URL de l'image de couverture <span class="text-xs text-muted">(optionnel)</span></label>
                <input type="url" id="image" name="image" class="pi-input" placeholder="https://...">
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg">Lancer un Hackathon</button>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('input[name="deadline_type"]').forEach(r => {
    r.addEventListener('change', () => {
        document.getElementById('deadline').style.display = r.value === 'custom' && r.checked ? 'block' : 'none';
    });
});
</script>
</body>
</html>
