<?php
declare(strict_types=1);

/**
 * Pré-lancement : accès Phase 1 (deck + guides) sans funnel Systeme.io — 0 €.
 * Actif uniquement si formation_free_grant_until_systeme_io = true dans config.php.
 */
require_once __DIR__ . '/../api/bootstrap.php';

if (!mha_formation_shop_bypass_active()) {
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Boutique</title></head><body style="font-family:monospace;background:#111;color:#eee;padding:2rem;">';
    echo '<p>L’activation gratuite pré-lancement est désactivée. Utilise la <a href="/boutique/" style="color:#FBDD08;">boutique</a>.</p></body></html>';
    exit;
}

$productSlug = trim((string) ($_GET['product'] ?? $_POST['product'] ?? mha_formation_product_slug()));
$offer = mha_shop_offer($productSlug);
if ($offer === null || ($offer['status'] ?? '') !== 'available') {
    http_response_code(404);
    exit('Offre introuvable.');
}

$redirectAfter = '/auth/activate-formation-prelaunch.php?product=' . rawurlencode($productSlug);
$user = mha_require_login($redirectAfter);

if (mha_can_access_product($productSlug) && !mha_has_role('admin', 'tester')) {
    header('Location: /dashboard/?prelaunch=already', true, 303);
    exit;
}

if (!mha_shop_fulfill_purchase((string) $user['email'], $productSlug)) {
    header('Location: /dashboard/?prelaunch=error', true, 303);
    exit;
}

header('Location: /dashboard/?prelaunch=granted', true, 303);
exit;
