# Boutique GameDevReady — Systeme.io (paiement + validation)

**Principe cible :** tout l’aspect **commercial** passe par **Systeme.io** — checkout, **TVA**, factures, remboursements.  
Le site grant le deck quand Systeme.io envoie l’événement **`SALE_NEW`**.

## Mode pré-lancement (entreprise pas encore en service)

Tant que `formation_free_grant_until_systeme_io` = **true** dans `api/config.php` :

| Élément | Comportement |
|---------|----------------|
| Boutique / dashboard | **0 €** — bouton « Activer Phase 1 » |
| Endpoint | `/auth/activate-formation-prelaunch.php` → même grant que la vente (deck + guides + flashdev-soft) |
| Systeme.io vente | **Non utilisé** (opt-in FlashDev CRM reste possible via API) |

**Désactiver le bypass** dès l’entreprise est en activité : `formation_free_grant_until_systeme_io` → **false**, puis checklist § « Entreprise en activité » dans [`TODO.md`](../TODO.md).

---

## Mode production (entreprise en activité)

| Fichier | Rôle |
|---------|------|
| `WebSite/boutique/index.php` | Grille 3 phases (1 en vente) |
| `WebSite/boutique/acheter.php` | Login → redirect URL funnel / boutique Systeme.io |
| `WebSite/boutique/merci.php` | Retour élève (l’accès = webhook, pas cette page seule) |
| `WebSite/api/systeme-webhook.php` | **Webhook natif** Systeme.io → `user_products` |
| `WebSite/api/shop-webhook.php` | Alias (même handler) — préférer `systeme-webhook.php` |
| `WebSite/auth/activate-formation-prelaunch.php` | Bypass 0 € (pré-lancement uniquement) |

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

### 4. Page merci funnel (optionnel)

Dans le funnel Systeme.io, page de remerciement ou redirection :

`https://madhackademy.eu/boutique/merci.php?product=gamedevready-bases-cpp`

(`acheter.php` peut aussi ajouter `success_url` si ton funnel le supporte.)

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

Dashboard **admin / testeur** : formulaire « Accorder un produit » (support interne uniquement).

## FTP

Uploader : `boutique/`, `api/systeme-webhook.php`, `api/bootstrap.php`, pages vitrine, **sans** écraser `config.php`.

## Liens

| Doc | Rôle |
|-----|------|
| [`NOTE_SYSTEMEIO-API-FLASHDEV.md`](NOTE_SYSTEMEIO-API-FLASHDEV.md) | Opt-in soft + API contacts |
| [`NOTE_AUTH-SETUP.md`](NOTE_AUTH-SETUP.md) | Comptes / produits |
