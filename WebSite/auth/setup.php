<?php
declare(strict_types=1);

/**
 * Installation initiale — créer les comptes admin / testeur.
 * 1. Copier api/config.example.php → api/config.php
 * 2. Importer sql/schema.sql dans MySQL
 * 3. Ouvrir /auth/setup.php?key=VOTRE_SETUP_KEY
 * 4. SUPPRIMER ce fichier du FTP après installation
 */

require_once __DIR__ . '/../api/bootstrap.php';

$config = mha_config();
$setupKey = $config['setup_key'] ?? '';
$providedKey = $_GET['key'] ?? $_POST['key'] ?? '';

$allowed = mha_has_role('admin') || ($setupKey !== '' && hash_equals($setupKey, $providedKey));
if (!$allowed) {
    http_response_code(403);
    exit('Accès refusé. Utilisez ?key=VOTRE_SETUP_KEY ou connectez-vous en admin.');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $displayName = trim($_POST['display_name'] ?? '');
    $role = $_POST['role'] ?? 'tester';
    $grantProduct = isset($_POST['grant_product']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif (strlen($password) < 8) {
        $error = 'Mot de passe : 8 caractères minimum.';
    } elseif (!in_array($role, ['admin', 'tester', 'student'], true)) {
        $error = 'Rôle invalide.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mha_db()->prepare(
                'INSERT INTO users (email, password_hash, display_name, role) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$email, $hash, $displayName ?: $email, $role]);
            $userId = (int) mha_db()->lastInsertId();

            if ($grantProduct && $role === 'student') {
                $product = $config['product_slug'] ?? 'gamedevready-bases-cpp';
                $stmt = mha_db()->prepare(
                    'INSERT INTO user_products (user_id, product_slug) VALUES (?, ?)'
                );
                $stmt->execute([$userId, $product]);
            }

            $message = "Compte créé : {$email} ({$role})";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'Cet email existe déjà.';
            } else {
                $error = 'Erreur base de données : ' . $e->getMessage();
            }
        }
    }
}

$users = mha_db()->query(
    'SELECT id, email, display_name, role, is_active, created_at FROM users ORDER BY id'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup comptes — MadHackAdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white font-mono min-h-screen px-4 py-10">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-red-500 mb-2">Création de comptes</h1>
        <p class="text-gray-400 text-sm mb-6">Admin et testeurs ont accès à tous les guides. Supprimez ce fichier après installation.</p>

        <?php if ($message !== ''): ?>
            <p class="mb-4 text-sm text-green-400 border border-green-900 bg-green-950/30 rounded px-3 py-2"><?= mha_escape($message) ?></p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <p class="mb-4 text-sm text-red-400 border border-red-900 bg-red-950/40 rounded px-3 py-2"><?= mha_escape($error) ?></p>
        <?php endif; ?>

        <form method="post" class="border border-gray-800 rounded-lg p-6 bg-gray-950 space-y-4 mb-10">
            <input type="hidden" name="key" value="<?= mha_escape($providedKey) ?>">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Email</label>
                <input type="email" name="email" required class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Mot de passe (8+ car.)</label>
                <input type="password" name="password" required minlength="8" class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Nom affiché</label>
                <input type="text" name="display_name" class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Rôle</label>
                <select name="role" class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-sm">
                    <option value="admin">admin — vous (accès total + setup)</option>
                    <option value="tester" selected>tester — testeur (accès guides)</option>
                    <option value="student">student — futur élève payant</option>
                </select>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-400">
                <input type="checkbox" name="grant_product" class="rounded">
                Accorder la formation (uniquement si rôle = student)
            </label>
            <button type="submit" class="w-full py-3 bg-red-900 hover:bg-red-700 text-red-100 font-bold rounded text-sm">Créer le compte</button>
        </form>

        <h2 class="text-lg font-bold mb-3">Comptes existants</h2>
        <div class="overflow-x-auto border border-gray-800 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-900 text-gray-400">
                    <tr>
                        <th class="text-left px-3 py-2">Email</th>
                        <th class="text-left px-3 py-2">Nom</th>
                        <th class="text-left px-3 py-2">Rôle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr class="border-t border-gray-800">
                        <td class="px-3 py-2"><?= mha_escape($u['email']) ?></td>
                        <td class="px-3 py-2"><?= mha_escape($u['display_name']) ?></td>
                        <td class="px-3 py-2 text-red-400"><?= mha_escape($u['role']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if ($users === []): ?>
                    <tr><td colspan="3" class="px-3 py-4 text-gray-500">Aucun compte — créez le premier ci-dessus.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="mt-6 text-xs text-gray-600">
            <a href="/auth/login.php" class="text-gray-400 hover:text-red-400">→ Page de connexion</a>
        </p>
    </div>
</body>
</html>
