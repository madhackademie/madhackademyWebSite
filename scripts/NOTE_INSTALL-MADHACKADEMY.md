# Note — Options d'installateur MadHackademy (FlashDev + VS Code + Raylib)

> Dernière mise à jour : 28 juin 2026  
> Emplacement : `scripts/NOTE_INSTALL-MADHACKADEMY.md` (identique dans les deux dépôts)  
> Complète : `madhackademyWebSite/NOTE_SETUP_WORKSPACE.md` (setup développeur depuis zéro)

---

## Objectif

Proposer un **lanceur / installateur** qui prépare la machine d'un **élève** (pas du développeur) en une étape :

1. **FlashDev** — logiciel de révision (`FlashRevisionSoft` / LÖVE2D)
2. **VS Code** — éditeur pour ouvrir les exercices
3. **Raylib + w64devkit** — chaîne de compilation C++ des exercices GameDevReady

Le flux pédagogique existe déjà dans le soft : bouton **Start** → ouverture du dossier exercice + lancement de VS Code via le champ `URLSoft` dans `data.json`.

---

## Écosystème actuel (ce qui existe déjà)

| Composant | Rôle | Fichiers clés |
|-----------|------|---------------|
| FlashDev | App flashcards, répétition espacée | `SquelletteGCS/main.lua`, `data.json` |
| Lancement exercice | Ouvre dossier + VS Code | `SquelletteGCS/state/currentProjectMenu.lua` |
| Decks additionnels | Installation de contenu | `SquelletteGCS/state/deckInstaller.lua` |
| Exercices Raylib C++ | Projets avec makefile + `.vscode/` | `SquelletteGCS/elements_revisions/RaylibC++/` |
| Cartes web (URLNet) | Fiches HTML sur gameopenmoney.com | voir `NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md` |

### Comportement au clic « Start »

```lua
-- currentProjectMenu.lua (résumé)
love.system.openURL(folderBaseAddress .. URLFolder)   -- explorateur
os.execute('code "' .. dossier_exercice .. '"')       -- si URLSoft == "code"
```

Les cartes Raylib ont `"URLSoft": "code"` dans `data.json`.

---

## Les 3 briques à installer chez l'élève

