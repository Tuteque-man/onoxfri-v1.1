@echo off
setlocal

REM Configuration
set "PHP_BIN=php"
set "HOST=127.0.0.1"
set "PORT=8000"

REM Move to this script directory (backend/php-8.2.12)
pushd "%~dp0"

echo Starting PHP built-in server on http://%HOST%:%PORT% ...
echo Document root: %CD%

"%PHP_BIN%" -S %HOST%:%PORT% -t "%CD%" index.php

popd
endlocal
