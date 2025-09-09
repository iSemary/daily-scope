<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $apiController = new \App\Http\Controllers\ApiController();
                
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return $apiController->return(422, 'Validation failed', [], ['errors' => $e->errors()]);
                }
                
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return $apiController->return(401, 'Unauthenticated');
                }
                
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return $apiController->return(403, 'This action is unauthorized');
                }
                
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return $apiController->return(404, 'Resource not found');
                }
                
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return $apiController->return(404, 'Route not found');
                }
                
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    return $apiController->return(405, 'Method not allowed');
                }
                
                if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                    return $apiController->return(429, 'Too many requests');
                }
                
                $statusCode = 500;
                $message = config('app.debug') ? $e->getMessage() : 'Something went wrong';
                
                return $apiController->return($statusCode, $message, [], config('app.debug') ? [
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTrace(),
                ] : []);
            }
        });
    })->create();
