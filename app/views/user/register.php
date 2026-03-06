<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription – ProveIt</title>
    <link rel="icon" type="image/png" href="public/images/logo.png">
    <link rel="stylesheet" href="public/css/app.css">
</head>
<body>
<div class="pi-auth-wrapper">
    <div class="pi-auth-card animate-in">
        <div class="logo">
            <h2>Prove<span class="accent">it</span></h2>
            <p>Rejoignez la communauté des hackathons</p>
        </div>

        <h3>Créer un compte</h3>

        <?php if (isset($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <?= csrf_field() ?>
            <div class="pi-form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" class="pi-input" placeholder="Votre nom" required>
            </div>
            <div class="pi-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="pi-input" placeholder="vous@example.com" required>
            </div>
            <div class="pi-form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="pi-input" placeholder="Min 6 caractères" required>
            </div>
            <div class="pi-form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="pi-input" placeholder="Répétez le mot de passe" required>
                <div id="passwordError" class="pi-alert pi-alert-error" style="display:none; margin-top:0.5rem;">Les mots de passe ne correspondent pas</div>
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg">Créer un compte</button>
        </form>

        <div class="pi-auth-footer">
            Vous avez déjà un compte ? <a href="index.php?controller=User&action=login">Connectez-vous</a>
        </div>
    </div>
</div>
<script>
const form = document.getElementById('registerForm');
const pw = document.getElementById('password');
const cpw = document.getElementById('confirm_password');
const err = document.getElementById('passwordError');
form.addEventListener('submit', function(e) {
    if (pw.value !== cpw.value) { e.preventDefault(); err.style.display = 'block'; }
});
cpw.addEventListener('input', function() {
    if (pw.value === cpw.value) err.style.display = 'none';
});
</script>
</body>
</html>
