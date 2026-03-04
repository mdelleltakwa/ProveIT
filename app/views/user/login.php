<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – ProveIt</title>
    <link rel="icon" type="image/png" href="public/images/logo.png">
    <link rel="stylesheet" href="public/css/app.css">
</head>
<body>
<div class="pi-auth-wrapper">
    <div class="pi-auth-card animate-in">
        <div class="logo">
            <h2>Prove<span class="accent">it</span></h2>
            <p>Hackathon Platform</p>
        </div>

        <h3>Welcome back</h3>

        <?php if (isset($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="pi-alert pi-alert-success">Account created! Please log in.</div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            <div class="pi-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="pi-input" placeholder="you@example.com" required>
            </div>
            <div class="pi-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="pi-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg">Sign in</button>
        </form>

        <div class="pi-auth-footer">
            Don't have an account? <a href="index.php?controller=User&action=register">Create one</a>
        </div>
    </div>
</div>
</body>
</html>
