<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si es administrador, permitir acceso a todo
        if ($user->esAdmin()) {
            return $next($request);
        }

        // Verificar si el usuario tiene al menos uno de los permisos requeridos
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}