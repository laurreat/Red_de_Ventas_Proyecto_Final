#!/bin/bash

# ============================================
# SCRIPT DE DEPLOYMENT - WELCOME PAGE
# Arepa la Llanerita
# ============================================

echo ""
echo "========================================"
echo "  DEPLOYMENT - WELCOME PAGE"
echo "  Arepa la Llanerita"
echo "========================================"
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Limpiar cache de Laravel
echo -e "${YELLOW}[1/4] Limpiando cache de Laravel...${NC}"
php artisan view:clear
php artisan cache:clear
php artisan config:clear
echo -e "${GREEN}✓ Cache limpiado${NC}"

echo ""

# Compilar assets (opcional)
echo -e "${YELLOW}[2/4] ¿Deseas compilar assets con Vite? (s/n)${NC}"
read -p "Respuesta: " compile

if [[ "$compile" == "s" || "$compile" == "S" ]]; then
    echo "Compilando assets..."
    npm run build
    echo -e "${GREEN}✓ Assets compilados${NC}"
else
    echo -e "${RED}× Compilación omitida${NC}"
fi

echo ""

# Verificar permisos
echo -e "${YELLOW}[3/4] Verificando permisos de archivos...${NC}"
chmod 644 public/css/welcome.css 2>/dev/null
chmod 644 public/js/welcome.js 2>/dev/null
echo -e "${GREEN}✓ Permisos verificados${NC}"

echo ""

# Resumen
echo -e "${YELLOW}[4/4] Verificando archivos creados...${NC}"

if [ -f "public/css/welcome.css" ]; then
    echo -e "${GREEN}✓ CSS: public/css/welcome.css${NC}"
else
    echo -e "${RED}× ERROR: CSS no encontrado${NC}"
fi

if [ -f "public/js/welcome.js" ]; then
    echo -e "${GREEN}✓ JS: public/js/welcome.js${NC}"
else
    echo -e "${RED}× ERROR: JS no encontrado${NC}"
fi

if [ -f "resources/views/welcome.blade.php" ]; then
    echo -e "${GREEN}✓ View: resources/views/welcome.blade.php${NC}"
else
    echo -e "${RED}× ERROR: View no encontrada${NC}"
fi

echo ""
echo "========================================"
echo "  DEPLOYMENT COMPLETADO"
echo "========================================"
echo ""
echo "Ahora puedes visitar: http://localhost/"
echo ""

# Hacer el script ejecutable
chmod +x deploy-welcome.sh
