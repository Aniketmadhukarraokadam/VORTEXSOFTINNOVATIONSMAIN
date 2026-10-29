<#
.SYNOPSIS
    Vortexsoft Innovations — Local FTP Deployment Sync Tool
    Uploads website files directly to Hostinger FTP server.

.EXAMPLE
    .\deploy-ftp.ps1
    (Deploys modified / newly added files based on Git status)

.EXAMPLE
    .\deploy-ftp.ps1 -DryRun
    (Previews which files would be uploaded without uploading)

.EXAMPLE
    .\deploy-ftp.ps1 -Mode All
    (Syncs all project files to Hostinger)

.EXAMPLE
    .\deploy-ftp.ps1 -File "index.php"
    (Deploys a single specific file)
#>

[CmdletBinding()]
param(
    [ValidateSet('Modified', 'All', 'Single')]
    [string]$Mode = 'Modified',

    [string]$File = '',

    [switch]$DryRun
)

$ErrorActionPreference = 'Stop'
$rootDir = $PSScriptRoot

# ── Load credentials from config/.env ─────────────────────────
$envFile = Join-Path $rootDir "config\.env"
$ftpHost = "82.180.143.14"
$ftpUser = "u696371114.vortexsoftinnovations.com"
$ftpPass = "ShivaG@1437"
$ftpDir  = ""

if (Test-Path $envFile) {
    Get-Content $envFile | ForEach-Object {
        $line = $_.Trim()
        if ($line -and -not $line.StartsWith('#') -and $line.Contains('=')) {
            $parts = $line.Split('=', 2)
            $key = $parts[0].Trim()
            $val = $parts[1].Trim().Trim('"').Trim("'")
            if ($key -eq 'FTP_HOST' -and $val) { $ftpHost = $val }
            if ($key -eq 'FTP_USER' -and $val) { $ftpUser = $val }
            if ($key -eq 'FTP_PASS' -and $val) { $ftpPass = $val }
            if ($key -eq 'FTP_DIR'  -and $val) { $ftpDir  = $val }
        }
    }
}

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host " Vortexsoft Innovations - FTP Deployment Sync" -ForegroundColor Green
Write-Host " Host:     $ftpHost" -ForegroundColor White
Write-Host " User:     $ftpUser" -ForegroundColor White
Write-Host " Mode:     $Mode $(if ($DryRun) { '[DRY RUN]' })" -ForegroundColor Yellow
if ($File) {
    Write-Host " Target:   $File" -ForegroundColor Yellow
}
Write-Host "====================================================" -ForegroundColor Cyan

# -- Test Connection -------------------------------------------
Write-Host "Connecting to FTP server..." -NoNewline
$testListing = curl.exe -s --max-time 10 --user "${ftpUser}:${ftpPass}" "ftp://${ftpHost}/"
if ($LASTEXITCODE -ne 0) {
    Write-Host " [FAILED]" -ForegroundColor Red
    Write-Host "Could not authenticate or reach $ftpHost. Please verify your internet or credentials." -ForegroundColor Red
    exit 1
}
Write-Host " [CONNECTED]" -ForegroundColor Green

# -- Exclude patterns ------------------------------------------
$excludePatterns = @(
    '^\.git',
    '^\.github',
    '^\.env',
    'config/\.env',
    '^node_modules',
    '^backups',
    '^docs',
    '^uploads/resumes',
    '^run-server\.',
    '^deploy-ftp\.',
    '^deploy\.',
    '^router\.php',
    'README\.md$',
    'vortexsoft_website_details\.txt$',
    'website_info\.txt$',
    '\.log$',
    'Thumbs\.db$',
    '\.DS_Store$',
    '\.tmp$',
    '\.bak$',
    '^db_health_check\.php$',
    '^scratch/'
)

