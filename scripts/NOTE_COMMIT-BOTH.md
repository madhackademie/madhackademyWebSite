# Note — Script `commit-both.ps1`

> Dernière mise à jour : 23 juin 2026  
> Emplacement : `scripts/commit-both.ps1` (identique dans les deux dépôts)

---

## À quoi sert ce script ?

Le workspace MadHackAdemy ouvre **deux dépôts Git séparés** :

| Dépôt | Rôle |
|-------|------|
| `madhackademyWebSite` | Site web + futur API PHP |
| `FlashRevisionSoft` | Logiciel FlashDev (Lua / LÖVE2D) |

Git ne permet **pas** un seul commit pour les deux. Ce script enchaîne :

1. Statut des deux repos
2. `git add` + `git commit` dans chaque repo modifié
3. **Question interactive** : `Push vers origin pour les depots committes ? (o/n)`
4. Push des repos concernés si tu réponds `o`, `oui`, `y` ou `yes`

---

## Prérequis

### Structure des dossiers

Les deux dépôts doivent être **côte à côte** :

```
CentreFormationMadHackAdemyInternetSite/
├── madhackademyWebSite/
│   └── scripts/commit-both.ps1
└── FlashRevisionSoft/
    └── scripts/commit-both.ps1
```

Le script remonte automatiquement d’un niveau (`scripts/` → repo → parent) pour trouver l’autre dépôt.

### PowerShell (Windows)

Si tu vois `PSSecurityException` / `l'exécution de scripts est désactivée` :

**Option A — lanceur .cmd (sans changer la config) :**

```powershell
.\scripts\commit-both.cmd
```

**Option B — autoriser les scripts une fois pour toutes :**

```powershell
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
```

**Option C — bypass ponctuel :**

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\commit-both.ps1
```

---

## Utilisation

Lancer depuis **n’importe lequel** des deux dépôts :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
.\scripts\commit-both.ps1
```

ou :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\FlashRevisionSoft
.\scripts\commit-both.ps1
```

---

## Exemples

### Mode interactif (par défaut)

Affiche le statut, puis demande un message pour chaque repo modifié :

```powershell
.\scripts\commit-both.ps1
```

### Messages séparés (feature transversale)

Quand site et soft changent pour la même fonctionnalité :

```powershell
.\scripts\commit-both.ps1 `
  -SiteMessage "feat(api): endpoint POST /api/sync/events" `
  -SoftMessage "feat(sync): envoi batch des evenements d'etude"
```

### Même message pour les deux

```powershell
.\scripts\commit-both.ps1 -Message "chore: alignement doc architecture soft-site"
```

### Push sans question (automatique)

Pour pousser directement sans la question `(o/n)` :

```powershell
.\scripts\commit-both.ps1 `
  -SiteMessage "fix: fermer div index.html" `
  -SoftMessage "fix: correction chemin deck" `
  -Push
```

### Flux interactif typique

```
Message commit SITE : fix: hero centre-formation
Message commit SOFT : (Entree si rien a committer cote soft)
[SITE] Commit OK.
Push vers origin pour les depots committes ? (o/n): o
[SITE] Push OK.
```

### Simulation (sans rien modifier)

```powershell
.\scripts\commit-both.ps1 -DryRun
```

Avec messages préremplis :

```powershell
.\scripts\commit-both.ps1 -DryRun -SiteMessage "test" -SoftMessage "test"
```

---

## Options

| Option | Description |
|--------|-------------|
| `-Message` | Message commun pour tous les repos modifiés |
| `-SiteMessage` | Message uniquement pour `madhackademyWebSite` |
| `-SoftMessage` | Message uniquement pour `FlashRevisionSoft` |
| `-Push` | Push automatique sans demander `(o/n)` |
| `-DryRun` | Affiche ce qui serait fait, sans committer |
| `-NoStage` | N’exécute pas `git add -A` (staging déjà fait à la main) |

**Règle :** ne pas combiner `-Message` avec `-SiteMessage` / `-SoftMessage`.

---

## Comportement détaillé

1. Vérifie que les deux dossiers `.git` existent
2. Affiche branche et fichiers modifiés par repo
3. Si les deux repos sont propres → arrêt immédiat
4. Pour chaque repo avec des changements :
   - `git add -A` (sauf `-NoStage`)
   - `git commit -m "..."` 
5. Si un repo n’a pas de message (mode interactif, Entrée vide) → commit ignoré pour ce repo
6. Demande `Push vers origin ? (o/n)` — ou push direct si `-Push`
7. Push uniquement les repos qui viennent d’être committés

---

## Bonnes pratiques

### Messages de commit coordonnés

Pour une feature qui touche les deux projets, privilégier des messages qui se comprennent mutuellement :

```
SITE : feat(api): reception des study_events
SOFT : feat(sync): envoi des study_events vers l'API
```

### Pull requests

- **Une PR par dépôt** sur GitHub
- Référencer l’autre PR dans la description si besoin

### Ce que le script ne fait pas

- Pas de merge entre repos
- Pas de tag synchronisé
- Pas de commit si le repo est déjà propre
- Pas de `--amend`, `--force`, ou skip hooks

---

## Ouvrir le workspace (autre machine)

1. Cloner les deux repos au même niveau :

```powershell
mkdir CentreFormationMadHackAdemyInternetSite
cd CentreFormationMadHackAdemyInternetSite
git clone https://github.com/madhackademie/madhackademyWebSite.git
git clone https://github.com/madhackademie/FlashRevisionSoft.git
```

2. (Windows) Activer les chemins longs pour FlashRevisionSoft :

```powershell
git config --global core.longpaths true
```

3. Ouvrir le workspace dans Cursor :

```powershell
cd madhackademyWebSite
cursor madhackademy.code-workspace
```

Le fichier `madhackademy.code-workspace` est aussi présent à la racine de `FlashRevisionSoft` (chemins inversés, même résultat).

---

## Dépannage

| Problème | Solution |
|----------|----------|
| `Depot site/soft introuvable` | Vérifier la structure côte à côte et les noms exacts des dossiers |
| Script bloqué par PowerShell | `Set-ExecutionPolicy -Scope CurrentUser RemoteSigned` |
| `git push a echoue` | Vérifier connexion, droits GitHub, branche à jour (`git pull` avant) |
| Un seul repo modifié | Normal : seul ce repo sera commité |

---

## Fichiers liés

| Fichier | Contenu |
|---------|---------|
| `madhackademy.code-workspace` | Workspace Cursor multi-dossiers |
| `scripts/sync-both.ps1` | Comparaison local/remote + pull |
| `scripts/NOTE_SYNC-BOTH.md` | Doc sync-both |
| `NOTE_ARCHITECTURE_SOFT-SITE.md` | Architecture site ↔ soft |
| `TODO.md` | Tâches prioritaires du site |
