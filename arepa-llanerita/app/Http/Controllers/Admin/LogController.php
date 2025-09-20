<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado. Solo administradores.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $date = $request->get('date', now()->format('Y-m-d'));
        $search = $request->get('search', '');

        // Obtener logs del sistema
        $logs = $this->getLogs($filter, $date, $search);

        // Estadísticas
        $stats = [
            'total_logs' => count($logs),
            'errors_today' => $this->countLogsByLevel('error', $date),
            'warnings_today' => $this->countLogsByLevel('warning', $date),
            'info_today' => $this->countLogsByLevel('info', $date),
            'log_files_count' => $this->getLogFilesCount(),
            'total_size' => $this->getLogsTotalSize()
        ];

        // Actividad reciente (últimos 24 horas)
        $recentActivity = $this->getRecentActivity();

        // Resumen por niveles
        $levelSummary = $this->getLevelSummary($date);

        return view('admin.logs.index', compact(
            'logs',
            'stats',
            'recentActivity',
            'levelSummary',
            'filter',
            'date',
            'search'
        ));
    }

    public function show($filename)
    {
        $logPath = storage_path('logs/' . $filename);

        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Archivo de log no encontrado');
        }

        $content = File::get($logPath);
        $size = File::size($logPath);
        $lastModified = Carbon::createFromTimestamp(File::lastModified($logPath));

        return view('admin.logs.show', compact('filename', 'content', 'size', 'lastModified'));
    }

    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);

        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Archivo de log no encontrado');
        }

        return response()->download($logPath);
    }

    public function delete($filename)
    {
        try {
            $logPath = storage_path('logs/' . $filename);

            if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo de log no encontrado'
                ], 404);
            }

            // No permitir eliminar el log actual
            if ($filename === 'laravel.log' || $filename === 'laravel-' . now()->format('Y-m-d') . '.log') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el archivo de log actual'
                ], 400);
            }

            File::delete($logPath);

            return response()->json([
                'success' => true,
                'message' => 'Archivo de log eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clear()
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (File::exists($logPath)) {
                File::put($logPath, '');
            }

            return response()->json([
                'success' => true,
                'message' => 'Log principal limpiado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        try {
            $days = $request->input('days');
            $logsPath = storage_path('logs');
            $cutoffDate = Carbon::now()->subDays($days);

            $deleted = 0;
            $totalSize = 0;

            if (File::exists($logsPath)) {
                $files = File::files($logsPath);

                foreach ($files as $file) {
                    $filename = $file->getFilename();

                    // Skip current log file
                    if ($filename === 'laravel.log') {
                        continue;
                    }

                    $fileDate = Carbon::createFromTimestamp($file->getMTime());

                    if ($fileDate->lt($cutoffDate) && str_ends_with($filename, '.log')) {
                        $totalSize += $file->getSize();
                        File::delete($file->getPathname());
                        $deleted++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} archivos de log antiguos",
                'deleted_count' => $deleted,
                'space_freed' => $this->formatBytes($totalSize)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'level' => 'nullable|in:emergency,alert,critical,error,warning,notice,info,debug'
        ]);

        try {
            $startDate = Carbon::parse($request->input('start_date'));
            $endDate = Carbon::parse($request->input('end_date'));
            $level = $request->input('level');

            $logs = $this->getLogsForExport($startDate, $endDate, $level);

            $filename = 'logs_export_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.txt';
            $content = $this->formatLogsForExport($logs);

            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $lastWeek = now()->subWeek()->format('Y-m-d');

        $stats = [
            'today' => [
                'total' => $this->countLogsByDate($today),
                'errors' => $this->countLogsByLevel('error', $today),
                'warnings' => $this->countLogsByLevel('warning', $today),
                'info' => $this->countLogsByLevel('info', $today)
            ],
            'yesterday' => [
                'total' => $this->countLogsByDate($yesterday),
                'errors' => $this->countLogsByLevel('error', $yesterday),
                'warnings' => $this->countLogsByLevel('warning', $yesterday),
                'info' => $this->countLogsByLevel('info', $yesterday)
            ],
            'last_week' => [
                'total' => $this->countLogsInRange($lastWeek, $today),
                'errors' => $this->countLogsByLevelInRange('error', $lastWeek, $today),
                'warnings' => $this->countLogsByLevelInRange('warning', $lastWeek, $today),
                'info' => $this->countLogsByLevelInRange('info', $lastWeek, $today)
            ],
            'files' => $this->getLogFilesInfo(),
            'system_health' => $this->getSystemHealth()
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    // Métodos privados auxiliares

    private function getLogs($filter = 'all', $date = null, $search = '')
    {
        $logs = [];
        $logPath = storage_path('logs');

        if (!File::exists($logPath)) {
            return $logs;
        }

        $files = collect(File::files($logPath))
            ->filter(function ($file) {
                return str_ends_with($file->getFilename(), '.log');
            })
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                $logEntry = $this->parseLogLine($line);
                if (!$logEntry) continue;

                // Filtrar por fecha
                if ($date && !str_contains($logEntry['date'], $date)) {
                    continue;
                }

                // Filtrar por nivel
                if ($filter !== 'all' && strtolower($logEntry['level']) !== strtolower($filter)) {
                    continue;
                }

                // Filtrar por búsqueda
                if ($search && !str_contains(strtolower($logEntry['message']), strtolower($search))) {
                    continue;
                }

                $logEntry['file'] = $file->getFilename();
                $logs[] = $logEntry;
            }
        }

        // Ordenar por fecha más reciente
        usort($logs, function($a, $b) {
            return strtotime($b['datetime']) - strtotime($a['datetime']);
        });

        // Limitar a 1000 entradas para performance
        return array_slice($logs, 0, 1000);
    }

    private function parseLogLine($line)
    {
        // Regex para parsear líneas de log Laravel
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?\.(\w+): (.*)/', $line, $matches)) {
            return [
                'datetime' => $matches[1],
                'date' => substr($matches[1], 0, 10),
                'time' => substr($matches[1], 11),
                'level' => strtolower($matches[2]),
                'message' => trim($matches[3]),
                'raw' => $line
            ];
        }

        return null;
    }

    private function countLogsByLevel($level, $date)
    {
        $logs = $this->getLogs($level, $date);
        return count($logs);
    }

    private function countLogsByDate($date)
    {
        $logs = $this->getLogs('all', $date);
        return count($logs);
    }

    private function countLogsInRange($startDate, $endDate)
    {
        // Implementación simplificada
        $count = 0;
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($currentDate->lte($end)) {
            $count += $this->countLogsByDate($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        return $count;
    }

    private function countLogsByLevelInRange($level, $startDate, $endDate)
    {
        $count = 0;
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($currentDate->lte($end)) {
            $count += $this->countLogsByLevel($level, $currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        return $count;
    }

    private function getLogFilesCount()
    {
        $logPath = storage_path('logs');

        if (!File::exists($logPath)) {
            return 0;
        }

        return count(File::files($logPath));
    }

    private function getLogsTotalSize()
    {
        $logPath = storage_path('logs');
        $totalSize = 0;

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }
        }

        return $this->formatBytes($totalSize);
    }

    private function getRecentActivity()
    {
        $logs = $this->getLogs('all', now()->format('Y-m-d'));

        return array_slice($logs, 0, 10);
    }

    private function getLevelSummary($date)
    {
        $levels = ['error', 'warning', 'info', 'debug'];
        $summary = [];

        foreach ($levels as $level) {
            $summary[$level] = $this->countLogsByLevel($level, $date);
        }

        return $summary;
    }

    private function getLogFilesInfo()
    {
        $logPath = storage_path('logs');
        $files = [];

        if (File::exists($logPath)) {
            $logFiles = File::files($logPath);

            foreach ($logFiles as $file) {
                $files[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'size_bytes' => $file->getSize(),
                    'modified' => Carbon::createFromTimestamp($file->getMTime())->format('d/m/Y H:i:s')
                ];
            }
        }

        return $files;
    }

    private function getSystemHealth()
    {
        $health = [
            'status' => 'good',
            'issues' => []
        ];

        // Verificar errores recientes
        $recentErrors = $this->countLogsByLevel('error', now()->format('Y-m-d'));
        if ($recentErrors > 10) {
            $health['status'] = 'warning';
            $health['issues'][] = "Se encontraron {$recentErrors} errores hoy";
        }

        // Verificar tamaño de logs
        $totalSizeBytes = 0;
        $logPath = storage_path('logs');
        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                $totalSizeBytes += $file->getSize();
            }
        }

        if ($totalSizeBytes > 100 * 1024 * 1024) { // 100MB
            $health['status'] = 'warning';
            $health['issues'][] = 'Los logs ocupan más de 100MB de espacio';
        }

        return $health;
    }

    private function getLogsForExport($startDate, $endDate, $level = null)
    {
        // Implementación simplificada para exportar logs
        $logs = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dailyLogs = $this->getLogs($level, $currentDate->format('Y-m-d'));
            $logs = array_merge($logs, $dailyLogs);
            $currentDate->addDay();
        }

        return $logs;
    }

    private function formatLogsForExport($logs)
    {
        $content = "# Exportación de Logs del Sistema\n";
        $content .= "# Generado el: " . now()->format('d/m/Y H:i:s') . "\n";
        $content .= "# Total de entradas: " . count($logs) . "\n\n";

        foreach ($logs as $log) {
            $content .= "[{$log['datetime']}] {$log['level']}: {$log['message']}\n";
        }

        return $content;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}