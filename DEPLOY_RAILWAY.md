# 🚀 Guía Completa: Desplegar Arepa la Llanerita en Railway

**Última actualización:** 2025-10-07
**Objetivo:** Hosting de pruebas gratuito para testear la aplicación en dispositivos móviles
**Tiempo estimado:** 30-45 minutos

---

## 📋 Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Preparación del Proyecto](#preparación-del-proyecto)
3. [Configuración en Railway](#configuración-en-railway)
4. [Configuración de MongoDB](#configuración-de-mongodb)
5. [Configuración de Variables de Entorno](#configuración-de-variables-de-entorno)
6. [Despliegue y Verificación](#despliegue-y-verificación)
7. [Inicialización de Datos](#inicialización-de-datos)
8. [Pruebas en Móvil (PWA)](#pruebas-en-móvil-pwa)
9. [Solución de Problemas](#solución-de-problemas)
10. [Comandos Útiles](#comandos-útiles)

---

## ✅ Requisitos Previos

### Antes de empezar, necesitas:

- ✅ Cuenta de GitHub (con el repositorio ya creado)
- ✅ Git instalado en tu PC
- ✅ Navegador web (Chrome, Firefox, Edge)
- ✅ Tu celular con internet (para probar la PWA)

### Cuentas a crear (GRATIS):

1. **GitHub Account** - Ya lo tienes ✅
2. **Railway Account** - https://railway.app (crear con GitHub)

---

## 🔧 Preparación del Proyecto

### Paso 1: Verificar Archivos de Configuración

Los siguientes archivos ya están creados en tu proyecto:

#### ✅ `arepa-llanerita/Procfile`
```
web: php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
```

#### ✅ `arepa-llanerita/nixpacks.toml`
```toml
[phases.setup]
nixPkgs = ['php82', 'php82Extensions.mongodb', 'php82Extensions.redis', 'nodejs_20']

[phases.install]
cmds = [
  'composer install --no-dev --optimize-autoloader',
  'npm ci',
  'npm run build'
]

[phases.build]
cmds = [
  'php artisan config:cache',
  'php artisan route:cache',
  'php artisan view:cache'
]

[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=${PORT:-8000}'
```

### Paso 2: Generar APP_KEY

En tu terminal (Git Bash o CMD):

```bash
cd "C:\Users\luKsha\Documents\GitHub\Red_de_Ventas_Proyecto_Final\arepa-llanerita"
php artisan key:generate --show
```

**📝 IMPORTANTE:** Copia y guarda el resultado (algo como `base64:Yr7CBP1wA...`). Lo necesitarás en Railway.

**Ejemplo de salida:**
```
base64:Yr7CBP1wAEorK80S5lEMNltXn/zuZZF13YOVFX5AaCE=
```

### Paso 3: Verificar Commits en GitHub

Verifica que los archivos estén en GitHub:

```bash
git status
git log --oneline -5
```

Si todo está actualizado, verás: `Your branch is up to date with 'origin/main'`

---

## 🌐 Configuración en Railway

### Paso 1: Crear Cuenta en Railway

1. Ve a **https://railway.app**
2. Haz clic en **"Start a New Project"**
3. Selecciona **"Login with GitHub"**

   ![Railway Login](https://railway.app/brand/logo-light.png)

4. **Autoriza Railway** para acceder a tus repositorios
5. Acepta los permisos solicitados

### Paso 2: Crear Nuevo Proyecto

1. Una vez dentro, haz clic en **"New Project"**
2. Verás varias opciones, selecciona **"Deploy MongoDB"**

   **⚠️ IMPORTANTE:** NO selecciones todavía tu repositorio. Primero configuramos MongoDB.

---

## 🍃 Configuración de MongoDB

### Paso 1: Desplegar MongoDB

1. En el marketplace de Railway, busca **"MongoDB"**
2. Haz clic en **"Add MongoDB"**
3. Railway comenzará a crear tu base de datos (tarda ~1 minuto)
4. Verás un servicio llamado **"MongoDB"** en tu proyecto

### Paso 2: Obtener Credenciales de MongoDB

1. Haz clic en el servicio **"MongoDB"**
2. Ve a la pestaña **"Variables"**
3. Verás variables como:
   ```
   MONGO_URL
   MONGO_HOST
   MONGO_PORT
   MONGO_DATABASE
   MONGO_USER (puede que no aparezca si no tiene auth)
   MONGO_PASSWORD (puede que no aparezca si no tiene auth)
   ```

4. **📝 Anota estas variables** (las necesitarás en el siguiente paso)

   **Ejemplo:**
   ```
   MONGO_URL=mongodb://mongo.railway.internal:27017
   MONGO_HOST=mongo.railway.internal
   MONGO_PORT=27017
   MONGO_DATABASE=railway
   ```

### Paso 3: Hacer MongoDB Accesible Públicamente (Opcional)

**⚠️ SOLO para desarrollo/testing:**

1. En el servicio MongoDB, ve a **"Settings"**
2. Busca **"Networking"**
3. Haz clic en **"Generate Domain"** (esto crea una URL pública para MongoDB)

**⚠️ NOTA:** Para producción real, esto NO se recomienda por seguridad.

---

## 🚂 Desplegar Aplicación Laravel

### Paso 1: Agregar Servicio de Laravel

1. En tu proyecto de Railway, haz clic en **"New"** → **"GitHub Repo"**
2. Busca tu repositorio: **"laurreat/Red_de_Ventas_Proyecto_Final"**
3. Haz clic en **"Add"** o **"Deploy"**
4. Railway detectará automáticamente que es Laravel

### Paso 2: Configurar Root Directory

Railway puede confundirse con la estructura del proyecto. Configura el directorio raíz:

1. Haz clic en tu servicio de Laravel (el nuevo que acabas de crear)
2. Ve a **"Settings"**
3. Busca **"Root Directory"**
4. Cambia de `/` a `/arepa-llanerita`

   ```
   Root Directory: /arepa-llanerita
   ```

5. Haz clic en **"Save Changes"**

### Paso 3: Configurar Custom Build Command

1. Aún en **"Settings"**, busca **"Build Command"**
2. Cambia el comando predeterminado a:

   ```bash
   npm ci && npm run build && composer install --no-dev --optimize-autoloader
   ```

3. Busca **"Start Command"** y asegúrate que sea:

   ```bash
   php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
   ```

---

## 🔐 Configuración de Variables de Entorno

### Paso 1: Acceder a Variables

1. En tu servicio de Laravel, ve a **"Variables"**
2. Haz clic en **"Raw Editor"** (más fácil para pegar todo)

### Paso 2: Agregar Variables de Entorno

Copia y pega lo siguiente (reemplaza los valores entre `< >`):

```env
# === APLICACIÓN ===
APP_NAME="Arepa la Llanerita"
APP_ENV=production
APP_KEY=<TU_APP_KEY_GENERADO_EN_PASO_2>
APP_DEBUG=false
APP_URL=<URL_QUE_RAILWAY_TE_DARÁ>

# === LOGS ===
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# === MONGODB (PRINCIPAL) ===
DB_CONNECTION=mongodb
MONGODB_HOST=${{MongoDB.MONGO_HOST}}
MONGODB_PORT=${{MongoDB.MONGO_PORT}}
MONGODB_DATABASE=${{MongoDB.MONGO_DATABASE}}
MONGODB_USERNAME=
MONGODB_PASSWORD=

# === MYSQL (OPCIONAL - Password Resets) ===
# Si no quieres usar MySQL, deja estos campos vacíos
MYSQL_HOST=
MYSQL_PORT=3306
MYSQL_DATABASE=
MYSQL_USERNAME=
MYSQL_PASSWORD=

# === CACHE Y SESIONES ===
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

# === CACHE CONFIGURATION ===
CACHE_PREFIX=arepa_llanerita_railway

# === REDIS (DESACTIVADO PARA RAILWAY FREE) ===
REDIS_HOST=
REDIS_PASSWORD=
REDIS_PORT=6379

# === MAIL (OPCIONAL) ===
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@arepallanerita.com"
MAIL_FROM_NAME="${APP_NAME}"

# === CONFIGURACIÓN DE NEGOCIO ===
COMISION_VENDEDOR=10
COMISION_LIDER=5
BONO_REFERIDO=50000
MONEDA=VES

# === FEATURES DE LA APLICACIÓN ===
SISTEMA_REFERIDOS=true
NOTIFICACIONES_EMAIL=false
AUDITORIA_ACTIVA=true

# === SEGURIDAD ===
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# === PERFORMANCE ===
OPCACHE_ENABLE=true
QUERY_LOG=false

# === DEVELOPMENT ===
TELESCOPE_ENABLED=false
DEBUGBAR_ENABLED=false
```

### Paso 3: Reemplazar Valores

#### **APP_KEY**
Reemplaza `<TU_APP_KEY_GENERADO_EN_PASO_2>` con el valor que generaste antes.

**Ejemplo:**
```env
APP_KEY=base64:Yr7CBP1wAEorK80S5lEMNltXn/zuZZF13YOVFX5AaCE=
```

#### **APP_URL**
Por ahora, déjalo así. Después del primer despliegue, Railway te dará una URL.

```env
APP_URL=https://production-XXXX.up.railway.app
```

#### **MONGODB_HOST, MONGODB_PORT, MONGODB_DATABASE**

Railway usa **referencias de variables** automáticas:

```env
MONGODB_HOST=${{MongoDB.MONGO_HOST}}
MONGODB_PORT=${{MongoDB.MONGO_PORT}}
MONGODB_DATABASE=${{MongoDB.MONGO_DATABASE}}
```

Esto significa que Railway tomará automáticamente las variables del servicio MongoDB.

### Paso 4: Guardar Variables

1. Haz clic en **"Save Config"** o presiona `Ctrl + S`
2. Railway reiniciará automáticamente el despliegue

---

## 🚀 Despliegue y Verificación

### Paso 1: Verificar Build

1. Ve a la pestaña **"Deployments"** de tu servicio Laravel
2. Verás el progreso del despliegue en tiempo real
3. Espera a que diga **"Success"** o **"Deployed"**

   **Logs que verás:**
   ```
   ===== Installing Dependencies =====
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build

   ===== Building Application =====
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

   ===== Starting Server =====
   php artisan serve --host=0.0.0.0 --port=8000
   ```

### Paso 2: Obtener URL Pública

1. Ve a **"Settings"** de tu servicio Laravel
2. Busca **"Domains"** o **"Networking"**
3. Haz clic en **"Generate Domain"**
4. Railway creará una URL como:

   ```
   https://red-de-ventas-proyecto-final-production.up.railway.app
   ```

5. **📝 Copia esta URL**

### Paso 3: Actualizar APP_URL

1. Ve de nuevo a **"Variables"**
2. Busca `APP_URL` y reemplázalo con tu URL de Railway:

   ```env
   APP_URL=https://red-de-ventas-proyecto-final-production.up.railway.app
   ```

3. Guarda y espera a que se redespliegue (tarda ~1 minuto)

### Paso 4: Probar Acceso Web

1. Abre tu URL de Railway en el navegador
2. **Deberías ver un error 500 o página en blanco** (es normal, falta inicializar MongoDB)

---

## 💾 Inicialización de Datos

### Paso 1: Acceder a Terminal de Railway

1. En tu servicio Laravel, haz clic en **"Shell"** o **"Terminal"** (arriba a la derecha)
2. Se abrirá una terminal web conectada a tu servidor

### Paso 2: Crear Colecciones de MongoDB

En la terminal de Railway, ejecuta:

```bash
php artisan mongo:collections
```

**Salida esperada:**
```
✓ Colección 'users' creada con índices
✓ Colección 'pedidos' creada con índices
✓ Colección 'productos' creada con índices
✓ Colección 'comisiones' creada con índices
✓ Colección 'referidos' creada con índices
✓ Colección 'roles' creada con índices
✓ Colección 'permissions' creada con índices
✓ Colección 'notificaciones' creada con índices
✓ Colección 'auditorias' creada con índices
✓ Colección 'configuraciones' creada con índices

¡Todas las colecciones fueron creadas exitosamente!
```

### Paso 3: Sembrar Datos Iniciales

```bash
php artisan mongo:seed
```

**Salida esperada:**
```
Sembrando datos iniciales en MongoDB...

✓ Usuario administrador creado
  Email: admin@arepallanerita.com
  Contraseña: admin123

✓ Roles y permisos creados
✓ Productos de ejemplo creados
✓ Configuraciones iniciales creadas

¡Datos iniciales sembrados exitosamente!
```

**📝 IMPORTANTE:** Anota estas credenciales de administrador:

```
Email: admin@arepallanerita.com
Contraseña: admin123
```

### Paso 4: Crear Enlace Simbólico de Storage

```bash
php artisan storage:link
```

**Salida esperada:**
```
The [public/storage] link has been connected to [storage/app/public].
```

### Paso 5: Limpiar Caché

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Paso 6: Verificar Conexión a MongoDB

```bash
php artisan test:mongo
```

**Salida esperada:**
```
✓ Conexión a MongoDB exitosa
✓ Base de datos: railway
✓ Host: mongo.railway.internal:27017
✓ Colecciones encontradas: 10
```

---

## 🎯 Pruebas en Móvil (PWA)

### Paso 1: Acceder desde el Navegador de Escritorio

1. Abre tu URL de Railway en **Chrome** o **Edge**:

   ```
   https://red-de-ventas-proyecto-final-production.up.railway.app
   ```

2. **Deberías ver la página de LOGIN**
3. Inicia sesión con:

   ```
   Email: admin@arepallanerita.com
   Contraseña: admin123
   ```

4. Verifica que cargue el **Dashboard de Administrador**

### Paso 2: Probar en Móvil (Android)

1. **Abre Chrome en tu celular Android**
2. Ingresa la URL de Railway
3. Inicia sesión
4. **Presiona el menú de Chrome** (3 puntos verticales)
5. Busca la opción **"Agregar a pantalla de inicio"** o **"Instalar app"**
6. Acepta la instalación

   **📱 Aparecerá el ícono de la app en tu pantalla de inicio**

### Paso 3: Probar en Móvil (iOS/iPhone)

1. **Abre Safari en tu iPhone**
2. Ingresa la URL de Railway
3. Inicia sesión
4. **Presiona el botón de "Compartir"** (cuadrado con flecha hacia arriba)
5. Busca **"Agregar a pantalla de inicio"**
6. Acepta

   **📱 Aparecerá el ícono de la app en tu pantalla de inicio**

### Paso 4: Probar Funcionalidades

Desde tu móvil, verifica:

- ✅ Login y logout
- ✅ Dashboard carga correctamente
- ✅ Ver lista de productos
- ✅ Ver lista de pedidos
- ✅ Crear un nuevo pedido
- ✅ Ver detalles de un pedido
- ✅ Módulo de comisiones
- ✅ Red de referidos (visualización)

### Paso 5: Compartir URL con Usuarios de Prueba

Comparte la URL con amigos/testers para que prueben:

```
URL: https://red-de-ventas-proyecto-final-production.up.railway.app

Credenciales de prueba (admin):
Email: admin@arepallanerita.com
Contraseña: admin123
```

**⚠️ IMPORTANTE:** Crea usuarios de prueba adicionales para roles de:
- Líder
- Vendedor
- Cliente

---

## 🔍 Solución de Problemas

### Problema 1: Error 500 al Abrir la URL

**Causa:** Variables de entorno incorrectas o MongoDB no conectado

**Solución:**

1. Ve a **"Deployments"** → **"View Logs"**
2. Busca errores relacionados con:
   - `MONGODB_HOST not found`
   - `Connection refused`
3. Verifica que las variables `MONGODB_HOST`, `MONGODB_PORT`, `MONGODB_DATABASE` estén configuradas
4. Revisa que el servicio MongoDB esté **"Running"**

### Problema 2: "APP_KEY not set"

**Solución:**

1. Genera un nuevo APP_KEY local:
   ```bash
   php artisan key:generate --show
   ```
2. Copia el resultado
3. Ve a Railway → Variables → `APP_KEY` → Pega el valor
4. Guarda y espera redespliegue

### Problema 3: CSS/JS No Cargan

**Causa:** Vite no compiló los assets o `APP_URL` incorrecto

**Solución:**

1. Ve a **"Deployments"** → **"View Logs"**
2. Busca la sección **"npm run build"**
3. Verifica que diga:
   ```
   ✓ built in 15s
   ```
4. Si no compiló, ve a **"Settings"** → **"Build Command"** y asegúrate que tenga:
   ```bash
   npm ci && npm run build && composer install --no-dev --optimize-autoloader
   ```

### Problema 4: Imágenes de Productos No Cargan

**Causa:** Storage link no creado

**Solución:**

1. En Terminal de Railway:
   ```bash
   php artisan storage:link
   ```
2. Verifica con:
   ```bash
   ls -la public/storage
   ```

### Problema 5: Login No Funciona

**Causa:** Datos no sembrados en MongoDB

**Solución:**

1. En Terminal de Railway:
   ```bash
   php artisan mongo:seed --force
   ```
2. Intenta de nuevo con:
   ```
   Email: admin@arepallanerita.com
   Contraseña: admin123
   ```

### Problema 6: MongoDB Connection Timeout

**Causa:** Variables `${{MongoDB.XXXX}}` no se están resolviendo

**Solución:**

1. Ve a tu servicio **MongoDB** → **Variables**
2. Copia los valores reales:
   ```
   MONGO_HOST=mongo.railway.internal
   MONGO_PORT=27017
   MONGO_DATABASE=railway
   ```
3. Ve a tu servicio **Laravel** → **Variables**
4. Reemplaza las referencias por los valores reales:
   ```env
   MONGODB_HOST=mongo.railway.internal
   MONGODB_PORT=27017
   MONGODB_DATABASE=railway
   ```

---

## 🛠️ Comandos Útiles

### En Terminal de Railway (Shell)

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Verificar salud del sistema
php artisan system:health

# Limpiar caché completo
php artisan optimize:clear

# Recrear colecciones (⚠️ ELIMINA DATOS)
php artisan mongo:collections --recreate

# Verificar conexión a MongoDB
php artisan test:mongo

# Ver rutas disponibles
php artisan route:list

# Ver usuarios creados
php artisan tinker
>>> User::count()
>>> User::all()->pluck('email')
>>> exit

# Crear usuario manualmente
php artisan tinker
>>> $user = new App\Models\User;
>>> $user->name = "Usuario Prueba";
>>> $user->email = "prueba@test.com";
>>> $user->password = Hash::make('password123');
>>> $user->rol = "vendedor";
>>> $user->activo = true;
>>> $user->save();
>>> exit
```

### En tu PC Local (Git Bash)

```bash
# Ver estado de Git
git status

# Hacer cambios y deployar
git add .
git commit -m "Descripción del cambio"
git push origin main

# Railway redesplegarará automáticamente
```

---

## 📊 Monitoreo y Logs

### Ver Logs en Railway

1. Ve a tu servicio Laravel
2. Haz clic en **"Deployments"**
3. Selecciona el despliegue activo
4. Haz clic en **"View Logs"**

### Métricas de Uso (Plan Gratuito)

Railway te da **500 horas/mes** gratis:

- Si tu app está **siempre activa**: ~720 horas/mes (se acabarán las horas gratis)
- **Solución:** Railway puede poner tu app en "sleep" cuando no se use

### Configurar Sleep Mode (Opcional)

1. Ve a **"Settings"** de tu servicio Laravel
2. Busca **"Health Check"**
3. Configura:
   ```
   Health Check Path: /
   Health Check Timeout: 300
   ```

Esto apagará tu app después de 5 minutos de inactividad y la encenderá cuando alguien acceda.

---

## 🎯 Checklist Final

Antes de compartir la app con usuarios:

- [ ] URL de Railway carga correctamente
- [ ] Login funciona con credenciales de admin
- [ ] Dashboard muestra estadísticas
- [ ] Módulo de Productos carga y muestra productos
- [ ] Módulo de Pedidos permite crear pedidos
- [ ] Stock se actualiza al crear/cancelar pedidos
- [ ] PWA se instala correctamente en móvil Android
- [ ] PWA se instala correctamente en móvil iOS
- [ ] Ícono de la app aparece en pantalla de inicio
- [ ] App funciona sin conexión (offline) - básico
- [ ] Usuarios de prueba pueden acceder con sus roles

---

## 📝 Notas Importantes

### Limitaciones del Plan Gratuito de Railway

- **500 horas de servidor/mes** (~16 horas/día si está siempre activa)
- **100 GB de ancho de banda/mes**
- **1 GB de RAM** por servicio
- **1 GB de almacenamiento** para MongoDB
- **Sin tarjeta de crédito requerida** para el plan gratuito

### Recomendaciones para Pruebas

1. **No uses datos reales** de clientes (GDPR)
2. **Crea usuarios de prueba** con datos ficticios
3. **Documenta todos los errores** que encuentres
4. **Pide feedback** a usuarios testers sobre UX en móvil
5. **Monitorea el uso de horas** en Railway Dashboard

### Migración a Producción (Futuro)

Cuando estés listo para producción real:

1. **Railway Plan Hobby** ($5/mes) - 500 horas adicionales
2. **Hostinger VPS** - Control total
3. **AWS/DigitalOcean** - Escalabilidad

---

## 🆘 Soporte y Recursos

### Documentación Oficial

- **Railway Docs:** https://docs.railway.app
- **Laravel Deployment:** https://laravel.com/docs/12.x/deployment
- **MongoDB Laravel:** https://mongodb.github.io/laravel-mongodb/

### Comunidad

- **Railway Discord:** https://discord.gg/railway
- **Laravel Discord:** https://discord.gg/laravel

### Contacto del Proyecto

- **Desarrollador:** Luis Alberto Urrea Trujillo
- **Email:** luis2005.320@gmail.com
- **GitHub:** https://github.com/laurreat

---

## ✅ Resumen de URLs y Credenciales

Una vez completado el despliegue, tendrás:

```
=== ACCESO WEB ===
URL Railway: https://red-de-ventas-proyecto-final-production.up.railway.app

=== CREDENCIALES ADMIN ===
Email: admin@arepallanerita.com
Contraseña: admin123

=== MONGODB ===
Host: mongo.railway.internal
Puerto: 27017
Database: railway

=== REPOSITORIO ===
GitHub: https://github.com/laurreat/Red_de_Ventas_Proyecto_Final
```

---

## 🎉 ¡Listo!

Tu aplicación **Arepa la Llanerita** ya está desplegada en Railway y lista para pruebas.

**Siguientes pasos:**

1. ✅ Comparte la URL con testers
2. ✅ Instala la PWA en tus dispositivos
3. ✅ Documenta bugs y mejoras
4. ✅ Itera y mejora basándote en feedback

**¡Éxito con las pruebas!** 🚀🥘

---

**Generado con ❤️ para preservar la tradición culinaria colombiana amazónica**

*Última actualización: 2025-10-07*
