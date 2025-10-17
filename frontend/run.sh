#!/bin/bash

# =====================================================
# ONOXFRI - Script Simplificado para Frontend Vue
# Compatible con Linux/macOS
# =====================================================

echo "🚀 ONOXFRI - Plataforma de Inventario Inteligente"
echo "=============================================="
echo ""
echo "🎨 Frontend Vue 3 con diseño futurista"
echo "📂 Directorio: $(pwd)/frontend"
echo "🌐 URL: http://localhost:5173"
echo ""
echo "=============================================="
echo ""

cd frontend

echo "🔍 Verificando dependencias..."
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias por primera vez..."
    echo ""
    npm install
    if [ $? -ne 0 ]; then
        echo "❌ Error al instalar dependencias"
        echo "💡 Verifica que Node.js esté instalado correctamente"
        echo "📖 Consulta el README.md para más ayuda"
        exit 1
    fi
else
    echo "✅ Dependencias ya instaladas"
fi

echo ""
echo "🚀 Iniciando servidor de desarrollo..."
echo "📝 El servidor se ejecutará en: http://localhost:5173"
echo "💡 Presiona Ctrl+C para detener el servidor"
echo ""
echo "=============================================="
echo ""

npm run dev

echo ""
echo "=============================================="
echo "💡 Consejos útiles:"
echo "- El servidor está corriendo en http://localhost:5173"
echo "- Asegúrate que el backend PHP esté corriendo en puerto 80"
echo "- Consulta el README.md para más información"
echo "=============================================="