<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – ProveIt</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="pi-container">
    <div class="pi-page-header animate-in">
        <h1>Admin <span class="accent">Dashboard</span></h1>
        <p>Overview of platform activity and management tools.</p>
    </div>

    <!-- Stats -->
    <div class="pi-stats-grid animate-in">
        <div class="pi-stat-card">
            <div class="value"><?= (int)$totalUsers ?></div>
            <div class="label">Users</div>
        </div>
        <div class="pi-stat-card">
            <div class="value" style="color:var(--green);"><?= (int)$activeHackathons ?></div>
            <div class="label">Active Hackathons</div>
        </div>
        <div class="pi-stat-card">
            <div class="value"><?= (int)$totalHackathons ?></div>
            <div class="label">Total Hackathons</div>
        </div>
        <div class="pi-stat-card">
            <div class="value" style="color:var(--purple);"><?= (int)$totalSubmissions ?></div>
            <div class="label">Submissions</div>
        </div>
        <div class="pi-stat-card">
            <div class="value" style="color:var(--orange);"><?= (int)$totalComments ?></div>
            <div class="label">Comments</div>
        </div>
        <div class="pi-stat-card">
            <div class="value" style="color:var(--green);"><?= (int)$totalVotes ?></div>
            <div class="label">Votes</div>
        </div>
    </div>

    <!-- Chart -->
    <div class="pi-card mb-3 animate-in">
        <div class="pi-section-title">📊 Platform Overview</div>
        <canvas id="statsChart" height="100"></canvas>
    </div>

    <!-- Users Table -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">👥 Users</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>XP</th><th>Role</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="mono text-accent"><?= (int)($u['xp'] ?? 0) ?></td>
                    <td><span class="pi-rank-badge" style="background:<?= $u['role']==='admin' ? 'var(--red-dim)' : 'var(--accent-dim)' ?>;color:<?= $u['role']==='admin' ? 'var(--red)' : 'var(--accent)' ?>;"><?= htmlspecialchars($u['role']) ?></span></td>
                    <td><a href="index.php?controller=Admin&action=deleteUser&id=<?= (int)$u['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Delete user?');">Delete</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hackathons Table -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">🏁 Hackathons</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead>
                    <tr><th>ID</th><th>Title</th><th>Category</th><th>Deadline</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($hackathons as $h): ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$h['id'] ?></td>
                    <td><a href="index.php?controller=Hackathon&action=detail&id=<?= (int)$h['id'] ?>"><?= htmlspecialchars($h['title']) ?></a></td>
                    <td class="text-muted"><?= htmlspecialchars($h['category']) ?></td>
                    <td class="mono text-xs"><?= htmlspecialchars($h['deadline'] ?? '') ?></td>
                    <td><a href="index.php?controller=Admin&action=deleteHackathon&id=<?= (int)$h['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Delete?');">Delete</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">📦 Submissions</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead>
                    <tr><th>ID</th><th>Hackathon</th><th>User</th><th>Description</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($submissions as $s): ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$s['id'] ?></td>
                    <td><?= (int)$s['hackathon_id'] ?></td>
                    <td><?= (int)$s['user_id'] ?></td>
                    <td class="desc-cell text-muted"><?= htmlspecialchars($s['description']) ?></td>
                    <td><a href="index.php?controller=Admin&action=deleteSubmission&id=<?= (int)$s['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Delete?');">Delete</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comments Table -->
    <div class="pi-section animate-in">
        <div class="pi-section-title">💬 Comments</div>
        <div class="pi-table-wrap">
            <table class="pi-table">
                <thead>
                    <tr><th>ID</th><th>Submission</th><th>User</th><th>Content</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($comments as $cm): ?>
                <tr>
                    <td class="mono text-xs"><?= (int)$cm['id'] ?></td>
                    <td><?= (int)$cm['submission_id'] ?></td>
                    <td><?= (int)$cm['user_id'] ?></td>
                    <td class="desc-cell text-muted"><?= htmlspecialchars($cm['content']) ?></td>
                    <td><a href="index.php?controller=Admin&action=deleteComment&id=<?= (int)$cm['id'] ?>" class="pi-btn pi-btn-danger pi-btn-sm" onclick="return confirm('Delete?');">Delete</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statsChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Users', 'Hackathons', 'Submissions', 'Comments', 'Votes'],
                datasets: [{
                    label: 'Count',
                    data: [<?= (int)$totalUsers ?>, <?= (int)$totalHackathons ?>, <?= (int)$totalSubmissions ?>, <?= (int)$totalComments ?>, <?= (int)$totalVotes ?>],
                    backgroundColor: ['#3b9eff', '#00d68f', '#a855f7', '#ffa502', '#ff4757'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#1e1e2a' },
                        ticks: { color: '#555566' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#8888a0' }
                    }
                }
            }
        });
    }
});
</script>
</body>
</html>
