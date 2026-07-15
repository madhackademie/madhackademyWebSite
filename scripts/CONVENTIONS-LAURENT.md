# Mes 5 conventions — MadHackAdemy / FlashDev

> **Pour Laurent** — règles personnelles à respecter sur le projet.  
> L'assistant les cite en début de session (voir `.cursor/rules/conventions-laurent-rappel.mdc`).  
> Dernière mise à jour : 15 juillet 2026

---

## 1. Reprise session : sync avant code

Avant toute modification :

1. Lancer `.\scripts\sync-both.ps1` (site + soft)
2. Lire `scripts/NOTE_PROCHAINE-SESSION.md`
3. Vérifier la branche soft (`scripts/NOTE_SYNC-BOTH.md` — merge après tests, portable ou bureau)

**Pourquoi :** éviter de coder sur une base obsolète ou une branche non validée.

---

## 2. Un seul P1 par session

- Choisir **une** priorité bloquante (celle du `TODO.md` ou `NOTE_PROCHAINE-SESSION.md`)
- La terminer ou la cocher avant d'ouvrir un nouveau chantier
- Refuser la dispersion (P2/P3 en fond sauf si P1 fini)

**Pourquoi :** le MVP release avance par incréments testables, pas par accumulation de demi-faits.

---

## 3. Documenter chaque décision

Après une avancée ou un choix de périmètre, mettre à jour **au moins un** de :

- `TODO.md` (site) / `FlashRevisionSoft/TODO.md` (soft)
- `scripts/NOTE_MVP-FLASHDEV.md` (must-have release)
- `scripts/NOTE_PROCHAINE-SESSION.md` (reprise suivante)

**Pourquoi :** l'assistant et toi reprenez la même vérité à la session suivante.

---

## 4. Enchaîner les P1 et clôturer la session

**Quand la P1 courante est terminée** (faite ou cochée) :

- Nommer **tout de suite** la prochaine P1 à exécuter — une seule, explicite
- L'écrire en tête de `scripts/NOTE_PROCHAINE-SESSION.md` (section « Prochaine étape »)

**En fin de session** (obligatoire, ne pas quitter sans) :

1. Bilan : ce qui a été fait / coché aujourd'hui
2. **Prochaine P1** nommée pour la session suivante
3. Tâches de test éventuelles si la P1 implique une validation
4. Mise à jour `TODO.md` si le backlog a changé
5. Comit.both avant fermeture

**Pourquoi :** la session suivante démarre sans réfléchir « par où commencer ».

---

## 5. Tester avant merge / release

Avant merge branche soft ou déploiement FTP :

- [ ] Tests deck `DeckBootCampCpp/` (install + 7 cartes, parcours complet)
- [ ] Parcours prod site : carte → login → guide (×7)
- [ ] Merge soft sur **portable ou bureau** (après tests), puis `sync-both` sur l'autre machine

**Pourquoi :** les testeurs et la prod ne doivent pas recevoir une branche non validée.

---

## Modifier cette liste

Éditer ce fichier directement. L'assistant relit ces 5 règles à chaque reprise de session.  
Ne pas dépasser **5 règles** — si tu en ajoutes une, en retirer une autre.
