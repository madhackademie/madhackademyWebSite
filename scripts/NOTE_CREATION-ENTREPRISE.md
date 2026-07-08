# Note — Création d'entreprise (Allemagne / commercialisation)

> Dernière mise à jour : 8 juillet 2026  
> **Hors périmètre technique** — décisions juridiques / fiscales à trancher avant vente officielle du bootcamp GameDevReady.  
> **Ne remplace pas un conseil juridique ou fiscal professionnel.**

---

## Objectif

Clarifier si tu dois **créer une structure en Allemagne** pour vendre la formation (decks, guides, abonnements), **quel statut** choisir, et **quelles démarches** enchaîner — en visant une mise en place **compte pro + activité** vers **septembre 2026**.

---

## Contexte projet

| Élément | Lien avec l'entreprise |
|---------|------------------------|
| Vente bootcamp GameDevReady | Revenus formation / contenu digital |
| Guides protégés + paiement (`student`) | Besoin d'une entité légale pour encaisser |
| `gameopenmoney.com` | Vitrine + boutique à venir |
| FlashRevisionSoft | Produit logiciel (vente deck, pas obligatoirement la même entité — à valider) |

---

## Références utiles

### 1) Recherche IA juridique / fiscal (Allemagne)

Question de départ à creuser avec un outil IA spécialisé juridique/fiscal **ou** un conseiller :

> *Faut-il créer une entreprise en Allemagne pour vendre une formation en ligne (contenu + logiciel) ? Si oui : quel type (Freiberufler, Einzelunternehmen, UG, GmbH…) et quelles démarches ?*

- Recherche Google de référence (à rouvrir / affiner) :  
  [Recherche : IA juridique/fiscal — création entreprise Allemagne](https://www.google.com/search?q=quel+est+l%27ia+qui+est+le+plus+adapt%C3%A9+en+juridique%2Ffiscal+pour+m%27aider+a+savoir+si+je+dois+cr%C3%A9er+une+entreprise+en+allemagne+et+si+oui+quel+type+et+les+demarches)

**À documenter ici après recherche :**

- [ ] Réponse synthétique : créer ou non ?
- [ ] Statut retenu (ou shortlist)
- [ ] Seuils / obligations (TVA, Gewerbeanmeldung, Finanzamt, etc.)
- [ ] Outil IA ou professionnel consulté (nom, date, conclusion)

### 2) Compte bancaire pro — N26

Cible : ouverture **septembre 2026**.

- Page N26 — compte auto-entrepreneur / business :  
  [https://n26.com/fr-fr/compte-bancaire-auto-entrepreneur](https://n26.com/fr-fr/compte-bancaire-auto-entrepreneur)

**Points à vérifier (d'après la doc N26) :**

| Point | Détail |
|-------|--------|
| Offre visée | N26 **Business Smart** (~ 4,90 €/mois) — freelances / indépendants |
| Nom sur le compte | **Nom légal personnel** (pas le nom commercial sur la carte) |
| Compte perso + pro | **Pas les deux en parallèle** sur N26 |
| Outils utiles | Espaces (sous-comptes), répartition revenus, stats dépenses |
| Cashback | 0,1 % sur paiements carte (Business Smart) |

**Checklist N26 :**

- [ ] Vérifier éligibilité (résidence, statut pro visé en Allemagne)
- [ ] Choisir Business Smart vs autre offre N26
- [ ] Préparer pièce d'identité + infos fiscales
- [ ] Ouvrir le compte (objectif : **septembre 2026**)
- [ ] Domicilier encaissements boutique / System.io / Stripe sur ce compte

---

## Questions à trancher (checklist)

### Statut & fiscalité

- [ ] Activité en Allemagne : **Freiberufler** vs **Gewerbe** (Einzelunternehmen) vs société (UG/GmbH) ?
- [ ] Chiffre d'affaires prévisionnel bootcamp (année 1) — impact TVA / Kleinunternehmerregelung ?
- [ ] Facturation clients FR / UE / hors UE — règles OSS / TVA numérique ?
- [ ] Propriété intellectuelle (guides, soft) : reste-t-elle à ton nom ou à transférer dans la structure ?

### Opérationnel

- [ ] Nom commercial : MadHackAdemy / GameDevReady / gameopenmoney — cohérence avec statut choisi
- [ ] Compte de paiement en ligne (Stripe, System.io, PayPal…) raccordé au compte N26 pro
- [ ] Mentions légales + CGV site alignées sur l'entité légale (voir `TODO.md` § contenu légal)
- [ ] Assurance RC pro / cyber (optionnel selon activité — à valider)

### Calendrier cible

| Échéance | Action |
|----------|--------|
| **Juillet–août 2026** | Recherche statut + consultation IA / conseiller |
| **Août 2026** | Décision statut + démarches administratives (Gewerbe / Finanzamt…) |
| **Septembre 2026** | Ouverture compte N26 Business + branchement paiements |
| **Avant release publique bootcamp** | Entité + compte + facturation opérationnels |

---

## Lien avec le projet technique

| Tâche technique | Dépendance entreprise |
|-----------------|----------------------|
| Webhook paiement → `user_products` | Compte marchand / entité légale |
| Boutique (3 offres P1) | Prix TTC, factures, CGV |
| Dashboard élève | Peut attendre ; vente nécessite au minimum encaissement légal |
| CGV / mentions légales (`TODO.md`) | **Bloqué** sans entité identifiée |

---

## Documents liés (repo)

| Fichier | Sujet |
|---------|--------|
| [`TODO.md`](../TODO.md) | Offres boutique, textes légaux |
| [`NOTE_ARCHITECTURE_SOFT-SITE.md`](../NOTE_ARCHITECTURE_SOFT-SITE.md) | Paiement, System.io |
| [`scripts/NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md) | Reprise session dev |

---

## Journal (à compléter)

| Date | Action | Résultat |
|------|--------|----------|
| 08/07/2026 | Création de cette note | Références N26 + piste IA juridique |
| | | |
| | | |

---

*Fin de note — compléter le journal au fur et à mesure des démarches.*
