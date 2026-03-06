<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="pi-container pi-container-sm">
    <a href="index.php?controller=User&action=profile" class="pi-btn pi-btn-ghost pi-btn-sm mb-3">← Retour</a>

    <div class="pi-card animate-in">
        <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">Modifier le Profil</h2>

        <?php if (!empty($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- ✅ UN SEUL FORM (avec enctype pour l'upload) -->
        <form method="POST"
              enctype="multipart/form-data"
              action="index.php?controller=User&action=editProfile">

            <?= csrf_field() ?>

            <div class="pi-form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" class="pi-input"
                       value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="pi-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="pi-input"
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="pi-form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" class="pi-textarea"
                          placeholder="Parlez-nous de vous..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>

            <div class="pi-form-group">
                <label for="avatar">Avatar</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
            </div>

            <div class="pi-form-group">
                <label for="new_password">Nouveau mot de passe <span class="text-xs text-muted">(laisser vide pour garder)</span></label>
                <input type="password" id="new_password" name="new_password" class="pi-input"
                       placeholder="Min 6 caractères">
            </div>

            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block">Sauvegarder les modifications</button>
        </form>
    </div>
</div>

</body>
</html>