# 🛠️ Comandos Útiles - Módulo Pedidos Cliente

## 🧹 Limpieza de Caché

```bash
# Limpiar todo el caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan optimize
```

## 🔍 Verificación de Rutas

```bash
# Ver todas las rutas del módulo cliente.pedidos
php artisan route:list --name=cliente.pedidos

# Ver todas las rutas de cliente
php artisan route:list --name=cliente

# Ver ruta específica
php artisan route:list | findstr "pedidos"
```

Resultado esperado:
```
GET     cliente/pedidos ........................ cliente.pedidos.index
GET     cliente/pedidos/create ................. cliente.pedidos.create
POST    cliente/pedidos ........................ cliente.pedidos.store
GET     cliente/pedidos/{id} ................... cliente.pedidos.show
POST    cliente/pedidos/{id}/cancelar .......... cliente.pedidos.cancel
```

## 🗄️ MongoDB - Consultas Útiles

### Conectar a MongoDB (Compass o mongosh)
```javascript
use arepa_llanerita_db

// Ver todos los pedidos
db.pedidos.find().pretty()

// Ver pedidos de un cliente específico
db.pedidos.find({
    "user_id": ObjectId("USER_ID_AQUI")
}).sort({created_at: -1})

// Contar pedidos por estado
db.pedidos.aggregate([
    {
        $group: {
            _id: "$estado",
            total: { $sum: 1 }
        }
    }
])

// Ver productos con stock bajo
db.productos.find({
    activo: true,
    stock: { $lt: 10 }
})

// Ver últimos 10 pedidos
db.pedidos.find().sort({created_at: -1}).limit(10)

// Buscar pedido por número
db.pedidos.findOne({
    numero_pedido: "ARE-2024-00001"
})

// Ver pedidos pendientes
db.pedidos.find({
    estado: "pendiente"
}).count()

// Actualizar stock de un producto
db.productos.updateOne(
    { _id: ObjectId("PRODUCTO_ID") },
    { $set: { stock: 50 } }
)

// Ver estadísticas de ventas
db.pedidos.aggregate([
    {
        $match: { estado: { $in: ["confirmado", "entregado"] } }
    },
    {
        $group: {
            _id: null,
            total_ventas: { $sum: "$total_final" },
            cantidad_pedidos: { $sum: 1 },
            promedio: { $avg: "$total_final" }
        }
    }
])
```

## 📊 Logs y Debugging

```bash
# Ver logs en tiempo real (Windows)
Get-Content storage\logs\laravel.log -Wait -Tail 50

# Ver solo errores
findstr /C:"ERROR" storage\logs\laravel.log

# Ver logs de pedidos creados hoy
findstr /C:"Pedido creado exitosamente" storage\logs\laravel.log

# Limpiar logs viejos
del storage\logs\laravel.log

# Ver tamaño de logs
dir storage\logs\laravel.log
```

## 🧪 Testing Manual

### 1. Crear Usuario de Prueba (Tinker)
```bash
php artisan tinker
```

```php
// En Tinker:
$user = new App\Models\User();
$user->name = "Cliente Test";
$user->email = "cliente@test.com";
$user->password = bcrypt('password123');
$user->email_verified_at = now();
$user->telefono = "+57 300 123 4567";
$user->direccion = "Calle 123 # 45-67, Bogotá";
$user->save();

// Asignar rol de cliente
$user->syncRoles(['cliente']);

echo "Usuario creado: " . $user->_id;
exit
```

### 2. Crear Productos de Prueba
```php
php artisan tinker

// En Tinker:
use App\Models\Producto;
use App\Models\Categoria;

$categoria = Categoria::firstOrCreate(
    ['nombre' => 'Arepas'],
    ['descripcion' => 'Arepas tradicionales', 'activo' => true]
);

for ($i = 1; $i <= 5; $i++) {
    $producto = new Producto();
    $producto->nombre = "Arepa Test $i";
    $producto->descripcion = "Descripción de prueba $i";
    $producto->precio = rand(3000, 8000);
    $producto->stock = rand(10, 50);
    $producto->stock_minimo = 5;
    $producto->activo = true;
    $producto->categoria_id = $categoria->_id;
    $producto->categoria_data = [
        '_id' => $categoria->_id,
        'nombre' => $categoria->nombre
    ];
    $producto->save();
    echo "Producto creado: {$producto->nombre}\n";
}

exit
```

