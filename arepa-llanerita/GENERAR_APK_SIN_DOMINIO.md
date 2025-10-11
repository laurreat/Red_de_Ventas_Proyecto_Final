# 📱 Generar APK sin Dominio Propio - Guía Completa

## 🎯 Resumen

**¿Se puede generar una APK sin tener un dominio?**
✅ **SÍ, es posible**, pero con algunas consideraciones:

### Opciones Disponibles:

1. **✅ Usar hosting gratuito con HTTPS** (Recomendado)
2. **✅ Usar subdominios gratuitos**
3. **⚠️ APK "local" sin TWA** (limitado)
4. **✅ Usar túneles temporales para testing**

---

## 🌟 Opción 1: Hosting Gratuito con HTTPS (RECOMENDADO)

### Por Qué Esta Opción:
- ✅ 100% Gratis
- ✅ HTTPS incluido
- ✅ Subdominio incluido
- ✅ Funciona para Play Store
- ✅ Fácil deployment desde GitHub

### Servicios Recomendados:

#### 1. **Vercel** (Más Recomendado)
```
✅ Gratis para siempre
✅ HTTPS automático
✅ Dominio: tu-app.vercel.app
✅ Deploy desde GitHub
✅ CDN global
✅ 100GB ancho de banda/mes
```

#### 2. **Netlify**
```
✅ Gratis para siempre
✅ HTTPS automático
✅ Dominio: tu-app.netlify.app
✅ Deploy desde GitHub
✅ 100GB ancho de banda/mes
```

#### 3. **Railway**
```
✅ Gratis hasta $5/mes de uso
✅ HTTPS automático
✅ Dominio: tu-app.railway.app
✅ Ideal para apps Laravel
✅ Base de datos incluida
```

#### 4. **Render**
```
✅ Gratis para siempre
✅ HTTPS automático
✅ Dominio: tu-app.onrender.com
✅ Deploy desde GitHub
✅ Ideal para apps completas
```

---

## 🚀 TUTORIAL COMPLETO: Deployment en Vercel

### Paso 1: Preparar tu Proyecto

```bash
# 1. Asegúrate de tener Git instalado
git --version

# 2. Si no está inicializado, inicializa el repo
cd C:\Users\luKsha\Documents\GitHub\Red_de_Ventas_Proyecto_Final
git init
git add .
git commit -m "Preparar para deployment"
```

### Paso 2: Subir a GitHub

```bash
# Opción A: Si ya tienes un repo en GitHub
git remote add origin https://github.com/TU_USUARIO/red-de-ventas-arepa-llanerita.git
git push -u origin main

# Opción B: Crear nuevo repo desde GitHub
# 1. Ve a github.com
# 2. Click en "New repository"
# 3. Nombre: red-de-ventas-arepa-llanerita
# 4. Click "Create repository"
# 5. Sigue las instrucciones que aparecen
```

### Paso 3: Conectar con Vercel

1. **Ir a Vercel**
   - Visita: https://vercel.com
   - Click en "Sign Up" (gratis)
   - Selecciona "Continue with GitHub"

2. **Autorizar GitHub**
   - Permite que Vercel acceda a tus repos
   - Acepta los permisos

3. **Importar Proyecto**
   - Click en "Add New..." → "Project"
   - Busca tu repo: `red-de-ventas-arepa-llanerita`
   - Click en "Import"

4. **Configurar Proyecto**
   ```
   Framework Preset: Other
   Root Directory: arepa-llanerita
   Build Command: composer install && php artisan key:generate
   Output Directory: public
   Install Command: composer install
   ```

5. **Variables de Entorno**

   Click en "Environment Variables" y agrega:
   ```
   APP_NAME=Arepa la Llanerita
   APP_ENV=production
   APP_KEY=base64:TU_APP_KEY_AQUI
   APP_DEBUG=false
   APP_URL=https://tu-app.vercel.app

   # MongoDB
   DB_CONNECTION=mongodb
   MONGODB_HOST=TU_MONGODB_HOST
   MONGODB_PORT=27017
   MONGODB_DATABASE=arepa_llanerita
   MONGODB_USERNAME=TU_USERNAME
   MONGODB_PASSWORD=TU_PASSWORD

   # Moneda
   MONEDA=COP
   ```

