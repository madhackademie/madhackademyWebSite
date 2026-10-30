<?php
declare(strict_types=1);

/** Redirige vers la page commande (pseudo checkout). */
require_once __DIR__ . '/../api/bootstrap.php';

$productSlug = trim((string) ($_GET['product'] ?? mha_formation_product_slug()));
header('Location: /boutique/acheter.php?product=' . rawurlencode($productSlug), true, 303);
exit;
