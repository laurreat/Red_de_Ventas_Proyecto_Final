# 🚀 Mejoras Futuras - Módulo Pedidos Cliente

## 📋 Roadmap de Funcionalidades

### 🔔 FASE 1: Notificaciones en Tiempo Real (Prioridad: Alta)

**Objetivo:** Mantener al cliente informado automáticamente de cambios en sus pedidos.

#### Tecnologías:
- Laravel Echo + Pusher
- Laravel Broadcasting
- Service Workers (PWA Push Notifications)

#### Implementación:

**1. Backend (Laravel):**
```php
// app/Events/PedidoEstadoActualizado.php
class PedidoEstadoActualizado implements ShouldBroadcast
{
    public $pedido;
    
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->pedido->user_id);
    }
    
    public function broadcastAs()
    {
        return 'pedido.actualizado';
    }
}

// En el controller al cambiar estado:
event(new PedidoEstadoActualizado($pedido));
```

**2. Frontend (JavaScript):**
```javascript
// Escuchar eventos
Echo.private(`user.${userId}`)
    .listen('.pedido.actualizado', (e) => {
        pedidosManager.showToast('success', 'Pedido Actualizado', 
            `Tu pedido #${e.pedido.numero_pedido} cambió a: ${e.pedido.estado}`);
        // Actualizar UI sin recargar
        actualizarPedidoEnLista(e.pedido);
    });
```

**3. PWA Push Notifications:**
```javascript
// Pedir permiso para notificaciones
if ('Notification' in window && 'serviceWorker' in navigator) {
    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            // Suscribirse a push notifications
            navigator.serviceWorker.ready.then(registration => {
                registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: vapidPublicKey
                });
            });
        }
    });
}
```

**Beneficios:**
- ✅ Cliente informado en tiempo real
- ✅ Reduce consultas al servidor
- ✅ Mejor experiencia de usuario
- ✅ Funciona incluso con app cerrada (PWA)

---

### 🗺️ FASE 2: Rastreo GPS del Pedido (Prioridad: Alta)

**Objetivo:** Seguimiento en tiempo real de la ubicación del repartidor.

#### Tecnologías:
- Google Maps API
- Geolocalización HTML5
- WebSockets para actualizaciones

#### Implementación:

**1. Vista de Rastreo:**
```blade
<!-- resources/views/cliente/pedidos/track.blade.php -->
<div class="map-container" id="trackingMap"></div>
<div class="tracking-info">
    <div class="delivery-status">
        <i class="bi bi-truck"></i>
        <span>Tu pedido está en camino</span>
    </div>
    <div class="eta">
        Tiempo estimado: <strong id="eta">15 minutos</strong>
    </div>
</div>
```

**2. JavaScript con Google Maps:**
```javascript
let map, pedidoMarker, repartidorMarker;

function initMap(pedidoLat, pedidoLng) {
    map = new google.maps.Map(document.getElementById('trackingMap'), {
        center: {lat: pedidoLat, lng: pedidoLng},
        zoom: 14
    });
    
    // Marcador del destino
    pedidoMarker = new google.maps.Marker({
        position: {lat: pedidoLat, lng: pedidoLng},
        map: map,
        icon: '/images/markers/home.png',
        title: 'Tu ubicación'
    });
    
    // Marcador del repartidor (se actualiza en tiempo real)
    repartidorMarker = new google.maps.Marker({
        position: {lat: 0, lng: 0},
        map: map,
        icon: '/images/markers/delivery.png',
        title: 'Repartidor'
    });
}

// Actualizar posición del repartidor
Echo.private(`pedido.${pedidoId}`)
    .listen('.location.updated', (e) => {
        repartidorMarker.setPosition({
            lat: e.lat,
            lng: e.lng
        });
        
        // Calcular ETA
        calcularETA(e.lat, e.lng, pedidoLat, pedidoLng);
    });
