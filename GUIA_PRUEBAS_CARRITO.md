# Guía de Pruebas - Carrito de Compras Reparado (Con Validación de Stock)

## Cómo Probar las Correcciones

### 1. Acceder al Dashboard del Cliente
1. Inicia sesión como cliente en la aplicación
2. Ve al dashboard del cliente
3. Verás el catálogo de productos

### 2. Probar Validación de Stock al Agregar (NUEVO) ✅
```
✅ Prueba: Buscar un producto con stock = 0 o agotado
✅ Esperado: 
   - El botón muestra "Agotado" en vez de "Agregar al carrito"
   - El botón está deshabilitado (gris)
   - No se puede hacer clic

✅ Prueba: Intentar agregar un producto que ya está en el carrito con cantidad = stock
✅ Esperado:
   - Aparece notificación "Stock máximo disponible: X"
   - No se incrementa la cantidad
   - El producto no se vuelve a agregar
```

### 3. Probar Agregar Productos
```
✅ Prueba: Hacer clic en "Agregar al carrito" en varios productos con stock
✅ Esperado: 
   - Aparece notificación "Producto agregado al carrito"
   - El badge del carrito incrementa su número
   - El producto se agrega al carrito con cantidad 1
```

### 4. Probar Abrir/Cerrar Carrito
```
✅ Prueba: Hacer clic en el botón "Carrito" o "Ver Carrito"
✅ Esperado:
   - El sidebar del carrito se desliza desde la derecha
   - Aparece un backdrop oscuro detrás
   - El scroll de la página se bloquea
   - Los productos agregados se muestran correctamente
   - Se muestra el stock disponible de cada producto

✅ Prueba: Cerrar el carrito
   - Clic en el botón X
   - Clic en el backdrop (fondo oscuro)
   - Presionar tecla Escape
✅ Esperado: El carrito se cierra y el backdrop desaparece
```

### 5. Probar Botones + y - con Validación de Stock (CORREGIDO) ✅
```
✅ Prueba: Hacer clic en el botón "+" de un producto
✅ Esperado:
   - La cantidad aumenta en 1
   - El subtotal del producto se actualiza
   - El total general se actualiza
   - El badge del carrito se actualiza
   - Si la cantidad = stock, el botón "+" se deshabilita
   - Aparece mensaje "Máximo alcanzado" en rojo

✅ Prueba: Hacer clic en el botón "+" cuando cantidad = stock
✅ Esperado:
   - Aparece notificación "Stock máximo disponible: X"
   - La cantidad NO aumenta
   - El botón "+" está deshabilitado (gris)

✅ Prueba: Hacer clic en el botón "-" de un producto
✅ Esperado:
   - La cantidad disminuye en 1
   - Si estaba en el máximo, el botón "+" se habilita de nuevo
   - Si llega a 0, el producto se elimina automáticamente
   - Los totales se actualizan
   - El badge del carrito se actualiza
```

### 6. Probar Indicadores de Stock en el Carrito (NUEVO) ✅
```
✅ Prueba: Ver un producto con stock bajo (≤ 5 unidades)
✅ Esperado:
   - Se muestra "Stock: X" en el carrito
   - Aparece alerta amarilla "⚠ Pocas unidades"
   - El item NO tiene borde amarillo (mientras no alcance el máximo)

✅ Prueba: Incrementar hasta alcanzar el stock máximo
✅ Esperado:
   - Se muestra "Stock: X" en el carrito
   - Aparece alerta roja "⚠ Máximo alcanzado"
   - El botón "+" se deshabilita
   - El item del carrito tiene un BORDE AMARILLO de advertencia
   - El fondo del item es ligeramente amarillo
```

### 7. Probar Eliminar Producto (CORREGIDO) ✅
```
✅ Prueba: Hacer clic en el botón 🗑️ (basura) de un producto
✅ Esperado:
   - El producto se elimina inmediatamente del carrito
   - Aparece notificación "Producto eliminado del carrito"
   - Los totales se recalculan
   - El badge del carrito se actualiza
```

### 8. Probar Vaciar Carrito (NUEVO) ✨
```
✅ Prueba: Con productos en el carrito, hacer clic en "Vaciar"
✅ Esperado:
   - Aparece modal de confirmación
   - Pregunta "¿Estás seguro de que deseas vaciar tu carrito?"
   
✅ Prueba: Confirmar vaciar carrito
✅ Esperado:
   - Todos los productos se eliminan
   - El carrito muestra "Tu carrito está vacío"
   - El badge del carrito desaparece
   - Aparece notificación "Carrito vaciado"
```

