# Boutique GameDevReady — Systeme.io (paiement + validation)

**Principe cible :** tout l’aspect **commercial** passe par **Systeme.io** — checkout, **TVA**, factures, remboursements.  
Le site grant le deck quand Systeme.io envoie l’événement **`SALE_NEW`**.

## Vitrine temporaire — boutique ≈ GameDevReady (oct. 2026)

**Constat :** le contenu affiché se recoupe entre le hub **`gamedevready.html`**, **`centre-formation.html#boutique`** et **`/boutique/index.php`**.

**Nav (oct. 2026)** : menu FR unifié — Accueil · FlashDev · Téléchargement · Formation · GameDevReady · **Boutique** (+ FR|EN). Partial PHP : `WebSite/partials/nav-fr.php` (pages boutique) ; mêmes liens copiés sur les `.html` vitrine.

**Décision :** on **garde** la duplication de contenu le temps de **valider la boucle élève**. Le bas de `#boutique` sur centre-formation envoie vers le **checkout** (`acheter.php`), pas vers une 3e grille identique sans explication.

**Après boucle GameDevReady Phase 1 validée** (communauté FR) — **P2 à planifier** :

- Repenser **l’architecture vitrine** (rôle distinct : hub pédagogique vs tunnel achat vs centre formation).
- **Refonte boutique** : une source de vérité contenu, landings par offre, moins de pages redondantes.
- Trancher si la boutique reste sous `/boutique/` ou fusion partielle avec le hub GameDevReady.

Suivi tâches : [`TODO.md`](../TODO.md) § refonte boutique / architecture.

---

Tant que `formation_free_grant_until_systeme_io` = **true** dans `api/config.php` :

| Élément | Comportement |
|---------|----------------|
| Boutique / dashboard | **0 €** — « Commander » → page acheter |
| Parcours | `/boutique/acheter.php` (récap) → **Confirmer** → `/boutique/merci.php` (zip + liens) |
| Systeme.io vente | **Non utilisé** — URL merci déjà utilisable dans un funnel test |

**Désactiver le bypass** dès l’entreprise est en activité : `formation_free_grant_until_systeme_io` → **false**, puis checklist § « Entreprise en activité » dans [`TODO.md`](../TODO.md) et **§ Après statut validé** dans [`NOTE_CREATION-ENTREPRISE.md`](NOTE_CREATION-ENTREPRISE.md).

---

## Gate commercial — passerelle obligatoire (post-entreprise)

Quand le statut est **créé et validé**, le site **ne doit plus** grant la Phase 1 en 0 € :

1. `formation_free_grant_until_systeme_io` = **false** → `acheter.php` n’affiche plus « Confirmer 0 € » ; seul le bouton **Payer via Systeme.io** (ou message « paiement en configuration » si URL manquante).
2. L’accès deck = **webhook `SALE_NEW`** uniquement (plus de contournement hors passerelle).

**Avant ce linkage :** pré-lancement sur madhackademy.eu uniquement ; funnel Systeme.io peut rester en brouillon.

---

## Mode production (entreprise en activité)

| Fichier | Rôle |
|---------|------|
| `WebSite/boutique/index.php` | Grille 3 phases (1 en vente) |
| `WebSite/boutique/acheter.php` | Pseudo checkout (récap + confirm 0 € ou redirect Systeme.io) |
| `WebSite/boutique/merci.php` | Remerciement + liens téléchargement deck / FlashDev |
| `WebSite/api/systeme-webhook.php` | **Webhook natif** Systeme.io → `user_products` |
| `WebSite/api/shop-webhook.php` | Alias (même handler) — préférer `systeme-webhook.php` |
| `WebSite/auth/activate-formation-prelaunch.php` | Redirige vers `acheter.php` (compat anciens liens) |

## Côté Systeme.io (Laurent)

### 1. Produit / funnel Phase 1 à 1 €

- Créer l’offre **Bootcamp C++ Phase 1** (1 €, one-shot) dans un funnel ou la boutique créateur.
- Copier l’URL publique de l’étape de paiement dans `shop_checkout_urls`.

### 2. Webhook compte (recommandé)

1. **Paramètres** → **Webhooks** → créer un webhook.
2. **URL :** `https://madhackademy.eu/api/systeme-webhook.php`
3. **Secret :** long aléatoire → **identique** à `systeme_io_webhook_secret` dans `api/config.php`.
4. **Abonnement événement :** **`SALE_NEW`** (nouvelle vente).

