<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$user = mha_require_login('/dashboard/');

if (!mha_has_role('admin', 'tester')) {
    http_response_code(403);
    exit('Accès réservé aux administrateurs et testeurs.');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /dashboard/', true, 303);
    exit;
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$productSlug = trim((string) ($_POST['product_slug'] ?? mha_formation_product_slug()));

$allowedSlugs = [
    mha_flashdev_product_slug(),
    mha_formation_product_slug(),
];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /dashboard/?grant_error=email', true, 303);
    exit;
}

if (!in_array($productSlug, $allowedSlugs, true)) {
    header('Location: /dashboard/?grant_error=product', true, 303);
    exit;
}

$stmt = mha_db()->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
$stmt->execute([$email]);
$targetId = (int) ($stmt->fetchColumn() ?: 0);

if ($targetId <= 0) {
    header('Location: /dashboard/?grant_error=unknown', true, 303);
    exit;
}

mha_grant_product($targetId, $productSlug);

header('Location: /dashboard/?granted=1&for=' . rawurlencode($email), true, 303);
exit;