6. **Deploy**
   - Click en "Deploy"
   - Espera 2-5 minutos
   - ¡Listo! Tu app estará en: `https://tu-app.vercel.app`

### Paso 4: Configurar Laravel para Vercel

Crea el archivo `vercel.json` en la raíz de `arepa-llanerita/`:

```json
{
  "version": 2,
  "builds": [
    {
      "src": "public/**",
      "use": "@vercel/static"
    },
    {
      "src": "index.php",
      "use": "vercel-php@0.6.0"
    }
  ],
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/index.php"
    }
  ],
  "env": {
    "APP_ENV": "production",
    "APP_DEBUG": "false",
    "LOG_CHANNEL": "stderr"
  }
}
```

### Paso 5: Configurar MongoDB (Base de Datos)

#### Opción A: MongoDB Atlas (Gratis)

1. **Crear Cuenta**
   - Ve a: https://www.mongodb.com/cloud/atlas/register
   - Registrate gratis

2. **Crear Cluster**
   - Click en "Build a Database"
   - Selecciona "FREE" (M0)
   - Región: Closest to Colombia (ej: São Paulo)
   - Click "Create"

3. **Configurar Acceso**
   - Username: `arepa_admin`
   - Password: (guarda esta contraseña)
   - IP Address: `0.0.0.0/0` (permitir todos)

4. **Obtener Connection String**
   - Click en "Connect"
   - Selecciona "Connect your application"
   - Copia el string:
   ```
   mongodb+srv://arepa_admin:PASSWORD@cluster0.xxxxx.mongodb.net/arepa_llanerita?retryWrites=true&w=majority
   ```

5. **Actualizar Variables en Vercel**
   - Ve a tu proyecto en Vercel
   - Settings → Environment Variables
   - Actualiza MONGODB_HOST, MONGODB_USERNAME, MONGODB_PASSWORD

### Paso 6: Probar tu App

```bash
# Visita tu app
https://tu-app.vercel.app

# Verifica que funcione:
# ✅ Página principal carga
# ✅ Login funciona
# ✅ Base de datos conecta
```

---

## 📦 Generar APK con tu Subdominio de Vercel

Ahora que tienes tu app en `https://tu-app.vercel.app`, puedes generar la APK:

### Método 1: PWABuilder (Más Fácil)

```bash
# 1. Ve a PWABuilder
https://www.pwabuilder.com

# 2. Ingresa tu URL
https://tu-app.vercel.app

# 3. Genera el paquete Android
- Package ID: com.arepallanerita.app
- App Name: Arepa la Llanerita
- Host: tu-app.vercel.app

# 4. Descarga APK y AAB
¡Listo para Play Store!
```

### Método 2: Bubblewrap

```bash
# Ejecuta el script que creamos
cd arepa-llanerita
generar-apk.bat

# Opción 1: Inicializar
# Cuando pregunte el dominio: https://tu-app.vercel.app

# Opción 2: Generar APK
# Se generará con tu subdominio de Vercel
```

### Configurar assetlinks.json en Vercel

1. **Crear el archivo**
   ```
   arepa-llanerita/public/.well-known/assetlinks.json
   ```

2. **Contenido**:
   ```json
   [{
     "relation": ["delegate_permission/common.handle_all_urls"],
     "target": {
       "namespace": "android_app",
       "package_name": "com.arepallanerita.app",
       "sha256_cert_fingerprints": [
         "TU_SHA256_AQUI"
       ]
     }
   }]
   ```

3. **Commit y Push**:
   ```bash
   git add public/.well-known/assetlinks.json
   git commit -m "Add assetlinks.json"
   git push
   ```

4. **Verificar**:
   - Espera que Vercel redeploy (automático)
   - Visita: `https://tu-app.vercel.app/.well-known/assetlinks.json`
   - Debe mostrar el contenido del archivo

---

## 🌐 Opción 2: Railway (Mejor para Laravel)

Railway es ideal para apps Laravel con MongoDB.

### Paso 1: Crear Cuenta en Railway

```bash
# 1. Ve a Railway
https://railway.app

# 2. Sign Up con GitHub
- Click en "Login with GitHub"
- Autoriza Railway

# 3. Obtener $5 gratis
- Verifica tu email
- Agrega método de pago (no se cobra, solo para verificación)
```

