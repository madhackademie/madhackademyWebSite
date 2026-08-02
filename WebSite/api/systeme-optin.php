<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

const SYSTEME_IO_API_BASE = 'https://api.systeme.io/api';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /flashdev.html', true, 303);
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$consent = isset($_POST['consent']) && (string) $_POST['consent'] !== '' && (string) $_POST['consent'] !== '0';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$consent) {
    header('Location: /flashdev.html?optin=error', true, 303);
    exit;
}

$config = mha_config();
$apiKey = trim((string) ($config['systeme_io_api_key'] ?? ''));
$tagName = trim((string) ($config['systeme_io_tag_flashdev'] ?? 'flashdev-download'));

if ($apiKey === '') {
    http_response_code(500);
    exit('Configuration Systeme.io manquante.');
}

$result = systeme_optin_create_contact($apiKey, $email);

$contactId = 0;
if ($result['status'] === 'created') {
    $contactId = (int) ($result['contactId'] ?? 0);
} elseif ($result['status'] === 'exists') {
    $contactId = (int) (systeme_optin_find_contact_id_by_email($apiKey, $email) ?? 0);
}

// CRM Systeme.io obligatoire : contact + tag avant compte site
if ($contactId <= 0 || !systeme_optin_assign_tag_by_name($apiKey, $contactId, $tagName)) {
    header('Location: /flashdev.html?optin=error', true, 303);
    exit;
}

systeme_optin_finish_site_account($email);

/**
 * Compte MySQL + acquis flashdev-soft, puis set-password ou page « déjà un compte ».
 */
function systeme_optin_finish_site_account(string $email): void
{
    try {
        $user = mha_ensure_flashdev_user($email);
        mha_grant_product((int) $user['id'], mha_flashdev_product_slug());

        if ((int) $user['password_initialized'] !== 1) {
            $token = mha_create_password_setup_token((int) $user['id']);
            header('Location: /auth/set-password.php?token=' . rawurlencode($token), true, 303);
            exit;
        }

        // MDP déjà choisi → feedback clair (pas une page login muette)
        header(
            'Location: /optin-email-existe.html?email=' . rawurlencode($email),
            true,
            303
        );
        exit;
    } catch (Throwable $e) {
        header('Location: /flashdev.html?optin=error', true, 303);
        exit;
    }
}

/**
 * @return array{status: 'created'|'exists'|'error', contactId?: int}
 */
function systeme_optin_create_contact(string $apiKey, string $email): array
{
    $payload = json_encode([
        'email' => $email,
        'locale' => 'fr',
    ], JSON_THROW_ON_ERROR);

    $response = systeme_optin_request('POST', SYSTEME_IO_API_BASE . '/contacts', $apiKey, $payload);
    if ($response === null) {
        return ['status' => 'error'];
    }

    [$httpCode, $body] = $response;

    if ($httpCode === 201) {
        $data = json_decode($body, true);
        $contactId = is_array($data) ? (int) ($data['id'] ?? 0) : 0;
        if ($contactId <= 0) {
            return ['status' => 'error'];
        }

        return ['status' => 'created', 'contactId' => $contactId];
    }

    // Email déjà connu (message FR/EN variable selon locale API)
    if ($httpCode === 422) {
        return ['status' => 'exists'];
    }

    return ['status' => 'error'];
}

function systeme_optin_assign_tag_by_name(string $apiKey, int $contactId, string $tagName): bool
{
    if ($contactId <= 0 || $tagName === '') {
        return false;
    }

    $tagId = systeme_optin_resolve_tag_id($apiKey, $tagName);
    if ($tagId === null) {
        return false;
    }

    $payload = json_encode(['tagId' => $tagId], JSON_THROW_ON_ERROR);
    $response = systeme_optin_request(
        'POST',
        SYSTEME_IO_API_BASE . '/contacts/' . $contactId . '/tags',
        $apiKey,
        $payload
    );

    if ($response === null) {
        return false;
    }

    [$httpCode] = $response;

    // 204 = OK ; 422 = souvent déjà tagué (automation peut avoir déjà tourné)
    return $httpCode === 204 || $httpCode === 422;
}

function systeme_optin_resolve_tag_id(string $apiKey, string $tagName): ?int
{
    $url = SYSTEME_IO_API_BASE . '/tags?' . http_build_query([
        'query' => $tagName,
        'limit' => 100,
    ]);
    $response = systeme_optin_request('GET', $url, $apiKey);
    if ($response === null) {
        return null;
    }

    [$httpCode, $body] = $response;
    if ($httpCode !== 200) {
        return null;
    }

    $data = json_decode($body, true);
    if (!is_array($data)) {
        return null;
    }

    $items = $data['items'] ?? $data;
    if (!is_array($items)) {
        return null;
    }

    $wanted = mb_strtolower($tagName);
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $name = mb_strtolower(trim((string) ($item['name'] ?? '')));
        if ($name === $wanted) {
            $id = (int) ($item['id'] ?? 0);

            return $id > 0 ? $id : null;
        }
    }

    return null;
}

function systeme_optin_find_contact_id_by_email(string $apiKey, string $email): ?int
{
    $wanted = mb_strtolower(trim($email));
    $queries = [
        ['email' => $email, 'limit' => 20],
        ['email' => $wanted, 'limit' => 20],
        ['query' => $email, 'limit' => 50],
    ];

    foreach ($queries as $params) {
        $url = SYSTEME_IO_API_BASE . '/contacts?' . http_build_query($params);
        $response = systeme_optin_request('GET', $url, $apiKey);
        if ($response === null) {
            continue;
        }

        [$httpCode, $body] = $response;
        if ($httpCode !== 200) {
            continue;
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            continue;
        }

        $items = $data['items'] ?? [];
        if (!is_array($items)) {
            continue;
        }

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (mb_strtolower(trim((string) ($item['email'] ?? ''))) === $wanted) {
                $id = (int) ($item['id'] ?? 0);

                return $id > 0 ? $id : null;
            }
        }
    }

    return null;
}

/**
 * @return array{0: int, 1: string}|null
 */
function systeme_optin_request(string $method, string $url, string $apiKey, ?string $body = null): ?array
{
    $ch = curl_init($url);
    if ($ch === false) {
        return null;
    }

    $headers = [
        'Accept: application/json',
        'X-API-Key: ' . $apiKey,
    ];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
    }

    $opts = [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
    ];
    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = $body;
    }

    curl_setopt_array($ch, $opts);
    $responseBody = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($responseBody === false) {
        return null;
    }

    return [$httpCode, (string) $responseBody];
}
