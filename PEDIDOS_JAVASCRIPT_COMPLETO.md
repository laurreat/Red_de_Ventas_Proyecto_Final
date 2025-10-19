# Documentación Completa - pedidos-cliente-modern.js

## Versión: 3.0
## Fecha: 2025-10-19

---

## 📋 ÍNDICE

1. [Resumen General](#resumen-general)
2. [Clase PedidosClienteManager](#clase-pedidosclientemanager)
3. [Métodos Públicos](#métodos-públicos)
4. [Métodos Privados](#métodos-privados)
5. [Eventos y Listeners](#eventos-y-listeners)
6. [Uso y Ejemplos](#uso-y-ejemplos)
7. [API Reference](#api-reference)

---

## 📖 RESUMEN GENERAL

El archivo `pedidos-cliente-modern.js` contiene la clase **PedidosClienteManager** que maneja toda la lógica del frontend para:

- ✅ Crear pedidos
- ✅ Ver historial de pedidos
- ✅ Cancelar pedidos
- ✅ Repetir pedidos anteriores
- ✅ Gestionar carrito de compras
- ✅ Filtrar y buscar pedidos
- ✅ Notificaciones y modales
- ✅ Sincronización con localStorage
- ✅ Validaciones de formulario
- ✅ UX mejorada con animaciones

---

## 🏗️ CLASE: PedidosClienteManager

### Constructor

```javascript
constructor() {
  this.modals = {};        // Almacena modales activos
  this.toastCount = 0;     // Contador de notificaciones
  this.cart = new Map();   // Carrito de compras (Map para búsqueda O(1))
  this.init();             // Inicialización automática
}
```

### Propiedades

| Propiedad | Tipo | Descripción |
|-----------|------|-------------|
| `modals` | Object | Almacena referencias a modales activos |
| `toastCount` | Number | Contador para IDs únicos de toasts |
| `cart` | Map | Carrito de compras (key: productoId, value: {nombre, precio, cantidad}) |

---

## 🔓 MÉTODOS PÚBLICOS

### 1. Gestión de Carrito

#### `addToCart(productoId, nombre, precio, cantidad = 1)`
Agrega un producto al carrito.

```javascript
pedidosManager.addToCart('12345', 'Arepa de Queso', 5000, 2);
```

**Parámetros:**
- `productoId` (String): ID único del producto
- `nombre` (String): Nombre del producto
- `precio` (Number): Precio unitario
- `cantidad` (Number): Cantidad (por defecto 1)

**Retorna:** void

**Efectos:**
- Agrega/actualiza producto en el carrito
- Actualiza la vista del carrito
- Muestra notificación de éxito

---

#### `removeFromCart(productoId)`
Elimina un producto del carrito.

```javascript
pedidosManager.removeFromCart('12345');
```

**Parámetros:**
- `productoId` (String): ID del producto a eliminar

**Retorna:** void

**Efectos:**
- Elimina producto del carrito
- Desmarca checkbox si existe
- Actualiza vista del carrito
- Muestra notificación

---

#### `updateQuantity(productoId, cantidad)`
Actualiza la cantidad de un producto.

```javascript
pedidosManager.updateQuantity('12345', 5);
```

**Parámetros:**
- `productoId` (String): ID del producto
- `cantidad` (Number): Nueva cantidad (mínimo 1)

**Retorna:** void

---

#### `clearCart()`
Vacía completamente el carrito.

```javascript
pedidosManager.clearCart();
```

**Retorna:** void

**Efectos:**
- Limpia todos los productos
- Desmarca todos los checkboxes
- Resetea la vista del carrito
- Muestra notificación

---

#### `getCartTotal()`
Obtiene el total del carrito.

```javascript
const total = pedidosManager.getCartTotal();
console.log(`Total: $${total}`);
```

**Retorna:** Number - Total en pesos colombianos

---

#### `getCartItemCount()`
Obtiene la cantidad de items en el carrito.

```javascript
const count = pedidosManager.getCartItemCount();
console.log(`Items: ${count}`);
```

**Retorna:** Number - Cantidad de productos únicos

---

### 2. Gestión de Pedidos

#### `viewOrder(orderId)`
Navega a la vista de detalles de un pedido.

```javascript
pedidosManager.viewOrder('orden123');
```

**Parámetros:**
- `orderId` (String): ID del pedido

---

#### `showCancelModal(orderId)`
Muestra modal de confirmación para cancelar pedido.

```javascript
pedidosManager.showCancelModal('orden123');
```

**Parámetros:**
- `orderId` (String): ID del pedido a cancelar

---

#### `confirmCancel(orderId)`
Confirma y ejecuta la cancelación del pedido.

```javascript
pedidosManager.confirmCancel('orden123');
```

**Parámetros:**
- `orderId` (String): ID del pedido

**Proceso:**
1. Cierra modal
2. Muestra loading
3. Envía petición POST al backend
4. Muestra resultado
5. Recarga página si es exitoso

---

#### `repeatOrder(orderId)`
Repite un pedido anterior cargando sus productos.

```javascript
pedidosManager.repeatOrder('orden123');
```

**Parámetros:**
- `orderId` (String): ID del pedido a repetir

**Proceso:**
1. Obtiene detalles del pedido
2. Guarda productos en localStorage
3. Redirige a crear pedido
4. Los productos se cargan automáticamente

---

#### `confirmOrder()`
Valida y confirma el pedido actual.

```javascript
const isValid = pedidosManager.confirmOrder();
```

**Retorna:** Boolean - true si la validación pasa

**Validaciones:**
- Carrito no vacío
- Dirección de entrega
- Teléfono de contacto
- Método de pago

---

### 3. Filtros y Búsqueda

#### `applyFilters()`
Aplica filtros al listado de pedidos.

```javascript
pedidosManager.applyFilters();
```

**Lee de:**
- `#filter-estado` - Estado del pedido
- `#filter-fecha` - Rango de fechas
- `#filter-busqueda` - Término de búsqueda

**Retorna:** void (recarga página con parámetros URL)

---

#### `clearFilters()`
Limpia todos los filtros aplicados.

```javascript
pedidosManager.clearFilters();
```

**Retorna:** void (recarga página sin parámetros)

---

### 4. UI y Notificaciones

#### `showToast(type, title, message)`
Muestra notificación toast.

```javascript
pedidosManager.showToast('success', '¡Éxito!', 'Pedido creado correctamente');
pedidosManager.showToast('error', 'Error', 'No se pudo procesar');
pedidosManager.showToast('warning', 'Advertencia', 'Stock limitado');
pedidosManager.showToast('info', 'Info', 'Datos guardados');
```

**Parámetros:**
- `type` (String): 'success' | 'error' | 'warning' | 'info'
- `title` (String): Título de la notificación
- `message` (String): Mensaje descriptivo

**Características:**
- Auto-cierre en 5 segundos
- Icono según el tipo
- Animaciones suaves
- Apilado de notificaciones
- Botón de cerrar manual

---

#### `closeToast(id)`
Cierra una notificación específica.

```javascript
pedidosManager.closeToast('toast-1');
```

---

#### `showLoading(text = 'Cargando...')`
Muestra overlay de carga con mensaje.

```javascript
pedidosManager.showLoading('Procesando pedido...');
```

**Parámetros:**
- `text` (String): Texto a mostrar (opcional)

---

#### `hideLoading()`
Oculta el overlay de carga.

```javascript
pedidosManager.hideLoading();
```

---

### 5. Modales

#### `createModal(options)`
Crea un modal personalizado.

```javascript
pedidosManager.createModal({
  id: 'mi-modal',
  type: 'primary',      // primary, danger, warning, success, info
  icon: '🎉',
  title: 'Título del Modal',
  body: '<p>Contenido HTML</p>',
  footer: '<button>Cerrar</button>'
});
```

**Parámetros:**
- `options.id` (String): ID único del modal
- `options.type` (String): Tipo de modal para color
- `options.icon` (String): Emoji o icono
- `options.title` (String): Título
- `options.body` (String): Contenido HTML
- `options.footer` (String): HTML del footer (opcional)

**Retorna:** Object - {backdrop, modal}

---

#### `showModal(id)`
Muestra un modal existente.

```javascript
pedidosManager.showModal('mi-modal');
```

---

#### `closeModal(id)`
Cierra un modal específico.

```javascript
pedidosManager.closeModal('mi-modal');
```

---

#### `closeAllModals()`
Cierra todos los modales abiertos.

```javascript
pedidosManager.closeAllModals();
```

---

### 6. Utilidades

#### `formatNumber(num)`
Formatea número con separador de miles.

```javascript
pedidosManager.formatNumber(15000);  // "15.000"
```

---

#### `formatCurrency(amount)`
Formatea como moneda colombiana.

```javascript
pedidosManager.formatCurrency(15000);  // "COP 15.000"
```

---

#### `formatDate(date)`
Formatea fecha en español.

```javascript
pedidosManager.formatDate(new Date());  // "19 de octubre de 2025, 14:30"
```

---

#### `escapeHtml(text)`
Escapa HTML para prevenir XSS.

```javascript
pedidosManager.escapeHtml('<script>alert("xss")</script>');
// "&lt;script&gt;alert("xss")&lt;/script&gt;"
```

---

## 🔒 MÉTODOS PRIVADOS (Internos)

### `init()`
Inicializa el manager.

**Acciones:**
- Configura event listeners
- Anima tarjetas
- Verifica PWA
- Carga carrito desde localStorage

---

### `initEventListeners()`
Configura todos los event listeners.

**Eventos configurados:**
- Click en acciones de pedidos
- Submit de formularios
- Cerrar modales
- ESC para cerrar modales
- Prevenir múltiples envíos

---

### `animateCards()`
Aplica animaciones de entrada a las tarjetas.

**Elementos animados:**
- `.pedido-card`
- `.pedido-stat-card`
- `.pedidos-form-section`

---

### `initCartFromLocalStorage()`
Carga productos desde localStorage al carrito.

**Proceso:**
1. Lee `localStorage.getItem('carrito')`
2. Marca checkboxes correspondientes
3. Establece cantidades
4. Actualiza vista del carrito
5. Limpia localStorage
6. Muestra notificación

---

### `updateCart()`
Actualiza la vista del resumen del carrito.

**Actualiza:**
- `#cartItems` - Lista de productos
- `#cartTotal` - Total en pesos
- `#submitBtn` - Estado del botón
- `#productosSeleccionados` - Contador

---

### `checkPWAStatus()`
Verifica si hay Service Worker registrado.

---

## 📡 EVENTOS Y LISTENERS

### Eventos de Click

```javascript
// Ver pedido
<button data-action="view-order" data-order-id="123">Ver</button>

// Cancelar pedido
<button data-action="cancel-order" data-order-id="123">Cancelar</button>

// Repetir pedido
<button data-action="repeat-order" data-order-id="123">Repetir</button>

// Aplicar filtros
<button data-action="filter">Filtrar</button>

// Limpiar filtros
<button data-action="clear-filters">Limpiar</button>

// Cerrar modal
<button data-close-modal="mi-modal">Cerrar</button>
```

### Eventos de Teclado

- **ESC**: Cierra todos los modales abiertos

### Eventos de Formulario

- **Submit**: Valida y previene múltiples envíos

---

## 💻 USO Y EJEMPLOS

### Ejemplo 1: Agregar Producto al Carrito

```javascript
// Desde un botón
<button onclick="agregarProducto('123', 'Arepa', 5000)">
  Agregar
</button>

<script>
function agregarProducto(id, nombre, precio) {
  pedidosManager.addToCart(id, nombre, precio, 1);
}
</script>
```

### Ejemplo 2: Validar Formulario Antes de Enviar

```javascript
document.getElementById('miFormulario').addEventListener('submit', (e) => {
  if (!pedidosManager.confirmOrder()) {
    e.preventDefault();
    return false;
  }
});
```

### Ejemplo 3: Mostrar Notificación Personalizada

```javascript
// Éxito
pedidosManager.showToast('success', '¡Genial!', 'Pedido confirmado');

// Error
pedidosManager.showToast('error', 'Error', 'No se pudo procesar');

// Advertencia
pedidosManager.showToast('warning', 'Atención', 'Stock limitado');

// Información
pedidosManager.showToast('info', 'Info', 'Datos guardados');
```

### Ejemplo 4: Modal Personalizado

```javascript
pedidosManager.createModal({
  id: 'confirm-delete',
  type: 'danger',
  icon: '⚠️',
  title: '¿Eliminar producto?',
  body: '<p>Esta acción no se puede deshacer</p>',
  footer: `
    <button onclick="pedidosManager.closeModal('confirm-delete')">
      Cancelar
    </button>
    <button onclick="eliminarProducto()">
      Eliminar
    </button>
  `
});

pedidosManager.showModal('confirm-delete');
```

### Ejemplo 5: Cargar Productos desde Otra Página

```javascript
// Página de productos
function irACrearPedido() {
  const productos = [
    { id: '1', nombre: 'Arepa', precio: 5000, cantidad: 2 },
    { id: '2', nombre: 'Empanada', precio: 3000, cantidad: 3 }
  ];
  
  localStorage.setItem('carrito', JSON.stringify(productos));
  window.location.href = '/cliente/pedidos/create';
}

// En create.blade.php se cargan automáticamente
```

---

## 📚 API REFERENCE

### Métodos de Carrito

| Método | Parámetros | Retorna | Descripción |
|--------|-----------|---------|-------------|
| `addToCart()` | id, nombre, precio, cantidad | void | Agrega producto |
| `removeFromCart()` | id | void | Elimina producto |
| `updateQuantity()` | id, cantidad | void | Actualiza cantidad |
| `clearCart()` | - | void | Vacía carrito |
| `getCartTotal()` | - | Number | Total del carrito |
| `getCartItemCount()` | - | Number | Cantidad de items |

### Métodos de Pedidos

| Método | Parámetros | Retorna | Descripción |
|--------|-----------|---------|-------------|
| `viewOrder()` | orderId | void | Ver detalles |
| `showCancelModal()` | orderId | void | Modal cancelar |
| `confirmCancel()` | orderId | void | Cancelar pedido |
| `repeatOrder()` | orderId | void | Repetir pedido |
| `confirmOrder()` | - | Boolean | Validar y confirmar |

### Métodos de UI

| Método | Parámetros | Retorna | Descripción |
|--------|-----------|---------|-------------|
| `showToast()` | type, title, message | void | Notificación |
| `closeToast()` | id | void | Cerrar notificación |
| `showLoading()` | text | void | Mostrar loading |
| `hideLoading()` | - | void | Ocultar loading |
| `createModal()` | options | Object | Crear modal |
| `showModal()` | id | void | Mostrar modal |
| `closeModal()` | id | void | Cerrar modal |
| `closeAllModals()` | - | void | Cerrar todos |

### Métodos de Utilidad

| Método | Parámetros | Retorna | Descripción |
|--------|-----------|---------|-------------|
| `formatNumber()` | num | String | Formato número |
| `formatCurrency()` | amount | String | Formato moneda |
| `formatDate()` | date | String | Formato fecha |
| `escapeHtml()` | text | String | Escape HTML |

---

## 🔐 SEGURIDAD

### Prevención de XSS

Todos los métodos que renderizan contenido HTML usan `escapeHtml()`:

```javascript
escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}
```

### CSRF Token

Todas las peticiones AJAX incluyen el token CSRF:

```javascript
headers: {
  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

### Validación de Formularios

- Validación en frontend antes de enviar
- Prevención de múltiples envíos
- Feedback inmediato al usuario

---

## 🎨 ESTILOS CSS REQUERIDOS

### Clases para Modales

```css
.pedido-modal-backdrop { }
.pedido-modal { }
.pedido-modal-content { }
.pedido-modal-header { }
.pedido-modal-body { }
.pedido-modal-footer { }
.pedido-modal-icon-primary { }
.pedido-modal-icon-danger { }
.pedido-modal-icon-warning { }
```

### Clases para Toasts

```css
.toast-container { }
.toast { }
.toast-success { }
.toast-error { }
.toast-warning { }
.toast-info { }
.toast-icon { }
.toast-content { }
.toast-title { }
.toast-message { }
```

### Clases para Loading

```css
.pedidos-loading-overlay { }
.pedidos-loading-content { }
.pedidos-loading-spinner { }
.pedidos-loading-text { }
```

---

## 🧪 TESTING

### Pruebas Manuales

```javascript
// 1. Test de carrito
pedidosManager.addToCart('test1', 'Producto Test', 1000, 2);
console.assert(pedidosManager.getCartTotal() === 2000, 'Total correcto');

// 2. Test de notificaciones
pedidosManager.showToast('success', 'Test', 'Mensaje de prueba');

// 3. Test de modal
pedidosManager.createModal({
  id: 'test-modal',
  type: 'info',
  icon: '🧪',
  title: 'Test Modal',
  body: 'Contenido de prueba'
});
pedidosManager.showModal('test-modal');

// 4. Test de formateo
console.log(pedidosManager.formatNumber(1500000)); // "1.500.000"
console.log(pedidosManager.formatCurrency(1500000)); // "COP 1.500.000"
```

---

## 📝 CHANGELOG

### Versión 3.0 (2025-10-19)
- ✅ Refactorización completa de la clase
- ✅ Agregados 20+ métodos públicos
- ✅ Sincronización con localStorage
- ✅ Sistema de notificaciones mejorado
- ✅ Modales dinámicos
- ✅ Validaciones de formulario
- ✅ Prevención de XSS
- ✅ Gestión de carrito completa
- ✅ PWA ready
- ✅ Documentación completa

### Versión 2.0 (Anterior)
- Versión minificada
- Funcionalidad básica

---

## 🤝 CONTRIBUCIÓN

Para modificar o extender esta clase:

1. Mantener coherencia con métodos existentes
2. Agregar documentación JSDoc
3. Validar entradas de usuario
4. Escapar HTML cuando sea necesario
5. Probar en todos los navegadores
6. Actualizar esta documentación

---

## 📞 SOPORTE

Para reportar bugs o sugerir mejoras, contacta al equipo de desarrollo.

---

**Versión:** 3.0  
**Autor:** Equipo de Desarrollo  
**Última Actualización:** 2025-10-19 02:30 UTC  
**Estado:** ✅ Producción Ready
