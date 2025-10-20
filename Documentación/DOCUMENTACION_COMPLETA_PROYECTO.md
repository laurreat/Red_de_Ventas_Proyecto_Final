# DOCUMENTACIÓN COMPLETA DEL PROYECTO
# RED DE VENTAS MLM - AREPA LA LLANERITA

## 📋 ÍNDICE

1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Módulos del Sistema](#módulos-del-sistema)
4. [Base de Datos MongoDB](#base-de-datos-mongodb)
5. [Modelos y Relaciones](#modelos-y-relaciones)
6. [Controladores Detallados](#controladores-detallados)
7. [Interfaces de Usuario](#interfaces-de-usuario)
8. [Servicios y Utilidades](#servicios-y-utilidades)
9. [Seguridad y Autenticación](#seguridad-y-autenticación)
10. [API y Rutas](#api-y-rutas)
11. [Configuración y Despliegue](#configuración-y-despliegue)

---

## 1. RESUMEN EJECUTIVO

### 1.1 Descripción del Proyecto
**Red de Ventas MLM - Arepa La Llanerita** es un sistema integral de gestión de ventas multinivel (Multi-Level Marketing) desarrollado con Laravel 12 y MongoDB. El sistema permite administrar una red de vendedores, gestionar pedidos, calcular comisiones automáticamente, y ofrecer un catálogo público de productos.

### 1.2 Características Principales
- ✅ **Sistema MLM Completo**: Gestión de referidos multinivel con cálculo automático de comisiones
- ✅ **4 Roles Diferenciados**: Administrador, Líder, Vendedor y Cliente
- ✅ **Gestión de Productos**: Catálogo completo con inventario en tiempo real
- ✅ **Procesamiento de Pedidos**: Flujo completo desde creación hasta entrega
- ✅ **Sistema de Comisiones**: Cálculo automático de comisiones por ventas directas y referidos
- ✅ **Dashboard Interactivos**: Interfaces específicas para cada rol con métricas en tiempo real
- ✅ **Notificaciones en Tiempo Real**: Sistema completo de alertas y notificaciones
- ✅ **Reportes y Analytics**: Generación de reportes detallados de ventas y rendimiento
- ✅ **Auditoría Completa**: Trazabilidad de todas las acciones del sistema
- ✅ **Sistema de Cupones**: Descuentos y promociones configurables
- ✅ **Zonas de Entrega**: Gestión geográfica de entregas
- ✅ **Control de Inventario**: Movimientos de stock con historial completo

### 1.3 Stack Tecnológico
- **Backend**: Laravel 12.x (PHP 8.2+)
- **Base de Datos**: MongoDB 7.0+ (NoSQL)
- **Frontend**: Blade Templates, TailwindCSS 3.x, Alpine.js 3.x
- **Componentes Reactivos**: Laravel Livewire 3.6
- **Librerías Adicionales**:
  - Laravel UI 4.6 - Autenticación
  - MongoDB Laravel Driver 5.0 - Integración con MongoDB
  - DomPDF - Generación de PDFs
  - Intervention Image 3.11 - Manipulación de imágenes
  - Laravel Tinker - REPL interactivo
  - Laravel Pail - Visualización de logs

### 1.4 Estadísticas del Proyecto
- **Total de líneas de código**: ~64,546 líneas
  - Controladores: 16,277 líneas
  - Modelos: 3,817 líneas
  - Vistas Blade: 44,452 líneas
- **Colecciones MongoDB**: 13 colecciones
- **Controladores**: 42 controladores organizados por rol
- **Modelos**: 21 modelos con soporte MongoDB
- **Vistas Blade**: 300+ archivos de interfaz
- **Rutas Definidas**: 200+ rutas web protegidas

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Arquitectura General - Diagrama

```
┌──────────────────────────────────────────────────────────────┐
│                    CAPA DE PRESENTACIÓN                       │
│                                                               │
│  ┌─────────────┐  ┌─────────────┐  ┌──────────────┐        │
│  │   Admin     │  │   Líder     │  │   Vendedor   │        │
│  │  Dashboard  │  │  Dashboard  │  │   Dashboard  │        │
│  └─────────────┘  └─────────────┘  └──────────────┘        │
│                                                               │
│  ┌─────────────┐  ┌─────────────┐  ┌──────────────┐        │
│  │   Cliente   │  │  Catálogo   │  │   Público    │        │
│  │  Dashboard  │  │   Público   │  │   Welcome    │        │
│  └─────────────┘  └─────────────┘  └──────────────┘        │
└───────────────────────┬──────────────────────────────────────┘
                        │ HTTP/HTTPS
                        ▼
┌──────────────────────────────────────────────────────────────┐
│                  CAPA DE APLICACIÓN (Laravel)                 │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  ROUTING LAYER (web.php / api.php / console.php)     │   │
│  └────────────────────┬─────────────────────────────────┘   │
│                       │                                       │
│  ┌────────────────────▼─────────────────────────────────┐   │
│  │  MIDDLEWARE LAYER                                     │   │
│  │  - Autenticación (auth)                              │   │
│  │  - Autorización por Roles (role:admin,lider,etc)    │   │
│  │  - CSRF Protection                                    │   │
│  │  - Rate Limiting                                      │   │
│  │  - Email Verification                                 │   │
│  └────────────────────┬─────────────────────────────────┘   │
│                       │                                       │
│  ┌────────────────────▼─────────────────────────────────┐   │
│  │  CONTROLLER LAYER                                     │   │
│  │  ┌─────────────┐  ┌─────────────┐  ┌────────────┐  │   │
│  │  │   Admin     │  │   Líder     │  │  Vendedor  │  │   │
│  │  │ Controllers │  │ Controllers │  │ Controllers│  │   │
│  │  └─────────────┘  └─────────────┘  └────────────┘  │   │
│  │  ┌─────────────┐  ┌─────────────┐  ┌────────────┐  │   │
│  │  │  Cliente    │  │    Auth     │  │   Public   │  │   │
│  │  │ Controllers │  │ Controllers │  │ Controllers│  │   │
│  │  └─────────────┘  └─────────────┘  └────────────┘  │   │
│  └────────────────────┬─────────────────────────────────┘   │
│                       │                                       │
│  ┌────────────────────▼─────────────────────────────────┐   │
│  │  SERVICE LAYER                                        │   │
│  │  - ComisionService (Cálculo de comisiones)           │   │
│  │  - NotificationService (Gestión de notificaciones)   │   │
│  │  - CacheService (Optimización de consultas)          │   │
│  │  - OptimizedQueryService (Consultas optimizadas)     │   │
│  │  - PasswordResetService (Recuperación passwords)     │   │
│  └────────────────────┬─────────────────────────────────┘   │
│                       │                                       │
│  ┌────────────────────▼─────────────────────────────────┐   │
│  │  MODEL LAYER (Eloquent MongoDB)                       │   │
│  │  - User, Producto, Pedido, Comision                  │   │
│  │  - Referido, Notificacion, Categoria                 │   │
│  │  - DetallePedido, MovimientoInventario               │   │
│  │  - ZonaEntrega, Configuracion, Cupon, Auditoria     │   │
│  └────────────────────┬─────────────────────────────────┘   │
└────────────────────────┼─────────────────────────────────────┘
                         │ MongoDB Driver
                         ▼
┌──────────────────────────────────────────────────────────────┐
│                    CAPA DE DATOS (MongoDB)                    │
│                                                               │
│  Collections (13):                                            │
│  ✅ users              ✅ productos          ✅ pedidos       │
│  ✅ comisiones         ✅ referidos          ✅ notificaciones│
│  ✅ categorias         ✅ detalle_pedidos    ✅ cupones       │
│  ✅ zonas_entrega      ✅ configuraciones    ✅ auditorias    │
│  ✅ movimientos_inventario                                    │
│                                                               │
│  Features:                                                    │
│  - Embedded Documents (Documentos Anidados)                  │
│  - Aggregation Pipeline (Consultas Complejas)               │
│  - Índices Optimizados                                       │
│  - Replicación y Sharding Ready                             │
└──────────────────────────────────────────────────────────────┘
```

### 2.2 Flujo de Datos

#### Flujo de Autenticación
```
Usuario → Login Form → AuthController → MongoDB (users) 
→ Session Created → Redirect según rol
```

#### Flujo de Creación de Pedido
```
Vendedor → Crear Pedido Form → PedidoController 
→ Validar Datos → Crear Pedido en MongoDB 
→ Actualizar Stock (MovimientoInventario) 
→ ComisionService → Calcular Comisiones 
→ Crear Comision en MongoDB 
→ NotificationService → Enviar Notificaciones 
→ Redirect a Ver Pedido
```

#### Flujo de Sistema MLM
```
Nuevo Usuario Registrado con Código Referido 
→ Buscar Referidor en MongoDB 
→ Crear Registro en Referidos 
→ Actualizar total_referidos del Referidor 
→ Cuando el Referido realiza venta 
→ ComisionService calcula comisión del Referidor 
→ Crear Comision tipo "referido_nivel_1" o "referido_nivel_2"
```

---

## 3. MÓDULOS DEL SISTEMA

El sistema está organizado en 4 módulos principales según los roles de usuario:

### 3.1 MÓDULO DE ADMINISTRACIÓN (Admin)

**Ruta Base**: `/admin/*`  
**Middleware**: `auth`, `role:administrador`  
**Dashboard**: `resources/views/dashboard/admin-spa.blade.php`

#### 3.1.1 Gestión de Usuarios

**Controlador**: `App\Http\Controllers\Admin\UserController`  
**Vistas**: `resources/views/admin/users/`

**Funcionalidades de la Interfaz**:

**A. Lista de Usuarios** (`index.blade.php`)
- ✅ Tabla responsiva con todos los usuarios del sistema
- ✅ Columnas: ID, Nombre, Email, Cédula, Rol, Estado, Acciones
- ✅ Filtros avanzados:
  - Por rol (Admin, Líder, Vendedor, Cliente)
  - Por estado (Activo/Inactivo)
  - Búsqueda por nombre, email, cédula
- ✅ Paginación (20 resultados por página)
- ✅ Badges visuales para roles con colores distintivos
- ✅ Indicador de estado (verde activo / rojo inactivo)
- ✅ Acciones por fila:
  - 👁️ Ver detalles
  - ✏️ Editar
  - 🔄 Activar/Desactivar
  - 🗑️ Eliminar (con confirmación)
- ✅ Contador total de usuarios
- ✅ Botón "Crear Usuario" destacado

**B. Crear Usuario** (`create.blade.php`)
- 📝 Formulario con validación en tiempo real
- **Campos del formulario**:
  - Nombre (requerido, texto, max 255)
  - Apellidos (requerido, texto, max 255)
  - Cédula (requerido, único, numérico)
  - Email (requerido, único, email válido)
  - Contraseña (requerido, min 8 caracteres)
  - Confirmar Contraseña
  - Teléfono (opcional, formato válido)
  - Dirección (opcional, textarea)
  - Ciudad (opcional, select con opciones)
  - Departamento (opcional, select con opciones)
  - Fecha de Nacimiento (opcional, date picker)
  - Rol (requerido, select: administrador, lider, vendedor, cliente)
  - Código de Referido (opcional, para vendedores)
  - Zonas Asignadas (checkbox multiple, solo para vendedores)
  - Estado Activo (checkbox, default checked)
- ✅ Validaciones JavaScript en tiempo real
- ✅ Campos condicionales según rol seleccionado
- ✅ Botones: Guardar, Guardar y Continuar, Cancelar

**C. Ver Usuario** (`show.blade.php`)
- 📊 Dashboard personalizado del usuario
- **Sección 1: Información Personal**
  - Avatar (imagen de perfil)
  - Nombre completo
  - Email, teléfono
  - Dirección completa
  - Fecha de nacimiento
  - Estado de cuenta
  - Fecha de registro
  - Último acceso
- **Sección 2: Estadísticas (según rol)**
  - **Para Vendedores/Líderes**:
    - Total ventas realizadas
    - Comisiones ganadas
    - Comisiones disponibles
    - Meta mensual y progreso
    - Total de referidos
    - Nivel en la red
  - **Para Clientes**:
    - Total de pedidos
    - Monto total gastado
    - Pedido promedio
    - Productos favoritos
- **Sección 3: Red de Referidos** (si aplica)
  - Árbol visual de referidos multinivel
  - Total referidos directos (nivel 1)
  - Total referidos indirectos (nivel 2+)
  - Código de referido personal
- **Sección 4: Historial de Actividad**
  - Últimas ventas realizadas
  - Últimos pedidos
  - Últimas comisiones recibidas
  - Cambios de estado recientes
- **Sección 5: Acciones Rápidas**
  - Editar usuario
  - Cambiar contraseña
  - Activar/Desactivar
  - Ver pedidos del usuario
  - Ver comisiones del usuario
  - Generar reporte individual

**D. Editar Usuario** (`edit.blade.php`)
- ✏️ Formulario pre-llenado con datos actuales
- Mismos campos que crear, excepto contraseña
- ✅ Opción "Cambiar Contraseña" (opcional)
- ✅ Historial de cambios (auditoría)
- ✅ Última modificación y por quién
- ✅ Botones: Actualizar, Cancelar

**Métodos del Controlador UserController**:
```php
- index(): Lista usuarios con filtros y búsqueda
- create(): Muestra formulario de creación
- store(Request $request): Valida y crea usuario
- show($id): Muestra detalles completos
- edit($id): Muestra formulario de edición
- update(Request $request, $id): Actualiza usuario
- destroy($id): Elimina usuario (soft delete)
- toggleActive($id): Activa/desactiva usuario via AJAX
```

---

#### 3.1.2 Gestión de Productos

**Controlador**: `App\Http\Controllers\Admin\ProductoController`  
**Vistas**: `resources/views/admin/productos/`

**Funcionalidades de la Interfaz**:

**A. Catálogo de Productos** (`index.blade.php`)
- 🛍️ Vista en grid con tarjetas de producto
- **Elementos por tarjeta**:
  - Imagen del producto (con fallback)
  - Nombre del producto
  - Categoría (badge con color)
  - Precio (formato moneda)
  - Stock disponible
  - Indicador de stock (🟢 disponible, 🟡 bajo, 🔴 agotado)
  - Estado (activo/inactivo)
  - Acciones rápidas
- ✅ Filtros múltiples:
  - Por categoría (dropdown)
  - Por estado (activo/inactivo/todos)
  - Por disponibilidad de stock
  - Búsqueda por nombre
  - Rango de precios (slider)
- ✅ Ordenamiento:
  - Nombre (A-Z, Z-A)
  - Precio (menor a mayor, mayor a menor)
  - Stock (menor a mayor, mayor a menor)
  - Fecha de creación
- ✅ Vista alternativa: Tabla detallada
- ✅ Acciones masivas:
  - Activar/Desactivar múltiples
  - Exportar seleccionados
  - Ajustar precios en lote
- ✅ Alertas de stock bajo
- ✅ Contador de productos por estado

**B. Crear Producto** (`create.blade.php`)
- 📦 Formulario multi-sección
- **Sección 1: Información Básica**
  - Nombre (requerido, max 255)
  - Descripción (textarea, WYSIWYG editor)
  - Categoría (select con búsqueda)
  - Precio (decimal, formato moneda)
  - Estado (activo/inactivo)
- **Sección 2: Inventario**
  - Stock inicial (número)
  - Stock mínimo (número, para alertas)
  - Unidad de medida
  - SKU/Código (opcional, único)
- **Sección 3: Imágenes**
  - Imagen principal (drag & drop)
  - Galería adicional (múltiples imágenes)
  - Preview en tiempo real
  - Edición básica (crop, resize)
- **Sección 4: Detalles del Producto**
  - Ingredientes (lista editable)
  - Especificaciones técnicas (key-value pairs)
  - Tiempo de preparación (minutos)
  - Información nutricional
  - Alergenos
- **Sección 5: SEO y Marketing**
  - Meta título
  - Meta descripción
  - Palabras clave
  - Destacado (checkbox)
  - Nuevo (checkbox)
  - Oferta (checkbox)
- ✅ Vista previa del producto
- ✅ Validación en tiempo real
- ✅ Autoguardado cada 2 minutos
- ✅ Botones: Publicar, Guardar Borrador, Cancelar

**C. Ver Producto** (`show.blade.php`)
- 🔍 Vista detallada estilo e-commerce
- **Panel Izquierdo**:
  - Galería de imágenes (slider)
  - Zoom en hover
  - Miniaturas navegables
- **Panel Derecho**:
  - Nombre y categoría
  - Precio actual
  - Stock disponible con indicador visual
  - Descripción completa
  - Especificaciones técnicas (tabla)
  - Ingredientes (lista)
  - Tiempo de preparación
- **Tabs Inferiores**:
  - **Tab Estadísticas**:
    - Total de ventas
    - Unidades vendidas
    - Ingresos generados
    - Gráfico de ventas histórico
    - Productos relacionados más vendidos
  - **Tab Historial de Precios**:
    - Tabla con cambios de precio
    - Fecha, precio anterior, nuevo precio, usuario
    - Gráfico de evolución de precios
  - **Tab Reviews** (futuro):
    - Calificación promedio (estrellas)
    - Total de reviews
    - Lista de reviews con respuestas
  - **Tab Inventario**:
    - Movimientos recientes
    - Entradas y salidas
    - Tabla con fecha, tipo, cantidad, usuario, motivo
- ✅ Acciones rápidas flotantes:
  - Editar
  - Clonar producto
  - Activar/Desactivar
  - Ajustar stock
  - Ver pedidos con este producto
  - Exportar información

**D. Editar Producto** (`edit.blade.php`)
- ✏️ Mismo formulario que crear, pre-llenado
- ✅ Historial de cambios visible
- ✅ Comparación con versión anterior
- ✅ Opción de restaurar valores anteriores
- ✅ Notificación de cambio de precio (alerta vendedores)

**Métodos del Controlador ProductoController**:
```php
- index(): Lista productos con filtros complejos
- create(): Form con categorías disponibles
- store(Request $request): Crea producto, sube imágenes
- show($id): Detalle con estadísticas y relaciones
- edit($id): Form pre-llenado
- update(Request $request, $id): Actualiza y registra cambios
- destroy($id): Elimina (verifica si tiene ventas)
- toggleStatus($id): Cambia estado activo/inactivo
```

---


#### 3.1.3 Gestión de Pedidos

**Controlador**: `App\Http\Controllers\Admin\PedidoController`  
**Vistas**: `resources/views/admin/pedidos/`

**Funcionalidades de la Interfaz**:

**A. Lista de Pedidos** (`index.blade.php`)
- 📋 Tabla responsiva con todos los pedidos
- **Columnas**:
  - Número de Pedido (enlace clicable)
  - Cliente (nombre y email)
  - Vendedor (nombre con badge de rol)
  - Total (formato moneda destacado)
  - Estado (badge con color según estado)
  - Fecha de creación
  - Fecha entrega estimada
  - Acciones
- **Estados visuales**:
  - �� Pendiente (amarillo)
  - 🔵 Confirmado (azul)
  - 🟠 En Preparación (naranja)
  - 🟢 Listo (verde claro)
  - 🚚 En Camino (azul oscuro)
  - ✅ Entregado (verde)
  - ❌ Cancelado (rojo)
- ✅ Filtros avanzados:
  - Por estado (multi-select)
  - Por vendedor (autocomplete)
  - Por cliente (autocomplete)
  - Por rango de fechas (date picker)
  - Por rango de montos (slider)
  - Por zona de entrega
- ✅ Búsqueda rápida por número de pedido
- ✅ Ordenamiento por cualquier columna
- ✅ Acciones por fila:
  - Ver detalles completos
  - Cambiar estado rápido
  - Imprimir ticket
  - Cancelar (con motivo)
  - Editar datos de entrega
- ✅ Acciones masivas:
  - Exportar a Excel/PDF
  - Cambio de estado masivo
  - Envío de notificaciones
  - Generación de reportes
- ✅ Estadísticas en header:
  - Total de pedidos del día
  - Total ventas del día
  - Pedidos pendientes
  - Pedidos en proceso
- ✅ Actualizaciones en tiempo real (polling)

**B. Crear Pedido** (`create.blade.php`)
- 🛒 Formulario wizard multi-paso
- **Paso 1: Selección de Cliente**
  - Búsqueda de cliente existente (autocomplete)
  - Crear cliente nuevo (modal)
  - Datos del cliente mostrados: nombre, email, teléfono, dirección
- **Paso 2: Asignación de Vendedor**
  - Búsqueda de vendedor (autocomplete filtrado por zonas)
  - Asignación automática según zona
  - Datos del vendedor: nombre, zonas asignadas, ventas del mes
- **Paso 3: Selección de Productos**
  - Catálogo de productos disponibles
  - Búsqueda rápida de productos
  - Filtro por categoría
  - Vista en grid o lista
  - Para cada producto:
    - Imagen
    - Nombre
    - Precio
    - Stock disponible
    - Botón "Agregar" con selector de cantidad
  - Carrito lateral mostrando:
    - Productos agregados
    - Cantidad (editable)
    - Precio unitario
    - Subtotal por ítem
    - Botón eliminar
  - Cálculos automáticos:
    - Subtotal
    - Descuento (si aplica)
    - IVA (configurable)
    - Total final
- **Paso 4: Aplicar Descuentos** (opcional)
  - Búsqueda de cupones
  - Validación en tiempo real
  - Aplicación de descuento
  - Descuento manual (porcentaje o monto fijo)
- **Paso 5: Datos de Entrega**
  - Dirección de entrega (textarea)
  - Teléfono de contacto
  - Zona de entrega (select)
  - Precio de domicilio (automático según zona)
  - Fecha entrega estimada (date picker)
  - Hora entrega estimada (time picker)
  - Notas especiales (textarea)
  - Método de pago (select)
- **Paso 6: Resumen y Confirmación**
  - Vista previa de todos los datos
  - Resumen del cliente
  - Resumen del vendedor
  - Lista de productos con totales
  - Datos de entrega
  - Total final destacado
  - Checkbox confirmación
  - Botones: Crear Pedido, Volver, Cancelar
- ✅ Validación en cada paso
- ✅ Navegación entre pasos
- ✅ Guardado de progreso
- ✅ Preview antes de confirmar

**C. Ver Pedido** (`show.blade.php`)
- 📄 Interfaz detallada estilo factura/ticket
- **Header del Pedido**:
  - Número de pedido (grande, destacado)
  - Estado actual (badge grande)
  - Fecha y hora de creación
  - Botones de acción flotantes:
    - Cambiar estado
    - Editar
    - Imprimir
    - Descargar PDF
    - Enviar por email
    - Cancelar pedido
- **Sección 1: Timeline de Estados**
  - Línea de tiempo visual con todos los estados
  - Estados pasados (verde, completado)
  - Estado actual (azul, pulsante)
  - Estados futuros (gris, pendiente)
  - Fecha y hora de cada cambio
  - Usuario que realizó el cambio
  - Notas de cada cambio
- **Sección 2: Datos del Cliente**
  - Avatar (si tiene)
  - Nombre completo (enlace a perfil)
  - Email
  - Teléfono
  - Total de pedidos históricos
  - Cliente desde (fecha registro)
  - Badge si es cliente frecuente
- **Sección 3: Datos del Vendedor**
  - Avatar (si tiene)
  - Nombre completo (enlace a perfil)
  - Email
  - Zonas asignadas
  - Ventas del mes
  - Comisión de este pedido (calculada)
- **Sección 4: Productos del Pedido**
  - Tabla detallada:
    - Columnas: Imagen, Producto, Cantidad, Precio Unit., Subtotal
    - Totales parciales destacados
  - Subtotal
  - Descuentos aplicados (con detalle)
  - IVA (si aplica)
  - Costo de envío/domicilio
  - **Total Final** (destacado, grande)
- **Sección 5: Datos de Entrega**
  - Dirección completa
  - Zona de entrega
  - Teléfono de contacto
  - Fecha entrega estimada
  - Hora entrega estimada
  - Método de pago
  - Notas especiales (si las hay)
- **Sección 6: Información Adicional**
  - Comisiones calculadas:
    - Comisión del vendedor
    - Comisiones de referidos (si aplica)
    - Estado de comisiones
  - Historial de cambios (auditoría)
  - Actividad relacionada
  - Documentos adjuntos
- **Sección 7: Acciones de Gestión**
  - Cambiar estado (con modal de confirmación y notas)
  - Editar datos de entrega
  - Agregar/quitar productos (si estado lo permite)
  - Aplicar/quitar descuentos
  - Registrar pago
  - Asignar repartidor
  - Marcar como entregado
  - Cancelar pedido (con motivo obligatorio)
- ✅ Actualización automática de estado
- ✅ Notificaciones push de cambios
- ✅ Historial de visualizaciones

**Métodos del Controlador PedidoController**:
```php
- index(): Lista con filtros y búsqueda avanzada
- create(): Form wizard, carga productos y usuarios
- store(Request $request): 
  * Valida datos completos
  * Crea pedido
  * Crea detalles embebidos
  * Actualiza stock (MovimientoInventario)
  * Calcula comisiones (ComisionService)
  * Envía notificaciones (NotificationService)
  * Registra auditoría
- show($id): 
  * Carga pedido con todas las relaciones
  * Calcula estadísticas
  * Carga historial
- edit($id): Form de edición (solo datos permitidos)
- update(Request $request, $id):
  * Actualiza datos permitidos
  * Registra cambios en historial
- updateStatus(Request $request, $id):
  * Cambia estado
  * Registra en historial_estados
  * Trigger eventos según estado
  * Notifica a cliente y vendedor
- destroy($id):
  * Cancela pedido
  * Devuelve stock
  * Cancela comisiones pendientes
  * Registra auditoría
- searchCliente(Request $request): 
  * API para autocomplete de clientes
- searchVendedor(Request $request):
  * API para autocomplete de vendedores
```

---

#### 3.1.4 Sistema de Comisiones

**Controlador**: `App\Http\Controllers\Admin\ComisionController`  
**Vistas**: `resources/views/admin/comisiones/`

**Funcionalidades de la Interfaz**:

**A. Dashboard de Comisiones** (`index.blade.php`)
- 💰 Vista general del sistema de comisiones
- **KPIs en Header**:
  - Total comisiones generadas (mes actual)
  - Total comisiones pendientes de pago
  - Total comisiones pagadas (mes actual)
  - Comisiones por aprobar
- **Gráficos**:
  - Evolución de comisiones (línea temporal)
  - Comisiones por tipo (pie chart)
  - Top 10 vendedores por comisiones (bar chart)
  - Distribución por niveles MLM (funnel chart)
- **Tabla de Comisiones**:
  - Columnas: Usuario, Tipo, Monto, Estado, Pedido, Fecha
  - Filtros:
    - Por usuario (autocomplete)
    - Por tipo (venta_directa, referido_nivel_1, referido_nivel_2, bono_liderazgo)
    - Por estado (pendiente, aprobada, pagada)
    - Por rango de fechas
    - Por rango de montos
  - Estados visuales:
    - 🟡 Pendiente
    - 🟢 Aprobada
    - ✅ Pagada
  - Acciones:
    - Ver detalles
    - Aprobar (si pendiente)
    - Marcar como pagada (si aprobada)
    - Rechazar (si pendiente)
    - Ver pedido relacionado
- **Acciones Masivas**:
  - Aprobar seleccionadas
  - Marcar como pagadas en lote
  - Exportar a Excel/PDF
  - Generar liquidación
  - Enviar notificaciones de pago

**B. Detalle de Comisión** (`show.blade.php`)
- 📊 Vista completa de una comisión
- **Header**:
  - Monto grande destacado
  - Estado con badge
  - Tipo de comisión
  - Fecha de creación
- **Información del Beneficiario**:
  - Datos completos del usuario
  - Total comisiones acumuladas
  - Saldo disponible
  - Método de pago preferido
  - Datos bancarios (si tiene)
- **Detalles del Cálculo**:
  - Pedido que generó la comisión (enlace)
  - Total del pedido
  - Porcentaje aplicado
  - Fórmula de cálculo
  - Configuración de comisión usada
- **Información del Pedido**:
  - Número de pedido
  - Cliente
  - Vendedor
  - Total
  - Estado
  - Fecha
- **Historial y Trazabilidad**:
  - Fecha de creación
  - Fecha de aprobación (si aplica)
  - Usuario que aprobó
  - Fecha de pago (si aplica)
  - Usuario que registró pago
  - Método de pago utilizado
  - Número de transacción
  - Notas administrativas
- **Acciones Disponibles**:
  - Aprobar comisión
  - Rechazar comisión
  - Marcar como pagada
  - Registrar detalles de pago
  - Descargar comprobante
  - Enviar notificación

**C. Cálculo de Comisiones** (Modal)
- ⚙️ Herramienta para calcular/recalcular comisiones
- **Opciones**:
  - Calcular para pedido específico
  - Calcular para todos los pendientes
  - Recalcular con nuevas tasas
  - Simular cálculo (sin guardar)
- **Configuración**:
  - Porcentaje venta directa
  - Porcentaje referido nivel 1
  - Porcentaje referido nivel 2
  - Condiciones especiales
- **Preview antes de aplicar**:
  - Total comisiones a generar
  - Desglose por usuario
  - Desglose por tipo
- **Ejecución y Resultado**:
  - Barra de progreso
  - Log de procesamiento
  - Resumen de comisiones creadas
  - Errores (si los hay)

**Métodos del Controlador ComisionController**:
```php
- index(): 
  * Dashboard con estadísticas
  * Lista con filtros complejos
  * Gráficos de comisiones
- show($id):
  * Detalle completo de comisión
  * Historial de cambios
  * Información relacionada
- calcular(Request $request):
  * Calcula comisiones pendientes
  * Valida configuración
  * Genera comisiones masivamente
  * Registra en log
- exportar(Request $request):
  * Exporta datos de comisiones
  * Formato Excel o PDF
  * Con filtros aplicados
```

---

#### 3.1.5 Red de Referidos

**Controlador**: `App\Http\Controllers\Admin\ReferidoController`  
**Vistas**: `resources/views/admin/referidos/`

**Funcionalidades de la Interfaz**:

**A. Vista General de Red** (`index.blade.php`)
- 🌳 Dashboard del sistema MLM
- **Estadísticas Globales**:
  - Total de usuarios en la red
  - Usuarios activos vs inactivos
  - Total de niveles en la red
  - Profundidad máxima alcanzada
  - Tasa de conversión de referidos
  - Comisiones generadas por referidos
- **Métricas por Nivel**:
  - Nivel 1 (directos): cantidad, comisiones
  - Nivel 2 (indirectos): cantidad, comisiones
  - Nivel 3+: cantidad, comisiones
- **Top Referidores**:
  - Ranking por total referidos
  - Ranking por comisiones generadas
  - Ranking por red más activa
  - Datos: Nombre, Total Referidos, Comisiones, Red Activa
- **Árbol Interactivo de la Red**:
  - Visualización tipo organigrama
  - Navegación por niveles
  - Click en nodo para ver detalles
  - Zoom in/out
  - Búsqueda de usuario en el árbol
  - Colores según actividad:
    - Verde: Activo y vendiendo
    - Amarillo: Activo sin ventas recientes
    - Gris: Inactivo
- **Filtros y Búsqueda**:
  - Por nivel de profundidad
  - Por estado (activo/inactivo)
  - Por rango de referidos
  - Por comisiones generadas
  - Por fecha de ingreso

**B. Detalle de Red Individual** (`red.blade.php`)
- 👥 Vista de la red de un usuario específico
- **Información del Referidor Principal**:
  - Datos del usuario
  - Código de referido
  - Fecha de ingreso a la red
  - Total de personas referidas (directas e indirectas)
  - Comisiones generadas por su red
- **Árbol de su Red**:
  - Visualización completa multinivel
  - Nivel 1: Referidos directos
    - Foto, nombre, fecha ingreso, ventas, comisiones generadas
  - Nivel 2: Referidos de los referidos
    - Misma información
  - Niveles adicionales (si existen)
- **Estadísticas de la Red**:
  - Total en nivel 1
  - Total en nivel 2
  - Total en nivel 3+
  - Comisiones por nivel
  - Usuarios activos por nivel
  - Crecimiento mensual
- **Timeline de Crecimiento**:
  - Gráfico de crecimiento de la red en el tiempo
  - Hitos importantes
  - Ingresos de nuevos referidos
- **Análisis de Rendimiento**:
  - Tasa de activación de referidos
  - Promedio de ventas por referido
  - ROI de la red
  - Predicción de crecimiento

**C. Estadísticas Generales** (`estadisticas.blade.php`)
- 📈 Analytics completos del sistema MLM
- **Crecimiento de la Red**:
  - Gráfico de evolución temporal
  - Nuevos ingresos por mes
  - Tasa de crecimiento
  - Proyección de crecimiento
- **Análisis de Conversión**:
  - Referidos registrados vs activos
  - Tasa de conversión a vendedor
  - Tiempo promedio de activación
  - Tasa de abandono
- **Análisis de Comisiones**:
  - Total generado por referidos
  - Promedio por usuario
  - Distribución por niveles
  - Evolución temporal
- **Mapa de Calor**:
  - Zonas geográficas con más referidos
  - Penetración de mercado
  - Oportunidades de expansión
- **Reportes Descargables**:
  - Reporte completo de red (PDF/Excel)
  - Análisis de vendedores top
  - Proyecciones de crecimiento
  - Análisis de comisiones

**Métodos del Controlador ReferidoController**:
```php
- index():
  * Dashboard general de referidos
  * Estadísticas globales
  * Top referidores
- red($id = null):
  * Árbol de red de un usuario
  * Si no hay ID, muestra todos
  * Datos multinivel embebidos
- estadisticas():
  * Analytics completos
  * Gráficos de crecimiento
  * Análisis de conversión
- show($id):
  * Detalle de relación de referido
  * Historial de comisiones generadas
  * Performance del referido
- exportar(Request $request):
  * Exporta datos de la red
  * Excel o PDF
  * Con filtros aplicados
```

---

#### 3.1.6 Reportes y Analytics

**Controlador**: `App\Http\Controllers\Admin\ReporteController`  
**Vistas**: `resources/views/admin/reportes/`

**Funcionalidades de la Interfaz**:

**A. Reporte de Ventas** (`ventas.blade.php`)
- 📊 Analytics completos de ventas
- **Filtros de Período**:
  - Hoy
  - Esta semana
  - Este mes
  - Este año
  - Rango personalizado (date range picker)
  - Comparación con período anterior
- **KPIs Principales**:
  - Total de ventas (monto)
  - Cantidad de pedidos
  - Ticket promedio
  - Tasa de conversión
  - Crecimiento vs período anterior (%)
- **Gráficos Principales**:
  - **Evolución de Ventas** (línea temporal):
    - Ventas diarias/semanales/mensuales
    - Comparación con período anterior
    - Tendencia y proyección
  - **Ventas por Categoría** (pie chart):
    - Distribución porcentual
    - Monto por categoría
    - Productos más vendidos por categoría
  - **Ventas por Vendedor** (bar chart horizontal):
    - Top 10 vendedores
    - Monto de ventas
    - Cantidad de pedidos
    - Comisiones generadas
  - **Ventas por Zona** (mapa de calor):
    - Distribución geográfica
    - Concentración de ventas
    - Oportunidades de expansión
  - **Horarios de Mayor Venta** (heat map):
    - Por día de la semana
    - Por hora del día
    - Patrones de comportamiento
- **Tabla Detallada de Pedidos**:
  - Todos los pedidos del período
  - Columnas: Número, Cliente, Vendedor, Total, Estado, Fecha
  - Ordenamiento por cualquier columna
  - Exportable a Excel/PDF
- **Análisis de Productos**:
  - Productos más vendidos (top 20)
  - Productos con menos rotación
  - Productos más rentables
  - Análisis de stock
- **Análisis de Clientes**:
  - Nuevos clientes en el período
  - Clientes recurrentes
  - Valor de vida del cliente (LTV)
  - Tasa de retención
- **Opciones de Exportación**:
  - Excel detallado
  - PDF ejecutivo
  - CSV para análisis externo
  - Enviar por email

**B. Reporte de Productos** (`productos.blade.php`)
- 📦 Analytics de catálogo
- **Métricas Generales**:
  - Total de productos activos
  - Total de categorías
  - Valor total del inventario
  - Productos con stock bajo
  - Productos agotados
- **Análisis de Rotación**:
  - Productos de alta rotación
  - Productos de baja rotación
  - Productos sin ventas (período seleccionado)
  - Días promedio de rotación
- **Análisis de Rentabilidad**:
  - Productos más rentables
  - Margen de ganancia por producto
  - ROI por producto
  - Precio promedio de venta
- **Gráficos**:
  - Ventas por producto (bar chart)
  - Distribución de precios (histogram)
  - Stock por categoría (stacked bar)
  - Tendencia de ventas por producto
- **Tabla de Productos**:
  - Todas las métricas consolidadas
  - Producto, Categoría, Precio, Stock, Vendidos, Ingresos, Margen
  - Ordenamiento y filtros
  - Exportable
- **Alertas y Recomendaciones**:
  - Productos que requieren reorden
  - Productos candidatos para descuento
  - Productos para destacar
  - Productos para descontinuar

**C. Reporte de Comisiones** (`comisiones.blade.php`)
- 💰 Analytics de comisiones
- **Resumen Financiero**:
  - Total comisiones generadas
  - Total comisiones pagadas
  - Total comisiones pendientes
  - Proyección de pagos próximos
- **Comisiones por Tipo**:
  - Venta directa: monto y cantidad
  - Referido nivel 1: monto y cantidad
  - Referido nivel 2: monto y cantidad
  - Bonos de liderazgo: monto y cantidad
- **Gráficos**:
  - Evolución de comisiones (línea)
  - Distribución por tipo (pie)
  - Top beneficiarios (bar)
  - Comisiones pagadas vs pendientes (stacked bar)
- **Análisis por Usuario**:
  - Tabla con todos los beneficiarios
  - Total comisiones, pagadas, pendientes, saldo
  - Última fecha de pago
  - Método de pago preferido
- **Liquidaciones**:
  - Generar liquidación de período
  - Desglose por usuario
  - Estado de pagos
  - Documentación de respaldo
- **Proyecciones**:
  - Comisiones esperadas próximo mes
  - Basado en histórico
  - Basado en pipeline de pedidos

**Métodos del Controlador ReporteController**:
```php
- ventas(Request $request):
  * Genera reporte de ventas
  * Aplica filtros de fecha
  * Calcula KPIs
  * Prepara datos para gráficos
  * Retorna vista con datos
- productos(Request $request):
  * Analiza catálogo de productos
  * Métricas de rotación
  * Análisis de rentabilidad
  * Recomendaciones automáticas
- comisiones(Request $request):
  * Reporte de comisiones
  * Filtros por tipo y estado
  * Proyecciones
  * Liquidaciones
- exportarVentas(Request $request):
  * Exporta reporte a Excel o PDF
  * Aplica formato profesional
  * Incluye gráficos
```

---


#### 3.1.7 Configuración del Sistema

**Controlador**: `App\Http\Controllers\Admin\ConfiguracionController`  
**Vista**: `resources/views/admin/configuracion/index.blade.php`

**Funcionalidades**:
- ⚙️ Panel de configuración global del sistema
- **Configuración General**:
  - Nombre del negocio
  - Logo y favicon
  - Información de contacto
  - Redes sociales
  - Horarios de atención
  - Moneda y formato numérico
- **Configuración MLM**:
  - Porcentajes de comisión por nivel
  - Niveles máximos de profundidad
  - Condiciones para bonos
  - Metas y objetivos
- **Configuración de Pedidos**:
  - Estados disponibles
  - Tiempo estimado por estado
  - Políticas de cancelación
  - Métodos de pago activos
- **Configuración de Notificaciones**:
  - Canales activos (email, push, SMS)
  - Plantillas de mensajes
  - Frecuencia de envío
  - Usuarios que reciben notificaciones
- **Mantenimiento**:
  - Limpiar caché del sistema
  - Limpiar logs antiguos
  - Crear respaldo de base de datos
  - Ver información del sistema

---

### 3.2 MÓDULO DE LÍDER

**Ruta Base**: `/lider/*`  
**Middleware**: `auth`, `role:lider,administrador`  
**Dashboard**: `resources/views/lider/dashboard/index.blade.php`

#### 3.2.1 Dashboard del Líder

**Funcionalidades de la Interfaz**:

**Vista Principal** (`dashboard/index.blade.php`)
- 📊 Panel de control con métricas del equipo
- **KPIs del Equipo**:
  - Total de vendedores en el equipo
  - Ventas totales del equipo (mes actual)
  - Comisiones generadas
  - Meta del mes y progreso
  - Comparación con mes anterior
- **Gráficos de Rendimiento**:
  - Evolución de ventas del equipo (línea)
  - Ventas por vendedor (bar chart)
  - Cumplimiento de metas (gauge charts)
  - Tendencia de crecimiento
- **Top Performers**:
  - 5 mejores vendedores del mes
  - Ventas, comisiones, pedidos
  - Badge de reconocimiento
- **Alertas y Notificaciones**:
  - Vendedores que no han vendido
  - Metas en riesgo
  - Nuevos referidos en el equipo
  - Comisiones pendientes
- **Actividad Reciente**:
  - Últimas ventas del equipo
  - Nuevos miembros del equipo
  - Cambios de estado importantes
- **Acceso Rápido**:
  - Ver equipo completo
  - Asignar metas
  - Gestionar capacitaciones
  - Ver comisiones del equipo
  - Generar reportes

---

#### 3.2.2 Gestión de Equipo

**Controlador**: `App\Http\Controllers\Lider\EquipoController`  
**Vistas**: `resources/views/lider/equipo/`

**A. Lista del Equipo** (`index.blade.php`)
- 👥 Vista de todos los vendedores del equipo
- **Información por Vendedor**:
  - Avatar y nombre
  - Email y teléfono
  - Fecha de ingreso
  - Ventas del mes
  - Comisiones ganadas
  - Meta asignada y progreso
  - Estado (activo/inactivo)
  - Nivel en la red
- **Filtros**:
  - Por estado
  - Por cumplimiento de meta
  - Por ventas (alto, medio, bajo)
  - Por zona asignada
- **Acciones por Vendedor**:
  - Ver perfil completo
  - Ver historial de ventas
  - Asignar/actualizar meta
  - Enviar mensaje
  - Asignar capacitación
- **Visualización alternativa**:
  - Vista de tarjetas (grid)
  - Vista de tabla (detallada)
  - Vista de organigrama

**B. Perfil del Vendedor** (`show.blade.php`)
- 📋 Información completa del vendedor
- **Datos Personales**:
  - Información de contacto
  - Fecha de ingreso
  - Referido por
  - Nivel actual
- **Estadísticas de Rendimiento**:
  - Ventas totales (histórico)
  - Ventas del mes
  - Promedio de ventas mensuales
  - Total comisiones ganadas
  - Pedidos realizados
  - Ticket promedio
  - Tasa de conversión
- **Gráficos de Rendimiento**:
  - Evolución de ventas (6 meses)
  - Cumplimiento de metas (histórico)
  - Comisiones por mes
- **Historial de Ventas**:
  - Tabla con todos los pedidos
  - Filtrable por fecha
  - Exportable
- **Red de Referidos**:
  - Personas que ha referido
  - Estado de sus referidos
  - Comisiones generadas por referidos
- **Metas Asignadas**:
  - Meta actual
  - Progreso
  - Histórico de metas
  - Cumplimiento
- **Capacitaciones**:
  - Completadas
  - Pendientes
  - Progreso
- **Acciones**:
  - Asignar nueva meta
  - Enviar mensaje
  - Asignar capacitación
  - Exportar rendimiento
  - Ver pedidos completos

**Métodos del Controlador EquipoController**:
```php
- index(): Lista vendedores del equipo del líder
- show($id): Detalle completo del vendedor
- asignarMeta(Request $request, $id): Asigna meta a vendedor
- obtenerHistorialVentasAjax($id): API para tabla de ventas
- exportarHistorial($id): Exporta rendimiento a Excel
```

---

#### 3.2.3 Comisiones del Líder

**Controlador**: `App\Http\Controllers\Lider\ComisionController`  
**Vistas**: `resources/views/lider/comisiones/`

**Funcionalidades**:
- 💰 Gestión de comisiones personales y del equipo
- **Dashboard de Comisiones**:
  - Total comisiones ganadas
  - Comisiones disponibles para retiro
  - Comisiones pendientes de aprobación
  - Próximo pago estimado
- **Desglose de Comisiones**:
  - Por venta directa
  - Por referidos nivel 1
  - Por referidos nivel 2
  - Bonos de liderazgo
- **Solicitud de Retiro**:
  - Formulario de solicitud
  - Monto a retirar
  - Método de pago
  - Datos bancarios
  - Historial de solicitudes
- **Historial de Pagos**:
  - Todos los pagos recibidos
  - Fecha, monto, método
  - Comprobantes descargables

---

#### 3.2.4 Metas y Objetivos

**Controlador**: `App\Http\Controllers\Lider\MetaController`  
**Vistas**: `resources/views/lider/metas/`

**Funcionalidades**:
- 🎯 Gestión de metas del equipo
- **Metas del Equipo**:
  - Meta colectiva del mes
  - Progreso actual
  - Días restantes
  - Proyección de cumplimiento
- **Metas Individuales**:
  - Lista de vendedores con metas
  - Meta asignada
  - Progreso individual
  - Estado (en riesgo, en camino, alcanzada)
- **Asignación de Metas**:
  - Formulario para asignar metas
  - Sugerencias basadas en histórico
  - Metas por período
  - Notificación automática al vendedor
- **Seguimiento**:
  - Alertas de metas en riesgo
  - Notificaciones de logros
  - Historial de cumplimiento

---

#### 3.2.5 Capacitación del Equipo

**Controlador**: `App\Http\Controllers\Lider\CapacitacionController`  
**Vistas**: `resources/views/lider/capacitacion/`

**Funcionalidades**:
- 📚 Sistema de capacitación para el equipo
- **Gestión de Capacitaciones**:
  - Crear nueva capacitación
  - Título, descripción, contenido
  - Duración estimada
  - Recursos/archivos adjuntos
- **Asignación**:
  - Asignar a todo el equipo
  - Asignar a vendedores específicos
  - Establecer fecha límite
  - Seguimiento obligatorio/opcional
- **Seguimiento de Progreso**:
  - Vendedores asignados
  - Progreso por vendedor
  - Completadas vs pendientes
  - Tiempo promedio de compleción
- **Biblioteca de Capacitaciones**:
  - Todas las capacitaciones creadas
  - Histórico
  - Estadísticas de uso
  - Feedback de vendedores

---

### 3.3 MÓDULO DE VENDEDOR

**Ruta Base**: `/vendedor/*`  
**Middleware**: `auth`, `role:vendedor,lider,administrador`  
**Dashboard**: `resources/views/vendedor/dashboard/index.blade.php`

#### 3.3.1 Dashboard del Vendedor

**Vista Principal** (`dashboard/index.blade.php`)
- 📊 Panel personalizado del vendedor
- **KPIs Personales**:
  - Ventas del día
  - Ventas del mes
  - Meta mensual y progreso
  - Comisiones ganadas este mes
  - Comisiones disponibles
  - Posición en el ranking
- **Gráficos**:
  - Evolución de ventas (últimos 7 días)
  - Cumplimiento de meta (gauge)
  - Comisiones por tipo (pie chart)
- **Acceso Rápido**:
  - Crear nuevo pedido
  - Ver mis clientes
  - Ver catálogo de productos
  - Ver mis comisiones
  - Ver mi red de referidos
- **Últimos Pedidos**:
  - 5 pedidos más recientes
  - Estado actual
  - Acceso rápido a detalles
- **Notificaciones**:
  - Alertas importantes
  - Mensajes del líder
  - Cambios de estado de pedidos
- **Mi Red**:
  - Total de referidos
  - Referidos activos
  - Comisiones por referidos
  - Enlace de referido para compartir

---

#### 3.3.2 Gestión de Pedidos del Vendedor

**Controlador**: `App\Http\Controllers\Vendedor\PedidoController`  
**Vistas**: `resources/views/vendedor/pedidos/`

**Funcionalidades**:

**A. Mis Pedidos** (`index.blade.php`)
- 📋 Lista de todos los pedidos del vendedor
- **Filtros**:
  - Por estado
  - Por fecha
  - Por cliente
  - Por monto
- **Vista de Pedidos**:
  - Número, cliente, total, estado, fecha
  - Acciones: Ver, Editar (si permite), Cambiar estado
- **Estadísticas**:
  - Total de pedidos
  - Pedidos del día/mes
  - Total vendido
  - Ticket promedio

**B. Crear Pedido** (`create.blade.php`)
- 🛒 Formulario de creación
- **Pasos**:
  1. Buscar/crear cliente
  2. Agregar productos al carrito
  3. Aplicar descuentos (si tiene)
  4. Datos de entrega
  5. Confirmar y crear
- **Características**:
  - Autocompletado de clientes
  - Búsqueda rápida de productos
  - Cálculo automático de totales
  - Preview antes de confirmar
  - Opción de guardar como borrador

**C. Ver Pedido** (`show.blade.php`)
- 📄 Detalle completo del pedido
- Similar a la vista de admin pero con permisos limitados
- **Acciones disponibles**:
  - Cambiar estado (si tiene permiso)
  - Editar datos de entrega
  - Agregar notas
  - Contactar al cliente
  - Ver comisión generada

---

#### 3.3.3 Gestión de Clientes

**Controlador**: `App\Http\Controllers\Vendedor\ClienteController`  
**Vistas**: `resources/views/vendedor/clientes/`

**Funcionalidades**:

**A. Mis Clientes** (`index.blade.php`)
- 👥 Lista de clientes del vendedor
- **Información por Cliente**:
  - Nombre, email, teléfono
  - Total de compras
  - Última compra
  - Estado (activo/inactivo)
- **Filtros y Búsqueda**:
  - Por actividad
  - Por total de compras
  - Por fecha de última compra
  - Búsqueda por nombre/email
- **Acciones**:
  - Ver perfil completo
  - Ver historial de compras
  - Crear nuevo pedido para este cliente
  - Enviar mensaje/email
  - Agregar notas

**B. Perfil del Cliente** (`show.blade.php`)
- 📋 Información detallada
- **Datos Personales**:
  - Información de contacto
  - Dirección
  - Fecha de registro
- **Historial de Compras**:
  - Todos los pedidos
  - Total gastado
  - Productos más comprados
  - Frecuencia de compra
- **Estadísticas**:
  - Valor de vida del cliente (LTV)
  - Ticket promedio
  - Productos favoritos
- **Acciones Rápidas**:
  - Crear nuevo pedido
  - Enviar promoción
  - Ver pedidos pendientes

**C. Crear Cliente** (`create.blade.php`)
- ➕ Formulario de registro de cliente
- **Campos**:
  - Nombre, apellidos
  - Cédula, email, teléfono
  - Dirección completa
  - Preferencias de contacto
- **Opciones**:
  - Enviar credenciales por email
  - Crear pedido inmediatamente

---

#### 3.3.4 Catálogo de Productos para Vendedor

**Controlador**: `App\Http\Controllers\Vendedor\ProductoController`  
**Vistas**: `resources/views/vendedor/productos/`

**Funcionalidades**:
- 🛍️ Vista de catálogo optimizada para ventas
- **Vista de Productos**:
  - Grid con imágenes
  - Nombre, precio, stock
  - Indicador de disponibilidad
  - Botón "Agregar a pedido"
- **Búsqueda y Filtros**:
  - Por categoría
  - Por rango de precio
  - Por disponibilidad
  - Búsqueda por nombre
- **Detalle de Producto**:
  - Información completa
  - Especificaciones
  - Fotos adicionales
  - Stock disponible
- **Acciones**:
  - Agregar a pedido rápido
  - Compartir enlace del producto
  - Ver productos similares

---

#### 3.3.5 Comisiones del Vendedor

**Controlador**: `App\Http\Controllers\Vendedor\ComisionController`  
**Vistas**: `resources/views/vendedor/comisiones/`

**Funcionalidades**:
- 💰 Gestión de comisiones personales
- **Dashboard de Comisiones**:
  - Total ganado (histórico)
  - Ganado este mes
  - Disponible para retiro
  - Pendiente de aprobación
- **Desglose Detallado**:
  - Por venta directa
  - Por referidos nivel 1
  - Por referidos nivel 2
- **Gráfico de Evolución**:
  - Comisiones por mes (últimos 6 meses)
  - Proyección basada en tendencia
- **Tabla de Comisiones**:
  - Todas las comisiones
  - Filtros por tipo y estado
  - Pedido relacionado
  - Fecha y monto
- **Solicitud de Retiro**:
  - Formulario de solicitud
  - Verificación de monto disponible
  - Método de pago
  - Datos bancarios
  - Estado de solicitudes previas
- **Historial de Pagos**:
  - Pagos recibidos
  - Fechas, montos, métodos
  - Comprobantes descargables

---

#### 3.3.6 Red de Referidos del Vendedor

**Controlador**: `App\Http\Controllers\Vendedor\ReferidoController`  
**Vistas**: `resources/views/vendedor/referidos/`

**Funcionalidades**:
- 🌳 Gestión de red MLM personal
- **Dashboard de Red**:
  - Total de referidos (directos e indirectos)
  - Referidos activos
  - Comisiones generadas por red
  - Crecimiento de la red
- **Código de Referido**:
  - Código personal único
  - Enlace para compartir
  - Botones para compartir en redes sociales
  - WhatsApp, Facebook, Twitter, copiar enlace
  - QR code para compartir
- **Árbol de Red**:
  - Visualización de referidos
  - Nivel 1 (directos)
  - Nivel 2 (indirectos)
  - Estado de cada referido
  - Ventas de cada referido
- **Invitar Nuevos**:
  - Formulario de invitación
  - Envío por email
  - Envío por WhatsApp
  - Mensaje personalizable
- **Ganancias por Referidos**:
  - Comisiones generadas
  - Desglose por referido
  - Gráficos de rendimiento
- **Estadísticas**:
  - Tasa de conversión
  - Referidos más activos
  - Proyección de comisiones

---

### 3.4 MÓDULO DE CLIENTE

**Ruta Base**: `/cliente/*`  
**Middleware**: `auth`, `verified`  
**Dashboard**: `resources/views/cliente/dashboard.blade.php`

#### 3.4.1 Dashboard del Cliente

**Vista Principal** (`dashboard.blade.php`)
- 🏠 Panel personalizado del cliente
- **Información Personal**:
  - Nombre y foto
  - Email y teléfono
  - Dirección de entrega preferida
- **Mis Pedidos**:
  - Pedido actual en proceso (si existe)
  - Estado con timeline
  - Acciones: Ver detalle, Cancelar
- **Resumen de Compras**:
  - Total de pedidos
  - Total gastado
  - Pedido promedio
- **Productos Favoritos**:
  - Lista de productos marcados como favoritos
  - Botón para agregar a nuevo pedido
- **Productos Recomendados**:
  - Basados en compras anteriores
  - Productos más populares
  - Nuevos productos
- **Historial Reciente**:
  - Últimos 5 pedidos
  - Estado de cada uno
  - Acceso rápido a detalles
- **Acciones Rápidas**:
  - Hacer nuevo pedido
  - Ver catálogo
  - Ver historial completo
  - Actualizar perfil

---

#### 3.4.2 Gestión de Pedidos del Cliente

**Controlador**: `App\Http\Controllers\Cliente\PedidoClienteController`  
**Vistas**: `resources/views/cliente/pedidos/`

**Funcionalidades**:

**A. Mis Pedidos** (`index.blade.php`)
- 📦 Lista de todos los pedidos del cliente
- **Filtros**:
  - Por estado
  - Por fecha
- **Vista de Pedidos**:
  - Número de pedido
  - Fecha
  - Total
  - Estado con badge visual
  - Acciones: Ver detalle, Cancelar (si permite)
- **Indicadores Visuales**:
  - 🟡 Pendiente
  - 🔵 Confirmado
  - 🟠 En Preparación
  - 🚚 En Camino
  - ✅ Entregado
  - ❌ Cancelado

**B. Crear Pedido** (`create.blade.php`)
- 🛒 Proceso de compra simplificado
- **Catálogo de Productos**:
  - Vista en grid
  - Filtros por categoría
  - Búsqueda
- **Carrito**:
  - Productos agregados
  - Cantidades editables
  - Subtotal por producto
  - Total general
- **Datos de Entrega**:
  - Dirección (usa la guardada o ingresa nueva)
  - Teléfono de contacto
  - Notas especiales
- **Confirmación**:
  - Resumen completo
  - Confirmar y pagar
  - Método de pago

**C. Ver Pedido** (`show.blade.php`)
- 📄 Detalle completo del pedido
- **Header**:
  - Número de pedido grande
  - Estado actual con badge
  - Fecha de pedido
- **Timeline de Estado**:
  - Visualización del progreso
  - Estimación de entrega
- **Productos**:
  - Lista con imágenes
  - Cantidades y precios
- **Totales**:
  - Subtotal
  - Descuentos (si aplica)
  - Envío
  - Total
- **Datos de Entrega**:
  - Dirección
  - Teléfono
  - Notas
- **Acciones**:
  - Cancelar pedido (si está pendiente)
  - Contactar vendedor
  - Descargar factura
  - Reportar problema

---


## 4. BASE DE DATOS MONGODB

### 4.1 Estructura General

El sistema utiliza MongoDB como base de datos principal con **13 colecciones** implementadas:

```
arepa_llanerita_mongo/
├── users                    (Usuarios del sistema)
├── productos                (Catálogo de productos)
├── categorias               (Categorías de productos)
├── pedidos                  (Pedidos/Ventas)
├── detalle_pedidos          (Detalles de productos en pedidos)
├── comisiones               (Comisiones MLM)
├── referidos                (Red de referidos multinivel)
├── notificaciones           (Sistema de notificaciones)
├── movimientos_inventario   (Control de stock)
├── zonas_entrega            (Zonas geográficas de entrega)
├── configuraciones          (Configuraciones del sistema)
├── cupones                  (Descuentos y promociones)
└── auditorias               (Registro de cambios y auditoría)
```

### 4.2 Colecciones Principales

#### 4.2.1 Colección: users

**Descripción**: Usuarios del sistema con roles diferenciados

**Campos Principales**:
```javascript
{
  _id: ObjectId,
  name: String,              // Nombre
  apellidos: String,          // Apellidos
  cedula: String,             // Cédula (único)
  email: String,              // Email (único)
  password: String,           // Contraseña encriptada
  telefono: String,           // Teléfono
  direccion: String,          // Dirección física
  ciudad: String,             // Ciudad
  departamento: String,       // Departamento/Estado
  fecha_nacimiento: Date,     // Fecha de nacimiento
  rol: String,                // Rol: administrador, lider, vendedor, cliente
  activo: Boolean,            // Estado activo/inactivo
  ultimo_acceso: DateTime,    // Último acceso al sistema
  referido_por: ObjectId,     // ID del usuario que lo refirió
  codigo_referido: String,    // Código único para referir (único)
  total_referidos: Number,    // Cantidad total de referidos
  comisiones_ganadas: Decimal128,    // Total comisiones ganadas
  comisiones_disponibles: Decimal128, // Comisiones disponibles
  meta_mensual: Decimal128,   // Meta de ventas mensuales
  ventas_mes_actual: Decimal128,      // Ventas del mes
  nivel_vendedor: Number,     // Nivel en la estructura
  zonas_asignadas: Array,     // Zonas de entrega asignadas
  configuracion_personal: {   // Configuraciones embebidas
    tema: String,
    idioma: String,
    notificaciones: Object
  },
  created_at: DateTime,
  updated_at: DateTime
}
```

**Índices**:
- `email` (único)
- `cedula` (único)
- `codigo_referido` (único)
- `rol`
- `activo`

**Relaciones**:
- `pedidos_como_vendedor`: hasMany → pedidos (vendedor_id)
- `pedidos_como_cliente`: hasMany → pedidos (user_id)
- `comisiones`: hasMany → comisiones (user_id)
- `referidos_directos`: hasMany → referidos (referidor_id)

---

#### 4.2.2 Colección: productos

**Descripción**: Catálogo de productos con inventario

**Campos Principales**:
```javascript
{
  _id: ObjectId,
  nombre: String,             // Nombre del producto
  descripcion: String,        // Descripción detallada
  categoria_id: ObjectId,     // ID de categoría
  categoria_data: {           // Datos embebidos de categoría
    _id: ObjectId,
    nombre: String,
    descripcion: String
  },
  precio: Decimal128,         // Precio del producto
  stock: Number,              // Cantidad en inventario
  stock_minimo: Number,       // Stock mínimo para alertas
  activo: Boolean,            // Estado activo/inactivo
  imagen: String,             // URL imagen principal
  imagenes_adicionales: Array, // URLs imágenes adicionales
  especificaciones: Object,   // Especificaciones técnicas embebidas
  tiempo_preparacion: Number, // Tiempo en minutos
  ingredientes: Array,        // Lista de ingredientes
  historial_precios: [        // Historial de cambios de precio
    {
      precio_anterior: Decimal128,
      precio_nuevo: Decimal128,
      fecha_cambio: DateTime,
      usuario_id: ObjectId
    }
  ],
  reviews: [                  // Reseñas embebidas
    {
      user_id: ObjectId,
      calificacion: Number,
      comentario: String,
      fecha: DateTime
    }
  ],
  created_at: DateTime,
  updated_at: DateTime
}
```

**Índices**:
- `nombre` (texto)
- `categoria_id`
- `activo`
- `stock`

**Scopes útiles**:
- `activos()`: Solo productos activos
- `conStock()`: Con stock disponible
- `stockBajo()`: Stock <= stock_minimo
- `porCategoria($id)`: Filtrar por categoría

---

#### 4.2.3 Colección: pedidos

**Descripción**: Pedidos con datos embebidos de cliente, vendedor y productos

**Campos Principales**:
```javascript
{
  _id: ObjectId,
  numero_pedido: String,      // Número único (ej: PED-20240119-001)
  user_id: ObjectId,          // ID del cliente
  cliente_data: {             // Datos embebidos del cliente
    _id: ObjectId,
    name: String,
    email: String,
    telefono: String,
    direccion: String
  },
  vendedor_id: ObjectId,      // ID del vendedor
  vendedor_data: {            // Datos embebidos del vendedor
    _id: ObjectId,
    name: String,
    email: String
  },
  estado: String,             // pendiente, confirmado, en_preparacion, listo, en_camino, entregado, cancelado
  detalles: [                 // Productos embebidos
    {
      producto_id: ObjectId,
      producto_data: {
        nombre: String,
        imagen: String,
        especificaciones: Object
      },
      cantidad: Number,
      precio_unitario: Decimal128,
      subtotal: Decimal128
    }
  ],
  subtotal: Decimal128,       // Subtotal de productos
  descuento: Decimal128,      // Descuento aplicado
  iva: Decimal128,            // IVA (si aplica)
  total: Decimal128,          // Total sin descuentos
  total_final: Decimal128,    // Total final a pagar
  direccion_entrega: String,  // Dirección de entrega
  telefono_entrega: String,   // Teléfono de contacto
  notas: String,              // Notas adicionales
  fecha_entrega_estimada: DateTime,
  zona_entrega_id: ObjectId,
  zona_entrega_data: Object,  // Datos embebidos de la zona
  historial_estados: [        // Historial de cambios embebido
    {
      estado: String,
      fecha: DateTime,
      usuario_id: ObjectId,
      notas: String
    }
  ],
  metodo_pago: String,
  comisiones_calculadas: [    // Comisiones embebidas
    {
      user_id: ObjectId,
      tipo: String,
      monto: Decimal128
    }
  ],
  stock_devuelto: Boolean,    // Bandera para evitar doble devolución
  created_at: DateTime,
  updated_at: DateTime
}
```

**Índices**:
- `numero_pedido` (único)
- `user_id`
- `vendedor_id`
- `estado`
- `created_at`

**Scopes útiles**:
- `porEstado($estado)`: Filtrar por estado
- `pendientes()`: Solo pendientes
- `entregados()`: Solo entregados
- `delVendedor($id)`: Del vendedor específico
- `delCliente($id)`: Del cliente específico
- `hoy()`: Pedidos de hoy

---

#### 4.2.4 Colección: comisiones

**Descripción**: Sistema de comisiones MLM

**Campos Principales**:
```javascript
{
  _id: ObjectId,
  user_id: ObjectId,          // Usuario que recibe la comisión
  user_data: {                // Datos embebidos del usuario
    _id: ObjectId,
    name: String,
    email: String,
    rol: String
  },
  pedido_id: ObjectId,        // Pedido que genera la comisión
  pedido_data: {              // Datos embebidos del pedido
    _id: ObjectId,
    numero_pedido: String,
    total: Decimal128,
    fecha: DateTime
  },
  tipo: String,               // venta_directa, referido_nivel_1, referido_nivel_2, bono_liderazgo
  porcentaje: Decimal128,     // Porcentaje aplicado
  monto: Decimal128,          // Monto de la comisión
  estado: String,             // pendiente, aprobada, pagada
  fecha_pago: DateTime,       // Fecha de pago (si aplica)
  detalles_calculo: {         // Detalles embebidos del cálculo
    total_pedido: Decimal128,
    porcentaje_aplicado: Decimal128,
    fecha_calculo: DateTime
  },
  metodo_pago: String,        // Método usado para pagar
  created_at: DateTime,
  updated_at: DateTime
}
```

**Índices**:
- `user_id`
- `pedido_id`
- `tipo`
- `estado`

**Scopes útiles**:
- `porEstado($estado)`
- `pendientes()`
- `aprobadas()`
- `pagadas()`

---

#### 4.2.5 Colección: referidos

**Descripción**: Sistema de referidos multinivel

**Campos Principales**:
```javascript
{
  _id: ObjectId,
  referidor_id: ObjectId,     // Usuario que refiere
  referidor_data: {           // Datos embebidos del referidor
    _id: ObjectId,
    name: String,
    email: String
  },
  referido_id: ObjectId,      // Usuario referido
  referido_data: {            // Datos embebidos del referido
    _id: ObjectId,
    name: String,
    email: String
  },
  fecha_registro: DateTime,   // Fecha de registro del referido
  activo: Boolean,            // Estado del referido
  nivel: Number,              // Nivel del referido (1, 2, etc.)
  comisiones_generadas: Decimal128,   // Total comisiones generadas
  comisiones_pendientes: Decimal128,  // Pendientes de pago
  comisiones_pagadas: Decimal128,     // Ya pagadas
  total_ventas_referido: Decimal128,  // Total ventas del referido
  meta_referido: Decimal128,  // Meta asignada
  estado_referido: String,    // Estado específico
  historial_comisiones: [     // Historial embebido
    {
      fecha: DateTime,
      monto: Decimal128,
      pedido_id: ObjectId
    }
  ],
  created_at: DateTime,
  updated_at: DateTime
}
```

**Índices**:
- `referidor_id`
- `referido_id`
- `nivel`
- `activo`

---

### 4.3 Características de MongoDB Implementadas

#### 4.3.1 Documentos Embebidos
El sistema hace uso extensivo de documentos embebidos para optimizar consultas:

```javascript
// Ejemplo en Pedidos
{
  cliente_data: { /* datos completos del cliente */ },
  vendedor_data: { /* datos completos del vendedor */ },
  detalles: [ /* productos con toda su información */ ],
  historial_estados: [ /* cambios de estado completos */ ]
}
```

**Ventajas**:
- ✅ Una sola consulta para obtener toda la información
- ✅ No requiere JOINs como en SQL
- ✅ Mejor rendimiento en lecturas
- ✅ Datos históricos preservados (aunque el original cambie)

#### 4.3.2 Índices Optimizados
Índices estratégicos para mejorar rendimiento:

```javascript
// Ejemplo de índices compuestos
db.pedidos.createIndex({ vendedor_id: 1, created_at: -1 })
db.pedidos.createIndex({ estado: 1, created_at: -1 })
db.comisiones.createIndex({ user_id: 1, estado: 1 })
```

#### 4.3.3 Agregaciones
Uso de pipelines de agregación para reportes complejos:

```javascript
// Ejemplo: Total de ventas por vendedor
db.pedidos.aggregate([
  { $match: { estado: "entregado" } },
  { $group: {
      _id: "$vendedor_id",
      total_ventas: { $sum: "$total_final" },
      cantidad_pedidos: { $sum: 1 }
  }},
  { $sort: { total_ventas: -1 } }
])
```

---

## 5. MODELOS Y RELACIONES

### 5.1 Modelo User

**Archivo**: `app/Models/User.php`

**Características**:
- Extiende `MongoDB\Laravel\Auth\User`
- Implementa autenticación de Laravel
- Usa trait `HandlesDecimal128` para números decimales

**Métodos Principales**:
```php
// Accessors
- nombreCompleto(): Nombre + Apellidos
- esAdmin(): Verifica si es administrador
- esLider(): Verifica si es líder
- esVendedor(): Verifica si es vendedor
- esCliente(): Verifica si es cliente
- puedeVender(): Verifica permisos de venta

// Métodos de Negocio
- agregarReferido($referido): Agrega referido a su red
- agregarVentaAlHistorial($pedido): Registra venta
- calcularComisiones(): Calcula comisiones disponibles

// Scopes
- activos(): Solo usuarios activos
- porRol($rol): Filtrar por rol
- vendedores(): Solo vendedores
- clientes(): Solo clientes
```

---

### 5.2 Modelo Producto

**Archivo**: `app/Models/Producto.php`

**Características**:
- Extiende `MongoDB\Laravel\Eloquent\Model`
- Maneja datos embebidos de categoría
- Control de inventario integrado

**Métodos Principales**:
```php
// Accessors/Mutators
- getCategoriaAttribute(): Obtiene categoría embebida
- setCategoriaAttribute($categoria): Embebe datos de categoría

// Métodos de Negocio
- estaDisponible(): Verifica disponibilidad
- tieneStockBajo(): Verifica si stock < stock_minimo
- estaAgotado(): Verifica si stock = 0
- agregarReview($user, $calificacion, $comentario): Agrega reseña
- actualizarPrecio($nuevoPrecio, $usuario): Actualiza precio y registra en historial
- promedioReviews(): Calcula promedio de calificaciones

// Scopes
- activos(): Solo productos activos
- conStock(): Con stock disponible
- stockBajo(): Stock bajo
- porCategoria($id): De una categoría específica
- buscar($termino): Búsqueda por nombre
```

---

### 5.3 Modelo Pedido

**Archivo**: `app/Models/Pedido.php`

**Características**:
- Maneja documentos embebidos complejos
- Gestiona historial de estados
- Calcula comisiones automáticamente

**Métodos Principales**:
```php
// Accessors/Mutators
- getProductosAttribute(): Obtiene array de productos
- setProductosAttribute($productos): Establece productos

// Métodos de Negocio
- agregarDetalle($producto, $cantidad, $precio): Agrega producto
- recalcularTotales(): Recalcula subtotal, descuento, total
- cambiarEstado($nuevoEstado, $usuario, $notas): Cambia estado y registra
- calcularComisionVendedor(): Calcula comisión del vendedor
- asignarDatosEmbebidos(): Embebe datos de cliente y vendedor
- devolverStock(): Devuelve productos al inventario (si se cancela)

// Scopes
- porEstado($estado): Filtrar por estado
- pendientes(): Solo pendientes
- confirmados(): Solo confirmados
- entregados(): Solo entregados
- delVendedor($id): Del vendedor específico
- delCliente($id): Del cliente específico
- hoy(): Pedidos de hoy
```

---

### 5.4 Modelo Comision

**Archivo**: `app/Models/Comision.php`

**Métodos Principales**:
```php
// Métodos de Negocio
- aprobar($usuario): Cambia estado a aprobada
- marcarComoPagada($usuario, $metodoPago, $referencia): Marca como pagada
- rechazar($usuario, $motivo): Rechaza comisión

// Scopes
- porEstado($estado): Filtrar por estado
- pendientes(): Solo pendientes
- aprobadas(): Solo aprobadas
- pagadas(): Solo pagadas
- delUsuario($id): Del usuario específico
- porTipo($tipo): Por tipo de comisión
```

---

## 6. SERVICIOS Y UTILIDADES

### 6.1 ComisionService

**Archivo**: `app/Services/ComisionService.php`

**Descripción**: Servicio para cálculo automático de comisiones MLM

**Métodos Principales**:
```php
/**
 * Crea comisión cuando se crea un pedido
 */
public static function crearComisionPorPedido(Pedido $pedido): ?Comision

/**
 * Calcula comisión de venta directa
 */
public static function calcularVentaDirecta(Pedido $pedido, User $vendedor): Comision

/**
 * Calcula comisiones de referidos (multinivel)
 */
public static function calcularComisionesReferidos(Pedido $pedido, User $vendedor): array

/**
 * Procesa comisiones cuando pedido es entregado
 */
public static function procesarComisionesEntrega(Pedido $pedido): void

/**
 * Cancela comisiones si pedido es cancelado
 */
public static function cancelarComisionesPedido(Pedido $pedido): void
```

**Flujo de Cálculo de Comisiones**:
1. Pedido es creado → `crearComisionPorPedido()`
2. Verifica que hay vendedor asignado
3. Calcula comisión de venta directa (15% por defecto)
4. Busca referidores del vendedor (nivel 1 y 2)
5. Calcula comisiones de referidos (5% nivel 1, 2% nivel 2)
6. Crea registros en colección `comisiones` con estado "pendiente"
7. Embebe datos del pedido y usuario
8. Retorna comisiones creadas

---

### 6.2 NotificationService

**Archivo**: `app/Services/NotificationService.php`

**Descripción**: Gestión centralizada de notificaciones

**Métodos Principales**:
```php
/**
 * Envía notificación a un usuario
 */
public static function enviarNotificacion(
    User $usuario,
    string $titulo,
    string $mensaje,
    string $tipo = 'sistema',
    array $datosAdicionales = []
): Notificacion

/**
 * Notifica cambio de estado de pedido
 */
public static function notificarCambioEstadoPedido(Pedido $pedido): void

/**
 * Notifica nueva comisión
 */
public static function notificarNuevaComision(Comision $comision): void

/**
 * Notifica al equipo (líderes y vendedores)
 */
public static function notificarEquipo(string $titulo, string $mensaje): void
```

**Tipos de Notificaciones**:
- `pedido`: Relacionadas con pedidos
- `comision`: Relacionadas con comisiones
- `sistema`: Del sistema
- `promocion`: Promociones y ofertas

---

### 6.3 CacheService

**Archivo**: `app/Services/CacheService.php`

**Descripción**: Optimización de consultas frecuentes mediante caché

**Métodos Principales**:
```php
/**
 * Obtiene estadísticas de dashboard con caché
 */
public static function getEstadisticasDashboard(User $usuario, string $rol): array

/**
 * Obtiene productos con caché
 */
public static function getProductosActivos(): Collection

/**
 * Limpia caché específica
 */
public static function limpiarCacheUsuario(User $usuario): void

/**
 * Limpia toda la caché
 */
public static function limpiarCacheCompleta(): void
```

---

## 7. SEGURIDAD Y AUTENTICACIÓN

### 7.1 Sistema de Roles

El sistema implementa un middleware personalizado de roles:

**Archivo**: `app/Http/Middleware/RoleMiddleware.php`

**Roles Disponibles**:
- `administrador`: Acceso total al sistema
- `lider`: Gestión de equipo y reportes
- `vendedor`: Ventas y gestión de clientes
- `cliente`: Visualización de pedidos

**Uso en Rutas**:
```php
// Ruta solo para administradores
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});

// Ruta para líderes y administradores
Route::middleware(['auth', 'role:lider,administrador'])->group(function () {
    Route::get('/lider/equipo', [EquipoController::class, 'index']);
});

// Ruta para vendedores, líderes y administradores
Route::middleware(['auth', 'role:vendedor,lider,administrador'])->group(function () {
    Route::get('/vendedor/pedidos', [PedidoController::class, 'index']);
});
```

---

### 7.2 Protección CSRF

Todas las rutas POST, PUT, PATCH, DELETE están protegidas con tokens CSRF:

```blade
<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    <!-- campos del formulario -->
</form>
```

---

### 7.3 Validación de Datos

Validación robusta en todos los controladores:

```php
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
    'cedula' => 'required|unique:users,cedula',
    'telefono' => 'required|string',
    'rol' => 'required|in:administrador,lider,vendedor,cliente',
]);
```

---

### 7.4 Encriptación de Contraseñas

Las contraseñas se encriptan automáticamente con Bcrypt:

```php
'password' => 'hashed', // en el modelo User
```

---

## 8. CONFIGURACIÓN Y DESPLIEGUE

### 8.1 Requisitos del Sistema

**Servidor**:
- PHP 8.2 o superior
- Composer 2.x
- Node.js 18.x o superior
- NPM o Yarn

**Base de Datos**:
- MongoDB 7.0 o superior
- Extensión PHP MongoDB

**Extensiones PHP Requeridas**:
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- MongoDB

---

### 8.2 Instalación

```bash
# 1. Clonar repositorio
git clone [url-del-repositorio]
cd Red_de_Ventas_Proyecto_Final

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JavaScript
npm install

# 4. Copiar archivo de configuración
cp .env.example .env

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Configurar base de datos en .env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=arepa_llanerita_mongo

# 7. Compilar assets
npm run build

# 8. Iniciar servidor
php artisan serve
```

---

### 8.3 Configuración de MongoDB

**Archivo**: `config/database.php`

```php
'mongodb' => [
    'driver' => 'mongodb',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE', 'arepa_llanerita_mongo'),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
    'options' => [
        'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'),
    ],
],
```

---

### 8.4 Configuración de Comisiones

**Archivo**: `config/comisiones.php`

```php
return [
    'venta_directa' => 15,        // 15% de comisión por venta directa
    'referido_nivel_1' => 5,      // 5% por ventas de referidos nivel 1
    'referido_nivel_2' => 2,      // 2% por ventas de referidos nivel 2
    'bono_liderazgo' => 10,       // 10% bono para líderes
    'minimo_retiro' => 50000,     // Mínimo para solicitar retiro
];
```

---

### 8.5 Comandos Artisan Personalizados

```bash
# Generar código de referido para usuarios
php artisan usuarios:generar-codigos

# Calcular comisiones pendientes
php artisan comisiones:calcular

# Limpiar caché del sistema
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generar respaldo de MongoDB
php artisan mongodb:backup
```

---

## 9. CONCLUSIÓN

Este sistema de Red de Ventas MLM es una solución completa y robusta que integra:

✅ **Gestión Completa de Usuarios** con 4 roles diferenciados
✅ **Sistema MLM Multinivel** con cálculo automático de comisiones
✅ **Gestión de Productos e Inventario** en tiempo real
✅ **Procesamiento de Pedidos** con múltiples estados
✅ **Dashboards Interactivos** para cada rol
✅ **Reportes y Analytics** detallados
✅ **Sistema de Notificaciones** completo
✅ **Auditoría y Trazabilidad** de todas las operaciones
✅ **Base de Datos MongoDB** optimizada con documentos embebidos
✅ **Arquitectura Escalable** y mantenible

### Tecnologías Implementadas

- Laravel 12.x con MongoDB
- Blade Templates para vistas
- TailwindCSS para estilos
- Alpine.js para interactividad
- Livewire para componentes reactivos
- Chart.js para gráficos
- Sistema de caché integrado
- Servicios de negocio desacoplados

### Métricas del Proyecto

- **64,546 líneas de código**
- **42 controladores** organizados por rol
- **21 modelos** con soporte MongoDB
- **300+ vistas** Blade
- **200+ rutas** protegidas
- **13 colecciones** MongoDB optimizadas

---

**Documentación generada**: 2024
**Versión del Sistema**: 2.0
**Base de Datos**: MongoDB 7.0+
**Framework**: Laravel 12.x

---

## CONTACTO Y SOPORTE

Para más información o soporte técnico, consulte la documentación adicional en la carpeta `Documentación/` del proyecto.

