@echo off
color 0C
echo ╔═══════════════════════════════════════════════════════╗
echo ║   ⚠️  RED MLM FRESH - ELIMINAR Y RECREAR  ⚠️         ║
echo ╚═══════════════════════════════════════════════════════╝
echo.
echo ⛔ ADVERTENCIA: Esta opción ELIMINARÁ DATOS CALCULABLES:
echo   • ❌ Usuarios (excepto admin@arepallanerita.com)
echo   • ❌ Pedidos y detalles embebidos
echo   • ❌ Comisiones generadas
echo   • ❌ Relaciones de referidos
echo   • ❌ Notificaciones
echo   • ❌ Mensajes de líder
echo   • ❌ Movimientos de inventario relacionados
echo.
echo ✅ MANTENDRÁ (NO se elimina):
echo   • ✓ Productos existentes y sus imágenes
echo   • ✓ Categorías
echo   • ✓ Capacitaciones
echo   • ✓ Configuraciones del sistema
echo   • ✓ Cupones activos
echo   • ✓ Zonas de entrega
echo   • ✓ Roles y permisos
echo   • ✓ Auditorías del sistema
echo.
echo Y creará una red coherente con datos realistas:
echo   • 1 Administrador (preservado si existe)
echo   • 5 Líderes principales con historial
echo   • 15-25 Sub-líderes con equipos
echo   • 50-100 Vendedores activos (5-10 referidos)
echo   • 100-200 Vendedores regulares (1-4 referidos)
echo   • 100-300 Vendedores iniciales (0-1 referidos)
echo   • 100 Clientes reales para pedidos
echo   • Pedidos coherentes con fechas y productos
echo   • Comisiones calculadas automáticamente
echo   • Historial de ventas por mes
echo.
echo Total estimado: 400-700 usuarios en la red
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
    echo    🌐 Dashboard Admin: http://127.0.0.1:8000/dashboard
    echo    🔗 Red MLM Visual: http://127.0.0.1:8000/admin/referidos
    echo.
    echo 📧 Credenciales de acceso:
    echo    Email: admin@arepallanerita.com
    echo    Password: admin123
    echo.
    echo 📊 Datos preservados y reutilizados:
    echo    ✓ Productos con imágenes y categorías
    echo    ✓ Capacitaciones existentes
    echo    ✓ Configuraciones del sistema
    echo    ✓ Cupones y zonas de entrega
    echo.
    echo 📈 Datos regenerados con coherencia:
    echo    ✓ Red de usuarios con referidos reales
    echo    ✓ Pedidos con productos existentes
    echo    ✓ Comisiones calculadas correctamente
    echo    ✓ Historial de ventas mensual
    echo    ✓ Fechas coherentes y progresivas
    echo    ✓ Estados de pedidos realistas
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
