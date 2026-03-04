# ProveIt – Hackathon Platform

**ProveIt** is a modern web platform for organizing and participating in 48-hour hackathons. Features a gamification system with XP, badges, and a global leaderboard.

## Features

### Users
- Registration & Login with CSRF protection
- Profile with XP, rank, badges, and project history
- Edit profile (name, email, bio, avatar, password)

### Hackathons
- Create hackathons with auto 48h deadline or custom deadline
- Search, filter by category/status, sort by newest/popular/ending
- Join hackathons (+20 XP)
- Real-time countdown timer display

### Submissions
- Submit projects with title, description, GitHub & demo links (+30 XP)
- Ranked by community votes
- Edit/delete own submissions

### Voting & Comments
- One vote per user per submission
- Submission owners receive +10 XP per vote
- Comment on any submission

### Gamification (XP System)
| Action | XP |
|---|---|
| Join a hackathon | +20 |
| Submit a project | +30 |
| Receive a vote | +10 |
| Top 3 finish | +50 |
| Win a hackathon | +100 |

### Badges
🚀 First Steps · 📦 Builder · 🥉 Top 3 · 🏆 Champion · 👍 Supporter · 🔥 On Fire · ⭐ Rising Star · 💎 Diamond

### Ranks
- Rookie (0 XP) → Coder (50) → Hacker (200) → Expert (500) → Legend (1000)

### Admin
- Dashboard with stats and charts
- Manage users, hackathons, submissions, comments

## Tech Stack
- **Backend:** PHP 8 (OOP, MVC, no framework)
- **Database:** MySQL with PDO (prepared statements)
- **Frontend:** Custom CSS (dark theme), Chart.js
- **Security:** CSRF tokens, password hashing, XSS prevention

## Setup

1. **Clone** the repository
2. **Import** `database/proveit.sql` into MySQL
3. **Edit** `config/config.php` with your DB credentials
4. **Start** Apache + MySQL (XAMPP/WAMP)
5. **Navigate** to `http://localhost/proveit/`

### Default Admin
- Email: `admin@proveit.com`
- Password: `admin`

## Project Structure
```
/proveit
├── index.php                  # Front controller
├── config/config.php          # Configuration, CSRF, helpers
├── database/proveit.sql       # Database schema
├── app/
│   ├── controllers/           # MVC Controllers
│   ├── models/                # Database models
│   └── views/                 # PHP templates
│       ├── partials/nav.php   # Shared navigation
│       ├── user/              # Auth, profile, leaderboard
│       ├── hackathon/         # List, detail, create, edit
│       ├── submission/        # Edit
│       └── admin/             # Dashboard
└── public/
    ├── css/app.css            # Main stylesheet
    └── images/logo.png        # Logo
```

## Authors
Mohammed Rami Abbassi / Takwa Mdallel / Chahd Benslimen
