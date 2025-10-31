@echo off
REM Script simple para instalar dependencias de ONOXFRI
REM Sin bucles complejos ni dependencias de OneDrive

title ONOXFRI - Instalador de Dependencias
cls

echo =====================================================
echo     ONOXFRI - Instalador de Dependencias
echo =====================================================
echo.
echo Este script instalara las dependencias en:
echo   1. Raiz del proyecto
echo   2. Carpeta frontend
echo.
echo =====================================================
echo.

REM Verificar que npm este instalado
where npm >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] npm no encontrado.
    echo.
    echo Por favor instala Node.js desde: https://nodejs.org/
    echo.
    pause
    exit /b 1
)

echo [OK] npm encontrado
echo Version de npm:
call npm --version
echo.
echo Presiona cualquier tecla para continuar...
pause >nul
echo.

REM Obtener directorio del script
set "SCRIPT_DIR=%~dp0"
cd /d "%SCRIPT_DIR%"

echo Directorio del proyecto: %SCRIPT_DIR%
echo =====================================================
echo.
echo Presiona cualquier tecla para iniciar instalacion...
pause >nul
echo.

REM ============================================
REM INSTALAR EN RAIZ DEL PROYECTO
REM ============================================
if exist "%SCRIPT_DIR%package.json" (
    echo [1/2] Instalando dependencias en RAIZ del proyecto...
    echo.
    
    cd /d "%SCRIPT_DIR%"
    
    REM Limpiar cache
    echo Limpiando cache de npm...
    call npm cache clean --force 2>nul
    echo [OK] Cache limpiado
    echo.
    
    echo Instalando dependencias (esto puede tardar varios minutos)...
    echo Por favor espera...
    echo.
    call npm install --force
    
    if %errorlevel% neq 0 (
        echo.
        echo [ERROR] Fallo la instalacion en la raiz
        echo.
        pause
        exit /b 1
    )
    
    echo.
    echo [OK] Dependencias instaladas en raiz
    echo.
    pause >nul
) else (
    echo [SKIP] No hay package.json en la raiz
    echo.
)

echo =====================================================
echo.

REM ============================================
REM INSTALAR EN FRONTEND
REM ============================================
if exist "%SCRIPT_DIR%frontend\package.json" (
    echo [2/2] Instalando dependencias en FRONTEND...
    echo.
    
    cd /d "%SCRIPT_DIR%frontend"
    
    REM Limpiar cache
    echo Limpiando cache de npm...
    call npm cache clean --force 2>nul
    echo [OK] Cache limpiado
    echo.
    
    echo Instalando dependencias (esto puede tardar varios minutos)...
    echo Por favor espera...
    echo.
    call npm install --force
    
    if %errorlevel% neq 0 (
        echo.
        echo [ERROR] Fallo la instalacion en frontend
        echo.
        pause
        exit /b 1
    )
    
    echo.
    echo [OK] Dependencias instaladas en frontend
    echo.
) else (
    echo [ERROR] No se encuentra frontend\package.json
    echo.
    pause
    exit /b 1
)

REM ============================================
REM FINALIZADO
REM ============================================
cd /d "%SCRIPT_DIR%"

echo =====================================================
echo.
echo [EXITO] Todas las dependencias instaladas correctamente
echo.
echo =====================================================
echo.
echo Ahora puedes:
echo   - Ejecutar backend: backend\start-backend.bat
echo   - Ejecutar frontend: frontend\run.bat
echo.
echo =====================================================
echo.
pause