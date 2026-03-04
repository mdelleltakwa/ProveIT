<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – ProveIt</title>
    <link rel="icon" type="image/png" href="public/images/logo.png">
    <link rel="stylesheet" href="public/css/app.css">
</head>
<body>
<div class="pi-auth-wrapper">
    <div class="pi-auth-card animate-in">
        <div class="logo">
            <h2>Prove<span class="accent">it</span></h2>
            <p>Join the hackathon community</p>
        </div>

        <h3>Create account</h3>

        <?php if (isset($error)): ?>
            <div class="pi-alert pi-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <?= csrf_field() ?>
            <div class="pi-form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="pi-input" placeholder="Your name" required>
            </div>
            <div class="pi-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="pi-input" placeholder="you@example.com" required>
            </div>
            <div class="pi-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="pi-input" placeholder="Min 6 characters" required>
            </div>
            <div class="pi-form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="pi-input" placeholder="Repeat password" required>
                <div id="passwordError" class="pi-alert pi-alert-error" style="display:none; margin-top:0.5rem;">Passwords do not match</div>
            </div>
            <button type="submit" class="pi-btn pi-btn-primary pi-btn-block pi-btn-lg">Create account</button>
        </form>

        <div class="pi-auth-footer">
            Already have an account? <a href="index.php?controller=User&action=login">Sign in</a>
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
