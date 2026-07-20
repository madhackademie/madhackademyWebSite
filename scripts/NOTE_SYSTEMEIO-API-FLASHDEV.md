# Note — API Systeme.io → FlashDev (opt-in téléchargement)

> Dernière mise à jour : 20 juillet 2026  
> **Priorité :** intégrer l’opt-in directement sur `flashdev.html` / site (design libre), contacts dans Systeme.io.  
> Vocabulaire : on parle surtout d’**API** (créer un contact), pas d’un webhook entrant Systeme.io.

---

## Objectif

```
Page FlashDev (HTML flex libre sur gameopenmoney.com)
   → formulaire POST vers /api/systeme-optin.php
      → PHP appelle https://api.systeme.io/api/contacts
      → contact créé dans Systeme.io
      → automation envoie l’email avec le lien de téléchargement
```

- **Design** = ton site (plus de bride éditeur Systeme.io)  
- **Liste email + automation** = Systeme.io  
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

- [ ] Clé créée et copiée
- [ ] Emplacement prévu dans `config.php` (ex. `SYSTEME_IO_API_KEY`)

### 2) Tag (optionnel mais utile)

Dans Systeme.io, créer un tag ex. `flashdev-download`.

Automation typique : **Tag ajouté** → envoyer l’email avec le lien de téléchargement.

Checklist :

- [ ] Tag `flashdev-download` créé
- [ ] (Plus tard) appel API pour associer le tag au contact

### 3) Schéma chez toi

```
Page FlashDev (ton HTML flex)
   → formulaire POST vers /api/systeme-optin.php
      → PHP appelle https://api.systeme.io/api/contacts
      → contact créé dans Systeme.io
      → automation envoie l’email
```

Checklist :

- [ ] Emplacement page : intégrer sur / près de `flashdev.html` (pas seulement Systeme.io)
- [ ] Endpoint prévu : `WebSite/api/systeme-optin.php`

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

- [ ] `curl` OK
- [ ] Contact visible dans Systeme.io

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

- [ ] Endpoint PHP créé + déployé FTP
- [ ] Clé lue depuis `config.php`
- [ ] Gestion erreur / email déjà existant
- [ ] Consentement checkbox vérifié côté serveur

### 6) Automation Systeme.io

1. Automation : **quand un contact est créé** (ou tag `flashdev-download`)
2. Action : **envoyer un email** avec le lien download
3. Option : double opt-in si tu veux être strict GDPR

Lien download typique (à trancher selon stratégie accès) :

- `https://gameopenmoney.com/flashdev.html`  
  ou URL directe installeur sous `/flashdev/`

Checklist :

- [ ] Automation créée et testée
- [ ] Email reçu avec le bon lien
- [ ] Copy email aligné (téléchargement soft, pas vente formation)

### 7) Formulaire sur ta page

```html
<form method="post" action="/api/systeme-optin.php">
  <input type="email" name="email" required placeholder="ton@email.com">
  <label>
    <input type="checkbox" name="consent" required>
    J’accepte de recevoir des emails de FlashDev…
  </label>
  <button type="submit">Recevoir le lien de téléchargement</button>
</form>
```

Design = HTML flex (snippet / `flashdev.html`). Systeme.io = uniquement boîte mail + liste.

Checklist :

- [ ] Formulaire intégré à la page FlashDev (site)
- [ ] Plus de dépendance au builder Systeme.io pour le design
- [ ] Test bout en bout : submit → contact → email → lien

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
| GDPR | Case consentement + désinscription dans les emails Systeme.io |
| Suite possible | Tag → automation ; plus tard : créer / activer compte site (lien magique) = autre chantier |

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

- [ ] Étape 1 — Clé API
- [ ] Étape 2 — Tag
- [ ] Étape 3 — Schéma / fichiers
- [ ] Étape 4 — Test curl
- [ ] Étape 5 — `systeme-optin.php`
- [ ] Étape 6 — Automation email
- [ ] Étape 7 — Formulaire sur page FlashDev
- [ ] Test prod bout en bout
