<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        if (!in_array($provider, ['google'])) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, $provider)
    {
        if (!in_array($provider, ['google'])) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            DB::beginTransaction();

            $rawName = trim($socialUser->getName() ?: $socialUser->getNickname() ?: 'ผู้ใช้งาน Google');
            $parts = preg_split('/\s+/u', $rawName, 2);
            $firstName = $parts[0] ?? $rawName;
            $lastName = $parts[1] ?? '-';
            $email = $socialUser->getEmail() ?: ($provider . '_' . $socialUser->getId() . '@example.com');

            $customer = Customer::where('email', $email)->first();

            if (!$customer) {
                $customer = Customer::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                ]);
            }

            DB::commit();

            // ตั้งค่า Session สำหรับระบบฝั่งลูกค้า (Customer Middleware)
            $request->session()->regenerate();
            $request->session()->put([
                'customer_logged_in' => true,
                'customer_id'        => $customer->customer_id,
                'customer_name'      => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                'customer_email'     => $customer->email,
            ]);

            return redirect()->intended(route('customer.dashboard'))->with('success', 'เข้าสู่ระบบด้วย Google สำเร็จแล้ว!');


        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('login')
                ->with('error', 'ไม่สามารถเข้าสู่ระบบด้วยบัญชีโซเชียลได้');
        }
    }
}