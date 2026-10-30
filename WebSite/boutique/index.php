<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$user = mha_current_user();
$offers = mha_shop_offers();
$formationSlug = mha_formation_product_slug();
$prelaunchBypass = mha_formation_shop_bypass_active();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique GameDevReady — MadHackAdemy</title>
    <meta name="description" content="Achetez le deck FlashDev GameDevReady phase par phase — Bootcamp C++ Phase 1 à 1 €.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/nav-site.css?v=8">
    <style>
        .nintendo-red { background: #E60012; }
        .text-nintendo-red { color: #E60012; }
        .text-nintendo-yellow { color: #FBDD08; }
        .gradient-text {
            background: linear-gradient(90deg, #E60012, #FBDD08, #05C31C, #1B8CE4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .shop-card { border: 2px solid #374151; border-radius: 0.75rem; background: #111827; }
        .shop-card--live { border-color: #E60012; }
        .shop-card--soon { opacity: 0.65; }
    </style>
</head>
<body class="bg-black text-white font-mono min-h-screen">

    <nav class="fixed top-0 w-full bg-black bg-opacity-95 backdrop-blur-sm z-50 border-b-2 border-gray-800">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center gap-3">
            <a href="/" class="text-xl md:text-2xl font-bold gradient-text shrink-0">MadHackAdemy</a>
            <div class="nav-site-actions flex items-center gap-3">
                <div class="nav-site-links hidden md:flex flex-wrap justify-end gap-3 md:gap-5 items-center text-sm md:text-base">
                    <a href="/centre-formation.html" class="nav-item">Formation</a>
                    <a href="/gamedevready.html" class="nav-item">GameDevReady</a>
                    <a href="/boutique/" class="nav-item" aria-current="page">Boutique</a>
                    <a href="/telecharger.html" class="nav-item">FlashDev gratuit</a>
                </div>
                <span data-nav-session-host class="nav-session-host"></span>
            </div>
        </div>
    </nav>

    <main class="pt-28 pb-16 px-4 max-w-6xl mx-auto">
        <header class="text-center mb-12">
            <p class="text-sm text-gray-500 uppercase tracking-widest mb-2">Formation → Boutique</p>
            <h1 class="text-4xl md:text-5xl font-bold gradient-text mb-4">GameDevReady</h1>
            <p class="text-gray-400 max-w-2xl mx-auto text-sm md:text-base">
                <strong class="text-gray-200">Une phase = un achat = un deck FlashDev.</strong>
                <?php if ($prelaunchBypass): ?>
                    <span class="block mt-2 text-green-400/90">Pré-lancement : Phase 1 activable à <strong>0 €</strong> (sans Systeme.io) le temps de monter l’entreprise.</span>
                <?php else: ?>
                    Paiement et facturation via <strong class="text-gray-300">Systeme.io</strong> (TVA gérée côté plateforme).
                <?php endif; ?>
            </p>
        </header>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($offers as $offer): ?>
                <?php
                $slug = (string) $offer['product_slug'];
                $available = ($offer['status'] ?? '') === 'available';
                $owned = $user && mha_can_access_product($slug);
                $cardClass = $available ? 'shop-card shop-card--live' : 'shop-card shop-card--soon';
                ?>
                <article class="<?= mha_escape($cardClass) ?> overflow-hidden flex flex-col">
                    <div class="h-2 <?= $available ? 'nintendo-red' : 'bg-gray-700' ?>"></div>
                    <div class="p-8 flex flex-col flex-1">
                        <p class="text-xs uppercase tracking-widest text-gray-500 mb-2">
                            Offre <?= (int) ($offer['phase'] ?? 0) ?>
                            <?= $available ? '· disponible' : '· bientôt' ?>
                        </p>
                        <h2 class="text-xl font-bold mb-1 <?= $available ? 'text-nintendo-red' : 'text-gray-400' ?>">
                            <?= mha_escape((string) $offer['short_title']) ?>
                        </h2>
                        <p class="text-gray-400 text-sm mb-4"><?= mha_escape((string) $offer['subtitle']) ?></p>
                        <?php if (!empty($offer['includes'])): ?>
                            <ul class="text-xs text-gray-500 space-y-1 mb-6 flex-1">
                                <?php foreach ($offer['includes'] as $line): ?>
                                    <li>✓ <?= mha_escape((string) $line) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-xs text-gray-600 mb-6 flex-1">Non inclus dans l’achat Phase 1.</p>
                        <?php endif; ?>
                        <div class="flex items-center justify-between gap-3 mt-auto pt-4 border-t border-gray-800">
                            <span class="text-2xl font-bold text-nintendo-yellow">
                                <?php if ($available && $prelaunchBypass && $slug === $formationSlug): ?>
                                    0 €
                                <?php else: ?>
                                    <?= mha_escape((string) $offer['price_label']) ?>
                                <?php endif; ?>
                            </span>
                            <?php if ($available && $owned): ?>
                                <a href="/dashboard/" class="px-4 py-2 bg-green-700 hover:bg-green-600 text-white font-bold rounded text-sm">
                                    Ma bibliothèque
                                </a>
                            <?php elseif ($available): ?>
                                <a href="/boutique/acheter.php?product=<?= rawurlencode($slug) ?>"
                                   class="px-4 py-2 nintendo-red text-white font-bold rounded text-sm hover:opacity-90">
                                    <?= ($prelaunchBypass && $slug === $formationSlug) ? 'Activer →' : 'Acheter →' ?>
                                </a>
                            <?php else: ?>
                                <span class="text-sm text-gray-600 font-bold">Bientôt</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <section class="mt-16 shop-card p-8 text-center">
            <h2 class="text-lg font-bold text-nintendo-yellow mb-3"><?= $prelaunchBypass ? 'Avant d’activer' : 'Avant d’acheter' ?></h2>
            <ul class="text-sm text-gray-400 space-y-2 max-w-xl mx-auto text-left">
                <li>1. Crée ton compte FlashDev (gratuit) sur <a href="/telecharger.html" class="text-yellow-400 hover:underline">/telecharger.html</a></li>
                <li>2. Installe le logiciel FlashDev (socle vide)</li>
                <li>3. <?= $prelaunchBypass ? 'Active' : 'Achète' ?> la Phase 1 ici → deck + guides dans <strong>Ma bibliothèque</strong></li>
            </ul>
        </section>
    </main>

    <script src="/nav-mobile.js?v=8" defer></script>
    <script src="/nav-session.js?v=8" defer></script>
</body>
</html>
