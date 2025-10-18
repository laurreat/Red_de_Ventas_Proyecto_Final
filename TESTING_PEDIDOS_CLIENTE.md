# 🧪 Guía de Testing - Módulo Pedidos Cliente

## 🚀 Preparación del Entorno

### 1. Limpiar Caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 2. Verificar Rutas
```bash
php artisan route:list --name=cliente.pedidos
```

**Rutas esperadas:**
- GET `/cliente/pedidos` → cliente.pedidos.index
- GET `/cliente/pedidos/create` → cliente.pedidos.create
- POST `/cliente/pedidos` → cliente.pedidos.store
- GET `/cliente/pedidos/{id}` → cliente.pedidos.show
- POST `/cliente/pedidos/{id}/cancelar` → cliente.pedidos.cancel

### 3. Verificar Base de Datos MongoDB
```bash
# En MongoDB Compass o mongosh
use arepa_llanerita_db
db.productos.countDocuments({activo: true, stock: {$gt: 0}})
db.pedidos.countDocuments()
```

---

## ✅ Test Cases

### TEST 1: Vista Index (Listado de Pedidos)

**URL:** `/cliente/pedidos`

**Pasos:**
1. Iniciar sesión como cliente
2. Navegar a "Mis Pedidos"
3. Verificar que se muestre el header con gradiente vino tinto
4. Verificar las 4 stats cards:
   - Total Pedidos
   - Pendientes
   - En Proceso
   - Entregados
5. Verificar filtros funcionales:
   - Búsqueda por número
   - Filtro por estado
   - Filtro por fecha
6. Verificar lista de pedidos con badges de estado
7. Verificar botones de acción (Ver, Cancelar, Rastrear)

**Resultado Esperado:**
- ✅ Header animado aparece suavemente
- ✅ Stats cards muestran números correctos
- ✅ Filtros funcionan sin recargar página completa
- ✅ Badges tienen colores correctos por estado
- ✅ Botones responden a hover
- ✅ Empty state si no hay pedidos

---

### TEST 2: Vista Create (Crear Pedido)

**URL:** `/cliente/pedidos/create`

**Pasos:**
1. Clic en "Nuevo Pedido"
2. Verificar que se carguen productos por categoría
3. Buscar un producto en el buscador
4. Seleccionar 2-3 productos diferentes
5. Ajustar cantidades con botones +/-
6. Verificar que el resumen se actualice en tiempo real
7. Llenar formulario de entrega:
   - Dirección válida
   - Teléfono válido
   - Método de pago
   - Notas (opcional)
8. Click en "Confirmar Pedido"

**Validaciones a Probar:**
- ❌ Intentar enviar sin productos → Error
- ❌ Intentar cantidad > stock → Error
- ❌ Dirección vacía → Error
- ❌ Teléfono vacío → Error
- ❌ Sin método de pago → Error
- ✅ Todo correcto → Redirección a Show

**Resultado Esperado:**
- ✅ Productos se cargan con imágenes
- ✅ Buscador filtra en tiempo real
- ✅ Cantidad se limita por stock
- ✅ Resumen muestra total correcto
- ✅ Validaciones muestran mensajes claros
- ✅ Loading overlay durante envío
- ✅ Toast de éxito al crear
- ✅ Redirección a detalle del pedido

---

### TEST 3: Vista Show (Detalle del Pedido)

**URL:** `/cliente/pedidos/{id}`

**Pasos:**
1. Entrar a un pedido desde la lista
2. Verificar header con número de pedido y estado
3. Verificar tabla de productos con:
   - Imágenes
   - Nombres
   - Cantidades
   - Precios
   - Subtotales
4. Verificar timeline de historial
5. Verificar información de entrega
6. Verificar datos del cliente
7. Verificar método de pago
8. Probar botón "Imprimir"
9. Si está pendiente/confirmado, probar "Cancelar"

**Resultado Esperado:**
- ✅ Todos los datos del pedido visibles
- ✅ Timeline muestra estados en orden
- ✅ Productos con imágenes correctas
- ✅ Totales calculados bien
- ✅ Botón cancelar solo si es permitido
- ✅ Modal de confirmación al cancelar
- ✅ Print styles al imprimir (sin botones)

---

### TEST 4: Cancelar Pedido

**Pasos:**
1. Crear un pedido nuevo (estado: pendiente)
2. Entrar al detalle
3. Click en "Cancelar Pedido"
4. Verificar modal de confirmación
5. Confirmar cancelación
6. Verificar:
   - Toast de éxito
   - Estado cambia a "cancelado"
   - Stock devuelto (verificar en productos)
   - Badge rojo en lista

