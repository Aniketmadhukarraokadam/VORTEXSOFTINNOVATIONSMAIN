Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "Starting Vortexsoft Innovations Local Web Server..." -ForegroundColor Green
Write-Host "URL: http://localhost:8000" -ForegroundColor Yellow
Write-Host "====================================================" -ForegroundColor Cyan
Set-Location -Path $PSScriptRoot
Start-Process "http://localhost:8000"
php -S localhost:8000 router.php
