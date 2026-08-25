# Guide — Prochaine session de travail

> **Référence principale** pour reprendre le projet madhackademyWebSite  
> Dernière mise à jour : 25 août 2026  
> Domaine marque : [madhackademy.eu](https://madhackademy.eu/)  
> Ancien domaine : `gameopenmoney.com` → **301** vers madhackademy (voir `.htaccess`)

---

## Reprise session suivante — **P1 unique** (26/08)

> **Test global élève** : installer FlashDev **depuis le site** (release **0.2.2**), soft vide → installer le deck Bootcamp C++ → parcours 7 cartes.  
> **Ne pas utiliser** la copie OneDrive (`…\OneDrive\RevisionSoft\…`) — ancienne version.

### Bilan 25/08

- Plan de contrôle soft/deck identifié (`FlashRevisionSoft/TODO.md` + `NOTE_REVISION-BOOTCAMP-FR.md`)
- Décision : test à vide **demain**, install **prod site**, pas OneDrive
- Cases test deck encore **vides** (à cocher pendant le run)

### Parcours demain

1. Télécharger l’installeur **prod** (pas le repo Cursor, pas OneDrive)
2. Installer dans un dossier **neuf**
3. Lancer : **aucune carte** (soft vide)
4. Explore deck → `DeckBootCampCpp/` (le zip/exe release **n’embarque pas** le deck)
5. Parcours 7 cartes : start → VS Code → done → webGuide
6. Cocher `NOTE_REVISION-BOOTCAMP-FR.md` § Soft + `TODO.md` soft (checklist test deck)

### URLs / livrables

| Quoi | Où |
|------|-----|
| Page download | [madhackademy.eu/telecharger.html](https://madhackademy.eu/telecharger.html) — si 404, [flashdev.html](https://madhackademy.eu/flashdev.html) ou lien direct |
| Setup | `https://madhackademy.eu/flashdev/FlashDev-Setup-0.2.2-win64.exe` |
| Manifest | `https://madhackademy.eu/flashdev/latest-version.json` (doit lire **0.2.2**) |
| Deck | dossier local `FlashRevisionSoft/DeckBootCampCpp/` (pas dans le zip update) |

### Checklist test (à cocher demain)

- [ ] Manifest prod = **0.2.2**
- [ ] Setup `.exe` téléchargé depuis le site, install dossier neuf
- [ ] FlashDev lance, **0 carte**
- [ ] Explore deck → `DeckBootCampCpp/` → preview + install
- [ ] 7 cartes dans `data.json` + images `images_current/` (ne **pas** committer `data.json`)
- [ ] Start / reset / VS Code / done sur les 7 cartes
- [ ] Pas de crash `newImage` ; ordre `nextRevision` OK
- [ ] **webGuide** ouvre `URLNet` (aujourd’hui : `/auth/guide.php?m=XX` — login possible)

### Ensuite (P2 — ne pas ouvrir avant fin du test)

- FTP nav-globale (`telecharger.html`, nav, opt-in) si pas encore en prod — checklist ci-dessous
- Ressenti nav FlashDev vs Téléchargement — à trancher après tests

---

## Archive P1 4/08 — FTP nav-globale (si pas encore uploadé)

> **FTP + parcours prod** : uploader les fichiers de la branche `feature/nav-globale`, naviguer, tester liens / login / téléchargement + email Systeme.io.  
> Ressenti nav : **FlashDev** vs **Téléchargement** — à trancher après tests (laisser pour l’instant).

### Checklist FTP (racine `www` = contenu de `WebSite/`)

**Pages HTML (nav + copy + WIP + page téléchargement dédiée)**
- [ ] `index.html`
- [ ] `index-en.html`
- [ ] `flashdev.html` (vitrine / aperçu)
- [ ] `telecharger.html` (**nouveau** — gate compte + installeur)
- [ ] `gamedevready.html`
- [ ] `gamedevready-bases-cpp.html`
- [ ] `roadMapCPPBootcamp.html`
- [ ] `centre-formation.html`
- [ ] `training-center.html`

- [ ] `nav-site.css` (**nouveau** — couleurs nav + session + hamburger)
- [ ] `nav-session.js` (**nouveau** — email + Déconnexion si connecté)
- [ ] `nav-mobile.js` (**nouveau** — hamburger mobile toutes pages)

**Auth / opt-in (redirects → `telecharger.html`)**
- [ ] `api/systeme-optin.php`
- [ ] `auth/set-password.php`
- [ ] `auth/login.php`
- [ ] `auth/logout.php`
- [ ] `optin-email-existe.html`

**Optionnel si pas déjà en prod**
- [ ] `api/bootstrap.php`
- [ ] `auth/me.php`
- [ ] `merci-flashdev.html`

> Ne pas écraser `api/config.php` (secrets FTP uniquement).

### Tests après upload

- [ ] Nav globale : Accueil · FlashDev · **Téléchargement** · Formation · GameDevReady · FR|EN
- [ ] `/telecharger.html` : gate → set-password → download
- [ ] `/flashdev.html` : vitrine + aperçu (sans gate) ; `?dl=1` redirige vers telecharger
- [ ] MiniPoulpe visible section « Qui suis-je ? » (centre-formation)
- [ ] Modules roadmap 2–5 : badge « En construction »
- [ ] Login / logout → retour téléchargement
- [ ] Opt-in email neuf → Systeme.io + set-password → `/telecharger.html`
- [ ] Email Bienvenue ; 2ᵉ fois → « déjà un compte »

### Ensuite (P2 / plus tard)

- Remplacer badge WIP SVG par image générée (ChatGPT) si souhaité
- Media FlashDev (`/flashdev/media/`) — vidéo + captures
- Merge `feature/nav-globale` → `main` après validation FTP

### Nav globale (4/08 — branche `feature/nav-globale`)

Barre unifiée :

`MadHackAdemy` · Accueil · FlashDev · **Téléchargement** · Formation · GameDevReady · FR|EN

- `flashdev.html` = présentation / aperçu media  
- `telecharger.html` = compte + installeur (page dédiée)

### Roadmap produit (ordre décidé 2/08)

| # | Chantier | Notes |
|---|----------|--------|
| **1** | Media vitrine FlashDev | Captures + vidéo démo sur page download |
| **2** | Shop / paiement | Dépend entité légale — voir `NOTE_CREATION-ENTREPRISE.md` |
| **3** | Parcours GameDevReady Bases C++ | Valider **FR → EN → DE → ES** (contenu + webGuides) |
| **4** | WebGuides « mode programmeur » | Utilisation soft : créer cartes **à la main** une par une, ou créer un deck (mains dans le cambouis) |
| **5** | Protection decks (plus tard) | Empêcher redistribution / decks hors licence — token (ex. empreinte machine / n° série HDD ou autre). **Coder après** validation parcours multilangue §3 |

> Soft gratuit + CRM compte = déjà en place. Helper « IA pour générer carte/deck » = tip pédagogique dans les guides §4, pas bloquant.

### Bilan tunnel compte + CRM (2/08) — done

- Compte site immédiat : opt-in → Systeme.io (contact + tag) → MySQL + `flashdev-soft` → set-password → download
- Feedback « déjà un compte » + déconnexion sur `flashdev.html`
- Email Bienvenue Systeme.io reçu en test (choix A)
- Docs : `NOTE_SYSTEMEIO-API-FLASHDEV.md` §6

### Modèle accès (rappel)

| Acquisition | Download FlashDev | webGuide |
|-------------|-------------------|----------|
| Opt-in soft (`flashdev-soft`) | Oui | **Non** |
| Formation payante | Oui | Oui |
| Anonyme | Gate | Non |

### Bilan leads / domaine (2/08) — done

- Domaine `madhackademy.eu` + email `contact@` + auth Systeme.io
- Soft liens + release **0.2.2** (zip / exe / setup zip)
- Opt-in site → API → tag `flashdev-download` → email
- Lien email : `flashdev.html?dl=1` (page puis bouton ; temporaire jusqu’au login-acquis)
- Page merci : sans bouton download (évite skip mail)
- **Redirect 301** : `WebSite/.htaccess` — validé prod

#### Tests redirect (prod OK)

```text
https://gameopenmoney.com/                    → https://madhackademy.eu/          ✅
https://www.gameopenmoney.com/flashdev.html → https://madhackademy.eu/flashdev.html ✅
https://gameopenmoney.com/flashdev.html?dl=1 → https://madhackademy.eu/flashdev.html?dl=1 ✅
https://madhackademy.eu/                      → 200 inchangé ✅
```

Futur site gameopenmoney : Multisite → **autre dossier** + retirer la règle Host du `.htaccess` MadHackAdemy.

---

## Décision domaine (1 août 2026)

| Point | Choix |
|--------|--------|
| Domaine marque | **madhackademy.eu** (acheté) |
| Email | `contact@madhackademy.eu` (MX Plan 5 OK) + Outlook |
| Site | Multisite OVH → même racine que gameopenmoney ; HTTPS OK |
| `gameopenmoney.com` | Autre usage plus tard (pas la marque MadHackAdemy) |
| Migration liens | **Ensemble** après auth Systeme.io validée |
| Suite | Automation Systeme.io + double opt-in, puis paiement |

### Checklist (ordre strict)

1. [x] OVH : boîte `contact@madhackademy.eu` — envoi + réception OK  
2. [x] Systeme.io : domaine + DNS CNAME/DMARC  
3. [x] Systeme.io : domaine **authentifié** + expéditeur `contact@` (validé 2/08)  
4. [ ] Test envoi Systeme.io → réception réelle  
5. [x] Bascule liens absolus soft/mock → `madhackademy.eu` (local 2/08 — à tester)  
6. [ ] Double opt-in + automation tag `flashdev-download` → email lien FlashDev  
7. [ ] Paiement (Stripe / Systeme.io) → accès `student` / `user_products`

---

## Inventaire liens absolus `gameopenmoney.com` → `madhackademy.eu`

> Pages HTML vitrine : **relatives** → déjà OK. PHP/MySQL : pas d’URL domaine en dur.  
> **Code soft + mock : basculé 2/08** (local). Backups `update_work/backup_*` laissés tels quels.

### Prod / code (fait localement 2/08)

| Fichier | Nb | État |
|---------|----|------|
| `FlashRevisionSoft/SquelletteGCS/data.json` | 7 `URLNet` | ✅ |
| `FlashRevisionSoft/DeckBootCampCpp/DeckInstaller.json` | 7 `URLNet` | ✅ |
| `FlashRevisionSoft/SquelletteGCS/lib/updateCheck.lua` | 1 manifest | ✅ |
| `FlashRevisionSoft/SquelletteGCS/state/currentProjectMenu.lua` | 1 préfixe download | ✅ |
| staging `update_work/staging/…` (mêmes 2 fichiers) | 2 | ✅ |
| `WebSite/systeme-io-capture-mock.html` | 1 | ✅ |

**Test à faire :** webGuide (carte → guide) + check update FlashDev via `madhackademy.eu/flashdev/…`.

### Docs seulement (P2 — notes / TODO)

`TODO.md`, `NOTE_PROCHAINE-SESSION.md`, `NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`, `NOTE_UPDATE-WORKFLOW.md`, `NOTE_SYSTEMEIO-API-FLASHDEV.md`, `NOTE_SETUP-UTILISATEUR.md`, `NOTE_OVH-PHP-MYSQL.md`, `NOTE_ARCHITECTURE_SOFT-SITE.md` — références URL à mettre à jour au fil de l’eau.

### Pas de re-upload massif

Multisite = même disque. Upload FTP **uniquement** des fichiers modifiés après édition.

---

## Bilan 30/07 → 1/08

| Fait / en cours | Détail |
|-----------------|--------|
| Feature update soft | ✅ Validée sur `main` (T1–T4) |
| Opt-in site / API | En cours — `NOTE_SYSTEMEIO-API-FLASHDEV.md` |
| Automation | Prévu : Tag `flashdev-download` → Envoyer un email |
| Double opt-in | **Décidé** |
| Domaine + HTTPS | ✅ Multisite + Let’s Encrypt OK |
| Email OVH | ✅ `contact@madhackademy.eu` + Outlook |
| Systeme.io DNS | ✅ CNAME×3 + DMARC — statut **En attente** (reprise demain) |

---

## Soft — feature update ✅ (juillet 2026)

| Point | État |
|--------|------|
| Feature mise à jour | **Validée** — code sur `main` / `origin/main` |
| Branche `feat/options-update` | Plus nécessaire (intégrée ; absente du remote) |
| Tests | **T1–T4 ✅** (prod 0.1.0 → 0.2.1) — voir `NOTE_UPDATE-WORKFLOW.md` § 6 |
| Restant | T5–T7 = **P2** (non bloquant) |

### Release FlashDev — rappel rapide

| Élément | Chemin / URL |
|---------|----------------|
| **Workflow complet** | `FlashRevisionSoft/scripts/NOTE_UPDATE-WORKFLOW.md` |
| **Build zip** | `.\scripts\build-release-zip.cmd -Version "X.Y.Z" -UpdateManifest` |
| **FTP** | `WebSite/flashdev/` → `https://madhackademy.eu/flashdev/` |
| **Release 0.2.2** | ✅ Zip + exe + setup zip ; FTP + test MAJ validés (2/08) |
| **Manifest** | `latest-version.json` (UTF-8 **sans BOM**) |
| **Package** | `FlashRevisionSoft-X.Y.Z-win64.zip` (code only) |

**≠ `sync-both`** : sync Git entre machines · **`flashdev/`** = distribution aux élèves.

---

## Archive — session 14–15 juillet 2026 (update)

### Bilan (historique)

| Fait | Détail |
|------|--------|
| Site | `flashdev.html` + `flashdev/latest-version.json` + `index.html` — **FTP OK** |
| Soft | Phase A + B update sur `main` (T1–T4 ✅) |
| Merge `feat/options-update` | **Fait** (travail consolidé sur `main` — vérifié 30/07/2026) |

### Checklist update (archive)

- [x] Bouton Options + menu déroulant mise à jour / état « à jour » grisé
- [x] Phase A — `updateCheck.lua` + HTTPS
- [x] Phase A — tests T1 / T2 / T3
- [x] Phase B — update 1 clic (T4 prod ✅)
- [x] Intégration sur `main`

### Tests check mise à jour (réf. workflow)

> Doc : `FlashRevisionSoft/scripts/NOTE_UPDATE-WORKFLOW.md` § 6 · feature sur **`main`**

- [x] **T1–T4** — Validés (prod 0.1.0 → 0.2.1)
- [ ] **P2** — T5–T7 + non-régression deck (webGuide, explore, Start, Done) — non bloquant

### Reporté

- [ ] Refaire les graphiques du bouton **webGuide** (`currentProjectMenu.lua`) — assets + protocole production

---

## 1. Où en est le projet ?

### Fait

| Zone | État |
|------|------|
| **Page Bases C++** | Miniatures → ancres sur la même page ; cartes Frogger en iframe ; boutons **Ouvrir le guide →** |
| **7 guides HTML** | `WebSite/Formations/BaseCpp/guides/*Guide/` (local + FTP) |
| **Structure FicheFormationHtlm** | Dossiers `*Guide/` pour modules 01–07 ; cartes = `WebSite/Formations/BaseCpp/cards/` |
| **Réorganisation Formations** | `Formations/BaseCpp/cards/` + `guides/` — juillet 2026 |
| **Auth MVP (code local)** | PHP login + rôles admin/tester/student + `auth/guide.php` + blocage `.htaccess` |
| **Auth OVH (production)** | PHP 8+, MySQL, FTP, comptes admin + testeurs, `setup.php` retiré — juillet 2026 |
| **Tests production** | Parcours cartes + guides 01–07, login, blocage URL directe (403), logout — juillet 2026 |
| **Beta testeurs** | Identifiants distribués ; `URLNet` FlashDev à jour — juillet 2026 |

### Prochaine étape

> **P1 prioritaire :** opt-in FlashDev via API Systeme.io — [`NOTE_SYSTEMEIO-API-FLASHDEV.md`](NOTE_SYSTEMEIO-API-FLASHDEV.md).

- [ ] **P1** — API Systeme.io + formulaire sur `flashdev.html` (étapes 1–7)
- [ ] **P1** — Valider le périmètre MVP avant release (`scripts/NOTE_MVP-FLASHDEV.md`)
- [ ] Contenu centre-formation (voir `TODO.md` § P1 contenu)
- [ ] Paiement / rôle `student` (webhook → `user_products`)
- [ ] **P1** — Dashboard élève (`dashboard/index.php`) : install FlashRevisionSoft, utilitaires, contenu acheté
- [ ] *(phase ultérieure)* Appairage FlashDev ↔ compte (token API) — **P2 avant release publique** : site = guides via login, soft = révisions locales

> Dépannage auth / OVH : **`NOTE_OVH-PHP-MYSQL.md`** · **`NOTE_AUTH-SETUP.md`**

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
| **admin** | Tous | Créé en prod (juillet 2026) |
| **tester** | Tous | Idem — identifiants distribués |
| **student** | Si `user_products` rempli | Futur : webhook paiement |

### Fichiers clés (repo)

| Chemin | Rôle |
|--------|------|
| `WebSite/gamedevready-bases-cpp.html` | Page deck + liens guides |
| `WebSite/Formations/BaseCpp/cards/` | Cartes Frogger (publiques) |
| `WebSite/Formations/BaseCpp/guides/` | Guides pédagogiques (`*Guide/`) |
| `WebSite/api/bootstrap.php` | Logique auth |
| `WebSite/auth/login.php`, `guide.php` | Pages auth |
| `WebSite/sql/schema.sql` | Tables MySQL |
| `FicheFormationHtlm/{module}/*Guide/` | Sources éditoriales (ne pas servir en prod directement) |

---

## 3. Mapping modules (référence rapide)

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

## 4. Documents liés

| Document | Contenu |
|----------|---------|
| **`TODO.md`** | Backlog global — priorités actuelles |
| **`scripts/NOTE_OVH-PHP-MYSQL.md`** | Guide OVH — PHP, MySQL, FTP, phpMyAdmin, SSL, dépannage |
| **`scripts/NOTE_AUTH-SETUP.md`** | Détail auth (rôles, URLs, fichiers) |
| **`scripts/NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`** | FTP GameDevReady, cartes, `URLNet` FlashDev |
| **`NOTE_ARCHITECTURE_SOFT-SITE.md`** | Sync FlashDev ↔ site, API future, paiement |
| **`scripts/NOTE_CREATION-ENTREPRISE.md`** | Création entreprise (Allemagne), compte N26, calendrier sept. 2026 |
| **`scripts/NOTE_REVISION-BOOTCAMP-FR.md`** | Checklist révision cartes + guides FR + test soft (P1) |
| **`scripts/CONVENTIONS-LAURENT.md`** | 5 conventions personnelles — rappel début session |
| **`scripts/NOTE_MVP-FLASHDEV.md`** | Périmètre MVP avant release — **P1 prochaine session** |
| **`FlashRevisionSoft/scripts/NOTE_UPDATE-WORKFLOW.md`** | Release FlashDev — zip, manifest, FTP, versions |

---

## 5. Prochaines évolutions

1. **Contenu centre-formation** — bio, boutique, offres (`TODO.md` § P1 contenu)
2. **Entreprise & compte pro** — statut Allemagne, N26 (objectif sept. 2026) — `NOTE_CREATION-ENTREPRISE.md`
3. **Paiement** — Stripe ou System.io → webhook PHP → `user_products` pour rôle `student`
4. **FlashDev** — token API après login (appairage soft ↔ compte)
5. **Dashboard élève** — roadmap + classement (`NOTE_ARCHITECTURE_SOFT-SITE.md`)

### Plan B — hébergeur sans PHP

- Héberger l'auth sur un sous-domaine avec PHP (ex. Railway, Render, alwaysdata)
- Ou plateforme LMS (Teachable, Podia) pour les guides en attendant
- Les guides resteraient publics sur FTP — **non recommandé** pour une formation payante

---

## 6. Commandes / rappels Git

```bash
# Voir les fichiers auth
git status WebSite/api WebSite/auth WebSite/sql

# config.php ne doit PAS apparaître (gitignore)
```

---

*Fin du guide — reprendre via `TODO.md` (contenu centre-formation).*