**Edge Cases:**
- ❌ Intentar cancelar pedido entregado → No debe permitir
- ❌ Intentar cancelar pedido cancelado → No debe permitir
- ✅ Cancelar pendiente → OK
- ✅ Cancelar confirmado → OK

**Resultado Esperado:**
- ✅ Modal aparece con animación
- ✅ Confirmación requiere clic explícito
- ✅ Loading overlay durante proceso
- ✅ Toast muestra éxito
- ✅ Stock devuelto correctamente
- ✅ Historial registra cancelación

---

### TEST 5: Filtros y Búsqueda

**URL:** `/cliente/pedidos`

**Escenarios:**

**5.1 Filtro por Estado:**
- Seleccionar "Pendiente" → Solo pedidos pendientes
- Seleccionar "Entregado" → Solo entregados
- Seleccionar "Todos" → Todos los estados

**5.2 Filtro por Fecha:**
- Seleccionar fecha específica → Solo de ese día
- Limpiar filtro → Todos los días

**5.3 Búsqueda:**
- Buscar por número de pedido → Encuentra exacto
- Buscar por dirección → Encuentra coincidencias
- Buscar texto inexistente → Empty state

**5.4 Combinación:**
- Estado + Fecha + Búsqueda → Filtros AND

**Resultado Esperado:**
- ✅ Filtros se aplican correctamente
- ✅ URL refleja filtros (para compartir)
- ✅ Resultados correctos
- ✅ Empty state si no hay resultados
- ✅ Botón "Limpiar filtros" funciona

---

### TEST 6: Responsive Design

**Dispositivos a Probar:**
- 📱 Mobile (320px - 480px)
- 📱 Tablet (768px - 1024px)
- 💻 Desktop (>1024px)

**Elementos a Verificar:**

**Mobile (<768px):**
- ✅ Header stack verticalmente
- ✅ Stats cards 1 columna
- ✅ Tabla se convierte en cards
- ✅ Botones full-width
- ✅ Modal ocupa 95% ancho
- ✅ Toasts ocupan ancho completo

**Tablet:**
- ✅ Stats cards 2 columnas
- ✅ Tabla visible
- ✅ Sidebar apilado abajo

**Desktop:**
- ✅ Todo en grid normal
- ✅ Sidebar sticky
- ✅ Hover effects funcionan

---

### TEST 7: PWA (Offline)

**Pasos:**
1. Instalar PWA (si está disponible)
2. Navegar a pedidos con conexión
3. Desconectar red (modo avión)
4. Intentar navegar a pedidos guardados
5. Verificar service worker en DevTools

**Resultado Esperado:**
- ✅ PWA se instala correctamente
- ✅ Assets CSS/JS se cargan offline
- ✅ Páginas visitadas se muestran offline
- ✅ Mensaje claro si intenta crear sin conexión
- ✅ Sincronización al reconectar

---

### TEST 8: Seguridad

**8.1 XSS Protection:**
```javascript
// Intentar inyectar script en dirección
<script>alert('XSS')</script>
```
**Esperado:** Texto escapado, no ejecuta

**8.2 CSRF Protection:**
- Intentar POST sin token → Error 419
- Con token válido → Success

**8.3 Autorización:**
- Usuario A intenta ver pedido de Usuario B → Error 403/404
- Cliente intenta acceder `/admin/pedidos` → Redirect

**8.4 MongoDB Injection:**
```javascript
// Intentar inyectar en búsqueda
{"$gt": ""}
```
**Esperado:** Sanitizado, no ejecuta query

**8.5 Límite de Pedidos:**
- Crear 5 pedidos pendientes → OK
- Intentar crear 6to → Error "Demasiados pendientes"

---

### TEST 9: Performance

**Métricas a Medir (Chrome DevTools):**

**Index:**
- Time to First Byte (TTFB): <500ms
- First Contentful Paint (FCP): <1.5s
- Time to Interactive (TTI): <3s
- Largest Contentful Paint (LCP): <2.5s

**Create:**
- Carga de productos: <1s
- Respuesta de búsqueda: <100ms
- Actualización de carrito: <50ms

**Show:**
- Carga completa: <2s
- Imágenes lazy load: visible

**Herramientas:**
```bash
# Lighthouse
npm install -g lighthouse
lighthouse https://tu-dominio.com/cliente/pedidos --view

# PageSpeed Insights
# Visitar: https://pagespeed.web.dev/
```

---

### TEST 10: Validaciones de Formulario

**Create Form - Casos de Prueba:**

