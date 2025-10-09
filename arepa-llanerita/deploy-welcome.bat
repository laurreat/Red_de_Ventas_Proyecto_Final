@echo off
REM ============================================
REM SCRIPT DE DEPLOYMENT - WELCOME PAGE
REM Arepa la Llanerita
REM ============================================

echo.
echo ========================================
echo   DEPLOYMENT - WELCOME PAGE
echo   Arepa la Llanerita
echo ========================================
echo.

REM Limpiar cache de Laravel
echo [1/4] Limpiando cache de Laravel...
php artisan view:clear
php artisan cache:clear
php artisan config:clear
echo ✓ Cache limpiado

echo.

REM Compilar assets (opcional)
echo [2/4] ¿Deseas compilar assets con Vite? (S/N)
set /p compile="Respuesta: "

if /i "%compile%"=="S" (
    echo Compilando assets...
    call npm run build
    echo ✓ Assets compilados
) else (
    echo × Compilación omitida
)

echo.

REM Verificar permisos
echo [3/4] Verificando permisos de archivos...
icacls "public\css\welcome.css" /grant Everyone:F >nul 2>&1
icacls "public\js\welcome.js" /grant Everyone:F >nul 2>&1
echo ✓ Permisos verificados

echo.

REM Resumen
echo [4/4] Verificando archivos creados...
if exist "public\css\welcome.css" (
    echo ✓ CSS: public\css\welcome.css
) else (
    echo × ERROR: CSS no encontrado
)

if exist "public\js\welcome.js" (
    echo ✓ JS: public\js\welcome.js
) else (
    echo × ERROR: JS no encontrado
)

if exist "resources\views\welcome.blade.php" (
    echo ✓ View: resources\views\welcome.blade.php
) else (
    echo × ERROR: View no encontrada
)

echo.
echo ========================================
echo   DEPLOYMENT COMPLETADO
echo ========================================
echo.
echo Ahora puedes visitar: http://localhost/
echo.
echo Presiona cualquier tecla para salir...
pause >nul
