#Requires -Version 5.1
<#
.SYNOPSIS
    Commit coordonne pour madhackademyWebSite + FlashRevisionSoft.

.EXAMPLE
    .\scripts\commit-both.ps1

.EXAMPLE
    .\scripts\commit-both.ps1 -SiteMessage "fix: hero centre-formation" -SoftMessage "feat: sync client MVP"

.EXAMPLE
    .\scripts\commit-both.ps1 -Message "chore: alignement doc" -Push

.EXAMPLE
    .\scripts\commit-both.ps1 -DryRun
#>

[CmdletBinding()]
param(
    [string]$Message,
    [string]$SiteMessage,
    [string]$SoftMessage,
    [switch]$Push,
    [switch]$DryRun,
    [switch]$NoStage
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

function Get-RepoStatus {
    param([string]$Path)

    Push-Location $Path
    try {
        $porcelain = @(git status --porcelain 2>&1)
        if ($LASTEXITCODE -ne 0) {
            throw ($porcelain -join [Environment]::NewLine)
        }

        $branch = git branch --show-current 2>&1
        if ($LASTEXITCODE -ne 0) {
            throw $branch
        }

        $lines = @($porcelain | Where-Object { $_ -and $_.Trim().Length -gt 0 })
        $hasChanges = $lines.Count -gt 0

        return [PSCustomObject]@{
            Path       = $Path
            Branch     = [string]$branch
            HasChanges = $hasChanges
            Lines      = $lines
        }
    }
    finally {
        Pop-Location
    }
}

function Write-StatusBlock {
    param(
        [string]$Label,
        [object]$Status
    )

    $state = if ($Status.HasChanges) { "modifications" } else { "propre" }
    Write-Host ("[{0}] {1} - {2}" -f $Label, $Status.Branch, $state)
    foreach ($line in $Status.Lines) {
        Write-Host ("  {0}" -f $line)
    }
}

function Test-YesResponse {
    param([string]$Value)

    $normalized = $Value.Trim().ToLowerInvariant()
    return $normalized -in @('o', 'oui', 'y', 'yes')
}

function Invoke-RepoCommit {
    param(
        [string]$Path,
        [string]$Label,
        [string]$CommitMessage,
        [bool]$IsDryRun,
        [bool]$SkipStage
    )

    if ([string]::IsNullOrWhiteSpace($CommitMessage)) {
        Write-Host ("[{0}] Aucun message, commit ignore." -f $Label) -ForegroundColor Yellow
        return $false
    }

    Push-Location $Path
    try {
        Write-Host ""
        Write-Host ("=== {0} ===" -f $Label) -ForegroundColor Cyan
        Write-Host ("Chemin  : {0}" -f $Path)
        Write-Host ("Branche : {0}" -f (git branch --show-current))
        Write-Host ("Message : {0}" -f $CommitMessage)

        if ($IsDryRun) {
            git status -sb
            Write-Host "[DRY-RUN] git add + git commit" -ForegroundColor DarkGray
            return $true
        }

        if (-not $SkipStage) {
            git add -A
            if ($LASTEXITCODE -ne 0) {
                throw ("git add a echoue dans {0}" -f $Label)
            }
        }

        $stillDirty = @(git status --porcelain | Where-Object { $_ -and $_.Trim().Length -gt 0 })
        if ($stillDirty.Count -eq 0) {
            Write-Host ("[{0}] Rien a committer apres staging." -f $Label) -ForegroundColor Yellow
            return $false
        }

        git commit -m $CommitMessage
        if ($LASTEXITCODE -ne 0) {
            throw ("git commit a echoue dans {0}" -f $Label)
        }

        Write-Host ("[{0}] Commit OK." -f $Label) -ForegroundColor Green
        return $true
    }
    finally {
        Pop-Location
    }
}

function Invoke-RepoPush {
    param(
        [string]$Path,
        [string]$Label,
        [bool]$IsDryRun
    )

    Push-Location $Path
    try {
        $branch = git branch --show-current
        Write-Host ""
        Write-Host ("=== Push {0} ===" -f $Label) -ForegroundColor Cyan
        Write-Host ("Branche : {0}" -f $branch)

        if ($IsDryRun) {
            Write-Host ("[DRY-RUN] git push -u origin {0}" -f $branch) -ForegroundColor DarkGray
            return $true
        }

        git push -u origin $branch
        if ($LASTEXITCODE -ne 0) {
            throw ("git push a echoue dans {0}" -f $Label)
        }

        Write-Host ("[{0}] Push OK." -f $Label) -ForegroundColor Green
        return $true
    }
    finally {
        Pop-Location
    }
}

function Invoke-PushForCommittedRepos {
    param(
        [array]$CommittedRepos,
        [bool]$IsDryRun,
        [bool]$ForcePush
    )

    if ($CommittedRepos.Count -eq 0) {
        return
    }

    $shouldPush = $ForcePush

    if (-not $shouldPush -and -not $IsDryRun) {
        $answer = Read-Host "Push vers origin pour les depots committes ? (o/n)"
        $shouldPush = Test-YesResponse -Value $answer
    }

    if (-not $shouldPush) {
        Write-Host "Push ignore." -ForegroundColor Yellow
        return
    }

    foreach ($repo in $CommittedRepos) {
        Invoke-RepoPush -Path $repo.Path -Label $repo.Label -IsDryRun:$IsDryRun | Out-Null
    }
}

$paths = Get-WorkspacePaths
Write-Host "Workspace MadHackAdemy" -ForegroundColor Magenta
Write-Host ("Parent : {0}" -f $paths.Parent)
Write-Host ("Site   : {0}" -f $paths.Site)
Write-Host ("Soft   : {0}" -f $paths.Soft)

$siteStatus = Get-RepoStatus -Path $paths.Site
$softStatus = Get-RepoStatus -Path $paths.Soft

Write-Host ""
Write-Host "=== Statut ===" -ForegroundColor Cyan
Write-StatusBlock -Label "Site" -Status $siteStatus
Write-StatusBlock -Label "Soft" -Status $softStatus

if (-not $siteStatus.HasChanges -and -not $softStatus.HasChanges) {
    Write-Host ""
    Write-Host "Les deux depots sont propres. Rien a faire." -ForegroundColor Green
    exit 0
}

if ($Message -and ($SiteMessage -or $SoftMessage)) {
    throw "Utilisez soit -Message, soit -SiteMessage et -SoftMessage, pas les deux."
}

if ($Message) {
    if (-not $SiteMessage -and $siteStatus.HasChanges) { $SiteMessage = $Message }
    if (-not $SoftMessage -and $softStatus.HasChanges) { $SoftMessage = $Message }
}

if ($siteStatus.HasChanges -and [string]::IsNullOrWhiteSpace($SiteMessage)) {
    $SiteMessage = Read-Host "Message commit SITE (madhackademyWebSite)"
}
if ($softStatus.HasChanges -and [string]::IsNullOrWhiteSpace($SoftMessage)) {
    $SoftMessage = Read-Host "Message commit SOFT (FlashRevisionSoft)"
}

$committedRepos = @()

if ($siteStatus.HasChanges) {
    if (Invoke-RepoCommit -Path $paths.Site -Label "SITE" -CommitMessage $SiteMessage -IsDryRun:$DryRun.IsPresent -SkipStage:$NoStage.IsPresent) {
        $committedRepos += [PSCustomObject]@{ Path = $paths.Site; Label = "SITE" }
    }
}

if ($softStatus.HasChanges) {
    if (Invoke-RepoCommit -Path $paths.Soft -Label "SOFT" -CommitMessage $SoftMessage -IsDryRun:$DryRun.IsPresent -SkipStage:$NoStage.IsPresent) {
        $committedRepos += [PSCustomObject]@{ Path = $paths.Soft; Label = "SOFT" }
    }
}

Write-Host ""
if ($DryRun) {
    Write-Host "Dry-run termine, aucun commit reel." -ForegroundColor DarkGray
    if ($committedRepos.Count -gt 0) {
        Write-Host "En mode reel, le script demanderait : Push vers origin ? (o/n)" -ForegroundColor DarkGray
        if ($Push.IsPresent) {
            Invoke-PushForCommittedRepos -CommittedRepos $committedRepos -IsDryRun:$true -ForcePush:$true
        }
    }
}
elseif ($committedRepos.Count -gt 0) {
    Invoke-PushForCommittedRepos -CommittedRepos $committedRepos -IsDryRun:$false -ForcePush:$Push.IsPresent
    Write-Host ""
    Write-Host "Termine. Ouvre une PR par depot si tu es sur des branches." -ForegroundColor Green
}
