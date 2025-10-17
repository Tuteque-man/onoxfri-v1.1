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
echo 📂 Directorio: %cd%\frontend
echo 🌐 URL: http://localhost:5173
echo.
echo =====================================================
echo.

cd frontend

echo 🔍 Verificando dependencias...
if not exist node_modules (
    echo 📦 Instalando dependencias por primera vez...
    echo.
    npm install
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

npm run dev

echo.
echo =====================================================
echo 💡 Consejos útiles:
echo - El servidor está corriendo en http://localhost:5173
echo - Asegúrate que el backend PHP esté corriendo en puerto 80
echo - Consulta el README.md para más información
echo =====================================================
pause