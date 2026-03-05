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
            <p>Rejoignez la communauté hackathon</p>
        </div>

        <h3>Créer un compte</h3>

        <?php if (isset($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <?= csrf_field() ?>

            <!-- Role Selection -->
            <div class="pi-form-group">
                <label>Je suis</label>
                <div class="pi-role-select">
                    <label class="pi-role-option" id="role-candidat-label">
                        <input type="radio" name="role" value="candidat" checked>
                        <div class="pi-role-card">
                            <span class="pi-role-icon">💻</span>
                            <strong>Candidat</strong>
                            <span class="pi-role-desc">Participer aux hackathons, soumettre des projets</span>
                        </div>
                    </label>
                    <label class="pi-role-option" id="role-orga-label">
                        <input type="radio" name="role" value="organisateur">
                        <div class="pi-role-card">
                            <span class="pi-role-icon">🎯</span>
                            <strong>Organisateur</strong>
                            <span class="pi-role-desc">Créer des hackathons, évaluer les projets</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pi-form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" class="pi-input" placeholder="Votre nom" required>
            </div>
            <div class="pi-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="pi-input" placeholder="email@exemple.com" required>
            </div>
            <div class="pi-form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="pi-input" placeholder="Min 6 caractères" required>
            </div>
            <div class="pi-form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="pi-input" placeholder="Répéter le mot de passe" required>
                <div id="passwordError" class="pi-alert pi-alert-error" style="display:none;margin-top:0.5rem;">Les mots de passe ne correspondent pas</div>
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg">Créer le compte</button>
        </form>

        <div class="pi-auth-footer">
            Déjà un compte ? <a href="index.php?controller=User&action=login">Se connecter</a>
        </div>
    </div>
</div>

<style>
.pi-role-select { display: flex; gap: 0.75rem; }
.pi-role-option { flex: 1; cursor: pointer; }
.pi-role-option input[type="radio"] { display: none; }
.pi-role-card {
    display: flex; flex-direction: column; align-items: center; gap: 0.3rem;
    padding: 1rem 0.75rem; border: 2px solid var(--border); border-radius: var(--radius);
    background: var(--bg-input); text-align: center; transition: all 0.2s;
}
.pi-role-card .pi-role-icon { font-size: 1.5rem; }
.pi-role-card strong { font-size: 0.85rem; color: var(--text-primary); }
.pi-role-desc { font-size: 0.7rem; color: var(--text-muted); line-height: 1.3; }
.pi-role-option input:checked + .pi-role-card {
    border-color: var(--accent); background: var(--accent-dim);
}
.pi-role-option input:checked + .pi-role-card strong { color: var(--accent); }
</style>

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
