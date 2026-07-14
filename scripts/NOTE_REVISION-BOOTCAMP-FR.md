# Checklist — Révision bootcamp C++ (FR)

> Dernière mise à jour : 8 juillet 2026  
> **P1** — à cocher au fur et à mesure des corrections (version française uniquement pour l'instant).  
> Parcours : **site** (carte HTML + guide protégé) + **soft** (carte Love2D / exercice).

---

## Légende

| Colonne | Signification |
|---------|---------------|
| **Carte site** | `WebSite/Formations/BaseCpp/cards/*.html` — contenu, visuel Frogger, liens |
| **Guide site** | `WebSite/Formations/BaseCpp/guides/*Guide/*.html` — pédagogie, images, cohérence avec la carte |
| **Carte soft** | Exercice dans FlashDev (`elements_revisions/…/0x_0N_*`) — start, done, reset, image |
| **Deck install** | Entrée `DeckBootCampCpp/DeckInstaller.json` + test via explore deck |

**Critère « corrigé »** : relu, testé en local ou prod, contenu OK — coche quand c'est bon pour toi.

---

## 01 — Printf

| Élément | Fichier / URL | Corrigé |
|---------|---------------|---------|
| Carte site (FR) | `Formations/BaseCpp/cards/01-printf.html` | [ ] |
| Guide site (FR) | `guides/01_PrintFGuide/printfC++FrogTheme.html` — `/auth/guide.php?m=01` | [ ] |
| Carte soft | `0x_01_Print` — start → VS Code → done → SM2 | [ ] |
| `CardProperties` | `0x_01_Print/CardProperties` | [ ] |
| Entrée deck | `DeckBootCampCpp/DeckInstaller.json` id 1 | [ ] |

---

## 02 — Variables

| Élément | Fichier / URL | Corrigé |
|---------|---------------|---------|
| Carte site (FR) | `Formations/BaseCpp/cards/02-variables.html` | [ ] |
| Guide site (FR) | `guides/02_VariableGuide/VariableC++FroggerTheme.html` — `?m=02` | [ ] |
| Carte soft | `0X_02_Variable` | [ ] |
| `CardProperties` | `0X_02_Variable/CardProperties` | [ ] |
| Entrée deck | `DeckBootCampCpp/DeckInstaller.json` id 2 | [ ] |

---

## 03 — Conditions

| Élément | Fichier / URL | Corrigé |
|---------|---------------|---------|
| Carte site (FR) | `Formations/BaseCpp/cards/03-conditions.html` | [ ] |
| Guide site (FR) | `guides/03_ConditionsGuide/Conditions.html` — `?m=03` | [ ] |
| Carte soft | `0x_03_Conditions` | [ ] |
| `CardProperties` | `0x_03_Conditions/CardProperties` | [ ] |
| Entrée deck | `DeckBootCampCpp/DeckInstaller.json` id 3 | [ ] |

---

## 04 — Boucles

| Élément | Fichier / URL | Corrigé |
|---------|---------------|---------|
| Carte site (FR) | `Formations/BaseCpp/cards/04-boucles.html` | [ ] |
| Guide site (FR) | `guides/04_BouclesGuide/LoopModule.html` — `?m=04` | [ ] |
| Carte soft | `0X_04_Boucles` | [ ] |
| `CardProperties` | `0X_04_Boucles/CardProperties` | [ ] |
| Entrée deck | `DeckBootCampCpp/DeckInstaller.json` id 4 | [ ] |

---

## 05 — STD & Fonctions

| Élément | Fichier / URL | Corrigé |
|---------|---------------|---------|
| Carte site (FR) | `Formations/BaseCpp/cards/05-std-fonctions.html` | [ ] |
| Guide site (FR) | `guides/05_StdFonctionsGuide/stdLib&Fonction.html` — `?m=05` | [ ] |
| Carte soft | `0x_05_STD_Fonctions` | [ ] |
| `CardProperties` | `0x_05_STD_Fonctions/CardProperties` | [ ] |
| Entrée deck | `DeckBootCampCpp/DeckInstaller.json` id 5 | [ ] |

---

## 06 — Conteneurs

| Élément | Fichier / URL | Corrigé |
|---------|---------------|---------|
| Carte site (FR) | `Formations/BaseCpp/cards/06-conteneurs.html` | [ ] |
| Guide site (FR) | `guides/06_ConteneursGuide/Conteneurs.html` — `?m=06` | [ ] |
| Carte soft | `0X_06_Conteneurs` | [ ] |
| `CardProperties` | `0X_06_Conteneurs/CardProperties` | [ ] |
| Entrée deck | `DeckBootCampCpp/DeckInstaller.json` id 6 | [ ] |

---

## 07 — Struct & Méthodes

| Élément | Fichier / URL | Corrigé |
|---------|---------------|---------|
| Carte site (FR) | `Formations/BaseCpp/cards/07_StructMethode_Card/07-struct-methodes-card.html` | [ ] |
| Guide site (FR) | `guides/07_StructMethodesGuide/StructMethodes.html` — `?m=07` | [ ] |
| Carte soft | `0x_07_Struct_Methodes` | [ ] |
| `CardProperties` | `0x_07_Struct_Methodes/CardProperties` | [ ] |
| Entrée deck | `DeckBootCampCpp/DeckInstaller.json` id 7 | [ ] |

---

## Tests globaux — **P1**

### Site (révision FR)

- [ ] **P1** — Page `gamedevready-bases-cpp.html` : 7 miniatures, ancres, boutons guide
- [ ] **P1** — Parcours prod : carte → **Ouvrir le guide** → login → guide complet (×7)
- [ ] **P1** — Images guides : pas de lien cassé (`Image/`, `image/`)
- [ ] **P1** — Cohérence texte carte site ↔ guide (modules 01–07)
- [ ] **P1** — Anti-copie (×7) : accès direct `guides/*Guide/*.html` → **403** ; guide servi uniquement via `/auth/guide.php?m=XX` après login
- [ ] **P1** — Upload FTP après corrections (`Formations/BaseCpp/` + `.htaccess` guides)

### Soft (test deck)

- [ ] **P1** — Explore deck → `DeckBootCampCpp/` → preview + install
- [ ] **P1** — 7 cartes installées dans `data.json` + images `images_current/`
- [ ] **P1** — Parcours start / reset / code / done sur les 7 cartes
- [ ] **P1** — Aucun crash `newImage`, ordre `nextRevision` OK
- [ ] **P1** — Bouton **webGuide** ouvre la bonne page web (`URLNet`)

### Soft (check mise à jour — Phase A, branche `feat/options-update`)

> Manifest prod : `/flashdev/latest-version.json` · voir `FlashRevisionSoft/scripts/NOTE_UPDATE-SYSTEM.md`

- [ ] **P1 — T2** — À jour (local = manifest v0.1.0) : console `[updateCheck] Soft a jour` ; Options → mise à jour **grisée**
- [ ] **P1 — T3** — Hors ligne : **pas de crash** ; Start / Done / flèches OK ; bouton grisé
- [ ] **P1** — Deck bootcamp (7 cartes) : `data.json` et SM2 **inchangés** après le check
- [ ] **P1 — T1** *(optionnel)* — Manifest v0.2.0 en ligne → bouton mise à jour **actif**

---

## Synthèse progression

| Module | Carte site | Guide site | Carte soft |
|--------|:----------:|:----------:|:----------:|
| 01 Printf | [ ] | [ ] | [ ] |
| 02 Variables | [ ] | [ ] | [ ] |
| 03 Conditions | [ ] | [ ] | [ ] |
| 04 Boucles | [ ] | [ ] | [ ] |
| 05 STD & Fonctions | [ ] | [ ] | [ ] |
| 06 Conteneurs | [ ] | [ ] | [ ] |
| 07 Struct & Méthodes | [ ] | [ ] | [ ] |

**Total corrigé :** ___ / 21 (carte + guide + soft × 7)

---

## Pages vitrine — validation (**P1**)

| Page | Contenu | Liens | Page validée |
|------|:-------:|:-----:|:------------:|
| [`index.html`](../WebSite/index.html) | [ ] | [ ] | [ ] |
| [`centre-formation.html`](../WebSite/centre-formation.html) | [ ] | [ ] | [ ] |
| [`gamedevready.html`](../WebSite/gamedevready.html) | [ ] | [ ] | [ ] |

**Critère page validée :** contenu relu + tous les liens fonctionnels en prod (plus de `#` ni placeholders).

---

## Documents liés

| Fichier | Sujet |
|---------|--------|
| [`TODO.md`](../TODO.md) | Priorités site |
| [`FlashRevisionSoft/TODO.md`](../../FlashRevisionSoft/TODO.md) | Priorités soft |
| [`NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`](NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md) | Upload prod |

---

*Cocher `[x]` dans ce fichier au fil de la révision. Sources éditoriales : `FicheFormationHtlm/{module}/*Guide/` → recopier vers `WebSite/Formations/BaseCpp/` avant FTP.*
