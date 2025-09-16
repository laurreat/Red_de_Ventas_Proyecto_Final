# 🥞 Arepa la Llanerita - Red de Ventas Multi-Nivel

## 🎯 Descripción del Proyecto
Sistema de ventas multi-nivel (MLM) para "Arepa la Llanerita" desarrollado en Laravel 12. Permite gestión completa de vendedores, líderes, productos, pedidos y comisiones con dashboards diferenciados por rol.

## ✅ Estado Actual (MVP Funcional)

### Stack Tecnológico
- **Backend**: Laravel 12 + PHP 8.2
- **Frontend**: Bootstrap 5.3 + Livewire 3 + Alpine.js + Tailwind CSS 4.0
- **Base de Datos**: SQLite/MySQL
- **Herramientas**: Vite, Sass, Composer, NPM

### Funcionalidades Implementadas
- ✅ Sistema de autenticación seguro con Laravel Auth
- ✅ 4 roles definidos: Administrador, Líder, Vendedor, Cliente
- ✅ Middleware de roles (`RoleMiddleware`) para control de acceso
- ✅ Dashboards específicos por rol con métricas en tiempo real
- ✅ Sistema de referidos básico implementado
- ✅ Diseño responsive con identidad corporativa
- ✅ Base de datos estructurada con seeders

## 📊 Módulos del Sistema

### 🔐 Autenticación y Roles
- Login horizontal responsive
- Middleware de protección por roles
- Redirección automática según rol
- Sesiones seguras con Laravel

### 📈 Dashboards por Rol

#### Administrador
- Métricas generales del sistema
- Gestión de usuarios (`UserController`)
- Control de productos y categorías
- Reportes de ventas y comisiones
- Pedidos recientes y productos populares

#### Líder
- Gestión de equipos de vendedores
- Métricas de rendimiento del equipo
- Control de metas mensuales
- Comisiones por equipos

#### Vendedor
- Panel de ventas personales
- Sistema de referidos
- Comisiones ganadas y disponibles
- Historial de pedidos

#### Cliente
- Historial de compras
- Programa de referidos
- Productos favoritos
- Estado de pedidos

### 🛍️ Sistema de Productos
- **Modelos**: `Producto`, `Categoria`, `MovimientoInventario`
- 8 categorías predefinidas
- 18 productos con precios reales ($8,000 - $35,000)
- Control de inventario básico

### 📦 Sistema de Pedidos
- **Modelos**: `Pedido`, `DetallePedido`
- Estados: pendiente, procesando, entregado, cancelado
- Cálculo automático de totales
- Integración con sistema de comisiones

### 💰 Sistema de Comisiones y Referidos
- **Modelos**: `Comision`, `Referido`
- Seguimiento de metas mensuales
- Cálculo de comisiones por ventas
- Red de referidos multi-nivel

## 🗄️ Estructura de Base de Datos

### Tablas Principales
- `users` - Usuarios con roles, metas y comisiones
- `productos` - Catálogo de productos
- `categorias` - Clasificación de productos
- `pedidos` - Órdenes de compra
- `detalle_pedidos` - Items específicos de cada pedido
- `comisiones` - Registro de comisiones
- `referidos` - Red de referidos

### Tablas Auxiliares
- `zonas_entrega` - Áreas de cobertura
- `cupones` - Sistema de descuentos
- `notificaciones` - Alertas del sistema
- `movimientos_inventario` - Control de stock
- `configuraciones` - Parámetros del sistema

## 🎨 Diseño y UI

