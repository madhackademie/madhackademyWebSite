<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/bootstrap.php';

$error = '';
$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$redirect = '/flashdev.html';
$tokenInfo = $token !== '' ? mha_peek_password_setup_token($token) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');
    $token = trim((string) ($_POST['token'] ?? ''));
    $tokenInfo = $token !== '' ? mha_peek_password_setup_token($token) : null;

    if ($tokenInfo === null) {
        $error = 'Lien invalide ou expiré. Réinscris-toi sur la page FlashDev pour en recevoir un nouveau.';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Les deux mots de passe ne correspondent pas.';
    } elseif (mha_complete_password_setup($token, $password)) {
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = 'Impossible d’enregistrer le mot de passe. Réessaie ou demande un nouveau lien.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir mon mot de passe — MadHackAdemy</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white font-mono min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md border border-gray-800 rounded-lg p-8 bg-gray-950">
        <h1 class="text-2xl font-bold mb-2 text-red-500">MadHackAdemy</h1>
        <p class="text-gray-400 text-sm mb-6">Crée ton compte — choisis ton mot de passe</p>

        <?php if ($tokenInfo === null && $error === ''): ?>
            <p class="mb-4 text-sm text-red-400 border border-red-900 bg-red-950/40 rounded px-3 py-2">
                Lien invalide ou expiré. Réinscris-toi sur la page FlashDev pour en recevoir un nouveau.
            </p>
            <a href="/flashdev.html"
               class="block w-full py-3 text-center border border-gray-700 hover:border-red-500 text-gray-300 rounded text-sm transition">
                Retour à FlashDev
            </a>
        <?php else: ?>
            <?php if ($tokenInfo !== null): ?>
                <p class="mb-4 text-sm text-gray-400">
                    Compte : <span class="text-green-400"><?= mha_escape($tokenInfo['email']) ?></span>
                </p>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <p class="mb-4 text-sm text-red-400 border border-red-900 bg-red-950/40 rounded px-3 py-2"><?= mha_escape($error) ?></p>
            <?php endif; ?>

            <?php if ($tokenInfo !== null): ?>
            <form method="post" class="space-y-4">
                <input type="hidden" name="token" value="<?= mha_escape($token) ?>">
                <div>
                    <label for="password" class="block text-xs text-gray-500 mb-1">Mot de passe (min. 8 caractères)</label>
                    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"
                        class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm focus:border-red-500 outline-none">
                </div>
                <div>
                    <label for="password_confirm" class="block text-xs text-gray-500 mb-1">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password"
                        class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm focus:border-red-500 outline-none">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-red-900 hover:bg-red-700 text-red-100 font-bold rounded text-sm transition">
                    Créer mon compte et télécharger
                </button>
            </form>
            <?php else: ?>
            <a href="/flashdev.html"
               class="block w-full py-3 text-center border border-gray-700 hover:border-red-500 text-gray-300 rounded text-sm transition">
                Retour à FlashDev
            </a>
            <?php endif; ?>
        <?php endif; ?>

        <p class="mt-6 text-center text-xs text-gray-600">
            Déjà un mot de passe ?
            <a href="/auth/login.php?redirect=%2Fflashdev.html" class="text-gray-400 hover:text-red-400">Se connecter</a>
        </p>
    </div>
</body>
</html>
