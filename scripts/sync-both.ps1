#Requires -Version 5.1
<#
.SYNOPSIS
    Compare local vs remote et pull coordonne pour madhackademyWebSite + FlashRevisionSoft.

.EXAMPLE
    .\scripts\sync-both.ps1

.EXAMPLE
    .\scripts\sync-both.ps1 -Pull

.EXAMPLE
    .\scripts\sync-both.ps1 -DryRun
#>

[CmdletBinding()]
param(
    [switch]$Pull,
    [switch]$DryRun,
    [switch]$NoFetch
)

$ErrorActionPreference = "Stop"

function Get-WorkspacePaths {
    $scriptDir = Split-Path -Parent $PSCommandPath
    $repoRoot = Split-Path -Parent $scriptDir
    $parentDir = Split-Path -Parent $repoRoot

    $siteRepo = Join-Path $parentDir "madhackademyWebSite"
    $softRepo = Join-Path $parentDir "FlashRevisionSoft"

    if (-not (Test-Path (Join-Path $siteRepo ".git"))) {
        throw "Depot site introuvable : $siteRepo"
    }
    if (-not (Test-Path (Join-Path $softRepo ".git"))) {
        throw "Depot soft introuvable : $softRepo"
    }

    return [PSCustomObject]@{
        Parent = $parentDir
        Site   = $siteRepo
        Soft   = $softRepo
    }
}

function Test-YesResponse {
    param([string]$Value)

    $normalized = $Value.Trim().ToLowerInvariant()
    return $normalized -in @('o', 'oui', 'y', 'yes')
}

function Get-RepoSyncStatus {
    param(
        [string]$Path,
        [string]$Label,
        [bool]$SkipFetch,
        [bool]$IsDryRun
    )

    Push-Location $Path
    try {
        $branch = [string](git branch --show-current 2>&1)
        if ($LASTEXITCODE -ne 0) {
            throw ("[{0}] Impossible de lire la branche : {1}" -f $Label, $branch)
        }

        $localLines = @(git status --porcelain | Where-Object { $_ -and $_.Trim().Length -gt 0 })
        $hasLocalChanges = $localLines.Count -gt 0

        $upstream = git rev-parse --abbrev-ref '@{u}' 2>$null
        $hasUpstream = ($LASTEXITCODE -eq 0) -and (-not [string]::IsNullOrWhiteSpace($upstream))

        $fetchOk = $true
        $fetchMessage = "fetch ignore (-NoFetch)"

        if (-not $SkipFetch -and -not $IsDryRun) {
            git fetch origin 2>&1 | Out-Host
            if ($LASTEXITCODE -ne 0) {
                $fetchOk = $false
                $fetchMessage = "fetch echoue"
            }
            else {
                $fetchMessage = "fetch OK"
            }
        }
        elseif (-not $SkipFetch -and $IsDryRun) {
            $fetchMessage = "[DRY-RUN] git fetch origin"
        }

        $behind = 0
        $ahead = 0
        $syncState = "inconnu"

        if ($hasUpstream) {
            $counts = git rev-list --left-right --count '@{u}...HEAD' 2>&1
            if ($LASTEXITCODE -eq 0) {
                $parts = $counts -split "\s+"
                if ($parts.Count -ge 2) {
                    $behind = [int]$parts[0]
                    $ahead = [int]$parts[1]
                }

                if ($behind -eq 0 -and $ahead -eq 0) {
                    $syncState = "a jour"
                }
                elseif ($behind -gt 0 -and $ahead -eq 0) {
                    $syncState = "en retard"
                }
                elseif ($behind -eq 0 -and $ahead -gt 0) {
                    $syncState = "en avance"
                }
                else {
                    $syncState = "diverge"
                }
            }
            else {
                $syncState = "upstream illisible"
            }
        }
        else {
            $syncState = "pas de branche distante"
        }

        $localHead = git log -1 --oneline 2>&1
        $remoteHead = $null
        if ($hasUpstream) {
            $remoteHead = git log -1 --oneline '@{u}' 2>$null
        }

        return [PSCustomObject]@{
            Label           = $Label
            Path            = $Path
            Branch          = $branch
            Upstream        = $upstream
            HasUpstream     = $hasUpstream
            HasLocalChanges = $hasLocalChanges
            LocalLines      = $localLines
            Behind          = $behind
            Ahead           = $ahead
            SyncState       = $syncState
            FetchOk         = $fetchOk
            FetchMessage    = $fetchMessage
            LocalHead       = [string]$localHead
            RemoteHead      = [string]$remoteHead
            CanPull         = ($hasUpstream -and $behind -gt 0 -and $ahead -eq 0 -and $fetchOk)
            NeedsManual     = ($hasUpstream -and (($behind -gt 0 -and $ahead -gt 0) -or ($syncState -eq 'diverge')))
        }
    }
    finally {
        Pop-Location
    }
}

