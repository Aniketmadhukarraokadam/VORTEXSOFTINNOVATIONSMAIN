@echo off
title Vortexsoft Innovations — Auto-Sync & Auto-Deploy Watcher
cd /d "%~dp0"
powershell.exe -ExecutionPolicy Bypass -NoProfile -File "%~dp0watch-autodeploy.ps1"
pause
