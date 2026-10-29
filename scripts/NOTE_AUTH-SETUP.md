# Auth — comptes admin / testeur / guides protégés

> Première mise en place : juin 2026  
> **Production OVH : juillet 2026** — déploiement, tests parcours, identifiants testeurs distribués  
> **OVH (maintenance / dépannage) :** [`NOTE_OVH-PHP-MYSQL.md`](NOTE_OVH-PHP-MYSQL.md)  
> **État projet :** [`NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md)

---

## Rôles

| Rôle | Accès guides | Usage |
|------|--------------|-------|
| **admin** | Oui (tous) | Vous — gestion comptes via phpMyAdmin si besoin |
| **tester** | Oui (tous) | Testeurs beta |
| **student** | Si produit accordé | Futurs élèves payants |

---

## URLs

| Page | URL |
|------|-----|
| Connexion | `/auth/login.php` |
| Déconnexion | `/auth/logout.php` |
| Guide module 01 | `/auth/guide.php?m=01` |
| Guide module 02–07 | `/auth/guide.php?m=02` … `m=07` |

---

## Fichiers liés

| Fichier | Rôle |
|---------|------|
| `api/bootstrap.php` | Session, login, contrôle accès |
| `api/config.php` | Secrets MySQL (sur FTP uniquement, jamais sur Git) |
| `auth/guide.php` | Sert le HTML guide si autorisé |
| `Formations/BaseCpp/guides/.htaccess` | Bloque accès direct aux `.html` des guides |

---

## Données client — compte, achats, catalogue

| Table | Rôle |
|-------|------|
| **`users`** | Compte (email, rôle, mot de passe) |
| **`user_products`** | **Acquis** : une ligne par produit possédé (`product_slug`, `granted_at`) — soft gratuit, phases formation, futur pack |
| **`products`** | **Catalogue** (à déployer) : titre, phase, URL page, chemin zip deck re-téléchargeable — voir `sql/migration_products_catalog.sql` |
| **`user_trusted_devices`** | Navigateur autorisé pour les guides formation (1 par élève) |

Flux : paiement ou opt-in → `INSERT user_products` → dashboard affiche `JOIN products` → boutons guide + re-download deck.

Slugs actuels (`config.php`) : `flashdev_product_slug`, `product_slug` (formation P1). À terme, **tous** les slugs boutique doivent exister dans **`products`**.

---

## Prochaine étape (élèves payants)

Quand Stripe / System.io sera branché : webhook PHP → insert `user_products` pour rôle `student`.

Voir `NOTE_ARCHITECTURE_SOFT-SITE.md` §5 et §7.

## Appareil lié — accès guides formation

Pour les **élèves** (`student`) avec le produit formation (`product_slug`, ex. `gamedevready-bases-cpp`) :

- **1er** clic « Ouvrir le guide » → cookie `mha_device` + ligne MySQL `user_trusted_devices`
- **Autre PC / navigateur** → page 403 (guides bloqués) ; connexion site + téléchargement FlashDev restent possibles
- **Admin / testeur** : pas de lien appareil

### Mise en prod

1. Importer `WebSite/sql/migration_user_trusted_devices.sql` (ou schéma complet à jour)
2. Dans `api/config.php` : copier `device_bind_formation` + `device_binding_secret` depuis `config.example.php`
3. Tester : compte `student` avec formation → guide OK sur PC A → guide refusé en navigation privée sur PC B
4. Changement d’ordinateur élève : `/auth/setup.php` (admin) → **Réinitialiser** appareil formation

Désactiver temporairement : `'device_bind_formation' => false` dans `config.php`.

## Piratage guides + deck partagé

Un élève légitime peut sauvegarder le HTML des guides et le donner avec le deck : un tiers peut réviser **sans compte**. Aucune protection web n’y met fin totalement.

**Position produit (2026)** : phases **1 €** — friction actuelle (achat, login, appareil formation, pas de guide dans le zip) suffit ; **pas** de DRM lourd ni filigrane obligatoire tout de suite. **Phase 3 (10 €)** et pack : réévaluer après **statistiques** (`TODO.md` § *Protection anti-copie* — `guide_access_log`, analytics).

## Opt-in téléchargement FlashDev (prioritaire)

Formulaire sur le site → API Systeme.io (créer contact) → email avec lien download.  
Tuto : [`NOTE_SYSTEMEIO-API-FLASHDEV.md`](NOTE_SYSTEMEIO-API-FLASHDEV.md).
