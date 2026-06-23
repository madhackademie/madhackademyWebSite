# Note — Script `sync-both.ps1`

> Dernière mise à jour : 23 juin 2026  
> Emplacement : `scripts/sync-both.ps1` (identique dans les deux dépôts)

---

## À quoi sert ce script ?

Compare **local vs remote** pour les deux dépôts du workspace, puis propose un **pull coordonné** :

| Dépôt | Rôle |
|-------|------|
| `madhackademyWebSite` | Site web |
| `FlashRevisionSoft` | Logiciel FlashDev |

Pour chaque repo, le script affiche :

- branche et upstream (`origin/main`, etc.)
- `git fetch origin`
- retard / avance vs remote
- dernier commit local vs remote
- modifications locales non committées
- proposition de pull si des commits sont disponibles

---

## Utilisation

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\madhackademyWebSite
.\scripts\sync-both.ps1
```

Si PowerShell bloque les `.ps1` (`PSSecurityException`), utilise le lanceur :

```powershell
.\scripts\sync-both.cmd
```

Ou depuis FlashRevisionSoft :

```powershell
cd D:\CentreFormationMadHackAdemyInternetSite\FlashRevisionSoft
.\scripts\sync-both.ps1
```

---

## Exemples

### Comparer + demander pull (o/n)

```powershell
.\scripts\sync-both.ps1
```

Flux typique :

```
=== SITE ===
Sync      : en retard (retard: 2, avance: 0)
HEAD local  : abc1234 ancien commit
HEAD remote : def5678 commit recent

=== SOFT ===
Sync      : a jour (retard: 0, avance: 0)

Pull les depots en retard ? (o/n): o
[SITE] Pull OK.
```

### Pull automatique sans question

```powershell
.\scripts\sync-both.ps1 -Pull
```

### Comparer seulement (simulation fetch/pull)

```powershell
.\scripts\sync-both.ps1 -DryRun
```

### Comparer sans fetch (offline)

```powershell
.\scripts\sync-both.ps1 -NoFetch
```

Utile sans réseau : compare avec la dernière info fetch déjà en cache.

---

## Options

| Option | Description |
|--------|-------------|
| `-Pull` | Pull automatique sans demander `(o/n)` |
| `-DryRun` | Affiche le rapport sans fetch/pull réel |
| `-NoFetch` | Pas de `git fetch` (utilise le cache Git local) |

---

## États affichés

| État | Signification |
|------|---------------|
| `a jour` | Local = remote |
| `en retard` | Des commits remote ne sont pas encore en local → pull possible |
| `en avance` | Tu as des commits locaux non poussés |
| `diverge` | Retard **et** avance → merge manuel requis |
| `pas de branche distante` | Pas de upstream configuré |

---

## Sécurité (pull refusé si)

| Situation | Comportement |
|-----------|--------------|
| Modifications locales non committées | Pull **ignoré** pour ce repo |
| Branche divergée | Pull **ignoré** — résolution manuelle |
| Déjà à jour | Rien à pull |

Le pull utilise `git pull --ff-only` (fast-forward uniquement, pas de merge surprise).

---

## Workflow recommandé

```powershell
# 1. Recuperer les dernieres versions
.\scripts\sync-both.ps1

# 2. Travailler...

# 3. Committer et pousser
.\scripts\commit-both.ps1
```

---

## Dépannage

| Problème | Solution |
|----------|----------|
| `fetch echoue` | Vérifier connexion, accès GitHub, credentials |
| `Pull ignore : divergee` | `git pull --rebase` ou merge manuel dans le repo concerné |
| `Pull ignore : modifications locales` | Committer ou stasher avant pull |
| `pas de branche distante` | `git push -u origin main` une première fois |

---

## Fichiers liés

| Fichier | Contenu |
|---------|---------|
| `scripts/commit-both.ps1` | Commit + push coordonnés |
| `scripts/NOTE_COMMIT-BOTH.md` | Doc commit-both |
| `NOTE_SETUP_WORKSPACE.md` | Setup machine from scratch |
