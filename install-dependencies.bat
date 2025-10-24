@echo off
rem Instala todas las dependencias npm buscando package.json recursivamente.
rem Uso: doble clic en este .bat o ejecutar desde CMD: install-all.bat [ruta_raiz]
rem Si no se pasa ruta, usa el directorio actual.

setlocal enabledelayedexpansion

if "%~1"=="" (
  set "ROOT=%cd%"
) else (
  set "ROOT=%~1"
)

echo Instalando dependencias npm en: %ROOT%
where npm >nul 2>&1
if errorlevel 1 (
  echo ERROR: npm no encontrado. Instala Node.js y vuelve a ejecutar.
  exit /b 1
)

for /f "delims=" %%F in ('dir /b /s "%ROOT%\package.json" 2^>nul') do (
  set "PKG_DIR=%%~dpF"
  rem quitar barra final si existe
  if "!PKG_DIR:~-1!"=="\" set "PKG_DIR=!PKG_DIR:~0,-1!"
  pushd "!PKG_DIR!" >nul

  echo.
  echo ==============================================
  echo Instalando en: !PKG_DIR!
  if exist package-lock.json (
    echo Usando: npm ci
    npm ci
  ) else (
    echo Usando: npm install
    npm install
  )
  if errorlevel 1 (
    echo ERROR durante la instalación en: !PKG_DIR!
    popd >nul
    exit /b 1
  )

  popd >nul
)

echo.
echo Todas las instalaciones completadas correctamente.
endlocal
exit /b 0