<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Capacitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapacitacionController extends Controller
{
    public function index()
    {
        $lider = Auth::user();

        // Obtener miembros del equipo
        $equipo = User::where('referido_por', $lider->id)
                     ->where('rol', '!=', 'administrador')
                     ->get();

        // Obtener capacitaciones del líder
        $capacitaciones = Capacitacion::porLider($lider->id);

        // Calcular estadísticas
        $stats = [
            'total_modulos' => $capacitaciones->count(),
            'total_miembros' => $equipo->count(),
            'total_asignaciones' => $capacitaciones->sum(function($cap) {
                return isset($cap->asignaciones) ? count($cap->asignaciones) : 0;
            }),
        ];

        // Calcular progreso del equipo
        $progresoEquipo = [];
        foreach ($equipo as $miembro) {
            $completados = 0;
            $ultimaCapacitacion = null;
            $fechaUltima = null;

            foreach ($capacitaciones as $cap) {
                if ($cap->completadaPor($miembro->id)) {
                    $completados++;
                    if (isset($cap->asignaciones)) {
                        foreach ($cap->asignaciones as $asig) {
                            if ($asig['vendedor_id'] == $miembro->id && isset($asig['fecha_completado'])) {
                                if (!$fechaUltima || $asig['fecha_completado'] > $fechaUltima) {
                                    $fechaUltima = $asig['fecha_completado'];
                                    $ultimaCapacitacion = $cap->titulo;
                                }
                            }
                        }
                    }
                }
            }

            $progresoEquipo[] = [
                'miembro' => $miembro,
                'modulos_completados' => $completados,
                'total_modulos' => $capacitaciones->count(),
                'ultimo_modulo' => $ultimaCapacitacion ?? 'Ninguno',
                'fecha_ultimo' => $fechaUltima ?? now()
            ];
        }

        return view('lider.capacitacion.index', compact('capacitaciones', 'equipo', 'progresoEquipo', 'stats'));
    }

    public function create()
    {
        return view('lider.capacitacion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'contenido' => 'required|string',
            'duracion' => 'required|string|max:100',
            'nivel' => 'required|in:basico,intermedio,avanzado',
            'categoria' => 'nullable|string|max:100',
            'icono' => 'nullable|string|max:100',
            'video_url' => 'nullable|url',
            'imagen_url' => 'nullable|url',
            'objetivos' => 'nullable|array',
            'objetivos.*' => 'string',
            'recursos' => 'nullable|array'
        ]);

        $lider = Auth::user();

        // Obtener el último orden
        $ultimoOrden = Capacitacion::where('lider_id', $lider->id)->max('orden') ?? 0;

        $capacitacion = Capacitacion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'contenido' => $request->contenido,
            'duracion' => $request->duracion,
            'nivel' => $request->nivel,
            'categoria' => $request->categoria ?? 'General',
            'icono' => $request->icono ?? 'bi-book',
            'objetivos' => $request->objetivos ?? [],
            'recursos' => $request->recursos ?? [],
            'video_url' => $request->video_url,
            'imagen_url' => $request->imagen_url,
            'orden' => $ultimoOrden + 1,
            'activo' => true,
            'lider_id' => $lider->id,
            'asignaciones' => []
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Capacitación creada exitosamente',
                'data' => $capacitacion
            ]);
        }

        return redirect()->route('lider.capacitacion.index')
                        ->with('success', 'Capacitación creada exitosamente.');
    }

    public function show($id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $lider = Auth::user();

        // Verificar que el líder es el propietario
        if ($capacitacion->lider_id != $lider->id) {
            abort(403, 'No autorizado');
        }

        // Obtener miembros del equipo
        $equipo = User::where('referido_por', $lider->id)
                     ->where('rol', '!=', 'administrador')
                     ->get();

        // Obtener progreso de miembros asignados
        $progreso = [];
        if (isset($capacitacion->asignaciones)) {
            foreach ($capacitacion->asignaciones as $asignacion) {
                $miembro = User::find($asignacion['vendedor_id']);
                if ($miembro) {
                    $progreso[] = [
                        'miembro' => $miembro,
                        'progreso' => $asignacion['progreso'] ?? 0,
                        'completado' => $asignacion['completado'] ?? false,
                        'fecha_asignacion' => $asignacion['fecha_asignacion'] ?? null,
                        'fecha_inicio' => $asignacion['fecha_inicio'] ?? null,
                        'fecha_completado' => $asignacion['fecha_completado'] ?? null
                    ];
                }
            }
        }

        return view('lider.capacitacion.show', compact('capacitacion', 'equipo', 'progreso'));
    }

    public function edit($id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $lider = Auth::user();

        // Verificar que el líder es el propietario
        if ($capacitacion->lider_id != $lider->id) {
            abort(403, 'No autorizado');
        }

        return view('lider.capacitacion.edit', compact('capacitacion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'contenido' => 'required|string',
            'duracion' => 'required|string|max:100',
            'nivel' => 'required|in:basico,intermedio,avanzado',
            'categoria' => 'nullable|string|max:100',
            'icono' => 'nullable|string|max:100',
            'video_url' => 'nullable|url',
            'imagen_url' => 'nullable|url',
            'objetivos' => 'nullable|array',
            'objetivos.*' => 'string',
            'recursos' => 'nullable|array'
        ]);

        $capacitacion = Capacitacion::findOrFail($id);
        $lider = Auth::user();

        // Verificar que el líder es el propietario
        if ($capacitacion->lider_id != $lider->id) {
            abort(403, 'No autorizado');
        }

        $capacitacion->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'contenido' => $request->contenido,
            'duracion' => $request->duracion,
            'nivel' => $request->nivel,
            'categoria' => $request->categoria ?? 'General',
            'icono' => $request->icono ?? 'bi-book',
            'objetivos' => $request->objetivos ?? [],
            'recursos' => $request->recursos ?? [],
            'video_url' => $request->video_url,
            'imagen_url' => $request->imagen_url
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Capacitación actualizada exitosamente',
                'data' => $capacitacion
            ]);
        }

        return redirect()->route('lider.capacitacion.index')
                        ->with('success', 'Capacitación actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $lider = Auth::user();

        // Verificar que el líder es el propietario
        if ($capacitacion->lider_id != $lider->id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }
            abort(403, 'No autorizado');
        }

        $capacitacion->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Capacitación eliminada exitosamente'
            ]);
        }

        return redirect()->route('lider.capacitacion.index')
                        ->with('success', 'Capacitación eliminada exitosamente.');
    }

    public function asignar(Request $request)
    {
        $request->validate([
            'miembro_ids' => 'required|array',
            'miembro_ids.*' => 'exists:users,_id',
            'modulo_id' => 'required'
        ]);

        $capacitacion = Capacitacion::findOrFail($request->modulo_id);
        $lider = Auth::user();

        // Verificar que el líder es el propietario
        if ($capacitacion->lider_id != $lider->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }
            return redirect()->back()->with('error', 'No autorizado');
        }

        // Asignar la capacitación a los miembros
        $capacitacion->asignarA($request->miembro_ids);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Módulo asignado correctamente al equipo'
            ]);
        }

        return redirect()->back()->with('success', 'Módulo asignado correctamente al equipo.');
    }

    public function actualizarProgreso(Request $request, $id)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:users,_id',
            'progreso' => 'required|integer|min:0|max:100'
        ]);

        $capacitacion = Capacitacion::findOrFail($id);
        $capacitacion->actualizarProgreso($request->vendedor_id, $request->progreso);

        return response()->json([
            'success' => true,
            'message' => 'Progreso actualizado correctamente'
        ]);
    }

    public function marcarCompletada(Request $request, $id)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:users,_id'
        ]);

        $capacitacion = Capacitacion::findOrFail($id);
        $capacitacion->marcarCompletada($request->vendedor_id);

        return response()->json([
            'success' => true,
            'message' => 'Capacitación marcada como completada'
        ]);
    }
}
