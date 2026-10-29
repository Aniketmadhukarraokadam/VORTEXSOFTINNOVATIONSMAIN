@echo off
title Point Domain to Hostinger Server (82.180.143.14)

:: Check for Administrator privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Requesting administrator privileges to update hosts file...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

set HOSTS=C:\Windows\System32\drivers\etc\hosts

:: Remove any existing vortexsoftinnovations lines
powershell -Command "$content = (Get-Content '%HOSTS%') | Where-Object { $_ -notmatch 'vortexsoftinnovations' }; Set-Content '%HOSTS%' $content"

:: Add the working Hostinger IP
echo 82.180.143.14 vortexsoftinnovations.com www.vortexsoftinnovations.com vortexsoftinnovations.in www.vortexsoftinnovations.in >> "%HOSTS%"

:: Flush DNS
ipconfig /flushdns

echo ===================================================================
echo SUCCESS! Your PC is now pointed directly to Hostinger (82.180.143.14)
echo The 504 Gateway Timeout is bypassed.
echo ===================================================================
echo.
echo Opening https://www.vortexsoftinnovations.com/admin/login.php ...
start https://www.vortexsoftinnovations.com/admin/login.php
pause
