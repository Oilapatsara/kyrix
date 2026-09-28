<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'owner') {
            return redirect()->route('owner.dashboard');
        }

        if (Auth::check()) {
            abort_unless(Auth::user()->role === 'customer' && Auth::user()->status === 'active', 403);
        }

        // 1. กรณีที่มี Auth user สิทธิ์ customer อยู่แล้ว ให้ซิงค์ session ลูกค้าให้อัตโนมัติ
        if (Auth::check() && Auth::user()->role === 'customer') {
            $customer = Customer::where('user_id', Auth::id())->first()
                ?? Customer::whereNull('user_id')->where('email', Auth::user()->email)->first();

            abort_unless($customer, 403, 'ไม่พบข้อมูลลูกค้าในระบบ กรุณาติดต่อเจ้าของร้าน');

            $request->session()->put([
                'customer_logged_in' => true,
                'customer_id' => $customer->customer_id,
                'customer_name' => trim(($customer->first_name ?? '').' '.($customer->last_name ?? '')),
                'customer_email' => $customer->email,
            ]);

            return $next($request);
        }

        // 2. กรณีที่มี Session ลูกค้าอยู่แล้ว ให้ซิงค์ Auth::login ด้วยหากยังไม่ได้ login
        if ($request->session()->get('customer_logged_in') && $request->session()->get('customer_id')) {
            $customerId = $request->session()->get('customer_id');
            $customer = Customer::find($customerId);

            if ($customer) {
                if ($customer->user_id && ! Auth::check()) {
                    $user = User::find($customer->user_id);
                    if ($user) {
                        abort_unless($user->role === 'customer' && $user->status === 'active', 403);
                        Auth::login($user);
                    }
                }

                return $next($request);
            }
        }

        // 3. หากไม่มีทั้ง Auth และ Session ให้เด้งไปหน้า login
        return redirect()->guest(route('login'))->withErrors([
            'email' => 'กรุณาเข้าสู่ระบบลูกค้าก่อน',
        ]);
    }
}
