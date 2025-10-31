@echo off
setlocal

REM Change to script directory (backend folder)
cd /d "%~dp0"

echo =====================================================
echo ONOXFRI - Backend PHP API
echo =====================================================
echo.

REM Verificar si PHP está instalado
where php >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP no encontrado en el sistema.
    echo.
    echo Por favor instala PHP y agregalo al PATH del sistema.
    echo Descarga PHP desde: https://www.php.net/downloads
    echo.
    echo Presiona cualquier tecla para cerrar...
    pause >nul
    exit /b 1
)

REM Verificar que existe la carpeta api
if not exist "api" (
    echo ERROR: No se encuentra la carpeta 'api' en el directorio backend.
    echo Directorio actual: %cd%
    echo.
    echo Presiona cualquier tecla para cerrar...
    pause >nul
    exit /b 1
)

REM Start PHP built-in server for API
echo Iniciando servidor PHP en http://127.0.0.1:8000 ...
echo.
echo Presiona Ctrl+C para detener el servidor
echo =====================================================
echo.
call php -S 127.0.0.1:8000 -t api

if errorlevel 1 (
    echo.
    echo =====================================================
    echo ERROR: El servidor PHP no pudo iniciarse.
    echo =====================================================
    echo.
    echo Presiona cualquier tecla para cerrar...
    pause >nul
    exit /b 1
)

echo.
echo Servidor detenido.
pause
endlocal
