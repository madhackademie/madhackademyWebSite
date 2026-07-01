# TODO — madhackademyWebSite

> Dernière mise à jour : 1er juillet 2026  
> Projet : site vitrine FlashDev + MadHackAdemy

---

## Prochaine session de travail

> **Priorité absolue (P0)** — **réorganiser `WebSite/guides/`** (`guides/BaseCpp/{NN}/guide/` + `card/`) **avant** tout nouveau contenu, auth OVH ou déploiement FTP massif.  
> Sinon : refactorisation coûteuse sur une base `guides/cards/` déjà brouillonne.

> Guide session (auth, etc.) : [`scripts/NOTE_PROCHAINE-SESSION.md`](scripts/NOTE_PROCHAINE-SESSION.md) — **à reprendre après la migration P0**.

| Doc | Sujet |
|-----|--------|
| **`TODO.md` § P0** | **Réorganisation `guides/BaseCpp/`** — structure cible + checklist |
| [`scripts/NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`](scripts/NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md) | FTP *(à mettre à jour après P0)* |
| [`scripts/NOTE_OVH-PHP-MYSQL.md`](scripts/NOTE_OVH-PHP-MYSQL.md) | PHP, MySQL, auth OVH *(après P0)* |
| [`scripts/NOTE_AUTH-SETUP.md`](scripts/NOTE_AUTH-SETUP.md) | Rôles, URLs auth |

### Checklist rapide prochaine session

**P0 — Réorganisation guides (faire en premier)**

- [ ] **P0** — Créer `WebSite/guides/BaseCpp/` + 7 modules `{NN}_{nom}/guide/` + `card/` — détail § P0 ci-dessous
- [ ] **P0** — Migrer HTML + `Image/` (harmoniser casse `Image/`)
- [ ] **P0** — Mettre à jour `gamedevready-bases-cpp.html`, `api/bootstrap.php`, `auth/guide.php`, notes FTP, `URLNet`
- [ ] **P0** — Re-sync FTP sur la **nouvelle** arborescence ; supprimer ancien `guides/cards/` + doublons `Image/GameDevReady/bases-cpp/`

**P1 — Auth OVH** *(ne pas déployer de nouveaux chemins guides tant que P0 n’est pas fini)*

- [x] **P1** — Guides `*Guide/` (01–07) uploadés chez l'hébergeur OVH — juin 2026 *(chemins legacy — à remplacer après P0)*
- [ ] **P1** — Vérifier PHP 8+ et MySQL chez OVH → **`scripts/NOTE_OVH-PHP-MYSQL.md`**
- [ ] **P1** — Importer `WebSite/sql/schema.sql` dans phpMyAdmin
- [ ] **P1** — Créer `api/config.php` (copie de `config.example.php`) sur le FTP
- [ ] **P1** — Uploader `api/`, `auth/`, `.htaccess` guides, `gamedevready-bases-cpp.html` *(chemins post-P0)*
- [ ] **P1** — Créer compte **admin** + comptes **tester** via `/auth/setup.php?key=…`
- [ ] **P1** — Supprimer `auth/setup.php` du FTP après création des comptes
- [ ] **P1** — Tester login + guides via `/auth/guide.php?m=01` … + blocage URL directe (403)
- [ ] **P2** — Distribuer identifiants aux testeurs beta

---

