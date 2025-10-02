@echo off
echo ========================================
echo   Arepa la Llanerita - Mobile Testing
echo ========================================
echo.
echo Este script iniciara el servidor para pruebas moviles
echo.

cd arepa-llanerita

echo [1/2] Iniciando servidor Laravel...
start cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

timeout /t 3 /nobreak > nul

echo.
echo [2/2] Obteniendo tu IP local...
echo.
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    set IP=%%a
    set IP=!IP:~1!
    echo Tu IP es: !IP!
    echo.
    echo ========================================
    echo   ACCEDE DESDE TU MOVIL A:
    echo   http://!IP!:8000
    echo ========================================
    echo.
    goto :found
)

:found
echo.
echo INSTRUCCIONES:
echo 1. Conecta tu movil a la misma red WiFi
echo 2. Abre el navegador en tu movil
echo 3. Navega a la URL mostrada arriba
echo.
echo NOTA: Para PWA completa necesitas HTTPS
echo Considera usar ngrok para HTTPS gratuito
echo.
echo Presiona cualquier tecla para abrir ngrok.com...
pause > nul
start https://ngrok.com/download