| Brique | Usage | Emplacement attendu aujourd'hui |
|--------|-------|----------------------------------|
| **LÖVE 11.x** | Exécuter FlashDev | `C:\Program Files\LOVE\love.exe` (référencé dans `.vscode/launch.json`) |
| **VS Code** | Éditer / compiler les exercices | `code` doit être dans le **PATH** |
| **Raylib + w64devkit** | gcc, gdb, mingw32-make | **`C:\raylib\`** (chemins en dur dans tous les `.vscode/` Raylib) |

### Extensions VS Code recommandées

| Extension | ID | Obligatoire pour Raylib |
|-----------|-----|-------------------------|
| C/C++ | `ms-vscode.cpptools` | Oui |
| C/C++ Extension Pack | `ms-vscode.cpptools-extension-pack` | Optionnel |

Installation silencieuse :

```powershell
code --install-extension ms-vscode.cpptools
```

---

## Options d'implémentation

### Option A — Script PowerShell + lanceur `.cmd` (recommandé pour MVP)

Même modèle que `sync-both.ps1` / `commit-both.ps1`.

**Fichiers envisagés :**

```
scripts/
├── install-madhackademy.ps1
├── install-madhackademy.cmd      # bypass PSSecurityException
└── NOTE_INSTALL-MADHACKADEMY.md  # cette note
```

**Flux :**

1. Vérifier LÖVE, VS Code, `C:\raylib\`, présence de FlashDev
2. Télécharger / installer ce qui manque (`winget`, zip officiel, ou miroir FTP)
3. Ajouter `code` au PATH si absent
4. Installer l'extension C/C++
5. Copier ou cloner `FlashRevisionSoft` si besoin
6. Créer un raccourci Bureau « FlashDev MadHackademy »

| Avantages | Inconvénients |
|-----------|---------------|
| Rapide à écrire, versionné dans Git | Moins « pro » visuellement |
| Cohérent avec les scripts existants | Nécessite PowerShell (déjà le cas) |
| Facile à maintenir / déboguer | Élève voit une console |

**Effort estimé :** 1–2 jours pour un MVP fiable.

---

### Option B — Installateur Windows (Inno Setup / NSIS)

Un fichier `.exe` avec assistant graphique, barre de progression, icône Bureau, entrée « Ajouter / Supprimer des programmes ».

**Flux :**

1. Écran d'accueil MadHackademy
2. Cases à cocher : LÖVE / VS Code / Raylib / FlashDev
3. Téléchargement et extraction silencieuse
4. Raccourcis + désinstallation propre

| Avantages | Inconvénients |
|-----------|---------------|
| Expérience professionnelle pour l'élève | Plus de travail initial (3–5 jours) |
| Pas de console visible | Binaire à recompiler à chaque changement |
| Signature de code possible (SmartScreen) | Certificat code signing = coût annuel |

**Effort estimé :** 3–5 jours + tests sur machines vierges.

---

### Option C — Wizard au premier lancement (dans FlashDev / LÖVE)

Écran LÖVE « Première installation » qui appelle des scripts PowerShell en arrière-plan.

| Avantages | Inconvénients |
|-----------|---------------|
| Tout reste dans l'app | LÖVE ne gère pas bien les droits admin |
| Pas de fichier séparé à distribuer | Téléchargements fragiles depuis Lua |
| | UX confuse si l'install échoue mid-game |

**Recommandation :** à éviter en première version ; envisager plus tard comme **écran de vérification** (« tout est OK ? ») plutôt qu'installateur complet.

---

## Schéma cible (Option A ou B)

```
install-madhackademy.cmd
        │
        ▼
   ┌─────────┐
   │ Checks  │
   └────┬────┘
        ├── LÖVE installé ?
        ├── VS Code + code dans PATH ?
        ├── C:\raylib\ présent ?
        ├── Extension cpptools ?
        └── FlashDev présent ?
        │
        ▼ (si manquant)
   ┌─────────────────────────────────────┐
   │ winget / zip LOVE                   │
   │ Installer VS Code + PATH            │
   │ Extraire raylib + w64devkit → C:\   │
   │ git clone ou zip FlashRevisionSoft  │
   └─────────────────────────────────────┘
        │
        ▼
   Raccourci Bureau → love.exe SquelletteGCS
