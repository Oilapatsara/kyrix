<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            // ค้นหาจาก provider และ provider_id ก่อน
            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            // ถ้ายังไม่เจอ ให้ค้นหาจากอีเมล
            if (!$user && $socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())
                    ->first();
            }

            // ถ้ายังไม่มีผู้ใช้ ให้สร้างใหม่
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName()
                        ?: $socialUser->getNickname()
                        ?: 'ผู้ใช้งาน',

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

            Auth::login($user, true);

            return redirect()->intended('/');

        } catch (\Exception $e) {
            return redirect()
                ->route('login')
                ->with('error', 'ไม่สามารถเข้าสู่ระบบด้วยบัญชีโซเชียลได้');
        }
    }
}