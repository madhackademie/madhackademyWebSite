# Auth — comptes admin / testeur / guides protégés

> Première mise en place : juin 2026  
> Prérequis hébergeur : **PHP 8+** et **MySQL** sur gameopenmoney.com  
> **OVH :** [`NOTE_OVH-PHP-MYSQL.md`](NOTE_OVH-PHP-MYSQL.md) — guide pas à pas complet  
> **Session de travail :** [`NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md)

---

## Rôles

| Rôle | Accès guides | Usage |
|------|--------------|-------|
| **admin** | Oui (tous) | Vous — peut créer des comptes via setup |
| **tester** | Oui (tous) | Testeurs beta |
| **student** | Si produit accordé | Futurs élèves payants |

---

## Installation FTP (une fois)

### 1. Base MySQL

Dans phpMyAdmin (panneau hébergeur) :

1. Créer une base (ex. `madhackademy`)
2. Importer `WebSite/sql/schema.sql`

### 2. Configuration PHP

Sur le FTP :

1. Copier `api/config.example.php` → `api/config.php`
2. Renseigner host, user, pass MySQL
3. Changer `setup_key` (chaîne secrète longue)

**Ne pas** committer `config.php` (déjà dans `.gitignore`).

### 3. Upload des dossiers

```
WebSite/api/          → /api/
WebSite/auth/         → /auth/
WebSite/sql/          → (optionnel, déjà importé)
WebSite/guides/cards/.htaccess  → bloque HTML direct des *Guide/
```

### 4. Créer vos comptes

Ouvrir dans le navigateur :

```
https://gameopenmoney.com/auth/setup.php?key=VOTRE_SETUP_KEY
```

1. Créer votre compte **admin** (votre email)
2. Créer un compte **tester** par testeur
3. **Supprimer** `auth/setup.php` du FTP après installation

### 5. Tester

1. `https://gameopenmoney.com/auth/login.php`
2. `https://gameopenmoney.com/gamedevready-bases-cpp.html` → **Ouvrir le guide →**
3. Sans login → redirection connexion
4. URL directe guide (doit être bloquée) :
   `https://gameopenmoney.com/guides/cards/01_PrintFGuide/printfC++FrogTheme.html` → **403**

---

## URLs

| Page | URL |
|------|-----|
| Connexion | `/auth/login.php` |
| Déconnexion | `/auth/logout.php` |
| Guide module 01 | `/auth/guide.php?m=01` |
| Guide module 02–07 | `/auth/guide.php?m=02` … `m=07` |

---

## Fichiers liés

| Fichier | Rôle |
|---------|------|
| `api/bootstrap.php` | Session, login, contrôle accès |
| `auth/guide.php` | Sert le HTML guide si autorisé |
| `guides/cards/.htaccess` | Bloque accès direct aux `.html` dans `*Guide/` |

---

## Prochaine étape (élèves payants)

Quand Stripe / System.io sera branché : webhook PHP → insert `user_products` pour rôle `student`.

Voir `NOTE_ARCHITECTURE_SOFT-SITE.md` §5 et §7.
