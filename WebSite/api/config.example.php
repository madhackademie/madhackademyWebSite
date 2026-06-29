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
    /** Produit formation — utilisé pour les futurs élèves payants */
    'product_slug' => 'gamedevready-bases-cpp',
];
