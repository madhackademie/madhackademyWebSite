# Guide — Prochaine session de travail

> **Référence principale** pour reprendre le projet madhackademyWebSite  
> Dernière mise à jour : 2 juillet 2026  
> Domaine : [gameopenmoney.com](https://gameopenmoney.com/)

---

## 1. Où en est le projet ?

### Fait (session juin 2026)

| Zone | État |
|------|------|
| **Page Bases C++** | Miniatures → ancres sur la même page ; cartes Frogger en iframe ; boutons **Ouvrir le guide →** |
| **7 guides HTML** | `WebSite/Formations/BaseCpp/guides/*Guide/` (local + FTP) |
| **Structure FicheFormationHtlm** | Dossiers `*Guide/` pour modules 01–07 ; cartes = `WebSite/Formations/BaseCpp/cards/` |
| **Réorganisation Formations** | `Formations/BaseCpp/cards/` + `guides/` — juillet 2026 |
| **Auth MVP (code local)** | PHP login + rôles admin/tester/student + `auth/guide.php` + blocage `.htaccess` |

### Pas encore fait en production

- [x] Upload FTP des guides `*Guide/` (01–07) — **fait chez l'hébergeur**
- [ ] Mise en place **PHP + MySQL** sur OVH (voir `NOTE_OVH-PHP-MYSQL.md`)
- [ ] Upload `api/`, `auth/`, `.htaccess` — protection des guides
- [ ] Création comptes **admin** (vous) et **tester** (beta testeurs)
- [ ] Test complet du parcours protégé en HTTPS

> **Note :** tant que l'auth PHP n'est pas déployée, les guides restent accessibles en **URL directe** (ex. `…/Formations/BaseCpp/guides/01_PrintFGuide/printfC++FrogTheme.html`). Priorité : étapes B–F du §3 + guide OVH.

---

## 2. Architecture actuelle (à retenir)

```
Visiteur
  └── gamedevready-bases-cpp.html     (public — cartes visibles)
        ├── Miniatures deck           → scroll #carte-print … #carte-struct
        ├── Carte Frogger (iframe)    → Formations/BaseCpp/cards/01-printf.html … (public)
        └── Bouton « Ouvrir le guide »→ /auth/guide.php?m=01 … m=07 (protégé)

/auth/login.php                       → connexion
/auth/guide.php?m=XX                  → vérifie session → sert le HTML guide
/Formations/BaseCpp/guides/*.html     → bloqué en direct (.htaccess) — passer par auth
```

### Rôles

| Rôle | Guides | Création compte |
|------|--------|-----------------|
| **admin** | Tous | Via `setup.php` (puis à retirer) |
| **tester** | Tous | Idem |
| **student** | Si `user_products` rempli | Futur : webhook paiement |

### Fichiers clés (repo)

| Chemin | Rôle |
|--------|------|
| `WebSite/gamedevready-bases-cpp.html` | Page deck + liens guides |
| `WebSite/Formations/BaseCpp/cards/` | Cartes Frogger (publiques) |
| `WebSite/Formations/BaseCpp/guides/` | Guides pédagogiques (`*Guide/`) |
| `WebSite/api/bootstrap.php` | Logique auth |
| `WebSite/auth/login.php`, `guide.php`, `setup.php` | Pages auth |
| `WebSite/sql/schema.sql` | Tables MySQL |
| `FicheFormationHtlm/{module}/*Guide/` | Sources éditoriales (ne pas servir en prod directement) |

---

## 3. Checklist — prochaine session (ordre recommandé)

### Étape A — Vérifier l'hébergeur

- [ ] PHP **8+** activé sur gameopenmoney.com
- [ ] MySQL / MariaDB + accès **phpMyAdmin**
- [ ] HTTPS actif
- [ ] `mod_rewrite` activé (pour `.htaccess` dans `Formations/BaseCpp/guides/`)

> Si pas de PHP/MySQL : voir **`NOTE_OVH-PHP-MYSQL.md`** (guide OVH) ou §6 plan B ci-dessous.

### Étape B — Base de données

1. Créer une base (ex. `madhackademy`)
2. Importer `WebSite/sql/schema.sql` via phpMyAdmin

Tables créées : `users`, `user_products`

### Étape C — Config PHP (FTP)

1. Copier `WebSite/api/config.example.php` → `WebSite/api/config.php`
2. Renseigner : host, nom base, user, mot de passe MySQL
3. Changer `setup_key` (longue chaîne secrète — ex. générateur de mot de passe)
4. **Ne jamais** committer `config.php` (déjà dans `.gitignore`)

### Étape D — Upload FTP

Envoyer **le contenu de `WebSite/`** à la racine web (pas tout le repo) :

- [ ] `Formations/BaseCpp/guides/` (01–07) + `.htaccess`
- [ ] `Formations/BaseCpp/cards/`
- [ ] `api/` + `api/config.php`
- [ ] `auth/`
- [ ] `gamedevready-bases-cpp.html` (boutons `/auth/guide.php`) si pas à jour
- [ ] Retirer ancien `guides/cards/` du FTP si encore présent

### Étape E — Créer les comptes

1. Ouvrir : `https://gameopenmoney.com/auth/setup.php?key=VOTRE_SETUP_KEY`
2. Créer **votre** compte → rôle **admin**
3. Créer un compte par testeur → rôle **tester**
4. **Supprimer** `auth/setup.php` du FTP (sécurité)

### Étape F — Tests production

| Test | Résultat attendu |
|------|------------------|
| `/gamedevready-bases-cpp.html` | Page OK, miniatures + cartes |
| Clic **Ouvrir le guide** sans être connecté | Redirection `/auth/login.php` |
| Login admin → **Ouvrir le guide** module 01 | Guide Frogger complet + images |
| URL directe `…/Formations/BaseCpp/guides/01_PrintFGuide/printfC++FrogTheme.html` | **403 Forbidden** |
| Modules 02–07 guides | Idem via `?m=02` … `?m=07` |
| `/auth/logout.php` | Déconnexion OK |

### Étape G — Après validation

- [ ] Envoyer identifiants testeurs (email + mot de passe temporaire)
- [ ] Mettre à jour `FlashRevisionSoft/data.json` → `URLNet` vers guides ou cartes (voir note déploiement)
- [ ] Cocher les tâches correspondantes dans `TODO.md`

---

## 4. Mapping modules (référence rapide)

| # | Carte (public) | Guide (protégé) | Lien bouton |
|---|----------------|-----------------|-------------|
| 01 | `Formations/BaseCpp/cards/01-printf.html` | `guides/01_PrintFGuide/printfC++FrogTheme.html` | `/auth/guide.php?m=01` |
| 02 | `02-variables.html` | `guides/02_VariableGuide/VariableC++FroggerTheme.html` | `?m=02` |
| 03 | `03-conditions.html` | `guides/03_ConditionsGuide/Conditions.html` | `?m=03` |
| 04 | `04-boucles.html` | `guides/04_BouclesGuide/LoopModule.html` | `?m=04` |
| 05 | `05-std-fonctions.html` | `guides/05_StdFonctionsGuide/stdLib&Fonction.html` | `?m=05` |
| 06 | `06-conteneurs.html` | `guides/06_ConteneursGuide/Conteneurs.html` | `?m=06` |
| 07 | `07_StructMethode_Card/07-struct-methodes-card.html` | `guides/07_StructMethodesGuide/StructMethodes.html` | `?m=07` |

**Sources éditoriales** (modifier ici, recopier vers `WebSite/Formations/BaseCpp/`) :

`FicheFormationHtlm/{module}/*Guide/`

---

## 5. Documents liés

| Document | Contenu |
|----------|---------|
| **`scripts/NOTE_OVH-PHP-MYSQL.md`** | **Guide OVH complet** — PHP, MySQL, FTP, phpMyAdmin, SSL, dépannage |
| **`scripts/NOTE_AUTH-SETUP.md`** | Détail auth (rôles, URLs, fichiers) |
| **`scripts/NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`** | FTP GameDevReady, cartes, `URLNet` FlashDev |
| **`NOTE_ARCHITECTURE_SOFT-SITE.md`** | Sync FlashDev ↔ site, API future, paiement |
| **`TODO.md`** | Backlog global — section « Prochaine session » |

---

## 6. Prochaines évolutions (après auth OK)

1. **Paiement** — Stripe ou System.io → webhook PHP → `user_products` pour rôle `student`
2. **FlashDev** — token API après login (appairage soft ↔ compte)
3. **Dashboard élève** — roadmap + classement (`NOTE_ARCHITECTURE_SOFT-SITE.md`)
4. **Contenu centre-formation** — bio, boutique, offres (TODO P1 contenu)

### Plan B — hébergeur sans PHP

- Héberger l'auth sur un sous-domaine avec PHP (ex. Railway, Render, alwaysdata)
- Ou plateforme LMS (Teachable, Podia) pour les guides en attendant
- Les guides resteraient publics sur FTP — **non recommandé** pour une formation payante

---

## 7. Commandes / rappels Git

```bash
# Voir les fichiers auth ajoutés
git status WebSite/api WebSite/auth WebSite/sql

# config.php ne doit PAS apparaître (gitignore)
```

Commit suggéré (quand vous le demanderez) :

```
feat(auth): login admin/testeur et guides protégés via guide.php
```

---

## 8. Contacts / infos à préparer avant la session

- [ ] Identifiants panneau hébergeur (FTP + phpMyAdmin)
- [ ] Email admin MadHackAdemy
- [ ] Liste emails testeurs beta
- [ ] Confirmation PHP/MySQL chez le provider

---

*Fin du guide — reprendre à la section 3, étape A.*
