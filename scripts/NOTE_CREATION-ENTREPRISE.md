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
| **Passerelle de vente obligatoire** (plus de bypass 0 €) | Statut créé **et** validé (Finanzamt / TVA / compte pro) |
| Webhook paiement → `user_products` | Stripe connecté à Systeme.io + entité légale |
| Boutique Phase 1 **1 €** | Price plan Systeme.io + `shop_checkout_urls` dans `config.php` |
| Dashboard élève | Opérationnel en pré-lancement via bypass ; **vente publique** = passerelle active |
| CGV / mentions légales (`TODO.md`) | **Bloqué** sans entité identifiée |

---

## Après statut validé — brancher la passerelle de vente

> **Décision oct. 2026 :** tant que l’entreprise n’est pas en place, les élèves passent par le site (`acheter.php` → confirm 0 €).  
> **Dès que le statut est créé et validé**, tout achat Phase 1 doit **automatiquement** passer par **Systeme.io** (TVA, facturation) — plus de grant gratuit sur le site.

Checklist unique (cocher dans l’ordre) — détail technique : [`NOTE_BOUTIQUE-GAMEDEVREADY.md`](NOTE_BOUTIQUE-GAMEDEVREADY.md) · cases `TODO.md` § Boutique / technique :

| # | Action |
|---|--------|
| 1 | Entité + Finanzamt / TVA OK · compte pro (ex. N26) · Stripe raccordé à Systeme.io |
| 2 | Funnel **Order form** Phase 1 à **1 €** (price plan enregistré, pas l’exemple visuel) |
| 3 | **`api/config.php` prod** : `formation_free_grant_until_systeme_io` → **`false`** |
| 4 | Même fichier : `shop_checkout_urls['gamedevready-bases-cpp']` = URL checkout Systeme.io |
| 5 | Webhook compte **SALE_NEW** → `https://madhackademy.eu/api/systeme-webhook.php` + `systeme_io_webhook_secret` |
| 6 | Mapping `systeme_io_price_plan_products` (ID price plan → `gamedevready-bases-cpp`) |
| 7 | Test : boutique → **Payer via Systeme.io** → merci → dashboard deck · remboursement test |
| 8 | CGV / mentions légales site alignées sur l’entité |

Parcours élève **après linkage** : Formation → Boutique → `acheter.php` → **Systeme.io** → `merci.php` → accès deck via webhook (plus de bouton « Confirmer 0 € »).

---

## Documents liés (repo)

| Fichier | Sujet |
|---------|--------|
| [`TODO.md`](../TODO.md) | Offres boutique, textes légaux, **P2 passerelle post-entreprise** |
| [`NOTE_BOUTIQUE-GAMEDEVREADY.md`](NOTE_BOUTIQUE-GAMEDEVREADY.md) | Systeme.io, webhook, bypass pré-lancement |
| [`scripts/NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md) | Reprise session dev |

---

## Journal (à compléter)

| Date | Action | Résultat |
|------|--------|----------|
| 08/07/2026 | Création de cette note | Références N26 + piste IA juridique |
| 30/10/2026 | Décision produit | Vente publique = passerelle Systeme.io après statut validé ; pré-lancement = site 0 € uniquement |
| | | |

---

*Fin de note — compléter le journal au fur et à mesure des démarches.*
