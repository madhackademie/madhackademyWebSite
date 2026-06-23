# Note — Setup workspace MadHackAdemy (depuis zero)

> Dernière mise à jour : 23 juin 2026  
> Objectif : reproduire sur une **nouvelle machine** le même environnement que  
> `CentreFormationMadHackAdemyInternetSite/` (site + soft + workspace Cursor).

---

## Resultat attendu

```
CentreFormationMadHackAdemyInternetSite/
├── madhackademyWebSite/          # Site web (gameopenmoney.com)
│   ├── WebSite/
│   ├── scripts/commit-both.ps1
│   └── madhackademy.code-workspace
└── FlashRevisionSoft/            # Logiciel FlashDev (Lua / LÖVE2D)
    ├── SquelletteGCS/
    ├── scripts/commit-both.ps1
    └── madhackademy.code-workspace
```

Dans Cursor : **deux dossiers** ouverts ensemble via `madhackademy.code-workspace`.

---

## 1. Prérequis (installer une fois)

| Outil | Lien | Obligatoire |
|-------|------|-------------|
| Git | https://git-scm.com/ | Oui |
| Cursor | https://cursor.com/ | Oui |
| LÖVE 11.1 | https://love2d.org/ | Non (pour lancer le soft) |

---

## 2. Configuration Git (Windows, une fois)

Chemins longs requis pour `FlashRevisionSoft` :

```powershell
git config --global core.longpaths true
git config --global user.name "Ton Nom"
git config --global user.email "ton@email.com"
```

Autoriser les scripts PowerShell locaux (pour `commit-both.ps1`) :

```powershell
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
```

---

## 3. Creer le dossier parent et cloner les deux depots

Choisir un emplacement (ex. disque `D:`). Adapter le chemin si besoin.

```powershell
# Creer le dossier parent
mkdir D:\CentreFormationMadHackAdemyInternetSite
cd D:\CentreFormationMadHackAdemyInternetSite

# Cloner les deux depots GitHub (cote a cote, meme niveau)
git clone https://github.com/madhackademie/madhackademyWebSite.git
git clone https://github.com/madhackademie/FlashRevisionSoft.git
```

Verifier la structure :

```powershell
Get-ChildItem
# Doit afficher : madhackademyWebSite, FlashRevisionSoft
```

---

## 4. Mettre a jour les depots (sync local vs remote)

A faire apres le clone, ou a chaque session de travail :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
.\scripts\sync-both.ps1
```

Le script :
- `git fetch` sur les deux repos
- compare local / remote (retard, avance, diverge)
- demande `Pull les depots en retard ? (o/n)`

Pull automatique sans question :

```powershell
.\scripts\sync-both.ps1 -Pull
```

Doc : `scripts/NOTE_SYNC-BOTH.md`

---

## 5. Ouvrir le workspace dans Cursor

Depuis le depot site :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
cursor madhackademy.code-workspace
```

**Alternative sans CLI :**

1. Ouvrir Cursor
2. **File → Open Workspace from File…**
3. Choisir `madhackademyWebSite\madhackademy.code-workspace`

**Verification :** l'explorateur affiche :

- `Site — madhackademyWebSite`
- `Soft — FlashRevisionSoft`

---

## 6. Verifier que tout fonctionne

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
.\scripts\commit-both.ps1 -DryRun
```

Sortie attendue (exemple) :

```
Workspace MadHackAdemy
Parent : D:\CentreFormationMadHackAdemyInternetSite
Site   : D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
Soft   : D:\CentreFormationMadHackAdemyInternetSite\FlashRevisionSoft
=== Statut ===
...
```

---

## 7. Previsualiser le site en local (optionnel)

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite\WebSite
python -m http.server 8080
```

Navigateur : http://localhost:8080/

---

## 8. Lancer FlashDev (optionnel)

Si LÖVE 11.1 est installe et dans le PATH :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\FlashRevisionSoft\SquelletteGCS
love .
```

Ou glisser-deposer le dossier `SquelletteGCS` sur `love.exe`.

---

## 9. Workflow Git quotidien (deux depots)

Synchroniser avec GitHub (compare + pull) :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
.\scripts\sync-both.ps1
```

Pull automatique sans question :

```powershell
.\scripts\sync-both.ps1 -Pull
```

Documentation : `scripts/NOTE_SYNC-BOTH.md`

Commit coordonne (demande push o/n a la fin) :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
.\scripts\commit-both.ps1
```

Messages separes (feature site + soft) :

```powershell
.\scripts\commit-both.ps1 `
  -SiteMessage "feat(api): endpoint sync" `
  -SoftMessage "feat(sync): client HTTP Lua"
```

Push automatique sans question :

```powershell
.\scripts\commit-both.ps1 -Message "chore: maintenance" -Push
```

Documentation detaillee : `scripts/NOTE_COMMIT-BOTH.md`

---

## 10. Cas : le site est deja clone, pas le soft

Si tu as deja `madhackademyWebSite` ouvert ailleurs :

```powershell
# 1. Remonter au dossier parent du site
cd D:\chemin\vers\madhackademyWebSite
cd ..

# 2. Cloner le soft AU MEME NIVEAU que le site
git clone https://github.com/madhackademie/FlashRevisionSoft.git

# 3. Pull les deux
cd madhackademyWebSite
git pull
cd ..\FlashRevisionSoft
git pull

# 4. Ouvrir le workspace
cd ..\madhackademyWebSite
cursor madhackademy.code-workspace
```

**Regle :** les deux dossiers doivent etre freres, pas l'un dans l'autre.

---

## 11. Extensions Cursor recommandees

Proposees automatiquement par le workspace :

| Extension | Usage |
|-----------|-------|
| `sumneko.lua` | FlashRevisionSoft |
| `ms-vscode.cpptools` | Exercices Raylib C++ |
| `bmewburn.vscode-intelephense-client` | Futur API PHP |

---

## 12. Depannage rapide

| Probleme | Commande / action |
|----------|-------------------|
| `Depot soft introuvable` | Verifier noms et emplacement cote a cote |
| Script bloque | `Set-ExecutionPolicy -Scope CurrentUser RemoteSigned` |
| Erreur chemin trop long | `git config --global core.longpaths true` puis re-cloner |
| Workspace n'affiche qu'un dossier | Rouvrir via **Open Workspace from File** |
| `cursor` inconnu | Reinstaller Cursor avec "Add to PATH" coche |

---

## 13. Checklist setup complet

```
[ ] Git installe
[ ] core.longpaths true (Windows)
[ ] Dossier parent cree
[ ] git clone madhackademyWebSite
[ ] git clone FlashRevisionSoft
[ ] git pull x2
[ ] cursor madhackademy.code-workspace
[ ] commit-both.ps1 -DryRun OK
[ ] (optionnel) python -m http.server pour le site
[ ] (optionnel) love . pour FlashDev
```

---

## 14. Documents lies

| Fichier | Contenu |
|---------|---------|
| `scripts/NOTE_COMMIT-BOTH.md` | Utilisation du script commit/push |
| `scripts/NOTE_SYNC-BOTH.md` | Comparaison local/remote + pull |
| `NOTE_ARCHITECTURE_SOFT-SITE.md` | Lien site ↔ soft (API, sync) |
| `TODO.md` | Taches prioritaires du site |
| `NOTE_RECAPITULATIVE.md` | Etat actuel des pages HTML |
