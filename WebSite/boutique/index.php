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
    <link rel="stylesheet" href="/nav-site.css?v=11">
    <style>
        .nintendo-red { background: #E60012; }
        .nintendo-yellow { background: #FBDD08; }
        .nintendo-blue { background: #1B8CE4; }
        .text-nintendo-red { color: #E60012; }
        .text-nintendo-yellow { color: #FBDD08; }
        .text-nintendo-blue { color: #1B8CE4; }
        .gradient-text {
            background: linear-gradient(90deg, #E60012, #FBDD08, #05C31C, #1B8CE4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .shop-card { border: 2px solid #374151; border-radius: 0.75rem; background: #111827; }
    </style>
</head>
<body class="bg-black text-white font-mono min-h-screen">

    <?php $navCurrent = 'boutique'; require __DIR__ . '/../partials/nav-fr.php'; ?>

    <main class="pt-24 pb-16 px-4 max-w-6xl mx-auto">
        <p class="text-center text-xs text-gray-600 max-w-2xl mx-auto mb-8 leading-relaxed">
            Même grille d’offres que <a href="/centre-formation.html#boutique" class="text-gray-500 hover:text-yellow-400 underline underline-offset-2">Formation → La Boutique</a>
            et le <a href="/gamedevready.html" class="text-gray-500 hover:text-yellow-400 underline underline-offset-2">hub GameDevReady</a>.
            Ici : tunnel de commande (récap, confirmation, zip). Refonte vitrine prévue après validation du parcours élève.
        </p>
        <header class="text-center mb-12">
            <p class="text-sm text-gray-500 uppercase tracking-widest mb-2">Formation → Boutique</p>
            <h1 class="text-4xl md:text-5xl font-bold gradient-text mb-4">GameDevReady</h1>
            <p class="text-gray-400 max-w-2xl mx-auto text-sm md:text-base">
                Decks GameDevReady pour FlashDev : <strong class="text-gray-200">une phase = un achat = un zip + les guides</strong>.
                Commence par la Phase 1 <strong class="text-nintendo-yellow">BootCampC++ bases</strong> à <strong class="text-nintendo-yellow">1&nbsp;€</strong>.
                <?php if ($prelaunchBypass): ?>
                    Commande sur le site en pré-lancement (sans Systeme.io pour l’instant).
                <?php else: ?>
                    Paiement et facturation via <strong class="text-gray-300">Systeme.io</strong>.
                <?php endif; ?>
            </p>
        </header>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($offers as $offer): ?>
                <?php require __DIR__ . '/partials/offer-card.php'; ?>
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
