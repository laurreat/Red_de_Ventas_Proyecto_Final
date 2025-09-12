# Contexto del Proyecto - Arepa la Llanerita

## Descripción General
Sistema de ventas y gestión para "Arepa la Llanerita" - Una empresa de arepas con sistema de referidos y red de vendedores multinivel.

## Estado Actual del Desarrollo

### ✅ **COMPLETADO - Módulo 1: Frontend Base**

#### Autenticación
- **Login horizontal responsive** implementado con diseño moderno
- Panel izquierdo: información de la empresa con logo
- Panel derecho: formulario de login  
- Totalmente responsive para web, tablet y móvil
- Sistema de roles: administrador, lider, vendedor, cliente

#### Dashboard por Roles
1. **Dashboard Administrador** (`/dashboard` - rol: administrador)
   - Métricas principales: usuarios, vendedores, productos, ventas
   - Pedidos recientes con estados y detalles
   - Productos populares con barras de progreso
   - Auto-actualización cada 5 minutos

2. **Dashboard Líder** (`/dashboard` - rol: lider)
   - Gestión de equipo de vendedores
   - Progreso de metas mensuales (personal y equipo)
   - Rendimiento individual de cada miembro
   - Sistema de logros y reconocimientos

3. **Dashboard Vendedor** (`/dashboard` - rol: vendedor)
   - Acciones rápidas: nuevo pedido, inventario, clientes, reportes
   - Progreso de meta mensual con animaciones
   - Sistema de referidos con código personal
   - Historial de pedidos y comisiones

4. **Dashboard Cliente** (`/dashboard` - rol: cliente)
   - Bienvenida personalizada con referidos
   - Estadísticas: pedidos, total comprado, favoritos, referidos
   - Acciones rápidas: hacer pedido, ver menú, historial
   - Programa de referidos con código compartible

#### Componentes Livewire Dinámicos
1. **ToastNotifications** (`@livewire('toast-notifications')`)
   - Sistema de notificaciones reactivas
   - Tipos: success, error, warning, info
   - Auto-hide configurable
   - Transiciones suaves con Alpine.js
   - Función global: `showToast(message, type, duration)`

2. **StatsWidget** (`@livewire('stats-widget')`)
   - Métricas en tiempo real con polling automático
   - Animaciones cuando cambian valores
   - Tipos disponibles: total_usuarios, ventas_mes, pedidos_hoy, etc.
   - Uso: `@livewire('stats-widget', ['title' => 'Usuarios', 'type' => 'total_usuarios', 'icon' => 'bi-people', 'color' => 'primary'])`

### 🏗️ **Arquitectura Técnica**

#### Frontend Stack
- **Laravel 11** con PHP 8.2+
- **Bootstrap 5.3.2** personalizado
- **Livewire 3** para componentes reactivos
- **Alpine.js** para interactividad client-side
- **Vite** para bundling y optimización
- **Bootstrap Icons** para iconografía

#### Base de Datos
- **12 modelos implementados**: User, Categoria, Producto, Pedido, DetallePedido, etc.
- **Sistema de referidos** completo en tabla users
- **Roles y permisos** con middleware personalizado
- **Migraciones** para campos adicionales en users
- **Seeders** con usuarios de prueba por cada rol

#### Middleware y Rutas
- **RoleMiddleware**: Control de acceso por roles
- **Rutas protegidas** por autenticación y roles específicos
- **DashboardController**: Lógica centralizada para todos los dashboards
- **Redirecciones inteligentes** según rol del usuario

#### Estilos y UX
- **Variables CSS personalizadas** para colores de marca
- **Design system consistente** en todos los dashboards
- **Animaciones CSS** para feedback visual
- **Loading states** y spinners durante operaciones
- **Responsive design** mobile-first

### 📁 **Estructura de Archivos Importantes**

