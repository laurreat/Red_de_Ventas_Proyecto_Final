@echo off
color 0C
echo ╔═══════════════════════════════════════════════════════╗
echo ║   ⚠️  RED MLM FRESH - ELIMINAR Y RECREAR  ⚠️         ║
echo ╚═══════════════════════════════════════════════════════╝
echo.
echo ⛔ ADVERTENCIA: Esta opción ELIMINARÁ DATOS CALCULABLES:
echo   • ❌ Usuarios (excepto admin@arepallanerita.com)
echo   • ❌ Pedidos y detalles de pedidos
echo   • ❌ Comisiones
echo   • ❌ Relaciones de referidos
echo   • ❌ Notificaciones
echo   • ❌ Mensajes de líder
echo   • ❌ Movimientos de inventario de pedidos
echo.
echo ✅ MANTENDRÁ (NO se elimina):
echo   • ✓ Productos y sus imágenes
echo   • ✓ Categorías
echo   • ✓ Capacitaciones
echo   • ✓ Configuraciones del sistema
echo   • ✓ Cupones
echo   • ✓ Zonas de entrega
echo   • ✓ Roles y permisos
echo   • ✓ Auditorías
echo.
echo Y creará una red completamente nueva con:
echo   • 1 Administrador (preservado)
echo   • 5 Líderes principales
echo   • 15-25 Sub-líderes
echo   • 75-200 Vendedores activos
echo   • 150-400 Vendedores regulares
echo   • 150-800 Clientes/Vendedores iniciales
echo   • 100 Clientes reales
echo   • Pedidos completos con productos existentes
echo.
echo Total estimado: 350-750 usuarios en la red
echo.
echo ═══════════════════════════════════════════════════════
echo.

choice /C SN /M "¿ESTÁS SEGURO de que deseas ELIMINAR todos los datos y empezar desde cero"
if errorlevel 2 goto :cancelar
if errorlevel 1 goto :confirmar

:confirmar
echo.
choice /C SN /M "¿CONFIRMAS que deseas continuar con la eliminación"
if errorlevel 2 goto :cancelar
if errorlevel 1 goto :ejecutar

:ejecutar
color 0A
echo.
echo 🗑️  Eliminando datos existentes...
echo 🚀 Construyendo nueva red MLM...
echo.
php artisan mlm:build --fresh

if %errorlevel% equ 0 (
    color 0A
    echo.
    echo ✅ ¡Red MLM creada exitosamente desde cero!
    echo.
    echo 📌 Ahora puedes acceder a:
    echo    http://127.0.0.1:8000/admin/referidos
    echo.
    echo 📧 Credenciales:
    echo    Email: admin@arepallanerita.com
    echo    Password: admin123
    echo.
    echo 📊 Datos preservados:
    echo    ✓ Productos y categorías existentes
    echo    ✓ Capacitaciones
    echo    ✓ Configuraciones del sistema
    echo    ✓ Cupones y zonas de entrega
    echo.
    echo 📈 Datos regenerados:
    echo    ✓ Usuarios y red de referidos
    echo    ✓ Pedidos con productos
    echo    ✓ Comisiones calculadas
    echo    ✓ Métricas actualizadas
    echo.
) else (
    color 0C
    echo.
    echo ❌ Hubo un error al crear la red MLM
    echo    Revisa los logs en storage/logs/laravel.log
    echo.
)
goto :fin

:cancelar
color 0E
echo.
echo ❌ Operación cancelada - No se eliminó ningún dato
echo.
goto :fin

:fin
color 07
pause