```

**3. Backend - Actualizar ubicación:**
```php
// app/Http/Controllers/Repartidor/RepartidorController.php
public function updateLocation(Request $request, $pedidoId)
{
    $pedido = Pedido::findOrFail($pedidoId);
    
    // Guardar última ubicación
    $pedido->ubicacion_repartidor = [
        'lat' => $request->lat,
        'lng' => $request->lng,
        'timestamp' => now()
    ];
    $pedido->save();
    
    // Broadcast a cliente
    broadcast(new RepartidorLocationUpdated($pedido, $request->lat, $request->lng));
}
```

**Beneficios:**
- ✅ Transparencia total
- ✅ Reduce ansiedad del cliente
- ✅ Mejor coordinación entrega
- ✅ Menos llamadas de seguimiento

---

### ⭐ FASE 3: Sistema de Reseñas y Calificaciones (Prioridad: Media)

**Objetivo:** Permitir a clientes calificar productos después de recibir el pedido.

#### Estructura de Datos:

**En modelo Producto:**
```php
'reviews' => [
    [
        'user_id' => ObjectId,
        'pedido_id' => ObjectId,
        'rating' => 5, // 1-5 estrellas
        'comment' => 'Deliciosa arepa!',
        'fecha' => ISODate,
        'fotos' => ['url1.jpg', 'url2.jpg'],
        'verificado' => true, // Compró el producto
        'likes' => 10,
        'reportado' => false
    ]
],
'rating_promedio' => 4.8,
'total_reviews' => 127
```

#### Implementación:

**1. Vista de Calificación:**
```blade
<!-- Modal después de entrega -->
<div class="review-modal">
    <h4>¿Cómo estuvo tu pedido?</h4>
    <div class="products-to-review">
        @foreach($pedido->detalles as $detalle)
        <div class="product-review-item">
            <img src="{{ $detalle['producto_data']['imagen'] }}" />
            <div>
                <h6>{{ $detalle['producto_data']['nombre'] }}</h6>
                <div class="star-rating">
                    <i class="bi bi-star" data-rating="1"></i>
                    <i class="bi bi-star" data-rating="2"></i>
                    <i class="bi bi-star" data-rating="3"></i>
                    <i class="bi bi-star" data-rating="4"></i>
                    <i class="bi bi-star" data-rating="5"></i>
                </div>
                <textarea placeholder="Cuéntanos tu experiencia..."></textarea>
                <input type="file" multiple accept="image/*" />
            </div>
        </div>
        @endforeach
    </div>
    <button class="btn btn-primary">Enviar Reseñas</button>
