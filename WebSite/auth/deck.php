<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$formationSlug = mha_formation_product_slug();
$productSlug = trim((string) ($_GET['product'] ?? $_POST['product'] ?? $formationSlug));
if ($productSlug === '') {
    $productSlug = $formationSlug;
}

$pageUrl = mha_deck_download_page_url($productSlug);
$redirectAfter = $pageUrl;

$user = mha_current_user();
$promoEnabled = mha_tester_promo_enabled();
$flash = '';
$error = '';

if ($user && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = trim((string) ($_POST['action'] ?? ''));
    if ($action === 'redeem_promo') {
        $code = (string) ($_POST['promo_code'] ?? '');
        $result = mha_redeem_tester_promo_code($code, $user);
        if ($result['ok']) {
            $slug = (string) ($result['product_slug'] ?? $productSlug);
            header('Location: ' . mha_deck_download_page_url($slug) . '&promo=ok', true, 303);
            exit;
        }
        $error = match ($result['error'] ?? '') {
            'disabled' => 'Les codes testeurs ne sont pas activés sur ce serveur.',
            'product_unavailable' => 'Ce produit n’est pas encore disponible.',
            default => 'Code invalide — vérifie la casse ou demande un nouveau code à MadHackAdemy.',
        };
    }
}

if (!$user) {
    header('Location: /auth/login.php?redirect=' . rawurlencode($redirectAfter), true, 303);
    exit;
}

$user = mha_require_login($redirectAfter);

if (isset($_GET['promo']) && $_GET['promo'] === 'ok') {
    $flash = 'Accès deck activé sur ton compte. Tu peux télécharger le zip ci-dessous.';
}

$offer = mha_shop_offer($productSlug);
$title = (string) ($offer['title'] ?? $offer['short_title'] ?? 'BootCampC++ bases');
$owned = mha_can_access_product($productSlug);
$zipPath = mha_resolve_deck_zip_path($productSlug);
$canDownload = $owned && $zipPath !== null;
$downloadUrl = '/auth/download-deck.php?slug=' . rawurlencode($productSlug);
$hubUrl = (string) ($offer['hub_url'] ?? '/gamedevready-bases-cpp.html');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Télécharger le deck — <?= mha_escape($title) ?> — MadHackAdemy</title>
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
    $checkoutBackHref = '/dashboard/';
    $checkoutBackLabel = '← Ma bibliothèque';
    $checkoutStepLabel = 'Téléchargement deck';
    require __DIR__ . '/../partials/nav-checkout-bar.php';
    ?>

    <main class="pt-32 pb-16 px-4 max-w-2xl mx-auto">
        <header class="mb-8">
            <p class="text-xs uppercase tracking-widest text-gray-500 mb-2">GameDevReady · Phase 1</p>
            <h1 class="text-2xl md:text-3xl font-bold text-nintendo-yellow mb-2"><?= mha_escape($title) ?></h1>
            <p class="text-gray-400 text-sm">
                Compte : <strong class="text-gray-300"><?= mha_escape((string) $user['email']) ?></strong>
            </p>
        </header>

        <?php if ($flash !== ''): ?>
            <p class="mb-6 text-sm text-green-400 border border-green-900 rounded-lg px-4 py-3 bg-green-950/30">
                <?= mha_escape($flash) ?>
            </p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="mb-6 text-sm text-red-400 border border-red-900 rounded-lg px-4 py-3 bg-red-950/30">
                <?= mha_escape($error) ?>
            </p>
        <?php endif; ?>

        <?php if ($canDownload): ?>
            <div class="shop-card p-8 mb-8 border-green-700/50">
                <h2 class="text-lg font-bold text-nintendo-green mb-3">Téléchargement prêt</h2>
                <p class="text-gray-400 text-sm mb-6">
                    Installe le zip dans FlashDev (Explore deck → Install). Puis ouvre les guides depuis
                    <a href="<?= mha_escape($hubUrl) ?>" class="text-yellow-400 hover:underline">le parcours deck</a>.
                </p>
                <a href="<?= mha_escape($downloadUrl) ?>"
                   class="inline-block w-full text-center px-8 py-4 btn-shop-cta rounded">
                    Télécharger le deck (.zip)
                </a>
                <p class="text-xs text-gray-600 mt-4 text-center">
                    <a href="/telecharger.html" class="text-gray-500 hover:text-white">Installer FlashDev</a>
                    ·
                    <a href="/dashboard/" class="text-gray-500 hover:text-white">Ma bibliothèque</a>
                </p>
            </div>
        <?php elseif ($owned && $zipPath === null): ?>
            <div class="shop-card p-8 mb-8 border-amber-800/50">
                <h2 class="text-lg font-bold text-amber-400 mb-3">Accès OK — fichier en préparation</h2>
                <p class="text-gray-400 text-sm mb-4">
                    Ton compte a bien le produit, mais le zip n’est pas encore publié sur le serveur
                    (<code class="text-gray-500">deck_download_files</code>).
                </p>
                <a href="/dashboard/" class="text-yellow-400 hover:underline text-sm">← Ma bibliothèque</a>
            </div>
        <?php else: ?>
            <div class="shop-card p-8 mb-8">
                <h2 class="text-lg font-bold text-nintendo-yellow mb-3">Pas encore d’accès deck</h2>
                <p class="text-gray-400 text-sm mb-6">
                    Achète la Phase 1 dans la boutique, ou saisis un <strong class="text-gray-200">code testeur</strong>
                    si tu participes à la beta.
                </p>

                <?php if ($promoEnabled): ?>
                    <form method="post" action="<?= mha_escape($pageUrl) ?>" class="space-y-4">
                        <input type="hidden" name="product" value="<?= mha_escape($productSlug) ?>">
                        <input type="hidden" name="action" value="redeem_promo">
                        <label class="block text-xs text-gray-500" for="promo_code">Code promo testeur</label>
                        <input id="promo_code" name="promo_code" type="text" required autocomplete="off"
                               class="w-full px-4 py-3 bg-black border border-gray-700 rounded text-white uppercase tracking-widest"
                               placeholder="GDR-TEST-…">
                        <button type="submit" class="w-full px-6 py-3 btn-shop-cta rounded">
                            Valider le code
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-sm text-gray-600">
                        Les codes testeurs ne sont pas activés ici. Passe par la boutique ou contacte le support.
                    </p>
                <?php endif; ?>

                <div class="mt-8 pt-6 border-t border-gray-800 flex flex-wrap gap-3">
                    <a href="/boutique/acheter.php?product=<?= rawurlencode($productSlug) ?>"
                       class="px-4 py-2 btn-shop-cta rounded text-sm">
                        Boutique · Phase 1
                    </a>
                    <a href="/telecharger.html" class="px-4 py-2 border border-gray-600 rounded text-sm text-gray-300 hover:border-yellow-500">
                        FlashDev gratuit
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="/nav-mobile.js?v=8" defer></script>
    <script src="/nav-session.js?v=8" defer></script>
</body>
</html>
