<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarSesionAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('siic01_admin')) {
            return redirect()->away('https://siic01.ugel01.gob.pe/');
        }

        return $next($request);
    }
}