<?php
// app/Http/Middleware/GestionnaireMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GestionnaireMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isGestionnaire()) {
            abort(403, 'Accès réservé aux gestionnaires.');
        }

        return $next($request);
    }
}
