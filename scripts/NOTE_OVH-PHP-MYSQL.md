# Guide OVH — PHP + MySQL pour MadHackAdemy (gameopenmoney.com)

> Guide complet pour héberger l’auth PHP et MySQL du site sur **OVH Web Cloud Databases** ou **hébergement mutualisé OVH**  
> Projet : madhackademyWebSite — guides protégés, comptes admin/testeur  
> Dernière mise à jour : 29 juin 2026

**Documents liés :**

| Doc | Contenu |
|-----|---------|
| [`NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md) | Checklist globale prochaine session |
| [`NOTE_AUTH-SETUP.md`](NOTE_AUTH-SETUP.md) | Rôles, URLs auth, fichiers PHP |
| [`NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md`](NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md) | Cartes Frogger, guides, `URLNet` |

---

## 1. Ce dont vous avez besoin chez OVH

### Offre compatible

| Offre OVH | PHP | MySQL | Adapté au projet ? |
|-----------|-----|-------|-------------------|
| **Hébergement Web** (Perso, Pro, Performance, Kimsufi Web…) | ✅ | ✅ (inclus) | **Oui — recommandé** |
| **Hosting Starter** (ancien) | ✅ | ✅ | Oui |
| **Domaine seul** (sans hébergement) | ❌ | ❌ | Non — il faut un hébergement |
| **VPS / Public Cloud** | ✅ (à installer) | ✅ (à installer) | Oui mais plus technique |

> Ce guide cible l’**hébergement mutualisé OVH** (cas le plus courant pour gameopenmoney.com).

### Prérequis avant de commencer

- [ ] Accès à l’[Espace client OVH](https://www.ovh.com/manager/)
- [ ] Domaine **gameopenmoney.com** attaché à l’hébergement
- [ ] Identifiants **FTP** (login + mot de passe + serveur)
- [ ] Dépôt local à jour : dossier `WebSite/` du repo madhackademyWebSite

---

## 2. Vue d’ensemble du déploiement OVH

```
Espace client OVH
  ├── Multisite          → domaine → dossier www/
  ├── Bases de données   → créer MySQL + user
  ├── phpMyAdmin         → importer schema.sql
  └── FTP                → uploader WebSite/

Sur le FTP (racine www/)
  ├── api/config.php     ← secrets MySQL (jamais sur Git)
  ├── auth/              ← login, guide, setup
  ├── guides/cards/      ← cartes + *Guide/
  └── gamedevready-bases-cpp.html
```

---

## 3. Étape 1 — Activer PHP 8+ sur OVH

### 3.1 Vérifier la version PHP

1. Connectez-vous à [https://www.ovh.com/manager/](https://www.ovh.com/manager/)
2. **Hébergements** → cliquez sur votre hébergement
3. Onglet **Multisite** → repérez **gameopenmoney.com**
4. Notez le **Dossier racine** (souvent `www` ou `www/gameopenmoney`)

### 3.2 Choisir PHP 8.1 ou 8.2

**Méthode A — Fichier `.ovhconfig` (recommandé)**

Créez ou éditez le fichier `.ovhconfig` **à la racine du site** (même niveau que `index.html`) :

```ini
app.engine=php
app.engine.version=8.2
http.firewall=none
environment=production
```

Uploadez-le via FTP dans le dossier racine web (`www/`).

**Méthode B — Panneau OVH**

1. Hébergement → **Configuration générale** (ou **Modules et extensions** selon l’interface)
2. Version PHP du multisite → **PHP 8.2** (minimum **8.1**)
3. Valider — propagation : quelques minutes

### 3.3 Test rapide PHP

Créez temporairement `phpinfo.php` à la racine :

```php
<?php phpinfo();
```

Ouvrez `https://gameopenmoney.com/phpinfo.php` → vérifiez **PHP Version ≥ 8.1**.

⚠️ **Supprimez ce fichier immédiatement après le test** (faille de sécurité).

---

## 4. Étape 2 — Créer la base MySQL chez OVH

### 4.1 Création dans l’espace client

1. **Hébergements** → votre hébergement
2. Onglet **Bases de données**
3. **Créer une base de données**
4. Choisissez **MySQL** (MariaDB sur OVH — compatible)
5. Type : selon votre offre (généralement la version proposée par défaut, ex. MySQL 8.0)
6. Notez précieusement :

| Info | Exemple OVH | Votre valeur |
|------|-------------|--------------|
| **Nom de la base** | `gameo123.madhackademy` | ______________ |
| **Utilisateur** | `gameo123.admin` | ______________ |
| **Mot de passe** | *(celui que vous définissez)* | ______________ |
| **Serveur / Host** | `gameo123.mysql.db` | ______________ |

> Sur OVH mutualisé, les noms sont **préfixés** par votre identifiant de compte (`gameo123` etc.). Ce n’est pas une erreur.

### 4.2 Host : `localhost` ou `xxx.mysql.db` ?

| Valeur | Quand l’utiliser |
|--------|------------------|
| **`xxx.mysql.db`** | Connexion depuis le **site PHP sur OVH** — **utilisez celle affichée dans le panneau** |
| `localhost` | Parfois accepté sur le même serveur — si ça échoue, repassez sur `xxx.mysql.db` |
| `127.0.0.1` | Idem — préférez le host OVH officiel |

Dans `api/config.php`, mettez **exactement** le serveur indiqué dans l’espace client OVH.

---

## 5. Étape 3 — Importer le schéma SQL (phpMyAdmin OVH)

### 5.1 Accéder à phpMyAdmin

1. Espace client → **Hébergements** → **Bases de données**
2. Cliquez sur **…** à droite de votre base → **Accéder à phpMyAdmin**
3. Connectez-vous (identifiants = ceux de l’étape 4)

### 5.2 Importer `schema.sql`

1. Dans phpMyAdmin, sélectionnez votre base à gauche
2. Onglet **Importer**
3. **Choisir un fichier** → `WebSite/sql/schema.sql` (depuis votre PC)
4. Format : SQL
5. **Exécuter**

Tables créées :

- `users` — comptes admin / testeur / élève
- `user_products` — accès formation (futur paiement)

### 5.3 Vérification

Onglet **Structure** → vous devez voir `users` et `user_products`.

---

## 6. Étape 4 — Configuration `api/config.php`

### 6.1 Créer le fichier localement

1. Copiez `WebSite/api/config.example.php` → `WebSite/api/config.php`
2. Adaptez avec vos valeurs OVH :

```php
<?php
return [
    'db' => [
        'host' => 'VOTRE_ID.mysql.db',      // ex. gameo123.mysql.db
        'name' => 'VOTRE_ID.madhackademy',  // nom complet de la base OVH
        'user' => 'VOTRE_ID.admin',         // utilisateur MySQL OVH
        'pass' => 'VOTRE_MOT_DE_PASSE',
        'charset' => 'utf8mb4',
    ],
    'setup_key' => 'une-longue-chaine-secrete-unique-2026',
    'product_slug' => 'gamedevready-bases-cpp',
];
```

### 6.2 Règles de sécurité

- [ ] `config.php` **ne doit jamais** être commité sur Git (déjà dans `.gitignore`)
- [ ] Uploadez-le **uniquement par FTP**
- [ ] `setup_key` : minimum 20 caractères aléatoires

---

## 7. Étape 5 — Upload FTP (FileZilla ou gestionnaire OVH)

### 7.1 Connexion FTP OVH

| Paramètre | Où le trouver |
|-----------|---------------|
| **Hôte** | `ftp.cluster0XX.hosting.ovh.net` (onglet **FTP-SSH** de l’hébergement) |
| **Utilisateur** | `gameo123` ou `gameo123-login` |
| **Mot de passe** | Celui défini pour FTP |
| **Port** | 21 (FTP) ou 22 (SFTP si activé) |

### 7.2 Dossier de destination

Multisite → dossier racine de **gameopenmoney.com** :

```
/home/gameo123/www/          ← cas le plus fréquent
ou
/home/gameo123/www/gameopenmoney/
```

**Le contenu de `WebSite/`** va **directement dedans** (pas le dossier `WebSite` lui-même) :

```
www/
├── index.html
├── gamedevready-bases-cpp.html
├── api/
│   ├── bootstrap.php
│   └── config.php          ← créé à l’étape 6
├── auth/
│   ├── login.php
│   ├── logout.php
│   ├── guide.php
│   └── setup.php           ← à supprimer après install
├── sql/                    ← optionnel sur FTP
├── guides/
│   └── cards/
│       ├── .htaccess
│       ├── 01-printf.html …
│       └── 01_PrintFGuide/ … 07_StructMethodesGuide/
└── Image/
```

### 7.3 Fichiers `.htaccess`

OVH active **mod_rewrite** par défaut sur l’hébergement mutualisé.

Vérifiez que `guides/cards/.htaccess` est bien uploadé (bloque l’accès direct aux `.html` dans les dossiers `*Guide/`).

---

## 8. Étape 6 — HTTPS (SSL) sur OVH

1. Espace client → **Hébergements** → **Multisite**
2. Domaine gameopenmoney.com → **Modifier le domaine**
3. **SSL** → certificat **Let’s Encrypt** (gratuit) → Activer
4. Attendre 15 min à 2 h la propagation
5. Tester : `https://gameopenmoney.com` (cadenas vert)

> Les cookies de session PHP sont configurés en `secure` si HTTPS est détecté — le login fonctionne mieux en HTTPS.

---

## 9. Étape 7 — Créer les comptes admin et testeurs

1. Ouvrez dans le navigateur :

```
https://gameopenmoney.com/auth/setup.php?key=VOTRE_SETUP_KEY
```

(`VOTRE_SETUP_KEY` = valeur de `setup_key` dans `config.php`)

2. Créez **votre compte** :
   - Email : votre email
   - Mot de passe : 8+ caractères
   - Rôle : **admin**

3. Créez un compte par testeur :
   - Rôle : **tester**

4. **Supprimez `auth/setup.php` du FTP** (sécurité)

---

## 10. Étape 8 — Tests de validation

| # | Action | Résultat attendu |
|---|--------|------------------|
| 1 | `https://gameopenmoney.com/auth/login.php` | Page de connexion s’affiche |
| 2 | Login avec compte admin | Redirection OK |
| 3 | `gamedevready-bases-cpp.html` → **Ouvrir le guide** (module 01) | Guide complet + images |
| 4 | Déconnexion `/auth/logout.php` | Session terminée |
| 5 | **Ouvrir le guide** sans login | Redirection vers login |
| 6 | URL directe `…/guides/cards/01_PrintFGuide/printfC++FrogTheme.html` | **403 Forbidden** |
| 7 | Modules 02–07 via `?m=02` … `?m=07` | Guides OK si connecté |

---

## 11. Dépannage OVH (problèmes fréquents)

### « Configuration manquante : copiez config.example.php »

- `api/config.php` absent ou mauvais chemin FTP
- Vérifiez : `www/api/config.php` existe

### « SQLSTATE[HY000] [2002] Connection refused » ou « Access denied »

| Cause | Solution |
|-------|----------|
| Mauvais host | Utilisez `xxx.mysql.db` du panneau OVH, pas un host générique |
| Mauvais nom de base | Nom **complet** avec préfixe (`gameo123.madhackademy`) |
| Mauvais user/pass | Recréez le mot de passe MySQL dans l’espace client si besoin |
| Base pas encore active | Attendre 5–10 min après création |

### Page PHP affichée en texte brut (code visible)

- PHP non activé → vérifier `.ovhconfig` ou version PHP dans le panneau
- Fichier `.php` uploadé au mauvais endroit

### Erreur 500 Internal Server Error

1. Consultez les **logs** : Espace client → Hébergement → **Logs et statistiques** → **Erreurs**
2. Causes fréquentes :
   - Syntaxe PHP dans `config.php`
   - Extension PDO MySQL manquante (rare sur OVH — normalement OK)
   - `.htaccess` incompatible — tester en renommant temporairement `guides/cards/.htaccess`

### `.htaccess` ne bloque pas les guides (accès direct encore possible)

- Vérifiez que `mod_rewrite` est actif (OVH mutualisé : oui par défaut)
- Le fichier doit être dans `guides/cards/.htaccess`
- Testez avec un `.htaccess` minimal :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.+Guide)/.+\\.html?$ - [F,L,NC]
</IfModule>
```

### Session / login ne persiste pas

- Vérifiez HTTPS (cookie `secure`)
- Videz cache navigateur
- Testez en navigation privée

### Limite taille import phpMyAdmin

Si `schema.sql` est petit (notre cas) : pas de souci.  
Pour de gros imports futurs : compressez en `.sql.gz` ou importez par morceaux.

---

## 12. Structure des identifiants OVH (aide-mémoire)

À remplir une fois, garder en lieu sûr (gestionnaire de mots de passe) :

```
=== OVH Espace client ===
Identifiant NIC : _______________