### 3. Simular Pedido Completo
```php
php artisan tinker

use App\Models\User;
use App\Models\Pedido;
use App\Models\Producto;

$user = User::where('email', 'cliente@test.com')->first();
$productos = Producto::where('activo', true)->where('stock', '>', 0)->limit(3)->get();

$pedido = new Pedido();
$pedido->numero_pedido = 'TEST-' . time();
$pedido->user_id = $user->_id;
$pedido->cliente_data = [
    '_id' => $user->_id,
    'name' => $user->name,
    'email' => $user->email
];
$pedido->estado = 'pendiente';
$pedido->direccion_entrega = 'Dirección de prueba';
$pedido->telefono_entrega = '+57 300 123 4567';
$pedido->metodo_pago = 'efectivo';

$detalles = [];
$total = 0;

foreach ($productos as $producto) {
    $cantidad = rand(1, 3);
    $subtotal = $producto->precio * $cantidad;
    $total += $subtotal;
    
    $detalles[] = [
        'producto_id' => $producto->_id,
        'producto_data' => [
            '_id' => $producto->_id,
            'nombre' => $producto->nombre,
            'precio' => $producto->precio
        ],
        'cantidad' => $cantidad,
        'precio_unitario' => $producto->precio,
        'subtotal' => $subtotal
    ];
}

$pedido->detalles = $detalles;
$pedido->total = $total;
$pedido->total_final = $total;
$pedido->historial_estados = [[
    'estado' => 'pendiente',
    'fecha' => now(),
    'usuario_id' => $user->_id
]];

$pedido->save();

echo "Pedido creado: {$pedido->numero_pedido}\n";
echo "Total: \${$pedido->total_final}\n";
exit
```

## 🔐 Seguridad - Verificaciones

```bash
# Verificar permisos de archivos
icacls storage\logs

# Ver usuarios activos
php artisan tinker
User::where('activo', true)->count();
exit

# Revisar intentos de login fallidos (si está implementado)
php artisan tinker
DB::connection('mongodb')->collection('login_attempts')->count();
exit
```

## ⚡ Optimización

```bash
# Generar autoload optimizado
composer dump-autoload -o

# Cachear configuración (solo producción)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar caché de aplicación
php artisan cache:forget 'productos_disponibles'
php artisan cache:forget 'cliente_stats_*'

# Ver info de caché
php artisan cache:table
```

## 📦 Respaldo MongoDB

### Backup
```bash
# Backup completo
mongodump --uri="mongodb://localhost:27017/arepa_llanerita_db" --out="C:\backups\mongo_$(date +%Y%m%d)"

# Backup solo colección pedidos
mongodump --uri="mongodb://localhost:27017/arepa_llanerita_db" --collection=pedidos --out="C:\backups\pedidos_$(date +%Y%m%d)"

# Backup solo colección productos
mongodump --uri="mongodb://localhost:27017/arepa_llanerita_db" --collection=productos --out="C:\backups\productos_$(date +%Y%m%d)"
```

### Restore
```bash
# Restaurar todo
mongorestore --uri="mongodb://localhost:27017/arepa_llanerita_db" "C:\backups\mongo_20240101"

# Restaurar solo pedidos
mongorestore --uri="mongodb://localhost:27017/arepa_llanerita_db" --collection=pedidos "C:\backups\pedidos_20240101\arepa_llanerita_db\pedidos.bson"
```

## 🧪 Tests Automáticos (Feature Tests)

```bash
# Crear test
php artisan make:test PedidoClienteTest

# Ejecutar todos los tests
php artisan test

# Ejecutar solo tests de pedidos
php artisan test --filter=Pedido

# Con cobertura
php artisan test --coverage
```

### Ejemplo de Test:
```php
// tests/Feature/PedidoClienteTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoClienteTest extends TestCase
{
    public function test_cliente_puede_ver_listado_pedidos()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/cliente/pedidos');
        
        $response->assertStatus(200);
        $response->assertSee('Mis Pedidos');
    }
    
    public function test_cliente_puede_crear_pedido()
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 10]);
        
        $response = $this->actingAs($user)
            ->post('/cliente/pedidos', [
                'productos' => [
                    ['producto_id' => $producto->_id, 'cantidad' => 2]
                ],
                'direccion_entrega' => 'Calle 123',
                'telefono_entrega' => '+57 300 123 4567',
                'metodo_pago' => 'efectivo'
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('pedidos', [
            'user_id' => $user->_id,
            'estado' => 'pendiente'
        ]);
    }
}
```

## 📈 Monitoreo Performance

```bash
# Instalar Laravel Debugbar (solo desarrollo)
composer require barryvdh/laravel-debugbar --dev

# Ver queries de MongoDB en logs
php artisan tinker
DB::connection('mongodb')->enableQueryLog();
// Ejecutar operación
DB::connection('mongodb')->getQueryLog();
exit

# Medir tiempo de carga
Measure-Command { Invoke-WebRequest -Uri "http://localhost/cliente/pedidos" }
```

