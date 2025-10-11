<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use App\Services\CacheService;
use App\Services\OptimizedQueryService;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected $cacheService;
    protected $queryService;

    public function __construct()
    {
        $this->cacheService = new CacheService();
        $this->queryService = new OptimizedQueryService();
    }

    /**
     * Standard success response
     */
    protected function successResponse($data = null, $message = 'Operación exitosa', $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Standard error response
     */
    protected function errorResponse($message = 'Error en la operación', $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Handle pagination
     */
    protected function paginateResults($query, Request $request, $perPage = 15)
    {
        $page = $request->get('page', 1);
        $perPage = min($request->get('per_page', $perPage), 100); // Max 100 items per page

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Apply common filters
     */
    protected function applyDateFilters($query, Request $request, $dateField = 'created_at')
    {
        if ($request->has('fecha_inicio')) {
            $query->whereDate($dateField, '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin')) {
            $query->whereDate($dateField, '<=', $request->fecha_fin);
        }

        return $query;
    }

    /**
     * Apply search filters
     */
    protected function applySearchFilters($query, Request $request, array $searchFields = ['name'])
    {
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;

            $query->where(function($q) use ($searchFields, $searchTerm) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            });
        }

        return $query;
    }

    /**
     * Get user statistics
     */
    protected function getUserStats($userId)
    {
        return $this->cacheService->cacheUserStats($userId, function() use ($userId) {
            return $this->calculateUserStats($userId);
        });
    }

    /**
     * Calculate user statistics
     */
    protected function calculateUserStats($userId)
    {
        $user = auth()->user();

        $stats = [
            'ventas_mes_actual' => 0,
            'comisiones_mes_actual' => 0,
            'referidos_activos' => 0,
            'pedidos_pendientes' => 0,
        ];

        if ($user->puedeVender()) {
            $pedidosEsteMes = \App\Models\Pedido::where('vendedor_id', $userId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->get();

            $stats['ventas_mes_actual'] = $pedidosEsteMes->where('estado', 'entregado')->sum('total_final');
            $stats['comisiones_mes_actual'] = $stats['ventas_mes_actual'] * 0.1;
            $stats['pedidos_pendientes'] = $pedidosEsteMes->whereNotIn('estado', ['entregado', 'cancelado'])->count();
        }

        if ($user->tieneReferidos()) {
            $stats['referidos_activos'] = \App\Models\Referido::where('referidor_id', $userId)
                ->where('estado', 'activo')
                ->count();
        }

        return $stats;
    }

    /**
     * Validate date range
     */
    protected function validateDateRange(Request $request, $maxDays = 365)
    {
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');

        if ($fechaInicio && $fechaFin) {
            $inicio = \Carbon\Carbon::parse($fechaInicio);
            $fin = \Carbon\Carbon::parse($fechaFin);

            if ($fin->lt($inicio)) {
                return 'La fecha de fin debe ser posterior a la fecha de inicio';
            }

            if ($inicio->diffInDays($fin) > $maxDays) {
                return "El rango de fechas no puede exceder {$maxDays} días";
            }
        }

        return null;
    }

    /**
     * Log user activity
     */
    protected function logActivity($action, $model = null, $details = null)
    {
        try {
            \App\Models\Auditoria::create([
                'usuario_id' => auth()->id(),
                'accion' => $action,
                'tabla' => $model ? get_class($model) : null,
                'registro_id' => $model ? $model->id : null,
                'detalles' => $details,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Check user permissions
     */
    protected function checkPermission($permission)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'No autenticado');
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }
    }

    /**
     * Get common dashboard data
     */
    protected function getDashboardData($userRole, $userId)
    {
        return $this->cacheService->cacheDashboardData($userRole, $userId, function() use ($userRole, $userId) {
            $data = [
                'stats' => $this->calculateUserStats($userId),
                'notifications' => $this->getRecentNotifications($userId),
                'recent_activity' => $this->getRecentActivity($userId)
            ];

            // Role-specific data
            switch ($userRole) {
                case 'administrador':
                    $data['system_stats'] = $this->getSystemStats();
                    break;
                case 'lider':
                    $data['team_stats'] = $this->getTeamStats($userId);
                    break;
                case 'vendedor':
                    $data['sales_stats'] = $this->getSalesStats($userId);
                    break;
            }

            return $data;
        });
    }

    /**
     * Get recent notifications
     */
    protected function getRecentNotifications($userId, $limit = 5)
    {
        return \App\Models\Notificacion::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity
     */
    protected function getRecentActivity($userId, $limit = 10)
    {
        return \App\Models\Auditoria::where('usuario_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get system statistics (admin only)
     */
    protected function getSystemStats()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('activo', true)->count(),
            'total_orders' => \App\Models\Pedido::count(),
            'monthly_revenue' => \App\Models\Pedido::where('estado', 'entregado')
                ->whereMonth('created_at', now()->month)
                ->sum('total_final')
        ];
    }

    /**
     * Get team statistics (leader only)
     */
    protected function getTeamStats($userId)
    {
        $team = \App\Models\Referido::where('referidor_id', $userId)
            ->where('estado', 'activo')
            ->with('referido')
            ->get();

        return [
            'team_size' => $team->count(),
            'team_sales' => $team->sum(function($referido) {
                return $referido->referido->ventas_totales ?? 0;
            }),
            'team_active' => $team->where('referido.activo', true)->count()
        ];
    }

    /**
     * Get sales statistics (vendor only)
     */
    protected function getSalesStats($userId)
    {
        $pedidos = \App\Models\Pedido::where('vendedor_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->get();

        return [
            'monthly_orders' => $pedidos->count(),
            'monthly_sales' => $pedidos->where('estado', 'entregado')->sum('total_final'),
            'pending_orders' => $pedidos->whereNotIn('estado', ['entregado', 'cancelado'])->count(),
            'conversion_rate' => $pedidos->count() > 0
                ? ($pedidos->where('estado', 'entregado')->count() / $pedidos->count()) * 100
                : 0
        ];
    }
}