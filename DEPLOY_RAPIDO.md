# 🚀 Deploy Rápido - 15 Minutos a Producción

## ⚡ Opción Express: Vercel (La Más Rápida)

### ⏱️ Tiempo Total: 15 minutos

---

## 📋 Requisitos Previos (2 min)

- [ ] Cuenta de GitHub (gratis)
- [ ] Cuenta de MongoDB Atlas (gratis)
- [ ] Git instalado en tu computadora

---

## 🎬 Paso a Paso Ultra Rápido

### 1️⃣ MongoDB Atlas (3 minutos)

```bash
# 1. Ve a: https://www.mongodb.com/cloud/atlas/register
# 2. Registrate con Google (más rápido)
# 3. Click en "Build a Database" → "FREE" (M0)
# 4. Región: São Paulo (más cerca de Colombia)
# 5. Click "Create"

# 6. Configurar acceso:
Username: admin_arepa
Password: [GUARDA ESTA CONTRASEÑA SEGURA]

# 7. Network Access:
IP: 0.0.0.0/0 (Permitir desde cualquier lugar)

# 8. Obtener connection string:
# Click "Connect" → "Connect your application"
# Copia algo como:
mongodb+srv://admin_arepa:TU_PASSWORD@cluster0.xxxxx.mongodb.net/
```

**🎯 GUARDA ESTO:**
```
MONGODB_CONNECTION_STRING=mongodb+srv://admin_arepa:TU_PASSWORD@cluster0.xxxxx.mongodb.net/arepa_llanerita
```

---

### 2️⃣ Subir a GitHub (3 minutos)

```bash
# En tu terminal, navega al proyecto
cd C:\Users\luKsha\Documents\GitHub\Red_de_Ventas_Proyecto_Final

# Si NO has inicializado Git:
git init
git add .
git commit -m "First commit"

# Crear repo en GitHub:
# 1. Ve a: https://github.com/new
# 2. Nombre: arepa-llanerita
# 3. Public o Private (tu decides)
# 4. NO agregues README, .gitignore ni licencia
# 5. Click "Create repository"

# Conectar y subir:
git remote add origin https://github.com/TU_USUARIO/arepa-llanerita.git
git branch -M main
git push -u origin main
```

---

### 3️⃣ Deploy en Vercel (5 minutos)

```bash
# 1. Ve a: https://vercel.com/signup
# 2. Click "Continue with GitHub"
# 3. Autoriza Vercel

# 4. Import Project:
# - Click "Add New..." → "Project"
# - Busca: arepa-llanerita
# - Click "Import"

# 5. Configurar:
Framework Preset: Other
Root Directory: arepa-llanerita
Build Command: [dejar vacío]
Output Directory: public
```

**6. Variables de Entorno (COPIAR Y PEGAR):**

```env
APP_NAME=Arepa la Llanerita
APP_ENV=production
APP_DEBUG=false
APP_URL=

DB_CONNECTION=mongodb
MONGODB_CONNECTION_STRING=mongodb+srv://admin_arepa:TU_PASSWORD@cluster0.xxxxx.mongodb.net/arepa_llanerita

SESSION_DRIVER=cookie
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MONEDA=COP
COMISION_VENDEDOR=10
COMISION_LIDER=5
BONO_REFERIDO=50000
```

**⚠️ IMPORTANTE: Genera tu APP_KEY:**
```bash
# En tu terminal local:
cd arepa-llanerita
php artisan key:generate --show

# Copia el resultado (será algo como):
# base64:XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx

# Agrega esta variable en Vercel:
APP_KEY=base64:XxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx
```

**7. Deploy:**
```bash
# Click en "Deploy"
# Espera 2-3 minutos
# ¡Listo!
```

---

### 4️⃣ Obtener tu URL (1 minuto)

```bash
# Vercel te dará una URL como:
https://arepa-llanerita-xxxxx.vercel.app

# Copia esta URL
```

**Actualizar APP_URL en Vercel:**
```bash
# 1. Ve a Settings → Environment Variables
# 2. Encuentra APP_URL
# 3. Actualiza con tu URL de Vercel
# 4. Click "Save"
# 5. Deployments → Click en el último → "Redeploy"
```

---

### 5️⃣ Inicializar Base de Datos (2 minutos)

```bash
# Una vez que tu app esté online, visita:
https://tu-app.vercel.app

# Deberías ver un error de "APP_KEY not set" o similar
# Esto es normal en el primer deploy

# Para inicializar la BD, puedes:
# Opción A: Conectarte a MongoDB Atlas y crear las colecciones manualmente
# Opción B: Usar un script de inicialización

# Por ahora, visita:
https://tu-app.vercel.app/login

# Si ves la página de login, ¡felicidades!
```

---

### 6️⃣ Generar APK (1 minuto)

```bash
# 1. Ve a: https://www.pwabuilder.com
# 2. Pega tu URL: https://tu-app.vercel.app
# 3. Click "Start"
# 4. Espera validación (30 seg)
# 5. Click "Package For Stores" → "Android"
# 6. Configurar:

Package ID: com.arepallanerita.app
App name: Arepa la Llanerita
Launcher name: Arepa Llanerita
Theme color: #8B1538
Background color: #FFFFFF
Start URL: /
Display mode: Standalone
Orientation: Portrait

# 7. Click "Generate"
# 8. Descargar el .zip
# 9. Extraer y encontrarás:
#    - app-release-signed.apk (para testing)
#    - app-release-bundle.aab (para Play Store)
```

