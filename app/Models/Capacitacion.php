<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class Capacitacion extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'capacitaciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'contenido',
        'duracion',
        'nivel',
        'categoria',
        'icono',
        'objetivos',
        'recursos',
        'video_url',
        'imagen_url',
        'orden',
        'activo',
        'lider_id',
        'asignaciones',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'objetivos' => 'array',
        'recursos' => 'array',
        'asignaciones' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con el líder que creó la capacitación
    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    // Obtener capacitaciones por líder
    public static function porLider($liderId)
    {
        return self::where('lider_id', $liderId)
                   ->where('activo', true)
                   ->orderBy('orden', 'asc')
                   ->get();
    }

    // Obtener capacitaciones asignadas a un vendedor
    public static function porVendedor($vendedorId)
    {
        return self::where('asignaciones.vendedor_id', $vendedorId)
                   ->where('activo', true)
                   ->get();
    }

    // Verificar si está completada por un vendedor
    public function completadaPor($vendedorId)
    {
        if (!isset($this->asignaciones) || !is_array($this->asignaciones)) {
            return false;
        }

        foreach ($this->asignaciones as $asignacion) {
            if ($asignacion['vendedor_id'] == $vendedorId && isset($asignacion['completado']) && $asignacion['completado']) {
                return true;
            }
        }

        return false;
    }

    // Obtener progreso de un vendedor
    public function progresoPor($vendedorId)
    {
        if (!isset($this->asignaciones) || !is_array($this->asignaciones)) {
            return 0;
        }

        foreach ($this->asignaciones as $asignacion) {
            if ($asignacion['vendedor_id'] == $vendedorId) {
                return $asignacion['progreso'] ?? 0;
            }
        }

        return 0;
    }

    // Asignar capacitación a vendedores
    public function asignarA($vendedorIds)
    {
        if (!is_array($vendedorIds)) {
            $vendedorIds = [$vendedorIds];
        }

        $asignaciones = $this->asignaciones ?? [];

        foreach ($vendedorIds as $vendedorId) {
            // Verificar si ya está asignado
            $existe = false;
            foreach ($asignaciones as &$asignacion) {
                if ($asignacion['vendedor_id'] == $vendedorId) {
                    $existe = true;
                    break;
                }
            }

            // Si no existe, agregarlo
            if (!$existe) {
                $asignaciones[] = [
                    'vendedor_id' => $vendedorId,
                    'fecha_asignacion' => Carbon::now(),
                    'progreso' => 0,
                    'completado' => false,
                    'fecha_inicio' => null,
                    'fecha_completado' => null
                ];
            }
        }

        $this->asignaciones = $asignaciones;
        $this->save();

        return true;
    }

    // Marcar como completada para un vendedor
    public function marcarCompletada($vendedorId)
    {
        $asignaciones = $this->asignaciones ?? [];

        foreach ($asignaciones as &$asignacion) {
            if ($asignacion['vendedor_id'] == $vendedorId) {
                $asignacion['completado'] = true;
                $asignacion['progreso'] = 100;
                $asignacion['fecha_completado'] = Carbon::now();
                break;
            }
        }

        $this->asignaciones = $asignaciones;
        $this->save();

        return true;
    }

    // Actualizar progreso de un vendedor
    public function actualizarProgreso($vendedorId, $progreso)
    {
        $asignaciones = $this->asignaciones ?? [];

        foreach ($asignaciones as &$asignacion) {
            if ($asignacion['vendedor_id'] == $vendedorId) {
                $asignacion['progreso'] = min(100, max(0, $progreso));

                if (!$asignacion['fecha_inicio']) {
                    $asignacion['fecha_inicio'] = Carbon::now();
                }

                if ($progreso >= 100) {
                    $asignacion['completado'] = true;
                    $asignacion['fecha_completado'] = Carbon::now();
                }
                break;
            }
        }

        $this->asignaciones = $asignaciones;
        $this->save();

        return true;
    }
}
