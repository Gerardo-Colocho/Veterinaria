<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado vía Sanctum/Web y si su rol es admin o veterinario
        if ($request->user() && in_array($request->user()->rol, ['admin', 'veterinario'])) {
            return $next($request);
        }

        // Si no cumple, redirigir al login
        return redirect()->route('login')->with('error', 'Acceso denegado. Se requieren permisos de administrador.');
    }
}