# 🗺️ RUTAS Y ESTRUCTURA DEL PROYECTO - AREPA LA LLANERITA

## 📁 ESTRUCTURA GENERAL DEL PROYECTO

```
arepa-llanerita/
├── 🎨 FRONTEND (resources/)
├── ⚙️  BACKEND (app/)
├── 🗄️  BASE DE DATOS (database/)
├── 🌐 RUTAS (routes/)
├── ⚡ ASSETS COMPILADOS (public/)
└── 📋 CONFIGURACIÓN (config/)
```

---

## 🎨 FRONTEND - UBICACIONES

### 📍 **VISTAS PRINCIPALES**
```
resources/views/
├── layouts/
│   └── app.blade.php                    # Layout principal con navbar y variables CSS
├── auth/
│   └── login.blade.php                  # Pantalla de login horizontal
├── dashboard/                           # Dashboards por rol
│   ├── admin.blade.php                  # Dashboard administrador
│   ├── lider.blade.php                  # Dashboard líder  
│   ├── vendedor.blade.php               # Dashboard vendedor
│   └── cliente.blade.php                # Dashboard cliente
├── livewire/                           # Componentes Livewire
│   ├── toast-notifications.blade.php   # Sistema de notificaciones
│   └── stats-widget.blade.php          # Widget de estadísticas
└── welcome.blade.php                   # Página de bienvenida (no usada)
```

### 🎨 **ESTILOS Y ASSETS**
```
resources/
├── sass/
│   ├── app.scss                        # Estilos principales
│   └── _variables.scss                 # Variables SCSS de Bootstrap
├── js/
│   └── app.js                          # JavaScript principal
└── css/                               # (Se genera automáticamente)
```

### 🏗️ **ASSETS COMPILADOS**
```
public/
├── build/                             # Assets compilados por Vite
│   ├── assets/
│   │   ├── app-[hash].js
│   │   └── app-[hash].css
│   └── manifest.json
├── images/                            # Imágenes públicas
└── favicon.ico                        # Icono del sitio
```

---

## ⚙️ BACKEND - UBICACIONES

### 🎯 **CONTROLADORES**
```
app/Http/Controllers/
├── DashboardController.php            # Lógica de todos los dashboards
├── Auth/
│   ├── LoginController.php            # Login y logout
│   ├── RegisterController.php         # Registro de usuarios
│   └── ...                           # Otros controladores de auth
└── Controller.php                     # Controlador base
```

### 🛡️ **MIDDLEWARE**
```
app/Http/Middleware/
├── RoleMiddleware.php                 # Control de acceso por roles
├── Authenticate.php                   # Autenticación base Laravel
├── EncryptCookies.php                 # Encriptación de cookies
└── ...                               # Otros middleware estándar
```

### 📊 **MODELOS**
```
app/Models/
├── User.php                          # Usuario (con roles y referidos)
├── Producto.php                      # Productos del catálogo
├── Categoria.php                     # Categorías de productos
├── Pedido.php                        # Órdenes de compra
├── DetallePedido.php                 # Items individuales del pedido
├── Comision.php                      # Comisiones de vendedores
├── MetaVendedor.php                  # Metas mensuales
├── Inventario.php                    # Control de stock
├── Promocion.php                     # Promociones y descuentos
├── ConfiguracionSistema.php          # Configuraciones generales
├── LogActividad.php                  # Logs de actividad
└── NotificacionUsuario.php           # Notificaciones por usuario
```

### ⚡ **COMPONENTES LIVEWIRE**
```
app/Livewire/
├── ToastNotifications.php            # Sistema de notificaciones reactivo
└── StatsWidget.php                   # Widget de estadísticas en tiempo real
```

### 🔧 **SERVICIOS Y TRAITS**
```
app/Services/                         # (Por implementar)
├── ComisionService.php               # Cálculo de comisiones
├── ReporteService.php                # Generación de reportes
└── NotificacionService.php           # Envío de notificaciones

app/Traits/                          # (Por implementar)  
├── HasReferidos.php                 # Manejo de referidos
└── HasComisiones.php                # Manejo de comisiones
```

