# ProveIt – Plateforme Hackathon

**ProveIt** est une plateforme web de hackathons de 48h avec un système de gamification (XP, badges, classement).

## Rôles<<<<<<< HEAD
### Organisateur
=======
### 🎯 Organisateur
>>>>>>> master
- Créer / modifier / supprimer des hackathons
- Consulter TOUS les projets soumis avec détails complets
- Voter sur les projets
- Voir les statistiques (graphiques, classements)
- **NE PEUT PAS** participer aux hackathons

<<<<<<< HEAD
### Candidat
=======
###   Candidat
>>>>>>> master
- Consulter les hackathons disponibles
- Rejoindre un hackathon (+20 XP)
- Soumettre UN projet par hackathon (+30 XP)
- Voir uniquement SON propre projet en détail
- **NE VOIT PAS** les projets des autres candidats (compétition)
- Voter pour **1 seul** candidat par hackathon
- Commenter son propre projet

<<<<<<< HEAD
### Admin
=======
### ⚙️ Admin
>>>>>>> master
- Dashboard de gestion complet
- Gérer utilisateurs, hackathons, projets, commentaires

## Gamification (Candidats)
| Action | XP |
|---|---|
| Rejoindre un hackathon | +20 |
| Soumettre un projet | +30 |
| Recevoir un vote | +10 |
| Top 3 | +50 |
| Gagner | +100 |

**Rangs**: Rookie → Coder → Hacker → Expert → Legend

## Installation

1. Importer `database/proveit.sql` dans MySQL
2. Modifier `config/config.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS)
3. Démarrer Apache + MySQL (XAMPP/WAMP)
<<<<<<< HEAD
4. Aller sur `http://localhost/proveit/`
=======
4. Aller sur `http://localhost/proveit-v2/`
>>>>>>> master

### Comptes par défaut (mot de passe: admin)
- Admin: `admin@proveit.com`
- Organisateur: `orga@proveit.com`
- Candidat: `candidat@proveit.com`

## Auteurs
Mohammed Rami Abbassi / Takwa Mdallel / Chahd Benslimen
