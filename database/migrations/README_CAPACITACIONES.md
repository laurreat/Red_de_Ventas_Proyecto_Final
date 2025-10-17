# Migración de Capacitaciones

Este documento explica cómo funciona la migración y seeder de capacitaciones en MongoDB.

## Estructura de Datos

La colección `capacitaciones` almacena módulos de capacitación que los líderes pueden crear y asignar a sus vendedores.

### Campos Principales

- `_id`: ID único del documento (ObjectId de MongoDB)
- `titulo`: Nombre del módulo de capacitación
- `descripcion`: Descripción breve del módulo
- `contenido`: Contenido detallado del módulo
- `duracion`: Tiempo estimado para completar (ej: "3 horas")
- `nivel`: Nivel de dificultad (Básico, Intermedio, Avanzado)
- `categoria`: Categoría del módulo (Ventas, Marketing, Producto, etc.)
- `icono`: Clase de ícono FontAwesome
- `objetivos`: Array de objetivos de aprendizaje
- `recursos`: Array de recursos disponibles
- `video_url`: URL del video de capacitación
- `imagen_url`: URL de la imagen representativa
- `orden`: Orden de presentación
- `activo`: Estado activo/inactivo (boolean)
- `lider_id`: ID del líder que creó el módulo
- `asignaciones`: Array de asignaciones a vendedores
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

### Estructura de Asignaciones

Cada elemento en el array `asignaciones` contiene:

```php
[
    'vendedor_id' => ObjectId,
    'fecha_asignacion' => DateTime,
    'progreso' => Integer (0-100),
    'completado' => Boolean,
    'fecha_inicio' => DateTime|null,
    'fecha_completado' => DateTime|null
]
```

## Proceso de Migración

### 1. Ejecutar la Migración

La migración crea los índices necesarios para optimizar las consultas:

```bash
php artisan migrate
```

Esto creará los siguientes índices:
- `lider_id`: Para búsquedas rápidas por líder
- `orden`: Para mantener el orden de las capacitaciones
- `activo`: Para filtrar capacitaciones activas
- Índice compuesto `[lider_id, activo, orden]`: Para consultas frecuentes
- `asignaciones.vendedor_id`: Para búsquedas de capacitaciones por vendedor

### 2. Ejecutar los Seeders

Para poblar la base de datos con datos iniciales:

```bash
# Ejecutar solo el seeder de capacitaciones
php artisan db:seed --class=CapacitacionSeeder

# O ejecutar todos los seeders (incluye capacitaciones)
php artisan db:seed
```

El seeder creará automáticamente 8 módulos de capacitación y los asignará al primer líder encontrado en la base de datos.

**IMPORTANTE**: El seeder NO elimina datos existentes. Verifica si ya existen capacitaciones con el mismo título para el mismo líder antes de crear nuevos registros. Esto permite ejecutar el seeder múltiples veces de forma segura.

### 3. Limpiar y Recrear SOLO Capacitaciones (Opcional)

Si deseas eliminar todas las capacitaciones y recrearlas:

```bash
# Desde tinker
php artisan tinker
> App\Models\Capacitacion::truncate();
> exit

# Luego ejecutar el seeder
php artisan db:seed --class=CapacitacionSeeder
```

### 4. Refrescar TODA la Base de Datos (¡Cuidado!)

⚠️ **ADVERTENCIA CRÍTICA**: El siguiente comando eliminará TODAS las colecciones y TODOS los datos en MongoDB:

```bash
php artisan migrate:fresh --seed
```

Solo usa este comando si:
- Estás en ambiente de desarrollo
- Quieres empezar completamente desde cero
- Has respaldado tus datos importantes

## Módulos de Capacitación Incluidos

1. **Técnicas de Venta Efectivas** (Básico - 3h)
2. **Atención al Cliente Excelente** (Intermedio - 2.5h)
3. **Conocimiento del Producto** (Básico - 4h)
4. **Estrategias de Prospección** (Intermedio - 3.5h)
5. **Negociación Avanzada** (Avanzado - 5h)
6. **Marketing Digital para Vendedores** (Intermedio - 3h)
7. **Gestión del Tiempo y Productividad** (Básico - 2h)
8. **Inteligencia Emocional en Ventas** (Avanzado - 2.5h)

## Verificación

Para verificar que la migración fue exitosa:

```bash
# Verificar colecciones en MongoDB
php artisan tinker
> DB::connection('mongodb')->getMongoDB()->listCollections();
> App\Models\Capacitacion::count();
> App\Models\Capacitacion::first();
```

## Rollback

Para revertir la migración:

```bash
php artisan migrate:rollback
```

Esto eliminará la colección `capacitaciones` y todos sus datos.

## Modelo Eloquent

El modelo `App\Models\Capacitacion` extiende de `MongoDB\Laravel\Eloquent\Model` y proporciona los siguientes métodos personalizados:

- `porLider($liderId)`: Obtener capacitaciones de un líder específico
- `porVendedor($vendedorId)`: Obtener capacitaciones asignadas a un vendedor
- `completadaPor($vendedorId)`: Verificar si un vendedor completó la capacitación
- `progresoPor($vendedorId)`: Obtener el progreso de un vendedor
- `asignarA($vendedorIds)`: Asignar la capacitación a vendedores
- `marcarCompletada($vendedorId)`: Marcar como completada para un vendedor
- `actualizarProgreso($vendedorId, $progreso)`: Actualizar el progreso de un vendedor

## Notas Importantes

1. **MongoDB vs SQL**: A diferencia de las migraciones SQL tradicionales, MongoDB no requiere definir la estructura de la colección de antemano. La colección se crea automáticamente al insertar el primer documento.

2. **Índices**: Los índices mejoran significativamente el rendimiento de las consultas. Se crean durante la migración.

3. **Lider ID**: El seeder automáticamente asigna las capacitaciones al primer líder encontrado. Si no hay líderes, el seeder se saltará.

4. **Preservación de Datos**: El seeder está diseñado para NO eliminar datos existentes. Verifica duplicados antes de insertar.

5. **Seguridad al Migrar**:
   - `php artisan migrate` - ✅ Seguro, solo crea índices, no borra datos
   - `php artisan db:seed` - ✅ Seguro, no borra datos existentes
   - `php artisan migrate:fresh` - ❌ PELIGROSO, borra TODAS las colecciones

6. **Conexión**: Asegúrate de que MongoDB esté corriendo y que las credenciales en el archivo `.env` sean correctas:
   ```
   DB_CONNECTION=mongodb
   MONGODB_HOST=127.0.0.1
   MONGODB_PORT=27017
   MONGODB_DATABASE=arepa_llanerita_mongo
   ```

## Solución de Problemas

### Error: "Class 'Jenssegers\Mongodb\Eloquent\Model' not found"

El paquete `jenssegers/mongodb` es obsoleto. Asegúrate de usar `mongodb/laravel-mongodb`:

```bash
composer require mongodb/laravel-mongodb
```

### No se ven las capacitaciones en la interfaz

Verifica que estés autenticado con un usuario que tenga el mismo `lider_id` que las capacitaciones creadas.

### MongoDB no está corriendo

En Windows, verifica que el servicio MongoDB esté activo:
```bash
net start MongoDB
```