---

## 🗄️ BASE DE DATOS - UBICACIONES

### 📋 **MIGRACIONES**
```
database/migrations/
├── 2014_10_12_000000_create_users_table.php
├── 2014_10_12_100000_create_password_resets_table.php
├── 2019_08_19_000000_create_failed_jobs_table.php
├── 2019_12_14_000001_create_personal_access_tokens_table.php
├── 2025_09_12_014629_add_fields_to_users_table.php   # Campos adicionales usuarios
├── create_categorias_table.php
├── create_productos_table.php
├── create_pedidos_table.php
├── create_detalle_pedidos_table.php
├── create_comisiones_table.php
├── create_metas_vendedor_table.php
├── create_inventarios_table.php
├── create_promociones_table.php
├── create_configuracion_sistema_table.php
├── create_logs_actividad_table.php
├── create_notificaciones_usuarios_table.php
└── create_sessions_table.php         # Para sesiones en BD
```

### 🌱 **SEEDERS**
```
database/seeders/
├── DatabaseSeeder.php                # Seeder principal
├── UserSeeder.php                    # Usuarios de prueba por roles
├── CategoriaSeeder.php               # Categorías de productos
├── ProductoSeeder.php                # Productos del catálogo
└── ConfiguracionSeeder.php           # Configuraciones iniciales
```

### 🏭 **FACTORIES**
```
database/factories/
├── UserFactory.php                   # Factory de usuarios
├── ProductoFactory.php               # Factory de productos
└── PedidoFactory.php                 # Factory de pedidos
```

---

## 🌐 RUTAS - UBICACIONES

### 🗺️ **ARCHIVO DE RUTAS**
```
routes/
├── web.php                           # Rutas web principales
├── api.php                          # API routes (futuro)
├── console.php                      # Comandos Artisan
└── channels.php                     # Canales de broadcast
```

### 🔗 **RUTAS PRINCIPALES DEFINIDAS**
```php
// RUTAS PÚBLICAS
GET  /                               → Redirige a login o dashboard
GET  /inicio                         → Fuerza logout y va al login  
GET  /login                          → Formulario de login
POST /login                          → Procesar login
POST /logout                         → Cerrar sesión
GET  /register                       → Formulario de registro
POST /register                       → Procesar registro

// RUTAS AUTENTICADAS
GET  /dashboard                      → Dashboard según rol del usuario
GET  /home                          → Redirige al dashboard

// RUTAS POR ROLES (Middleware role)
/admin/*                            → Solo administradores
/lider/*                            → Líderes y administradores  
/vendedor/*                         → Vendedores, líderes y admins
```

---

## ⚡ CONFIGURACIÓN - UBICACIONES

### 📝 **ARCHIVOS DE CONFIGURACIÓN**
```
config/
├── app.php                          # Configuración general de Laravel
├── auth.php                         # Configuración de autenticación
├── database.php                     # Configuración de base de datos
├── session.php                      # Configuración de sesiones
├── mail.php                        # Configuración de email
├── filesystems.php                 # Configuración de almacenamiento
└── livewire.php                     # Configuración de Livewire
```

### 🔐 **VARIABLES DE ENTORNO**
```
.env                                 # Variables de entorno (NO versionar)
├── APP_NAME="Arepa la Llanerita"
├── APP_ENV=local
├── APP_KEY=[generada]
├── APP_DEBUG=true
├── APP_URL=http://localhost
├── DB_CONNECTION=mysql
├── DB_HOST=127.0.0.1
├── DB_PORT=3306
├── DB_DATABASE=arepa_llanerita
├── DB_USERNAME=[usuario]
├── DB_PASSWORD=[password]
└── SESSION_DRIVER=database
```

---

## 🚀 COMANDOS DE DESARROLLO

