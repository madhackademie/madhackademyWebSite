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
