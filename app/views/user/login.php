<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – ProveIt</title>
    <link rel="icon" type="image/png" href="public/images/logo.png">
    <link rel="stylesheet" href="public/css/app.css">
</head>
<body>
<div class="pi-auth-wrapper">
    <div class="pi-auth-card animate-in">
        <div class="logo">
            <h2>Prove<span class="accent">it</span></h2>
            <p>Plateforme de Hackathon</p>
        </div>

        <h3>Bienvenue de retour</h3>

        <?php if (isset($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="pi-alert pi-alert-success">Compte créé ! Veuillez vous connecter.</div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            <div class="pi-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="pi-input" placeholder="vous@example.com" required>
            </div>
            <div class="pi-form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="pi-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg">Se connecter</button>
        </form>

        <div class="pi-auth-footer">
            Vous n'avez pas de compte ? <a href="index.php?controller=User&action=register">Créez-en un</a>
        </div>
    </div>
</div>
</body>
</html>
