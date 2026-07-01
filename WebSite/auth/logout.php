<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

mha_logout();
header('Location: /auth/login.php');
exit;
