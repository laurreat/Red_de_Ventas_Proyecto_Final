# 📱 Método Más Fácil para Probar la PWA en tu Móvil

## Arepa la Llanerita - Guía Rápida y Portable

---

## 🎯 Método Seleccionado: **ngrok (HTTPS)**

**Por qué este método:**

- ✅ PWA 100% funcional
- ✅ Instalable en tu móvil
- ✅ Funciona desde cualquier lugar (no solo tu WiFi)
- ✅ HTTPS real (requerido para PWA)
- ✅ Gratis
- ✅ Sin configuración complicada
- ✅ Portable (funciona en cualquier red)

**Tiempo estimado:** 5 minutos

---

## 📋 Requisitos Previos

- [ ] Tu computadora con el proyecto
- [ ] Tu móvil (Android o iPhone)
- [ ] Conexión a internet (en ambos dispositivos)

---

## 🚀 PASO 1: Descargar ngrok

### Opción A: Descarga Directa (Recomendado)

1. **Abre tu navegador**
2. **Ve a:** <https://ngrok.com/download>

3. **Descarga ngrok para Windows:**
3. **Descarga ngrok para Windows:**
   - Click en el botón "Download for Windows"
   - Se descargará un archivo ZIP

4. **Extrae el archivo:**
   - Click derecho en el ZIP descargado
   - "Extraer aquí" o "Extract here"
   - Verás el archivo `ngrok.exe`

5. **Mueve ngrok.exe a una carpeta accesible:**
   - Recomendado: `C:\ngrok\ngrok.exe`
   - O déjalo en Descargas

### Opción B: Con Chocolatey (Si lo tienes instalado)

```bash
choco install ngrok
```

---

## 🔑 PASO 2: Crear Cuenta en ngrok (GRATIS)

1. **Ve a:** <https://dashboard.ngrok.com/signup>

2. **Regístrate:**
   - Usa tu email
   - Crea una contraseña
   - O usa "Sign up with Google/GitHub"

3. **Verifica tu email** (revisa tu bandeja de entrada)

4. **Copia tu Authtoken:**
   - Después de iniciar sesión, estarás en el dashboard
   - Verás una sección "Your Authtoken"
   - Click en "Copy" para copiar el token
   - Se ve algo así: `2abc123XYZ456def789GHI`

---

## ⚙️ PASO 3: Configurar ngrok (Solo una vez)

1. **Abre CMD o PowerShell:**
   - Presiona `Windows + R`
   - Escribe `cmd` y Enter

   (O la carpeta donde pusiste ngrok.exe)

3. **Configura tu authtoken:**
   bash
   ngrok config add-authtoken TU_TOKEN_AQUI
   ngrok config add-authtoken 2abc123XYZ456def789GHI

4. **Deberías ver:**
✅ **¡Listo! Nunca más tendrás que hacer esto**

---

## 🖥️ PASO 4: Iniciar el Servidor Laravel

2. **Navega al proyecto:**

bash
cd C:\Users\luKsha\Documents\GitHub\Red_de_Ventas_Proyecto_Final\arepa-llanerita

3. **Inicia el servidor:**

bash

php artisan serve