### Paso 2: Deploy desde GitHub

```bash
# 1. Click en "New Project"
# 2. Selecciona "Deploy from GitHub repo"
# 3. Busca: red-de-ventas-arepa-llanerita
# 4. Click en "Deploy Now"
```

### Paso 3: Configurar Variables

```bash
# En el dashboard de Railway:
# Settings → Variables

APP_NAME=Arepa la Llanerita
APP_ENV=production
APP_KEY=base64:TU_KEY
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

DB_CONNECTION=mongodb
MONGODB_HOST=${{MongoDB.MONGO_URL}}
MONGODB_DATABASE=arepa_llanerita

MONEDA=COP
```

### Paso 4: Agregar MongoDB

```bash
# 1. En tu proyecto Railway
# 2. Click en "New" → "Database" → "Add MongoDB"
# 3. Railway conectará automáticamente
```

### Paso 5: Configurar Dominio

```bash
# 1. Settings → Domains
# 2. Click en "Generate Domain"
# 3. Te dará: tu-app.up.railway.app
```

### Paso 6: Generar APK

```bash
# Usa PWABuilder o Bubblewrap con:
https://tu-app.up.railway.app
```

---

## 🔧 Opción 3: APK Sin TWA (Sin Dominio)

Si NO quieres usar hosting, puedes crear una APK "local" usando otras tecnologías:

### A. Usar Capacitor (Recomendado)

Capacitor convierte tu web app en app nativa real.

```bash
# 1. Instalar Capacitor
npm install -g @capacitor/cli @capacitor/core @capacitor/android

# 2. Navegar a tu proyecto
cd arepa-llanerita

# 3. Inicializar Capacitor
npx cap init "Arepa la Llanerita" com.arepallanerita.app

# 4. Agregar plataforma Android
npx cap add android

# 5. Copiar tu app web
npm run build
npx cap copy

# 6. Abrir en Android Studio
npx cap open android

# 7. Generar APK desde Android Studio
# Build → Build Bundle(s) / APK(s) → Build APK(s)
```

**Configuración `capacitor.config.json`:**
```json
{
  "appId": "com.arepallanerita.app",
  "appName": "Arepa la Llanerita",
  "webDir": "public",
  "server": {
    "url": "http://localhost:8000",
    "cleartext": true
  }
}
```

**Ventajas de Capacitor:**
- ✅ No necesita dominio
- ✅ App nativa completa
- ✅ Acceso a todas las APIs nativas
- ✅ Funciona offline completamente
- ✅ Publicable en Play Store

**Desventajas:**
- ⚠️ Requiere Android Studio
- ⚠️ Más complejo de configurar
- ⚠️ Backend debe estar corriendo en servidor

### B. Usar Cordova (Alternativa)

Similar a Capacitor pero más antiguo:

```bash
# 1. Instalar Cordova
npm install -g cordova

# 2. Crear proyecto
cordova create ArepaLlanerita com.arepallanerita.app ArepaLlanerita

# 3. Agregar plataforma
cd ArepaLlanerita
cordova platform add android

# 4. Copiar tu web app
cp -r ../arepa-llanerita/public/* www/

# 5. Generar APK
cordova build android --release
```

---

## 🧪 Opción 4: Túneles para Testing

Para probar temporalmente sin dominio, usa túneles:

### A. ngrok (5 minutos de setup)

```bash
# 1. Descargar ngrok
https://ngrok.com/download

# 2. Instalar y autenticar
ngrok config add-authtoken TU_TOKEN

# 3. Iniciar tu servidor Laravel
cd arepa-llanerita
php artisan serve

# 4. En otra terminal, crear túnel
ngrok http 8000

# 5. Obtendrás una URL tipo:
https://abc123.ngrok.io

# 6. Usa esta URL para generar APK de prueba
```

**⚠️ Limitaciones de ngrok:**
- URL cambia cada vez que reinicias
- Gratis: 1 proceso simultáneo
- No apto para producción
- Solo para testing

### B. LocalTunnel

```bash
# 1. Instalar
npm install -g localtunnel

# 2. Iniciar servidor Laravel
php artisan serve

# 3. Crear túnel
lt --port 8000 --subdomain arepallanerita

# 4. URL: https://arepallanerita.loca.lt
```

