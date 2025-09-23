<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use MongoDB\Driver\Exception\ConnectionException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        // MongoDB connection errors
        $this->renderable(function (ConnectionException $e, $request) {
            \Log::error('MongoDB Connection Error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de conexión a la base de datos. Por favor intenta más tarde.',
                    'error_code' => 'DB_CONNECTION_ERROR'
                ], 503);
            }

            return response()->view('errors.database', ['exception' => $e], 503);
        });

        // Model not found errors
        $this->renderable(function (ModelNotFoundException $e, $request) {
            \Log::warning('Model Not Found: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recurso no encontrado.',
                    'error_code' => 'RESOURCE_NOT_FOUND'
                ], 404);
            }

            return response()->view('errors.404', ['exception' => $e], 404);
        });

        // Authentication errors
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado. Por favor inicia sesión.',
                    'error_code' => 'UNAUTHENTICATED'
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        });

        // Authorization errors
        $this->renderable(function (AuthorizationException $e, $request) {
            \Log::warning('Authorization Error: ' . $e->getMessage() . ' - User: ' . (auth()->id() ?? 'Guest'));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción.',
                    'error_code' => 'UNAUTHORIZED'
                ], 403);
            }

            return response()->view('errors.403', ['exception' => $e], 403);
        });

        // Validation errors
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos.',
                    'errors' => $e->errors(),
                    'error_code' => 'VALIDATION_ERROR'
                ], 422);
            }

            // Let Laravel handle validation errors normally for web requests
            return null;
        });

        // Method not allowed errors
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método HTTP no permitido.',
                    'error_code' => 'METHOD_NOT_ALLOWED'
                ], 405);
            }

            return response()->view('errors.405', ['exception' => $e], 405);
        });

        // 404 errors
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruta no encontrada.',
                    'error_code' => 'ROUTE_NOT_FOUND'
                ], 404);
            }

            return response()->view('errors.404', ['exception' => $e], 404);
        });

        // General HTTP errors
        $this->renderable(function (HttpException $e, $request) {
            $statusCode = $e->getStatusCode();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $this->getHttpErrorMessage($statusCode),
                    'error_code' => 'HTTP_ERROR_' . $statusCode
                ], $statusCode);
            }

            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", ['exception' => $e], $statusCode);
            }

            return response()->view('errors.generic', [
                'exception' => $e,
                'statusCode' => $statusCode
            ], $statusCode);
        });
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.',
                'error_code' => 'UNAUTHENTICATED'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Get HTTP error message
     */
    private function getHttpErrorMessage($statusCode)
    {
        return match ($statusCode) {
            400 => 'Solicitud incorrecta.',
            401 => 'No autenticado.',
            403 => 'Acceso denegado.',
            404 => 'Recurso no encontrado.',
            405 => 'Método no permitido.',
            409 => 'Conflicto en la solicitud.',
            422 => 'Datos no procesables.',
            429 => 'Demasiadas solicitudes.',
            500 => 'Error interno del servidor.',
            502 => 'Gateway no disponible.',
            503 => 'Servicio no disponible.',
            504 => 'Tiempo de espera agotado.',
            default => 'Error en el servidor.',
        };
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception)
    {
        // Don't report certain exceptions
        if ($this->shouldntReport($exception)) {
            return;
        }

        // Add context to logs
        $context = [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'input' => request()->except(['password', 'password_confirmation', '_token'])
        ];

        // Log based on exception type
        if ($exception instanceof ValidationException) {
            \Log::info('Validation Error', array_merge($context, [
                'errors' => $exception->errors()
            ]));
        } elseif ($exception instanceof AuthenticationException) {
            \Log::warning('Authentication Error', $context);
        } elseif ($exception instanceof AuthorizationException) {
            \Log::warning('Authorization Error', array_merge($context, [
                'message' => $exception->getMessage()
            ]));
        } elseif ($exception instanceof ModelNotFoundException) {
            \Log::warning('Model Not Found', array_merge($context, [
                'model' => $exception->getModel()
            ]));
        } else {
            \Log::error('Application Error: ' . $exception->getMessage(), array_merge($context, [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]));
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // In production, convert all unhandled exceptions to generic errors
        if (app()->environment('production') && !$this->isKnownException($exception)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ha ocurrido un error inesperado. Por favor intenta más tarde.',
                    'error_code' => 'UNEXPECTED_ERROR'
                ], 500);
            }

            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Check if exception is known/expected
     */
    private function isKnownException(Throwable $exception)
    {
        $knownExceptions = [
            ValidationException::class,
            AuthenticationException::class,
            AuthorizationException::class,
            ModelNotFoundException::class,
            NotFoundHttpException::class,
            MethodNotAllowedHttpException::class,
            HttpException::class,
            ConnectionException::class,
        ];

        foreach ($knownExceptions as $knownException) {
            if ($exception instanceof $knownException) {
                return true;
            }
        }

        return false;
    }
}