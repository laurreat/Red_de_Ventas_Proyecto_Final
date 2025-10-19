# Documentación - Módulo de Cliente

## Índice
1. [Descripción General](#descripción-general)
2. [Arquitectura del Módulo](#arquitectura-del-módulo)
3. [Funcionalidades Implementadas](#funcionalidades-implementadas)
4. [Controladores](#controladores)
5. [Modelos](#modelos)
6. [Vistas](#vistas)
7. [JavaScript y Frontend](#javascript-y-frontend)
8. [Rutas](#rutas)
9. [Seguridad y Validaciones](#seguridad-y-validaciones)
10. [Caché y Optimización](#caché-y-optimización)

---

## Descripción General

El **Módulo de Cliente** es el sistema completo que permite a los usuarios finales (clientes) interactuar con la plataforma de ventas de arepas. Incluye gestión de pedidos, carrito de compras, favoritos, perfil personal y visualización del catálogo de productos.

### Características Principales
- Dashboard personalizado con estadísticas
- Sistema de pedidos completo
- Carrito de compras con LocalStorage
- Gestión de favoritos sincronizada
- Perfil de usuario editable
- Catálogo de productos con filtros
- Sistema de referidos
- Historial de compras
- Facturación colombiana

---

## Arquitectura del Módulo

### Estructura de Archivos

```
app/
├── Http/Controllers/Cliente/
│   ├── ClienteDashboardController.php
│   └── PedidoClienteController.php
├── Models/
│   ├── User.php (con campos de cliente)
│   ├── Cliente.php
│   └── Pedido.php
resources/
├── views/cliente/
│   ├── dashboard.blade.php
│   └── pedidos/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── show.blade.php
public/
├── css/pages/
│   ├── cliente-dashboard-modern.css
│   └── pedidos-cliente-modern.css
└── js/pages/
    ├── cliente-dashboard-modern.js
    └── pedidos-cliente-modern.js
routes/
└── web.php (rutas del módulo cliente)
```

---

## Funcionalidades Implementadas

### 1. Dashboard de Cliente

#### Características
- **Bienvenida personalizada** con nombre del usuario
- **Estadísticas en tiempo real**:
  - Total de pedidos realizados
  - Total gastado (COP)
  - Productos favoritos guardados
  - Amigos referidos
- **Pedidos recientes** (últimos 5)
- **Productos favoritos** con sincronización
- **Catálogo de productos** agrupado por categorías
- **Acciones rápidas** para crear pedidos
- **Código de referido** visible para compartir

#### Endpoints Disponibles
- `GET /cliente/dashboard` - Dashboard principal
- `POST /cliente/favoritos/agregar` - Agregar producto a favoritos
- `POST /cliente/favoritos/eliminar` - Eliminar producto de favoritos
- `POST /cliente/perfil/actualizar` - Actualizar perfil del cliente
- `GET /cliente/productos/recomendados` - Obtener productos recomendados

#### Estadísticas Mostradas
```php
[
    'total_pedidos' => int,          // Total de pedidos realizados
    'total_gastado' => float,        // Suma total en pesos colombianos
    'total_referidos' => int,        // Personas referidas
    'pedidos_completados' => int,    // Pedidos en estado 'entregado'
    'productos_frecuentes' => array  // Top 5 productos más pedidos
]
```

---

### 2. Sistema de Pedidos

#### 2.1 Listado de Pedidos

**Ruta**: `/cliente/pedidos`

**Funcionalidades**:
- Visualización de todos los pedidos del cliente
- **Filtros disponibles**:
  - Por estado (pendiente, confirmado, en_preparacion, enviado, entregado, cancelado)
  - Por fecha específica
  - Búsqueda por número de pedido o dirección
- **Estadísticas en tarjetas**:
  - Total de pedidos
  - Pedidos pendientes
  - Pedidos en proceso
  - Pedidos entregados
- **Paginación** (10 pedidos por página)
- **Estados visuales** con colores e íconos distintivos

**Estados de Pedido**:
- `pendiente` - Pedido creado, esperando confirmación
- `confirmado` - Pedido confirmado por vendedor/admin
- `en_preparacion` - Pedido siendo preparado
- `enviado` - Pedido en camino al cliente
- `entregado` - Pedido entregado exitosamente
- `cancelado` - Pedido cancelado

#### 2.2 Crear Nuevo Pedido

**Ruta**: `/cliente/pedidos/create`

**Funcionalidades**:
- **Catálogo de productos** organizado por categorías
- **Buscador en tiempo real** para filtrar productos
- **Filtro por categoría**
- **Selector de cantidad** con validación de stock
- **Carrito visual** con resumen en tiempo real
- **Información de entrega**:
  - Dirección de entrega (editable)
  - Teléfono de contacto
  - Notas adicionales (opcional)
- **Métodos de pago**:
  - Efectivo
  - Transferencia bancaria
  - Tarjeta (débito/crédito)
- **Resumen del pedido**:
  - Subtotal
  - Descuentos (si aplica)
  - Total final
- **Validaciones automáticas**:
  - Stock disponible
  - Cantidad mínima (1)
  - Cantidad máxima (100)
  - Prevención de duplicados
  - Límite de pedidos pendientes (máximo 5)

**Datos del Pedido Generado**:
```php
[
    'numero_pedido' => 'ARE-2024-00001',  // Formato: ARE-AÑO-CONSECUTIVO
    'user_id' => ObjectId,                 // ID del cliente
    'cliente_data' => [                    // Datos embebidos del cliente
        '_id' => ObjectId,
        'name' => string,
        'apellidos' => string,
        'email' => string,
        'telefono' => string,
        'cedula' => string
    ],
    'vendedor_id' => ObjectId,             // ID del vendedor asignado
    'vendedor_data' => [...],              // Datos embebidos del vendedor
    'estado' => 'pendiente',
    'direccion_entrega' => string,
    'telefono_entrega' => string,
    'notas' => string|null,
    'metodo_pago' => string,
    'detalles' => [                        // Array de productos
        [
            'producto_id' => ObjectId,
            'producto_data' => [...],      // Datos embebidos del producto
            'cantidad' => int,
            'precio_unitario' => float,
            'subtotal' => float
        ]
    ],
    'total' => float,
    'descuento' => float,
    'total_final' => float,
    'fecha_entrega_estimada' => DateTime,  // 3 días hábiles
    'historial_estados' => [               // Trazabilidad completa
        [
            'estado' => 'pendiente',
            'fecha' => DateTime,
            'usuario_id' => ObjectId,
            'usuario_nombre' => string,
            'motivo' => string,
            'ip' => string
        ]
    ],
    'ip_creacion' => string,               // Auditoría
    'user_agent' => string,
    'stock_devuelto' => false              // Control de inventario
]
```

#### 2.3 Ver Detalle de Pedido (Factura)

**Ruta**: `/cliente/pedidos/{id}`

**Funcionalidades**:
- **Factura completa** con diseño profesional colombiano
- **Información del pedido**:
  - Número de pedido
  - Fecha de creación
  - Estado actual con badge colorido
  - Fecha estimada de entrega
- **Información del cliente**:
  - Nombre completo
  - Cédula
  - Email
  - Teléfono
  - Dirección de entrega
- **Información del vendedor asignado** (si aplica)
- **Detalle de productos**:
  - Nombre y descripción
  - Cantidad
  - Precio unitario
  - Subtotal
  - Imagen (si está disponible)
- **Totales**:
  - Subtotal
  - Descuentos
  - IVA (si aplica)
  - Total final
- **Método de pago**
- **Historial de estados** con fechas y responsables
- **Acciones disponibles**:
  - Descargar factura (PDF)
  - Imprimir factura
  - Cancelar pedido (si está pendiente o confirmado)
  - Ver tracking
  - Contactar soporte

#### 2.4 Cancelar Pedido

**Ruta**: `POST /cliente/pedidos/{id}/cancelar`

**Funcionalidades**:
- **Validación de estado**: Solo se pueden cancelar pedidos en estado `pendiente` o `confirmado`
- **Devolución automática de stock** a los productos
- **Actualización de estadísticas** de productos (veces_vendido)
- **Registro en historial** con motivo de cancelación
- **Invalidación de caché** de estadísticas del cliente
- **Prevención de doble cancelación** mediante bandera `stock_devuelto`

---

### 3. Sistema de Favoritos

#### Características
- **Almacenamiento dual**:
  - MongoDB (campo `favoritos` en User)
  - LocalStorage (para PWA offline)
- **Sincronización automática** entre frontend y backend
- **Contador en tiempo real** en la interfaz
- **Visualización de productos favoritos** en el dashboard
- **Agregar/Eliminar** con animaciones suaves

#### Endpoints
- `POST /cliente/favoritos/agregar`
  - **Entrada**: `producto_id` (string)
  - **Salida**: `success`, `message`, `total_favoritos`
  - **Validaciones**: Producto existe, no está duplicado

- `POST /cliente/favoritos/eliminar`
  - **Entrada**: `producto_id` (string)
  - **Salida**: `success`, `message`, `total_favoritos`

#### Estructura de Datos
```javascript
// LocalStorage
localStorage.setItem('favoritos', JSON.stringify([
    'producto_id_1',
    'producto_id_2',
    'producto_id_3'
]));

// MongoDB (campo en User)
favoritos: [
    ObjectId('producto_id_1'),
    ObjectId('producto_id_2'),
    ObjectId('producto_id_3')
]
```

---

### 4. Carrito de Compras

#### Características
- **Almacenamiento en LocalStorage** (funciona offline)
- **Persistencia** entre sesiones
- **Sincronización** con disponibilidad de productos
- **Validación de stock** en tiempo real
- **Contador visual** en navbar
- **Sidebar deslizante** para ver contenido
- **Cálculo automático** de totales
- **Botón de compra rápida**

#### Funcionalidades JavaScript
```javascript
class ClienteDashboardManager {
    // Agregar al carrito
    agregarAlCarrito(productoId, nombre, precio, imagen, stock)
    
    // Eliminar del carrito
    eliminarDelCarrito(productoId)
    
    // Actualizar cantidad
    actualizarCantidad(productoId, cantidad)
    
    // Vaciar carrito
    vaciarCarrito()
    
    // Obtener total
    getCarritoTotal()
    
    // Renderizar carrito
    renderCarrito()
    
    // Sincronizar con servidor
    sincronizarCarrito()
}
```

#### Estructura del Carrito
```javascript
[
    {
        id: 'producto_id_1',
        nombre: 'Arepa de Queso',
        precio: 3500,
        imagen: '/images/productos/arepa-queso.jpg',
        cantidad: 2,
        stock: 50
    },
    // ...más productos
]
```

---

### 5. Perfil de Usuario

#### Información Editable
- Nombre completo
- Apellidos
- Teléfono
- Dirección completa
- Ciudad
- Email (requiere verificación)
- Contraseña (con confirmación)

#### Endpoint
- `POST /cliente/perfil/actualizar`
  - **Campos opcionales**: Solo se actualizan los campos enviados
  - **Validaciones**:
    - `name`: string, max 255 caracteres
    - `apellidos`: string, max 255 caracteres
    - `telefono`: string, max 20 caracteres
    - `direccion`: string, max 500 caracteres
    - `ciudad`: string, max 100 caracteres

---

### 6. Sistema de Referidos

#### Características
- Cada cliente tiene un **código de referido único**
- Se muestra en el dashboard de forma destacada
- **Contador de referidos** en estadísticas
- **Bonificación** por cada cliente referido (configurable)
- **Trazabilidad completa** de la red de referidos

#### Campos en User
```php
'referido_por' => ObjectId,        // ID del usuario que lo refirió
'codigo_referido' => string,       // Código único (ej: "ABC123")
'total_referidos' => int,          // Contador de personas referidas
'referidos_data' => array          // Datos de los referidos
```

---

### 7. Catálogo de Productos

#### Visualización
- **Agrupación por categorías** con pestañas/acordeones
- **Tarjetas de producto** con:
  - Imagen principal
  - Nombre y descripción
  - Precio en COP
  - Stock disponible
  - Badge de "Destacado"
  - Badge de "Agotado"
  - Botón de favorito
  - Botón de agregar al carrito
- **Búsqueda en tiempo real**
- **Filtros**:
  - Por categoría
  - Por rango de precio
  - Por disponibilidad
- **Ordenamiento**:
  - Por nombre
  - Por precio (menor a mayor / mayor a menor)
  - Por popularidad (veces_vendido)
  - Por destacados

#### Caché de Productos
- **Duración**: 10 minutos (600 segundos)
- **Clave**: `productos_catalogo`
- **Invalidación**: Al actualizar productos en admin

---

## Controladores

### ClienteDashboardController.php

**Ubicación**: `app/Http/Controllers/Cliente/ClienteDashboardController.php`

#### Métodos Principales

##### `index()`
- **Propósito**: Mostrar dashboard principal del cliente
- **Retorno**: Vista con estadísticas, pedidos recientes, favoritos y catálogo
- **Caché**: 5 minutos para estadísticas

##### `getClienteStats(User $user)`
- **Propósito**: Calcular estadísticas del cliente
- **Retorno**: Array con métricas
- **Optimización**: Uso de agregaciones MongoDB

##### `agregarFavorito(Request $request)`
- **Propósito**: Agregar producto a favoritos
- **Validaciones**: Producto existe, no duplicado
- **Retorno**: JSON con éxito/error

##### `eliminarFavorito(Request $request)`
- **Propósito**: Eliminar producto de favoritos
- **Retorno**: JSON con éxito/error

##### `actualizarPerfil(Request $request)`
- **Propósito**: Actualizar datos del perfil
- **Validaciones**: Campos opcionales con reglas
- **Retorno**: JSON con datos actualizados

##### `crearPedido(Request $request)`
- **Propósito**: Crear pedido desde el carrito
- **Validaciones**: Items, stock, datos de entrega
- **Retorno**: JSON con número de pedido

##### `historialPedidos(Request $request)`
- **Propósito**: Obtener historial paginado
- **Parámetros**: page, per_page
- **Retorno**: JSON con pedidos

##### `verPedido($id)`
- **Propósito**: Ver detalle de un pedido específico
- **Validación**: El pedido pertenece al cliente
- **Retorno**: JSON con pedido completo

##### `cancelarPedido($id)`
- **Propósito**: Cancelar un pedido
- **Validaciones**: Estado permite cancelación
- **Acciones**: Devolver stock, actualizar estado
- **Retorno**: JSON con éxito/error

##### `productosRecomendados()`
- **Propósito**: Obtener productos recomendados
- **Lógica**: Basado en productos frecuentes y similares
- **Caché**: 1 hora
- **Retorno**: JSON con productos

---

### PedidoClienteController.php

**Ubicación**: `app/Http/Controllers/Cliente/PedidoClienteController.php`

#### Métodos Principales

##### `index(Request $request)`
- **Propósito**: Listar todos los pedidos con filtros
- **Filtros**: estado, fecha, búsqueda
- **Paginación**: 10 por página
- **Retorno**: Vista con pedidos y estadísticas

##### `show($id)`
- **Propósito**: Mostrar factura detallada
- **Validación**: Pedido pertenece al cliente
- **Retorno**: Vista de factura profesional

##### `create()`
- **Propósito**: Formulario de creación de pedido
- **Datos**: Productos disponibles agrupados por categoría
- **Caché**: 10 minutos para productos
- **Retorno**: Vista con formulario

##### `store(Request $request)`
- **Propósito**: Guardar nuevo pedido con seguridad
- **Validaciones**:
  - Productos array mínimo 1
  - IDs MongoDB válidos (24 caracteres)
  - Cantidades entre 1 y 100
  - Dirección y teléfono requeridos
  - Método de pago válido
- **Seguridad**:
  - Protección XSS (htmlspecialchars)
  - Validación de ObjectId
  - Prevención de duplicados
  - Límite de pedidos pendientes (5)
  - Sanitización de emails
- **Acciones**:
  - Crear pedido con datos embebidos
  - Actualizar stock atómicamente
  - Incrementar veces_vendido
  - Generar número de pedido consecutivo
  - Registrar historial de estados
  - Guardar IP y User Agent (auditoría)
- **Retorno**: Redirección a vista de pedido creado

##### `cancel(Request $request, $id)`
- **Propósito**: Cancelar pedido del cliente
- **Validación**: Estado permite cancelación
- **Acciones**:
  - Devolver stock a productos
  - Actualizar estadísticas
  - Cambiar estado a 'cancelado'
  - Registrar en historial
- **Retorno**: JSON con resultado

##### `getClienteStats($user)` (privado)
- **Propósito**: Calcular estadísticas con caché
- **Optimización**: Agregaciones MongoDB
- **Caché**: 10 minutos
- **Retorno**: Array de estadísticas

##### `generarNumeroPedido()` (privado)
- **Propósito**: Generar número único de pedido
- **Formato**: `ARE-YYYY-00001`
- **Lógica**: Consecutivo anual con padding

---

## Modelos

### User.php (Campos de Cliente)

**Ubicación**: `app/Models/User.php`

#### Campos Relacionados con Cliente
```php
'favoritos' => 'array',              // IDs de productos favoritos
'productos_recientes' => 'array',    // Historial de visualización
'referido_por' => ObjectId,          // Usuario que lo refirió
'codigo_referido' => 'string',       // Código único para referir
'total_referidos' => 'integer',      // Contador de referidos
'telefono' => 'string',
'direccion' => 'string',
'ciudad' => 'string',
'departamento' => 'string',
'fecha_nacimiento' => 'date',
'notif_email_pedidos' => 'boolean',  // Preferencias de notificaciones
'notif_push_browser' => 'boolean'
```

#### Relaciones
```php
// Pedidos del cliente
public function pedidos()
{
    return $this->hasMany(Pedido::class, 'user_id');
}

// Usuario que lo refirió
public function referidor()
{
    return $this->belongsTo(User::class, 'referido_por');
}

// Usuarios referidos por este cliente
public function referidos()
{
    return $this->hasMany(User::class, 'referido_por');
}
```

---

### Cliente.php (Modelo Alternativo)

**Ubicación**: `app/Models/Cliente.php`

**Nota**: Este modelo existe pero actualmente el sistema usa el modelo `User` con roles para gestionar clientes.

#### Campos
```php
'nombre' => 'string',
'email' => 'string',
'telefono' => 'string',
'direccion' => 'string',
'ciudad' => 'string',
'cedula' => 'string',
'vendedor_id' => ObjectId,
'activo' => 'boolean'
```

#### Relaciones
```php
// Vendedor asignado
public function vendedor()
{
    return $this->belongsTo(User::class, 'vendedor_id');
}

// Pedidos
public function pedidos()
{
    return $this->hasMany(Pedido::class, 'cliente_id');
}
```

#### Scopes
```php
// Clientes activos
Cliente::activos()->get();
```

---

### Pedido.php

**Ubicación**: `app/Models/Pedido.php`

#### Campos Principales
```php
'numero_pedido' => 'string',          // ARE-2024-00001
'user_id' => ObjectId,                // ID del cliente
'cliente_data' => 'array',            // Datos embebidos
'vendedor_id' => ObjectId,            // ID del vendedor
'vendedor_data' => 'array',           // Datos embebidos
'detalles' => 'array',                // Productos del pedido
'estado' => 'string',                 // pendiente, confirmado, etc.
'subtotal' => 'decimal:2',
'descuento' => 'decimal:2',
'iva' => 'decimal:2',
'total' => 'decimal:2',
'total_final' => 'decimal:2',
'direccion_entrega' => 'string',
'telefono_entrega' => 'string',
'notas' => 'string|null',
'metodo_pago' => 'string',
'fecha_entrega_estimada' => 'datetime',
'historial_estados' => 'array',
'stock_devuelto' => 'boolean'
```

#### Relaciones
```php
// Cliente
public function cliente()
{
    return $this->belongsTo(User::class, 'user_id');
}

// Vendedor
public function vendedor()
{
    return $this->belongsTo(User::class, 'vendedor_id');
}
```

#### Scopes
```php
// Por estado
Pedido::porEstado('pendiente')->get();
Pedido::pendientes()->get();
Pedido::confirmados()->get();
Pedido::entregados()->get();

// Por usuario
Pedido::delCliente($clienteId)->get();
Pedido::delVendedor($vendedorId)->get();

// Por fecha
Pedido::hoy()->get();
```

#### Métodos Útiles
```php
// Verificar estados
$pedido->estaPendiente();    // bool
$pedido->estaConfirmado();   // bool
$pedido->estaEntregado();    // bool
$pedido->estaCancelado();    // bool

// Verificar si puede ser cancelado
$pedido->puedeSerCancelado(); // bool

// Cambiar estado con historial
$pedido->cambiarEstado('confirmado', 'Pedido confirmado por vendedor', $userId);

// Agregar detalle
$pedido->agregarDetalle($productoData, $cantidad, $precioUnitario);

// Recalcular totales
$pedido->recalcularTotales();
```

#### Accessors
```php
// Obtener detalles (compatible con productos y detalles)
$pedido->detalles;  // Array de productos
$pedido->productos; // Alias de detalles

// Obtener detalles sin procesamiento
$pedido->getRawDetalles(); // Array puro
```

---

## Vistas

### dashboard.blade.php

**Ubicación**: `resources/views/cliente/dashboard.blade.php`

#### Secciones

##### 1. Bienvenida Hero
- Saludo personalizado
- Código de referido visible
- Información de referidor (si aplica)
- Emoji decorativo de arepas 🫓

##### 2. Tarjetas de Estadísticas
- Total de pedidos (con enlace a lista)
- Total gastado (formato COP)
- Productos favoritos (contador dinámico)
- Amigos referidos (con mensaje de agradecimiento)
- Animaciones escalonadas

##### 3. Acciones Rápidas
- Crear nuevo pedido
- Ver mis pedidos
- Explorar catálogo
- Ver perfil
- Botones con íconos y colores distintivos

##### 4. Productos Favoritos
- Grid responsive de productos
- Botón para eliminar de favoritos
- Botón para agregar al carrito
- Mensaje si no hay favoritos
- Sincronización automática

##### 5. Pedidos Recientes
- Lista de últimos 5 pedidos
- Estado con badge colorido
- Fecha formateada
- Total del pedido
- Botón para ver detalle
- Mensaje si no hay pedidos

##### 6. Catálogo de Productos
- Pestañas por categoría
- Tarjetas de producto con imagen
- Precio destacado
- Stock disponible
- Botón de favorito (corazón)
- Botón de agregar al carrito
- Búsqueda en tiempo real
- Indicador de "Agotado"
- Badge de "Destacado"

##### 7. Carrito Lateral (Offcanvas)
- Lista de productos agregados
- Cantidad editable
- Subtotal por producto
- Total general
- Botón para vaciar carrito
- Botón para crear pedido
- Cierra con ESC o click fuera

---

### pedidos/index.blade.php

**Ubicación**: `resources/views/cliente/pedidos/index.blade.php`

#### Secciones

##### 1. Header Hero
- Título con gradiente
- Botón "Nuevo Pedido" destacado
- Animación de entrada

##### 2. Stats Cards Interactivas
- Total de pedidos
- Pedidos pendientes
- Pedidos en proceso
- Pedidos entregados
- Íconos coloridos y animados

##### 3. Filtros Profesionales
- Búsqueda por texto
- Filtro por estado
- Filtro por fecha
- Botones de limpiar y aplicar
- Formulario con método GET

##### 4. Lista de Pedidos
- Tarjetas de pedido con:
  - Número de pedido
  - Fecha de creación
  - Estado con badge
  - Total en COP
  - Dirección de entrega
  - Método de pago
  - Botón "Ver Detalle"
  - Botón "Cancelar" (si aplica)
- Mensaje si no hay pedidos
- Paginación estilizada

---

### pedidos/create.blade.php

**Ubicación**: `resources/views/cliente/pedidos/create.blade.php`

#### Secciones

##### 1. Header con Navegación
- Breadcrumb
- Botón volver atrás
- Botones de acción rápida (limpiar, cargar desde carrito)

##### 2. Buscador y Filtros
- Input de búsqueda en tiempo real
- Select de categorías
- Botón de ordenar

##### 3. Catálogo de Productos
- Grid responsive
- Tarjetas de producto con:
  - Imagen
  - Nombre y descripción
  - Precio
  - Stock disponible
  - Selector de cantidad
  - Botón "Agregar"
  - Badge de agotado
- Búsqueda instantánea
- Filtrado por categoría

##### 4. Sidebar de Resumen
- **Productos Seleccionados**:
  - Lista con miniatura
  - Cantidad editable
  - Precio × cantidad
  - Botón eliminar
- **Información de Entrega**:
  - Dirección (textarea)
  - Teléfono de contacto
  - Notas adicionales (opcional)
- **Método de Pago**:
  - Radio buttons estilizados
  - Efectivo, Transferencia, Tarjeta
- **Resumen de Totales**:
  - Subtotal
  - Descuentos (si hay)
  - IVA (si aplica)
  - Total Final (destacado)
- **Botón de Crear Pedido** (grande y colorido)

##### 5. Validaciones JavaScript
- Al menos un producto
- Cantidad mayor a 0
- Dirección y teléfono requeridos
- Método de pago seleccionado
- Confirmación antes de enviar

---

### pedidos/show.blade.php (Factura)

**Ubicación**: `resources/views/cliente/pedidos/show.blade.php`

#### Secciones

##### 1. Header de Factura
- Gradiente con colores de marca
- Logo y nombre de la empresa
- Tipo de documento (Factura de Venta)
- Número de factura destacado

##### 2. Información del Pedido
- Número de pedido
- Fecha de emisión
- Estado actual con badge
- Fecha estimada de entrega
- Método de pago

##### 3. Información del Cliente
- Nombre completo
- Cédula
- Email
- Teléfono
- Dirección de entrega completa

##### 4. Información del Vendedor
- Nombre del vendedor asignado
- Teléfono de contacto
- Email (si está disponible)

##### 5. Detalle de Productos
- Tabla profesional con:
  - Imagen miniatura
  - Descripción del producto
  - Cantidad
  - Precio unitario
  - Subtotal
- Formato de moneda colombiana (COP)
- Totales parciales

##### 6. Resumen de Totales
- Subtotal
- Descuentos aplicados
- IVA (si aplica)
- **Total Final** (destacado en grande)

##### 7. Historial de Estados
- Timeline vertical
- Cada cambio de estado con:
  - Fecha y hora
  - Estado
  - Usuario responsable
  - Motivo/Comentario
- Íconos distintivos por estado

##### 8. Acciones Disponibles
- **Descargar PDF** (icono de descarga)
- **Imprimir** (icono de impresora)
- **Cancelar Pedido** (si está pendiente/confirmado)
- **Volver a Pedidos**
- **Contactar Soporte**

##### 9. Notas y Términos
- Observaciones del cliente
- Términos y condiciones
- Información de garantía
- Datos de contacto de la empresa

##### Estilos CSS
- Diseño profesional colombiano
- Gradientes en header
- Colores de marca consistentes
- Bordes y sombras suaves
- Responsive para móviles
- Optimizado para impresión

---

## JavaScript y Frontend

### cliente-dashboard-modern.js

**Ubicación**: `public/js/pages/cliente-dashboard-modern.js`

#### Clase Principal: ClienteDashboardManager

##### Constructor
```javascript
constructor() {
    this.carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    this.favoritos = JSON.parse(localStorage.getItem('favoritos')) || [];
    this.init();
}
```

##### Métodos de Inicialización
- `init()` - Configura todo al cargar
- `setupEventListeners()` - Eventos globales (ESC, clicks)
- `animateCards()` - Animaciones de entrada
- `updateCarritoCount()` - Actualizar contador
- `sincronizarFavoritosConDOM()` - Sincronizar con servidor

##### Métodos del Carrito
```javascript
// Agregar producto
agregarAlCarrito(productoId, nombre, precio, imagen, stock)

// Eliminar producto
eliminarDelCarrito(productoId)

// Actualizar cantidad
actualizarCantidad(productoId, nuevaCantidad)

// Cambiar cantidad (incremento/decremento)
cambiarCantidad(productoId, delta)

// Vaciar todo
vaciarCarrito()

// Calcular total
getCarritoTotal()

// Renderizar en UI
renderCarrito()

// Abrir/cerrar sidebar
toggleCarrito()
openCarrito()
closeCarrito()

// Proceder al checkout
irACheckout()

// Guardar en localStorage
guardarCarrito()
```

##### Métodos de Favoritos
```javascript
// Toggle favorito
toggleFavorito(productoId)

// Agregar favorito
agregarFavorito(productoId)

// Eliminar favorito
eliminarFavorito(productoId)

// Cargar desde servidor
loadFavoritos()

// Sincronizar con servidor
sincronizarFavoritosConDOM()

// Actualizar contador
actualizarContadorFavoritos()

// Guardar en localStorage
guardarFavoritos()
```

##### Métodos de Búsqueda y Filtros
```javascript
// Buscar productos
buscarProductos(termino)

// Filtrar por categoría
filtrarPorCategoria(categoria)

// Ordenar productos
ordenarProductos(criterio) // nombre, precio, popularidad

// Limpiar filtros
limpiarFiltros()
```

##### Métodos de UI
```javascript
// Mostrar toast notification
showToast(mensaje, tipo) // success, error, warning, info

// Cerrar todos los modales
closeAllModals()

// Mostrar loading
showLoading()

// Ocultar loading
hideLoading()

// Confirmar acción
confirm(mensaje, callback)

// Scroll suave
smoothScroll(elemento)
```

##### Eventos Personalizados
```javascript
// Al agregar al carrito
document.addEventListener('carritoActualizado', (e) => {
    console.log('Carrito:', e.detail.carrito);
});

// Al cambiar favoritos
document.addEventListener('favoritosActualizados', (e) => {
    console.log('Favoritos:', e.detail.favoritos);
});
```

---

### pedidos-cliente-modern.js

**Ubicación**: `public/js/pages/pedidos-cliente-modern.js`

#### Clase Principal: PedidoClienteManager

##### Métodos de Creación de Pedido
```javascript
// Inicializar formulario
initFormularioPedido()

// Agregar producto al pedido
agregarProductoAPedido(productoId, nombre, precio, imagen, stock)

// Eliminar producto del pedido
eliminarProductoPedido(productoId)

// Actualizar cantidad en pedido
actualizarCantidadPedido(productoId, cantidad)

// Calcular totales del pedido
calcularTotalesPedido()

// Renderizar resumen
renderResumenPedido()

// Validar formulario
validarFormulario()

// Enviar pedido
enviarPedido()
```

##### Métodos de Visualización
```javascript
// Buscar productos
buscarProductosForm(termino)

// Filtrar por categoría
filtrarCategoria(categoria)

// Mostrar detalle de producto
mostrarDetalleProducto(productoId)

// Limpiar selección
limpiarSeleccion()

// Cargar desde carrito
cargarDesdeCarrito()
```

##### Métodos de Cancelación
```javascript
// Mostrar modal de cancelación
mostrarModalCancelar(pedidoId)

// Confirmar cancelación
confirmarCancelacion(pedidoId, motivo)

// Procesar respuesta
procesarCancelacion(response)
```

##### Métodos de Filtros (Lista)
```javascript
// Aplicar filtros
aplicarFiltros()

// Filtrar por estado
filtrarPorEstado(estado)

// Filtrar por fecha
filtrarPorFecha(fecha)

// Buscar en pedidos
buscarEnPedidos(termino)

// Limpiar filtros
limpiarFiltros()
```

##### Métodos de Factura
```javascript
// Descargar PDF
descargarFacturaPDF(pedidoId)

// Imprimir factura
imprimirFactura()

// Compartir factura
compartirFactura(pedidoId)

// Ver tracking
verTracking(pedidoId)
```

---

## Rutas

### Definición en web.php

**Ubicación**: `routes/web.php`

```php
Route::prefix('cliente')->name('cliente.')->middleware(['auth', 'verified'])->group(function () {

    // Dashboard Principal
    Route::get('/dashboard', [ClienteDashboardController::class, 'index'])
        ->name('dashboard');

    // Favoritos
    Route::post('/favoritos/agregar', [ClienteDashboardController::class, 'agregarFavorito'])
        ->name('favoritos.agregar');

    Route::post('/favoritos/eliminar', [ClienteDashboardController::class, 'eliminarFavorito'])
        ->name('favoritos.eliminar');

    // Perfil
    Route::post('/perfil/actualizar', [ClienteDashboardController::class, 'actualizarPerfil'])
        ->name('perfil.actualizar');

    // Pedidos - Resource Routes
    Route::get('/pedidos', [PedidoClienteController::class, 'index'])
        ->name('pedidos.index');
    
    Route::get('/pedidos/create', [PedidoClienteController::class, 'create'])
        ->name('pedidos.create');
    
    Route::post('/pedidos', [PedidoClienteController::class, 'store'])
        ->name('pedidos.store');
    
    Route::get('/pedidos/{id}', [PedidoClienteController::class, 'show'])
        ->name('pedidos.show');
    
    Route::post('/pedidos/{id}/cancelar', [PedidoClienteController::class, 'cancel'])
        ->name('pedidos.cancel');

    // Pedidos - Métodos adicionales (compatibilidad)
    Route::post('/pedidos/crear', [ClienteDashboardController::class, 'crearPedido'])
        ->name('pedidos.crear');

    Route::get('/pedidos/historial', [ClienteDashboardController::class, 'historialPedidos'])
        ->name('pedidos.historial');

    Route::get('/pedidos/ver/{id}', [ClienteDashboardController::class, 'verPedido'])
        ->name('pedidos.ver');

    // Recomendaciones
    Route::get('/productos/recomendados', [ClienteDashboardController::class, 'productosRecomendados'])
        ->name('productos.recomendados');
});
```

### Middlewares Aplicados
- `auth` - Usuario autenticado
- `verified` - Email verificado
- `role:cliente` - Rol de cliente (implícito en el grupo)

---

## Seguridad y Validaciones

### Validaciones del Lado del Servidor

#### Crear Pedido
```php
$validator = Validator::make($request->all(), [
    'productos' => 'required|array|min:1',
    'productos.*.producto_id' => 'required|string|size:24', // MongoDB ObjectId
    'productos.*.cantidad' => 'required|integer|min:1|max:100',
    'direccion_entrega' => 'required|string|max:500',
    'telefono_entrega' => 'required|string|max:20',
    'notas' => 'nullable|string|max:1000',
    'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
]);
```

#### Protección XSS
```php
// Sanitización de datos embebidos
'name' => htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'),
'email' => filter_var($user->email, FILTER_SANITIZE_EMAIL),
'direccion_entrega' => htmlspecialchars($request->direccion_entrega, ENT_QUOTES, 'UTF-8'),
```

#### Validación de ObjectId
```php
if (!preg_match('/^[a-f0-9]{24}$/i', $productoId)) {
    // ID inválido
}
```

#### Prevención de Spam
```php
// Límite de pedidos pendientes
$pedidosPendientes = Pedido::where('user_id', $user->_id)
    ->where('estado', 'pendiente')
    ->count();

if ($pedidosPendientes >= 5) {
    return redirect()->back()
        ->with('error', 'Tienes demasiados pedidos pendientes.');
}
```

#### Prevención de Duplicados
```php
$productosIds = [];
foreach ($request->productos as $item) {
    if (in_array($item['producto_id'], $productosIds)) {
        continue; // Saltar duplicados
    }
    $productosIds[] = $item['producto_id'];
}
```

### Validaciones del Lado del Cliente

#### JavaScript
```javascript
// Validar antes de enviar
function validarFormulario() {
    const productos = getProductosSeleccionados();
    
    if (productos.length === 0) {
        showToast('Debes seleccionar al menos un producto', 'error');
        return false;
    }
    
    const direccion = document.getElementById('direccion_entrega').value.trim();
    if (!direccion) {
        showToast('La dirección de entrega es requerida', 'error');
        return false;
    }
    
    const telefono = document.getElementById('telefono_entrega').value.trim();
    if (!telefono) {
        showToast('El teléfono de contacto es requerido', 'error');
        return false;
    }
    
    const metodoPago = document.querySelector('input[name="metodo_pago"]:checked');
    if (!metodoPago) {
        showToast('Selecciona un método de pago', 'error');
        return false;
    }
    
    return true;
}
```

### Auditoría y Trazabilidad

#### Registro en Historial
```php
'historial_estados' => [[
    'estado' => 'pendiente',
    'fecha' => now(),
    'usuario_id' => $user->_id,
    'usuario_nombre' => $user->name,
    'motivo' => 'Pedido creado por el cliente',
    'ip' => $request->ip()
]]
```

#### Log de Eventos
```php
\Log::info("Pedido creado exitosamente", [
    'pedido_id' => $pedido->_id,
    'numero_pedido' => $pedido->numero_pedido,
    'user_id' => $user->_id,
    'total' => $pedido->total_final,
    'productos' => count($detalles)
]);
```

---

## Caché y Optimización

### Estrategias de Caché

#### Estadísticas del Cliente
```php
// Caché de 5 minutos (300 segundos)
$cacheKey = "cliente_stats_{$user->_id}";

$stats = Cache::remember($cacheKey, 300, function () use ($user) {
    return $this->getClienteStats($user);
});

// Invalidar al crear/cancelar pedido
Cache::forget("cliente_stats_{$user->_id}");
```

#### Catálogo de Productos
```php
// Caché de 10 minutos (600 segundos)
$productos_catalogo = Cache::remember('productos_catalogo', 600, function () {
    return Producto::where('activo', true)
        ->where('stock', '>', 0)
        ->orderBy('destacado', 'desc')
        ->orderBy('veces_vendido', 'desc')
        ->orderBy('nombre', 'asc')
        ->get();
});
```

#### Productos Disponibles
```php
// Caché de 10 minutos
$productos = Cache::remember('productos_disponibles', 600, function () {
    return Producto::where('activo', true)
        ->where('stock', '>', 0)
        ->orderBy('nombre', 'asc')
        ->get();
});
```

#### Productos Recomendados
```php
// Caché de 1 hora (3600 segundos)
$cacheKey = "recomendaciones_{$user->_id}";

$productos = Cache::remember($cacheKey, 3600, function () use ($user) {
    // Lógica de recomendaciones
});
```

### Optimizaciones de MongoDB

#### Agregaciones
```php
// Usar agregaciones en lugar de múltiples queries
return [
    'total_pedidos' => Pedido::where('user_id', $userId)->count(),
    'total_gastado' => Pedido::where('user_id', $userId)
        ->whereIn('estado', ['confirmado', 'enviado', 'entregado'])
        ->sum('total_final') ?? 0,
    // ...
];
```

#### Índices Recomendados
```javascript
// En MongoDB
db.pedidos.createIndex({ "user_id": 1, "estado": 1 });
db.pedidos.createIndex({ "numero_pedido": 1 }, { unique: true });
db.pedidos.createIndex({ "created_at": -1 });
db.productos.createIndex({ "activo": 1, "stock": 1 });
db.users.createIndex({ "favoritos": 1 });
```

#### Datos Embebidos
```php
// Evitar joins guardando datos frecuentes embebidos
'cliente_data' => [
    '_id' => $user->_id,
    'name' => $user->name,
    'email' => $user->email,
    // ... otros datos del cliente
]
```

### Optimizaciones de Frontend

#### LocalStorage para PWA
```javascript
// Persistir carrito y favoritos offline
localStorage.setItem('carrito', JSON.stringify(this.carrito));
localStorage.setItem('favoritos', JSON.stringify(this.favoritos));
```

#### Lazy Loading de Imágenes
```html
<img src="{{ $producto->imagen_principal }}" 
     loading="lazy" 
     alt="{{ $producto->nombre }}">
```

#### Debounce en Búsqueda
```javascript
// Evitar búsquedas excesivas
let searchTimeout;
document.getElementById('searchProductos').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        buscarProductos(e.target.value);
    }, 300); // 300ms de espera
});
```

---

## Resumen de Funcionalidades

### ✅ Implementado y Funcionando

1. **Dashboard del Cliente**
   - Estadísticas en tiempo real
   - Pedidos recientes
   - Productos favoritos
   - Catálogo de productos
   - Sistema de referidos

2. **Gestión de Pedidos**
   - Listar pedidos con filtros
   - Crear nuevo pedido
   - Ver detalle/factura
   - Cancelar pedido
   - Historial de estados

3. **Carrito de Compras**
   - Agregar/eliminar productos
   - Actualizar cantidades
   - Persistencia en LocalStorage
   - Sincronización con stock
   - Checkout integrado

4. **Sistema de Favoritos**
   - Marcar productos como favoritos
   - Sincronización con MongoDB
   - Contador en tiempo real
   - Visualización en dashboard

5. **Catálogo de Productos**
   - Búsqueda en tiempo real
   - Filtros por categoría
   - Ordenamiento múltiple
   - Información de stock
   - Productos destacados

6. **Perfil de Usuario**
   - Edición de datos personales
   - Visualización de información
   - Código de referido

7. **Facturación**
   - Factura profesional colombiana
   - Descarga en PDF
   - Impresión optimizada
   - Historial completo

8. **Seguridad**
   - Protección XSS
   - Validación de entrada
   - Sanitización de datos
   - Auditoría completa
   - Límites anti-spam

9. **Optimización**
   - Caché multinivel
   - Datos embebidos en MongoDB
   - LocalStorage para PWA
   - Lazy loading
   - Agregaciones eficientes

---

## Notas Técnicas

### Tecnologías Utilizadas
- **Backend**: Laravel 10 + MongoDB
- **Frontend**: Blade Templates + Bootstrap 5
- **JavaScript**: Vanilla JS (ES6+) con clases
- **Base de Datos**: MongoDB Atlas
- **Caché**: Redis/File Cache
- **Estilos**: CSS3 con custom properties
- **Iconos**: Bootstrap Icons

### Compatibilidad
- **Navegadores**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Dispositivos**: Responsive (móvil, tablet, desktop)
- **PWA**: Funciona offline con LocalStorage
- **MongoDB**: 5.0+

### Consideraciones de Rendimiento
- Caché de 5-60 minutos según tipo de dato
- Paginación de 10 elementos
- Lazy loading de imágenes
- Debounce en búsquedas (300ms)
- Agregaciones MongoDB optimizadas
- Índices en campos frecuentes

---

## Próximas Mejoras Sugeridas

1. **Notificaciones Push** para estados de pedidos
2. **Chat en vivo** con vendedor asignado
3. **Programa de puntos** y recompensas
4. **Historial de visualización** de productos
5. **Listas de deseos** adicionales
6. **Compartir productos** en redes sociales
7. **Valoraciones y reseñas** de productos
8. **Métodos de pago** integrados (PSE, PayU)
9. **Tracking GPS** en tiempo real
10. **Notificaciones por email/SMS** automatizadas

---

## Conclusión

El módulo de cliente está completamente implementado y funcional, ofreciendo una experiencia de usuario moderna, segura y eficiente. Todas las funcionalidades descritas están actualmente operativas en el sistema.

**Fecha de documentación**: Octubre 2024
**Versión del sistema**: 2.0
**Estado**: Producción

---

**Desarrollado para**: Red de Ventas Proyecto Final  
**Empresa**: Arepa Llanerita  
**Base de datos**: MongoDB  
**Framework**: Laravel 10  