### C. Serveo (Sin instalación)

```bash
# Crear túnel SSH
ssh -R 80:localhost:8000 serveo.net

# Te dará una URL como:
# https://example.serveo.net
```

---

## 📊 Comparación de Opciones

| Opción | Costo | HTTPS | Play Store | Dificultad | Recomendado |
|--------|-------|-------|------------|------------|-------------|
| **Vercel** | Gratis | ✅ | ✅ | ⭐ Fácil | ⭐⭐⭐⭐⭐ |
| **Railway** | Gratis* | ✅ | ✅ | ⭐⭐ Media | ⭐⭐⭐⭐ |
| **Netlify** | Gratis | ✅ | ✅ | ⭐ Fácil | ⭐⭐⭐⭐ |
| **Capacitor** | Gratis | ❌ | ✅ | ⭐⭐⭐⭐ Difícil | ⭐⭐⭐ |
| **ngrok** | Gratis* | ✅ | ❌ | ⭐ Fácil | ⭐⭐ (solo test) |

*Railway: $5/mes de crédito gratis, Netlify: 100GB/mes, ngrok: limitaciones en plan gratis

---

## 🎯 Mi Recomendación Personal

### Para Producción (Play Store):
```
1º Vercel (Laravel + MongoDB Atlas)
   ✅ Más fácil
   ✅ 100% gratis
   ✅ HTTPS automático
   ✅ Deploy en 5 minutos

2º Railway
   ✅ Mejor para Laravel
   ✅ MongoDB incluido
   ✅ $5/mes gratis

3º Capacitor (solo si tienes experiencia Android)
   ✅ App nativa completa
   ⚠️ Requiere backend separado
```

### Para Testing:
```
1º ngrok
   ✅ Más rápido (2 minutos)
   ✅ No requiere cuenta
   ⚠️ Solo temporal
```

---

## 🚀 Plan de Acción Recomendado

### Si NO tienes dominio y quieres publicar YA:

**DÍA 1 (2 horas):**
1. ✅ Crear cuenta en Vercel (5 min)
2. ✅ Crear cuenta en MongoDB Atlas (5 min)
3. ✅ Subir proyecto a GitHub (10 min)
4. ✅ Deploy en Vercel (30 min)
5. ✅ Configurar MongoDB (30 min)
6. ✅ Probar app funcionando (20 min)

**DÍA 2 (1 hora):**
1. ✅ Generar APK con PWABuilder (15 min)
2. ✅ Configurar assetlinks.json (15 min)
3. ✅ Probar APK en celular (30 min)

**DÍA 3 (30 min):**
1. ✅ Crear cuenta Play Store ($25 USD)
2. ✅ Subir AAB
3. ✅ Completar información
4. ✅ Enviar para revisión

**TOTAL: 3-4 horas de trabajo + 1-7 días de revisión**

---

## 📝 Resumen Ejecutivo

### ¿Puedes generar APK sin dominio?

**SÍ, tienes 2 caminos:**

#### Camino A: Hosting Gratuito (RECOMENDADO)
```
✅ 100% Gratis
✅ Listo en 2 horas
✅ HTTPS incluido
✅ Subdominio incluido
✅ Funciona para Play Store
✅ Fácil de mantener

Servicios: Vercel, Railway, Netlify
```

#### Camino B: App Nativa Local
```
✅ Sin hosting necesario
✅ App nativa completa
⚠️ Más complejo
⚠️ Requiere Android Studio
⚠️ Backend separado necesario

Herramienta: Capacitor
```

---

## 🆘 Necesitas Ayuda?

### Opción Más Rápida:
1. Usa **Vercel** + **MongoDB Atlas** (ambos gratis)
2. Sigue el tutorial de arriba paso a paso
3. En 2 horas tendrás tu app online con HTTPS
4. Genera APK con PWABuilder (5 minutos)
5. ¡Sube a Play Store!

### Si Te Atoras:
- Vercel tiene excelente documentación
- MongoDB Atlas tiene tutoriales paso a paso
- PWABuilder es automático (solo pones la URL)

**¡Tu app YA está lista, solo necesitas hospedarla!**

---

**💪 Puedes hacerlo!**
