<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$user = mha_require_login('/dashboard/');
$library = mha_user_library($user);
$formationSlug = mha_formation_product_slug();
$isStaff = mha_has_role('admin', 'tester');
$prelaunchBypass = mha_formation_shop_bypass_active();
$hasFormationProduct = mha_can_access_product($formationSlug);

$grantFlash = $_GET['granted'] ?? '';
$grantFor = (string) ($_GET['for'] ?? '');
$grantError = (string) ($_GET['grant_error'] ?? '');
$shopFlash = (string) ($_GET['shop'] ?? '');
$prelaunchFlash = (string) ($_GET['prelaunch'] ?? '');
$displayName = trim((string) ($user['display_name'] ?? ''));
if ($displayName === '') {
    $displayName = (string) $user['email'];
}
$firstName = explode('@', $displayName)[0];
$roleLabel = match ($user['role']) {
    'admin' => 'Administrateur',
    'tester' => 'Testeur',
    default => 'Élève',
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma bibliothèque — MadHackAdemy</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/nav-site.css?v=9">
    <style>
        .nintendo-red { background: #E60012; }
        .nintendo-yellow { background: #FBDD08; }
        .nintendo-green { background: #05C31C; }
        .nintendo-blue { background: #1B8CE4; }
        .text-nintendo-yellow { color: #FBDD08; }
        .text-nintendo-green { color: #05C31C; }
        .gradient-text {
            background: linear-gradient(90deg, #E60012, #FBDD08, #05C31C, #1B8CE4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .dash-card {
            background: linear-gradient(145deg, #111827 0%, #0a0a0a 100%);
            border: 2px solid #374151;
            border-radius: 0.75rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .dash-card:hover {
            border-color: #4b5563;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
        }
        .dash-btn-primary {
            background: #FBDD08;
            color: #111;
            font-weight: 700;
            transition: transform 0.12s ease, filter 0.12s ease;
        }
        .dash-btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); }
        .dash-btn-secondary {
            border: 2px solid #374151;
            color: #e5e7eb;
            font-weight: 700;
        }
        .dash-btn-secondary:hover { border-color: #05C31C; color: #05C31C; }
        .dash-btn-deck {
            border: 2px solid #05C31C;
            color: #05C31C;
            font-weight: 700;
        }
        .dash-btn-deck:hover { background: rgba(5, 195, 28, 0.12); }
        .dash-btn-muted {
            border: 2px dashed #4b5563;
            color: #6b7280;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-black text-white font-mono min-h-screen">

    <nav class="fixed top-0 w-full bg-black bg-opacity-95 backdrop-blur-sm z-50 border-b-2 border-gray-800">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center gap-3">
            <a href="/" class="text-xl md:text-2xl font-bold gradient-text shrink-0">MadHackAdemy</a>
            <div class="nav-site-actions flex items-center gap-3">
                <div class="nav-site-links hidden md:flex flex-wrap justify-end gap-3 md:gap-5 items-center text-sm md:text-base">
                    <a href="/" class="nav-item">Accueil</a>
                    <a href="/flashdev.html" class="nav-item">FlashDev</a>
                    <a href="/telecharger.html" class="nav-item">Téléchargement</a>
                    <a href="/gamedevready.html" class="nav-item">GameDevReady</a>
                    <a href="/dashboard/" class="nav-item" aria-current="page">Ma bibliothèque</a>
                </div>
                <span data-nav-session-host class="nav-session-host"></span>
            </div>
        </div>
    </nav>

    <main class="pt-28 pb-16 px-4 max-w-4xl mx-auto">
        <header class="mb-10">
            <p class="text-sm text-gray-500 uppercase tracking-widest mb-2">Espace élève</p>
            <h1 class="text-3xl md:text-4xl font-bold mb-2">
                Bon retour, <span class="text-nintendo-yellow"><?= mha_escape($firstName) ?></span>.
            </h1>
            <p class="text-gray-400 text-sm md:text-base"><?= mha_escape((string) $user['email']) ?> · <?= mha_escape($roleLabel) ?></p>
        </header>

        <?php if ($grantFlash === '1' && $grantFor !== ''): ?>
            <p class="mb-6 text-sm text-green-400 border border-green-900 bg-green-950/40 rounded-lg px-4 py-3">
                Accès accordé pour <?= mha_escape($grantFor) ?> — recharge la page si la bibliothèque de l’élève ne se met pas à jour.
            </p>
        <?php endif; ?>
        <?php if ($shopFlash === 'already'): ?>
            <p class="mb-6 text-sm text-amber-300 border border-amber-900 bg-amber-950/40 rounded-lg px-4 py-3">
                Tu as déjà la Phase 1 sur ce compte — retrouve le deck ci-dessous.
            </p>
        <?php endif; ?>
        <?php if ($prelaunchFlash === 'granted'): ?>
            <p class="mb-6 text-sm text-green-400 border border-green-900 bg-green-950/40 rounded-lg px-4 py-3">
                Phase 1 activée (pré-lancement, sans paiement). Télécharge le deck dans ta bibliothèque ci-dessous.
            </p>
        <?php elseif ($prelaunchFlash === 'already'): ?>
            <p class="mb-6 text-sm text-amber-300 border border-amber-900 bg-amber-950/40 rounded-lg px-4 py-3">
                Tu as déjà accès à la Phase 1 sur ce compte.
            </p>
        <?php elseif ($prelaunchFlash === 'error'): ?>
            <p class="mb-6 text-sm text-red-400 border border-red-900 bg-red-950/40 rounded-lg px-4 py-3">
                Impossible d’activer la Phase 1 — réessaie ou contacte le support.
            </p>
        <?php endif; ?>
        <?php if ($grantError !== ''): ?>
            <p class="mb-6 text-sm text-red-400 border border-red-900 bg-red-950/40 rounded-lg px-4 py-3">
                <?php
                echo match ($grantError) {
                    'email' => 'Email invalide.',
                    'product' => 'Produit invalide.',
                    'unknown' => 'Aucun compte actif avec cet email.',
                    default => 'Erreur lors de l’attribution.',
                };
                ?>
            </p>
        <?php endif; ?>

        <?php if ($prelaunchBypass && !$isStaff && !$hasFormationProduct): ?>
            <section class="mb-10 dash-card p-6 md:p-8 border-green-700/50">
                <h2 class="text-lg font-bold text-nintendo-green mb-2">Phase 1 · BootCampC++ bases · 1&nbsp;€</h2>
                <p class="text-gray-400 text-sm mb-4">
                    L’entreprise et le funnel Systeme.io ne sont pas encore en service. Tu peux activer le
                    <strong>Bootcamp C++</strong> comme un client (deck .zip + guides) — sans paiement.
                </p>
                <a href="/boutique/acheter.php?product=<?= rawurlencode($formationSlug) ?>"
                   class="inline-block px-6 py-3 rounded text-sm btn-shop-cta">
                    Commander · 1&nbsp;€
                </a>
                <p class="text-xs text-gray-600 mt-4">
                    Après mise en activité : achat 1 € via Systeme.io (TVA) — ce raccourci sera désactivé.
                </p>
            </section>
        <?php endif; ?>

        <?php if ($isStaff): ?>
            <section class="mb-10 dash-card p-6 md:p-8 border-red-900/50">
                <h2 class="text-lg font-bold text-red-400 mb-2">Admin — accorder un produit</h2>
                <p class="text-gray-400 text-sm mb-4">
                    Simule un achat ou un grant manuel (email doit déjà avoir un compte sur le site).
                </p>
                <form method="post" action="/auth/grant-product.php" class="space-y-4 max-w-md">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1" for="grant-email">Email élève</label>
                        <input id="grant-email" name="email" type="email" required
                               class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm"
                               placeholder="eleve@example.com">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1" for="grant-product">Produit</label>
                        <select id="grant-product" name="product_slug"
                                class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm">
                            <option value="<?= mha_escape($formationSlug) ?>" selected>
                                GameDevReady — Phase 1 Bases C++ (deck + guides)
                            </option>
                            <option value="<?= mha_escape(mha_flashdev_product_slug()) ?>">
                                FlashDev (soft gratuit)
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-3 rounded text-sm bg-red-900 hover:bg-red-700 text-red-100 font-bold">
                        Accorder l’accès
                    </button>
                </form>
                <p class="text-xs text-gray-600 mt-4">
                    En tant que <?= mha_escape($roleLabel) ?>, tu vois déjà tous les produits dans ta bibliothèque sans grant.
                </p>
            </section>
        <?php endif; ?>

        <section class="mb-10 bg-gray-900/80 border border-gray-800 rounded-xl p-6 md:p-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold">Notifications <span class="text-amber-500">(0)</span></h2>
            </div>
            <p class="text-gray-500 text-sm text-center py-6">
                Aucune notification pour l’instant.<br>
                <span class="text-gray-600">Reviens plus tard pour les nouveautés de parcours ou les réponses du support.</span>
            </p>
        </section>

        <section>
            <h2 class="text-2xl font-bold mb-2 flex items-center gap-2">
                <span class="inline-block w-3 h-3 nintendo-yellow rounded-sm"></span>
                Ma bibliothèque
            </h2>
            <p class="text-gray-400 text-sm mb-8">Decks et logiciels associés à ton compte — téléchargement, guides et pages deck.</p>

            <?php if ($library === []): ?>
                <div class="dash-card p-8 text-center">
                    <p class="text-gray-300 mb-4">Aucun produit activé pour ce compte.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="/telecharger.html" class="inline-block px-6 py-3 dash-btn-primary rounded">Créer mon accès FlashDev</a>
                        <a href="/boutique/index.php" class="inline-block px-6 py-3 btn-shop-cta rounded text-sm">Bootcamp C++ · 1&nbsp;€</a>
                        <?php if (mha_tester_promo_enabled()): ?>
                            <a href="<?= mha_escape(mha_deck_download_page_url($formationSlug)) ?>"
                               class="inline-block px-6 py-3 dash-btn-secondary rounded text-sm">
                                Code testeur · deck
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <ul class="space-y-6">
                    <?php foreach ($library as $item): ?>
                        <li class="dash-card overflow-hidden">
                            <div class="h-1.5 flex">
                                <?php if ($item['kind'] === 'soft'): ?>
                                    <span class="flex-1 nintendo-green"></span>
                                <?php else: ?>
                                    <span class="flex-1 nintendo-red"></span>
                                    <span class="flex-1 nintendo-yellow"></span>
                                    <span class="flex-1 nintendo-blue"></span>
                                <?php endif; ?>
                            </div>
                            <div class="p-6 md:p-8">
                                <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">
                                    <?= $item['kind'] === 'soft' ? 'Logiciel' : 'Formation' ?>
                                    <?php if ($item['phase']): ?> · Phase <?= (int) $item['phase'] ?><?php endif; ?>
                                </p>
                                <h3 class="text-xl md:text-2xl font-bold mb-2"><?= mha_escape((string) $item['title']) ?></h3>
                                <?php if ($item['subtitle'] !== ''): ?>
                                    <p class="text-gray-400 text-sm mb-6"><?= mha_escape((string) $item['subtitle']) ?></p>
                                <?php endif; ?>

                                <div class="flex flex-wrap gap-3">
                                    <?php if ($item['kind'] === 'soft' && $item['flashdev_download_url']): ?>
                                        <a href="<?= mha_escape((string) $item['flashdev_download_url']) ?>"
                                           class="px-5 py-3 rounded text-sm dash-btn-primary">
                                            Télécharger FlashDev
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($item['kind'] === 'formation'): ?>
                                        <a href="<?= mha_escape((string) $item['hub_url']) ?>"
                                           class="px-5 py-3 rounded text-sm dash-btn-secondary">
                                            Voir le deck (site)
                                        </a>
                                        <?php if ($item['guide_start_url']): ?>
                                            <a href="<?= mha_escape((string) $item['guide_start_url']) ?>"
                                               class="px-5 py-3 rounded text-sm dash-btn-secondary">
                                                Guide module 01
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($item['can_download_deck'] && $item['deck_download_url']): ?>
                                            <a href="<?= mha_escape((string) $item['deck_download_url']) ?>"
                                               class="px-5 py-3 rounded text-sm dash-btn-deck">
                                                Télécharger le deck (.zip)
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= mha_escape((string) ($item['deck_page_url'] ?? mha_deck_download_page_url((string) $item['slug']))) ?>"
                                               class="px-5 py-3 rounded text-sm dash-btn-deck">
                                                Télécharger le deck (.zip)
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if ($item['kind'] === 'formation' && !$item['can_download_deck']): ?>
                                    <p class="text-xs text-gray-600 mt-4">
                                        Après publication du zip sur le serveur, le bouton « Télécharger le deck » apparaîtra ici.
                                        Installe-le dans FlashDev via <strong>explore deck</strong>.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <p class="mt-12 text-center text-gray-600 text-xs">
            <a href="/auth/logout.php?redirect=/" class="hover:text-red-400 transition">Déconnexion</a>
        </p>
    </main>

    <script src="/nav-mobile.js?v=7" defer></script>
    <script src="/nav-session.js?v=7" defer></script>
</body>
</html>