### 9. Probar Confirmar Pedido con Validación de Stock (NUEVO) ✅
```
✅ Prueba: Con productos que NO superan el stock, hacer clic en "Confirmar Pedido"
✅ Esperado:
   - Aparece modal con resumen del pedido
   - Muestra todos los productos con cantidades y precios
   - Muestra el stock disponible de cada producto
   - Muestra el total a pagar
   - El botón dice "Continuar"
   - NO hay alertas de stock
   
✅ Prueba: Hacer clic en "Continuar"
✅ Esperado:
   - Redirige a la página de crear pedido
   - El carrito persiste (se guarda en localStorage)

✅ Prueba: Con productos que SUPERAN el stock, hacer clic en "Confirmar Pedido"
✅ Esperado:
   - Aparece modal con resumen del pedido
   - Aparece alerta ROJA: "Algunos productos superan el stock disponible"
   - Los productos con problemas tienen fondo rojo claro
   - Se muestra "⚠ Supera el stock disponible" en cada producto problemático
   - El botón dice "Ajustar Cantidades" (no "Continuar")
   - El botón es AMARILLO (warning) no verde (success)
   
✅ Prueba: Hacer clic en "Ajustar Cantidades"
✅ Esperado:
   - El modal se cierra
   - El carrito permanece abierto
   - Puedes ajustar las cantidades
```

### 10. Probar Persistencia
```
✅ Prueba: Agregar productos al carrito y recargar la página (F5)
✅ Esperado:
   - Los productos permanecen en el carrito
   - Las cantidades se mantienen
   - El stock guardado se mantiene
   - El badge muestra el número correcto
   - Las validaciones de stock siguen funcionando
```

### 11. Probar Responsive
```
✅ Prueba: Abrir en dispositivo móvil o redimensionar ventana
✅ Esperado:
   - El carrito ocupa 100% del ancho en pantallas pequeñas
   - Todos los botones siguen siendo clicables
   - El layout se adapta correctamente
   - Las alertas de stock son visibles
```

## Casos de Prueba Específicos de Stock

### Caso Stock 1: Producto Agotado (Stock = 0)
1. Buscar producto con stock = 0
2. Verificar que botón muestre "Agotado"
3. Verificar que botón esté deshabilitado
4. Intentar hacer clic (no debe responder)

### Caso Stock 2: Stock Bajo (Stock = 3)
1. Agregar producto con stock = 3
2. En el carrito ver alerta "Stock: 3"
3. Ver alerta "⚠ Pocas unidades" en amarillo
4. Incrementar a 2 unidades
5. Incrementar a 3 unidades
6. Ver alerta "⚠ Máximo alcanzado" en rojo
7. Verificar que botón "+" esté deshabilitado
8. Ver borde amarillo en el item

### Caso Stock 3: Superar Stock al Agregar
1. Agregar producto con stock = 5
2. Incrementar a 5 unidades en el carrito
3. Intentar agregar el mismo producto desde el catálogo
4. Verificar mensaje "Stock máximo disponible: 5"
5. Verificar que la cantidad NO aumente

### Caso Stock 4: Confirmar con Stock Insuficiente
1. Agregar producto A con stock = 5 (cantidad 3) ✅
2. Agregar producto B con stock = 2 (cantidad 2) ✅
3. Manualmente en consola cambiar cantidad de B a 5 (simular bug)
4. Hacer clic en "Confirmar Pedido"
5. Ver alerta de stock en el modal
6. Ver producto B resaltado en rojo
7. Ver botón "Ajustar Cantidades"
8. No poder continuar

## Casos de Prueba Generales

### Caso 1: Carrito Vacío
- Estado inicial: Sin productos
- Botón "Vaciar": ❌ No visible
- Botón "Confirmar Pedido": ⚠️ Deshabilitado
- Total: $0

### Caso 2: Un Producto en Carrito
- Agregar 1 producto con stock = 10
- Badge: 1
- Ver stock: 10
- Botón "-": Elimina el producto
- Botón "+": Aumenta a 2
- Botón 🗑️: Elimina el producto

### Caso 3: Múltiples Productos
- Agregar 3 productos diferentes
- Badge: 3+ (suma de cantidades)
- Aumentar cantidad de uno: Badge aumenta
- Eliminar uno: Badge disminuye
- Vaciar: Badge desaparece

### Caso 4: Cantidades Grandes
- Agregar producto con stock = 50
- Hacer clic en "+" 10 veces
- Verificar subtotal correcto
- Verificar total general correcto
- Seguir aumentando hasta alcanzar 50
- Ver botón "+" deshabilitarse

## Errores Comunes y Soluciones

### ❌ El botón + o - no hace nada
**Causa:** Caché del navegador con código antiguo
**Solución:** Hacer hard refresh (Ctrl + Shift + R o Ctrl + F5)

