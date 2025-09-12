# Próximos Pasos - Prompts para Claude

## 🎨 **1. CAMBIO DE COLORES CORPORATIVOS**
**Prompt:**
```
Lee el archivo CONTEXTO_PROYECTO.md para entender el estado actual del proyecto. Necesito cambiar los colores de la marca de naranja a los colores corporativos reales de Arepa la Llanerita: vino tinto (#722F37) como color primario y blanco (#FFFFFF) como color secundario. Actualiza todas las variables CSS en el layout principal y ajusta los colores en todos los dashboards para que se vean bien con la nueva paleta. También actualiza los gradientes y asegúrate de que el contraste sea adecuado para accesibilidad, y posteriormente verifica ¿por qué al abrir el servidor local con php artisan serve, la ip que suelta siempre arroja a dashboard y no al login?, ya que el dashboard presenta problemas, cualquier dashboard presenta problemas, posteriormente a esto, dime donde está cada cosa del front end y backend para tener yo mismo un control de esto y no solo la ia.
```

## 🏬 **2. MÓDULO DE INVENTARIO Y PRODUCTOS**
**Prompt:**
```
Basándote en el contexto del proyecto en CONTEXTO_PROYECTO.md, implementa el módulo completo de gestión de productos e inventario. Necesito:
1. CRUD de productos con imágenes, categorías y stock
2. Sistema de alertas de stock bajo
3. Gestión de categorías
4. Historial de movimientos de inventario
5. Dashboard específico para inventario con métricas
6. Componentes Livewire para búsqueda y filtrado en tiempo real

Usa los modelos ya creados (Producto, Categoria, MovimientoInventario) y crea las vistas, controladores y rutas necesarias.
```

## 📦 **3. SISTEMA DE PEDIDOS Y VENTAS**
**Prompt:**
```
Implementa el sistema completo de pedidos basándote en el contexto del proyecto. Necesito:
1. Carrito de compras con Livewire 
2. Proceso de checkout paso a paso
3. Gestión de estados de pedidos (pendiente, confirmado, en_preparacion, listo, en_camino, entregado)
4. Panel de gestión de pedidos para vendedores y administradores
5. Sistema de tracking para clientes
6. Cálculo automático de comisiones para vendedores

Usa los modelos Pedido y DetallePedido ya creados. Implementa notificaciones toast para feedback de usuario.
```

## 👥 **4. SISTEMA AVANZADO DE REFERIDOS**
**Prompt:**
```
Mejora el sistema de referidos existente implementando:
1. Árbol genealógico visual de referidos
2. Sistema de niveles y bonificaciones por profundidad
3. Dashboard de referidos con métricas avanzadas
4. Enlaces de referidos personalizados
5. Sistema de recompensas por metas de referidos
6. Reportes de comisiones por referidos

Usa el sistema de referidos ya configurado en el modelo User y expándelo con nuevas funcionalidades.
```

## 📊 **5. SISTEMA DE REPORTES**
**Prompt:**
```
Implementa un sistema completo de reportes con:
1. Reportes de ventas por período, vendedor, producto
2. Análisis de rendimiento de vendedores
3. Reportes de comisiones y pagos
4. Dashboard ejecutivo con gráficos (Chart.js)
5. Exportación a PDF y Excel
6. Reportes automáticos por email
7. Filtros avanzados y búsquedas

Crea controladores específicos para reportes y usa componentes Livewire para filtrado dinámico.
```

## 🚚 **6. MÓDULO DE ENTREGAS**
**Prompt:**
```
Implementa el sistema de gestión de entregas:
1. Asignación de pedidos a repartidores
2. Rutas optimizadas de entrega
3. Tracking en tiempo real para clientes
4. App móvil básica para repartidores (PWA)
5. Notificaciones automáticas por SMS/WhatsApp
6. Estados de entrega y confirmaciones

Integra con mapas y servicios de geolocalización si es posible.
```

## 💳 **7. PASARELAS DE PAGO**
**Prompt:**
```
Integra pasarelas de pago colombianas:
1. PayU Colombia
2. Mercado Pago
3. PSE (Pagos Seguros en Línea)
4. Transferencias bancarias
5. Pago contra entrega
6. Sistema de cuotas y financiación

Implementa webhooks para confirmación de pagos y actualización automática de estados de pedidos.
```

## 📱 **8. OPTIMIZACIÓN MÓVIL Y PWA**
**Prompt:**
```
Convierte la aplicación en una Progressive Web App (PWA):
1. Service Workers para funcionamiento offline
2. Notificaciones push
3. Instalación como app móvil
4. Optimización de performance móvil
5. Carga lazy de imágenes
6. Interfaz específica para móvil

Mantén toda la funcionalidad existente pero optimizada para dispositivos móviles.
```

## 🔔 **9. SISTEMA DE NOTIFICACIONES**
**Prompt:**
```
Implementa un sistema completo de notificaciones:
1. Notificaciones en tiempo real en la app
2. Emails automáticos (pedidos, comisiones, etc.)
3. SMS para confirmaciones importantes
4. WhatsApp Business API para comunicación
5. Panel de configuración de notificaciones por usuario
6. Templates personalizables para emails

Usa Laravel Notifications y colas para procesamiento asíncrono.
```

## 🛡️ **10. SEGURIDAD Y AUDITORÍA**
**Prompt:**
```
Refuerza la seguridad del sistema:
1. Logs de auditoría para acciones importantes
2. Autenticación de dos factores (2FA)
3. Encriptación de datos sensibles
4. Políticas de contraseñas fuertes
5. Prevención de ataques comunes (CSRF, XSS, SQL Injection)
6. Sistema de backups automáticos
7. Monitoreo de intentos de acceso maliciosos

Implementa middlewares de seguridad y logs detallados.
```

## 🎯 **ORDEN RECOMENDADO DE DESARROLLO:**

### **Fase 1 - Inmediata:**
1. Cambio de colores corporativos ✋ **EMPEZAR AQUÍ**
2. Módulo de inventario y productos
3. Sistema de pedidos y ventas

### **Fase 2 - Corto plazo:**
4. Sistema avanzado de referidos  
5. Sistema de reportes
6. Módulo de entregas

### **Fase 3 - Mediano plazo:**
7. Pasarelas de pago
8. Optimización móvil y PWA
9. Sistema de notificaciones

### **Fase 4 - Largo plazo:**
10. Seguridad y auditoría

## 💡 **NOTAS IMPORTANTES:**

- **Siempre** lee el archivo `CONTEXTO_PROYECTO.md` antes de empezar cualquier desarrollo
- **Mantén** la consistencia con el design system ya implementado
- **Usa** los componentes Livewire existentes como base para nuevos
- **Preserva** el sistema de roles y permisos ya configurado
- **Testa** cada funcionalidad con los usuarios de prueba existentes

## 🔄 **ACTUALIZACIÓN DE CONTEXTO:**
Después de cada fase completada, actualiza el archivo `CONTEXTO_PROYECTO.md` con los nuevos módulos implementados para futuras sesiones de Claude.