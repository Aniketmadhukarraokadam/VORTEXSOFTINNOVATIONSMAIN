@echo off
title Vortexsoft Innovations Local Server
echo ====================================================
echo Starting Vortexsoft Innovations Local Web Server...
echo URL: http://localhost:8000
echo ====================================================
cd /d "%~dp0"
start http://localhost:8000
php -S localhost:8000 router.php
pause