</div>
```

**2. Controller:**
```php
public function storeReview(Request $request, $pedidoId)
{
    $validator = Validator::make($request->all(), [
        'producto_id' => 'required|exists:productos,_id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:500',
        'fotos.*' => 'nullable|image|max:2048'
    ]);
    
    // Verificar que el pedido esté entregado
    $pedido = Pedido::where('_id', $pedidoId)
        ->where('user_id', auth()->id())
        ->where('estado', 'entregado')
        ->firstOrFail();
    
    // Guardar reseña en producto
    $producto = Producto::findOrFail($request->producto_id);
    
    $fotos = [];
    if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
            $fotos[] = $foto->store('reviews', 'public');
        }
    }
    
    $reviews = $producto->reviews ?? [];
    $reviews[] = [
        'user_id' => auth()->id(),
        'user_nombre' => auth()->user()->name,
        'pedido_id' => $pedidoId,
        'rating' => $request->rating,
        'comment' => htmlspecialchars($request->comment),
        'fecha' => now(),
        'fotos' => $fotos,
        'verificado' => true,
        'likes' => 0
    ];
    
    $producto->reviews = $reviews;
    
    // Recalcular promedio
    $totalRatings = array_sum(array_column($reviews, 'rating'));
    $producto->rating_promedio = $totalRatings / count($reviews);
    $producto->total_reviews = count($reviews);
    
    $producto->save();
    
    return response()->json(['success' => true]);
}
```

**3. Mostrar Reseñas en Producto:**
```blade
<div class="product-reviews">
    <div class="reviews-summary">
        <div class="rating-large">{{ number_format($producto->rating_promedio, 1) }}</div>
        <div class="stars">★★★★★</div>
        <div class="total">{{ $producto->total_reviews }} reseñas</div>
    </div>
    
    @foreach($producto->reviews as $review)
    <div class="review-item">
        <div class="review-header">
            <strong>{{ $review['user_nombre'] }}</strong>
            <span class="verified">✓ Compra verificada</span>
            <small>{{ Carbon\Carbon::parse($review['fecha'])->diffForHumans() }}</small>
        </div>
        <div class="review-rating">
            @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $review['rating'] ? '-fill' : '' }}"></i>
            @endfor
        </div>
        <p>{{ $review['comment'] }}</p>
        @if(!empty($review['fotos']))
        <div class="review-photos">
            @foreach($review['fotos'] as $foto)
            <img src="{{ asset('storage/' . $foto) }}" />
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
</div>
```

**Beneficios:**
- ✅ Feedback valioso
- ✅ Mejora productos
- ✅ Genera confianza
- ✅ SEO positivo

---

### 💳 FASE 4: Pagos Online (Prioridad: Alta)

**Objetivo:** Permitir pagos con tarjeta/PSE directamente en la plataforma.

#### Opciones de Pasarelas (Colombia):

1. **Wompi (Bancolombia)** - Recomendado
   - Integración sencilla
   - Fees competitivos (2.99% + $900)
   - Soporte PSE, tarjetas, Nequi

2. **PayU Latam**
   - Ampliamente usado
   - Múltiples métodos
   - Fees ~3.49% + IVA

3. **MercadoPago**
   - Fácil integración
   - QR codes
   - Fees ~3.99%

#### Implementación con Wompi:

**1. Backend:**
```php
// app/Services/WompiPaymentService.php
class WompiPaymentService
{
    protected $publicKey;
    protected $privateKey;
    
    public function createTransaction($pedido)
    {
        $reference = 'PEDIDO_' . $pedido->numero_pedido;
        
        $data = [
            'amount_in_cents' => $pedido->total_final * 100, // Convertir a centavos
            'currency' => 'COP',
            'customer_email' => $pedido->cliente_data['email'],
            'payment_method' => [
                'type' => 'CARD', // o 'PSE', 'NEQUI'
            ],
            'reference' => $reference,
            'redirect_url' => route('cliente.pedidos.payment-callback')
        ];
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->privateKey,
            'Content-Type' => 'application/json'
        ])->post('https://production.wompi.co/v1/transactions', $data);
        
        return $response->json();
    }
    
    public function verifyTransaction($transactionId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->publicKey
        ])->get("https://production.wompi.co/v1/transactions/{$transactionId}");
        
        return $response->json();
    }
}
```

**2. Controller:**
```php
public function processPayment(Request $request, $pedidoId)
{
    $pedido = Pedido::where('_id', $pedidoId)
        ->where('user_id', auth()->id())
        ->firstOrFail();
    
    if ($pedido->estado !== 'pendiente') {
        return redirect()->back()->with('error', 'Pedido ya procesado');
    }
    
    $wompi = new WompiPaymentService();
    $transaction = $wompi->createTransaction($pedido);
    
    if ($transaction['status'] === 'PENDING') {
        // Guardar referencia de transacción
        $pedido->payment_transaction_id = $transaction['id'];
        $pedido->save();
        
        // Redirigir a Wompi checkout
        return redirect($transaction['payment_link_url']);
    }
    
    return redirect()->back()->with('error', 'Error al procesar pago');
}