function Write-SyncReport {
    param([object]$Status)

    Write-Host ""
    Write-Host ("=== {0} ===" -f $Status.Label) -ForegroundColor Cyan
    Write-Host ("Chemin    : {0}" -f $Status.Path)
    Write-Host ("Branche   : {0}" -f $Status.Branch)
    Write-Host ("Remote    : {0}" -f $(if ($Status.HasUpstream) { $Status.Upstream } else { "(non configure)" }))
    Write-Host ("Fetch     : {0}" -f $Status.FetchMessage)
    Write-Host ("Local     : {0}" -f $(if ($Status.HasLocalChanges) { "modifications non committes" } else { "propre" }))
    Write-Host ("Sync      : {0} (retard: {1}, avance: {2})" -f $Status.SyncState, $Status.Behind, $Status.Ahead)
    Write-Host ("HEAD local  : {0}" -f $Status.LocalHead)

    if ($Status.HasUpstream -and $Status.RemoteHead) {
        Write-Host ("HEAD remote : {0}" -f $Status.RemoteHead)
    }

    if ($Status.HasLocalChanges) {
        foreach ($line in $Status.LocalLines) {
            Write-Host ("  {0}" -f $line)
        }
    }

    if ($Status.NeedsManual) {
        Write-Host "Attention : branche divergee, pull automatique impossible (merge manuel requis)." -ForegroundColor Yellow
    }
    elseif ($Status.CanPull) {
        Write-Host ("Mise a jour disponible : {0} commit(s) a recuperer." -f $Status.Behind) -ForegroundColor Yellow
    }
    elseif ($Status.SyncState -eq "a jour") {
        Write-Host "Deja synchronise avec le remote." -ForegroundColor Green
    }
    elseif (-not $Status.HasUpstream) {
        Write-Host "Pas de branche upstream. Ex: git push -u origin <branche>" -ForegroundColor Yellow
    }
}

function Invoke-RepoPull {
    param(
        [object]$Status,
        [bool]$IsDryRun
    )

    Write-Host ""
    Write-Host ("=== Pull {0} ===" -f $Status.Label) -ForegroundColor Cyan

    if ($Status.HasLocalChanges) {
        Write-Host ("[{0}] Pull ignore : modifications locales non committes." -f $Status.Label) -ForegroundColor Yellow
        return $false
    }

    if ($Status.NeedsManual) {
        Write-Host ("[{0}] Pull ignore : branche divergee." -f $Status.Label) -ForegroundColor Yellow
        return $false
    }

    if (-not $Status.CanPull) {
        Write-Host ("[{0}] Rien a pull (deja a jour ou pas de remote)." -f $Status.Label) -ForegroundColor DarkGray
        return $false
    }

    if ($IsDryRun) {
        Write-Host ("[DRY-RUN] git pull --ff-only (depuis {0})" -f $Status.Upstream) -ForegroundColor DarkGray
        return $true
    }

    Push-Location $Status.Path
    try {
        git pull --ff-only
        if ($LASTEXITCODE -ne 0) {
            throw ("git pull a echoue dans {0}" -f $Status.Label)
        }

        Write-Host ("[{0}] Pull OK." -f $Status.Label) -ForegroundColor Green
        return $true
    }
    finally {
        Pop-Location
    }
}

function Invoke-PullForRepos {
    param(
        [array]$Statuses,
        [bool]$IsDryRun,
        [bool]$ForcePull
    )

    $pullable = @($Statuses | Where-Object { $_.CanPull })
    if ($pullable.Count -eq 0) {
        Write-Host ""
        Write-Host "Aucun depot a mettre a jour via pull." -ForegroundColor Green
        return
    }

    $shouldPull = $ForcePull
    if (-not $shouldPull -and -not $IsDryRun) {
        $answer = Read-Host "Pull les depots en retard ? (o/n)"
        $shouldPull = Test-YesResponse -Value $answer
    }

    if (-not $shouldPull) {
        Write-Host "Pull ignore." -ForegroundColor Yellow
        return
    }

    foreach ($status in $pullable) {
        Invoke-RepoPull -Status $status -IsDryRun:$IsDryRun | Out-Null
    }
}

$paths = Get-WorkspacePaths
Write-Host "Sync workspace MadHackAdemy" -ForegroundColor Magenta
Write-Host ("Parent : {0}" -f $paths.Parent)
Write-Host ("Site   : {0}" -f $paths.Site)
Write-Host ("Soft   : {0}" -f $paths.Soft)

$siteStatus = Get-RepoSyncStatus -Path $paths.Site -Label "SITE" -SkipFetch:$NoFetch.IsPresent -IsDryRun:$DryRun.IsPresent
$softStatus = Get-RepoSyncStatus -Path $paths.Soft -Label "SOFT" -SkipFetch:$NoFetch.IsPresent -IsDryRun:$DryRun.IsPresent

Write-Host ""
Write-Host "=== Comparaison local / remote ===" -ForegroundColor Cyan
Write-SyncReport -Status $siteStatus
Write-SyncReport -Status $softStatus

if ($DryRun) {
    Write-Host ""
    Write-Host "Dry-run termine." -ForegroundColor DarkGray
    $anyPull = @($siteStatus, $softStatus | Where-Object { $_.CanPull })
    if ($anyPull.Count -gt 0) {
        Write-Host "En mode reel, le script demanderait : Pull les depots en retard ? (o/n)" -ForegroundColor DarkGray
        if ($Pull.IsPresent) {
            Invoke-PullForRepos -Statuses @($siteStatus, $softStatus) -IsDryRun:$true -ForcePull:$true
        }
    }
    exit 0
}

Invoke-PullForRepos -Statuses @($siteStatus, $softStatus) -IsDryRun:$false -ForcePull:$Pull.IsPresent

Write-Host ""
Write-Host "Termine." -ForegroundColor Green
