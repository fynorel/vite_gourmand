# 🍽️ Vite & Gourmand

Application web de commande de menus traiteur pour l'entreprise Vite & Gourmand (Bordeaux).  
Développée dans le cadre du titre professionnel **Développeur Web et Web Mobile**.

---

## 🛠️ Stack technique

| Technologie     | Version         |
|-----------------|-----------------|
| PHP             | 8.5.6           |
| Symfony         | 7.4.11          |
| MariaDB         | 11.8.6          |
| MongoDB         | 7.0.34          |
| Bootstrap       | 5.3.0           |

---

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé sur votre machine :

- **PHP** >= 8.2 avec les extensions : `pdo`, `pdo_mysql`, `mongodb`, `intl`, `mbstring`, `xml`
- **Composer** >= 2.x
- **MariaDB** ou MySQL >= 10.x
- **MongoDB** >= 7.x
- **Symfony CLI** (optionnel mais recommandé)
- **Git**

---

## 🚀 Installation en local

### 1. Cloner le dépôt

```bash
git clone https://github.com/VOTRE_USERNAME/vite_gourmand.git
cd vite_gourmand
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer les variables d'environnement

Copiez le fichier `.env` et adaptez-le :

```bash
cp .env .env.local
```

Éditez `.env.local` avec vos paramètres :

```env
# Base de données MariaDB
DATABASE_URL="mysql://VOTRE_USER:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/vite_gourmand?serverVersion=11.8.6-MariaDB&charset=utf8mb4"

# MongoDB
MONGODB_URL="mongodb://127.0.0.1:27017"
MONGODB_DB="vite_gourmand"

# Mailer (Mailtrap pour les tests)
MAILER_DSN=smtp://identifiant:motdepasse@sandbox.smtp.mailtrap.io:2525

# Environnement
APP_ENV=dev
APP_SECRET=VOTRE_SECRET_ALEATOIRE
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
```

### 5. Importer la structure SQL

```bash
mysql -u VOTRE_USER -p vite_gourmand < sql/structure.sql
```

> Le fichier `sql/structure.sql` contient la structure complète des tables ainsi que les données initiales (allergènes, entreprise, horaires).

### 6. Créer le compte administrateur

Le compte administrateur ne peut pas être créé depuis l'application.  
Exécutez la commande suivante pour le créer manuellement :

```bash
php bin/console app:create-admin
```

Ou insérez-le directement en base :

```sql
INSERT INTO utilisateur (nom, prenom, mail, gsm, adresse, mdp_hash, role, actif, compteur_authentification, date_creation)
VALUES ('ADMIN', 'Jose', 'jose@vite-gourmand.fr', '0600000000', '12 rue des Chartrons, 33000 Bordeaux', 'HASH_DU_MOT_DE_PASSE', 'ROLE_ADMIN', 1, 0, NOW());
```

> Le mot de passe doit être hashé avec Symfony. Utilisez : `php bin/console security:hash-password`

### 7. Charger les données de test (optionnel)

```bash
php bin/console doctrine:fixtures:load --append
```

> `--append` permet de ne pas écraser les données existantes (notamment le compte admin).

### 8. Démarrer le serveur de développement

```bash
# Avec Symfony CLI
symfony serve

# Ou avec PHP
php -S localhost:8000 -t public/
```

L'application est accessible sur : **http://localhost:8000**

---

## 👥 Comptes de test

| Rôle        | Email                        | Mot de passe    |
|-------------|------------------------------|-----------------|
| Admin       | jose@vite-gourmand.fr        | Admin@1234      |
| Employé     | bruno@exemple.fr             | Employe@1234    |
| Utilisateur | henri.dunand@exemple.fr      | Terminator@5678 |

> Les mots de passes exacts sont communiqués séparément dans le manuel d'utilisation.

---

## 🌿 Gestion des branches Git

```
main
└── developpement
    └── feature/nom-de-la-fonctionnalite
```

- **`main`** — branche de production (code stable déployé)
- **`developpement`** — branche d'intégration (recette)
- **`feature/*`** — branches de développement par fonctionnalité

### Workflow

```bash
# Créer une branche feature
git checkout developpement
git checkout -b feature/ma-fonctionnalite

# Merger vers développement après tests
git checkout developpement
git merge feature/ma-fonctionnalite
git push origin developpement

# Merger vers main pour la mise en production
git checkout main
git merge developpement
git push origin main
```

---

## 📁 Structure du projet

```
vite_gourmand/
├── config/             # Configuration Symfony
├── public/             # Point d'entrée (index.php)
├── src/
│   ├── Controller/     # Contrôleurs
│   ├── DataFixtures/   # Données de test
│   ├── Document/       # Documents MongoDB
│   ├── Entity/         # Entités Doctrine (MariaDB)
│   ├── Repository/     # Repositories
│   ├── Service/        # Services métier
│   └── Twig/           # Extensions Twig
├── sql/
│   └── structure.sql   # Structure BDD + données initiales
├── templates/          # Templates Twig
├── .env                # Variables d'environnement (exemple)
└── README.md
```

---

## 🔒 Sécurité

- Mots de passe hashés avec **bcrypt** via Symfony Security
- Protection **CSRF** sur tous les formulaires sensibles
- Contrôle d'accès par rôles : `ROLE_USER`, `ROLE_EMPLOYE`, `ROLE_ADMIN`
- Hiérarchie des rôles : `ROLE_ADMIN` > `ROLE_EMPLOYE` > `ROLE_USER`
- Validation des données côté serveur
- Mot de passe : 10 caractères minimum, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial

---

## ♿ Accessibilité (RGAA)

L'application respecte les principales exigences du RGAA :

- Lien d'évitement vers le contenu principal
- Attributs `aria-label`, `aria-live`, `aria-expanded` sur les éléments interactifs
- Rôles ARIA (`role="main"`, `role="navigation"`, `role="contentinfo"`)
- Focus visible pour la navigation clavier
- Balise `lang="fr"` sur la page
- Titres de page uniques (`<title>`)
- Textes alternatifs sur les images (`alt`)
- Contrastes suffisants (ratio > 4.5:1)

---

## 📦 Déploiement en production

Voir la documentation de déploiement dans `docs/deploiement.md`.

---

## 📄 Licence

Projet réalisé dans le cadre d'un ECF — Titre Professionnel Développeur Web et Web Mobile.  
© 2024 Vite & Gourmand — Tous droits réservés.
