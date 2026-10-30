# Decks zip (privé)

Les fichiers `.zip` des decks **ne doivent pas** être accessibles en URL directe.

1. Déposer le zip ici (ex. `GameDevReady-Phase1-BasesCpp.zip`).
2. Dans `api/config.php` (FTP) :

```php
'deck_download_files' => [
    'gamedevready-bases-cpp' => 'private/decks/GameDevReady-Phase1-BasesCpp.zip',
],
```

3. Ou renseigner `products.deck_download_path` en base (migration `sql/migration_products_catalog.sql`).

Téléchargement élève : `/dashboard/` → bouton, ou `/auth/download-deck.php?slug=gamedevready-bases-cpp` (session + droit produit requis).
