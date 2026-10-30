<?php
declare(strict_types=1);

const MHA_ROOT = __DIR__;

function mha_config(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }
    $path = MHA_ROOT . '/config.php';
    if (!is_file($path)) {
        http_response_code(500);
        exit('Configuration manquante : copiez api/config.example.php vers api/config.php');
    }
    $config = require $path;
    return $config;
}

function mha_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $db = mha_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $db['host'],
        $db['name'],
        $db['charset'] ?? 'utf8mb4'
    );
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function mha_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 14,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function mha_current_user(): ?array
{
    mha_start_session();
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    static $user = false;
    if ($user === false) {
        $stmt = mha_db()->prepare(
            'SELECT id, email, display_name, role, is_active, password_initialized
             FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([(int) $_SESSION['user_id']]);
        $row = $stmt->fetch();
        $user = ($row && (int) $row['is_active'] === 1) ? $row : null;
        if ($user === null) {
            unset($_SESSION['user_id']);
        }
    }
    return $user;
}

function mha_is_logged_in(): bool
{
    return mha_current_user() !== null;
}

function mha_has_role(string ...$roles): bool
{
    $user = mha_current_user();
    if ($user === null) {
        return false;
    }
    return in_array($user['role'], $roles, true);
}

function mha_flashdev_product_slug(): string
{
    $slug = trim((string) (mha_config()['flashdev_product_slug'] ?? 'flashdev-soft'));

    return $slug !== '' ? $slug : 'flashdev-soft';
}

function mha_formation_product_slug(): string
{
    $slug = trim((string) (mha_config()['product_slug'] ?? 'gamedevready-bases-cpp'));

    return $slug !== '' ? $slug : 'gamedevready-bases-cpp';
}

function mha_device_binding_enabled(): bool
{
    return (bool) (mha_config()['device_bind_formation'] ?? true);
}

function mha_device_cookie_name(): string
{
    return 'mha_device';
}

function mha_device_table_available(): bool
{
    static $available = null;
    if ($available !== null) {
        return $available;
    }
    try {
        mha_db()->query('SELECT 1 FROM user_trusted_devices LIMIT 1');
        $available = true;
    } catch (PDOException) {
        $available = false;
    }
    return $available;
}

function mha_device_hash(string $rawToken): string
{
    $secret = (string) (mha_config()['device_binding_secret'] ?? 'dev-insecure-change-me');

    return hash_hmac('sha256', $rawToken, $secret);
}

function mha_generate_device_token(): string
{
    return bin2hex(random_bytes(32));
}

function mha_set_device_cookie(string $rawToken): void
{
    setcookie(mha_device_cookie_name(), $rawToken, [
        'expires' => time() + 60 * 60 * 24 * 400,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[mha_device_cookie_name()] = $rawToken;
}

function mha_get_device_token_from_request(): ?string
{
    $raw = trim((string) ($_COOKIE[mha_device_cookie_name()] ?? ''));
    if ($raw === '' || strlen($raw) !== 64 || !ctype_xdigit($raw)) {
        return null;
    }

    return $raw;
}

function mha_fetch_bound_device_hash(int $userId): ?string
{
    if (!mha_device_table_available()) {
        return null;
    }
    $stmt = mha_db()->prepare(
        'SELECT device_token_hash FROM user_trusted_devices WHERE user_id = ? LIMIT 1'
    );
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    return $row ? (string) $row['device_token_hash'] : null;
}

function mha_touch_trusted_device(int $userId): void
{
    if (!mha_device_table_available()) {
        return;
    }
    mha_db()->prepare(
        'UPDATE user_trusted_devices SET last_seen_at = CURRENT_TIMESTAMP WHERE user_id = ?'
    )->execute([$userId]);
}

function mha_clear_device_binding(int $userId): void
{
    if (!mha_device_table_available()) {
        return;
    }
    mha_db()->prepare('DELETE FROM user_trusted_devices WHERE user_id = ?')->execute([$userId]);
}

/**
 * Élèves avec formation : 1er accès guide = enregistre le navigateur ; autre machine = refus.
 * Admin / testeur : pas de lien appareil. Compte soft seul : pas de lien appareil.
 */
function mha_ensure_formation_device(array $user): bool
{
    if (!mha_device_binding_enabled() || !mha_device_table_available()) {
        return true;
    }
    if (in_array($user['role'], ['admin', 'tester'], true)) {
        return true;
    }
    if (!mha_can_access_product(mha_formation_product_slug())) {
        return true;
    }

    $userId = (int) $user['id'];
    $storedHash = mha_fetch_bound_device_hash($userId);
    $raw = mha_get_device_token_from_request();

    if ($storedHash === null) {
        $raw = $raw ?? mha_generate_device_token();
        mha_set_device_cookie($raw);
        $stmt = mha_db()->prepare(
            'INSERT INTO user_trusted_devices (user_id, device_token_hash) VALUES (?, ?)'
        );
        $stmt->execute([$userId, mha_device_hash($raw)]);

        return true;
    }

    if ($raw === null) {
        return false;
    }
    if (!hash_equals($storedHash, mha_device_hash($raw))) {
        return false;
    }
    mha_touch_trusted_device($userId);

    return true;
}

function mha_deny_formation_device(): never
{
    http_response_code(403);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Appareil non autorisé — MadHackAdemy</title>'
        . '<script src="https://cdn.tailwindcss.com"></script></head>'
        . '<body class="bg-black text-white font-mono min-h-screen flex items-center justify-center px-4">'
        . '<div class="max-w-md border border-gray-800 rounded-lg p-8 bg-gray-950">'
        . '<h1 class="text-xl font-bold text-red-500 mb-3">Formation indisponible sur cet appareil</h1>'
        . '<p class="text-sm text-gray-400 mb-4">Ce compte est lié au navigateur utilisé lors du premier accès aux guides. '
        . 'Pour réviser, utilise le même ordinateur, ou contacte MadHackAdemy pour changer d’appareil.</p>'
        . '<p class="text-xs text-gray-500 mb-6">Le logiciel FlashDev reste utilisable localement ; seuls les guides en ligne sont concernés.</p>'
        . '<a href="/gamedevready-bases-cpp.html" class="block text-center py-2 text-sm text-gray-400 hover:text-red-400">← Retour Bases C++</a>'
        . '<a href="/auth/logout.php" class="block text-center py-2 text-sm text-gray-500 hover:text-red-400">Déconnexion</a>'
        . '</div></body></html>';
    exit;
}

function mha_can_access_product(string $productSlug): bool
{
    $user = mha_current_user();
    if ($user === null) {
        return false;
    }
    if (in_array($user['role'], ['admin', 'tester'], true)) {
        return true;
    }
    $stmt = mha_db()->prepare(
        'SELECT 1 FROM user_products WHERE user_id = ? AND product_slug = ? LIMIT 1'
    );
    $stmt->execute([(int) $user['id'], $productSlug]);
    return (bool) $stmt->fetchColumn();
}

function mha_user_product_slugs(int $userId): array
{
    $stmt = mha_db()->prepare(
        'SELECT product_slug FROM user_products WHERE user_id = ? ORDER BY product_slug'
    );
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return array_values(array_map('strval', $rows ?: []));
}

function mha_grant_product(int $userId, string $productSlug): void
{
    $stmt = mha_db()->prepare(
        'INSERT IGNORE INTO user_products (user_id, product_slug) VALUES (?, ?)'
    );
    $stmt->execute([$userId, $productSlug]);
}

function mha_products_table_available(): bool
{
    static $available = null;
    if ($available !== null) {
        return $available;
    }
    try {
        mha_db()->query('SELECT 1 FROM products LIMIT 1');
        $available = true;
    } catch (Throwable $e) {
        $available = false;
    }

    return $available;
}

/**
 * @return array<string, array<string, mixed>>
 */
function mha_default_products_catalog(): array
{
    return [
        mha_flashdev_product_slug() => [
            'slug' => mha_flashdev_product_slug(),
            'title' => 'FlashDev',
            'subtitle' => 'Logiciel de révision — gratuit contre email',
            'kind' => 'soft',
            'phase' => null,
            'hub_url' => '/telecharger.html',
            'deck_download_path' => null,
            'sort_order' => 0,
            'is_active' => 1,
        ],
        mha_formation_product_slug() => [
            'slug' => mha_formation_product_slug(),
            'title' => 'GameDevReady — Phase 1 Bases C++',
            'subtitle' => 'Bootcamp 7 modules · deck FlashDev + guides',
            'kind' => 'formation',
            'phase' => 1,
            'hub_url' => '/gamedevready-bases-cpp.html',
            'deck_download_path' => null,
            'sort_order' => 10,
            'is_active' => 1,
        ],
    ];
}

/**
 * @return array<string, array<string, mixed>>
 */
function mha_fetch_products_catalog(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $defaults = mha_default_products_catalog();
    if (!mha_products_table_available()) {
        $cache = $defaults;

        return $cache;
    }

    try {
        $stmt = mha_db()->query(
            'SELECT slug, title, subtitle, kind, phase, hub_url, deck_download_path, sort_order, is_active
             FROM products
             WHERE is_active = 1
             ORDER BY sort_order ASC, title ASC'
        );
        $rows = $stmt->fetchAll();
        if (!$rows) {
            $cache = $defaults;

            return $cache;
        }
        $cache = [];
        foreach ($rows as $row) {
            $slug = (string) $row['slug'];
            $cache[$slug] = [
                'slug' => $slug,
                'title' => (string) $row['title'],
                'subtitle' => (string) ($row['subtitle'] ?? ''),
                'kind' => (string) ($row['kind'] ?? 'formation'),
                'phase' => $row['phase'] !== null ? (int) $row['phase'] : null,
                'hub_url' => (string) ($row['hub_url'] ?? ''),
                'deck_download_path' => $row['deck_download_path'] !== null
                    ? (string) $row['deck_download_path'] : null,
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'is_active' => (int) ($row['is_active'] ?? 1),
            ];
        }
        foreach ($defaults as $slug => $def) {
            if (!isset($cache[$slug])) {
                $cache[$slug] = $def;
            }
        }

        return $cache;
    } catch (Throwable $e) {
        $cache = $defaults;

        return $cache;
    }
}

function mha_resolve_storage_path(string $path): ?string
{
    $path = trim($path);
    if ($path === '') {
        return null;
    }
    if (preg_match('#^[a-zA-Z]:[/\\\\]#', $path) || str_starts_with($path, '/')) {
        return is_file($path) ? $path : null;
    }
    $siteRoot = dirname(__DIR__);
    $full = $siteRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($path, '/'));

    return is_file($full) ? $full : null;
}

function mha_resolve_deck_zip_path(string $productSlug): ?string
{
    $productSlug = trim($productSlug);
    if ($productSlug === '') {
        return null;
    }

    $config = mha_config();
    $files = $config['deck_download_files'] ?? [];
    if (is_array($files) && isset($files[$productSlug])) {
        $resolved = mha_resolve_storage_path((string) $files[$productSlug]);
        if ($resolved !== null) {
            return $resolved;
        }
    }

    $catalog = mha_fetch_products_catalog();
    $rel = $catalog[$productSlug]['deck_download_path'] ?? null;
    if (is_string($rel) && $rel !== '') {
        return mha_resolve_storage_path($rel);
    }

    return null;
}

/**
 * @return list<array<string, mixed>>
 */
function mha_user_library(array $user): array
{
    $catalog = mha_fetch_products_catalog();
    $items = [];

    foreach ($catalog as $slug => $meta) {
        if ((int) ($meta['is_active'] ?? 1) !== 1) {
            continue;
        }
        if (!mha_can_access_product($slug)) {
            continue;
        }

        $kind = (string) ($meta['kind'] ?? 'formation');
        $hub = (string) ($meta['hub_url'] ?? '/');
        if ($hub === '') {
            $hub = '/';
        }

        $deckZip = mha_resolve_deck_zip_path($slug);
        $canDeckDownload = $deckZip !== null && $kind !== 'soft';

        $items[] = [
            'slug' => $slug,
            'title' => (string) ($meta['title'] ?? $slug),
            'subtitle' => (string) ($meta['subtitle'] ?? ''),
            'kind' => $kind,
            'phase' => $meta['phase'] ?? null,
            'hub_url' => $hub,
            'can_download_deck' => $canDeckDownload,
            'deck_download_url' => $canDeckDownload
                ? '/auth/download-deck.php?slug=' . rawurlencode($slug)
                : null,
            'flashdev_download_url' => ($kind === 'soft' || $slug === mha_flashdev_product_slug())
                ? '/telecharger.html'
                : null,
            'guide_start_url' => ($kind === 'formation')
                ? '/auth/guide.php?m=01'
                : null,
        ];
    }

    usort($items, static function (array $a, array $b): int {
        $oa = $a['kind'] === 'soft' ? 0 : 1;

        return $oa <=> ($b['kind'] === 'soft' ? 0 : 1);
    });

    return $items;
}

/**
 * Catalogue boutique GameDevReady (vitrine + checkout).
 *
 * @return array<string, array<string, mixed>>
 */
function mha_shop_offers(): array
{
    $formationSlug = mha_formation_product_slug();
    $checkoutUrls = mha_config()['shop_checkout_urls'] ?? [];

    return [
        $formationSlug => [
            'slug' => $formationSlug,
            'sku' => 'bootcamp-cpp-p1',
            'title' => 'Phase 1 — Bootcamp C++',
            'short_title' => 'Bootcamp C++',
            'subtitle' => 'Deck FlashDev · 7 modules · guides HTML',
            'price_cents' => 100,
            'price_label' => '1 €',
            'currency' => 'EUR',
            'phase' => 1,
            'status' => 'available',
            'product_slug' => $formationSlug,
            'hub_url' => '/gamedevready-bases-cpp.html',
            'checkout_url' => mha_shop_checkout_url($formationSlug),
            'includes' => [
                'Deck GameDevReady Phase 1 (7 cartes C++)',
                'Téléchargement .zip + install dans FlashDev',
                '7 guides pédagogiques (connexion site)',
                'Cartes HTML publiques sur le parcours deck',
            ],
        ],
        'gamedevready-phase-2' => [
            'slug' => 'gamedevready-phase-2',
            'sku' => 'raylib-p2',
            'title' => 'Phase 2 — Raylib & challenges',
            'short_title' => 'Raylib',
            'subtitle' => 'À la sortie — achat séparé',
            'price_cents' => 100,
            'price_label' => '1 €',
            'currency' => 'EUR',
            'phase' => 2,
            'status' => 'coming_soon',
            'product_slug' => 'gamedevready-phase-2',
            'hub_url' => '/gamedevready.html',
            'checkout_url' => null,
            'includes' => [],
        ],
        'gamedevready-phase-3' => [
            'slug' => 'gamedevready-phase-3',
            'sku' => 'projets-p3',
            'title' => 'Phase 3 — Projets & paradigmes',
            'short_title' => 'Projets',
            'subtitle' => 'À la sortie — achat séparé',
            'price_cents' => 1000,
            'price_label' => '10 €',
            'currency' => 'EUR',
            'phase' => 3,
            'status' => 'coming_soon',
            'product_slug' => 'gamedevready-phase-3',
            'hub_url' => '/gamedevready.html',
            'checkout_url' => null,
            'includes' => [],
        ],
    ];
}

function mha_shop_offer(string $slug): ?array
{
    $slug = trim($slug);
    $offers = mha_shop_offers();
    if (isset($offers[$slug])) {
        return $offers[$slug];
    }
    foreach ($offers as $offer) {
        if (($offer['product_slug'] ?? '') === $slug) {
            return $offer;
        }
    }

    return null;
}

function mha_shop_checkout_url(string $productSlug): ?string
{
    $urls = mha_config()['shop_checkout_urls'] ?? [];
    if (!is_array($urls)) {
        return null;
    }
    $url = trim((string) ($urls[$productSlug] ?? ''));
    if ($url === '' || !preg_match('#^https://#i', $url)) {
        return null;
    }

    return $url;
}

/**
 * true = accès Phase 1 sans paiement Systeme.io (entreprise / funnel pas encore en service).
 * Désactiver dès que Stripe + funnel 1 € + webhook SALE_NEW sont live.
 */
function mha_formation_shop_bypass_active(): bool
{
    return !empty(mha_config()['formation_free_grant_until_systeme_io']);
}

/**
 * Attribue un produit après paiement validé (webhook ou retour manuel staff).
 */
function mha_shop_fulfill_purchase(string $email, string $productSlug): bool
{
    $email = strtolower(trim($email));
    $productSlug = trim($productSlug);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $productSlug === '') {
        return false;
    }

    $offer = mha_shop_offer($productSlug);
    if ($offer === null || ($offer['status'] ?? '') !== 'available') {
        $allowed = [mha_formation_product_slug(), mha_flashdev_product_slug()];
        if (!in_array($productSlug, $allowed, true)) {
            return false;
        }
    }

    $grantSlug = (string) ($offer['product_slug'] ?? $productSlug);
    if ($grantSlug === 'gamedevready-phase-2' || $grantSlug === 'gamedevready-phase-3') {
        return false;
    }

    $stmt = mha_db()->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $userId = (int) ($stmt->fetchColumn() ?: 0);
    if ($userId <= 0) {
        $created = mha_ensure_flashdev_user($email);
        $userId = (int) ($created['id'] ?? 0);
    }
    if ($userId <= 0) {
        return false;
    }

    mha_grant_product($userId, $grantSlug);
    mha_grant_product($userId, mha_flashdev_product_slug());

    return true;
}

/**
 * Secret webhooks Systeme.io (Paramètres → Webhooks). Fallback legacy shop_webhook_secret.
 */
function mha_systeme_webhook_secret(): string
{
    $config = mha_config();
    $secret = trim((string) ($config['systeme_io_webhook_secret'] ?? ''));
    if ($secret === '') {
        $secret = trim((string) ($config['shop_webhook_secret'] ?? ''));
    }

    return $secret;
}

/**
 * @return array<int, string> pricePlan.id Systeme.io → product_slug site
 */
function mha_systeme_price_plan_products(): array
{
    $map = mha_config()['systeme_io_price_plan_products'] ?? [];
    if (!is_array($map)) {
        return [];
    }
    $out = [];
    foreach ($map as $planId => $slug) {
        $out[(int) $planId] = trim((string) $slug);
    }

    return $out;
}

function mha_systeme_verify_webhook_signature(string $rawBody, string $signatureHeader, string $secret): bool
{
    $signatureHeader = strtolower(trim($signatureHeader));
    if ($signatureHeader === '' || $secret === '') {
        return false;
    }

    $candidates = [$rawBody];
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded)) {
        $candidates[] = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $candidates[] = json_encode($decoded, JSON_UNESCAPED_SLASHES);
    }

    foreach (array_unique($candidates) as $payload) {
        $expected = hash_hmac('sha256', $payload, $secret);
        if (hash_equals($signatureHeader, strtolower($expected))) {
            return true;
        }
    }

    return false;
}

