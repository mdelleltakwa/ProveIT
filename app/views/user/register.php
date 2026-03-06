<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription – ProveIt</title>
    <link rel="icon" type="image/png" href="public/images/logo.png">
    <link rel="stylesheet" href="public/css/app.css">
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.0.96/css/materialdesignicons.min.css" rel="stylesheet"></head>
<body>
<div class="pi-signup-wrapper">
    <div class="pi-signup-card animate-in">
        <div class="logo">
             <div class="logo">
                <!-- <h2>Prove<span class="accent">it</span></h2> -->
                 <img src="public/images/icon.png" width="150">
            </div>
        </div>

        <h3>Créer un compte</h3>

        <?php if (isset($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

       <form method="POST" id="registerForm">
    <?= csrf_field() ?>

    <!-- Role Selection -->
    <div class="pi-form-group" id="roleSelection">
        <label>Je suis</label>
        <div class="pi-role-select">
            <label class="pi-role-option" id="role-candidat-label">
                <input type="radio" name="role" value="candidat" hidden>
                <div class="pi-role-card">

<span class="pi-role-icon"><i class="mdi mdi-laptop"></i></span>

                    <strong>Candidat</strong>
                    <span class="pi-role-desc">Participer aux hackathons, soumettre des projets</span>
                </div>
            </label>
            <label class="pi-role-option" id="role-orga-label">
                <input type="radio" name="role" value="organisateur" hidden>
                <div class="pi-role-card">
                    <span class="pi-role-icon"><i class="mdi mdi-target"></i></span>
                    <strong>Organisateur</strong>
                    <span class="pi-role-desc">Créer des hackathons, évaluer les projets</span>
                </div>
            </label>
        </div>
    </div>

    <!-- Candidate Form -->
    <div id="candidatForm" style="display:none;">
        <div class="pi-form-group">
            <label for="candidatName">Nom</label>
            <input type="text" id="candidatName" name="name" class="pi-input" placeholder="Votre nom" required>
        </div>
        <div class="pi-form-group">
            <label for="candidatEmail">Email</label>
            <input type="email" id="candidatEmail" name="email" class="pi-input" placeholder="email@exemple.com" required>
        </div>
        <div class="pi-form-group">
            <label for="candidatPassword">Mot de passe</label>
            <input type="password" id="candidatPassword" name="password" class="pi-input" placeholder="Min 6 caractères" required>
        </div>
        <div class="pi-form-group">
            <label for="candidatConfirm">Confirmer le mot de passe</label>
            <input type="password" id="candidatConfirm" name="confirm_password" class="pi-input" placeholder="Répéter le mot de passe" required>
            <div id="passwordError" class="pi-alert pi-alert-error" style="display:none;margin-top:0.5rem;">Les mots de passe ne correspondent pas</div>
        </div>
        <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg mb-3">Créer le compte Candidat</button>
        <button type="button" class="pi-btn pi-btn-secondary pi-btn-block" id="backFromCandidat">← Retour</button>
    </div>

    <!-- Organizer Form -->
    <div id="orgaForm" style="display:none;">
        <div class="pi-form-group">
            <label for="orgaName">Nom</label>
            <input type="text" id="orgaName" name="name" class="pi-input" placeholder="Votre nom" required>
        </div>
        <div class="pi-form-group">
            <label for="orgaEmail">Email</label>
            <input type="email" id="orgaEmail" name="email" class="pi-input" placeholder="email@exemple.com" required>
        </div>
        <div class="pi-form-group">
            <label for="orgaPassword">Mot de passe</label>
            <input type="password" id="orgaPassword" name="password" class="pi-input" placeholder="Min 6 caractères" required>
        </div>
        <div class="pi-form-group">
            <label for="orgaConfirm">Confirmer le mot de passe</label>
            <input type="password" id="orgaConfirm" name="confirm_password" class="pi-input" placeholder="Répéter le mot de passe" required>
            <div id="passwordError" class="pi-alert pi-alert-error" style="display:none;margin-top:0.5rem;">Les mots de passe ne correspondent pas</div>
        </div>
        <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg mb-3">Créer le compte Organisateur</button>
        <button type="button" class="pi-btn pi-btn-secondary pi-btn-block" id="backFromOrga">← Retour</button>
    </div>
</form>



        <div class="pi-signup-footer">
            Déjà un compte ? <a href="index.php?controller=User&action=login">Se connecter</a>
        </div>
    </div>
</div>

<script>
    // Get elements
    const roleCandidat = document.getElementById('role-candidat-label');
    const roleOrga = document.getElementById('role-orga-label');
    const roleSelection = document.getElementById('roleSelection');
    const candidatForm = document.getElementById('candidatForm');
    const orgaForm = document.getElementById('orgaForm');
    const backFromCandidat = document.getElementById('backFromCandidat');
    const backFromOrga = document.getElementById('backFromOrga');

    // Click events to show forms
    roleCandidat.addEventListener('click', () => {
        roleSelection.style.display = 'none';
        candidatForm.style.display = 'block';
    });

    roleOrga.addEventListener('click', () => {
        roleSelection.style.display = 'none';
        orgaForm.style.display = 'block';
    });

    // Back buttons
    backFromCandidat.addEventListener('click', () => {
        candidatForm.style.display = 'none';
        roleSelection.style.display = 'block';
    });

    backFromOrga.addEventListener('click', () => {
        orgaForm.style.display = 'none';
        roleSelection.style.display = 'block';
    });
</script>   

<style>
.pi-role-select { display: flex; gap: 0.75rem; }
.pi-role-option { flex: 1; cursor: pointer; }
.pi-role-option input[type="radio"] { display: none; }
.pi-role-card {
    display: flex; flex-direction: column; align-items: center; gap: 0.3rem;
    padding: 1rem 0.75rem; border: 2px solid var(--border); border-radius: var(--radius);
    background: var(--bg-input); text-align: center; transition: all 0.2s;
    height:150px
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
