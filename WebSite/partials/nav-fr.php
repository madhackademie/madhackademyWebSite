<?php
declare(strict_types=1);

/**
 * Nav vitrine FR — ordre unique sur tout le site formation / boutique.
 *
 * @var string $navCurrent accueil|flashdev|telecharger|formation|gamedevready|boutique
 */
$navCurrent = $navCurrent ?? '';
$navEnHref = $navEnHref ?? '/training-center.html';

$navLink = static function (string $key, string $href, string $label) use ($navCurrent): string {
    $current = $navCurrent === $key ? ' aria-current="page"' : '';

    return '<a href="' . mha_escape($href) . '" class="nav-item"' . $current . '>' . mha_escape($label) . '</a>';
};
?>
<nav class="fixed top-0 w-full z-50 bg-black/95 backdrop-blur-sm border-b-2 border-gray-800">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-3">
        <a href="/" class="text-xl md:text-2xl font-bold gradient-text hover:scale-105 transition-transform shrink-0">
            MadHackAdemy
        </a>

        <div class="nav-site-actions flex items-center gap-3">
            <div class="nav-site-links hidden md:flex flex-wrap justify-end gap-3 md:gap-5 text-sm md:text-base items-center">
                <?= $navLink('accueil', '/', 'Accueil') ?>
                <?= $navLink('flashdev', '/flashdev.html', 'FlashDev') ?>
                <?= $navLink('telecharger', '/telecharger.html', 'Téléchargement') ?>
                <?= $navLink('formation', '/centre-formation.html', 'Formation') ?>
                <?= $navLink('gamedevready', '/gamedevready.html', 'GameDevReady') ?>
                <?= $navLink('boutique', '/boutique/index.php', 'Boutique') ?>
                <div class="flex items-center gap-1.5 text-xs font-bold border border-gray-700 rounded px-2 py-1" role="navigation" aria-label="Langue">
                    <span class="text-white" aria-current="page">FR</span>
                    <span class="text-gray-600" aria-hidden="true">|</span>
                    <a href="<?= mha_escape($navEnHref) ?>" class="text-gray-400 hover:text-white transition-colors" hreflang="en" lang="en">EN</a>
                </div>
            </div>
            <span data-nav-session-host class="nav-session-host"></span>
            <button
                id="nav-toggle"
                type="button"
                class="nav-toggle md:hidden"
                aria-label="Ouvrir le menu"
                aria-expanded="false"
                aria-controls="nav-mobile-menu"
            >
                <span class="nav-bar"></span>
                <span class="nav-bar"></span>
                <span class="nav-bar"></span>
            </button>
        </div>
    </div>

    <div id="nav-mobile-menu" class="nav-mobile-panel hidden" data-nav-mobile-panel>
        <div class="nav-site-links nav-site-mobile">
            <?= $navLink('accueil', '/', 'Accueil') ?>
            <?= $navLink('flashdev', '/flashdev.html', 'FlashDev') ?>
            <?= $navLink('telecharger', '/telecharger.html', 'Téléchargement') ?>
            <?= $navLink('formation', '/centre-formation.html', 'Formation') ?>
            <?= $navLink('gamedevready', '/gamedevready.html', 'GameDevReady') ?>
            <?= $navLink('boutique', '/boutique/index.php', 'Boutique') ?>
            <div class="flex items-center gap-1.5 text-xs font-bold border border-gray-700 rounded px-2 py-1 w-fit mt-1" aria-label="Langue">
                <span class="text-white" aria-current="page">FR</span>
                <span class="text-gray-600" aria-hidden="true">|</span>
                <a href="<?= mha_escape($navEnHref) ?>" class="text-gray-400 hover:text-white transition-colors" hreflang="en" lang="en">EN</a>
            </div>
        </div>
    </div>
</nav>