function mha_systeme_product_slug_from_sale_payload(array $payload): ?string
{
    $planId = (int) ($payload['pricePlan']['id'] ?? 0);
    if ($planId > 0) {
        $map = mha_systeme_price_plan_products();
        if (isset($map[$planId]) && $map[$planId] !== '') {
            return $map[$planId];
        }
    }

    $configured = mha_systeme_price_plan_products();
    if (count($configured) === 1) {
        return reset($configured) ?: null;
    }

    return null;
}

/**
 * Point d’entrée HTTP webhook Systeme.io (événement SALE_NEW → accès deck).
 */
function mha_systeme_webhook_serve(): void
{
    header('Content-Type: application/json; charset=UTF-8');

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'method']);
        return;
    }

    $secret = mha_systeme_webhook_secret();
    if ($secret === '') {
        http_response_code(503);
        echo json_encode(['ok' => false, 'error' => 'webhook_not_configured']);
        return;
    }

    $raw = file_get_contents('php://input');
    if ($raw === false) {
        $raw = '';
    }

    $event = strtoupper(trim((string) ($_SERVER['HTTP_X_WEBHOOK_EVENT'] ?? '')));
    $signature = (string) ($_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '');

    if ($event !== '') {
        if (!mha_systeme_verify_webhook_signature($raw, $signature, $secret)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'invalid_signature']);
            return;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'invalid_json']);
            return;
        }

        if ($event === 'SALE_CANCELED' || $event === 'SALE_CANCELLED') {
            echo json_encode(['ok' => true, 'ignored' => 'sale_canceled']);
            return;
        }

        if ($event !== 'SALE_NEW' && $event !== 'NEW_SALE') {
            echo json_encode(['ok' => true, 'ignored' => 'event', 'event' => $event]);
            return;
        }

        $email = strtolower(trim((string) ($data['customer']['email'] ?? '')));
        $productSlug = mha_systeme_product_slug_from_sale_payload($data);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $productSlug === null) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'unmapped_sale']);
            return;
        }

        if (!mha_shop_fulfill_purchase($email, $productSlug)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'fulfill_failed']);
            return;
        }

        echo json_encode([
            'ok' => true,
            'source' => 'systeme_io',
            'email' => $email,
            'product_slug' => $productSlug,
            'order_id' => (int) ($data['order']['id'] ?? 0),
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    // Legacy : automation Systeme.io « action webhook » JSON custom (même secret)
    $data = json_decode($raw !== '' ? $raw : '', true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'invalid_json']);
        return;
    }

    $bodySecret = trim((string) ($data['secret'] ?? ''));
    $headerSecret = trim((string) ($_SERVER['HTTP_X_MHA_SHOP_SECRET'] ?? ''));
    $provided = $headerSecret !== '' ? $headerSecret : $bodySecret;
    if (!hash_equals($secret, $provided)) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'forbidden']);
        return;
    }

    $email = strtolower(trim((string) ($data['email'] ?? '')));
    $productSlug = trim((string) ($data['product_slug'] ?? mha_formation_product_slug()));
    if (!mha_shop_fulfill_purchase($email, $productSlug)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'fulfill_failed']);
        return;
    }

    echo json_encode(['ok' => true, 'email' => $email, 'product_slug' => $productSlug], JSON_UNESCAPED_UNICODE);
}

