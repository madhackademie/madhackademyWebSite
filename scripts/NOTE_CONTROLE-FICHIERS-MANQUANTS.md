# Note — Contrôle fichiers manquants après sync (ex. `update.png`)

> Dernière mise à jour : 16 juillet 2026  
> Cas d'usage : une machine (bureau / portable) n'a pas des fichiers présents sur l'autre, alors qu'ils ont été commités et poussés.

---

## Contexte — cas `update.png` (juillet 2026)

Les images UI du bouton **update** sont dans le dépôt **FlashRevisionSoft**, pas dans le site :

| Fichier | Chemin |
|---------|--------|
| Normal | `FlashRevisionSoft/SquelletteGCS/images_current/buton_Choix-assets/update.png` |
| Survol | `FlashRevisionSoft/SquelletteGCS/images_current/buton_Choix-assets/update Surv.png` |

Commit de référence : `978bcde` — *mise a jour linkage avec soft* (15 juil. 2026).

**Piège fréquent :** un `git pull` dans le dossier **site** ne récupère **rien** du soft. Les deux dépôts sont frères ; il faut synchroniser **les deux**.

---

## Étape 1 — Essai rapide (à faire en premier)

Depuis **n'importe lequel** des deux dépôts :

```powershell
cd C:\MadHackAdemyWebSite\madhackademyWebSite
.\scripts\sync-both.cmd
```

Ou :

```powershell
cd C:\MadHackAdemyWebSite\FlashRevisionSoft
.\scripts\sync-both.cmd
```

Répondre **o** si le script propose un pull.

### Vérification visuelle immédiate

```powershell
dir "C:\MadHackAdemyWebSite\FlashRevisionSoft\SquelletteGCS\images_current\buton_Choix-assets\update*.png"
```

**OK** → les deux fichiers s'affichent. **Sinon** → passer à l'étape 2.

---

## Étape 2 — Contrôle structuré (si fichiers toujours absents)

Cocher au fur et à mesure.

### 2.1 Workspace Cursor — les 2 dossiers sont ouverts ?

Le workspace doit ouvrir **deux** racines :

- `madhackademyWebSite`
- `FlashRevisionSoft`

Fichier : `madhackademyWebSite/madhackademy.code-workspace`

Si seul le site est ouvert, tu peux croire avoir « tout pull » alors que le soft n'a pas bougé.

---

### 2.2 Dépôt SOFT — état Git

```powershell
cd C:\MadHackAdemyWebSite\FlashRevisionSoft
git branch --show-current
git status --short
git fetch origin
git log --oneline -n 5
git log --oneline -n 3 origin/main
```

| Vérification | Attendu | Si différent |
|--------------|---------|--------------|
| Branche | `main` | `git switch main` (après stash/commit des modifs locales) |
| `git status` | propre ou seulement `data.json` / notes locales | voir § 2.4 |
| Dernier commit local | contient `978bcde` ou plus récent | pull manquant → § 2.3 |
| `origin/main` | même HEAD que local (après fetch) | `git pull --ff-only` |

Vérifier que le commit des images est bien dans l'historique :

```powershell
git log --oneline -- "SquelletteGCS/images_current/buton_Choix-assets/update.png"
```

Doit afficher au moins : `978bcde mise a jour linkage avec soft`.

---

### 2.3 Pull refusé par sync-both ?

`sync-both` **n'pull pas** si :

- modifications locales non committées dans ce repo ;
- branche **divergée** (en retard **et** en avance vs remote).

Diagnostic :

```powershell
cd C:\MadHackAdemyWebSite\FlashRevisionSoft
git status
git rev-list --left-right --count main...origin/main
```

| Sortie `left-right` | Signification | Action |
|---------------------|---------------|--------|
| `0  N` (N > 0) | en retard | `git pull --ff-only` |
| `M  0` (M > 0) | en avance | push depuis la machine source, puis sync ici |
| `M  N` (tous deux > 0) | divergé | merge ou rebase manuel — ne pas forcer |
| `0  0` | à jour côté Git | fichier absent → § 2.5 |