public function paymentCallback(Request $request)
{
    $transactionId = $request->id;
    
    $wompi = new WompiPaymentService();
    $transaction = $wompi->verifyTransaction($transactionId);
    
    $pedido = Pedido::where('payment_transaction_id', $transactionId)->first();
    
    if ($transaction['status'] === 'APPROVED') {
        $pedido->estado = 'confirmado';
        $pedido->payment_status = 'paid';
        $pedido->payment_method_real = $transaction['payment_method_type'];
        $pedido->cambiarEstado('confirmado', 'Pago confirmado online', auth()->id());
        
        // Enviar email de confirmación
        Mail::to($pedido->cliente_data['email'])
            ->send(new PedidoConfirmado($pedido));
        
        return redirect()
            ->route('cliente.pedidos.show', $pedido->_id)
            ->with('success', '¡Pago exitoso! Tu pedido ha sido confirmado.');
    }
    
    return redirect()
        ->route('cliente.pedidos.show', $pedido->_id)
        ->with('error', 'Pago rechazado. Intenta nuevamente.');
}
```

**3. Frontend:**
```blade
<div class="payment-options">
    <h5>Método de Pago</h5>
    
    <div class="payment-method" data-method="online">
        <input type="radio" name="metodo_pago" value="online" id="online">
        <label for="online">
            <i class="bi bi-credit-card"></i>
            Pago Online
            <span class="badge bg-success">Seguro</span>
        </label>
        <div class="payment-logos">
            <img src="/images/payments/visa.png" alt="Visa">
            <img src="/images/payments/mastercard.png" alt="Mastercard">
            <img src="/images/payments/pse.png" alt="PSE">
            <img src="/images/payments/nequi.png" alt="Nequi">
        </div>
    </div>
    
    <div class="payment-method" data-method="efectivo">
        <input type="radio" name="metodo_pago" value="efectivo" id="efectivo">
        <label for="efectivo">
            <i class="bi bi-cash-stack"></i>
            Pago Contraentrega
        </label>
    </div>
</div>
```

**Beneficios:**
- ✅ Pago inmediato
- ✅ Confirmación automática
- ✅ Reduce rechazos
- ✅ Mejor flujo de caja
- ✅ Seguridad PCI DSS

---

### 🎁 FASE 5: Sistema de Cupones y Descuentos (Prioridad: Media)

**Objetivo:** Aumentar ventas con promociones estratégicas.

#### Tipos de Cupones:

1. **Porcentaje:** 10% de descuento
2. **Monto Fijo:** $5,000 COP de descuento
3. **Envío Gratis:** Sin costo de envío
4. **BOGO:** Buy One Get One
5. **Por Categoría:** 20% en arepas dulces
6. **Primera Compra:** 15% para nuevos clientes
7. **Referido:** $10,000 por referir amigo

#### Modelo de Datos:

```php
// app/Models/Cupon.php
protected $fillable = [
    'codigo', // 'VERANO2024'
    'tipo', // 'porcentaje', 'monto_fijo', 'envio_gratis'
    'valor', // 10 (%) o 5000 (COP)
    'descripcion',
    'fecha_inicio',
    'fecha_fin',
    'usos_maximos', // null = ilimitado
    'usos_por_usuario', // 1
    'usos_actuales',
    'monto_minimo', // Pedido mínimo $20,000
    'categorias_aplicables', // ['arepas', 'empanadas']
    'productos_aplicables', // [ObjectId, ObjectId]
    'usuarios_aplicables', // [ObjectId] o null = todos
    'primer_pedido_only', // true/false
    'activo'
];
```

#### Implementación:

**1. Aplicar Cupón en Create:**
```blade
<div class="cupon-section">
    <label>¿Tienes un cupón?</label>
    <div class="input-group">
        <input type="text" 
               id="cuponInput" 
               class="form-control" 
               placeholder="Ingresa tu código">
        <button type="button" 
                class="btn btn-primary"
                onclick="aplicarCupon()">
            Aplicar
        </button>
    </div>
    <div id="cuponResult"></div>
</div>

