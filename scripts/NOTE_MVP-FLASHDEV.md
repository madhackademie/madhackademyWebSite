# Note — MVP FlashDev avant release

> Dernière mise à jour : **30 octobre 2026**  
> **Document de réflexion** — cocher au fil des sessions ce qui est **impératif** avant la première distribution publique.  
> **Prochaine session P1 :** boucle élève prod + contrôleur prérequis deck (voir `NOTE_PROCHAINE-SESSION.md`).

---

## Hypothèse MVP (à valider)

Un élève peut :

1. **Installer** l'environnement (soft + outils C++) sans être développeur
2. **Installer** le deck Bootcamp C++ (`DeckBootCampCpp`)
3. **Réviser** les 7 cartes (start → code → done → SM2)
4. **Consulter** carte/guide sur le site (login pour guides protégés)
5. **Savoir** si une mise à jour du soft est disponible (message + chemin update — design à affiner)

**Hors MVP initial :** linkage compte/soft, progression Progression Map synchronisée, paiement automatisé, gamification duels.

---

## Must-have — impératif avant release ?

> Cocher **Oui** quand validé comme bloquant release. Laisser vide = encore à trancher.

### Soft (FlashRevisionSoft)

| # | Élément | Bloquant release ? | Fait / testé |
|---|---------|:------------------:|:------------:|
| S1 | Deck `DeckBootCampCpp/` installable (explore deck → install → 7 cartes) | [ ] | [ ] |
| S2 | Parcours complet sur 7 cartes (start, reset, VS Code, done, SM2) | [ ] | [ ] |
| S3 | `CardProperties` corrects par module (pas copiés depuis Print) | [ ] | [ ] |
| S4 | JSON deck harmonisé (`URLNet`, chemins, `nextRevision`) | [ ] | [ ] |
| S5 | Bouton **webGuide** → ouvre `URLNet` (carte site) | [x] | [ ] |
| S6 | Message « mise à jour disponible » + mécanisme update (même minimal) | [ ] | [ ] |
| S7 | Installeur élève (script `.cmd` ou `.exe` — voir `NOTE_INSTALL-MADHACKADEMY.md`) | [x] | [ ] |
| S8 | **Contrôleur prérequis** à l’install deck (ex. Raylib pour bootcamp) — message clair si manquant | [ ] | [ ] |

### Site (madhackademyWebSite)

| # | Élément | Bloquant release ? | Fait / testé |
|---|---------|:------------------:|:------------:|
| W1 | 7 cartes HTML FR + 7 guides FR relus (`NOTE_REVISION-BOOTCAMP-FR.md`) | [ ] | [ ] |
| W2 | Parcours prod : deck page → guide login → guide complet (×7) | [ ] | [ ] |
| W3 | Pages vitrine validées : `index`, `centre-formation`, `gamedevready` | [ ] | [ ] |
| W4 | `dashboard/index.php` : install soft, utilitaires, contenu acheté | [ ] | [ ] |
| W5 | **Bouton télécharger l'installeur** sur le dashboard | [ ] | [ ] |
| W6 | Lien de vente dans chaque guide HTML (rattrapage partage) | [ ] | [ ] |
| W7 | Paiement → rôle `student` (accès guides payants) | [ ] | [ ] |

### Organisation / légal

| # | Élément | Bloquant release ? | Fait / testé |
|---|---------|:------------------:|:------------:|
| O1 | Entité légale + compte pro (voir `NOTE_CREATION-ENTREPRISE.md`) | [ ] | [ ] |
| O2 | CGV + mentions légales | [ ] | [ ] |
| O3 | Offre boutique définie (prix, CTA bootcamp) — **grille oct. 2026** : P1/P2 **1 €**, P3 **10 €**, pack lancement **9 €** (accès progressif) ; détail com' → [`TODO.md`](../TODO.md) § *Grille tarifaire GameDevReady* | [ ] | [ ] |

---

## Should-have — important mais reportable en V1 ?

| # | Élément | Report V1 ? | Notes |
|---|---------|:-----------:|-------|
| V1 | Linkage soft ↔ compte (anti-partage decks) | [ ] | P2 avant release publique premium |
| V2 | Progression Map synchronisée (streaks → avatar) | [ ] | P3 |
| V3 | Installeur `.exe` graphique (vs script PowerShell) | [ ] | Phase 3 `NOTE_INSTALL-MADHACKADEMY.md` |
| V4 | Update 1 clic dans le soft | [ ] | Peut commencer par message + lien dashboard |
| V5 | Paiement webhook automatisé | [ ] | Manuel testeurs OK pour beta ? |
| V6 | **Certificat signature de code** (Authenticode, `FlashDev-Setup-*.exe`) | [x] | **31/10/2026** — reporté tant que revenus insuffisants (~200–600 €/an OV/EV). Voir `NOTE_INSTALL-MADHACKADEMY.md` § SmartScreen. Réévaluer quand bootcamp / ventes régulières. |

