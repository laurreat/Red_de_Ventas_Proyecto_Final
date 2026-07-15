@echo off
echo ╔═══════════════════════════════════════════════════════╗
echo ║   🌟 Constructor de Red MLM - Arepa la Llanerita     ║
echo ╚═══════════════════════════════════════════════════════╝
echo.
echo Este script creará una red MLM completa y funcional con:
echo.
echo 📊 ESTRUCTURA DE LA RED:
echo   • 1 Administrador principal
echo   • 5 Líderes principales
echo   • 15-25 Sub-líderes
echo   • 75-200 Vendedores activos
echo   • 150-400 Vendedores regulares
echo   • 150-800 Clientes/Vendedores iniciales
echo   • 100 Clientes reales
echo.
echo 💼 DATOS GENERADOS:
echo   • Productos con categorías
echo   • Pedidos completos con productos
echo   • Clientes asociados a pedidos
echo   • Clientes en la red de referidos
echo   • Métricas reales de ventas
echo.
echo Total estimado: 350-750 usuarios en la red
echo.
echo ═══════════════════════════════════════════════════════
echo.

choice /C SN /M "¿Deseas continuar"
if errorlevel 2 goto :cancelar
if errorlevel 1 goto :ejecutar

:ejecutar
echo.
echo 🚀 Construyendo red MLM...
echo.
php artisan mlm:build

if %errorlevel% equ 0 (
    echo.
    echo ✅ ¡Red MLM creada exitosamente!
    echo.
    echo 📌 Ahora puedes acceder a:
    echo    http://127.0.0.1:8000/admin/referidos
    echo.
    echo 📧 Credenciales:
    echo    Email: admin@arepallanerita.com
    echo    Password: admin123
    echo.
    echo 📊 Datos generados:
    echo    ✓ Usuarios con roles diferenciados
    echo    ✓ Productos y categorías
    echo    ✓ Pedidos completos con productos
    echo    ✓ Clientes reales en pedidos
    echo    ✓ Clientes en red de referidos
    echo    ✓ Métricas de ventas reales
    echo.
) else (
    echo.
    echo ❌ Hubo un error al crear la red MLM
    echo    Revisa los logs en storage/logs/laravel.log
    echo.
)
goto :fin

:cancelar
echo.
echo ❌ Operación cancelada
echo.
goto :fin

:fin
pause
