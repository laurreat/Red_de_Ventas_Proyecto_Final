# 📱 Guía para Generar APK y Publicar en Google Play Store

## 🎯 Resumen Ejecutivo

Esta guía te permite convertir la PWA de **Arepa la Llanerita** en una aplicación Android nativa (APK/AAB) que puedes publicar en Google Play Store usando **TWA (Trusted Web Activity)**.

### ✨ Ventajas de este Método:
- ✅ No necesitas reescribir código en Java/Kotlin
- ✅ Tu PWA existente se convierte en app nativa
- ✅ Actualizaciones instantáneas (solo actualizas el sitio web)
- ✅ Usa Chrome como motor (mejor rendimiento)
- ✅ Acceso completo a APIs web modernas
- ✅ Publicable en Google Play Store

---

## 📋 Requisitos Previos

### 1. Software Necesario
```bash
# Instalar Node.js (si no lo tienes)
# Descargar desde: https://nodejs.org/

# Instalar Java JDK 11 o superior
# Descargar desde: https://www.oracle.com/java/technologies/downloads/

# Instalar Android Studio (opcional pero recomendado)
# Descargar desde: https://developer.android.com/studio
```

### 2. Verificar Instalaciones
```bash
node --version    # Debe ser v14+
npm --version
java -version     # Debe ser 11+
```

### 3. Dominio con HTTPS
- ⚠️ **IMPORTANTE**: Tu PWA debe estar en un dominio con HTTPS
- No funcionará con `http://` o `localhost` para producción
- Ejemplo: `https://arepallanerita.com`

---

## 🚀 Método 1: Usando Bubblewrap (Recomendado)

### Paso 1: Instalar Bubblewrap CLI

```bash
# Instalar globalmente
npm install -g @bubblewrap/cli

# Verificar instalación
bubblewrap --version
```

### Paso 2: Inicializar Proyecto

```bash
# Navegar a la carpeta del proyecto
cd C:\Users\luKsha\Documents\GitHub\Red_de_Ventas_Proyecto_Final

# Crear carpeta para la app Android
mkdir android-app
cd android-app

# Inicializar Bubblewrap (responder las preguntas)
bubblewrap init --manifest https://tu-dominio.com/manifest.json
```

### Paso 3: Configuración Interactiva

Bubblewrap te preguntará:

```
Domain being opened in the TWA: https://arepallanerita.com
URL path being opened in the TWA: /
Name of the application: Arepa la Llanerita
Short name: Arepa Llanerita
Application ID: com.arepallanerita.app
Fallback behavior: Custom Tabs
Orientation: Portrait
Display mode: Standalone
Status bar color: #8B1538
Navigation bar color: #FFFFFF
Background color: #FFFFFF
Theme color: #8B1538
Icon URL: https://arepallanerita.com/images/icons/icon-512x512.png
Maskable Icon URL: https://arepallanerita.com/images/icons/icon-512x512-maskable.png
Start URL: /
Web Manifest URL: https://arepallanerita.com/manifest.json
```

### Paso 4: Generar APK

```bash
# Generar APK para testing
bubblewrap build

# El APK estará en: ./app-release-signed.apk
```

### Paso 5: Generar AAB para Play Store

```bash
# Generar Android App Bundle (requerido por Play Store)
bubblewrap build --skipPwaValidation

# El AAB estará en: ./app-release-bundle.aab
```

---

## 🔧 Método 2: Usando PWABuilder (Más Fácil)

### Opción Visual sin Código

1. **Ir a PWABuilder**
   - Visita: https://www.pwabuilder.com

2. **Ingresar tu URL**
   - Pega: `https://tu-dominio.com`
   - Click en "Start"

3. **Validar PWA**
   - PWABuilder verificará tu manifest.json y service worker
   - Te mostrará una puntuación y qué mejorar

4. **Generar Paquete Android**
   - Click en "Publish to Store"
   - Selecciona "Android"
   - Descarga el paquete `.zip`

5. **Configurar Detalles**
   - Package ID: `com.arepallanerita.app`
   - App Name: `Arepa la Llanerita`
   - Version: `1.0.0`
   - Host: `arepallanerita.com`

