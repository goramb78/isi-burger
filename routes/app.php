<?php
// bootstrap/app.php — Laravel 11
// Remplacer le contenu de ce fichier par celui-ci
// pour enregistrer les middlewares personnalisés.

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ── Alias pour les middlewares personnalisés ──────────
        $middleware->alias([
            'gestionnaire' => \App\Http\Middleware\GestionnaireMiddleware::class,
            'client'       => \App\Http\Middleware\ClientMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
