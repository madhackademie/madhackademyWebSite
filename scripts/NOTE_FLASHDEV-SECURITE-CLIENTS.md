# FlashDev — Sécurité du téléchargement (clients / DSI)

> Document interne MadHackademy — texte réutilisable pour entreprises, écoles, testeurs.  
> Page publique : `/telecharger.html` (section « Intégrité et antivirus »).  
> Manifeste : `https://madhackademy.eu/flashdev/latest-version.json`

---

## Résumé (1 minute)

**FlashDev** est un logiciel de formation (révisions par cartes + LÖVE / VS Code). L’installeur Windows est produit par **Inno Setup** à partir du dépôt **FlashRevisionSoft** (scripts auditables).

- **Fichier officiel** : URL et empreinte **SHA-256** publiées dans `latest-version.json`.
- **Analyse multi-antivirus** : lien **VirusTotal** (même hash que l’installeur officiel).
- **Alertes type Norton `IDP.Generic`** : heuristique fréquente sur exe **non signé** qui lance PowerShell et installe LÖVE / VS Code — **pas** un nom de virus précis.
- **Signature Authenticode** : prévue lorsque le volume / revenus le justifieront (`FLASHDEV_SIGN_PFX` dans le build).

---

## Vérifier l’intégrité (service IT)

1. Télécharger **uniquement** depuis [madhackademy.eu/telecharger.html](https://madhackademy.eu/telecharger.html) (compte élève) ou l’URL exe du manifeste.
2. Comparer le hash :

```powershell
Get-FileHash ".\FlashDev-Setup-X.Y.Z-win64.exe" -Algorithm SHA256
```

3. Valeur attendue : champ **`installerSha256`** du manifeste  
   `https://madhackademy.eu/flashdev/latest-version.json`

Si les hash **coincident**, le fichier est **identique** à celui publié par MadHackademy (pas modifié en transit).

---

## VirusTotal (rapport multi-moteurs)

- URL du rapport (version courante) : champ **`virusTotalUrl`** du manifeste, ou  
  `https://www.virustotal.com/gui/file/<installerSha256>`
- **Première analyse** : un responsable MadHackademy ou le client peut **uploader** l’exe sur [virustotal.com](https://www.virustotal.com) ; le rapport reste lié au **hash** (réutilisable par tous).

Interprétation :

| Résultat | Signification |
|----------|----------------|
| 0 détection | Idéal |
| 1–3 détections **heuristiques** (ex. `IDP.Generic`, `Heur.AdvML.B`) | Faux positifs **courants** sur installeur non signé — croiser avec **SHA-256** et comportement ci-dessous |
| Détections multiples **nommées** (trojan connu) | Nous contacter avec le rapport ; comparer impérativement au hash officiel |

---

## Comportement de l’installeur (transparence)

Après copie des fichiers FlashDev :

| Étape | Action |
|-------|--------|
| Post-install | `install-madhackademy.ps1` (PowerShell, `-Unattended`) |
| LOVE | winget `Love2d.Love2d` ou zip portable GitHub officiel |
| VS Code | Téléchargement **Microsoft** (update.code.visualstudio.com) ou winget |
| Extensions | `sumneko.lua`, débogueur Lua (marketplace VS Code) |
| Réseau | Requis au **premier** setup uniquement |

Aucun rootkit, minage, ni téléchargement depuis un domaine autre que les sources ci-dessus (détail dans le dépôt : `scripts/install-madhackademy.ps1`).

---

## Antivirus d’entreprise (Norton, etc.)

Si l’exe est **supprimé** ou « infecté **IDP.Generic** » :

1. Restaurer depuis la **quarantaine** si présent.
2. Ajouter une **exclusion** sur le fichier ou le dossier d’install (`%LOCALAPPDATA%\FlashDev` ou `C:\FlashDev`).
3. Vérifier le **SHA-256** officiel avant exécution.
4. **Plan B — zip installeur** (sans `.exe`) : `FlashDev-Setup-X.Y.Z-win64.zip` sur le site (champ `installerPortableZipUrl` du manifeste) — extraire → exclusion Norton sur le dossier → `Install-Socle.cmd`.

**Important** : le zip `FlashRevisionSoft-*-win64.zip` (`downloadUrl`) = **mise à jour du jeu** dans le menu Options, **pas** la première installation complète.

Le zip installeur contient les **mêmes scripts `.ps1`** : Norton peut encore alerter, mais le **téléchargement** du `.exe` est parfois évité.

Soumission faux positif éditeur :

- [Norton](https://submit.norton.com/)
- [Microsoft Defender](https://www.microsoft.com/en-us/wdsi/filesubmission)

Joindre : hash SHA-256, lien VirusTotal, version, URL manifeste.

---

## Version 0.2.14 (exemple — mettre à jour à chaque release)

| Élément | Valeur |
|---------|--------|
| Installeur | `FlashDev-Setup-0.2.14-win64.exe` |
| SHA-256 | `46452bbc2f9e0ba912330a2b4443bfa67bf90c5fd14374bbe83bb09c5b008958` |
| Taille | 5 473 906 octets |
| VirusTotal | `https://www.virustotal.com/gui/file/46452bbc2f9e0ba912330a2b4443bfa67bf90c5fd14374bbe83bb09c5b008958` |

---

## Procédure MadHackademy à chaque release

1. Build + FTP exe / zip / `latest-version.json` (contient `installerSha256`, `virusTotalUrl`).
2. **Uploader** le même exe sur **VirusTotal** (compte MadHackademy) — une fois par hash.
3. Conserver capture ou lien du rapport pour les clients.
4. Soumettre faux positifs si Norton / autres remontent chez les testeurs.
5. (Futur) Signer l’exe avec certificat **Authenticode**.

---

## Contact

Support formation / technique : via le site MadHackademy (page contact / email support indiqué sur la vitrine).