```

---

## Points d'attention (défis techniques)

### 1. Chemins Raylib figés

Tous les exercices `RaylibC++/BasicCppRaylib/*/.vscode/` pointent vers :

```
C:/raylib/w64devkit/bin/gdb.exe
C:/raylib/w64devkit/bin/gcc.exe
C:/raylib/w64devkit/bin/mingw32-make.exe
C:/raylib/raylib/src/**
```

**Décision à prendre :**

| Stratégie | Action |
|-----------|--------|
| **A — Imposer `C:\raylib\`** (simple) | L'installateur extrait toujours à cet emplacement |
| **B — Chemin utilisateur** | Changer tous les `.vscode/` → `%USERPROFILE%\raylib\` (gros refactor) |
| **C — Template à l'install** | Générer `.vscode/` depuis un modèle avec le chemin détecté |

→ Pour le MVP : **stratégie A**.

### 2. `code` dans le PATH

Sans PATH, `URLSoft: "code"` échoue silencieusement dans `currentProjectMenu.lua`.

Chemins typiques VS Code :

```
%LOCALAPPDATA%\Programs\Microsoft VS Code\bin\code.cmd
C:\Program Files\Microsoft VS Code\bin\code.cmd
```

Le script doit **tester** `Get-Command code` et sinon ajouter au PATH utilisateur ou patcher `URLSoft` avec le chemin complet.

### 3. Droits administrateur

| Composant | Admin requis ? |
|-----------|----------------|
| LÖVE (installateur officiel) | Souvent non |
| VS Code (User install) | Non |
| `C:\raylib\` | **Souvent oui** → alternative : `%USERPROFILE%\raylib\` |
| Git (si clone) | Non |

### 4. Taille et réseau

Raylib + w64devkit ≈ **300–500 Mo**. Prévoir :

- miroir sur `gameopenmoney.com` (comme les cartes HTML), ou
- lien direct vers les releases officielles raylib

### 5. Antivirus / SmartScreen

Les kits MinGW déclenchent parfois des faux positifs. Documenter une procédure « autoriser l'exception » si besoin.

### 6. Mises à jour

| Composant | Stratégie |
|-----------|-----------|
| FlashDev | `git pull` ou nouveau zip + script update |
| Decks | déjà géré par `deckInstaller.lua` |
| Raylib / VS Code | hors scope MVP ; winget upgrade plus tard |

---

## Checklist de vérification post-install

À exécuter manuellement ou automatiser dans le script :

```powershell
# LÖVE
Test-Path "C:\Program Files\LOVE\love.exe"

# VS Code
Get-Command code

# Raylib
Test-Path "C:\raylib\w64devkit\bin\gcc.exe"
Test-Path "C:\raylib\raylib\src\raylib.h"

# FlashDev
Test-Path "...\FlashRevisionSoft\SquelletteGCS\main.lua"

# Test compilation (exercice Hello_Raylib)
cd "...\01_Hello_Raylib"
C:\raylib\w64devkit\bin\mingw32-make.exe PROJECT_NAME=test
```

---

## Arborescence cible chez l'élève

```
C:\
├── raylib\                          # imposé par les .vscode Raylib
│   ├── w64devkit\bin\gcc.exe
│   └── raylib\src\
├── Program Files\
│   ├── LOVE\love.exe
│   └── Microsoft VS Code\           # ou %LOCALAPPDATA%\Programs\...
└── Users\<eleve>\MadHackademy\       # emplacement suggéré pour le soft
    └── FlashRevisionSoft\
        └── SquelletteGCS\
```

Le dossier parent peut être ajusté ; seul **`C:\raylib\`** est contraint par le contenu pédagogique actuel.

---

## Liens avec la doc existante

| Document | Lien |
|----------|------|
| Setup workspace (dev) | `madhackademyWebSite/NOTE_SETUP_WORKSPACE.md` |
| Déploiement cartes web | `madhackademyWebSite/scripts/NOTE_DEPLOIEMENT-FTP-GAMEDEVREADY.md` |
| Architecture soft ↔ site | `madhackademyWebSite/NOTE_ARCHITECTURE_SOFT-SITE.md` |
| Sync des deux dépôts | `scripts/NOTE_SYNC-BOTH.md` |

---

## Recommandation

| Phase | Action |
|-------|--------|
| **Phase 1 (MVP)** | Option A — `install-madhackademy.ps1` + checks + raccourci |
| **Phase 2** | Héberger zip Raylib + FlashDev sur FTP pour élèves sans Git |
| **Phase 3** | Option B — installateur `.exe` si distribution grand public |
| **Phase 4** | Écran « vérification environnement » dans FlashDev (Option C light) |

---

## Prochaines étapes (TODO technique)

- [ ] Créer `install-madhackademy.ps1` avec fonctions `Test-Love`, `Test-VSCode`, `Test-Raylib`, `Test-FlashDev`
- [ ] Définir source de téléchargement Raylib (officiel vs miroir FTP)
- [ ] Script de création raccourci : `love.exe` + chemin vers `SquelletteGCS`
- [ ] Tester sur une VM Windows vierge (sans outils préinstallés)
- [ ] Documenter procédure manuelle de secours (fallback si script bloqué)
- [ ] (Optionnel) Page sur gameopenmoney.com « Télécharger l'environnement MadHackademy »

---

## Commandes utiles (référence winget)

```powershell
# LÖVE
winget install --id LOVE.LOVE -e

# VS Code
winget install --id Microsoft.VisualStudioCode -e

# Vérifier
winget list LOVE.LOVE
winget list Microsoft.VisualStudioCode
```

Raylib n'est pas toujours disponible via winget avec w64devkit — prévoir **téléchargement zip manuel** depuis [raylib.com](https://www.raylib.com/) ou copie depuis une machine déjà configurée.