Doc : [Webhooks Systeme.io](https://developer.systeme.io/docs/webhooks) · [SALE_NEW](https://developer.systeme.io/docs/webhooks-events-new-sale)

### 3. Mapping price plan → deck site

Après un achat test, noter `pricePlan.id` dans le payload (ou l’ID admin produit) :

```php
'systeme_io_price_plan_products' => [
    94283 => 'gamedevready-bases-cpp', // exemple — remplacer par ton ID réel
],
```

Sans mapping, le webhook répond `unmapped_sale` (422) et **aucun** accès n’est granté.

### 4. Page merci funnel (sans ou avec paiement)

URL à coller dans Systeme.io (redirection fin de tunnel) :

```
https://madhackademy.eu/boutique/merci.php?product=gamedevready-bases-cpp
```

Parcours site **sans** Systeme.io (pré-lancement) :

```
https://madhackademy.eu/boutique/acheter.php?product=gamedevready-bases-cpp
→ Confirmer (0 €) → merci + téléchargement deck
```

Quand la vente sera active, même URL merci après paiement ; accès deck = webhook **SALE_NEW**.

(`acheter.php` peut aussi ajouter `success_url` vers cette URL si ton funnel le supporte.)

### 5. Automations CRM (optionnel)

- Tag ex. `gamedevready-p1-acheteur` sur vente → emails de suivi **dans Systeme.io**.
- Ne pas confondre avec l’opt-in **`flashdev-download`** (soft gratuit).

## `api/config.php` (prod, non commité)

```php
'formation_free_grant_until_systeme_io' => false, // true = pré-lancement 0 € uniquement
'shop_checkout_urls' => [
    'gamedevready-bases-cpp' => 'https://….systeme.io/…',
],
'systeme_io_webhook_secret' => '…',
'systeme_io_price_plan_products' => [
    ID_PRICE_PLAN => 'gamedevready-bases-cpp',
],
'deck_download_files' => [
    'gamedevready-bases-cpp' => 'private/decks/GameDevReady-Phase1-BasesCpp.zip',
],
'systeme_io_api_key' => '…', // inchangé — opt-in FlashDev
```

Supprimer `beta_self_grant_formation` si encore présent.

## Effet du webhook

Sur **`SALE_NEW`** signé (`X-Webhook-Signature`) :

1. Email acheteur = `customer.email`
2. Produit site = mapping `pricePlan.id`
3. Création compte élève si besoin (`mha_ensure_flashdev_user`)
4. Grant **`gamedevready-bases-cpp`** + **`flashdev-soft`**

## Test sans payer

| Méthode | Usage |
|---------|--------|
| Dashboard **admin / testeur** | Formulaire « Accorder un produit » (support interne) |
| **Code promo testeur** | Page [`/auth/deck.php`](/WebSite/auth/deck.php) ou [`/telecharger-deck.php`](/WebSite/telecharger-deck.php) — grant Phase 1 + FlashDev si code valide |

### Config code promo (`api/config.php`, non commité)

```php
'tester_promo_enabled' => true,
'tester_promo_codes' => [
    'GDR-TEST-BOOTCAMP' => 'gamedevready-bases-cpp', // changer le libellé en prod
],
'deck_download_files' => [
    'gamedevready-bases-cpp' => 'private/decks/GameDevReady-Phase1-BasesCpp.zip',
],
```

- Saisie du code : **`/boutique/acheter.php?product=…`** (bloc « Code promo testeur ») ou [`/auth/deck.php`](/WebSite/auth/deck.php) / [`/telecharger-deck.php`](/WebSite/telecharger-deck.php).

- **`tester_promo_enabled` → false** avant ouverture publique (ou supprimer les codes).
- Le code est **insensible à la casse** ; espaces ignorés.
- Même effet qu’un achat pré-lancement sur le slug : `user_products` + téléchargement via `download-deck.php` si le zip est publié.

Parcours testeur : compte sur `/telecharger.html` → [`/telecharger-deck.php`](https://madhackademy.eu/telecharger-deck.php) → code → bouton zip.

## FTP

Uploader : `boutique/`, `api/systeme-webhook.php`, `api/bootstrap.php`, pages vitrine, **sans** écraser `config.php`.

## Liens

| Doc | Rôle |
|-----|------|
| [`NOTE_SYSTEMEIO-API-FLASHDEV.md`](NOTE_SYSTEMEIO-API-FLASHDEV.md) | Opt-in soft + API contacts |
| [`NOTE_AUTH-SETUP.md`](NOTE_AUTH-SETUP.md) | Comptes / produits |