| Page | URL |
|------|-----|
| Accueil FlashDev | [https://gameopenmoney.com/](https://gameopenmoney.com/) |
| Centre de formation | [https://gameopenmoney.com/centre-formation.html](https://gameopenmoney.com/centre-formation.html) |
| GameDevReady (hub) | [https://gameopenmoney.com/gamedevready.html](https://gameopenmoney.com/gamedevready.html) |
| Bases C++ (deck) | [https://gameopenmoney.com/gamedevready-bases-cpp.html](https://gameopenmoney.com/gamedevready-bases-cpp.html) |
| Guides (01–07) | `guides/cards/*Guide/` *(legacy — migration P0 → `guides/BaseCpp/`)* |

---

## P0 — Réorganisation `WebSite/guides/` (priorité absolue)

> **Bloquant** — à terminer **avant** auth OVH, nouveaux modules, Unreal/SDL, ou gros ajouts de contenu.  
> Objectif : ne plus construire sur `guides/cards/` (structure brouillonne, refactorisation inévitable sinon).

### Réorganisation deck Bases C++ (indexation & FTP)

> **Constat (juillet 2026)** — le dossier `guides/cards/` mélange cartes Frogger, guides pédagogiques et assets au même niveau. Difficile à parcourir (FileZilla), à déployer module par module, et à maintenir une fois le site monolithique grossi.

**État actuel (brouillon)**

```
guides/cards/
├── 01-printf.html … 06-conteneurs.html     ← cartes (racine)
├── 07_StructMethode_Card/                  ← carte 07 (exception)
├── 01_PrintFGuide/ … 07_StructMethodesGuide/  ← guides complets
│   └── Image/ ou image/                    ← casse incohérente
└── .htaccess
```

**Structure cible validée** *(intention produit — juillet 2026)*

Un **parcours** (`BaseCpp`, puis éventuellement `Unreal`, `SDL`, etc.) regroupe des **modules** numérotés.  
Chaque module = dossier `{NN}_{nom}` avec deux sous-dossiers **`guide/`** et **`card/`**, chacun avec **son propre** `Image/`.

> **BaseCpp** = Phase 1 actuelle (deck Raylib / C++ console). La même arborescence pourra accueillir d’autres stacks sans tout mélanger : `guides/Unreal/`, `guides/SDL/`, etc.

```
WebSite/guides/
├── BaseCpp/                        ← parcours C++ fondamentaux (actuel)
│   ├── 01_printf/
│   │   ├── guide/ … + Image/
│   │   └── card/  … + Image/
│   …
│   └── 07_struct_methodes/
├── Unreal/                         ← futur
└── SDL/                            ← futur
```

Exemple détaillé **BaseCpp** : `01_printf/guide/` + `01_printf/card/` (chacun avec `Image/`).

**Convention** : parcours `guides/{Stack}/` · module `{NN}_{snake_case}` · HTML carte `{NN}-{slug}-card.html`

**Correspondance migration (01 → 07)**

| Module | Dossier cible | Source guide | Source carte |
|--------|---------------|--------------|--------------|
| 01 | `01_printf/` | `01_PrintFGuide/` | `01-printf.html` |
| 02 | `02_variables/` | `02_VariableGuide/` | `02-variables.html` |
| 03 | `03_conditions/` | `03_ConditionsGuide/` | `03-conditions.html` |
| 04 | `04_boucles/` | `04_BouclesGuide/` | `04-boucles.html` |
| 05 | `05_std_fonctions/` | `05_StdFonctionsGuide/` | `05-std-fonctions.html` |
| 06 | `06_conteneurs/` | `06_ConteneursGuide/` | `06-conteneurs.html` |
| 07 | `07_struct_methodes/` | `07_StructMethodesGuide/` | `07_StructMethode_Card/` |

**Fichiers à mettre à jour après migration**

- `gamedevready-bases-cpp.html`, `api/bootstrap.php`, `auth/guide.php`
- `NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`, `FlashRevisionSoft/data.json` (`URLNet`), `.htaccess`

**Checklist réorganisation**

- [ ] **P0** — Créer `WebSite/guides/BaseCpp/` et les 7 dossiers `{NN}_{nom}/guide/` + `card/`
- [ ] **P0** — Déplacer HTML + `Image/` de chaque module (harmoniser la casse `Image/`)
- [ ] **P0** — Corriger tous les `src="Image/…"` et `url('Image/…')`
- [ ] **P0** — Mettre à jour iframes, `bootstrap.php`, notes FTP, `URLNet`
- [ ] **P0** — Redirections 301 ou compat temporaire (`guides/cards/…` → `guides/BaseCpp/…`)
- [ ] **P0** — Supprimer l’ancien `guides/cards/` et `WebSite/Image/GameDevReady/bases-cpp/` (doublons)
- [ ] **P0** — Documenter l’arborescence finale dans `NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md` + re-upload FTP
- [ ] **P3** — Extension `guides/Unreal/`, `guides/SDL/` sur le même modèle *(après P0 stable)*

---

## Tâches prioritaires

> **Après P0** — auth OVH, déploiement FTP guides, nouveaux contenus.

Ces tâches débloquent la mise en ligne ou corrigent des problèmes visibles pour les visiteurs.

### Contenu (bloquant publication centre-formation)

- [ ] **P1** — Rédiger l'accroche hero de `centre-formation.html` (1–2 phrases, cible + promesse)
- [ ] **P1** — Compléter la section « Qui suis-je ? » (bio, parcours, placeholders restants)
- [ ] **P1** — Remplir la méthode SITE + SOFT (sous-titre + 3 lignes par pilier)
- [ ] **P1** — Rédiger la roadmap centre-formation (4 étapes : titres, descriptions, durées)
- [ ] **P1** — Définir les 3 offres boutique (noms, contenu, prix, CTA)
- [ ] **P1** — Remplacer `[MadHackAdemy / LOGO]` et le footer `[TON NOM / CENTRE DE FORMATION]`

### Liens & mise en ligne

- [ ] **P1** — Remplacer tous les liens `#` sur `index.html` (GitHub, Twitch, YouTube, achat premium)
- [x] **P1** — Configurer l'hébergement statique → [gameopenmoney.com](https://gameopenmoney.com/)
- [x] **P2** — Vérifier que la navigation inter-pages fonctionne en production (`/` ↔ `/centre-formation.html`)

### Déploiement GameDevReady (provider / FTP)

> Procédure détaillée : **`scripts/NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`**  
> Session complète : **`scripts/NOTE_PROCHAINE-SESSION.md`** §3

- [x] **P1** — Guides `*Guide/` (01–07) en production sur gameopenmoney.com — juin 2026
- [ ] **P1** — Uploader / mettre à jour `gamedevready-bases-cpp.html` — **après P0** (chemins finaux)
- [ ] **P1** — Vérifier en production les 7 cartes + accès guides via `/auth/guide.php?m=01` … `m=07` — **après P0 + auth**
- [ ] **P1** — Tester le parcours : Bases C++ → miniature → carte → **Ouvrir le guide** (login requis)
- [ ] **P2** — Renseigner `URLNet` dans `FlashRevisionSoft/data.json` avec les URLs HTTPS

### Auth — guides protégés (admin / testeurs)

> Détail : **`scripts/NOTE_AUTH-SETUP.md`**

- [x] Code auth PHP local (`api/`, `auth/`, `sql/schema.sql`) — juin 2026
- [x] Boutons guide → `/auth/guide.php?m=XX` sur `gamedevready-bases-cpp.html`
- [x] 7 guides publiés dans `WebSite/guides/cards/*Guide/`
- [ ] **P1** — Déployer auth en production — **après P0** (voir checklist prochaine session)
- [ ] **P1** — Comptes admin + testeurs créés ; `setup.php` retiré du FTP
- [ ] **P2** — Webhook paiement → accès `student` (`user_products`)

### Corrections techniques urgentes

- [x] **P1** — Fermer la balise `<div class="pt-24">` manquante sur `index.html`
- [ ] **P2** — Normaliser le chemin image MiniPoulpe : `/Image/MiniPoulpeDicord.png` (au lieu de `\`)
- [ ] **P2** — Harmoniser le discours Lua vs C++ sur `index.html` (roadmap = C++/Raylib)

---

## Backlog

Tâches utiles mais non bloquantes — à traiter après les priorités.

### Guides de formation HTML (FlashDev / deck GameDevReady)

> Thème **Frogger** (charte du deck). Guides pédagogiques dans **`FicheFormationHtlm/{module}/*Guide/`** — cartes HTML Frogger publiées dans **`WebSite/guides/cards/`** uniquement.

| Dossier module | Guide HTML | Carte FlashSoft |
|----------------|------------|-----------------|
| `01_PrintC++` | `01_PrintFGuide/printfC++FrogTheme.html` | `0x_Print` |
| `02_Variable` | `02_VariableGuide/VariableC++FroggerTheme.html` | `0X_Variable` |
| `03_Conditions` | `03_ConditionsGuide/Conditions.html` | `0x_Conditions` |
| `04_Les boucles` | `04_BouclesGuide/LoopModule.html` | `0X_Boucles` |
| `05_LibrairieStandard&FonctionsC++` | `05_StdFonctionsGuide/stdLib&Fonction.html` | `0x_STD_Fonctions` |
| `06_Conteneurs` | `06_ConteneursGuide/Conteneurs.html` | `0X_Conteneurs` |
| `07_Struct_Methodes` | `07_StructMethodesGuide/StructMethodes.html` | `0x_Struct_Methodes` |

- [x] Importer les guides HTML sources dans ce repo (`FicheFormationHtlm/`)
- [x] Carte `07_Struct_Methodes` validée (guide Frogger `Frogger_theme_StrucAndMehtodeCard.html`) — juin 2026
- [x] Cartes Frogger HTML intégrées localement (`WebSite/guides/cards/`, page `gamedevready-bases-cpp.html`) — juin 2026
- [x] Guides 01–07 copiés dans `WebSite/guides/cards/*Guide/` — juin 2026
- [x] Structure `FicheFormationHtlm/*Guide/` (sources, sans HTML carte) — juin 2026
- [x] Page Bases C++ : miniatures → ancres, boutons guide protégés — juin 2026
- [x] Guides 01–07 en production FTP (`WebSite/guides/cards/*Guide/`) — juin 2026
- [ ] **P1** — Exposer **auth PHP** en production (voir `NOTE_OVH-PHP-MYSQL.md`) — **après P0**
- [ ] **P2** — Renseigner `URLNet` dans `FlashRevisionSoft/data.json` — **après P0**

### Contenu & éditorial
- [ ] Rédiger les textes légaux (mentions légales, CGV boutique)
- [ ] Préparer des témoignages / preuves sociales pour la page centre-formation
- [ ] Aligner la roadmap centre-formation avec celle de FlashDev (`index.html`) ou expliquer la différence

### Technique & UX

- [ ] Implémenter un countdown JS dynamique pour le stream du samedi (`index.html`)
- [ ] Ajouter un favicon et des meta SEO (description, Open Graph, Twitter Card)
- [ ] Extraire les styles communs (charte Nintendo) dans un fichier CSS partagé
- [ ] Remplacer Tailwind CDN par une build locale (perf + offline)
- [ ] Ajouter un menu mobile responsive (hamburger) sur les deux pages
- [ ] Corriger le titre `<title>` : `[MadHackAdemy]` → nom définitif

### Projet & maintenance

- [ ] Rédiger un `README.md` (description, preview locale, déploiement)
- [ ] Structurer un dossier `assets/` ou `css/` si le site grossit
- [ ] Configurer analytics (Plausible, GA4…) si souhaité
- [ ] Mettre en place un workflow de preview (PR previews Netlify/Vercel)
- [ ] Ajouter des tests de régression visuelle ou lint HTML (optionnel)

### Deck GameDevReady (coordination avec FlashRevisionSoft)

- [ ] **P2** — Mettre à jour la roadmap site : Premier Challenge → David & Goliath (combat tour par tour) une fois le projet créé côté soft
- [ ] **polish** — *(repo FlashRevisionSoft)* Branche `polish/cards-webm` : rendu hybride image/vidéo via `mediaType` optionnel (`"image"` par défaut, WebM pour cartes animées officielles) — détail dans `FlashRevisionSoft/TODO.md` — pas urgent

### Évolutions produit

#### Gamification — duels pixel art (FlashDev)

> Backlog fonctionnel pour le soft FlashRevisionSoft. Chaque révision de carte devient un combat ; la progression RPG motive la répétition espacée.

**Référence visuelle — David vs Goliath**

Maquette d’écran cible (beat’em up arcade type *Golden Axe* / *Cadillacs and Dinosaurs*) :

![Référence duel David vs Goliath](docs/gamification/references/david-vs-goliath-duel-reference.png)


| Élément à l’écran | Rôle dans FlashDev |
|-------------------|-------------------|
| **David** (1P, petit avatar) | L’élève — avatar joueur personnalisable |
| **Goliath** (boss, barre rouge) | Ennemi de la carte — difficulté élevée, plusieurs « attaques » (révisions) pour le vaincre |
| **Barre jaune 1P** | HP de l’élève (streak / survie entre sessions) |
| **Score `0024500`** | XP cumulée |
| **Barre rouge boss** | HP restant du boss — diminue à chaque révision réussie |
| **TIME** | Optionnel — pression douce ou limite par session |

*David contre Goliath* = métaphore produit : une carte difficile n’est pas un mur, c’est un duel gagnable coup par coup (révision par révision).

**Concept général**

- [ ] Avatar joueur personnalisable (sprite pixel art) affiché pendant les sessions de révision
- [ ] À chaque révision de carte : déclencher un **duel pixel art** contre l’ennemi associé à la carte
- [ ] Chaque carte porte un **type d’ennemi** (sprite dédié) et un **niveau de difficulté**
- [ ] Hiérarchie d’ennemis : **mob** (carte standard) → **mini-boss** (cartes clés / modules) → **boss** (fin de chapitre / deck)
- [ ] Victoire au duel → récompenses : **XP** (progression globale) + **HP** (ressource de survie / streak)
- [ ] Boss : **plusieurs attaques** (plusieurs révisions réussies de cartes liées) nécessaires pour le vaincre — barre de vie multi-étapes

**À spécifier / découper**

- [ ] Modèle de données carte ↔ ennemi (type, difficulté, HP ennemi, XP/HP gagnés)
- [ ] Règles de défaite (mauvaise réponse = dégâts subis par l’avatar ? perte de HP ?)
- [ ] Écran de duel (animations attaque joueur / ennemi, feedback victoire-défaite)
- [ ] Banque de sprites pixel art (avatar, mobs, mini-boss, boss par thème deck)
- [ ] Persistance locale : XP, HP courants, boss en cours (HP restant entre sessions)
- [ ] Sync optionnelle vers le site (voir `NOTE_ARCHITECTURE_SOFT-SITE.md`) pour afficher progression RPG en ligne

**Références produit**

- Révision = attaque (fronde / coup) ; boss = objectif long terme nécessitant N révisions réussies
- Métaphore **David vs Goliath** : l’élève (petit mais équipé) affronte des ennemis bien plus imposants
- Direction artistique : pixel art arcade 16-bit, HUD avec barres HP/XP — voir image ci-dessus
- Cohérent avec l’identité GameDev / pixel art du projet MadHackAdemy

---

- [ ] Intégrer un système de paiement pour les decks premium (Stripe, Gumroad…)
- [ ] Page dédiée par offre boutique avec landing optimisée conversion
- [ ] Formulaire de contact ou inscription newsletter
- [ ] Version anglaise du site (i18n)

---

## Légende priorités

| Tag | Signification |
|-----|---------------|
| **P0** | **Priorité absolue** — réorganisation `guides/BaseCpp/` ; bloque auth OVH et nouveaux contenus |
| **P1** | Critique — à faire juste après P0 |
| **P2** | Important — rapidement après P1 |
| **polish** | Amélioration visuelle / UX — non bloquant |
| *(backlog)* | Amélioration — quand le site est en ligne et le contenu rempli |

---

## État rapide du projet

| Page | Avancement estimé |
|------|---------------------|
| `index.html` (FlashDev) | ~80 % — contenu OK, liens et détails à finaliser |
| `centre-formation.html` | ~30 % — structure solide, contenu à rédiger |
| `gamedevready-bases-cpp.html` | ~90 % — deck + guides en prod ; auth PHP à déployer (OVH) |