function Test-IsExcluded([string]$relPath) {
    $norm = $relPath.Replace('\', '/')
    foreach ($pat in $excludePatterns) {
        if ($norm -match $pat) { return $true }
    }
    return $false
}

# ── Collect files to deploy ───────────────────────────────────
$filesToUpload = @()

if ($File) {
    $fullPath = Join-Path $rootDir $File
    if (-not (Test-Path $fullPath)) {
        Write-Host "Error: Specified file does not exist: $fullPath" -ForegroundColor Red
        exit 1
    }
    $rel = Resolve-Path -Path $fullPath -Relative
    $rel = $rel.TrimStart('.').TrimStart('\').TrimStart('/')
    $filesToUpload += $rel
} elseif ($Mode -eq 'All') {
    Write-Host "Scanning all files in workspace..." -ForegroundColor Cyan
    Get-ChildItem -Path $rootDir -Recurse -File | ForEach-Object {
        $rel = $_.FullName.Substring($rootDir.Length).TrimStart('\').TrimStart('/')
        if (-not (Test-IsExcluded $rel)) {
            $filesToUpload += $rel
        }
    }
} else {
    # Mode = 'Modified'
    Write-Host "Detecting modified files from Git..." -ForegroundColor Cyan
    $gitExe = "C:\Program Files\Git\cmd\git.exe"
    if (-not (Test-Path $gitExe)) {
        $gitExe = "git"
    }
    
    $gitStatus = & $gitExe status --porcelain 2>$null
    if ($gitStatus) {
        foreach ($line in $gitStatus) {
            $rel = $line.Substring(3).Trim()
            # Handle renames e.g. "old -> new"
            if ($rel -match '-> (.+)$') { $rel = $matches[1] }
            $fullTarget = Join-Path $rootDir $rel
            if (Test-Path $fullTarget -PathType Container) {
                Get-ChildItem -Path $fullTarget -Recurse -File | ForEach-Object {
                    $subRel = $_.FullName.Substring($rootDir.Length).TrimStart('\').TrimStart('/')
                    if (-not (Test-IsExcluded $subRel)) { $filesToUpload += $subRel }
                }
            } elseif ((Test-Path $fullTarget -PathType Leaf) -and (-not (Test-IsExcluded $rel))) {
                $filesToUpload += $rel
            }
        }
    }

    # If git had no unstaged changes, check files changed in the last 24 hours
    if ($filesToUpload.Count -eq 0) {
        Write-Host "No pending Git changes found. Checking files modified in the last 24 hours..." -ForegroundColor Gray
        $cutoff = (Get-Date).AddHours(-24)
        Get-ChildItem -Path $rootDir -Recurse -File | Where-Object { $_.LastWriteTime -gt $cutoff } | ForEach-Object {
            $rel = $_.FullName.Substring($rootDir.Length).TrimStart('\').TrimStart('/')
            if (-not (Test-IsExcluded $rel)) {
                $filesToUpload += $rel
            }
        }
    }
}

if ($filesToUpload.Count -eq 0) {
    Write-Host "`nNo files need deployment. All files are up-to-date!" -ForegroundColor Green
    exit 0
}

Write-Host "`nFound $($filesToUpload.Count) file(s) to deploy:" -ForegroundColor Yellow
$filesToUpload | ForEach-Object { Write-Host "  -> $_" -ForegroundColor Gray }

if ($DryRun) {
    Write-Host "`n[DRY RUN COMPLETE] No files were uploaded." -ForegroundColor Cyan
    exit 0
}

# ── Upload files ──────────────────────────────────────────────
$ErrorActionPreference = 'Continue'
Write-Host "`nStarting upload..." -ForegroundColor Cyan
$successCount = 0
$failCount = 0
$startTime = Get-Date

foreach ($rel in $filesToUpload) {
    $localFile = Join-Path $rootDir $rel
    $remoteUrl = "ftp://${ftpHost}/" + ($rel.Replace('\', '/'))
    
    Write-Host "  Uploading: $rel ..." -NoNewline

    $uploaded = $false
    for ($attempt = 1; $attempt -le 3; $attempt++) {
        $res = & curl.exe -s --max-time 45 --ftp-create-dirs -T "$localFile" --user "${ftpUser}:${ftpPass}" "$remoteUrl"
        if ($LASTEXITCODE -eq 0) {
            $uploaded = $true
            break
        }
        Start-Sleep -Seconds 1
    }

    if ($uploaded) {
        Write-Host " [OK]" -ForegroundColor Green
        $successCount++
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
        $failCount++
    }
}

$elapsed = [Math]::Round(((Get-Date) - $startTime).TotalSeconds, 1)
Write-Host "`n====================================================" -ForegroundColor Cyan
Write-Host " Deployment Finished in ${elapsed}s" -ForegroundColor Green
Write-Host " Successfully Uploaded: $successCount file(s)" -ForegroundColor Green
if ($failCount -gt 0) {
    Write-Host " Failed Uploads:        $failCount file(s)" -ForegroundColor Red
}
Write-Host " Live Site: https://www.vortexsoftinnovations.com" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan
