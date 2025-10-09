@echo off
echo ========================================
echo   FORZAR COLORES CORPORATIVOS NUEVOS
echo   Vino Tinto y Blanco
echo ========================================
echo.

echo [1/3] Limpiando cache de Laravel...
php artisan optimize:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan config:clear >nul 2>&1
echo [OK] Cache de Laravel limpiada

echo.
echo [2/3] Verificando archivo CSS...
if exist "public\css\welcome.css" (
    echo [OK] Archivo welcome.css encontrado
) else (
    echo [ERROR] No se encuentra welcome.css
    pause
    exit /b 1
)

echo.
echo [3/3] Instrucciones para el navegador:
echo.
echo Para ver los colores corporativos correctos:
echo.
echo OPCION 1 - Hard Refresh (RECOMENDADO):
echo   1. Abre: http://localhost o http://127.0.0.1:8000
echo   2. Presiona: Ctrl + Shift + R
echo   3. O Ctrl + F5
echo.
echo OPCION 2 - Modo Incognito:
echo   1. Presiona: Ctrl + Shift + N (Chrome/Edge)
echo   2. Abre: http://localhost o http://127.0.0.1:8000
echo.
echo OPCION 3 - Borrar cache del navegador:
echo   Chrome/Edge:
echo   1. Presiona: Ctrl + Shift + Delete
echo   2. Selecciona: "Imagenes y archivos en cache"
echo   3. Presiona: "Borrar datos"
echo   4. Recarga la pagina
echo.
echo ========================================
echo   COLORES CORPORATIVOS ACTUALES
echo ========================================
echo.
echo   Vino Tinto Principal: #8B1538
echo   Vino Tinto Oscuro:    #6B0F2A
echo   Vino Tinto Vibrante:  #C41E3A
echo   Blanco:               #FFFFFF
echo.
echo ========================================
echo.

choice /C SN /M "Deseas iniciar el servidor ahora"
if errorlevel 2 goto end
if errorlevel 1 goto server

:server
echo.
echo Iniciando servidor Laravel...
echo Abre tu navegador en modo incognito en: http://127.0.0.1:8000
echo.
echo Presiona Ctrl+C para detener el servidor
echo.
php artisan serve

:end
echo.
echo Listo! Recuerda hacer hard refresh (Ctrl + Shift + R)
echo.
pause