6. **Descargar**
   - Obtendrás un `.zip` con:
     - `app-release-signed.apk` (para testing)
     - `app-release-bundle.aab` (para Play Store)
     - Instrucciones de firma

---

## 🔑 Firma Digital (Keystore)

### Crear Keystore para Firma

```bash
# Generar keystore (guarda la contraseña en lugar seguro!)
keytool -genkey -v -keystore arepa-llanerita.keystore -alias arepa-key -keyalg RSA -keysize 2048 -validity 10000

# Te preguntará:
# - Contraseña del keystore (mínimo 6 caracteres)
# - Nombre y apellido
# - Unidad organizativa
# - Organización: Arepa la Llanerita
# - Ciudad
# - Estado/Provincia
# - Código de país: CO
```

### Firmar APK Manualmente

```bash
# Si necesitas firmar un APK sin firma
jarsigner -verbose -sigalg SHA256withRSA -digestalg SHA-256 -keystore arepa-llanerita.keystore app-release-unsigned.apk arepa-key

# Alinear el APK
zipalign -v 4 app-release-unsigned.apk app-release-signed.apk
```

---

## 📦 Preparar Assets para Play Store

### Assets Requeridos

Crea estos archivos en `arepa-llanerita/play-store-assets/`:

#### 1. Icono de App
- **📁 Archivo**: `icon-512x512.png`
- **Tamaño**: 512x512 px
- **Formato**: PNG con transparencia
- **Ya tienes**: `/images/icons/icon-512x512.png`

#### 2. Feature Graphic (Banner)
- **📁 Archivo**: `feature-graphic.png`
- **Tamaño**: 1024x500 px
- **Formato**: PNG o JPG
- **Contenido**: Logo + texto promocional

#### 3. Screenshots (Capturas)
Necesitas mínimo 2 screenshots:

**Para Teléfonos:**
- **Tamaño**: 1080x1920 px (o cualquier 16:9)
- **Cantidad**: Mínimo 2, máximo 8
- **Archivos**:
  - `screenshot-1-phone.png` (Dashboard)
  - `screenshot-2-phone.png` (Productos)
  - `screenshot-3-phone.png` (Pedidos)

**Para Tablets (opcional):**
- **Tamaño**: 1200x1920 px
- **Cantidad**: Mínimo 2

#### 4. Descripción de la App

**Título Corto** (máx 30 caracteres):
```
Arepa la Llanerita
```

**Descripción Corta** (máx 80 caracteres):
```
Sistema de ventas multinivel de arepas tradicionales colombianas
```

**Descripción Larga** (máx 4000 caracteres):
```
🌟 Arepa la Llanerita - Red de Ventas

Únete a la red de ventas más grande de arepas tradicionales colombianas amazónicas.
Sistema profesional de ventas multinivel (MLM) con comisiones atractivas y soporte completo.

✨ CARACTERÍSTICAS PRINCIPALES:

📊 Dashboard Intuitivo
- Visualiza tus ventas en tiempo real
- Estadísticas detalladas de rendimiento
- Seguimiento de metas y objetivos

💰 Sistema de Comisiones
- 10% de comisión por ventas directas
- 5% adicional como líder de equipo
- Bonos por referir nuevos vendedores
- Pagos puntuales y transparentes

👥 Red de Referidos (MLM)
- Construye tu equipo de vendedores
- Gana por las ventas de tu red
- Códigos de referido únicos
- Visualización de red multinivel

📦 Gestión de Pedidos
- Sistema fácil de usar
- Tracking en tiempo real
- Múltiples estados de pedido
- Notificaciones automáticas

🛍️ Catálogo de Productos
- Arepas tradicionales auténticas
- Productos especiales amazónicos
- Packs familiares
- Ingredientes 100% naturales

📱 Funciona Sin Internet
- Modo offline disponible
- Sincronización automática
- Acceso rápido desde pantalla de inicio

🎯 ¿POR QUÉ ELEGIRNOS?

✅ 30 años de experiencia
✅ Recetas tradicionales preservadas
✅ Control de calidad riguroso
✅ Producción artesanal
✅ Soporte 24/7
✅ Capacitación continua

💼 IDEAL PARA:

- Emprendedores que buscan ingresos adicionales
- Amantes de la gastronomía colombiana
- Personas que quieren trabajar desde casa
- Equipos de ventas organizados

📞 CONTACTO Y SOPORTE:

Estamos aquí para ayudarte en cada paso:
- WhatsApp: +57 315 431 1266
- Email: info@arepallanerita.com
- Soporte en tiempo real

🏆 BENEFICIOS EXCLUSIVOS:

- Registro 100% gratuito
- Sin costos ocultos
- Comisiones competitivas
- Bonificaciones por rendimiento
- Acceso a promociones exclusivas
- Comunidad de vendedores activa

Descarga ahora y comienza a generar ingresos compartiendo la tradición
culinaria colombiana amazónica.

#VentasMultinivel #MLM #ArepasColombinas #Emprendimiento #TrabajoDesdeCase
```

