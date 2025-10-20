@echo off
setlocal

REM Change to script directory (backend folder)
cd /d "%~dp0"

REM Start PHP built-in server for API
echo Starting ONOXFRI PHP API at http://127.0.0.1:8000 ...
php -S 127.0.0.1:8000 -t api

endlocal
