<?php
declare(strict_types=1);

require_once __DIR__ . '/api/bootstrap.php';

$product = trim((string) ($_GET['product'] ?? mha_formation_product_slug()));
$query = $product !== '' ? '?product=' . rawurlencode($product) : '';
header('Location: /auth/deck.php' . $query, true, 302);
exit;
