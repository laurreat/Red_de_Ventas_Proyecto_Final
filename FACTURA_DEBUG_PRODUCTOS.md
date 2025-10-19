# Corrección: Visualización de Productos en Factura

## 🐛 Problema Identificado

Los productos y sus cantidades no se mostraban en la factura, y el IVA no se calculaba correctamente.

## ✅ Soluciones Implementadas

### 1. **Manejo Robusto de Datos**

Se implementó un manejo flexible que soporta diferentes estructuras de datos de MongoDB:

```php
// Convierte automáticamente arrays u objetos
$detalles = is_array($pedido->detalles) ? $pedido->detalles : $pedido->detalles->toArray();

// Maneja cada detalle de forma segura
$detalleArray = is_array($detalle) ? $detalle : (is_object($detalle) ? (array)$detalle : []);

// Obtiene valores con valores por defecto
$cantidad = $detalleArray['cantidad'] ?? 0;
$precioUnitario = $detalleArray['precio_unitario'] ?? 0;
$subtotal = $detalleArray['subtotal'] ?? ($cantidad * $precioUnitario);
```

### 2. **Cálculo Correcto del IVA**

```php
// IVA incluido en el precio (19% Colombia)
$baseImponible = $subtotal / 1.19;
$iva = $subtotal - $baseImponible;
$totalIVA += $iva;
```

### 3. **Acceso Seguro a Imágenes**

```php
// Busca en múltiples campos posibles
$imagen = $productoData['imagen'] ?? $productoData['imagen_principal'] ?? null;
```

### 4. **Modo Debug Temporal**

Se agregó información de debugging que se muestra solo en modo desarrollo:

```php
@if(config('app.debug'))
<div class="alert alert-info">
    DEBUG: 
    Tipo de detalles: {{ gettype($pedido->detalles) }}
    Cantidad: {{ count($pedido->detalles) }}
    Primer detalle: {{ print_r($pedido->detalles[0]) }}
</div>
@endif
```

## 🔍 Verificación de Datos

### Revisar la Estructura del Pedido en MongoDB

Abre MongoDB Compass o la consola de Mongo y verifica:

```javascript
db.pedidos.findOne({numero_pedido: "PED-000001"})
```

**Debe tener esta estructura:**

```json
{
    "_id": "...",
    "numero_pedido": "PED-000001",
    "detalles": [
        {
            "producto_id": "...",
            "producto_data": {
                "_id": "...",
                "nombre": "Producto 1",
                "descripcion": "Descripción",
                "precio": 50000,
                "imagen": "productos/imagen.jpg",
                // o también puede ser:
                "imagen_principal": "productos/imagen.jpg"
            },
            "cantidad": 2,
            "precio_unitario": 50000,
            "subtotal": 100000
        }
    ],
    "total": 100000,
    "descuento": 0,
    "total_final": 100000
}
```

## 🛠️ Pasos para Verificar

### 1. Activar Modo Debug (Si no está activo)

Edita `.env`:
```env
APP_DEBUG=true
```

### 2. Ver un Pedido

1. Inicia sesión como cliente
2. Ve a "Mis Pedidos"
3. Haz click en cualquier pedido
4. Verás un cuadro azul con información de debug

### 3. Analizar el Debug

El cuadro mostrará:
- **Tipo de detalles:** Debería ser "array" o "object"
- **Cantidad:** Número de productos (debe ser > 0)
- **Primer detalle:** Estructura completa del primer producto

### 4. Problemas Comunes y Soluciones

#### ❌ Problema: "Cantidad de detalles: 0"

**Causa:** El pedido no tiene productos
**Solución:** Verifica que se creó correctamente el pedido

```php
// En PedidoClienteController@store
$pedido->detalles = $detalles; // Debe asignarse antes de save()
$pedido->save();
```

#### ❌ Problema: "Tipo: NULL"

**Causa:** Campo detalles no existe
**Solución:** El pedido se creó sin detalles

```bash
# Verificar en Tinker
php artisan tinker
>>> $pedido = App\Models\Pedido::first();
>>> $pedido->detalles;
```

#### ❌ Problema: Productos se muestran pero sin imágenes

**Causa:** Campo de imagen tiene nombre diferente
**Solución:** Ya implementado, busca en:
- `producto_data.imagen`
- `producto_data.imagen_principal`

#### ❌ Problema: IVA aparece en 0

**Causa:** Subtotal es 0
**Solución:** Verificar que precio_unitario y cantidad tienen valores

```php
// El cálculo es:
$subtotal = $cantidad * $precio_unitario;
$baseImponible = $subtotal / 1.19;
$iva = $subtotal - $baseImponible;
```