### 🛠️ **COMANDOS PRINCIPALES**
```bash
# SERVIDOR Y DESARROLLO
php artisan serve                    # Iniciar servidor local (:8000)
npm run dev                         # Compilar assets desarrollo (watch)
npm run build                       # Compilar assets producción

# BASE DE DATOS
php artisan migrate                 # Ejecutar migraciones
php artisan migrate:fresh --seed    # Reset BD + datos prueba
php artisan db:seed                 # Solo seeders

# CACHE Y OPTIMIZACIÓN
php artisan optimize               # Optimizar aplicación
php artisan config:clear          # Limpiar cache config
php artisan view:clear             # Limpiar cache vistas
php artisan route:clear            # Limpiar cache rutas

# LIVEWIRE
php artisan livewire:make [Nombre] # Crear componente Livewire
php artisan livewire:publish --config # Publicar config Livewire

# HERRAMIENTAS
php artisan tinker                 # REPL de Laravel
php artisan route:list             # Listar rutas
php artisan make:controller [Nombre] # Crear controlador
php artisan make:model [Nombre]    # Crear modelo
php artisan make:migration [nombre] # Crear migración
```

---

## 🔍 UBICACIONES ESPECÍFICAS

### 🎨 **PARA CAMBIAR COLORES/ESTILOS:**
- **Variables CSS:** `resources/views/layouts/app.blade.php` (líneas 29-42)
- **Variables SCSS:** `resources/sass/_variables.scss`
- **Estilos específicos:** Cada dashboard tiene su `@push('styles')`

### 🎯 **PARA CAMBIAR LÓGICA DE NEGOCIO:**
- **Dashboards:** `app/Http/Controllers/DashboardController.php`
- **Modelos:** `app/Models/`
- **Middleware roles:** `app/Http/Middleware/RoleMiddleware.php`

### 🌐 **PARA CAMBIAR RUTAS:**
- **Rutas web:** `routes/web.php`
- **Redirecciones:** Función en línea 7-12 de `web.php`

### 🗄️ **PARA MODIFICAR BD:**
- **Nuevos campos:** Crear migración con `php artisan make:migration`
- **Datos de prueba:** Modificar seeders en `database/seeders/`
- **Relaciones:** Modificar modelos en `app/Models/`

---

## 👥 USUARIOS DE PRUEBA

### 🔑 **CREDENCIALES DE ACCESO**
```
🔴 ADMINISTRADOR
Email: admin@arepallanerita.com
Pass:  admin123

🟡 LÍDER
Email: lider@arepallanerita.com  
Pass:  lider123

🟢 VENDEDOR
Email: vendedor@arepallanerita.com
Pass:  vendedor123

🔵 CLIENTE
Email: cliente@test.com
Pass:  cliente123
```

---

## ⚠️ SOLUCIÓN AL PROBLEMA DE REDIRECCIÓN

### 🐛 **PROBLEMA IDENTIFICADO:**
Laravel mantiene sesiones activas en BD. Cuando accedes a `/` y hay una sesión previa, te redirige automáticamente al dashboard.

### ✅ **SOLUCIONES:**
1. **Temporal:** Accede a `/inicio` para forzar logout
2. **En navegador:** Limpiar cookies/localStorage del sitio
3. **En BD:** Truncar tabla `sessions`
4. **Para desarrollo:** Usar modo incógnito

### 🔧 **IMPLEMENTADO:**
- Ruta `/inicio` que fuerza logout y redirige al login
- La ruta `/` mantiene el comportamiento estándar de Laravel Auth

---

## 📋 PRÓXIMOS MÓDULOS A IMPLEMENTAR

### 🚧 **NO IMPLEMENTADO AÚN:**
- 📦 Gestión de Inventario
- 🛒 Gestión de Pedidos  
- 📊 Sistema de Reportes
- 💳 Integración de Pagos
- 📱 API REST para móvil
- 📧 Notificaciones Email/SMS
- 💬 Chat en tiempo real
- 🚚 Módulo de entregas

---

*Última actualización: Septiembre 2025*
*Proyecto: Red de Ventas Arepa la Llanerita*