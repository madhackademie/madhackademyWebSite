# Guide d'installation — FlashDev MadHackademy (élève)

> Dernière mise à jour : 16 juillet 2026  
> Public : **utilisateur final** (élève / bootcamp), pas le développeur du site.  
> Script associé : `scripts/install-madhackademy.cmd`

---

## Ce que tu installes

| Composant | Rôle |
|-----------|------|
| **LÖVE 11.x** | Moteur qui exécute FlashDev (le soft de révision) |
| **VS Code** | Éditeur pour ouvrir les exercices depuis une carte (**Start**) |
| **Extensions Lua** | Autocomplétion Love2D + débogage dans VS Code |

Les bibliothèques Lua du soft (`json`, `utf8`, `lib/class`, etc.) sont **déjà incluses** dans `SquelletteGCS/` — rien à installer via LuaRocks.

> **Deck C++ (Raylib)** : la chaîne `gcc` / `C:\raylib\` n'est **pas** dans l'installeur socle. Voir [§ Raylib (deck C++ uniquement)](#raylib-deck-c-uniquement).

---

## Installation automatique (recommandée)

### 1. Obtenir FlashDev

**Téléchargement web (élève)** : page [gameopenmoney.com/flashdev.html](https://gameopenmoney.com/flashdev.html) → bouton **Télécharger l'installeur** → `FlashDev-Setup-X.Y.Z-win64.exe`.

### 2. Lancer le setup

Double-clic sur l'exe → assistant Inno Setup → Finish.

Le setup :
1. Copie FlashDev dans `%LOCALAPPDATA%\FlashDev`
2. Vérifie LÖVE / VS Code (déjà installés = **skip** ; sinon `winget`)
3. Installe les extensions Lua manquantes
4. Propose de lancer FlashDev

Si tu développes depuis le dépôt Git :

```powershell
cd "C:\chemin\vers\FlashRevisionSoft"
.\scripts\install-madhackademy.ps1
```

Options utiles (script seul) :

```powershell
.\scripts\install-madhackademy.ps1 -DryRun
.\scripts\install-madhackademy.ps1 -VerifyOnly
```

### 3. Vérifier

```powershell
.\scripts\install-madhackademy.ps1 -VerifyOnly
```

Cases attendues :

```text
[OK] LÖVE installé
[OK] VS Code + commande code
[OK] Extensions Lua
[OK] FlashDev (SquelletteGCS/main.lua)
```

### Comportement si LÖVE / VS Code déjà présents

Le post-install **ne réinstalle pas** :
- LÖVE détecté dans `C:\Program Files\LOVE\love.exe` → `[OK] Deja installe`
- VS Code / `code` dans le PATH → `[OK] VS Code detecte`
- Extensions Lua déjà là → skip ; sinon ajoutées

Seuls les composants manquants sont installés.
---

## Installation manuelle (secours)

Si `winget` est indisponible ou bloqué par l'école / l'entreprise :

### LÖVE 11.1

1. Télécharger : https://love2d.org/
2. Installer (emplacement par défaut : `C:\Program Files\LOVE\`)
3. Test : glisser-déposer le dossier `SquelletteGCS` sur `love.exe`

### VS Code

1. Télécharger : https://code.visualstudio.com/
2. Cocher **« Ajouter au PATH »** pendant l'installation
3. Test dans un **nouveau** terminal :

```powershell
code --version
```

### Extensions VS Code (Lua / Love2D)

Dans VS Code → Extensions, installer :

| Extension | ID |
|-----------|-----|
| Lua (sumneko) | `sumneko.lua` |
| Local Lua Debugger | `tomblind.local-lua-debugger-vscode` |

En ligne de commande :

```powershell
code --install-extension sumneko.lua
code --install-extension tomblind.local-lua-debugger-vscode
```

### Ouvrir le projet dans VS Code

```powershell
cd "C:\chemin\vers\FlashRevisionSoft"
code .
```

VS Code propose d'installer les extensions recommandées du dossier `.vscode/`.

---

## Lancer FlashDev

### Méthode 1 — Raccourci Bureau

Après l'installation : double-clic sur **FlashDev MadHackademy**.

### Méthode 2 — Script

```powershell
cd "C:\chemin\vers\FlashRevisionSoft"
.\scripts\run-love.ps1
```

### Méthode 3 — LÖVE directement

```powershell
& "C:\Program Files\LOVE\love.exe" "C:\chemin\vers\FlashRevisionSoft\SquelletteGCS"
```

### Méthode 4 — Depuis VS Code (développement)

1. Ouvrir le dossier `FlashRevisionSoft` dans VS Code
2. **Run and Debug** (F5) → **LÖVE2D — avec console** ou **sans console**

---

## Parcours typique (carte exercice)

1. Lancer **FlashDev**
2. Choisir une carte → **Start**
3. Le soft ouvre l'explorateur **et** VS Code sur le dossier exercice (`URLSoft: "code"` dans les decks)
4. Tu codes dans VS Code, tu reviens dans FlashDev → **Done**

> Si VS Code ne s'ouvre pas au **Start**, la commande `code` n'est pas dans le PATH : relancer `install-madhackademy.cmd` ou rouvrir le terminal après installation de VS Code.

---

## Raylib (deck C++ uniquement)

Pour les exercices **GameDevReady Bootcamp C++**, il faut en plus :

- **Raylib + w64devkit** extraits dans `C:\raylib\`
- Extension **C/C++** : `ms-vscode.cpptools`

Ce n'est **pas** installé par le socle v1. Voir `scripts/NOTE_INSTALL-MADHACKADEMY.md` (section Raylib) pour la procédure complète ou une future extension de l'installeur.

---

## Dépannage

| Problème | Solution |
|----------|----------|
| `winget` introuvable | Installer [App Installer](https://apps.microsoft.com/detail/9NBLGGH4NNS1) depuis le Microsoft Store, ou suivre l'installation manuelle |
| Script bloqué (exécution) | `Set-ExecutionPolicy -Scope CurrentUser RemoteSigned` puis relancer `.cmd` |
| `Love2D introuvable` | Réinstaller LÖVE ; vérifier `C:\Program Files\LOVE\love.exe` |
| `code` introuvable | Réinstaller VS Code avec PATH ; ou relancer `install-madhackademy.ps1` |
| VS Code sans autocomplétion `love.` | Installer `sumneko.lua` ; redémarrer VS Code |
| F5 ne lance pas le jeu | Vérifier le chemin LÖVE dans `.vscode/launch.json` (`C:/Program Files/LOVE/`) |
| Start n'ouvre pas VS Code | `Get-Command code` doit réussir dans PowerShell |
| Antivirus bloque winget / zip | Autoriser l'exception ou installation manuelle |

---

## Checklist rapide

```text
[ ] FlashRevisionSoft décompressé / cloné
[ ] install-madhackademy.cmd exécuté sans erreur
[ ] Raccourci Bureau lance le menu FlashDev
[ ] VS Code s'ouvre (code --version)
[ ] Extensions Lua installées
[ ] (Optionnel) Deck installé via menu « explore deck »
[ ] (Optionnel C++) Raylib dans C:\raylib\
```

---

## Documents liés

| Fichier | Contenu |
|---------|---------|
| `scripts/install-madhackademy.ps1` | Script d'installation |
| `scripts/NOTE_INSTALL-MADHACKADEMY.md` | Architecture installeur (développeur) |
| `madhackademyWebSite/NOTE_SETUP_WORKSPACE.md` | Setup **développeur** (Git, Cursor, deux dépôts) |