## 📝 Estructura Correcta de Detalles

### Al Crear el Pedido

En `PedidoClienteController@store`:

```php
$detalles[] = [
    'producto_id' => $producto->_id,
    'producto_data' => [
        '_id' => $producto->_id,
        'nombre' => $producto->nombre,
        'descripcion' => $producto->descripcion ?? '',
        'precio' => (float)$producto->precio,
        'imagen' => $producto->imagen_principal ?? null,
        'categoria_data' => $producto->categoria_data ?? []
    ],
    'cantidad' => $cantidadSolicitada,
    'precio_unitario' => (float)$producto->precio,
    'subtotal' => (float)$subtotal
];

$pedido->detalles = $detalles; // IMPORTANTE: Asignar antes de save()
```

## 🧪 Prueba Manual

### Crear un Pedido de Prueba

1. Como cliente, crea un nuevo pedido
2. Selecciona 2-3 productos
3. Cambia las cantidades
4. Completa el formulario
5. Confirma el pedido

### Verificar la Factura

1. Deberías ver la lista de productos con:
   - ✅ Imagen del producto
   - ✅ Nombre del producto
   - ✅ Cantidad en badge morado
   - ✅ Precio unitario
   - ✅ IVA calculado (19%)
   - ✅ Subtotal

2. En el resumen debe aparecer:
   - ✅ Subtotal (Base imponible)
   - ✅ IVA total calculado
   - ✅ Total a pagar
   - ✅ Total en letras

## 🔧 Si Aún No Funciona

### Opción 1: Verificar Creación de Pedidos

```bash
php artisan tinker
```

```php
// Ver último pedido
$pedido = App\Models\Pedido::latest()->first();
dd($pedido->detalles);

// Debe mostrar array con productos
// Si muestra NULL o vacío, el problema está en la creación
```

### Opción 2: Crear Pedido de Prueba Manualmente

```php
php artisan tinker
```

```php
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;

$cliente = User::where('rol', 'cliente')->first();
$producto = Producto::where('activo', true)->first();

$pedido = new Pedido();
$pedido->numero_pedido = 'PED-TEST-' . time();
$pedido->user_id = $cliente->_id;
$pedido->estado = 'pendiente';
$pedido->detalles = [
    [
        'producto_id' => $producto->_id,
        'producto_data' => [
            '_id' => $producto->_id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'precio' => $producto->precio,
            'imagen' => $producto->imagen_principal
        ],
        'cantidad' => 2,
        'precio_unitario' => $producto->precio,
        'subtotal' => $producto->precio * 2
    ]
];
$pedido->total = $producto->precio * 2;
$pedido->total_final = $producto->precio * 2;
$pedido->direccion_entrega = 'Dirección de prueba';
$pedido->telefono_entrega = '3001234567';
$pedido->metodo_pago = 'efectivo';
$pedido->save();

echo "Pedido creado: " . $pedido->numero_pedido;
```

Luego ve a la factura de ese pedido de prueba.

## 📊 Ejemplo de Salida Esperada

### Con Datos Correctos

```
┌──────────────────────────────────────────────────────────┐
│ Detalle de Productos                                     │
├──────┬─────────────────┬─────────┬──────────┬───────────┤
│ Item │ Producto        │ Cant.   │ Precio   │ Subtotal  │
├──────┼─────────────────┼─────────┼──────────┼───────────┤
│  1   │ [Img] Producto1 │    2    │  50,000  │  100,000  │
│  2   │ [Img] Producto2 │    1    │  30,000  │   30,000  │
└──────┴─────────────────┴─────────┴──────────┴───────────┘

Subtotal (Base Imponible):  $109,244
IVA (19%):                  $ 20,756
───────────────────────────────────────
TOTAL A PAGAR:              $130,000 COP

Son: CIENTO TREINTA MIL PESOS M/CTE
```

## 🚀 Después de Corregir

### Desactivar Debug

Una vez que todo funcione, edita `.env`:
```env
APP_DEBUG=false
```

Y el cuadro de debug desaparecerá automáticamente.

## 📞 Soporte Adicional

Si después de seguir estos pasos aún no funciona:

1. Copia el contenido del cuadro de DEBUG
2. Verifica los logs: `storage/logs/laravel.log`
3. Revisa la consola del navegador (F12)
4. Comparte la estructura del pedido desde MongoDB

---

**Fecha:** 2025-10-19  
**Estado:** ✅ CORREGIDO con manejo robusto de datos  
**Archivos modificados:** `resources/views/cliente/pedidos/show.blade.php`
