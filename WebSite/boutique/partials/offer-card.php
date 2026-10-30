<?php
declare(strict_types=1);

/** @var array<string, mixed> $offer */
/** @var string $formationSlug */
/** @var bool $prelaunchBypass */
/** @var array<string, mixed>|null $user */

$slug = (string) ($offer['product_slug'] ?? '');
$available = ($offer['status'] ?? '') === 'available';
$owned = $user && mha_can_access_product($slug);
$phase = (int) ($offer['phase'] ?? 0);
$vitrine = is_array($offer['vitrine'] ?? null) ? $offer['vitrine'] : [];
$accent = (string) ($vitrine['accent'] ?? 'gray');
$cardPhaseClass = match ($accent) {
    'red' => 'shop-offer-card--phase1',
    'yellow' => 'shop-offer-card--phase2',
    'blue' => 'shop-offer-card--phase3',
    default => '',
};
$badgeClass = match ($accent) {
    'red' => 'shop-offer-badge--red',
    'yellow' => 'shop-offer-badge--yellow',
    'blue' => 'shop-offer-badge--blue',
    default => '',
};
$cardWrap = 'bg-gray-800 p-8 rounded-lg border-2 hover:bg-gray-700 transition-all relative ' . $cardPhaseClass;
if (!$available) {
    $cardWrap .= ' opacity-70';
}
$titleClass = (string) ($vitrine['title_class'] ?? ($available ? 'text-nintendo-yellow' : 'text-gray-400'));
$emoji = (string) ($vitrine['emoji'] ?? '');
$badge = (string) ($vitrine['badge'] ?? ('Offre ' . $phase));
$tagline = (string) ($vitrine['tagline'] ?? '');
$body = (string) ($vitrine['body'] ?? (string) ($offer['subtitle'] ?? ''));
$highlight = (string) ($vitrine['highlight'] ?? '');
$hubUrl = (string) ($offer['hub_url'] ?? '/gamedevready-bases-cpp.html');
$showHub = !empty($vitrine['hub_link']) && $available;
$priceLabel = (string) ($offer['price_label'] ?? '');
$priceDisplay = str_replace(' ', '&nbsp;', mha_escape($priceLabel));
?>
<div class="<?= mha_escape(trim($cardWrap)) ?>">
    <?php if ($available && $badge !== ''): ?>
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-10">
            <span class="px-4 py-1 shop-offer-badge <?= mha_escape($badgeClass) ?> text-xs font-bold rounded whitespace-nowrap">
                <?= mha_escape($badge) ?>
            </span>
        </div>
    <?php elseif (!$available && $badge !== ''): ?>
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-10">
            <span class="px-4 py-1 bg-gray-700 text-gray-300 text-xs font-bold rounded whitespace-nowrap">
                <?= mha_escape($badge) ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if ($emoji !== ''): ?>
        <div class="text-4xl mb-2 mt-3" aria-hidden="true"><?= $emoji ?></div>
    <?php endif; ?>

    <?php if ($tagline !== ''): ?>
        <p class="text-xs font-bold uppercase tracking-wide mb-2 <?= $available ? 'text-gray-400' : 'text-gray-500' ?>">
            <?= mha_escape($tagline) ?>
        </p>
    <?php endif; ?>

    <h2 class="text-xl font-bold mb-3 <?= mha_escape($titleClass) ?>">
        <?= mha_escape((string) ($offer['short_title'] ?? $offer['title'] ?? '')) ?>
    </h2>

    <p class="text-gray-300 text-sm leading-relaxed mb-4">
        <?= mha_escape($body) ?>
        <?php if ($highlight !== ''): ?>
            <strong class="text-nintendo-yellow"> <?= mha_escape($highlight) ?></strong>
        <?php endif; ?>
        <?php if ($showHub): ?>
            <a href="<?= mha_escape($hubUrl) ?>"
               class="text-red-400 hover:text-red-300 font-bold underline underline-offset-2 block mt-2">
                Voir le parcours deck →
            </a>
        <?php endif; ?>
    </p>

    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-700">
        <span class="text-2xl font-bold <?= $available ? 'text-nintendo-yellow' : 'text-gray-600' ?>"><?= $priceDisplay ?></span>
        <?php if ($available && $owned): ?>
            <a href="/dashboard/" class="px-4 py-2 bg-green-700 hover:bg-green-600 text-white font-bold rounded text-sm">
                Ma bibliothèque
            </a>
        <?php elseif ($available): ?>
            <a href="/boutique/acheter.php?product=<?= rawurlencode($slug) ?>"
               class="px-4 py-2 btn-shop-cta rounded text-sm whitespace-nowrap">
                <?= ($prelaunchBypass && $slug === $formationSlug) ? 'Commander' : 'Acheter' ?> · <?= $priceDisplay ?>
            </a>
        <?php else: ?>
            <span class="text-sm text-gray-600 font-bold">Bientôt</span>
        <?php endif; ?>
    </div>
</div>
