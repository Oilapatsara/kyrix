<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->session()->get('customer_logged_in')){
            return redirect()->route('login')->withErrors([
                'email'=>'กรุณาเข้าสู่ระบบลูกค้าก่อน'
            ]);
        }

        return $next($request);
    }
}