@echo off
echo.
echo ========================================
echo   VERIFICACION WELCOME PAGE
echo   Arepa la Llanerita
echo ========================================
echo.

echo [1/5] Limpiando cache de Laravel...
php artisan route:clear >nul 2>&1
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan optimize:clear >nul 2>&1
echo OK - Cache limpiado

echo.
echo [2/5] Verificando archivos...
if exist "resources\views\welcome.blade.php" (
    echo OK - welcome.blade.php encontrado
) else (
    echo ERROR - welcome.blade.php NO encontrado
)

if exist "public\css\welcome.css" (
    echo OK - welcome.css encontrado
) else (
    echo ERROR - welcome.css NO encontrado
)

if exist "public\js\welcome.js" (
    echo OK - welcome.js encontrado
) else (
    echo ERROR - welcome.js NO encontrado
)

echo.
echo [3/5] Verificando ruta principal...
php artisan route:list --path=/ >nul 2>&1
if %errorlevel% equ 0 (
    echo OK - Ruta / configurada correctamente
) else (
    echo ADVERTENCIA - Verificar ruta manualmente
)

echo.
echo [4/5] Iniciando servidor...
echo.
echo El servidor se iniciara en: http://127.0.0.1:8000
echo.
echo IMPORTANTE:
echo 1. Abrir navegador en modo INCOGNITO
echo 2. Visitar: http://127.0.0.1:8000
echo 3. Hacer Hard Refresh (Ctrl + Shift + R)
echo.
echo Presiona Ctrl+C para detener el servidor cuando termines
echo.

php artisan serve

echo.
echo Servidor detenido.
echo.
pause