## 🔄 Sincronización de Datos

```bash
# Sincronizar datos embebidos (si cambian en master)
php artisan tinker

// Actualizar cliente_data en todos los pedidos de un usuario
$user = User::find('USER_ID');
$pedidos = Pedido::where('user_id', $user->_id)->get();

foreach ($pedidos as $pedido) {
    $pedido->cliente_data = [
        '_id' => $user->_id,
        'name' => $user->name,
        'apellidos' => $user->apellidos,
        'email' => $user->email,
        'telefono' => $user->telefono,
        'cedula' => $user->cedula
    ];
    $pedido->save();
    echo "Pedido {$pedido->numero_pedido} actualizado\n";
}

exit
```

## 🛠️ Mantenimiento

```bash
# Eliminar pedidos de prueba
php artisan tinker
Pedido::where('numero_pedido', 'like', 'TEST-%')->delete();
exit

# Limpiar pedidos cancelados antiguos (>90 días)
php artisan tinker
$fecha = now()->subDays(90);
Pedido::where('estado', 'cancelado')
    ->where('created_at', '<', $fecha)
    ->delete();
exit

# Verificar integridad de stock
php artisan tinker
$productos = Producto::where('stock', '<', 0)->get();
foreach ($productos as $producto) {
    echo "⚠️ Producto {$producto->nombre} tiene stock negativo: {$producto->stock}\n";
}
exit

# Corregir stock negativo
php artisan tinker
Producto::where('stock', '<', 0)->update(['stock' => 0]);
exit
```

## 📱 PWA - Comandos Service Worker

```javascript
// En la consola del navegador (F12)

// Ver si Service Worker está registrado
navigator.serviceWorker.getRegistrations().then(registrations => {
    console.log('Service Workers:', registrations.length);
    registrations.forEach(reg => console.log(reg));
});

// Desregistrar Service Worker (para testing)
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.unregister());
});

// Ver cache de PWA
caches.keys().then(keys => {
    console.log('Caches:', keys);
    keys.forEach(key => {
        caches.open(key).then(cache => {
            cache.keys().then(requests => {
                console.log(`Cache ${key}:`, requests.length, 'items');
            });
        });
    });
});

// Limpiar cache específico
caches.delete('arepa-llanerita-v1');

// Verificar si está instalado como PWA
window.matchMedia('(display-mode: standalone)').matches // true si está instalado
```

## 🎨 Generar Assets

```bash
# Compilar assets con Vite (si usas)
npm run build

# Compilar en modo desarrollo con watch
npm run dev

# Verificar assets
dir public\css\pages
dir public\js\pages
```

## 📊 Estadísticas Rápidas

```bash
php artisan tinker

// Total de pedidos
Pedido::count();

// Pedidos por estado
Pedido::selectRaw('estado, count(*) as total')
    ->groupBy('estado')
    ->get();

// Ventas del día
Pedido::whereDate('created_at', today())
    ->whereIn('estado', ['confirmado', 'entregado'])
    ->sum('total_final');

// Top 5 productos más vendidos
$topProductos = [];
$pedidos = Pedido::where('estado', 'entregado')->get();
foreach ($pedidos as $pedido) {
    foreach ($pedido->detalles as $detalle) {
        $key = $detalle['producto_data']['nombre'];
        if (!isset($topProductos[$key])) {
            $topProductos[$key] = 0;
        }
        $topProductos[$key] += $detalle['cantidad'];
    }
}
arsort($topProductos);
array_slice($topProductos, 0, 5, true);

exit
```

## 🆘 Solución de Problemas Comunes

### Error: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Error: MongoDB connection failed
```bash
# Verificar que MongoDB está corriendo
net start MongoDB

# Verificar conexión
php artisan tinker
DB::connection('mongodb')->getPdo();
exit
```

### Error: Route not found
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

### Error: View not found
```bash
php artisan view:clear
php artisan config:clear
```

### Assets no se cargan
```bash
# Verificar permisos
icacls public\css /grant Everyone:F /t
icacls public\js /grant Everyone:F /t

# Regenerar assets
npm run build

# Verificar cache busting
php artisan view:clear
```

---

## 📝 Notas Finales

- **Producción:** Siempre usar `php artisan optimize` antes de desplegar
- **Desarrollo:** Mantener `php artisan optimize:clear` activo
- **Logs:** Revisar `storage/logs/laravel.log` diariamente
- **Backups:** Hacer backup de MongoDB semanalmente
- **Testing:** Ejecutar tests antes de cada commit importante

---

**¡Todos estos comandos están listos para usar!** 🚀