<script>
function aplicarCupon() {
    const codigo = document.getElementById('cuponInput').value;
    
    fetch('/api/cupones/validar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            codigo: codigo,
            total: cartTotal,
            productos: getCartProducts()
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.valid) {
            // Aplicar descuento
            actualizarDescuento(data.descuento);
            mostrarCuponExito(data);
        } else {
            mostrarCuponError(data.message);
        }
    });
}
</script>
```

**2. Controller de Validación:**
```php
public function validarCupon(Request $request)
{
    $cupon = Cupon::where('codigo', strtoupper($request->codigo))
        ->where('activo', true)
        ->first();
    
    if (!$cupon) {
        return response()->json([
            'valid' => false,
            'message' => 'Cupón no válido'
        ]);
    }
    
    // Validar fechas
    if ($cupon->fecha_inicio && now() < $cupon->fecha_inicio) {
        return response()->json([
            'valid' => false,
            'message' => 'Cupón aún no disponible'
        ]);
    }
    
    if ($cupon->fecha_fin && now() > $cupon->fecha_fin) {
        return response()->json([
            'valid' => false,
            'message' => 'Cupón expirado'
        ]);
    }
    
    // Validar usos
    if ($cupon->usos_maximos && $cupon->usos_actuales >= $cupon->usos_maximos) {
        return response()->json([
            'valid' => false,
            'message' => 'Cupón agotado'
        ]);
    }
    
    // Validar usos por usuario
    if ($cupon->usos_por_usuario) {
        $usosUsuario = Pedido::where('user_id', auth()->id())
            ->where('cupon_codigo', $cupon->codigo)
            ->count();
            
        if ($usosUsuario >= $cupon->usos_por_usuario) {
            return response()->json([
                'valid' => false,
                'message' => 'Ya usaste este cupón'
            ]);
        }
    }
    
    // Validar monto mínimo
    if ($cupon->monto_minimo && $request->total < $cupon->monto_minimo) {
        return response()->json([
            'valid' => false,
            'message' => "Compra mínima de \${$cupon->monto_minimo}"
        ]);
    }
    
    // Calcular descuento
    $descuento = 0;
    if ($cupon->tipo === 'porcentaje') {
        $descuento = ($request->total * $cupon->valor) / 100;
    } elseif ($cupon->tipo === 'monto_fijo') {
        $descuento = $cupon->valor;
    }
    
    return response()->json([
        'valid' => true,
        'descuento' => $descuento,
        'tipo' => $cupon->tipo,
        'descripcion' => $cupon->descripcion
    ]);
}
```

**3. Al Crear Pedido:**
```php
// En PedidoClienteController@store
if ($request->cupon_codigo) {
    $cupon = Cupon::where('codigo', $request->cupon_codigo)->first();
    
    if ($cupon) {
        $pedido->cupon_codigo = $cupon->codigo;
        $pedido->cupon_descuento = $request->descuento;
        $pedido->descuento = $request->descuento;
        $pedido->total_final = $pedido->total - $pedido->descuento;
        
        // Incrementar uso
        $cupon->increment('usos_actuales');
    }
}
```

**Beneficios:**
- ✅ Aumenta conversiones
- ✅ Fideliza clientes
- ✅ Marketing efectivo
- ✅ Métricas de campañas
- ✅ Viral con referidos

---

### 📊 FASE 6: Analytics y Reportes para Cliente (Prioridad: Baja)

**Objetivo:** Dar insights al cliente sobre sus compras.

#### Dashboard de Cliente:

**1. Estadísticas Personales:**
```blade
<div class="my-stats-dashboard">
    <div class="stat-card">
        <h6>Tu Gasto Total</h6>
        <div class="value">${{ number_format($stats['total_gastado']) }}</div>
        <small>En los últimos 12 meses</small>
    </div>
    
    <div class="stat-card">
        <h6>Producto Favorito</h6>
        <div class="favorite-product">
            <img src="{{ $stats['producto_favorito']->imagen }}" />
            <span>{{ $stats['producto_favorito']->nombre }}</span>
        </div>
        <small>{{ $stats['veces_ordenado'] }} veces</small>
    </div>
    
    <div class="stat-card">
        <h6>Frecuencia</h6>
        <div class="value">{{ $stats['pedidos_por_mes'] }} pedidos/mes</div>
        <small>Promedio mensual</small>
    </div>
    
    <div class="stat-card">
        <h6>Ahorro con Cupones</h6>
        <div class="value text-success">${{ number_format($stats['ahorro_cupones']) }}</div>
        <small>Gracias a promociones</small>
    </div>
