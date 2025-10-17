<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
// use App\Models\Meta; // Modelo Meta no existe aún
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MetaController extends Controller
{
    public function index()
    {
        $lider = Auth::user();

        // Obtener miembros del equipo (usuarios referidos por el líder)
        $equipo = User::where('referido_por', $lider->id)
                     ->where('rol', '!=', 'administrador')
                     ->get();

        // Calcular métricas del mes actual
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        // Calcular progreso individual
        $progreso = [];
        foreach ($equipo as $miembro) {
            $ventasMes = to_float(Pedido::where('vendedor_id', $miembro->id)
                               ->whereBetween('created_at', [$inicioMes, $finMes])
                               ->sum('total_final'));

            $metaMensual = to_float($miembro->meta_mensual ?? 0);

            $progreso[] = [
                'miembro' => $miembro,
                'meta_actual' => $metaMensual,
                'ventas_mes' => $ventasMes,
                'porcentaje' => $metaMensual > 0 ? ($ventasMes / $metaMensual) * 100 : 0,
                'diferencia' => $ventasMes - $metaMensual
            ];
        }

        // Métricas del equipo
        $metaEquipo = to_float($equipo->sum('meta_mensual'));
        $ventasEquipo = array_sum(array_column($progreso, 'ventas_mes'));
        $progresoEquipo = $metaEquipo > 0 ? ($ventasEquipo / $metaEquipo) * 100 : 0;

        // Historial de metas (últimos 6 meses)
        $historialMetas = $this->obtenerHistorialMetas($equipo);

        return view('lider.metas.index', compact(
            'equipo',
            'progreso',
            'metaEquipo',
            'ventasEquipo',
            'progresoEquipo',
            'historialMetas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'miembro_id' => 'required|string',
            'meta_mensual' => 'required|numeric|min:0',
            'mes' => 'required|date_format:Y-m',
            'notas' => 'nullable|string|max:500'
        ]);

        // Verificar que el miembro pertenece al equipo del líder
        $lider = Auth::user();
        $miembro = User::where('id', $request->miembro_id)
                      ->where('referido_por', $lider->id)
                      ->first();

        if (!$miembro) {
            return redirect()->back()->with('error', 'No tienes permisos para asignar metas a este usuario.');
        }

        // Actualizar la meta mensual del usuario
        $miembro->update([
            'meta_mensual' => $request->meta_mensual
        ]);

        // Crear registro en tabla de metas (si existe)
        // TODO: Implementar modelo Meta cuando sea necesario
        /*
        try {
            Meta::updateOrCreate([
                'user_id' => $request->miembro_id,
                'periodo' => $request->mes . '-01'
            ], [
                'meta_ventas' => $request->meta_mensual,
                'notas' => $request->notas,
                'asignado_por' => $lider->id
            ]);
        } catch (\Exception $e) {
            // Si no existe la tabla Meta, solo actualizamos el usuario
        }
        */

        return redirect()->back()->with('success', 'Meta asignada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'meta_mensual' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:500'
        ]);

        // Verificar que el miembro pertenece al equipo del líder
        $lider = Auth::user();
        $miembro = User::where('id', $id)
                      ->where('referido_por', $lider->id)
                      ->first();

        if (!$miembro) {
            return redirect()->back()->with('error', 'No tienes permisos para modificar este usuario.');
        }

        // Actualizar meta
        $miembro->update([
            'meta_mensual' => $request->meta_mensual
        ]);

        // Actualizar registro de meta si existe
        // TODO: Implementar modelo Meta cuando sea necesario
        /*
        try {
            $mesActual = Carbon::now()->format('Y-m') . '-01';
            Meta::updateOrCreate([
                'user_id' => $id,
                'periodo' => $mesActual
            ], [
                'meta_ventas' => $request->meta_mensual,
                'notas' => $request->notas,
                'asignado_por' => $lider->id
            ]);
        } catch (\Exception $e) {
            // Si no existe la tabla Meta, continuamos
        }
        */

        return redirect()->back()->with('success', 'Meta actualizada correctamente.');
    }

    private function obtenerHistorialMetas($equipo)
    {
        $historial = [];
        $equipoIds = $equipo->pluck('id');

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);

            $ventas = to_float(Pedido::whereIn('vendedor_id', $equipoIds)
                            ->whereYear('created_at', $mes->year)
                            ->whereMonth('created_at', $mes->month)
                            ->sum('total_final'));

            // Meta del equipo para ese mes (asumimos que la meta actual se mantiene)
            $metaMes = to_float($equipo->sum('meta_mensual'));

            $historial[] = [
                'mes' => $mes->format('M Y'),
                'meta' => $metaMes,
                'ventas' => $ventas,
                'cumplimiento' => $metaMes > 0 ? ($ventas / $metaMes) * 100 : 0
            ];
        }

        return $historial;
    }

    public function asignarMetaEquipo(Request $request)
    {
        $request->validate([
            'meta_equipo' => 'required|numeric|min:0',
            'distribucion' => 'required|in:equitativa,proporcional,manual',
            'metas_individuales' => 'required_if:distribucion,manual|array',
            'metas_individuales.*' => 'required_if:distribucion,manual|numeric|min:0'
        ]);

        $lider = Auth::user();
        $equipo = User::where('referido_por', $lider->id)
                     ->where('rol', '!=', 'administrador')
                     ->get();

        $metaTotal = $request->meta_equipo;

        try {
            switch ($request->distribucion) {
                case 'equitativa':
                    $metaIndividual = $metaTotal / $equipo->count();
                    foreach ($equipo as $miembro) {
                        $miembro->update(['meta_mensual' => $metaIndividual]);
                    }
                    break;

                case 'proporcional':
                    // Basado en ventas del mes anterior
                    $mesAnterior = Carbon::now()->subMonth();
                    $ventasAnteriores = [];
                    $totalVentasAnteriores = 0;

                    foreach ($equipo as $miembro) {
                        $ventas = to_float(Pedido::where('vendedor_id', $miembro->id)
                                        ->whereYear('created_at', $mesAnterior->year)
                                        ->whereMonth('created_at', $mesAnterior->month)
                                        ->sum('total_final'));
                        $ventasAnteriores[$miembro->id] = $ventas;
                        $totalVentasAnteriores += $ventas;
                    }

                    foreach ($equipo as $miembro) {
                        if ($totalVentasAnteriores > 0) {
                            $proporcion = $ventasAnteriores[$miembro->id] / $totalVentasAnteriores;
                            $metaIndividual = $metaTotal * $proporcion;
                        } else {
                            $metaIndividual = $metaTotal / $equipo->count();
                        }
                        $miembro->update(['meta_mensual' => $metaIndividual]);
                    }
                    break;

                case 'manual':
                    foreach ($equipo as $miembro) {
                        if (isset($request->metas_individuales[$miembro->id])) {
                            $miembro->update(['meta_mensual' => $request->metas_individuales[$miembro->id]]);
                        }
                    }
                    break;
            }

            return redirect()->back()->with('success', 'Metas del equipo asignadas correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al asignar las metas: ' . $e->getMessage());
        }
    }
}
