<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$user = mha_require_login('/dashboard/');

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    $slug = mha_formation_product_slug();
}

if (!preg_match('/^[a-z0-9][a-z0-9-]{0,62}$/', $slug)) {
    http_response_code(400);
    exit('Slug produit invalide.');
}

if (!mha_can_access_product($slug)) {
    http_response_code(403);
    exit('Accès refusé — ce deck n’est pas activé pour ton compte.');
}

$path = mha_resolve_deck_zip_path($slug);
if ($path === null) {
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Deck indisponible</title></head>'
        . '<body style="font-family:monospace;background:#111;color:#eee;padding:2rem;">'
        . '<h1>Fichier deck non disponible</h1>'
        . '<p>Le zip n’est pas encore publié sur le serveur. Contacte MadHackAdemy ou réessaie plus tard.</p>'
        . '<p><a href="/dashboard/" style="color:#facc15;">← Ma bibliothèque</a></p></body></html>';
    exit;
}

$basename = basename($path);
$size = filesize($path);
if ($size === false) {
    http_response_code(500);
    exit('Impossible de lire le fichier deck.');
}

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $basename) . '"');
header('Content-Length: ' . (string) $size);
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

$handle = fopen($path, 'rb');
if ($handle === false) {
    http_response_code(500);
    exit('Impossible d’ouvrir le fichier deck.');
}

try {
    while (!feof($handle)) {
        $chunk = fread($handle, 8192);
        if ($chunk === false) {
            break;
        }
        echo $chunk;
    }
} finally {
    fclose($handle);
}
