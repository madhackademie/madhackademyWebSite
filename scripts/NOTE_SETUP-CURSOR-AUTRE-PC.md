# Note — Reproduire la config Cursor sur l'autre ordinateur

> Dernière mise à jour : 16 juillet 2026  
> Objectif : remettre sur l'ordinateur de bureau la meme configuration Cursor que sur ce PC, pour travailler dans le meme environnement.

---

## 1. Ce qui est configure ici

### Workspace ouvert dans Cursor

Le projet est ouvert via :

```text
madhackademyWebSite/madhackademy.code-workspace
```

Ce workspace ouvre bien **2 dossiers** :

- `Site — madhackademyWebSite`
- `Soft — FlashRevisionSoft`

### Reglages utilisateur Cursor observes sur ce PC

Fichier :

```text
C:\Users\laure\AppData\Roaming\Cursor\User\settings.json
```

Contenu actuel :

```json
{
  "window.commandCenter": true,
  "files.autoSave": "afterDelay",
  "window.autoDetectColorScheme": false
}
```

### Raccourcis clavier perso observes sur ce PC

Fichier :

```text
C:\Users\laure\AppData\Roaming\Cursor\User\keybindings.json
```

Contenu actuel :

```json
[
  {
    "key": "ctrl+i",
    "command": "composerMode.agent"
  }
]
```

### Extensions Cursor installees sur ce PC

Liste observee :

- `actboy168.lua-debug`
- `tomblind.local-lua-debugger-vscode`

### Extensions recommandees par le workspace

Le workspace recommande aussi :

- `sumneko.lua`
- `ms-vscode.cpptools`
- `bmewburn.vscode-intelephense-client`

Si elles ne sont pas presentes sur l'autre PC, les installer aussi pour aligner l'environnement.

---

## 2. Ce qu'il faut refaire sur l'ordinateur de bureau

### Etape 1 — Ouvrir le bon workspace

Dans Cursor :

1. `File`
2. `Open Workspace from File...`
3. Choisir :

```text
madhackademyWebSite\madhackademy.code-workspace
```

Verification attendue dans l'explorateur :

- `Site — madhackademyWebSite`
- `Soft — FlashRevisionSoft`

Si un seul dossier apparait, ce n'est pas la bonne ouverture.

### Etape 2 — Remettre les reglages utilisateur

Ouvrir le fichier :

```text
C:\Users\laure\AppData\Roaming\Cursor\User\settings.json
```

et remplacer son contenu par :

```json
{
  "window.commandCenter": true,
  "files.autoSave": "afterDelay",
  "window.autoDetectColorScheme": false
}
```

### Etape 3 — Remettre le raccourci clavier perso

Ouvrir le fichier :

```text
C:\Users\laure\AppData\Roaming\Cursor\User\keybindings.json
```

et verifier qu'il contient :

```json
[
  {
    "key": "ctrl+i",
    "command": "composerMode.agent"
  }
]
```

### Etape 4 — Installer les extensions utiles

Dans Cursor, installer au minimum :

- `actboy168.lua-debug`
- `tomblind.local-lua-debugger-vscode`
- `sumneko.lua`
- `ms-vscode.cpptools`
- `bmewburn.vscode-intelephense-client`

---

## 3. Checklist rapide de verification

Quand tout est remis, verifier :

```text
[ ] Le workspace ouvre bien Site + Soft
[ ] Ctrl+I ouvre bien le mode agent
[ ] L'auto-save fonctionne
[ ] Les extensions Lua / C++ / PHP sont visibles dans Cursor
[ ] Les fichiers du projet s'ouvrent sans comportement different de ce PC
```

---

## 4. Divergence constatee : fenetre d'impression

Observation notee par Laurent :

- la fenetre de print s'est ouverte la premiere fois
- aux tentatives suivantes, elle avait disparu

Comme ce comportement n'apparait pas dans la config du projet, il faut le traiter comme un point de verification **machine / Cursor / navigateur**.

### Verification a faire sur le PC de bureau

Tester dans cet ordre :

1. Fermer completement Cursor puis relancer
2. Reouvrir le workspace
3. Refaire le meme test d'impression
4. Essayer aussi avec `Ctrl+P` si le print venait d'une page HTML
5. Verifier si la fenetre d'impression n'est pas ouverte derriere une autre fenetre
6. Si rien ne s'ouvre, redemarrer aussi le navigateur ou la webview utilisee

### Hypothese pratique

Le probleme ressemble plus a :

- une fenetre systeme qui s'ouvre hors ecran
- une preview d'impression qui reste bloquee ou cachee
- une difference entre le setup Cursor / navigateur du portable et celui du bureau

Ce point doit donc etre re-teste **apres** avoir aligne :

- le workspace
- les settings Cursor
- les keybindings
- les extensions

---

## 5. Si besoin : methode simple pour comparer les 2 PC

Sur chaque machine, comparer ces 3 choses :

### A. Fichier user settings

```text
C:\Users\laure\AppData\Roaming\Cursor\User\settings.json
```

### B. Fichier keybindings

```text
C:\Users\laure\AppData\Roaming\Cursor\User\keybindings.json
```

### C. Liste des extensions

Commande :

```powershell
cursor --list-extensions
```

Si ces 3 elements sont identiques, le setup Cursor sera quasiment le meme sur les deux ordinateurs.

---

## 6. Resultat attendu

L'objectif n'est pas seulement "ouvrir le projet", mais retrouver sur le PC de bureau :

- le meme workspace a 2 dossiers
- les memes reglages Cursor
- le meme raccourci `Ctrl+I`
- les memes extensions utiles
- un comportement identique pendant les tests, y compris pour l'impression
