<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\ForceJsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->append(ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // custom handler for MethodNotAllowedHttpException
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'status' => false,
                'message' => 'Method Not Allowed'
            ], 405);
        });

        // custom handler for NotFoundHttpException
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->json([
                'status' => false,
                'message' => 'Not Found'
            ], 404);
        });

        // custom handler for RouteNotFoundException
        $exceptions->render(function (Symfony\Component\Routing\Exception\RouteNotFoundException  $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Route'
            ], 401);
        });
    })->create();
