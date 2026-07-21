<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

const SYSTEME_IO_CONTACTS_URL = 'https://api.systeme.io/api/contacts';

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

if ($apiKey === '') {
    http_response_code(500);
    exit('Configuration Systeme.io manquante.');
}

$result = systeme_optin_create_contact($apiKey, $email);

if ($result === 'created') {
    header('Location: /merci-flashdev.html', true, 303);
    exit;
}

if ($result === 'exists') {
    header('Location: /optin-email-existe.html', true, 303);
    exit;
}

header('Location: /flashdev.html?optin=error', true, 303);
exit;

/**
 * @return 'created'|'exists'|'error'
 */
function systeme_optin_create_contact(string $apiKey, string $email): string
{
    $payload = json_encode([
        'email' => $email,
        'locale' => 'fr',
    ], JSON_THROW_ON_ERROR);

    $ch = curl_init(SYSTEME_IO_CONTACTS_URL);
    if ($ch === false) {
        return 'error';
    }

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-API-Key: ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
    ]);

    $responseBody = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) {
        return 'created';
    }

    if ($httpCode === 422 && is_string($responseBody) && str_contains($responseBody, 'déjà utilisée')) {
        return 'exists';
    }

    return 'error';
}
