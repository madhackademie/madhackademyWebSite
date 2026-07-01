<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$config = mha_config();
$product = $config['product_slug'] ?? 'gamedevready-bases-cpp';
$moduleId = $_GET['m'] ?? '';

$redirect = '/auth/guide.php?m=' . rawurlencode($moduleId);
mha_require_product($product, $redirect);

mha_serve_guide_html($moduleId);
