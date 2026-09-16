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

            // ค้นหาจาก provider และ provider_id ก่อน
            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            // ถ้ายังไม่เจอ ให้ค้นหาจากอีเมล
            if (!$user && $socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())
                    ->first();
            }

            // ถ้ายังไม่มีผู้ใช้ ให้สร้างใหม่ในตาราง users
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName()
                        ?: $socialUser->getNickname()
                        ?: 'ผู้ใช้งาน Google',

                    'email' => $socialUser->getEmail()
                        ?: $provider . '_' . $socialUser->getId() . '@example.com',

                    'password' => Str::random(32),

                    'provider' => $provider,

                    'provider_id' => $socialUser->getId(),

                    'avatar' => $socialUser->getAvatar(),

                    'role' => 'customer',

                    'status' => 1,
                ]);
            } else {
                // อัปเดตข้อมูล Social Login ของผู้ใช้เดิม
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            // ค้นหาหรือสร้างข้อมูลในตาราง customers สำหรับระบบฝั่งลูกค้า
            $rawName = trim($socialUser->getName() ?: $socialUser->getNickname() ?: 'ผู้ใช้งาน Google');
            $parts = preg_split('/\s+/u', $rawName, 2);
            $firstName = $parts[0] ?? $rawName;
            $lastName = $parts[1] ?? '-';
            $email = $socialUser->getEmail() ?: ($provider . '_' . $socialUser->getId() . '@example.com');

            $customer = Customer::where('user_id', $user->user_id)->first();
            if (!$customer && $email) {
                $customer = Customer::where('email', $email)->first();
            }

            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => $user->user_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                ]);
            } else {
                if (!$customer->user_id) {
                    $customer->update(['user_id' => $user->user_id]);
                }
            }

            DB::commit();

            // ล็อกอินระบบ Auth หลัก (User)
            Auth::login($user, true);

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