---

## ✅ ¡Listo! Ya Tienes:

- ✅ App desplegada en producción con HTTPS
- ✅ URL: `https://tu-app.vercel.app`
- ✅ APK lista para instalar
- ✅ AAB lista para Play Store

---

## 🧪 Probar tu APK

```bash
# 1. Transfiere app-release-signed.apk a tu Android
# 2. Habilita "Instalar de fuentes desconocidas"
# 3. Instala el APK
# 4. ¡Prueba tu app!
```

---

## 📤 Subir a Play Store

### Requisitos:
- Cuenta Google Play Developer ($25 USD único)
- app-release-bundle.aab
- Screenshots (capturas de pantalla)
- Descripción de la app

### Pasos:
```bash
# 1. Ve a: https://play.google.com/console
# 2. Crea cuenta ($25)
# 3. "Crear aplicación"
# 4. Nombre: Arepa la Llanerita
# 5. Sube el AAB
# 6. Completa información
# 7. Envía para revisión
# 8. Espera 1-7 días
# 9. ¡Publicada! 🎉
```

---

## 🔄 Actualizar tu App

### Actualizar Código:

```bash
# 1. Haz cambios en tu código local
git add .
git commit -m "Descripción de cambios"
git push

# 2. Vercel detecta el push y redeploys automáticamente
# 3. En 2 minutos, tu app está actualizada
# 4. Los usuarios con la APK verán cambios automáticamente
```

### Actualizar APK (solo si cambian iconos o config):

```bash
# 1. Genera nueva APK/AAB con PWABuilder
# 2. Incrementa versión (1.0.1, 1.0.2, etc.)
# 3. Sube a Play Store
```

---

## 🐛 Troubleshooting Rápido

### Error: "500 Internal Server Error"
```bash
# Verifica variables de entorno en Vercel
# Especialmente: APP_KEY y MONGODB_CONNECTION_STRING
```

### Error: "Connection refused" MongoDB
```bash
# Verifica:
# 1. IP 0.0.0.0/0 permitida en MongoDB Atlas
# 2. Connection string correcta
# 3. Password sin caracteres especiales problemáticos
```

### APK no instala
```bash
# 1. Verifica que sea la versión "signed"
# 2. Habilita "Fuentes desconocidas" en Android
# 3. Prueba en otro dispositivo
```

### App se abre en navegador en vez de standalone
```bash
# Falta configurar assetlinks.json
# Ver archivo: GENERAR_APK_SIN_DOMINIO.md
# Sección: "Configurar assetlinks.json"
```

---

## 💡 Consejos Pro

### Para Mejor Rendimiento:
```bash
# 1. Activa caché en Vercel (automático)
# 2. Usa CDN para imágenes
# 3. Minifica CSS/JS con Vite
```

### Para Mejor SEO:
```bash
# 1. Agrega meta descriptions en views
# 2. Configura Open Graph tags
# 3. Crea sitemap.xml
```

### Para Debugging:
```bash
# Ver logs en Vercel:
# 1. Ve a tu proyecto
# 2. Click en "Deployments"
# 3. Click en el último deployment
# 4. Tab "Logs"
```

---

## 📊 Checklist Final

- [ ] MongoDB Atlas configurado
- [ ] Proyecto en GitHub
- [ ] Desplegado en Vercel
- [ ] URL funcionando con HTTPS
- [ ] Variables de entorno configuradas
- [ ] Login funciona
- [ ] APK generada con PWABuilder
- [ ] APK probada en dispositivo real
- [ ] assetlinks.json configurado (opcional)
- [ ] AAB lista para Play Store

---

## 🎯 Resumen Ultra Rápido

```bash
# 15 minutos para tener tu app en producción:

1. MongoDB Atlas (3 min) → Base de datos gratis
2. GitHub (3 min) → Subir código
3. Vercel (5 min) → Deploy automático
4. URL (1 min) → Obtener link HTTPS
5. Base Datos (2 min) → Inicializar
6. APK (1 min) → Generar con PWABuilder

TOTAL: 15 minutos
COSTO: $0.00 (100% gratis)
RESULTADO: App en producción + APK lista
```

---

## 🆘 ¿Problemas?

Si algo no funciona:

1. **Revisa los logs de Vercel**
2. **Verifica todas las variables de entorno**
3. **Asegúrate que MongoDB acepte conexiones de 0.0.0.0/0**
4. **Regenera APP_KEY si es necesario**

---

## 🚀 ¡Ya Está!

Tu app ahora está:
- ✅ En producción con HTTPS
- ✅ Accesible desde cualquier lugar
- ✅ Lista como APK instalable
- ✅ Preparada para Play Store

**¡Felicidades! 🎉**

---

**Siguiente paso recomendado:**
Lee `GENERAR_APK_PLAYSTORE.md` para publicar en Google Play Store.
