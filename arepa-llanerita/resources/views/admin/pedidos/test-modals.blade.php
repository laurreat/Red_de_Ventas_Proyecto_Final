<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Modales de Pedidos</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/pedidos.css') }}">
</head>
<body>
    <div class="container mt-5">
        <h1>Test de Modales de Pedidos</h1>

        <div class="row mt-4">
            <div class="col-md-4">
                <button type="button" class="btn btn-danger w-100"
                        onclick="confirmDeletePedido('12345', 'PED-001', 'Juan Pérez', '$125,000', 'Pendiente')">
                    <i class="bi bi-trash me-1"></i>
                    Test Modal Eliminar
                </button>
            </div>

            <div class="col-md-4">
                <button type="button" class="btn btn-warning w-100"
                        onclick="confirmStatusChangePedido('12345', 'confirmado', 'PED-001', 'Juan Pérez', 'Pendiente')">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Test Modal Estado
                </button>
            </div>

            <div class="col-md-4">
                <button type="button" class="btn btn-success w-100"
                        onclick="confirmSavePedido('test-form', 'Los cambios del pedido se guardarán.')">
                    <i class="bi bi-save me-1"></i>
                    Test Modal Guardar
                </button>
            </div>
        </div>

        <!-- Formularios de prueba ocultos -->
        <form id="delete-form-12345" style="display: none;">
            <input type="hidden" name="test" value="delete">
        </form>

        <form id="status-form-12345" style="display: none;">
            <input type="hidden" name="estado" id="estado-12345">
        </form>

        <form id="test-form" style="display: none;">
            <input type="hidden" name="test" value="save">
        </form>

        <div class="mt-4">
            <h3>Log de eventos:</h3>
            <div id="log" class="bg-light p-3 rounded" style="height: 300px; overflow-y: auto;"></div>
        </div>
    </div>

    <!-- Incluir modales -->
    @include('admin.partials.modals-pedidos-professional')

    <script src="{{ asset('js/admin/pedidos-modals.js') }}"></script>

    <script>
        // Override de submit para logging
        HTMLFormElement.prototype.originalSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function() {
            logEvent('Form submitted: ' + this.id);
            // No ejecutar realmente el submit en test
        };

        function logEvent(message) {
            const log = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            log.innerHTML += `<div><strong>${timestamp}:</strong> ${message}</div>`;
            log.scrollTop = log.scrollHeight;
        }

        document.addEventListener('DOMContentLoaded', function() {
            logEvent('Página de test cargada');

            // Verificar Bootstrap
            if (typeof bootstrap !== 'undefined') {
                logEvent('✅ Bootstrap disponible - versión: ' + (bootstrap.Modal?.VERSION || 'Desconocida'));
            } else {
                logEvent('❌ Bootstrap NO disponible');
            }

            // Verificar modales HTML
            setTimeout(function() {
                const deleteModal = document.getElementById('deletePedidoConfirmModal');
                const statusModal = document.getElementById('statusPedidoConfirmModal');
                const saveModal = document.getElementById('savePedidoConfirmModal');

                if (deleteModal) logEvent('✅ HTML Modal eliminar encontrado');
                if (statusModal) logEvent('✅ HTML Modal estado encontrado');
                if (saveModal) logEvent('✅ HTML Modal guardar encontrado');

                // Verificar funciones
                if (typeof confirmDeletePedido !== 'undefined') {
                    logEvent('✅ confirmDeletePedido disponible');
                } else {
                    logEvent('❌ confirmDeletePedido NO disponible');
                }
                if (typeof confirmStatusChangePedido !== 'undefined') {
                    logEvent('✅ confirmStatusChangePedido disponible');
                } else {
                    logEvent('❌ confirmStatusChangePedido NO disponible');
                }
                if (typeof confirmSavePedido !== 'undefined') {
                    logEvent('✅ confirmSavePedido disponible');
                } else {
                    logEvent('❌ confirmSavePedido NO disponible');
                }
            }, 500);
        });
    </script>
</body>
</html>