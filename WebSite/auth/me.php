<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

$user = mha_current_user();
$products = [];
$canDownloadFlashdev = false;

if ($user !== null) {
    $products = mha_user_product_slugs((int) $user['id']);
    $flashSlug = mha_flashdev_product_slug();
    $formationSlug = trim((string) (mha_config()['product_slug'] ?? 'gamedevready-bases-cpp'));
    $canDownloadFlashdev = in_array($user['role'], ['admin', 'tester'], true)
        || in_array($flashSlug, $products, true)
        || ($formationSlug !== '' && in_array($formationSlug, $products, true));
}

echo json_encode([
    'logged_in' => $user !== null,
    'email' => $user['email'] ?? null,
    'role' => $user['role'] ?? null,
    'products' => $products,
    'can_download_flashdev' => $canDownloadFlashdev,
], JSON_UNESCAPED_UNICODE);
