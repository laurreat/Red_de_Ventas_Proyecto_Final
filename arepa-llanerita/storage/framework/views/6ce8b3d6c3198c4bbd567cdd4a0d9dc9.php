

<?php $__env->startSection('title', '- Mi Cuenta'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pages/cliente-dashboard-modern.css')); ?>?v=<?php echo e(filemtime(public_path('css/pages/cliente-dashboard-modern.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Bienvenida Hero -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 fw-bold">¡Bienvenido, <?php echo e(auth()->user()->name); ?>! 🍯</h1>
                            <p class="mb-0 opacity-90">
                                Disfruta de nuestras deliciosas arepas y productos tradicionales. 
                                <?php if(auth()->user()->referido_por): ?>
                                Referido por: <strong>
                                    <?php
                                        $referidor = \App\Models\User::find(auth()->user()->referido_por);
                                    ?>
                                    <?php echo e($referidor->name ?? 'Usuario'); ?>

                                </strong>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="fs-1">🫓</div>
                            <?php if(auth()->user()->codigo_referido): ?>
                            <div class="mt-2">
                                <small class="opacity-75">Tu código:</small>
                                <div class="fw-bold fs-5"><?php echo e(auth()->user()->codigo_referido); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Cliente -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-1">
                <div class="card-body text-center">
                    <i class="bi bi-cart-check text-success fs-1 mb-3"></i>
                    <div class="metric-value"><?php echo e(number_format($stats['total_pedidos'])); ?></div>
                    <div class="metric-label">Pedidos Realizados</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-2">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar text-primary fs-1 mb-3"></i>
                    <div class="metric-value">$<?php echo e(number_format($stats['total_gastado'], 0)); ?></div>
                    <div class="metric-label">Total Comprado</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-3">
                <div class="card-body text-center">
                    <i class="bi bi-star-fill text-warning fs-1 mb-3"></i>
                    <div class="metric-value"><?php echo e($productos_favoritos->count()); ?></div>
                    <div class="metric-label">Productos Favoritos</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card metric-card h-100 animate-delay-4">
                <div class="card-body text-center">
                    <i class="bi bi-people text-info fs-1 mb-3"></i>
                    <div class="metric-value"><?php echo e(number_format($stats['total_referidos'])); ?></div>
                    <div class="metric-label">Amigos Referidos</div>
                    <?php if($stats['total_referidos'] > 0): ?>
                    <small class="text-success">¡Gracias por recomendarnos!</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contenido Principal -->
        <div class="col-xl-8 mb-4">
            <!-- Acciones Rápidas -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-fill me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="javascript:void(0)" class="quick-action" onclick="clienteDashboard.toggleCarrito()">
                                <i class="bi bi-cart-plus fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Ver Carrito</div>
                                <small>Productos seleccionados</small>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="javascript:void(0)" class="quick-action" onclick="showComingSoon('Ver Menú')">
                                <i class="bi bi-book fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Ver Menú</div>
                                <small>Productos disponibles</small>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="javascript:void(0)" class="quick-action" onclick="showComingSoon('Mis Pedidos')">
                                <i class="bi bi-clock-history fs-2 mb-2 d-block"></i>
                                <div class="fw-bold">Mis Pedidos</div>
                                <small>Historial completo</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catálogo de Productos -->
            <div class="card mb-4 animate-fade-in animate-delay-1">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                        Nuestro Catálogo
                    </h5>
                    <button class="btn btn-sm btn-primary" onclick="clienteDashboard.toggleCarrito()">
                        <i class="bi bi-cart3 me-1"></i>
                        Carrito
                        <span class="badge bg-danger ms-1 carrito-count" style="display:none;">0</span>
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtros y búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar productos...">
                            </div>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-0">
                            <select class="form-select" id="filtroCategoria">
                                <option value="">Todas las categorías</option>
                                <option value="arepas">Arepas</option>
                                <option value="bebidas">Bebidas</option>
                                <option value="acompanantes">Acompañantes</option>
                                <option value="postres">Postres</option>
                            </select>
                        </div>
                    </div>

                    <!-- Grid de productos -->
                    <div class="row" id="gridProductos">
                        <!-- Arepa Reina Pepiada -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="arepas" data-nombre="arepa reina pepiada">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-primary text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-egg-fried fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(1)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Arepa Reina Pepiada</h6>
                                    <p class="text-muted small mb-2">Pollo desmenuzado, aguacate, mayonesa y cilantro</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$8,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(1)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Arepa Pelua -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="arepas" data-nombre="arepa pelua">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-warning text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-egg-fried fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(2)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Arepa Pelua</h6>
                                    <p class="text-muted small mb-2">Carne mechada y queso amarillo derretido</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$9,000</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(2)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Arepa Catira -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="arepas" data-nombre="arepa catira">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-success text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-egg-fried fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(3)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Arepa Catira</h6>
                                    <p class="text-muted small mb-2">Pollo desmenuzado con queso amarillo</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$8,000</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(3)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Chicha de Arroz -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="bebidas" data-nombre="chicha de arroz">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-info text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-cup-straw fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(4)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Chicha de Arroz</h6>
                                    <p class="text-muted small mb-2">Bebida tradicional cremosa y refrescante</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$3,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(4)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tequeños -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="acompanantes" data-nombre="tequeños">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-danger text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-moisture fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(5)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Tequeños (6 unidades)</h6>
                                    <p class="text-muted small mb-2">Deditos de queso envueltos en masa crujiente</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$6,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(5)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quesillo -->
                        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="postres" data-nombre="quesillo">
                            <div class="card producto-card h-100">
                                <div class="card-body p-3">
                                    <div class="producto-imagen mb-3">
                                        <div class="bg-gradient-secondary text-white d-flex align-items-center justify-content-center" style="height: 120px; border-radius: 12px;">
                                            <i class="bi bi-cake2 fs-1"></i>
                                        </div>
                                        <button class="btn-favorito" onclick="toggleFavorito(6)">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <h6 class="card-title fw-bold mb-2">Quesillo</h6>
                                    <p class="text-muted small mb-2">Postre tradicional de leche condensada</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="precio-producto">
                                            <span class="fw-bold text-success fs-5">$4,500</span>
                                        </div>
                                        <div class="rating text-warning">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="agregarAlCarrito(6)">
                                        <i class="bi bi-cart-plus me-1"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje cuando no hay resultados -->
                    <div id="noResultados" class="text-center py-4" style="display: none;">
                        <i class="bi bi-search fs-1 text-muted"></i>
                        <h6 class="text-muted mt-3">No se encontraron productos</h6>
                        <p class="text-muted mb-3">Prueba con otros términos de búsqueda o categorías</p>
                    </div>
                </div>
            </div>

            <!-- Últimos Pedidos -->
            <div class="card animate-fade-in animate-delay-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bag-check me-2"></i>
                        Mis Últimos Pedidos
                    </h5>
                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Historial Completo')">
                        Ver todos
                    </a>
                </div>
                <div class="card-body">
                    <?php if($pedidos_recientes->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $pedidos_recientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 mb-3">
                                <div class="order-card card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="fw-bold">Pedido #<?php echo e($pedido->numero_pedido); ?></div>
                                                <small class="text-muted"><?php echo e($pedido->created_at->format('d/m/Y H:i')); ?></small>
                                            </div>
                                            <?php
                                                $statusColors = [
                                                    'pendiente' => 'warning',
                                                    'confirmado' => 'info',
                                                    'en_preparacion' => 'primary',
                                                    'listo' => 'success',
                                                    'en_camino' => 'info',
                                                    'entregado' => 'success',
                                                    'cancelado' => 'danger'
                                                ];
                                            ?>
                                            <span class="status-badge bg-<?php echo e($statusColors[$pedido->estado] ?? 'secondary'); ?> text-white">
                                                <?php echo e(ucfirst(str_replace('_', ' ', $pedido->estado))); ?>

                                            </span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div>
                                                <small class="text-muted">Total pagado</small>
                                                <div class="fw-bold text-success fs-5">$<?php echo e(number_format($pedido->total_final, 0)); ?></div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Detalles del Pedido')">
                                                Ver detalles
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                            <h6 class="text-muted mt-3">No tienes pedidos aún</h6>
                            <p class="text-muted mb-3">¡Haz tu primer pedido y disfruta de nuestras deliciosas arepas!</p>
                            <button class="btn btn-primary" onclick="clienteDashboard.toggleCarrito()">
                                <i class="bi bi-cart-plus me-2"></i>
                                Hacer mi primer pedido
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 mb-4">
            <!-- Información Personal -->
            <div class="card mb-3 animate-fade-in animate-delay-1">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Mi Información
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Nombre completo</small>
                        <div class="fw-medium"><?php echo e(auth()->user()->name); ?> <?php echo e(auth()->user()->apellidos ?? ''); ?></div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Email</small>
                        <div class="fw-medium"><?php echo e(auth()->user()->email); ?></div>
                    </div>
                    
                    <?php if(auth()->user()->telefono): ?>
                    <div class="mb-2">
                        <small class="text-muted">Teléfono</small>
                        <div class="fw-medium"><?php echo e(auth()->user()->telefono); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(auth()->user()->direccion): ?>
                    <div class="mb-3">
                        <small class="text-muted">Dirección</small>
                        <div class="fw-medium"><?php echo e(auth()->user()->direccion); ?></div>
                        <?php if(auth()->user()->ciudad): ?>
                        <small class="text-muted"><?php echo e(auth()->user()->ciudad); ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline-primary btn-sm w-100" onclick="showComingSoon('Actualizar Perfil')">
                        <i class="bi bi-pencil me-1"></i>
                        Actualizar información
                    </button>
                </div>
            </div>

            <!-- Programa de Referidos -->
            <?php if(auth()->user()->codigo_referido): ?>
            <div class="card mb-3 animate-fade-in animate-delay-2">
                <div class="card-body text-center" style="background: linear-gradient(135deg, #fef5f5, #f5e6d3); border-radius: 12px; border: 2px solid #722F37;">
                    <i class="bi bi-gift text-primary fs-1 mb-2"></i>
                    <h6 class="fw-bold mb-2">¡Refiere y Gana!</h6>
                    <p class="small mb-3">Comparte tu código con amigos y recibe beneficios especiales</p>
                    
                    <div class="bg-white p-2 rounded mb-3">
                        <div class="fw-bold text-primary"><?php echo e(auth()->user()->codigo_referido); ?></div>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="fw-bold text-success"><?php echo e($stats['total_referidos']); ?></div>
                            <small class="text-muted">Referidos</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-info">$0</div>
                            <small class="text-muted">Ahorrado</small>
                        </div>
                    </div>
                    
                    <button class="btn btn-warning btn-sm" onclick="shareReferralCode()">
                        <i class="bi bi-share me-1"></i>
                        Compartir código
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Productos Favoritos -->
            <div class="card animate-fade-in animate-delay-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-heart-fill me-2"></i>
                        Tus Favoritos
                    </h6>
                </div>
                <div class="card-body">
                    <?php if($productos_favoritos->count() > 0): ?>
                        <?php $__currentLoopData = $productos_favoritos->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-center py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                            <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium"><?php echo e($producto->nombre); ?></div>
                                <small class="text-muted">$<?php echo e(number_format($producto->precio, 0)); ?></small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="agregarAlCarrito(<?php echo e($producto->id); ?>)">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if($productos_favoritos->count() > 3): ?>
                        <div class="text-center mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="showComingSoon('Ver Todos los Favoritos')">
                                Ver todos (<?php echo e($productos_favoritos->count()); ?>)
                            </button>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="bi bi-heart fs-3 text-muted"></i>
                            <p class="text-muted mb-2">No tienes productos favoritos</p>
                            <button class="btn btn-sm btn-primary" onclick="document.getElementById('buscarProducto').scrollIntoView({behavior:'smooth'})">
                                Explorar productos
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Carrito Sidebar -->
<div id="carritoSidebar">
    <div class="carrito-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-cart3 me-2"></i>
                Mi Carrito
            </h5>
            <button class="btn btn-sm btn-light" onclick="clienteDashboard.closeCarrito()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    
    <div class="carrito-items" id="carritoItems">
        <div class="text-center py-5">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <p class="text-muted mt-3">Tu carrito está vacío</p>
        </div>
    </div>
    
    <div class="carrito-footer">
        <div class="d-flex justify-content-between mb-3">
            <strong>Total:</strong>
            <strong class="text-success fs-5" id="carritoTotal">$0</strong>
        </div>
        <button class="btn btn-success w-100 mb-2" onclick="clienteDashboard.confirmarPedido()">
            <i class="bi bi-check-circle me-2"></i>
            Confirmar Pedido
        </button>
        <button class="btn btn-outline-secondary w-100" onclick="clienteDashboard.closeCarrito()">
            Seguir comprando
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/pages/cliente-dashboard-modern.js')); ?>?v=<?php echo e(filemtime(public_path('js/pages/cliente-dashboard-modern.js'))); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Proyecto_Final\Red_de_Ventas_Proyecto_Final\arepa-llanerita\resources\views/cliente/dashboard.blade.php ENDPATH**/ ?>