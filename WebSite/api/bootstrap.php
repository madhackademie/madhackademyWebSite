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
            'SELECT id, email, display_name, role, is_active FROM users WHERE id = ? LIMIT 1'
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
        'SELECT id, password_hash, is_active FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([strtolower(trim($email))]);
    $row = $stmt->fetch();
    if (!$row || (int) $row['is_active'] !== 1) {
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