---

## Won't-have — explicitement hors MVP

- Gamification duels pixel art
- Cartes WebM animées
- Sync stats / leaderboard temps réel
- App mobile
- Hub formation multilingue (pages EN/DE, cartes + guides traduits, choix de zip deck) — **après** validation communauté FR

---

## Internationalisation formations (post-MVP — décision 30/10/2026)

**Ordre produit :** valider le parcours avec une **petite communauté française** → puis **anglais** → puis **allemand**. La vitrine EN (`training-center.html`) peut continuer à pointer vers les hubs **FR** (`gamedevready-bases-cpp.html`) tant que le contenu formation n’est pas traduit.

**Cible V2 (à concevoir plus tard, même principe de contrôle) :**

| Couche | FR (actuel) | Plus tard |
|--------|-------------|-----------|
| Vitrine hub deck | `gamedevready-bases-cpp.html` | Page(s) dédiée(s) par langue (ex. `…-en.html`) |
| Cartes + guides site | 7 cartes + 7 guides FR | Traductions ; URLs distinctes par langue, **même login** |
| Téléchargement deck | un zip (config `deck_download_files`) | **Une page** (dashboard ou merci) : choix **FR / EN / DE** parmi les zips autorisés |

**Contrôle d’accès — invariant quelle que soit la langue :**

- **Un achat = un droit produit** (`user_products.product_slug`, ex. `gamedevready-bases-cpp`) — pas un slug par langue.
- **Guides protégés** : `mha_require_product(slug)` + session ; la langue ne crée pas un produit séparé.
- **Formation Phase 1** : liaison **1 compte ↔ 1 machine** (`mha_ensure_formation_device`) — identique pour toutes les langues du même produit.
- **Téléchargement zip** : login + `mha_can_access_product` ; le serveur ne sert que les fichiers langue autorisés pour ce slug (à étendre dans `deck_download_files` ou catalogue, sans dupliquer la vente).

Webhook Systeme.io / boutique simulée : toujours **grant sur le slug métier**, pas sur la locale.

---

## Critère « MVP validé » (release go)

Cocher quand **tous** les Must-have marqués **Oui** sont testés :

- [ ] **MVP soft validé** — test complet deck + install sur machine vierge
- [ ] **MVP site validé** — parcours élève bout en bout (dashboard → install → deck → guide)
- [ ] **MVP business validé** — entité + paiement + pages légales (si vente dès release)
- [ ] **Décision release** — date + version taguée (`v0.1.0-mvp` ?)

---

## Journal des décisions

| Date | Décision | Validé par |
|------|----------|------------|
| 08/07/2026 | Création note MVP — périmètre à trancher session suivante | — |
| 29/10/2026 | **Setup exe** = socle LÖVE + VS Code + Lua seulement ; **Raylib hors installeur** ; prérequis deck = contrôleur à l’install (S8) + test boucle site→soft P1 session 30/10 | Laurent |
| 30/10/2026 | Formations **FR seules** jusqu’à validation communauté ; i18n **EN puis DE** ensuite (pages dédiées + cartes/guides + choix zip) — **même** contrôle compte / `user_products` / device | Laurent |
| 30/10/2026 | **Boutique / GameDevReady** : contenu dupliqué accepté jusqu’à loop Phase 1 OK ; **refonte architecture + boutique** reportée P2 post-validation | Laurent |
| 31/10/2026 | **Pas de certificat code signing** pour l’instant (coût annuel) ; installeur non signé → SmartScreen / navigateur : procédure testeur + SHA-256 manifest ; **réacheter signature quand revenus le justifient** (build déjà prêt : `FLASHDEV_SIGN_PFX` dans `build-installer-exe.ps1`) | Laurent |

---

## Documents liés

| Fichier | Sujet |
|---------|--------|
| [`NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md) | Reprise session |
| [`NOTE_REVISION-BOOTCAMP-FR.md`](NOTE_REVISION-BOOTCAMP-FR.md) | Tests carte par carte |
| [`NOTE_INSTALL-MADHACKADEMY.md`](NOTE_INSTALL-MADHACKADEMY.md) | Installeur |
| [`NOTE_CREATION-ENTREPRISE.md`](NOTE_CREATION-ENTREPRISE.md) | Légal / N26 |
| [`../FlashRevisionSoft/TODO.md`](../FlashRevisionSoft/TODO.md) | P1 soft |
| [`../TODO.md`](../TODO.md) | P1 site |
