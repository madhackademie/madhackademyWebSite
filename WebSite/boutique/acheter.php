<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$productSlug = trim((string) ($_GET['product'] ?? mha_formation_product_slug()));
$offer = mha_shop_offer($productSlug);

if ($offer === null || ($offer['status'] ?? '') !== 'available') {
    http_response_code(404);
    exit('Offre introuvable ou pas encore en vente.');
}

$redirectAfter = '/boutique/acheter.php?product=' . rawurlencode($productSlug);
$user = mha_require_login($redirectAfter);

if (mha_can_access_product($productSlug)) {
    header('Location: /dashboard/?shop=already', true, 303);
    exit;
}

if (mha_formation_shop_bypass_active()) {
    header('Location: /auth/activate-formation-prelaunch.php?product=' . rawurlencode($productSlug), true, 303);
    exit;
}

$checkoutUrl = mha_shop_checkout_url($productSlug);
if ($checkoutUrl === null) {
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Paiement</title></head><body style="font-family:monospace;background:#111;color:#eee;padding:2rem;">';
    echo '<h1>Paiement en configuration</h1>';
    echo '<p>Le lien Systeme.io Phase 1 n’est pas encore renseigné dans <code>api/config.php</code> (<code>shop_checkout_urls</code>).</p>';
    echo '<p><a href="/boutique/" style="color:#FBDD08;">← Boutique</a></p></body></html>';
    exit;
}

// Paiement = tunnel Systeme.io (TVA / facturation). Page merci = UX ; l’accès deck est granté par webhook SALE_NEW.
$successUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'madhackademy.eu')
    . '/boutique/merci.php?product=' . rawurlencode($productSlug);

$separator = str_contains($checkoutUrl, '?') ? '&' : '?';
$redirect = $checkoutUrl . $separator . 'success_url=' . rawurlencode($successUrl);

header('Location: ' . $redirect, true, 303);
exit;
