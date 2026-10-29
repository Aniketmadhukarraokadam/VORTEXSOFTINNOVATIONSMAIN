@echo off
title Vortexsoft Innovations — FTP Deployment
cd /d "%~dp0"
echo ====================================================
echo  Vortexsoft Innovations - FTP Deployment
echo ====================================================
echo  [1] Deploy modified/new files (Fast - recommended)
echo  [2] Dry-run (Preview files to be deployed)
echo  [3] Deploy single file
echo  [4] Full deploy (All files)
echo  [5] Exit
echo ====================================================
set /p choice="Select an option [1-5]: "

if "%choice%"=="1" (
    powershell.exe -ExecutionPolicy Bypass -NoProfile -File "%~dp0deploy-ftp.ps1" -Mode Modified
) else if "%choice%"=="2" (
    powershell.exe -ExecutionPolicy Bypass -NoProfile -File "%~dp0deploy-ftp.ps1" -DryRun
) else if "%choice%"=="3" (
    set /p targetfile="Enter relative file path (e.g. index.php): "
    powershell.exe -ExecutionPolicy Bypass -NoProfile -File "%~dp0deploy-ftp.ps1" -File "%targetfile%"
) else if "%choice%"=="4" (
    powershell.exe -ExecutionPolicy Bypass -NoProfile -File "%~dp0deploy-ftp.ps1" -Mode All
) else (
    echo Exiting...
)
echo.
pause
