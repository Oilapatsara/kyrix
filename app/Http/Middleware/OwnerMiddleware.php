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
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'email' => 'กรุณาเข้าสู่ระบบก่อนใช้งาน'
            ]);
        }

        if (Auth::user()->role !== 'owner') {
            return redirect()->route('customer.dashboard')->withErrors([
                'email' => 'ไม่มีสิทธิ์เข้าถึงส่วนผู้ดูแลร้านค้า'
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
