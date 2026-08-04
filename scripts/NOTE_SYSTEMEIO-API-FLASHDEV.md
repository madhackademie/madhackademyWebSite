# Note — API Systeme.io → FlashDev (opt-in téléchargement)

> Dernière mise à jour : 2 août 2026  
> **Priorité :** CRM Systeme.io + email Bienvenue ; compte site immédiat (choix **A**).  
> Vocabulaire : on parle surtout d’**API** (créer un contact), pas d’un webhook entrant Systeme.io.  
> **Décision UX (2/08/2026) :** **pas de formulaire capture Systeme.io** (design bridé). Opt-in = **formulaire site** + API.  
> **Décision produit (2/08 — choix A) :**  
> → Compte site **immédiat** : opt-in → MySQL + `flashdev-soft` → `set-password` → download (sans attendre le mail).  
> → Systeme.io = **CRM** (contacts, séquences, vidéos formation) : contact + tag `flashdev-download` **obligatoires** avant compte site.  
> → Email = automation **Tag ajouté** → **Bienvenue** (lien login), **pas** DOI natif (impossible via API).  
> → GDPR = case consentement site + désinscription dans les emails Systeme.io.  
> → Retest : **email neuf** pour voir contact créé + mail ; même email = pas de 2ᵉ mail (tag déjà présent).

---

## Objectif (choix A — actuel)

```
Page FlashDev (madhackademy.eu)
   → formulaire POST vers /api/systeme-optin.php
      → PHP : POST /contacts (+ tag flashdev-download)
      → si sync OK → MySQL user + flashdev-soft → set-password → download
      → automation Systeme.io : Tag ajouté → email Bienvenue (CRM / suite)
```

- **Design** = ton site  
- **Liste + emails nurture / vidéos** = Systeme.io  
- Clé API = **uniquement** dans `config.php` (FTP), **jamais** dans Git / HTML

---

## Docs officielles

| Doc | URL |
|-----|-----|
| Créer une clé API | https://help.systeme.io/article/2323-how-to-create-a-public-api-key-on-systeme-io |
| Utiliser l’API publique | https://help.systeme.io/article/2329-how-to-use-systeme-io-public-api |
| Référence API | https://developer.systeme.io/reference/api |
| Créer un contact (POST) | https://developer.systeme.io/reference/post_contact-1 |

Snippet HTML capture (référence visuelle) : [`WebSite/systeme-io-capture-snippet.html`](../WebSite/systeme-io-capture-snippet.html)

---

## Tuto pas à pas (étapes 1 → 7)

### 1) Clé API Systeme.io

1. Photo de profil → **Paramètres**
2. **Public API keys** → **Créer**
3. Nom : ex. `FlashDev-site`
4. **Copier la clé tout de suite** (tu ne la reverras plus)
5. Ne jamais la mettre dans le HTML / Git — seulement côté serveur (`api/config.php` sur FTP)

Checklist :

- [x] Clé créée et copiée
- [x] Emplacement dans `config.php` sur FTP (`systeme_io_api_key`) — voir aussi `config.example.php` dans le repo

### 2) Tag (optionnel mais utile)

Dans Systeme.io, créer un tag ex. `flashdev-download`.

Automation typique : **Tag ajouté** → envoyer l’email avec le lien de téléchargement.

Checklist :

- [x] Tag `flashdev-download` créé
- [ ] Appel API pour associer le tag au contact (requis pour déclencher l’automation « Tag ajouté »)

### 3) Schéma chez toi

```
Page FlashDev (ton HTML flex)
   → formulaire POST vers /api/systeme-optin.php
      → PHP appelle https://api.systeme.io/api/contacts
      → contact créé dans Systeme.io
      → automation envoie l’email
```

Checklist :

- [x] Emplacement page : formulaire intégré sur `flashdev.html` (gate accès)
- [x] Endpoint : `WebSite/api/systeme-optin.php`
- [x] Pages résultat : `merci-flashdev.html`, `optin-email-existe.html`
- [x] Charte visuelle partagée : `flashdev-optin.css`

### 4) Appel API (cœur)

```http
POST https://api.systeme.io/api/contacts
Header: X-API-Key: TA_CLE
Header: Content-Type: application/json

{
  "email": "eleve@example.com",
  "locale": "fr"
}
```

Test rapide (PowerShell / cmd) :

```bash
curl -X POST "https://api.systeme.io/api/contacts" ^
  -H "Content-Type: application/json" ^
  -H "X-API-Key: TA_CLE" ^
  -d "{\"email\":\"test@example.com\",\"locale\":\"fr\"}"
```

Si OK → contact visible dans Systeme.io (souvent **201** Created).

Checklist :

- [x] `curl` / `Invoke-WebRequest` OK (clé valide, API répond)
- [x] Contact visible dans Systeme.io (test juillet 2026 — email existant → 422 « déjà utilisée »)

### 5) Côté PHP (esquisse)

Fichier cible : `WebSite/api/systeme-optin.php`

```php
<?php
// api/systeme-optin.php — esquisse
$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Email invalide');
}

$apiKey = /* depuis config.php, jamais dans Git */;

$payload = json_encode([
    'email'  => $email,
    'locale' => 'fr',
]);

$ch = curl_init('https://api.systeme.io/api/contacts');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'X-API-Key: ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
]);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 201 = créé ; gérer aussi "déjà existant" si l’API le renvoie
header('Location: /merci-flashdev.html'); // page merci chez toi
```

Checklist :

