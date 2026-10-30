<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$productSlug = trim((string) ($_GET['product'] ?? mha_formation_product_slug()));
$user = mha_require_login('/boutique/merci.php?product=' . rawurlencode($productSlug));

$owned = mha_can_access_product($productSlug);
$offer = mha_shop_offer($productSlug);
$title = $offer['title'] ?? 'Phase 1 — Bootcamp C++';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci pour votre achat — MadHackAdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/nav-site.css?v=8">
</head>
<body class="bg-black text-white font-mono min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full border border-gray-800 rounded-xl p-8 bg-gray-950 text-center">
        <h1 class="text-2xl font-bold text-green-400 mb-4">Merci !</h1>
        <p class="text-gray-300 text-sm mb-6">
            Paiement traité par <strong>Systeme.io</strong> (TVA et facturation).
            <?php if ($owned): ?>
                Ton accès <strong><?= mha_escape((string) $title) ?></strong> est actif.
            <?php else: ?>
                L’accès au deck est débloqué dès la confirmation Systeme.io (webhook <em>SALE_NEW</em>, quelques secondes).
                Actualise <a href="/dashboard/" class="text-yellow-400 hover:underline">Ma bibliothèque</a>.
            <?php endif; ?>
        </p>
        <a href="/dashboard/" class="inline-block px-6 py-3 bg-yellow-400 text-black font-bold rounded">
            Ouvrir Ma bibliothèque
        </a>
        <p class="mt-6 text-xs text-gray-600">
            Télécharge le deck .zip → installe-le dans FlashDev (menu explore deck).
        </p>
    </div>
</body>
</html>
