<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>
<div class="pi-container pi-container-sm">
    <a href="index.php?controller=User&action=profile" class="pi-btn pi-btn-ghost pi-btn-sm mb-3">← Retour</a>
    <div class="pi-card animate-in">
        <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">Modifier le profil</h2>
        <?php if (isset($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="pi-form-group" style="display: flex;
    justify-content: center;">
    <!-- <label>Photo de profil <span class="text-xs text-muted">(jpg, png, max 2MB)</span></label> -->
    
    <!-- Hidden file input -->
    <input type="file" id="avatar" name="avatar" accept="image/*" style="display:none;">
    
    <!-- Profile photo wrapper -->
    <div id="avatarWrapper" style="position: relative; width: 80px; height: 80px; cursor: pointer;">
        <?php if (!empty($user['avatar_url'])): ?>
            <img id="avatarPreview" src="<?= BASE_URL . htmlspecialchars($user['avatar_url']) ?>" alt="Avatar"
                 style="width:100%; height:100%; border-radius:50%; object-fit:cover; border:2px solid #ddd;">
        <?php else: ?>
            <img id="avatarPreview" src="https://via.placeholder.com/80" alt="Avatar"
                 style="width:100%; height:100%; border-radius:50%; object-fit:cover; border:2px solid #ddd;">
        <?php endif; ?>
        <!-- Pencil icon overlay -->
        <span style="position:absolute; bottom:0; right:0; background:#fff; border-radius:50%; padding:4px; border:1px solid #ccc;">
            <i class="fas fa-pencil-alt" style="font-size:12px; color:#333;"></i>
        </span>
    </div>
</div>

            <div class="pi-form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" class="pi-input" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="pi-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="pi-input" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="pi-form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" class="pi-textarea" placeholder="Parlez-nous de vous..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>
            <!-- <div class="pi-form-group">
                <label for="avatar">Photo de profil <span class="text-xs text-muted">(jpg, png, max 2MB)</span></label>
                <input type="file" id="avatar" name="avatar" class="pi-input" accept="image/*">
                <?php if (!empty($user['avatar_url'])): ?>
                    <img src="<?= BASE_URL . htmlspecialchars($user['avatar_url']) ?>" alt="" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-top:0.5rem;">
                <?php endif; ?>
            </div>
            <div class="pi-form-group">
                <label for="new_password">Nouveau mot de passe <span class="text-xs text-muted">(laisser vide pour garder)</span></label>
                <input type="password" id="new_password" name="new_password" class="pi-input" placeholder="Min 6 caractères">
            </div> -->

<script>
    const avatarWrapper = document.getElementById('avatarWrapper');
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');

    // Click on photo opens file input
    avatarWrapper.addEventListener('click', () => {
        avatarInput.click();
    });

    // Preview selected image
    avatarInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                avatarPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block">Sauvegarder</button>
        </form>
    </div>
</div>
</body>
</html>