| Campo | Valor | Esperado |
|-------|-------|----------|
| Productos | [] | ❌ Error: "Selecciona productos" |
| Productos | [válido] | ✅ OK |
| Cantidad | 0 | ❌ Error: "Mínimo 1" |
| Cantidad | 101 | ❌ Error: "Máximo 100" |
| Cantidad | > stock | ❌ Error: "Stock insuficiente" |
| Dirección | "" | ❌ Error: "Campo requerido" |
| Dirección | "Calle 123" | ✅ OK |
| Teléfono | "" | ❌ Error: "Campo requerido" |
| Teléfono | "+57 300 123 4567" | ✅ OK |
| Método pago | "" | ❌ Error: "Selecciona método" |
| Método pago | "efectivo" | ✅ OK |
| Notas | 1001 caracteres | ❌ Error: "Máximo 1000" |

---

## 🐛 Errores Comunes y Soluciones

### Error: "Call to undefined method"
**Causa:** Modelo Pedido no tiene método
**Solución:** Verificar que el modelo tenga los métodos usados

### Error: "Collection productos not found"
**Causa:** MongoDB no tiene productos
**Solución:** 
```bash
php artisan db:seed --class=ProductosSeeder
```

### Error: 404 en rutas
**Causa:** Rutas no cargadas
**Solución:**
```bash
php artisan route:clear
php artisan optimize:clear
```

### Error: "CSRF token mismatch"
**Causa:** Token expirado
**Solución:** Refrescar página y volver a intentar

### Error: "Attempt to read property on null"
**Causa:** Relación no cargada
**Solución:** Verificar eager loading o null checks

---

## 📊 Checklist Final de Testing

### Funcionalidad
- [ ] Crear pedido con productos válidos
- [ ] Crear pedido con stock insuficiente (debe fallar)
- [ ] Ver listado de pedidos
- [ ] Ver detalle de un pedido
- [ ] Cancelar pedido pendiente
- [ ] Intentar cancelar pedido entregado (debe fallar)
- [ ] Filtrar por estado
- [ ] Filtrar por fecha
- [ ] Buscar por número de pedido
- [ ] Combinar filtros múltiples

### UI/UX
- [ ] Animaciones suaves sin lag
- [ ] Hover effects funcionan
- [ ] Badges colores correctos
- [ ] Modales centrados y animados
- [ ] Toasts aparecen y desaparecen
- [ ] Loading overlay durante operaciones
- [ ] Empty states cuando no hay datos

### Responsive
- [ ] Mobile 320px funciona
- [ ] Tablet 768px funciona
- [ ] Desktop 1920px funciona
- [ ] Touch-friendly en mobile
- [ ] Botones tamaño mínimo 44px

### Performance
- [ ] Index carga <3s
- [ ] Create carga <3s
- [ ] Show carga <2s
- [ ] Búsqueda responde <100ms
- [ ] Sin memory leaks

### Seguridad
- [ ] XSS no ejecuta
- [ ] CSRF protegido
- [ ] Solo propietario ve su pedido
- [ ] MongoDB injection bloqueado
- [ ] Límite pedidos pendientes funciona

### PWA
- [ ] Service worker activo
- [ ] Assets en cache
- [ ] Funciona offline (páginas visitadas)
- [ ] Manifest correcto
- [ ] Installable

---

## 🎯 Criterios de Aceptación

### ✅ El módulo está APROBADO si:
1. Todas las vistas cargan <3 segundos
2. Se pueden crear pedidos sin errores
3. Se pueden ver detalles completos
4. Se pueden cancelar pedidos permitidos
5. Filtros funcionan correctamente
6. Responsive funciona en 3 tamaños
7. No hay errores de consola JavaScript
8. No hay errores de MongoDB
9. Seguridad XSS/CSRF verificada
10. PWA funciona offline básico

### ❌ Rechazar si:
- Carga >5 segundos consistentemente
- Errores 500 en operaciones básicas
- XSS ejecuta código
- Pedidos se crean sin stock
- Layout roto en mobile
- Service worker no funciona

---

## 📝 Reporte de Bugs

### Template:
```markdown
## Bug: [Título descriptivo]

**Severidad:** Alta / Media / Baja

**Pasos para Reproducir:**
1. 
2. 
3. 

**Resultado Actual:**


**Resultado Esperado:**


**Evidencia:**
- Screenshot:
- Log:

**Entorno:**
- Navegador:
- Dispositivo:
- Usuario:
```

---

## ✅ Testing Completado

**Fecha:** _________________

**Tester:** _________________

**Resultado:** ✅ Aprobado / ❌ Rechazado

**Notas:** _________________
