<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapacitacionController extends Controller
{
    public function index()
    {
        $lider = Auth::user();

        // Obtener miembros del equipo (usuarios referidos por el líder)
        $equipo = User::where('referido_por', $lider->id)
                     ->where('rol', '!=', 'administrador')
                     ->get();

        // Módulos de capacitación disponibles
        $modulos = [
            [
                'id' => 1,
                'titulo' => 'Técnicas de Ventas Efectivas',
                'descripcion' => 'Aprende las mejores técnicas para cerrar ventas exitosamente',
                'duracion' => '2 horas',
                'nivel' => 'Básico',
                'icono' => 'bi-graph-up',
                'completado' => false
            ],
            [
                'id' => 2,
                'titulo' => 'Manejo de Objeciones',
                'descripcion' => 'Cómo manejar las objeciones más comunes de los clientes',
                'duracion' => '1.5 horas',
                'nivel' => 'Intermedio',
                'icono' => 'bi-chat-dots',
                'completado' => true
            ],
            [
                'id' => 3,
                'titulo' => 'Construcción de Relaciones con Clientes',
                'descripcion' => 'Estrategias para mantener relaciones duraderas',
                'duracion' => '3 horas',
                'nivel' => 'Avanzado',
                'icono' => 'bi-people-fill',
                'completado' => false
            ],
            [
                'id' => 4,
                'titulo' => 'Liderazgo de Equipos de Ventas',
                'descripcion' => 'Cómo liderar y motivar tu equipo efectivamente',
                'duracion' => '4 horas',
                'nivel' => 'Avanzado',
                'icono' => 'bi-person-badge',
                'completado' => false
            ]
        ];

        // Progreso del equipo (simulado)
        $progresoEquipo = [];
        foreach ($equipo as $miembro) {
            $progresoEquipo[] = [
                'miembro' => $miembro,
                'modulos_completados' => rand(1, 3),
                'total_modulos' => 4,
                'ultimo_modulo' => $modulos[rand(0, 3)]['titulo'],
                'fecha_ultimo' => now()->subDays(rand(1, 30))
            ];
        }

        return view('lider.capacitacion.index', compact('modulos', 'equipo', 'progresoEquipo'));
    }

    public function show($id)
    {
        // Mostrar detalles de un módulo específico
        $modulo = [
            'id' => $id,
            'titulo' => 'Técnicas de Ventas Efectivas',
            'descripcion' => 'Este módulo te enseñará las mejores prácticas...',
            'duracion' => '2 horas',
            'nivel' => 'Básico',
            'contenido' => [
                'Introducción a las ventas',
                'Identificación de necesidades',
                'Presentación de productos',
                'Cierre de ventas'
            ]
        ];

        return view('lider.capacitacion.show', compact('modulo'));
    }

    public function asignar(Request $request)
    {
        $request->validate([
            'miembro_ids' => 'required|array',
            'miembro_ids.*' => 'exists:users,id',
            'modulo_id' => 'required|integer'
        ]);

        // Aquí se asignaría el módulo a los miembros seleccionados
        // En una implementación real, esto se guardaría en la base de datos

        return redirect()->back()->with('success', 'Módulo asignado correctamente al equipo.');
    }
}