### ❌ El carrito no persiste
**Causa:** localStorage deshabilitado o navegador en modo incógnito
**Solución:** Verificar configuración del navegador

### ❌ Las imágenes no se muestran
**Causa:** Rutas de imágenes incorrectas o storage no vinculado
**Solución:** Verificar que las imágenes estén en public/storage

### ❌ Las validaciones de stock no funcionan
**Causa:** Código JavaScript en caché
**Solución:** Hacer hard refresh y verificar consola del navegador (F12)

### ❌ Puedo agregar más productos del stock disponible
**Causa:** El producto no tiene el atributo data-stock o es null
**Solución:** Verificar que los productos en la BD tengan campo "stock"

## Checklist de Pruebas Completas

### Funcionalidad Básica
- [ ] Agregar productos al carrito
- [ ] Incrementar cantidad con botón +
- [ ] Decrementar cantidad con botón -
- [ ] Eliminar producto individual
- [ ] Vaciar carrito completo
- [ ] Abrir/cerrar carrito con botón
- [ ] Cerrar carrito con Escape
- [ ] Cerrar carrito con backdrop
- [ ] Ver totales actualizados en tiempo real
- [ ] Ver subtotales por producto
- [ ] Confirmar pedido con modal
- [ ] Persistencia después de recargar
- [ ] Responsive en móvil
- [ ] Badge del carrito actualizado
- [ ] Notificaciones toast funcionando

### Validación de Stock
- [ ] Productos agotados marcados como "Agotado"
- [ ] Botón deshabilitado en productos sin stock
- [ ] Validación al agregar producto sin stock
- [ ] Validación al incrementar más allá del stock
- [ ] Mostrar stock disponible en el carrito
- [ ] Alerta "Pocas unidades" cuando stock ≤ 5
- [ ] Alerta "Máximo alcanzado" cuando cantidad = stock
- [ ] Botón "+" deshabilitado cuando se alcanza el máximo
- [ ] Borde amarillo en items sin stock suficiente
- [ ] Validación en modal de confirmar pedido
- [ ] Mensaje de error en modal si hay problemas de stock
- [ ] Botón cambia a "Ajustar Cantidades" si hay problemas
- [ ] No permite continuar si hay stock insuficiente

## Capturas de Pantalla Recomendadas

1. 📸 Carrito vacío
2. 📸 Carrito con productos (con stock normal)
3. 📸 Carrito con producto de stock bajo (alerta amarilla)
4. 📸 Carrito con producto en stock máximo (alerta roja, borde amarillo)
5. 📸 Producto agotado en el catálogo
6. 📸 Modal de confirmar pedido (sin problemas)
7. 📸 Modal de confirmar pedido (con problemas de stock)
8. 📸 Modal de vaciar carrito
9. 📸 Carrito en móvil
10. 📸 Notificaciones toast de stock

---

**Nota:** Todas las pruebas deben pasar exitosamente. Si alguna falla, verificar la consola del navegador (F12) para ver errores de JavaScript.

**Versión de Pruebas:** 2.1 (Con Validación de Stock)  
**Última Actualización:** 2025-10-18 20:48 UTC
1. Inicia sesión como cliente en la aplicación
2. Ve al dashboard del cliente
3. Verás el catálogo de productos

### 2. Probar Agregar Productos
```
✅ Prueba: Hacer clic en "Agregar al carrito" en varios productos
✅ Esperado: 
   - Aparece notificación "Producto agregado al carrito"
   - El badge del carrito incrementa su número
   - El producto se agrega al carrito
```

### 3. Probar Abrir/Cerrar Carrito
```
✅ Prueba: Hacer clic en el botón "Carrito" o "Ver Carrito"
✅ Esperado:
   - El sidebar del carrito se desliza desde la derecha
   - Aparece un backdrop oscuro detrás
   - El scroll de la página se bloquea
   - Los productos agregados se muestran correctamente

✅ Prueba: Cerrar el carrito
   - Clic en el botón X
   - Clic en el backdrop (fondo oscuro)
   - Presionar tecla Escape
✅ Esperado: El carrito se cierra y el backdrop desaparece
```

### 4. Probar Botones + y - (CORREGIDO) ✅
```
✅ Prueba: Hacer clic en el botón "+" de un producto
✅ Esperado:
   - La cantidad aumenta en 1
   - El subtotal del producto se actualiza
   - El total general se actualiza
   - El badge del carrito se actualiza

✅ Prueba: Hacer clic en el botón "-" de un producto
✅ Esperado:
   - La cantidad disminuye en 1
   - Si llega a 0, el producto se elimina automáticamente
   - Los totales se actualizan
   - El badge del carrito se actualiza
```