- [x] Endpoint PHP créé (`WebSite/api/systeme-optin.php`)
- [x] Clé lue depuis `config.php` (`systeme_io_api_key`)
- [x] Gestion erreur / email déjà existant → `optin-email-existe.html`
- [x] Consentement checkbox vérifié côté serveur
- [ ] Déployé sur FTP prod + test formulaire live
- [x] Tag `flashdev-download` ajouté via API après création contact (résolu par **nom**, 2/08)

### 6) Automation Systeme.io — email **Bienvenue** (choix A)

**Pas de DOI natif** sur ce tunnel (API uniquement). Le download ne dépend pas du mail.

1. Contact créé ou retrouvé (API) + tag `flashdev-download`  
2. Compte site immédiat (`set-password`)  
3. Automation : **Tag `flashdev-download` ajouté** → email **Bienvenue** (CRM / rappels / suite vidéos)

Lien dans l’email Systeme.io (page site, pas le .exe) :

```
https://madhackademy.eu/auth/login.php?redirect=%2Ftelecharger.html
```

Secours legacy (à retirer plus tard) :

```
https://madhackademy.eu/telecharger.html?dl=1
```

(`flashdev.html?dl=1` redirige aussi vers `telecharger.html?dl=1`.)
- Parcours principal : opt-in site → `set-password` → session → download (sans dépendre du mail)
- Email Systeme.io = Bienvenue / nurture ; lien → **login** puis FlashDev
- **Ne pas** mettre `/flashdev/go.php` ni `FlashDev-Setup-….exe` dans l’email
- Même email retesté : contact déjà là → tag souvent déjà présent → **pas de 2ᵉ envoi** (normal)

#### Checklist Laurent (UI Systeme.io + test prod)

- [ ] Automation : déclencheur **Tag `flashdev-download` ajouté** (règle existante OK)
- [ ] Copy email → **Bienvenue** (plus « voici le .exe ») : compte créé sur le site à l’inscription ; lien login ci-dessus
- [ ] Expéditeur `contact@madhackademy.eu` ; vérifier boîte + spams
- [ ] FTP : `api/systeme-optin.php` (sync contact + tag **obligatoires** avant compte)
- [ ] Test **email neuf** (absent Systeme.io + MySQL) → contact + tag + mail Bienvenue + set-password + download
- [ ] Test 2ᵉ fois même email (logout) → page « déjà un compte », pas de 2ᵉ mail

### 7) Formulaire sur ta page

```html
<form method="post" action="/api/systeme-optin.php">
  <input type="email" name="email" required placeholder="ton@email.com">
  <label>
    <input type="checkbox" name="consent" required>
    J’accepte de recevoir des emails de FlashDev…
  </label>
  <button type="submit">Créer mon compte et télécharger</button>
</form>
```

Après succès API : compte MySQL + acquis `flashdev-soft` → redirect `/auth/set-password.php?token=…`.  
Design = HTML site (`flashdev.html`). Systeme.io = liste + nurture email.

Checklist :

- [x] Formulaire intégré à la page FlashDev (site, cadre SNES)
- [x] Plus de dépendance au builder Systeme.io pour le design (lien externe retiré)
- [ ] Test bout en bout prod : submit → contact → tag → email → lien

---

## Ordre recommandé (implémentation)

1. Clé API + test `curl`
2. Voir le contact apparaître dans Systeme.io
3. Automation email
4. Coder `systeme-optin.php` proprement sur le site
5. Brancher le formulaire sur `flashdev.html` (ou page dédiée)
6. FTP + test prod

---

## Recommandations produit / marketing

| Point | Conseil |
|-------|---------|
| Où vivre la page | **Sur le site** (`flashdev.html` ou page capture dédiée), pas dans l’éditeur Systeme.io |
| Rôle de Systeme.io | Liste + emails automatiques — **pas** le design |
| Promesse | Opt-in = **lien téléchargement soft**, pas vente formation |
| Boucle gate | Pas de lien `flashdev.html` sur une page Systeme.io qui renvoie au gate login → boucle |
| Secrets | Clé API uniquement serveur ; rotation possible (max 3 clés) |
| GDPR | Case consentement site + désinscription emails (DOI natif API = non) |
| Suite possible | Séquences / vidéos formation via tags ; paiement → acquis formation site |

---

## Liens projet

| Doc | Rôle |
|-----|------|
| [`NOTE_AUTH-SETUP.md`](NOTE_AUTH-SETUP.md) | Login site / rôles |
| [`NOTE_ARCHITECTURE_SOFT-SITE.md`](../NOTE_ARCHITECTURE_SOFT-SITE.md) | Soft ↔ site, Systeme.io emails |
| [`NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md) | Priorité session |
| [`TODO.md`](../TODO.md) | Case P1 à cocher |
| `WebSite/flashdev.html` | Page téléchargement (cible d’intégration) |

---

## Statut

- [x] Étape 1 — Clé API
- [x] Étape 2 — Tag `flashdev-download` + association API
- [x] Étape 3 — Schéma / fichiers
- [x] Étape 4 — Test curl
- [x] Étape 5 — `systeme-optin.php` (choix A : contact + tag obligatoires, 422 = exists)
- [~] Étape 6 — Automation → copy **Bienvenue** (à aligner UI) + retest email neuf
- [x] Étape 7 — Formulaire sur `flashdev.html` + compte site immédiat
- [~] Test prod : sync CRM + mail Bienvenue (email neuf)

### Prochaine action (Laurent)

1. FTP `api/systeme-optin.php`
2. Aligner copy automation → Bienvenue + lien login
3. Tester avec un **email neuf**