### Identidad Visual
- **Color primario**: Vino tinto (#722F37)
- **Color secundario**: Blanco
- **Tipografía**: Inter (Google Fonts)
- **Framework**: Bootstrap 5.3 + variables CSS personalizadas

### Características del Diseño
- Responsive mobile-first
- Login con división horizontal
- Dashboards organizados con cards
- Componentes Livewire para interactividad
- Notificaciones toast implementadas

## 🔑 Credenciales de Prueba

| Rol | Email | Password |
|-----|-------|----------|
| Admin | admin@arepallanerita.com | admin123 |
| Líder | carlos.rodriguez@arepallanerita.com | lider123 |
| Vendedor | ana.lopez@arepallanerita.com | vendedor123 |
| Vendedor | miguel.torres@arepallanerita.com | vendedor123 |
| Cliente | maria.gonzalez@email.com | cliente123 |
| Cliente | pedro.ramirez@email.com | cliente123 |

## 🚀 Instalación y Desarrollo

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/SQLite

### Configuración Inicial
```bash
# Dependencias
composer install
npm install

# Configuración
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate:fresh --seed

# Desarrollo
php artisan serve          # Backend en :8000
npm run dev               # Frontend con Vite
```

### Comandos Útiles
```bash
# Laravel
php artisan migrate
php artisan db:seed
php artisan config:clear
php artisan view:clear

# Livewire
php artisan livewire:make ComponentName

# Desarrollo concurrente
composer run dev          # Servidor + queue + logs + vite
```

## 📁 Estructura del Proyecto

### Backend
- `app/Http/Controllers/` - Controladores principales
  - `DashboardController.php` - Dashboards por rol
  - `Admin/UserController.php` - Gestión de usuarios
- `app/Models/` - 12 modelos implementados
- `app/Livewire/` - Componentes interactivos
- `app/Http/Middleware/RoleMiddleware.php` - Control de acceso

### Frontend
- `resources/views/` - Vistas Blade
  - `dashboard/` - Dashboards por rol
  - `auth/` - Autenticación
  - `layouts/` - Plantillas base
  - `livewire/` - Componentes Livewire

### Base de Datos
- `database/migrations/` - Estructura de BD
- `database/seeders/` - Datos de prueba

## 🛠️ Paquetes y Dependencias

### PHP (Composer)
- `laravel/framework`: ^12.0
- `livewire/livewire`: ^3.6
- `laravel/ui`: ^4.6
- `barryvdh/laravel-dompdf`: ^3.1 (PDFs)
- `intervention/image`: ^3.11 (Imágenes)
- `maatwebsite/excel`: ^1.1 (Excel)

### JavaScript (NPM)
- `bootstrap`: ^5.2.3
- `@tailwindcss/vite`: ^4.0.0
- `@popperjs/core`: ^2.11.6
- `vite`: ^7.0.4
- `axios`: ^1.11.0

## ⚠️ Funcionalidades Pendientes

### Alto Prioridad
- [ ] CRUD completo de productos y categorías
- [ ] Sistema completo de pedidos (crear, editar, cancelar)
- [ ] Cálculo automático de comisiones
- [ ] Gestión de inventario en tiempo real
- [ ] Sistema de pagos y facturación

### Media Prioridad
- [ ] Reportes avanzados y analytics
- [ ] Sistema de notificaciones push
- [ ] Gestión de zonas de entrega
- [ ] Sistema de cupones y promociones
- [ ] API REST para mobile

### Baja Prioridad
- [ ] Integración con pasarelas de pago
- [ ] Sistema de chat interno
- [ ] Aplicación móvil
- [ ] Integración con ERP
- [ ] Sistema de backup automático

## 🔧 Próximos Pasos Técnicos

1. **Completar CRUDs básicos** - Productos, categorías, pedidos
2. **Implementar lógica de comisiones** - Cálculos automáticos
3. **Sistema de notificaciones** - Eventos en tiempo real
4. **Validaciones y seguridad** - Protección contra XSS, CSRF
5. **Testing** - Unit tests y feature tests
6. **Optimización** - Cache, índices DB, lazy loading
7. **Deployment** - Docker, CI/CD, producción

## 📌 Estado General
- ✅ **MVP funcional y estable**
- ✅ **Dashboards operativos sin errores críticos**
- ✅ **Base sólida para expansión**
- ⚠️ **Listo para desarrollo de módulos principales**
- 🚀 **Preparado para pasar a producción con mejoras**