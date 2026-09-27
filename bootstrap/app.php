<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'owner'    => \App\Http\Middleware\OwnerMiddleware::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            '/logout',
            'owner/bookings/*/status',
            'owner/bookings/*',
            'owner/payments/*',
            'owner/returns/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()
                ->back()
                ->withInput($request->except('_token', '_method'))
                ->with('error', 'เซสชันหมดอายุ หรือ CSRF Token ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        });
    })->create();