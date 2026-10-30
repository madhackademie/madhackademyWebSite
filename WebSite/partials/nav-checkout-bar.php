<?php
declare(strict_types=1);

/** @var string $checkoutBackHref */
/** @var string $checkoutBackLabel */
/** @var string $checkoutStepLabel */
?>
<div class="fixed top-16 left-0 right-0 z-40 bg-black/95 backdrop-blur-sm border-b border-gray-800">
    <div class="max-w-3xl mx-auto px-4 py-2.5 flex justify-between items-center gap-3 text-sm">
        <a href="<?= mha_escape($checkoutBackHref) ?>" class="text-gray-400 hover:text-white shrink-0">
            <?= mha_escape($checkoutBackLabel) ?>
        </a>
        <span class="text-gray-500 text-xs md:text-sm text-right"><?= mha_escape($checkoutStepLabel) ?></span>
    </div>
</div>
