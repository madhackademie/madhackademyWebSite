# Note — MVP FlashDev avant release

> Dernière mise à jour : 8 juillet 2026  
> **Document de réflexion** — cocher au fil des sessions ce qui est **impératif** avant la première distribution publique.  
> **Prochaine session P1 :** valider cette liste avec Laurent et figer le périmètre MVP.

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
| S7 | Installeur élève (script `.cmd` ou `.exe` — voir `NOTE_INSTALL-MADHACKADEMY.md`) | [ ] | [ ] |

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
| O3 | Offre boutique définie (prix, CTA bootcamp) | [ ] | [ ] |

---

## Should-have — important mais reportable en V1 ?

| # | Élément | Report V1 ? | Notes |
|---|---------|:-----------:|-------|
| V1 | Linkage soft ↔ compte (anti-partage decks) | [ ] | P2 avant release publique premium |
| V2 | Progression Map synchronisée (streaks → avatar) | [ ] | P3 |
| V3 | Installeur `.exe` graphique (vs script PowerShell) | [ ] | Phase 3 `NOTE_INSTALL-MADHACKADEMY.md` |
| V4 | Update 1 clic dans le soft | [ ] | Peut commencer par message + lien dashboard |
| V5 | Paiement webhook automatisé | [ ] | Manuel testeurs OK pour beta ? |

---

## Won't-have — explicitement hors MVP

- Gamification duels pixel art
- Cartes WebM animées
- Sync stats / leaderboard temps réel
- App mobile
- Version anglaise complète

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
| | | |

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