</div>
```

**2. Gráficos:**
```html
<!-- Usando Chart.js -->
<canvas id="gastosChart"></canvas>

<script>
const ctx = document.getElementById('gastosChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
            label: 'Tus Compras',
            data: [45000, 32000, 67000, 54000, 78000, 62000],
            borderColor: '#722F37',
            backgroundColor: 'rgba(114, 47, 55, 0.1)'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {display: false},
            title: {
                display: true,
                text: 'Historial de Compras'
            }
        }
    }
});
</script>
```

**Beneficios:**
- ✅ Engagement del cliente
- ✅ Insights valiosos
- ✅ Gamificación posible
- ✅ Fidelización

---

## 🎮 Bonus: Gamificación

### Sistema de Puntos y Niveles:

**Niveles:**
- 🥉 Bronce: 0-3 pedidos
- 🥈 Plata: 4-10 pedidos
- 🥇 Oro: 11-25 pedidos
- 💎 Diamante: 26+ pedidos

**Beneficios por Nivel:**
- Plata: 5% descuento permanente
- Oro: 10% descuento + envío gratis
- Diamante: 15% descuento + envío gratis + acceso early bird a nuevos productos

**Insignias:**
- 🎂 "Primera Compra"
- 🔥 "Racha de 5 pedidos"
- 🌟 "Cliente VIP"
- 👑 "Embajador" (10+ referidos)

---

## 📅 Timeline Sugerido

| Fase | Prioridad | Tiempo Estimado | Dependencias |
|------|-----------|-----------------|--------------|
| Notificaciones RT | Alta | 2 semanas | Laravel Echo, Pusher |
| Rastreo GPS | Alta | 3 semanas | Google Maps API |
| Reseñas | Media | 2 semanas | - |
| Pagos Online | Alta | 3 semanas | Wompi/PayU account |
| Cupones | Media | 2 semanas | - |
| Analytics Cliente | Baja | 1 semana | - |
| Gamificación | Baja | 2 semanas | - |

**Total:** ~15 semanas (3.75 meses) para todas las fases

---

## 💰 Inversión Estimada

### Servicios Externos:
- **Pusher (Notificaciones):** $49/mes (plan Startup)
- **Google Maps API:** ~$200/mes (10,000 requests/día)
- **Wompi/PayU:** Sin costo fijo, solo fees por transacción
- **Hosting Mejorado:** $50/mes (para soportar WebSockets)

**Total Mensual:** ~$300/mes

### Desarrollo:
- **Desarrollador Full-Stack:** $15-25/hora
- **15 semanas × 40 horas = 600 horas**
- **Costo Total:** $9,000 - $15,000 USD

---

## 🎯 ROI Esperado

### Métricas a Mejorar:
- **Tasa de Conversión:** +25% (con pagos online)
- **Ticket Promedio:** +15% (con cupones estratégicos)
- **Retención:** +30% (con notificaciones y gamificación)
- **Satisfacción:** +40% (con rastreo GPS)

### Proyección de Ingresos:
Si actualmente vendes $10,000 USD/mes:
- Con mejoras: $15,500 USD/mes
- **Retorno en:** ~6-10 meses

---

## ✅ Conclusión

Estas mejoras transformarán el módulo de pedidos de básico a **experiencia premium**, posicionando la plataforma como líder en el mercado de delivery de arepas.

**Prioriza:** Notificaciones RT + Pagos Online + Rastreo GPS para mayor impacto inicial.