4. **Deberías ver:**
   ```
   Starting Laravel development server: http://127.0.0.1:8000
✅ **Deja esta terminal abierta y corriendo**

---

## 🌐 PASO 5: Iniciar ngrok
2. **Navega a la carpeta de ngrok:**
   ```bash
   cd C:\ngrok
   ```


4. **Deberías ver algo así:**
   ```
   ngrok

   Session Status                online
   Account                       tu_email@ejemplo.com (Plan: Free)
   Version                       3.x.x
   Region                        United States (us)
   Latency                       45ms
   Web Interface                 http://127.0.0.1:4040
   Forwarding                    https://abc123xyz.ngrok-free.app -> http://localhost:8000

   Connections                   ttl     opn     rt1     rt5     p50     p90
                                 0       0       0.00    0.00    0.00    0.00
   ```

5. **COPIA LA URL HTTPS:**
   - La línea que dice "Forwarding"
✅ **Esta es tu URL mágica que funcionará en cualquier dispositivo**

---

## 📱 PASO 6: Abrir en tu Móvil
- Guarda la URL en una nota
- Abre la nota en tu móvil
- Copia y pega en el navegador

**Opción D: Código QR** (más rápido)
- ngrok genera un código QR automáticamente
- Ve a: http://127.0.0.1:4040 en tu PC
- Escanea el QR con tu móvil

### 🌍 Abrir en el Navegador Móvil

1. **En tu móvil, abre el navegador:**
   - **Android:** Chrome
   - **iPhone:** Safari

2. **Pega o toca la URL**

3. **Espera a que cargue**
   - Puede aparecer una pantalla de ngrok primero
   - Click en "Visit Site" si aparece

4. **¡Deberías ver Arepa la Llanerita!** 🎉

---

## 📲 PASO 7: Instalar como PWA (Opcional pero Recomendado)

### 📱 En Android (Chrome):
**Opción A: WhatsApp**

- Envíate la URL a ti mismo
- Abre el chat en tu móvil
- Click en el link

**Opción B: Email**

- Envíate un email con la URL
- Abre el email en tu móvil
- Click en el link

**Opción C: Google Keep / Notas**

- Guarda la URL en una nota
- Abre la nota en tu móvil
- Copia y pega en el navegador

**Opción D: Código QR** (más rápido)

- ngrok genera un código QR automáticamente
- Ve a: <http://127.0.0.1:4040> en tu PC
- Escanea el QR con tu móvil

### 🍎 En iPhone (Safari):

1. **Toca el botón de compartir:**
   - Es el cuadrado con una flecha hacia arriba
   - Está en la parte inferior (medio) del navegador

2. **Scroll hacia abajo en el menú**

3. **Toca "Agregar a pantalla de inicio"**
   - Tiene un ícono de +

4. **Personaliza el nombre (opcional):**
   - Por defecto será "Arepa Llanerita"
   - Puedes cambiarlo si quieres
### 📱 En Android (Chrome)
5. **Toca "Agregar" arriba a la derecha**

6. **¡Listo!**
   - La app aparecerá en tu **pantalla de inicio**
   - Con el icono personalizado
   - Sin barra de Safari

---

## ✅ Verificar que Funciona

### Checklist Rápido:

- [ ] La página carga en tu móvil
- [ ] Los botones son fáciles de tocar (no muy pequeños)
- [ ] Las tablas se ven como tarjetas
- [ ] Los formularios no hacen zoom al tocar
- [ ] La navegación funciona bien
- [ ] (Si instalaste) El icono está en tu pantalla de inicio
- [ ] (Si instalaste) Se abre sin barra de navegador
### 🍎 En iPhone (Safari)
---

## 🎨 Cosas Cool para Probar

### 1. **Modo Offline** (Si instalaste la PWA)
   - Activa modo avión en tu móvil
   - Abre la app instalada
   - Deberías poder ver algunas páginas en cache

### 2. **Shortcuts** (Android)
   - Mantén presionado el icono de la app
   - Verás atajos rápidos:
     - Dashboard
     - Admin Panel
     - Productos
     - Pedidos

### 3. **Splash Screen** (Si instalaste)
   - Cierra la app completamente
   - Ábrela de nuevo
   - Verás una pantalla de carga con tu logo

### 4. **Navegación Táctil**
   - Prueba tocar todos los botones
   - Deberían ser fáciles de presionar
### Checklist Rápido

### 5. **Rotar Pantalla**
   - Gira tu móvil a horizontal
   - La app debería adaptarse bien

---

## 🐛 Solución de Problemas

### Problema: "No puedo acceder a la URL"

**Solución:**
1. ✅ Verifica que Laravel esté corriendo (paso 4)
### 1. **Modo Offline** (Si instalaste la PWA)

- Activa modo avión en tu móvil
- Abre la app instalada
- Deberías poder ver algunas páginas en cache

### 2. **Shortcuts** (Android)

- Mantén presionado el icono de la app
- Verás atajos rápidos:
  - Dashboard
  - Admin Panel
  - Productos
  - Pedidos

### 3. **Splash Screen** (Si instalaste)

- Cierra la app completamente
- Ábrela de nuevo
- Verás una pantalla de carga con tu logo

### 4. **Navegación Táctil**

- Prueba tocar todos los botones
- Deberían ser fáciles de presionar
- Área táctil mínima de 44x44px

### 5. **Rotar Pantalla**

- Gira tu móvil a horizontal
- La app debería adaptarse bien
3. ✅ Busca manualmente en el menú del navegador
4. ✅ Android: Menú → "Instalar app"
5. ✅ iOS: Botón compartir → "Agregar a pantalla de inicio"

### Problema: "ngrok me pide actualizar plan"

**Solución:**
- El plan gratuito tiene límite de **40 conexiones/minuto**
- Es más que suficiente para testing
- Si llegas al límite, espera 1 minuto

### Problema: "La sesión de ngrok expiró"

**Solución:**
- Las sesiones gratuitas duran **2 horas**
- Simplemente reinicia ngrok:
  ```bash
  # En la terminal de ngrok:
  Ctrl + C  (para detener)
  ngrok http 8000  (para reiniciar)
  ```
- Te dará una **nueva URL**

---

## 🎯 Resumen de Comandos

```bash
# Terminal 1 - Laravel (dejar corriendo)
cd C:\Users\luKsha\Documents\GitHub\Red_de_Ventas_Proyecto_Final\arepa-llanerita
php artisan serve

