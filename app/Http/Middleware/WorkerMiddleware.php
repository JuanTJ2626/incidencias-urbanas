<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $rol = strtolower(trim(Auth::user()->rol ?? ''));

        if ($rol !== 'trabajador' && $rol !== 'worker' && $rol !== 'contratista') {
            return redirect('/home')->with('error', 'Acceso denegado.');
        }

        if (!(bool) Auth::user()->activo) {
            Auth::logout();
            return redirect('/login')->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
        }

        return $next($request);
    }
}
