# Note — Déploiement FTP GameDevReady (gameopenmoney.com)

> Dernière mise à jour : 2 juillet 2026  
> Domaine : [https://gameopenmoney.com/](https://gameopenmoney.com/)

---

## Objectif

Mettre en ligne les pages **GameDevReady** et les **cartes HTML Frogger** pour que :

1. Les visiteurs accèdent au deck depuis le site (`/gamedevready-bases-cpp.html`, etc.)
2. FlashDev puisse charger les URLs via le champ **`URLNet`** dans `FlashRevisionSoft/SquelletteGCS/data.json`

---

## Principe : quoi envoyer sur le FTP

Le dépôt local sert le site depuis le dossier **`WebSite/`**.  
Sur le FTP du provider, le **contenu de `WebSite/`** doit se retrouver à la **racine web** du domaine (souvent `public_html/`, `www/` ou `htdocs/` — voir panneau hébergeur).

```
Repo local                                    FTP (racine gameopenmoney.com)
───────────────────────────────────────────   ────────────────────────────────
WebSite/index.html                    →       /index.html
WebSite/gamedevready.html             →       /gamedevready.html
WebSite/gamedevready-bases-cpp.html   →       /gamedevready-bases-cpp.html
WebSite/Formations/BaseCpp/cards/     →       /Formations/BaseCpp/cards/
WebSite/Formations/BaseCpp/guides/    →       /Formations/BaseCpp/guides/
WebSite/Image/...                     →       /Image/...
```

**Ne pas** envoyer tout le repo (`FicheFormationHtlm/`, `scripts/`, `TODO.md`, etc.) — uniquement **`WebSite/`** et son arborescence.

---

## Arborescence formations (juillet 2026)

```
WebSite/Formations/
└── BaseCpp/
    ├── cards/          ← cartes Frogger (publiques, iframes)
    └── guides/         ← guides pédagogiques (protégés via /auth/guide.php)
        └── .htaccess
```

---

## Arborescence à indexer (upload FTP)

### Pages HTML (obligatoire)

| Fichier local | URL publique |
|---------------|--------------|
| `WebSite/index.html` | `https://gameopenmoney.com/` |
| `WebSite/centre-formation.html` | `https://gameopenmoney.com/centre-formation.html` |
| `WebSite/gamedevready.html` | `https://gameopenmoney.com/gamedevready.html` |
| `WebSite/gamedevready-bases-cpp.html` | `https://gameopenmoney.com/gamedevready-bases-cpp.html` |

### Cartes Frogger (deck bootstrap — Phase 1)

Dossier entier : **`WebSite/Formations/BaseCpp/cards/`**

| Fichier local | URL publique (pour `URLNet` FlashDev) |
|---------------|---------------------------------------|
| `Formations/BaseCpp/cards/01-printf.html` | `https://gameopenmoney.com/Formations/BaseCpp/cards/01-printf.html` |
| `Formations/BaseCpp/cards/02-variables.html` | `https://gameopenmoney.com/Formations/BaseCpp/cards/02-variables.html` |
| `Formations/BaseCpp/cards/03-conditions.html` | `https://gameopenmoney.com/Formations/BaseCpp/cards/03-conditions.html` |
| `Formations/BaseCpp/cards/04-boucles.html` | `https://gameopenmoney.com/Formations/BaseCpp/cards/04-boucles.html` |
| `Formations/BaseCpp/cards/05-std-fonctions.html` | `https://gameopenmoney.com/Formations/BaseCpp/cards/05-std-fonctions.html` |
| `Formations/BaseCpp/cards/06-conteneurs.html` | `https://gameopenmoney.com/Formations/BaseCpp/cards/06-conteneurs.html` |
| `Formations/BaseCpp/cards/07_StructMethode_Card/07-struct-methodes-card.html` | `https://gameopenmoney.com/Formations/BaseCpp/cards/07_StructMethode_Card/07-struct-methodes-card.html` |

Les liens du site utilisent des chemins **relatifs** (`Formations/BaseCpp/cards/...`) — ils fonctionnent tant que la structure FTP est identique.

### Guides pédagogiques (protégés)

Dossier entier : **`WebSite/Formations/BaseCpp/guides/`** (inclure **`.htaccess`**)

Accès via `/auth/guide.php?m=01` … `m=07` — pas d’URL directe publique.

### Assets (optionnel)

| Dossier local | Remarque |
|---------------|----------|
| `WebSite/Image/` | Mascotte, vignettes GameDevReady |

---

## Procédure FTP (checklist)

1. **Connexion** — Client FTP/SFTP (FileZilla, WinSCP…) avec identifiants du provider gameopenmoney.com
2. **Racine web** — Ouvrir le dossier document root (`public_html`, `www`, etc.)
3. **Upload** — Glisser le contenu de `madhackademyWebSite/WebSite/` (pas le dossier `WebSite` lui-même, sauf si le provider l’exige)
4. **Créer les dossiers** si absents : `Formations/BaseCpp/cards/`, `Formations/BaseCpp/guides/`
5. **Retirer** les anciens chemins obsolètes : `guides/cards/` (legacy)
6. **Vérifier les permissions** — fichiers `644`, dossiers `755` (classique Apache)
7. **Tester dans le navigateur** :
   - [https://gameopenmoney.com/gamedevready-bases-cpp.html](https://gameopenmoney.com/gamedevready-bases-cpp.html)
   - [https://gameopenmoney.com/Formations/BaseCpp/cards/01-printf.html](https://gameopenmoney.com/Formations/BaseCpp/cards/01-printf.html)
8. **Lien depuis l’accueil** — Module « Bases C++ » → `/gamedevready-bases-cpp.html`

---

## Mise à jour chez le provider

- [ ] **Uploader** `Formations/BaseCpp/` + pages GameDevReady
- [ ] **Retirer** l’ancien dossier `guides/cards/` du FTP si encore présent
- [ ] **Vérifier** que le domaine pointe bien sur le bon dossier (pas de sous-dossier oublié type `/WebSite/` dans l’URL)
- [ ] **HTTPS** — certificat actif sur toutes les URLs (FlashDev charge `URLNet` en HTTPS)
- [ ] **Cache** — vider le cache CDN / navigateur après upload si les anciennes pages s’affichent encore
- [ ] **Auth PHP** — voir `NOTE_OVH-PHP-MYSQL.md` et `NOTE_AUTH-SETUP.md`

---

## Lier FlashRevisionSoft (`URLNet`)

Dans `FlashRevisionSoft/SquelletteGCS/data.json` pour chaque carte GameDevReady :

```json
"URLNet": "https://gameopenmoney.com/Formations/BaseCpp/cards/01-printf.html"
```

| Carte `data.json` | `URLNet` cible |
|-------------------|----------------|
| `0x_Print` | `https://gameopenmoney.com/Formations/BaseCpp/cards/01-printf.html` |
| `0X_Variable` | `https://gameopenmoney.com/Formations/BaseCpp/cards/02-variables.html` |
| `0x_Conditions` | `https://gameopenmoney.com/Formations/BaseCpp/cards/03-conditions.html` |
| `0X_Boucles` | `https://gameopenmoney.com/Formations/BaseCpp/cards/04-boucles.html` |
| `0x_STD_Fonctions` | `https://gameopenmoney.com/Formations/BaseCpp/cards/05-std-fonctions.html` |
| `0X_Conteneurs` | `https://gameopenmoney.com/Formations/BaseCpp/cards/06-conteneurs.html` |
| `0x_Struct_Methodes` | `https://gameopenmoney.com/Formations/BaseCpp/cards/07_StructMethode_Card/07-struct-methodes-card.html` |

---

## Workflow de mise à jour

1. Modifier les sources dans `FicheFormationHtlm/{module}/*Guide/` ou les cartes dans `WebSite/Formations/BaseCpp/`
2. Prévisualiser en ouvrant les HTML localement ou via un serveur statique
3. Upload FTP **uniquement les fichiers modifiés** (ou resync du dossier `WebSite/`)
4. Tester les URLs publiques
5. Si les guides changent côté `FicheFormationHtlm/`, recopier vers `WebSite/Formations/BaseCpp/guides/` puis re-upload

**Cartes HTML (repo)** — `WebSite/Formations/BaseCpp/cards/` :

| Module | Fichier publié |
|--------|----------------|
| 01 | `01-printf.html` |
| 02 | `02-variables.html` |
| 03 | `03-conditions.html` |
| 04 | `04-boucles.html` |
| 05 | `05-std-fonctions.html` |
| 06 | `06-conteneurs.html` |
| 07 | `07_StructMethode_Card/07-struct-methodes-card.html` |

**Guides pédagogiques (repo)** — sources dans `FicheFormationHtlm/{module}/*Guide/` :

| Module | Guide source |
|--------|--------------|
| 01 | `01_PrintC++/01_PrintFGuide/printfC++FrogTheme.html` |
| 02 | `02_Variable/02_VariableGuide/VariableC++FroggerTheme.html` |
| 03 | `03_Conditions/03_ConditionsGuide/Conditions.html` |
| 04 | `04_Les boucles/04_BouclesGuide/LoopModule.html` |
| 05 | `05_LibrairieStandard&FonctionsC++/05_StdFonctionsGuide/stdLib&Fonction.html` |
| 06 | `06_Conteneurs/06_ConteneursGuide/Conteneurs.html` |
| 07 | `07_Struct_Methodes/07_StructMethodesGuide/StructMethodes.html` |

---

## Fichiers liés

| Fichier | Contenu |
|---------|---------|
| `TODO.md` | Tâches prioritaires |
| `NOTE_ARCHITECTURE_SOFT-SITE.md` | Sync soft ↔ site (API future) |
| `scripts/NOTE_COMMIT-BOTH.md` | Commit site + soft |
| `scripts/NOTE_AUTH-SETUP.md` | Auth guides protégés |