/**
 * Crée ou récupère un compte student pour l'opt-in FlashDev.
 * password_initialized = 0 tant que l'utilisateur n'a pas choisi son MDP.
 *
 * @return array{id: int, email: string, password_initialized: int}
 */
function mha_ensure_flashdev_user(string $email): array
{
    $email = strtolower(trim($email));
    $stmt = mha_db()->prepare(
        'SELECT id, email, password_initialized FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row) {
        return [
            'id' => (int) $row['id'],
            'email' => (string) $row['email'],
            'password_initialized' => (int) ($row['password_initialized'] ?? 1),
        ];
    }

    $placeholder = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
    $insert = mha_db()->prepare(
        'INSERT INTO users (email, password_hash, display_name, role, password_initialized)
         VALUES (?, ?, ?, \'student\', 0)'
    );
    $insert->execute([$email, $placeholder, $email]);

    return [
        'id' => (int) mha_db()->lastInsertId(),
        'email' => $email,
        'password_initialized' => 0,
    ];
}

/** @return string raw token (à mettre dans l’URL une seule fois) */
function mha_create_password_setup_token(int $userId, int $ttlSeconds = 86400): string
{
    $raw = bin2hex(random_bytes(32));
    $hash = hash('sha256', $raw);
    $expires = (new DateTimeImmutable('now'))->modify('+' . max(300, $ttlSeconds) . ' seconds');

    // Invalide les anciens tokens non utilisés
    $cleanup = mha_db()->prepare(
        'UPDATE password_setup_tokens SET used_at = NOW()
         WHERE user_id = ? AND used_at IS NULL'
    );
    $cleanup->execute([$userId]);

    $stmt = mha_db()->prepare(
        'INSERT INTO password_setup_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
    );
    $stmt->execute([$userId, $hash, $expires->format('Y-m-d H:i:s')]);

    return $raw;
}

/**
 * @return array{user_id: int, email: string}|null
 */
function mha_peek_password_setup_token(string $rawToken): ?array
{
    $rawToken = trim($rawToken);
    if ($rawToken === '' || strlen($rawToken) < 32) {
        return null;
    }
    $hash = hash('sha256', $rawToken);
    $stmt = mha_db()->prepare(
        'SELECT t.user_id, u.email
         FROM password_setup_tokens t
         INNER JOIN users u ON u.id = t.user_id
         WHERE t.token_hash = ?
           AND t.used_at IS NULL
           AND t.expires_at > NOW()
           AND u.is_active = 1
         LIMIT 1'
    );
    $stmt->execute([$hash]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    return [
        'user_id' => (int) $row['user_id'],
        'email' => (string) $row['email'],
    ];
}

function mha_complete_password_setup(string $rawToken, string $password): bool
{
    if (strlen($password) < 8) {
        return false;
    }
    $info = mha_peek_password_setup_token($rawToken);
    if ($info === null) {
        return false;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo = mha_db();
    $pdo->beginTransaction();
    try {
        $upd = $pdo->prepare(
            'UPDATE users SET password_hash = ?, password_initialized = 1 WHERE id = ?'
        );
        $upd->execute([$hash, $info['user_id']]);

        $tok = $pdo->prepare(
            'UPDATE password_setup_tokens SET used_at = NOW()
             WHERE token_hash = ? AND used_at IS NULL'
        );
        $tok->execute([hash('sha256', $rawToken)]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        return false;
    }

    return mha_login($info['email'], $password);
}

function mha_require_login(?string $redirectAfter = null): array
{
    $user = mha_current_user();
    if ($user !== null) {
        return $user;
    }
    $target = '/auth/login.php';
    if ($redirectAfter !== null && $redirectAfter !== '') {
        $target .= '?redirect=' . rawurlencode($redirectAfter);
    }
    header('Location: ' . $target);
    exit;
}

function mha_require_product(string $productSlug, ?string $redirectAfter = null): array
{
    $user = mha_require_login($redirectAfter);
    if (!mha_can_access_product($productSlug)) {
        http_response_code(403);
        exit('Accès refusé — formation non activée pour ce compte.');
    }
    if ($productSlug === mha_formation_product_slug() && !mha_ensure_formation_device($user)) {
        mha_deny_formation_device();
    }
    return $user;
}

function mha_login(string $email, string $password): bool
{
    $stmt = mha_db()->prepare(
        'SELECT id, password_hash, is_active, password_initialized FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([strtolower(trim($email))]);
    $row = $stmt->fetch();
    if (!$row || (int) $row['is_active'] !== 1) {
        return false;
    }
    // Compte opt-in sans MDP choisi → refus (passer par set-password)
    if (array_key_exists('password_initialized', $row) && (int) $row['password_initialized'] !== 1) {
        return false;
    }
    if (!password_verify($password, $row['password_hash'])) {
        return false;
    }
    mha_start_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $row['id'];
    return true;
}

function mha_logout(): void
{
    mha_start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function mha_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function mha_auth_nav_html(?string $redirectAfter = null): string
{
    $user = mha_current_user();
    if ($user !== null) {
        return sprintf(
            '<span class="text-gray-500 text-xs md:text-sm hidden sm:inline">%s</span>'
            . '<a href="/auth/logout.php" class="text-gray-400 hover:text-yellow-400 transition text-xs md:text-sm">Déconnexion</a>',
            mha_escape($user['email'])
        );
    }
    $redirect = $redirectAfter ?? '/gamedevready-bases-cpp.html';
    $href = '/auth/login.php?redirect=' . rawurlencode($redirect);

    return sprintf(
        '<a href="%s" class="text-gray-400 hover:text-yellow-400 transition text-xs md:text-sm">Connexion</a>',
        mha_escape($href)
    );
}

function mha_guide_toolbar_html(): string
{
    $user = mha_current_user();
    if ($user === null) {
        return '';
    }

    return '<div style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#111;border-bottom:1px solid #333;'
        . 'padding:8px 16px;font-family:monospace;font-size:12px;color:#aaa;display:flex;'
        . 'justify-content:space-between;align-items:center;gap:12px;">'
        . '<span>MadHackAdemy · ' . mha_escape($user['email']) . '</span>'
        . '<span style="display:flex;gap:12px;">'
        . '<a href="/dashboard/" style="color:#facc15;text-decoration:none;">Ma bibliothèque</a>'
        . '<a href="/gamedevready-bases-cpp.html" style="color:#fbbf24;text-decoration:none;">← Bases C++</a>'
        . '<a href="/auth/logout.php" style="color:#f87171;text-decoration:none;">Déconnexion</a>'
        . '</span></div>'
        . '<div style="height:36px;"></div>';
}

function mha_guides_catalog(): array
{
    return [
        '01' => [
            'title' => '01 — Printf',
            'file' => dirname(__DIR__) . '/Formations/BaseCpp/guides/01_PrintFGuide/printfC++FrogTheme.html',
            'base' => '/Formations/BaseCpp/guides/01_PrintFGuide/',
        ],
        '02' => [
            'title' => '02 — Variables',
            'file' => dirname(__DIR__) . '/Formations/BaseCpp/guides/02_VariableGuide/VariableC++FroggerTheme.html',
            'base' => '/Formations/BaseCpp/guides/02_VariableGuide/',
        ],
        '03' => [
            'title' => '03 — Conditions',
            'file' => dirname(__DIR__) . '/Formations/BaseCpp/guides/03_ConditionsGuide/Conditions.html',
            'base' => '/Formations/BaseCpp/guides/03_ConditionsGuide/',
        ],
        '04' => [
            'title' => '04 — Boucles',
            'file' => dirname(__DIR__) . '/Formations/BaseCpp/guides/04_BouclesGuide/LoopModule.html',
            'base' => '/Formations/BaseCpp/guides/04_BouclesGuide/',
        ],
        '05' => [
            'title' => '05 — STD & Fonctions',
            'file' => dirname(__DIR__) . '/Formations/BaseCpp/guides/05_StdFonctionsGuide/stdLib&Fonction.html',
            'base' => '/Formations/BaseCpp/guides/05_StdFonctionsGuide/',
        ],
        '06' => [
            'title' => '06 — Conteneurs',
            'file' => dirname(__DIR__) . '/Formations/BaseCpp/guides/06_ConteneursGuide/Conteneurs.html',
            'base' => '/Formations/BaseCpp/guides/06_ConteneursGuide/',
        ],
        '07' => [
            'title' => '07 — Struct & Méthodes',
            'file' => dirname(__DIR__) . '/Formations/BaseCpp/guides/07_StructMethodesGuide/StructMethodes.html',
            'base' => '/Formations/BaseCpp/guides/07_StructMethodesGuide/',
        ],
    ];
}

function mha_serve_guide_html(string $moduleId): void
{
    $catalog = mha_guides_catalog();
    if (!isset($catalog[$moduleId])) {
        http_response_code(404);
        exit('Guide introuvable.');
    }
    $guide = $catalog[$moduleId];
    if (!is_file($guide['file'])) {
        http_response_code(404);
        exit('Fichier guide manquant sur le serveur.');
    }
    $html = file_get_contents($guide['file']);
    if ($html === false) {
        http_response_code(500);
        exit('Impossible de lire le guide.');
    }
    $baseTag = '<base href="' . mha_escape($guide['base']) . '">';
    if (stripos($html, '<head>') !== false) {
        $html = preg_replace('/<head>/i', '<head>' . $baseTag, $html, 1);
    } else {
        $html = $baseTag . $html;
    }
    $toolbar = mha_guide_toolbar_html();
    if ($toolbar !== '') {
        if (preg_match('/<body[^>]*>/i', $html)) {
            $html = preg_replace('/<body([^>]*)>/i', '<body$1>' . $toolbar, $html, 1);
        } else {
            $html = $toolbar . $html;
        }
    }
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Frame-Options: SAMEORIGIN');
    echo $html;
}
