<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'owner') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'ไม่มีสิทธิ์เข้าถึงส่วนผู้ดูแลร้านค้า กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าของร้าน'
            ]);
        }

        if (Auth::user()->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'บัญชีผู้ดูแลระบบนี้ถูกปิดการใช้งาน'
            ]);
        }

        return $next($request);
    }
}