### 5. Probar Eliminar Producto (CORREGIDO) ✅
```
✅ Prueba: Hacer clic en el botón 🗑️ (basura) de un producto
✅ Esperado:
   - El producto se elimina inmediatamente del carrito
   - Aparece notificación "Producto eliminado del carrito"
   - Los totales se recalculan
   - El badge del carrito se actualiza
```

### 6. Probar Vaciar Carrito (NUEVO) ✨
```
✅ Prueba: Con productos en el carrito, hacer clic en "Vaciar"
✅ Esperado:
   - Aparece modal de confirmación
   - Pregunta "¿Estás seguro de que deseas vaciar tu carrito?"
   
✅ Prueba: Confirmar vaciar carrito
✅ Esperado:
   - Todos los productos se eliminan
   - El carrito muestra "Tu carrito está vacío"
   - El badge del carrito desaparece
   - Aparece notificación "Carrito vaciado"
```

### 7. Probar Confirmar Pedido
```
✅ Prueba: Con productos en el carrito, hacer clic en "Confirmar Pedido"
✅ Esperado:
   - Aparece modal con resumen del pedido
   - Muestra todos los productos con cantidades y precios
   - Muestra el total a pagar
   
✅ Prueba: Hacer clic en "Continuar"
✅ Esperado:
   - Redirige a la página de crear pedido
   - El carrito persiste (se guarda en localStorage)
```

### 8. Probar Persistencia
```
✅ Prueba: Agregar productos al carrito y recargar la página (F5)
✅ Esperado:
   - Los productos permanecen en el carrito
   - Las cantidades se mantienen
   - El badge muestra el número correcto
```

### 9. Probar Responsive
```
✅ Prueba: Abrir en dispositivo móvil o redimensionar ventana
✅ Esperado:
   - El carrito ocupa 100% del ancho en pantallas pequeñas
   - Todos los botones siguen siendo clicables
   - El layout se adapta correctamente
```

## Casos de Prueba Específicos

### Caso 1: Carrito Vacío
- Estado inicial: Sin productos
- Botón "Vaciar": ❌ No visible
- Botón "Confirmar Pedido": ⚠️ Deshabilitado
- Total: $0

### Caso 2: Un Producto en Carrito
- Agregar 1 producto
- Badge: 1
- Botón "-": Elimina el producto
- Botón "+": Aumenta a 2
- Botón 🗑️: Elimina el producto

### Caso 3: Múltiples Productos
- Agregar 3 productos diferentes
- Badge: 3+ (suma de cantidades)
- Aumentar cantidad de uno: Badge aumenta
- Eliminar uno: Badge disminuye
- Vaciar: Badge desaparece

### Caso 4: Cantidades Grandes
- Agregar producto
- Hacer clic en "+" 10 veces
- Verificar que el subtotal se calcule correctamente
- Verificar que el total general sea correcto

## Errores Comunes y Soluciones

### ❌ El botón + o - no hace nada
**Causa:** Caché del navegador con código antiguo
**Solución:** Hacer hard refresh (Ctrl + Shift + R o Ctrl + F5)

### ❌ El carrito no persiste
**Causa:** localStorage deshabilitado o navegador en modo incógnito
**Solución:** Verificar configuración del navegador

### ❌ Las imágenes no se muestran
**Causa:** Rutas de imágenes incorrectas o storage no vinculado
**Solución:** Verificar que las imágenes estén en public/storage

## Checklist de Pruebas Completas

- [ ] Agregar productos al carrito
- [ ] Incrementar cantidad con botón +
- [ ] Decrementar cantidad con botón -
- [ ] Eliminar producto individual
- [ ] Vaciar carrito completo
- [ ] Abrir/cerrar carrito con botón
- [ ] Cerrar carrito con Escape
- [ ] Cerrar carrito con backdrop
- [ ] Ver totales actualizados en tiempo real
- [ ] Ver subtotales por producto
- [ ] Confirmar pedido con modal
- [ ] Persistencia después de recargar
- [ ] Responsive en móvil
- [ ] Badge del carrito actualizado
- [ ] Notificaciones toast funcionando

## Capturas de Pantalla Recomendadas

1. 📸 Carrito vacío
2. 📸 Carrito con productos
3. 📸 Modal de confirmar pedido
4. 📸 Modal de vaciar carrito
5. 📸 Carrito en móvil
6. 📸 Notificaciones toast

---

**Nota:** Todas las pruebas deben pasar exitosamente. Si alguna falla, verificar la consola del navegador (F12) para ver errores de JavaScript.
