<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$productSlug = trim((string) ($_GET['product'] ?? mha_formation_product_slug()));
$user = mha_require_login('/boutique/merci.php?product=' . rawurlencode($productSlug));

$prelaunchBypass = mha_formation_shop_bypass_active();
$owned = mha_can_access_product($productSlug);
$offer = mha_shop_offer($productSlug);
$title = $offer['title'] ?? 'Phase 1 · Bootcamp C++';
$confirmed = isset($_GET['confirmed']);
$already = isset($_GET['already']);

$library = $owned ? mha_user_library($user) : [];
$deckDownloadUrl = null;
$flashdevUrl = null;
foreach ($library as $item) {
    if (($item['slug'] ?? '') === $productSlug && !empty($item['deck_download_url'])) {
        $deckDownloadUrl = (string) $item['deck_download_url'];
    }
    if (($item['kind'] ?? '') === 'soft' && !empty($item['flashdev_download_url'])) {
        $flashdevUrl = (string) $item['flashdev_download_url'];
    }
}
$hubUrl = (string) ($offer['hub_url'] ?? '/gamedevready-bases-cpp.html');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci — MadHackAdemy</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/nav-site.css?v=11">
    <style>
        .text-nintendo-yellow { color: #FBDD08; }
        .text-nintendo-green { color: #05C31C; }
        .shop-card { border: 2px solid #374151; border-radius: 0.75rem; background: #111827; }
    </style>
</head>
<body class="bg-black text-white font-mono min-h-screen">

    <?php
    $navCurrent = 'boutique';
    require __DIR__ . '/../partials/nav-fr.php';
    $checkoutBackHref = '/boutique/index.php';
    $checkoutBackLabel = '← Boutique';
    $checkoutStepLabel = 'Étape 2 · Merci';
    require __DIR__ . '/../partials/nav-checkout-bar.php';
    ?>

    <main class="pt-32 pb-16 px-4 max-w-2xl mx-auto text-center">
        <div class="shop-card p-8 md:p-10">
            <p class="text-4xl mb-4" aria-hidden="true">✓</p>
            <h1 class="text-2xl md:text-3xl font-bold text-nintendo-green mb-3">
                <?= $already ? 'Tu as déjà accès' : 'Merci !' ?>
            </h1>
            <p class="text-gray-300 text-sm mb-8 max-w-md mx-auto">
                <?php if ($prelaunchBypass && ($confirmed || $owned)): ?>
                    Commande pré-lancement confirmée pour
                    <strong><?= mha_escape((string) $user['email']) ?></strong>.
                    Télécharge le deck ci-dessous puis installe-le dans FlashDev.
                <?php elseif ($owned): ?>
                    Ton accès <strong><?= mha_escape((string) $title) ?></strong> est actif.
                <?php else: ?>
                    Paiement Systeme.io en cours de confirmation — actualise dans quelques secondes
                    ou ouvre <a href="/dashboard/" class="text-yellow-400 hover:underline">Ma bibliothèque</a>.
                <?php endif; ?>
            </p>

            <div class="flex flex-col gap-3 items-stretch max-w-sm mx-auto">
                <?php if ($deckDownloadUrl): ?>
                    <a href="<?= mha_escape($deckDownloadUrl) ?>"
                       class="px-6 py-4 btn-shop-cta rounded">
                        Télécharger le deck (.zip)
                    </a>
                <?php elseif ($owned): ?>
                    <a href="<?= mha_escape(mha_deck_download_page_url($productSlug)) ?>"
                       class="px-6 py-4 btn-shop-cta rounded">
                        Télécharger le deck (.zip)
                    </a>
                <?php endif; ?>

                <?php if ($flashdevUrl): ?>
                    <a href="<?= mha_escape($flashdevUrl) ?>"
                       class="px-6 py-3 border border-gray-600 text-gray-200 font-bold rounded text-sm hover:border-yellow-500">
                        Télécharger FlashDev (logiciel)
                    </a>
                <?php else: ?>
                    <a href="/telecharger.html"
                       class="px-6 py-3 border border-gray-600 text-gray-200 font-bold rounded text-sm hover:border-yellow-500">
                        Installer FlashDev (gratuit)
                    </a>
                <?php endif; ?>

                <a href="<?= mha_escape($hubUrl) ?>"
                   class="px-6 py-3 text-sm text-gray-400 hover:text-white">
                    Voir le parcours deck sur le site →
                </a>
                <a href="/dashboard/" class="px-6 py-3 bg-yellow-400 text-black font-bold rounded">
                    Ma bibliothèque
                </a>
            </div>

            <ol class="text-left text-xs text-gray-500 mt-10 space-y-2 max-w-md mx-auto">
                <li>1. Installe FlashDev si ce n’est pas déjà fait</li>
                <li>2. Télécharge le zip du deck</li>
                <li>3. Dans FlashDev : <strong>explore deck</strong> → dossier du deck → Install</li>
                <li>4. Révise les 7 cartes ; guides via login sur le site</li>
            </ol>
        </div>

        <?php if (!$prelaunchBypass && !$owned): ?>
            <p class="text-xs text-gray-600 mt-6">
                Accès débloqué par webhook Systeme.io (<em>SALE_NEW</em>) — pas par cette page seule.
            </p>
        <?php endif; ?>
    </main>
    <script src="/nav-mobile.js?v=8" defer></script>
    <script src="/nav-session.js?v=8" defer></script>
</body>
</html>