```
arepa-llanerita/
├── app/
│   ├── Http/
│   │   ├── Controllers/DashboardController.php     # Lógica de dashboards
│   │   └── Middleware/RoleMiddleware.php           # Control de acceso
│   ├── Livewire/
│   │   ├── ToastNotifications.php                 # Sistema de notificaciones  
│   │   └── StatsWidget.php                        # Métricas dinámicas
│   └── Models/                                     # 12 modelos implementados
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php                  # Layout principal
│   │   ├── auth/login.blade.php                   # Login horizontal
│   │   ├── dashboard/
│   │   │   ├── admin.blade.php                    # Dashboard administrador
│   │   │   ├── lider.blade.php                    # Dashboard líder
│   │   │   ├── vendedor.blade.php                 # Dashboard vendedor
│   │   │   └── cliente.blade.php                  # Dashboard cliente
│   │   └── livewire/
│   │       ├── toast-notifications.blade.php
│   │       └── stats-widget.blade.php
│   ├── sass/app.scss                              # Estilos personalizados
│   └── js/app.js                                  # JavaScript principal
├── database/
│   ├── migrations/
│   │   └── 2025_09_12_014629_add_fields_to_users_table.php
│   └── seeders/UserSeeder.php                     # Usuarios de prueba
├── routes/web.php                                 # Rutas con middleware de roles
└── public/build/                                  # Assets compilados
```

### 🧪 **Usuarios de Prueba**
```
Admin: admin@arepallanerita.com / admin123
Líder: lider@arepallanerita.com / lider123  
Vendedor: vendedor@arepallanerita.com / vendedor123
Cliente: cliente@test.com / cliente123
```

### 🎨 **Sistema de Colores Corporativos - ACTUALIZADO ✅**
```css
:root {
    --arepa-primary: #722F37;           /* Vino tinto (color principal) */
    --arepa-secondary: #FFFFFF;         /* Blanco (color secundario) */
    --arepa-accent: #8B4B52;           /* Vino tinto medio (acentos) */
    --arepa-light-burgundy: #A85D65;   /* Vino tinto claro */
    --arepa-dark-burgundy: #5A252B;    /* Vino tinto oscuro */
    --arepa-success: #28a745;          /* Verde para éxito */
    --arepa-danger: #dc3545;           /* Rojo para errores */
    --arepa-warning: #856404;          /* Marrón para advertencias */
    --arepa-info: #0c5460;             /* Azul oscuro para info */
    --arepa-dark: #343a40;             /* Gris oscuro */
    --arepa-light: #f8f9fa;            /* Gris claro */
    --arepa-cream: #FFF8F8;            /* Crema suave para fondos */
}
```

**Uso de los colores:**
- `--arepa-primary`: Color principal para botones, enlaces, íconos importantes
- `--arepa-secondary`: Fondos limpios, textos sobre fondos oscuros
- `--arepa-accent`: Gradientes, hover effects, elementos secundarios
- `--arepa-light-burgundy`: Elementos más sutiles, bordes suaves
- `--arepa-dark-burgundy`: Textos oscuros, elementos de contraste
- `--arepa-cream`: Fondo de la aplicación, áreas suaves

### 🚀 **Comandos de Desarrollo**
```bash
# Desarrollo
php artisan serve              # Servidor local
npm run dev                   # Compilar assets desarrollo
php artisan migrate:fresh --seed  # Reset DB con datos prueba

# Producción  
npm run build                 # Compilar assets optimizados
php artisan optimize          # Optimizar Laravel
```

### 🔄 **Funcionalidades Clave**
- **Auto-actualización**: Métricas se actualizan automáticamente
- **Notificaciones toast**: Sistema global de mensajes
- **Responsive**: Funciona en todos los dispositivos
- **Role-based**: Contenido específico según rol del usuario
- **Performance**: Carga rápida con assets optimizados
- **UX moderna**: Animaciones, transiciones, loading states

## 📋 **Lo que NO está implementado aún**
- Módulos de gestión (inventario, pedidos, productos)
- Sistema de reportes avanzados
- APIs para móvil
- Integración con pasarelas de pago
- Sistema de notificaciones por email/SMS
- Chat en tiempo real
- Módulo de entregas y logística

## 🎯 **Próximos Pasos Sugeridos**
Ver archivo `PROXIMOS_PASOS.md` para prompts específicos para continuar el desarrollo.