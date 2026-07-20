# Auth — comptes admin / testeur / guides protégés

> Première mise en place : juin 2026  
> **Production OVH : juillet 2026** — déploiement, tests parcours, identifiants testeurs distribués  
> **OVH (maintenance / dépannage) :** [`NOTE_OVH-PHP-MYSQL.md`](NOTE_OVH-PHP-MYSQL.md)  
> **État projet :** [`NOTE_PROCHAINE-SESSION.md`](NOTE_PROCHAINE-SESSION.md)

---

## Rôles

| Rôle | Accès guides | Usage |
|------|--------------|-------|
| **admin** | Oui (tous) | Vous — gestion comptes via phpMyAdmin si besoin |
| **tester** | Oui (tous) | Testeurs beta |
| **student** | Si produit accordé | Futurs élèves payants |

---

## URLs

| Page | URL |
|------|-----|
| Connexion | `/auth/login.php` |
| Déconnexion | `/auth/logout.php` |
| Guide module 01 | `/auth/guide.php?m=01` |
| Guide module 02–07 | `/auth/guide.php?m=02` … `m=07` |

---

## Fichiers liés

| Fichier | Rôle |
|---------|------|
| `api/bootstrap.php` | Session, login, contrôle accès |
| `api/config.php` | Secrets MySQL (sur FTP uniquement, jamais sur Git) |
| `auth/guide.php` | Sert le HTML guide si autorisé |
| `Formations/BaseCpp/guides/.htaccess` | Bloque accès direct aux `.html` des guides |

---

## Prochaine étape (élèves payants)

Quand Stripe / System.io sera branché : webhook PHP → insert `user_products` pour rôle `student`.

Voir `NOTE_ARCHITECTURE_SOFT-SITE.md` §5 et §7.

## Opt-in téléchargement FlashDev (prioritaire)

Formulaire sur le site → API Systeme.io (créer contact) → email avec lien download.  
Tuto : [`NOTE_SYSTEMEIO-API-FLASHDEV.md`](NOTE_SYSTEMEIO-API-FLASHDEV.md).
