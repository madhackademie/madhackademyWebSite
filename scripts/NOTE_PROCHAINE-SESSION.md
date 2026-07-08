# Guide — Prochaine session de travail

> **Référence principale** pour reprendre le projet madhackademyWebSite  
> Dernière mise à jour : 8 juillet 2026  
> Domaine : [gameopenmoney.com](https://gameopenmoney.com/)

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

> **P1 prioritaire session suivante :** valider le périmètre MVP avant release (`scripts/NOTE_MVP-FLASHDEV.md`) — pour chaque ligne Must-have, trancher bloquant release oui/non.

- [ ] **P1** — Valider le périmètre MVP avant release (`scripts/NOTE_MVP-FLASHDEV.md`) — cocher Must-have / Should-have / Won't-have
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
