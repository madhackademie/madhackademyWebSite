<?php
/**
 * Copier ce fichier en config.php et adapter les valeurs.
 * config.php ne doit PAS être commité (secrets).
 */
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'madhackademy',
        'user' => 'VOTRE_USER_MYSQL',
        'pass' => 'VOTRE_MOT_DE_PASSE_MYSQL',
        'charset' => 'utf8mb4',
    ],
    /** Clé secrète pour auth/setup.php (première installation uniquement) */
    'setup_key' => 'CHANGEZ-MOI-SETUP-2026',
    /** Produit formation — reserved pour les futurs élèves payants */
    'product_slug' => 'gamedevready-bases-cpp',
    /** Acquis soft gratuit — débloque le download FlashDev (pas les guides) */
    'flashdev_product_slug' => 'flashdev-soft',
    /** Clé API Systeme.io — opt-in FlashDev (api/systeme-optin.php) */
    'systeme_io_api_key' => 'COLLEZ_VOTRE_CLE_ICI',
    /** Nom du tag Systeme.io (résolu en ID via API — déclenche l’automation download) */
    'systeme_io_tag_flashdev' => 'flashdev-download',
    /**
     * true = Phase 1 sans Systeme.io (0 €, activate-formation-prelaunch) — tant que l’entreprise n’est pas en activité.
     * Mettre false + funnel 1 € + webhook SALE_NEW dès que la vente légale est prête.
     */
    'formation_free_grant_until_systeme_io' => true,
    /**
     * Boutique GameDevReady — paiement Systeme.io (TVA) quand formation_free_grant_until_systeme_io = false.
     * Lien checkout = étape funnel ou produit boutique Systeme.io (1 € Phase 1).
     */
    'shop_checkout_urls' => [
        'gamedevready-bases-cpp' => 'https://VOTRE-FUNNEL-OU-BOUTIQUE.systeme.io/...',
    ],
    /**
     * Webhook natif Systeme.io (Paramètres → Webhooks, événement SALE_NEW).
     * Même secret que celui saisi dans l’UI Systeme.io.
     */
    'systeme_io_webhook_secret' => 'CHANGEZ-MOI-WEBHOOK-SYSTEME-IO',
    /**
     * Mapping pricePlan.id (payload SALE_NEW) → product_slug MySQL.
     * Récupérer l’ID après un test achat ou dans l’admin produit Systeme.io.
     */
    'systeme_io_price_plan_products' => [
        // 123456 => 'gamedevready-bases-cpp',
    ],
    /** Lier l'accès aux guides formation au 1er navigateur (élèves student avec produit formation) */
    'device_bind_formation' => true,
    /** Secret HMAC cookie appareil — changer en prod (config.php uniquement) */
    'device_binding_secret' => 'CHANGEZ-MOI-DEVICE-SECRET-2026',
    /**
     * Zip decks (hors URL publique) — clé = product_slug, valeur = chemin relatif sous WebSite/ ou absolu
     * Ex. 'gamedevready-bases-cpp' => 'private/decks/GameDevReady-Phase1-BasesCpp.zip',
     */
    'deck_download_files' => [
        // 'gamedevready-bases-cpp' => 'private/decks/GameDevReady-Phase1-BasesCpp.zip',
    ],
];