---

## 🎨 Generar Assets Faltantes

### Crear Feature Graphic

Puedes usar Canva o Photoshop con estas especificaciones:

```
Tamaño: 1024x500 px
Fondo: Gradiente #8B1538 a #6B1028
Logo: Centrado
Texto: "Sistema de Ventas Multinivel"
Fuente: Poppins Bold
```

### Tomar Screenshots

1. **Abrir tu app en Chrome DevTools**
2. **Activar modo responsive** (F12 → Toggle Device Toolbar)
3. **Configurar**:
   - Dispositivo: Pixel 5 (1080x2340)
   - Zoom: 100%
4. **Navegar** a cada sección importante
5. **Capturar** (Ctrl+Shift+P → "Capture screenshot")

---

## 📤 Publicar en Google Play Store

### Paso 1: Crear Cuenta de Desarrollador

1. **Ir a**: https://play.google.com/console
2. **Crear cuenta** ($25 USD pago único)
3. **Completar perfil** de desarrollador

### Paso 2: Crear Nueva Aplicación

1. **Click** en "Crear aplicación"
2. **Nombre**: Arepa la Llanerita
3. **Idioma predeterminado**: Español (España)
4. **Tipo**: Aplicación
5. **Gratis o de pago**: Gratis

### Paso 3: Completar Información

#### Panel Principal
- **Categoría**: Negocios
- **Clasificación de contenido**: Todos
- **Datos de contacto**: info@arepallanerita.com

#### Presencia en Play Store
- **Icono**: icon-512x512.png
- **Feature Graphic**: feature-graphic.png
- **Capturas**: screenshots-phone-*.png
- **Descripción**: (usar la descripción larga de arriba)

#### Lanzamiento de Producción
1. **Subir AAB**: app-release-bundle.aab
2. **Versión**: 1.0.0 (código: 1)
3. **Notas de la versión**: "Lanzamiento inicial"

### Paso 4: Configuración Digital Asset Links

**⚠️ MUY IMPORTANTE** para TWA:

1. **Crear archivo** en tu servidor web:
   - Ruta: `https://tu-dominio.com/.well-known/assetlinks.json`

2. **Contenido del archivo**:
```json
[{
  "relation": ["delegate_permission/common.handle_all_urls"],
  "target": {
    "namespace": "android_app",
    "package_name": "com.arepallanerita.app",
    "sha256_cert_fingerprints": [
      "TU_SHA256_FINGERPRINT_AQUI"
    ]
  }
}]
```

3. **Obtener SHA256 Fingerprint**:
```bash
keytool -list -v -keystore arepa-llanerita.keystore -alias arepa-key
```

### Paso 5: Revisión y Publicación

1. **Revisar** toda la información
2. **Enviar** para revisión
3. **Esperar** aprobación (1-7 días)
4. **¡Publicado!** 🎉

---

## 🔄 Actualizar la App

### Ventaja de TWA

¡No necesitas publicar nueva versión para cada cambio!

**Cambios que se actualizan automáticamente:**
- ✅ Contenido del sitio web
- ✅ Estilos CSS
- ✅ JavaScript
- ✅ Imágenes
- ✅ Funcionalidades nuevas

**Solo necesitas nueva APK/AAB si cambias:**
- ⚠️ Icono de la app
- ⚠️ Nombre de la app
- ⚠️ Permisos de Android
- ⚠️ Splash screen
- ⚠️ Configuración TWA

