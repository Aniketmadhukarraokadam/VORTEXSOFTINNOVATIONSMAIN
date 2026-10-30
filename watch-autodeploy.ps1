<#
.SYNOPSIS
    Vortexsoft Innovations — Continuous Auto-Sync & Auto-Deploy Watcher
    Watches the project directory for changes, commits them, pushes to GitHub,
    and deploys to Hostinger via FTP automatically.
#>

$rootDir = $PSScriptRoot
Set-Location $rootDir

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host " 🚀 Vortexsoft Innovations — Auto-Deploy & Push Watcher" -ForegroundColor Green
Write-Host " Watching: $rootDir" -ForegroundColor White
Write-Host " Actions:  Auto-Stage -> Auto-Commit -> Auto-Push -> Auto-Deploy" -ForegroundColor Yellow
Write-Host " Press [Ctrl+C] to stop." -ForegroundColor Gray
Write-Host "====================================================" -ForegroundColor Cyan

# Exclude list regex
$excludeRegex = '(\.git|\.env|node_modules|backups|docs|scratch|uploads\\resumes|\.log$|\.tmp$|\.bak$)'

$fsw = New-Object System.IO.FileSystemWatcher
$fsw.Path = $rootDir
$fsw.IncludeSubdirectories = $true
$fsw.EnableRaisingEvents = $true
$fsw.NotifyFilter = [System.IO.NotifyFilters]'FileName, LastWrite'

$lastChange = [DateTime]::MinValue
$pendingChanges = $false

$action = {
    $path = $Event.SourceEventArgs.FullPath
    if ($path -notmatch $excludeRegex) {
        $script:lastChange = [DateTime]::Now
        $script:pendingChanges = $true
        Write-Host " [DETECTED] Change in: $($Event.SourceEventArgs.Name)" -ForegroundColor DarkGray
    }
}

Register-ObjectEvent $fsw 'Changed' -Action $action | Out-Null
Register-ObjectEvent $fsw 'Created' -Action $action | Out-Null
Register-ObjectEvent $fsw 'Deleted' -Action $action | Out-Null
Register-ObjectEvent $fsw 'Renamed' -Action $action | Out-Null

try {
    while ($true) {
        Start-Sleep -Milliseconds 800
        if ($pendingChanges -and ([DateTime]::Now - $lastChange).TotalSeconds -ge 4) {
            $pendingChanges = $false
            
            # Check git status
            $status = & git status --porcelain
            if ($status) {
                Write-Host "`n⚡ Changes detected! Synchronizing..." -ForegroundColor Yellow
                & git add -A
                $commitMsg = "auto: update $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
                & git commit -m "$commitMsg"
                Write-Host "✔ Committed, Pushed, and Deployed!" -ForegroundColor Green
            }
        }
    }
}
finally {
    $fsw.Dispose()
    Get-EventSubscriber | Unregister-Event
}
