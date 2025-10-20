@echo off
REM Script para crear automáticamente el módulo de perfil del cliente
echo ====================================
echo Creando Módulo de Perfil del Cliente
echo ====================================
echo.

REM Crear directorios necesarios
echo [1/4] Creando directorios...
mkdir "public\css\cliente" 2>nul
mkdir "public\js\cliente" 2>nul
mkdir "resources\views\cliente\perfil" 2>nul
echo Directorios creados exitosamente.
echo.

REM Copiar CSS del vendedor al cliente
echo [2/4] Copiando archivos CSS...
copy "public\css\vendedor\perfil-professional.css" "public\css\cliente\perfil-professional.css"
echo CSS copiado exitosamente.
echo.

REM Copiar JS del vendedor al cliente
echo [3/4] Copiando archivos JavaScript...
copy "public\js\vendedor\perfil-professional.js" "public\js\cliente\perfil-professional.js"
echo JavaScript copiado exitosamente.
echo.

echo [4/4] Módulo creado exitosamente!
echo.
echo ====================================
echo IMPORTANTE: Falta crear la vista
echo ====================================
echo Ahora debes copiar el contenido de la vista index.blade.php
echo que te proporcionaré en el siguiente mensaje
echo.
echo Ubicación: resources\views\cliente\perfil\index.blade.php
echo.
pause
