<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$error = '';
$redirect = $_GET['redirect'] ?? '/gamedevready-bases-cpp.html';

if (mha_is_logged_in()) {
    header('Location: ' . ($redirect ?: '/gamedevready-bases-cpp.html'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? $redirect;

    if ($email === '' || $password === '') {
        $error = 'Email et mot de passe requis.';
    } elseif (mha_login($email, $password)) {
        header('Location: ' . ($redirect ?: '/gamedevready-bases-cpp.html'));
        exit;
    } else {
        $error = 'Identifiants incorrects.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — MadHackAdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white font-mono min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md border border-gray-800 rounded-lg p-8 bg-gray-950">
        <h1 class="text-2xl font-bold mb-2 text-red-500">MadHackAdemy</h1>
        <p class="text-gray-400 text-sm mb-6">Connexion élève / testeur / admin</p>

        <?php if ($error !== ''): ?>
            <p class="mb-4 text-sm text-red-400 border border-red-900 bg-red-950/40 rounded px-3 py-2"><?= mha_escape($error) ?></p>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <input type="hidden" name="redirect" value="<?= mha_escape($redirect) ?>">
            <div>
                <label for="email" class="block text-xs text-gray-500 mb-1">Email</label>
                <input type="email" id="email" name="email" required autocomplete="username"
                    class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm focus:border-red-500 outline-none">
            </div>
            <div>
                <label for="password" class="block text-xs text-gray-500 mb-1">Mot de passe</label>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                    class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm focus:border-red-500 outline-none">
            </div>
            <button type="submit"
                class="w-full py-3 bg-red-900 hover:bg-red-700 text-red-100 font-bold rounded text-sm transition">
                Se connecter
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-600">
            <a href="/gamedevready-bases-cpp.html" class="text-gray-400 hover:text-red-400">← Retour Bases C++</a>
        </p>
    </div>
</body>
</html>
