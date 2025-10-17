<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MongoDB no requiere crear la estructura de colección como SQL
        // La colección se crea automáticamente al insertar el primer documento

        // Sin embargo, podemos crear índices para optimizar las consultas
        Schema::connection('mongodb')->table('capacitaciones', function ($collection) {
            // Índice en lider_id para búsquedas rápidas por líder
            $collection->index('lider_id');

            // Índice en orden para mantener el orden de las capacitaciones
            $collection->index('orden');

            // Índice en activo para filtrar capacitaciones activas
            $collection->index('activo');

            // Índice compuesto para búsquedas frecuentes
            $collection->index(['lider_id', 'activo', 'orden']);

            // Índice en asignaciones.vendedor_id para búsquedas de capacitaciones por vendedor
            $collection->index('asignaciones.vendedor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la colección completa
        Schema::connection('mongodb')->dropIfExists('capacitaciones');
    }
};
