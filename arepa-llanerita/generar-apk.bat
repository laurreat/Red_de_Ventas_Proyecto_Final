@echo off
echo ========================================
echo   Arepa la Llanerita - Generador de APK
echo ========================================
echo.

REM Verificar Node.js
echo [1/5] Verificando Node.js...
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js no esta instalado
    echo Descargalo desde: https://nodejs.org/
    pause
    exit /b 1
)
echo OK - Node.js instalado
echo.

REM Verificar Java
echo [2/5] Verificando Java JDK...
java -version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Java JDK no esta instalado
    echo Descargalo desde: https://www.oracle.com/java/technologies/downloads/
    pause
    exit /b 1
)
echo OK - Java JDK instalado
echo.

REM Verificar/Instalar Bubblewrap
echo [3/5] Verificando Bubblewrap CLI...
call npm list -g @bubblewrap/cli >nul 2>&1
if errorlevel 1 (
    echo Bubblewrap no instalado. Instalando...
    call npm install -g @bubblewrap/cli
    if errorlevel 1 (
        echo ERROR: No se pudo instalar Bubblewrap
        pause
        exit /b 1
    )
)
echo OK - Bubblewrap CLI instalado
echo.

REM Crear carpeta android-app si no existe
echo [4/5] Preparando estructura...
if not exist "android-app" (
    mkdir android-app
    echo Carpeta android-app creada
)
cd android-app
echo.

REM Opciones
echo [5/5] Que deseas hacer?
echo.
echo 1. Inicializar proyecto TWA (primera vez)
echo 2. Generar APK para testing
echo 3. Generar AAB para Play Store
echo 4. Ver informacion del keystore
echo 5. Salir
echo.
set /p opcion="Selecciona una opcion (1-5): "

if "%opcion%"=="1" goto init
if "%opcion%"=="2" goto build_apk
if "%opcion%"=="3" goto build_aab
if "%opcion%"=="4" goto keystore_info
if "%opcion%"=="5" goto end

:init
echo.
echo ==========================================
echo   Inicializando proyecto TWA
echo ==========================================
echo.
echo IMPORTANTE: Necesitas tener tu PWA ya desplegada en un dominio con HTTPS
echo.
set /p domain="Ingresa tu dominio (ej: https://arepallanerita.com): "
if "%domain%"=="" (
    echo ERROR: Dominio no puede estar vacio
    pause
    goto end
)

echo.
echo Iniciando Bubblewrap...
echo Responde las preguntas que aparezcan
echo.
call bubblewrap init --manifest %domain%/manifest.json

if errorlevel 1 (
    echo.
    echo ERROR: No se pudo inicializar el proyecto
    echo Verifica que tu dominio este accesible y tenga HTTPS
    pause
    goto end
)

echo.
echo ===================================
echo   Proyecto inicializado con exito!
echo ===================================
echo.
echo Archivos generados:
dir /b
echo.
pause
goto end

:build_apk
echo.
echo ==========================================
echo   Generando APK para Testing
echo ==========================================
echo.

if not exist "twa-manifest.json" (
    echo ERROR: Proyecto no inicializado
    echo Ejecuta primero la opcion 1
    pause
    goto end
)

echo Construyendo APK...
call bubblewrap build

if errorlevel 1 (
    echo ERROR: No se pudo generar el APK
    pause
    goto end
)

echo.
echo ===================================
echo   APK generado con exito!
echo ===================================
echo.
echo Archivo: app-release-signed.apk
echo Ubicacion: %CD%
echo.
echo Puedes instalarlo en tu dispositivo Android
echo.
pause
goto end

:build_aab
echo.
echo ==========================================
echo   Generando AAB para Play Store
echo ==========================================
echo.

if not exist "twa-manifest.json" (
    echo ERROR: Proyecto no inicializado
    echo Ejecuta primero la opcion 1
    pause
    goto end
)

echo Construyendo AAB...
call bubblewrap build

if errorlevel 1 (
    echo ERROR: No se pudo generar el AAB
    pause
    goto end
)

echo.
echo ===================================
echo   AAB generado con exito!
echo ===================================
echo.
echo Archivo: app-release-bundle.aab
echo Ubicacion: %CD%
echo.
echo Este archivo es el que debes subir a Google Play Console
echo.
pause
goto end

:keystore_info
echo.
echo ==========================================
echo   Informacion del Keystore
echo ==========================================
echo.
set /p keystore_path="Ingresa la ruta del keystore: "
if "%keystore_path%"=="" (
    echo ERROR: Ruta no puede estar vacia
    pause
    goto end
)

set /p alias="Ingresa el alias: "
if "%alias%"=="" (
    echo ERROR: Alias no puede estar vacio
    pause
    goto end
)

echo.
echo Obteniendo informacion...
keytool -list -v -keystore "%keystore_path%" -alias %alias%

echo.
echo IMPORTANTE: Copia el SHA256 que aparece arriba
echo Lo necesitas para assetlinks.json
echo.
pause
goto end

:end
cd ..
echo.
echo Gracias por usar el generador de APK!
echo.
pause
