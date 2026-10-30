<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$productSlug = trim((string) ($_GET['product'] ?? $_POST['product'] ?? mha_formation_product_slug()));
$offer = mha_shop_offer($productSlug);

if ($offer === null || ($offer['status'] ?? '') !== 'available') {
    http_response_code(404);
    exit('Offre introuvable ou pas encore en vente.');
}

$redirectAfter = '/boutique/acheter.php?product=' . rawurlencode($productSlug);
$user = mha_require_login($redirectAfter);

$prelaunchBypass = mha_formation_shop_bypass_active();
$checkoutUrl = mha_shop_checkout_url($productSlug);
$merciUrl = mha_shop_merci_url($productSlug);
$owned = mha_can_access_product($productSlug) && !mha_has_role('admin', 'tester');
$promoEnabled = mha_tester_promo_enabled();

if ($owned) {
    header('Location: /boutique/merci.php?product=' . rawurlencode($productSlug) . '&already=1', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = trim((string) ($_POST['action'] ?? ''));
    if ($action === 'redeem_promo' && $promoEnabled) {
        $code = (string) ($_POST['promo_code'] ?? '');
        $result = mha_redeem_tester_promo_code($code, $user);
        if (!$result['ok']) {
            $err = (string) ($result['error'] ?? 'invalid');
            header('Location: /boutique/acheter.php?product=' . rawurlencode($productSlug) . '&promo_error=' . rawurlencode($err), true, 303);
            exit;
        }
        $grantSlug = (string) ($result['product_slug'] ?? $productSlug);
        if ($grantSlug !== $productSlug) {
            header('Location: /boutique/acheter.php?product=' . rawurlencode($grantSlug) . '&promo_error=product_mismatch', true, 303);
            exit;
        }
        header('Location: ' . mha_deck_download_page_url($grantSlug) . '&promo=ok', true, 303);
        exit;
    }
    if ($action === 'prelaunch_confirm' && $prelaunchBypass) {
        if (!mha_shop_fulfill_purchase((string) $user['email'], $productSlug)) {
            header('Location: /boutique/acheter.php?product=' . rawurlencode($productSlug) . '&error=1', true, 303);
            exit;
        }
        header('Location: /boutique/merci.php?product=' . rawurlencode($productSlug) . '&confirmed=1', true, 303);
        exit;
    }
    if ($action === 'systeme_checkout' && $checkoutUrl !== null && !$prelaunchBypass) {
        $separator = str_contains($checkoutUrl, '?') ? '&' : '?';
        header('Location: ' . $checkoutUrl . $separator . 'success_url=' . rawurlencode($merciUrl), true, 303);
        exit;
    }
}

$displayPrice = (string) ($offer['price_label'] ?? '1 €');
$showError = isset($_GET['error']);
$promoError = (string) ($_GET['promo_error'] ?? '');
$promoErrorMessage = match ($promoError) {
    'disabled' => 'Les codes testeurs ne sont pas activés sur ce serveur (config.php).',
    'product_mismatch' => 'Ce code correspond à une autre offre — ouvre la bonne fiche produit.',
    'product_unavailable' => 'Produit non disponible pour ce code.',
    'invalid' => 'Code promo invalide.',
    default => $promoError !== '' ? 'Impossible d’appliquer le code.' : '',
};
$title = (string) ($offer['title'] ?? 'Phase 1');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande — <?= mha_escape($title) ?> — MadHackAdemy</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/nav-site.css?v=11">
    <style>
        .text-nintendo-yellow { color: #FBDD08; }
        .shop-card { border: 2px solid #374151; border-radius: 0.75rem; background: #111827; }
    </style>
</head>
<body class="bg-black text-white font-mono min-h-screen">

    <?php
    $navCurrent = 'boutique';
    require __DIR__ . '/../partials/nav-fr.php';
    $checkoutBackHref = '/boutique/index.php';
    $checkoutBackLabel = '← Boutique';
    $checkoutStepLabel = 'Étape 1 · Commande';
    require __DIR__ . '/../partials/nav-checkout-bar.php';
    ?>

    <main class="pt-32 pb-16 px-4 max-w-3xl mx-auto">
        <?php if ($showError): ?>
            <p class="mb-6 text-sm text-red-400 border border-red-900 rounded-lg px-4 py-3 bg-red-950/30">
                Impossible de finaliser — réessaie ou contacte le support.
            </p>
        <?php endif; ?>
        <?php if ($promoErrorMessage !== ''): ?>
            <p class="mb-6 text-sm text-red-400 border border-red-900 rounded-lg px-4 py-3 bg-red-950/30">
                <?= mha_escape($promoErrorMessage) ?>
            </p>
        <?php endif; ?>

        <header class="mb-8">
            <p class="text-xs uppercase tracking-widest text-gray-500 mb-2">GameDevReady · Offre 1</p>
            <h1 class="text-2xl md:text-3xl font-bold text-nintendo-yellow mb-2"><?= mha_escape($title) ?></h1>
            <p class="text-gray-400 text-sm"><?= mha_escape((string) ($offer['subtitle'] ?? '')) ?></p>
        </header>

        <div class="shop-card p-6 md:p-8 mb-8">
            <h2 class="text-sm font-bold text-gray-300 mb-4">Récapitulatif</h2>
            <ul class="text-xs text-gray-500 space-y-2 mb-6">
                <?php foreach (($offer['includes'] ?? []) as $line): ?>
                    <li>✓ <?= mha_escape((string) $line) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="flex justify-between items-center border-t border-gray-700 pt-4">
                <span class="text-gray-400 text-sm">Total</span>
                <span class="text-2xl font-bold text-nintendo-yellow"><?= str_replace(' ', '&nbsp;', mha_escape($displayPrice)) ?></span>
            </div>
            <p class="text-xs text-gray-600 mt-3">
                Compte : <?= mha_escape((string) $user['email']) ?>
            </p>
        </div>

        <?php if ($promoEnabled): ?>
            <div class="shop-card p-6 md:p-8 border-blue-800/50 mb-8">
                <h2 class="text-sm font-bold text-blue-300 mb-2">Code promo testeur / beta</h2>
                <p class="text-gray-400 text-sm mb-4">
                    Contourne le paiement Systeme.io pour l’instant : active la formation sur
                    <strong class="text-gray-200"><?= mha_escape((string) $user['email']) ?></strong>
                    puis accès au téléchargement du deck.
                </p>
                <form method="post" action="/boutique/acheter.php" class="space-y-4 max-w-md">
                    <input type="hidden" name="product" value="<?= mha_escape($productSlug) ?>">
                    <input type="hidden" name="action" value="redeem_promo">
                    <label class="block text-xs text-gray-500" for="promo_code">Code promo</label>
                    <input id="promo_code" name="promo_code" type="text" required autocomplete="off"
                           class="w-full px-4 py-3 bg-black border border-gray-700 rounded text-white uppercase tracking-widest"
                           placeholder="GDR-TEST-…">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 btn-shop-cta rounded">
                        Valider le code et télécharger le deck →
                    </button>
                </form>
                <p class="text-xs text-gray-600 mt-4">
                    Alternative :
                    <a href="<?= mha_escape(mha_deck_download_page_url($productSlug)) ?>" class="text-yellow-600 hover:underline">page deck</a>
                    · codes dans <code class="text-gray-500">config.php</code> (non commité).
                </p>
            </div>
        <?php endif; ?>

        <?php if ($prelaunchBypass): ?>
            <div class="shop-card p-6 md:p-8 border-green-800/60 mb-6">
                <h2 class="text-sm font-bold text-green-400 mb-2">Pré-lancement — sans paiement Systeme.io</h2>
                <p class="text-gray-400 text-sm mb-6">
                    Prix affiché <strong><?= mha_escape($displayPrice) ?></strong> — en pré-lancement, tu confirmes la commande sur le site
                    <strong>sans paiement Systeme.io</strong> (passerelle activée après création de l’entreprise).
                    La page suivante te donne le lien de téléchargement du deck
                    et l’accès aux guides — comme un client, le temps que le funnel de vente soit branché.
                </p>
                <form method="post" action="/boutique/acheter.php">
                    <input type="hidden" name="product" value="<?= mha_escape($productSlug) ?>">
                    <input type="hidden" name="action" value="prelaunch_confirm">
                    <button type="submit" class="w-full md:w-auto px-8 py-4 btn-shop-cta rounded">
                        Confirmer et accéder au deck →
                    </button>
                </form>
            </div>
            <p class="text-xs text-gray-600">
                URL funnel Systeme.io (merci, plus tard) :
                <code class="text-gray-500 break-all"><?= mha_escape($merciUrl) ?></code>
            </p>
        <?php elseif ($checkoutUrl !== null): ?>
            <form method="post" action="/boutique/acheter.php">
                <input type="hidden" name="product" value="<?= mha_escape($productSlug) ?>">
                <input type="hidden" name="action" value="systeme_checkout">
                <button type="submit" class="w-full md:w-auto px-8 py-4 btn-shop-cta rounded">
                    Payer <?= str_replace(' ', '&nbsp;', mha_escape($displayPrice)) ?> via Systeme.io →
                </button>
            </form>
            <p class="text-xs text-gray-600 mt-4">
                Retour après paiement :
                <a href="<?= mha_escape($merciUrl) ?>" class="text-yellow-600 hover:underline break-all"><?= mha_escape($merciUrl) ?></a>
            </p>
        <?php else: ?>
            <div class="shop-card p-6 md:p-8 border-amber-800/50">
                <h2 class="text-sm font-bold text-amber-400 mb-2">Paiement en préparation</h2>
                <p class="text-gray-400 text-sm mb-4">
                    Le funnel Systeme.io n’est pas encore relié (<code class="text-gray-500">shop_checkout_urls</code>).
                    Tu peux déjà configurer chez Systeme.io une page de remerciement qui redirige vers :
                </p>
                <p class="text-xs bg-black border border-gray-800 rounded p-3 break-all text-yellow-600 mb-4">
                    <?= mha_escape($merciUrl) ?>
                </p>
                <p class="text-xs text-gray-600">
                    L’accès deck sera débloqué par webhook <em>SALE_NEW</em> une fois la vente active — ou active le mode
                    pré-lancement dans <code class="text-gray-500">config.php</code>.
                    <?php if (!$promoEnabled): ?>
                        Tu peux aussi activer <code class="text-gray-500">tester_promo_enabled</code> + un code promo testeur.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </main>

    <script src="/nav-mobile.js?v=8" defer></script>
    <script src="/nav-session.js?v=8" defer></script>
</body>
</html>
