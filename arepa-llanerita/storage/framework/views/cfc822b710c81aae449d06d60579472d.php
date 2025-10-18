<?php $__env->startSection('title', 'Reportes de Ventas'); ?>

<?php $__env->startPush('styles'); ?>
<link href="<?php echo e(asset('css/admin/reportes-modern.css')); ?>?v=<?php echo e(filemtime(public_path('css/admin/reportes-modern.css'))); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="reporte-header scale-in">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="reporte-header-title">
                    <i class="bi bi-bar-chart-line"></i> Reportes de Ventas
                </h1>
                <p class="reporte-header-subtitle">Análisis detallado de ventas y rendimiento del negocio</p>
            </div>
            <div class="reporte-header-actions">
                <button class="reporte-btn reporte-btn-danger" type="button">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>
    </div>

    
    <div class="reporte-filters-card fade-in-up">
        <div class="reporte-filters-header">
            <i class="bi bi-funnel"></i>
            <h3 class="reporte-filters-title">Filtros de Reporte</h3>
        </div>
        <div class="reporte-filters-body">
            <form method="GET" action="<?php echo e(route('admin.reportes.ventas')); ?>" autocomplete="off">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-calendar-event"></i> Fecha Inicio
                        </label>
                        <input type="date" class="form-control" name="fecha_inicio"
                            value="<?php echo e($fechaInicio); ?>"
                            style="border-radius:10px;padding:.75rem;">
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-calendar-check"></i> Fecha Fin
                        </label>
                        <input type="date" class="form-control" name="fecha_fin"
                            value="<?php echo e($fechaFin); ?>"
                            style="border-radius:10px;padding:.75rem;">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person-badge"></i> Vendedor
                        </label>
                        <select class="form-select" name="vendedor_id" style="border-radius:10px;padding:.75rem;">
                            <option value="">Todos los vendedores</option>
                            <?php $__currentLoopData = $vendedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendedor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($vendedor->id); ?>" <?php echo e($vendedorId == $vendedor->id ? 'selected' : ''); ?>>
                                <?php echo e($vendedor->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100" style="border-radius:10px;padding:.75rem;">
                            <i class="bi bi-search"></i> Generar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-1">
                <div class="reporte-stat-icon" style="background:rgba(114,47,55,0.1);color:var(--wine);">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="reporte-stat-value"><?php echo e(number_format((float)($stats['total_ventas'] ?? 0))); ?></div>
                <div class="reporte-stat-label">Total Ventas</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-2">
                <div class="reporte-stat-icon" style="background:rgba(16,185,129,0.1);color:var(--success);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="reporte-stat-value">$<?php echo e(format_number((float)($stats['total_ingresos'] ?? 0), 0)); ?></div>
                <div class="reporte-stat-label">Total Ingresos</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-3">
                <div class="reporte-stat-icon" style="background:rgba(59,130,246,0.1);color:var(--info);">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="reporte-stat-value">$<?php echo e(format_number((float)($stats['ticket_promedio'] ?? 0), 0)); ?></div>
                <div class="reporte-stat-label">Ticket Promedio</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="reporte-stat-card fade-in-up animate-delay-4">
                <div class="reporte-stat-icon" style="background:rgba(245,158,11,0.1);color:var(--warning);">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="reporte-stat-value"><?php echo e(number_format((float)($stats['productos_vendidos'] ?? 0))); ?></div>
                <div class="reporte-stat-label">Productos Vendidos</div>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-8 mb-4">
            <div class="reporte-chart-card fade-in-up">
                <div class="reporte-chart-header">
                    <h3 class="reporte-chart-title">
                        <i class="bi bi-graph-up"></i>
                        <span>Ventas por Día</span>
                    </h3>
                </div>
                <div class="reporte-chart-body">
                    <?php if($ventasPorDia->count() > 0): ?>
                    <div class="reporte-chart-container">
                        <canvas id="ventasPorDiaChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="reporte-empty-state">
                        <div class="reporte-empty-state-icon">
                            <i class="bi bi-graph-down"></i>
                        </div>
                        <h4 class="reporte-empty-state-title">No hay datos disponibles</h4>
                        <p class="reporte-empty-state-text">No se encontraron ventas en el período seleccionado</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4 mb-4">
            <div class="reporte-chart-card fade-in-up animate-delay-1">
                <div class="reporte-chart-header">
                    <h3 class="reporte-chart-title">
                        <i class="bi bi-pie-chart"></i>
                        <span>Ventas por Estado</span>
                    </h3>
                </div>
                <div class="reporte-chart-body">
                    <?php if($ventasPorEstado->count() > 0): ?>
                    <div class="reporte-chart-container" style="height:300px;">
                        <canvas id="ventasPorEstadoChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="reporte-empty-state" style="padding:2rem;">
                        <div class="reporte-empty-state-icon" style="font-size:3rem;">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                        <p class="reporte-empty-state-text" style="margin:1rem 0 0 0;">No hay datos disponibles</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($ventasPorVendedor->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="reporte-table-card fade-in-up">
                <div class="reporte-table-header">
                    <div class="reporte-table-title">
                        <i class="bi bi-person-badge"></i>
                        <span>Rendimiento por Vendedor</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="reporte-table">
                            <thead>
                                <tr>
                                    <th>Vendedor</th>
                                    <th>Pedidos</th>
                                    <th>Total Ventas</th>
                                    <th>Comisión Estimada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $ventasPorVendedor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="fade-in-up">
                                    <td>
                                        <div>
                                            <div style="font-weight:600;color:var(--gray-900);margin-bottom:.25rem;"><?php echo e($data['vendedor']); ?></div>
                                            <small style="color:var(--gray-500);"><?php echo e($data['email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="reporte-badge reporte-badge-info"><?php echo e((int)($data['cantidad_pedidos'] ?? 0)); ?> pedidos</span>
                                    </td>
                                    <td>
                                        <strong style="font-size:1.125rem;color:var(--wine);">$<?php echo e(format_number((float)($data['total_ventas'] ?? 0), 0)); ?></strong>
                                    </td>
                                    <td>
                                        <strong style="font-size:1.125rem;color:var(--success);">$<?php echo e(format_number((float)($data['comision_estimada'] ?? 0), 0)); ?></strong>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        
        <div class="col-lg-6 mb-4">
            <div class="reporte-table-card fade-in-up animate-delay-2">
                <div class="reporte-table-header">
                    <div class="reporte-table-title">
                        <i class="bi bi-trophy"></i>
                        <span>Top 10 Productos</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if($productosMasVendidos->count() > 0): ?>
                    <?php $__currentLoopData = $productosMasVendidos->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="reporte-ranking-item">
                        <div class="reporte-ranking-position <?php echo e($index === 0 ? 'top-1' : ($index === 1 ? 'top-2' : ($index === 2 ? 'top-3' : ''))); ?>">
                            <?php echo e($index + 1); ?>

                        </div>
                        <div class="reporte-ranking-info">
                            <div class="reporte-ranking-name"><?php echo e($data['producto']); ?></div>
                            <div class="reporte-ranking-detail">
                                <span class="reporte-badge reporte-badge-info"><?php echo e($data['categoria']); ?></span>
                                <span style="margin-left:.5rem;"><?php echo e((int)($data['cantidad_vendida'] ?? 0)); ?> unidades vendidas</span>
                            </div>
                        </div>
                        <div class="reporte-ranking-value">
                            $<?php echo e(format_number((float)($data['total_ingresos'] ?? 0), 0)); ?>

                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="reporte-empty-state">
                        <div class="reporte-empty-state-icon">
                            <i class="bi bi-box"></i>
                        </div>
                        <p class="reporte-empty-state-text">No hay productos vendidos</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6 mb-4">
            <div class="reporte-table-card fade-in-up animate-delay-3">
                <div class="reporte-table-header">
                    <div class="reporte-table-title">
                        <i class="bi bi-people"></i>
                        <span>Top 10 Clientes</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if($clientesMasActivos->count() > 0): ?>
                    <?php $__currentLoopData = $clientesMasActivos->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="reporte-ranking-item">
                        <div class="reporte-ranking-position <?php echo e($index === 0 ? 'top-1' : ($index === 1 ? 'top-2' : ($index === 2 ? 'top-3' : ''))); ?>">
                            <?php echo e($index + 1); ?>

                        </div>
                        <div class="reporte-ranking-info">
                            <div class="reporte-ranking-name"><?php echo e($data['cliente']); ?></div>
                            <div class="reporte-ranking-detail">
                                <?php echo e($data['email']); ?> · <?php echo e((int)($data['cantidad_pedidos'] ?? 0)); ?> pedidos
                            </div>
                        </div>
                        <div class="reporte-ranking-value">
                            $<?php echo e(format_number((float)($data['total_gastado'] ?? 0), 0)); ?>

                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="reporte-empty-state">
                        <div class="reporte-empty-state-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <p class="reporte-empty-state-text">No hay clientes activos</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    window.reportesRoutes = {
        exportar: '<?php echo e(route("admin.reportes.exportar-ventas")); ?>'
    };

    window.ventasPorDiaData = {
        labels: [<?php $__currentLoopData = $ventasPorDia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fecha => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            '<?php echo e(\Carbon\Carbon::parse($fecha)->format("d/m")); ?>', <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        datasets: [{
            label: 'Ingresos Diarios',
            data: [<?php $__currentLoopData = $ventasPorDia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> {
                {
                    (float)($data['total'] ?? 0)
                }
            }, <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>],
            backgroundColor: 'rgba(114, 47, 55, 0.1)',
            borderColor: '#722F37',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#722F37',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    };

    window.ventasPorEstadoData = {
        labels: [<?php $__currentLoopData = $ventasPorEstado; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            '<?php echo e(ucfirst(str_replace("_", " ", $estado))); ?>', <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        datasets: [{
            data: [<?php $__currentLoopData = $ventasPorEstado; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> {
                {
                    (float)($data['total'] ?? 0)
                }
            }, <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>],
            backgroundColor: [
                '#722F37', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444', '#6b7280'
            ],
            borderWidth: 0
        }]
    };
</script>

<script src="<?php echo e(asset('js/admin/reportes-modern.js')); ?>?v=<?php echo e(filemtime(public_path('js/admin/reportes-modern.js'))); ?>" defer></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Proyecto_Final\Red_de_Ventas_Proyecto_Final\arepa-llanerita\resources\views/admin/reportes/ventas.blade.php ENDPATH**/ ?>