# Terminal 2 - ngrok (dejar corriendo)
cd C:\ngrok
ngrok http 8000
```

**Copiar la URL HTTPS → Abrir en móvil → ¡Disfrutar!**

---

## 📊 Ventajas de Este Método

| Característica | Red Local WiFi | ngrok HTTPS |
|----------------|----------------|-------------|
| **PWA Completa** | ❌ Limitada | ✅ 100% Funcional |
| **Instalable** | ❌ No | ✅ Sí |
| **Offline Support** | ⚠️ Parcial | ✅ Completo |
| **Push Notifications** | ❌ No | ✅ Sí (preparadas) |
| **Funciona fuera de WiFi** | ❌ No | ✅ Sí |
| **HTTPS Real** | ❌ No | ✅ Sí |
| **Splash Screen** | ❌ No | ✅ Sí |
| **Service Worker Completo** | ⚠️ Limitado | ✅ Completo |
| **Configuración** | Fácil | Muy Fácil |
| **Gratis** | ✅ Sí | ✅ Sí |

---

## 🎓 Próximos Pasos

### Después de Probar:

1. **Comparte la URL con tu equipo**
   - Cualquiera puede acceder desde su móvil
   - Ideal para demos

2. **Prueba en diferentes dispositivos**
   - Android de diferentes tamaños
   - iPhones de diferentes modelos
   - Tablets

3. **Recoge feedback**
   - ¿Los botones son fáciles de tocar?
   - ¿La navegación es intuitiva?
   - ¿Algo se ve mal?

4. **Cuando termines de probar:**
   ```bash
   # Detener Laravel (Terminal 1)
   Ctrl + C

   # Detener ngrok (Terminal 2)
   Ctrl + C
   ```

---

## 💡 Tips Pro

### 1. **Mantener la URL estable**
### Después de Probar

**Solución:**
- El plan **Personal de ngrok** ($8/mes) te da URLs estáticas
- Pero para testing, la URL dinámica está bien

### 2. **Ver tráfico en tiempo real**
ngrok incluye un inspector web:
```
http://127.0.0.1:4040
```
- Abre esto en tu navegador PC
- Verás todas las peticiones que llegan
- Útil para debugging

### 3. **Compartir con clientes**
La URL de ngrok funciona en **cualquier lugar del mundo**:
- Compártela con clientes para demos
- Compártela con testers
- Compártela con tu equipo

### 4. **Seguridad básica**
La URL es pública pero difícil de adivinar:
- `https://abc123xyz.ngrok-free.app`
- Aleatoria de 128 bits
- Prácticamente imposible de adivinar
- Pero no compartas la URL públicamente en internet

---
### 1. **Mantener la URL estable**

Con el plan gratuito, cada vez que reinicies ngrok obtendrás una URL diferente.

**Solución:**

- El plan **Personal de ngrok** ($8/mes) te da URLs estáticas
- Pero para testing, la URL dinámica está bien

### 2. **Ver tráfico en tiempo real**

ngrok incluye un inspector web:

**Contacto:**
- Email: luis2005.320@gmail.com
- Teléfono: +57 315 431 1266

---

## ✅ Checklist Final

Antes de probar en tu móvil, verifica:

- [ ] ngrok descargado y extraído
- [ ] Cuenta de ngrok creada
- [ ] Authtoken configurado
- [ ] Terminal 1: Laravel corriendo (`php artisan serve`)
- [ ] Terminal 2: ngrok corriendo (`ngrok http 8000`)
- [ ] URL HTTPS copiada
- [ ] Móvil con conexión a internet
- [ ] Navegador móvil abierto

---

## 🎉 ¡Listo!

Ahora tienes todo lo necesario para probar tu PWA en cualquier móvil, desde cualquier lugar.

**La URL de ngrok es portable:** funciona en tu casa, en la oficina, en un café, o donde sea que tengas internet.

**Contacto:**

- Email: <luis2005.320@gmail.com>
- Teléfono: +57 315 431 1266

*Generado con ❤️ por Claude Code - Equipo Arepa la Llanerita*
Generado con ❤️ por Claude Code - Equipo Arepa la Llanerita
