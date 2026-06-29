# Note — Déploiement FTP GameDevReady (gameopenmoney.com)

> Dernière mise à jour : 27 juin 2026  
> Domaine : [https://gameopenmoney.com/](https://gameopenmoney.com/)

---

## Objectif

Mettre en ligne les pages **GameDevReady** et les **cartes HTML Frogger** pour que :

1. Les visiteurs accèdent au deck depuis le site (`/gamedevready-bases-cpp.html`, etc.)
2. FlashDev puisse charger les URLs via le champ **`URLNet`** dans `FlashRevisionSoft/data.json`

---

## Principe : quoi envoyer sur le FTP

Le dépôt local sert le site depuis le dossier **`WebSite/`**.  
Sur le FTP du provider, le **contenu de `WebSite/`** doit se retrouver à la **racine web** du domaine (souvent `public_html/`, `www/` ou `htdocs/` — voir panneau hébergeur).

```
Repo local                          FTP (racine gameopenmoney.com)
─────────────────────────────────   ────────────────────────────────
WebSite/index.html          →       /index.html
WebSite/gamedevready.html   →       /gamedevready.html
WebSite/gamedevready-bases-cpp.html → /gamedevready-bases-cpp.html
WebSite/guides/cards/*.html →       /guides/cards/*.html
WebSite/Image/...           →       /Image/...
```

**Ne pas** envoyer tout le repo (`FicheFormationHtlm/`, `scripts/`, `TODO.md`, etc.) — uniquement **`WebSite/`** et son arborescence.

---

## Arborescence à indexer (upload FTP)

### Pages HTML (obligatoire pour les liens actuels)

| Fichier local | URL publique |
|---------------|--------------|
| `WebSite/index.html` | `https://gameopenmoney.com/` |
| `WebSite/centre-formation.html` | `https://gameopenmoney.com/centre-formation.html` |
| `WebSite/gamedevready.html` | `https://gameopenmoney.com/gamedevready.html` |
| `WebSite/gamedevready-bases-cpp.html` | `https://gameopenmoney.com/gamedevready-bases-cpp.html` |

### Cartes Frogger (deck bootstrap — Phase 1)

Dossier entier : **`WebSite/guides/cards/`**

| Fichier local | URL publique (pour `URLNet` FlashDev) |
|---------------|---------------------------------------|
| `guides/cards/01-printf.html` | `https://gameopenmoney.com/guides/cards/01-printf.html` |
| `guides/cards/02-variables.html` | `https://gameopenmoney.com/guides/cards/02-variables.html` |
| `guides/cards/03-conditions.html` | `https://gameopenmoney.com/guides/cards/03-conditions.html` |
| `guides/cards/04-boucles.html` | `https://gameopenmoney.com/guides/cards/04-boucles.html` |
| `guides/cards/05-std-fonctions.html` | `https://gameopenmoney.com/guides/cards/05-std-fonctions.html` |
| `guides/cards/06-conteneurs.html` | `https://gameopenmoney.com/guides/cards/06-conteneurs.html` |
| `guides/cards/07-struct-methodes.html` | `https://gameopenmoney.com/guides/cards/07-struct-methodes.html` |

Les liens du site utilisent des chemins **relatifs** (`guides/cards/...`) — ils fonctionnent tant que la structure FTP est identique.

### Assets (si utilisés plus tard)

| Dossier local | Remarque |
|---------------|----------|
| `WebSite/Image/` | Mascotte, vignettes GameDevReady (optionnel si page sans illustrations JPG) |

---

## Procédure FTP (checklist)

1. **Connexion** — Client FTP/SFTP (FileZilla, WinSCP…) avec identifiants du provider gameopenmoney.com
2. **Racine web** — Ouvrir le dossier document root (`public_html`, `www`, etc.)
3. **Upload** — Glisser le contenu de `madhackademyWebSite/WebSite/` (pas le dossier `WebSite` lui-même, sauf si le provider l’exige)
4. **Créer les dossiers** si absents : `guides/cards/`
5. **Vérifier les permissions** — fichiers `644`, dossiers `755` (classique Apache)
6. **Tester dans le navigateur** :
   - [https://gameopenmoney.com/gamedevready.html](https://gameopenmoney.com/gamedevready.html)
   - [https://gameopenmoney.com/gamedevready-bases-cpp.html](https://gameopenmoney.com/gamedevready-bases-cpp.html)
   - [https://gameopenmoney.com/guides/cards/01-printf.html](https://gameopenmoney.com/guides/cards/01-printf.html)
7. **Lien depuis l’accueil** — Module « Bases C++ » → `/gamedevready-bases-cpp.html`

---

## Mise à jour chez le provider (à faire)

- [ ] **Uploader** les nouveaux fichiers listés ci-dessus (premier déploiement GameDevReady)
- [ ] **Vérifier** que le domaine pointe bien sur le bon dossier (pas de sous-dossier oublié type `/WebSite/` dans l’URL)
- [ ] **HTTPS** — certificat actif sur toutes les URLs (FlashDev charge `URLNet` en HTTPS)
- [ ] **Cache** — vider le cache CDN / navigateur après upload si les anciennes pages s’affichent encore
- [ ] **Guides complets** (futur) — dossier `WebSite/guides/modules/` ou équivalent quand `printfC++FrogTheme.html`, etc. seront publiés

---

## Lier FlashRevisionSoft (`URLNet`)

Après déploiement FTP, renseigner dans `FlashRevisionSoft/SquelletteGCS/data.json` pour chaque carte GameDevReady :

```json
"URLNet": "https://gameopenmoney.com/guides/cards/01-printf.html"
```

| Carte `data.json` | `URLNet` cible |
|-------------------|----------------|
| `0x_Print` | `https://gameopenmoney.com/guides/cards/01-printf.html` |
| `0X_Variable` | `https://gameopenmoney.com/guides/cards/02-variables.html` |
| `0x_Conditions` | `https://gameopenmoney.com/guides/cards/03-conditions.html` |
| `0X_Boucles` | `https://gameopenmoney.com/guides/cards/04-boucles.html` |
| `0x_STD_Fonctions` | `https://gameopenmoney.com/guides/cards/05-std-fonctions.html` |
| `0X_Conteneurs` | `https://gameopenmoney.com/guides/cards/06-conteneurs.html` |
| `0x_Struct_Methodes` | `https://gameopenmoney.com/guides/cards/07-struct-methodes.html` |

Quand les **guides pédagogiques complets** seront en ligne, `URLNet` pourra pointer vers eux (ex. `https://gameopenmoney.com/guides/modules/01-printf.html`) au lieu de la carte seule.

---

## Workflow de mise à jour

1. Modifier les fichiers dans `WebSite/` (local)
2. Prévisualiser en ouvrant les HTML localement ou via un serveur statique
3. Upload FTP **uniquement les fichiers modifiés** (ou resync du dossier `WebSite/`)
4. Tester les URLs publiques
5. Si les cartes changent côté `FicheFormationHtlm/`, recopier vers `WebSite/guides/cards/` puis re-upload

**Cartes HTML (repo)** — source unique : `WebSite/guides/cards/` (modules 03–07 : plus de HTML carte dans `FicheFormationHtlm/`).

| Module | Fichier publié `WebSite/guides/cards/` |
|--------|----------------------------------------|
| 01 | `01-printf.html` |
| 02 | `02-variables.html` |
| 03 | `03-conditions.html` |
| 04 | `04-boucles.html` |
| 05 | `05-std-fonctions.html` |
| 06 | `06-conteneurs.html` |
| 07 | `07-struct-methodes.html` |

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
| `TODO.md` | Tâches P1 déploiement provider |
| `NOTE_ARCHITECTURE_SOFT-SITE.md` | Sync soft ↔ site (API future) |
| `scripts/NOTE_COMMIT-BOTH.md` | Commit site + soft |