=== FTP ===
Serveur : ftp.cluster___.hosting.ovh.net
Login   : _______________
Pass    : _______________
Dossier racine site : _______________

=== MySQL ===
Host    : _______________.mysql.db
Base    : _______________.madhackademy
User    : _______________.admin
Pass    : _______________

=== Auth MadHackAdemy ===
setup_key (avant suppression setup.php) : _______________
Email admin : _______________
Email testeur 1 : _______________
```

---

## 13. Maintenance OVH

| Tâche | Fréquence |
|-------|-----------|
| Sauvegarde base (export phpMyAdmin) | Avant chaque grosse modif |
| Vérifier logs erreurs PHP | Après déploiement |
| Renouveler SSL Let’s Encrypt | Automatique OVH |
| Mettre à jour PHP (8.2 → 8.3…) | 1×/an — tester `.ovhconfig` |
| Rotation mots de passe MySQL/FTP | Recommandé annuellement |

---

## 14. Prochaines étapes (après auth OK)

1. Distribuer identifiants aux testeurs beta
2. Cocher la checklist dans [`TODO.md`](../TODO.md)
3. Brancher paiement (Stripe / System.io) → table `user_products`
4. Lier FlashDev (`URLNet`, token API) — voir [`NOTE_ARCHITECTURE_SOFT-SITE.md`](../NOTE_ARCHITECTURE_SOFT-SITE.md)

---

## 15. Support OVH

- [Documentation hébergement web OVH](https://help.ovhcloud.com/csm/fr-web-hosting-getting-started)
- [Créer une base de données](https://help.ovhcloud.com/csm/fr-web-hosting-database-creation)
- [Activer PHP](https://help.ovhcloud.com/csm/fr-web-hosting-configuring-web-hosting-database)
- Support : Espace client → **Créer un ticket** (catégorie Hébergement web)

---

*Fin du guide OVH — revenir à [`NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md) §3 pour la checklist complète.*
