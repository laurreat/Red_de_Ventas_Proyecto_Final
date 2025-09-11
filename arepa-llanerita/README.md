# 🥞 Arepa la Llanerita - Sistema de Ventas

Proyecto final SENA - Sistema de gestión y ventas en línea para empresa de arepas.

## 🛠️ Requisitos del Sistema

- **PHP 8.1+** (XAMPP recomendado)
- **Composer**
- **Node.js & NPM**
- **MySQL**
- **Git**

## 🚀 Instalación para Colaboradores

### 1. Clonar el repositorio
```bash
git clone [URL_DEL_REPO]
cd Red_de_Ventas_Proyecto_Final/arepa-llanerita
```

### 2. Instalar dependencias
```bash
# Dependencias de PHP
composer install

# Dependencias de Node.js
npm install
```

### 3. Configurar entorno
```bash
# Copiar archivo de configuración
copy .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar base de datos
Editar archivo `.env`:
```env
DB_DATABASE=arepa_llanerita
DB_USERNAME=root
DB_PASSWORD=
```

Crear base de datos en MySQL:
```sql
CREATE DATABASE arepa_llanerita;
```

### 5. Ejecutar migraciones
```bash
php artisan migrate
php artisan db:seed
```

### 6. Iniciar desarrollo
```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Compilar assets
npm run dev
```

## 👥 Usuarios de Prueba

| Rol | Email | Password | Cédula |
|-----|-------|----------|---------|
| Administrador | admin@arepallanerita.com | admin123 | 12345678 |
| Líder | lider@arepallanerita.com | lider123 | 87654321 |
| Vendedor | vendedor@arepallanerita.com | vendedor123 | 11223344 |
| Cliente | cliente@test.com | cliente123 | 99887766 |

## 📋 Sprints del Proyecto

### Sprint 1 (10 días) ✅ En desarrollo
- [x] Configuración inicial
- [ ] Sistema de autenticación
- [ ] Roles y permisos
- [ ] Dashboard básico

### Sprint 2 (15 días)
- [ ] Gestión de inventario
- [ ] Catálogo de productos
- [ ] CRUD productos

### Sprint 3 (15 días)
- [ ] Carrito de compras
- [ ] Sistema de pedidos
- [ ] Notificaciones

## 🤝 Flujo de Trabajo Git

1. **Crear rama para nueva funcionalidad:**
   ```bash
   git checkout -b feature/nombre-funcionalidad
   ```

2. **Hacer commits frecuentes:**
   ```bash
   git add .
   git commit -m "feat: descripción del cambio"
   ```

3. **Subir cambios:**
   ```bash
   git push origin feature/nombre-funcionalidad
   ```

4. **Crear Pull Request** en GitHub

## 📁 Estructura del Proyecto

```
arepa-llanerita/
├── app/
│   ├── Http/Livewire/      # Componentes Livewire
│   ├── Models/             # Modelos de datos
│   └── Http/Controllers/   # Controladores
├── database/
│   ├── migrations/         # Estructura de BD
│   └── seeders/           # Datos iniciales
├── resources/
│   ├── views/             # Vistas Blade
│   └── js/                # JavaScript
└── public/                # Archivos públicos
```

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Crear nuevos componentes
php artisan make:livewire NombreComponente
php artisan make:model NombreModelo -m
php artisan make:controller NombreController

# Base de datos
php artisan migrate:fresh --seed  # Recrear BD con datos
php artisan tinker                # Consola interactiva
```

## 🐛 Solución de Problemas Comunes

### Error: ZIP extension missing
En `C:\xampp\php\php.ini` descomentar:
```ini
extension=zip
```

### Error: Storage link
```bash
php artisan storage:link
```

### Error: Node modules
```bash
rm -rf node_modules
npm install
```

## 📞 Contacto

- **Desarrollador Principal:** [Tu nombre]
- **Colaborador:** [Nombre del compañero]
- **Institución:** SENA