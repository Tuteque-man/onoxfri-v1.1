@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

title ONOXFRI - Frontend Vue (Puerto 5173)
cls

echo =====================================================
echo 🚀 ONOXFRI - Plataforma de Inventario Inteligente
echo =====================================================
echo.
echo 🎨 Frontend Vue 3 con diseño futurista
echo 📂 Directorio: %cd%
echo 🌐 URL: http://localhost:5173
echo.
echo =====================================================
echo.

REM Verificar si npm está instalado
where npm >nul 2>&1
if errorlevel 1 (
    echo ❌ ERROR: npm no encontrado en el sistema.
    echo.
    echo Por favor instala Node.js desde: https://nodejs.org/
    echo.
    echo Presiona cualquier tecla para cerrar...
    pause >nul
    exit /b 1
)

echo 🔍 Verificando dependencias...
if not exist node_modules (
    echo 📦 Instalando dependencias por primera vez...
    echo.
    call npm install
    if errorlevel 1 (
        echo ❌ Error al instalar dependencias
        echo 💡 Verifica que Node.js este instalado correctamente
        echo 📖 Consulta el README.md para más ayuda
        pause
        exit /b 1
    )
) else (
    echo ✅ Dependencias ya instaladas
)

echo.
echo 🚀 Iniciando servidor de desarrollo...
echo 📝 El servidor se ejecutará en: http://localhost:5173
echo 💡 Presiona Ctrl+C para detener el servidor
echo.
echo =====================================================
echo.

call npm run dev

if errorlevel 1 (
    echo.
    echo =====================================================
    echo ❌ ERROR: El servidor no pudo iniciarse
    echo =====================================================
    echo.
    pause
    exit /b 1
)

echo.
echo =====================================================
echo 💡 Consejos útiles:
echo - El servidor está corriendo en http://localhost:5173
echo - Asegúrate que el backend PHP esté corriendo en puerto 80
echo - Consulta el README.md para más información
echo =====================================================
pause