### Proceso de Actualización

```bash
# 1. Incrementar versión en twa-manifest.json
# versionCode: 2
# versionName: "1.0.1"

# 2. Regenerar AAB
bubblewrap build

# 3. Subir a Play Console
# - Ir a "Lanzamientos" → "Producción"
# - "Crear nuevo lanzamiento"
# - Subir nuevo AAB
# - Publicar
```

---

## 🧪 Probar la APK Antes de Publicar

### En Dispositivo Real

```bash
# 1. Habilitar "Fuentes desconocidas" en tu Android
# Configuración → Seguridad → Fuentes desconocidas

# 2. Transferir APK al teléfono
# Via USB, email, o Google Drive

# 3. Instalar
# Toca el archivo .apk → Instalar
```

### Usando Android Studio

```bash
# 1. Conectar dispositivo Android vía USB
adb devices

# 2. Instalar APK
adb install app-release-signed.apk

# 3. Ver logs en tiempo real
adb logcat | grep Chromium
```

---

## 📊 Checklist Completo

### Antes de Generar APK
- [ ] PWA funcionando en HTTPS
- [ ] manifest.json completo y válido
- [ ] Service Worker registrado
- [ ] Iconos 192x192 y 512x512
- [ ] start_url configurada
- [ ] theme_color definido

### Para Generar APK
- [ ] Node.js instalado
- [ ] Java JDK instalado
- [ ] Bubblewrap CLI instalado
- [ ] Dominio con HTTPS activo

### Para Play Store
- [ ] Cuenta de desarrollador creada ($25)
- [ ] Keystore generado y guardado
- [ ] Feature Graphic (1024x500)
- [ ] Screenshots (mínimo 2)
- [ ] Descripción escrita
- [ ] assetlinks.json configurado
- [ ] Política de privacidad publicada

---

## 🆘 Solución de Problemas

### Error: "PWA validation failed"

```bash
# Verificar manifest y SW con Lighthouse
# Chrome DevTools → Lighthouse → Progressive Web App
```

### Error: "Invalid keystore"

```bash
# Regenerar keystore
keytool -genkey -v -keystore new-keystore.keystore -alias new-key -keyalg RSA -keysize 2048 -validity 10000
```

### Error: "Digital Asset Links verification failed"

1. Verificar que `assetlinks.json` esté en `/.well-known/`
2. Verificar que sea accesible vía HTTPS
3. Verificar que el SHA256 fingerprint sea correcto
4. Esperar hasta 24 horas para propagación

### App se abre en Chrome en vez de standalone

- Verificar assetlinks.json
- Verificar firma del APK
- Reinstalar la app
- Limpiar caché de Chrome

---

## 📚 Recursos Adicionales

- **Bubblewrap Docs**: https://github.com/GoogleChromeLabs/bubblewrap
- **TWA Guide**: https://developer.chrome.com/docs/android/trusted-web-activity/
- **PWABuilder**: https://www.pwabuilder.com
- **Play Console**: https://play.google.com/console
- **Digital Asset Links**: https://developers.google.com/digital-asset-links

---

## 💡 Consejos Pro

1. **Versión Beta**: Publica primero en canal "Beta" para testing
2. **Feedback**: Usa Google Play Console para ver reseñas
3. **Analytics**: Integra Google Analytics para seguimiento
4. **Actualizaciones**: Comunica cambios en "Notas de versión"
5. **ASO**: Optimiza descripción con palabras clave relevantes

---

## 🎯 Próximos Pasos Recomendados

1. ✅ Asegurar dominio con HTTPS
2. ✅ Crear cuenta Google Play Developer
3. ✅ Generar assets gráficos (Feature Graphic, Screenshots)
4. ✅ Instalar Bubblewrap y generar APK de prueba
5. ✅ Probar APK en dispositivo real
6. ✅ Configurar assetlinks.json
7. ✅ Generar AAB final
8. ✅ Subir a Play Store
9. ✅ Esperar aprobación
10. ✅ ¡Publicar! 🚀

---

**Desarrollado con ❤️ por Luis Alberto Urrea Trujillo**
**Contacto**: luis2005.320@gmail.com | +57 315 431 1266
