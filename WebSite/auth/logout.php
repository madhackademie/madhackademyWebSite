<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

mha_logout();

$redirect = (string) ($_GET['redirect'] ?? '/flashdev.html');
if ($redirect === '' || $redirect[0] !== '/' || str_starts_with($redirect, '//')) {
    $redirect = '/flashdev.html';
}

header('Location: ' . $redirect);
exit;