Si **modifications locales** bloquent le pull :

```powershell
git stash push -m "avant sync controle update.png"
git pull --ff-only
git stash pop
```

Ou, pour un fichier de données local seulement :

```powershell
git restore SquelletteGCS/data.json
git pull --ff-only
```

---

### 2.4 Dépôt SITE — ne pas confondre

Le site n'a **pas** `update.png`. Contrôle rapide pour confirmer que le pull site n'est pas le sujet :

```powershell
cd C:\MadHackAdemyWebSite\madhackademyWebSite
git status --short
git log --oneline -n 3
```

Utile seulement si tu suspectes un workspace ou un chemin de clone incorrect.

---

### 2.5 Fichier suivi par Git mais absent sur disque

Rare, mais possible après conflit ou suppression locale :

```powershell
cd C:\MadHackAdemyWebSite\FlashRevisionSoft
git ls-files "SquelletteGCS/images_current/buton_Choix-assets/update.png"
git checkout HEAD -- "SquelletteGCS/images_current/buton_Choix-assets/update.png" "SquelletteGCS/images_current/buton_Choix-assets/update Surv.png"
```

Puis revérifier :

```powershell
dir "SquelletteGCS\images_current\buton_Choix-assets\update*.png"
```

---

### 2.6 Mauvais chemin de clone sur la machine de bureau

Vérifier que tu regardes le **bon** dossier (pas une ancienne copie) :

```powershell
cd C:\MadHackAdemyWebSite\FlashRevisionSoft
git remote -v
git rev-parse --show-toplevel
```

Remote attendu : dépôt **FlashRevisionSoft** sur GitHub (pas le repo site).

Comparer le hash HEAD avec la machine où ça marche :

```powershell
git rev-parse HEAD
```

Doit correspondre au portable si les deux sont à jour sur `main`.

---

## Étape 3 — Validation dans le soft

1. Lancer FlashDev depuis `FlashRevisionSoft/SquelletteGCS/` (ou ton raccourci habituel).
2. Ouvrir le menu où le bouton **update** doit apparaître.
3. Si l'image manque : erreur Love2D du type *Could not open file* sur `update.png` — revenir à l'étape 2.5.

---

## Synthèse des causes les plus fréquentes

| # | Cause | Symptôme | Fix |
|---|-------|----------|-----|
| 1 | Pull seulement sur le **site** | soft inchangé | `sync-both` (les 2 repos) |
| 2 | Soft sur une **autre branche** | commit `978bcde` absent du log | `git switch main` + pull |
| 3 | **Modifs locales** bloquent le pull | sync-both dit « Pull ignoré » | stash / restore puis pull |
| 4 | **Ancien clone** ou mauvais dossier | remote ou HEAD différent | vérifier `git remote -v` et chemin |
| 5 | Fichier **supprimé localement** | `git ls-files` OK, `dir` KO | `git checkout HEAD -- <fichier>` |

---

## Fichiers liés

| Fichier | Contenu |
|---------|---------|
| `scripts/NOTE_SYNC-BOTH.md` | Doc `sync-both` |
| `scripts/NOTE_COMMIT-BOTH.md` | Commit + push des deux repos |
| `scripts/NOTE_SETUP-CURSOR-AUTRE-PC.md` | Workspace 2 dossiers sur l'autre PC |
| `FlashRevisionSoft/scripts/NOTE_UPDATE-WORKFLOW.md` | Release / zip (autre sujet que le sync Git) |

---

## Après résolution

- [ ] `dir update*.png` OK sur la machine de bureau
- [ ] `git status` soft acceptable (pas de blocage pour la prochaine session)
- [ ] Test rapide du bouton update dans le soft

Si le problème persiste après cette checklist : noter la sortie exacte de `sync-both`, `git status`, `git log -n 3` et `git rev-list --left-right --count main...origin/main` (soft) pour diagnostic en session.
