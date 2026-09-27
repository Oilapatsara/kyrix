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
        if (!in_array($provider, ['google'], true)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, $provider)
    {
        if (!in_array($provider, ['google'], true)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | ข้อมูลจาก Google
            |--------------------------------------------------------------------------
            */

            $providerId = (string) $socialUser->getId();

            $email = $socialUser->getEmail()
                ?: ($provider . '_' . $providerId . '@example.com');

            $rawName = trim(
                $socialUser->getName()
                    ?: $socialUser->getNickname()
                    ?: 'ผู้ใช้งาน Google'
            );

            /*
            |--------------------------------------------------------------------------
            | ค้นหา User จาก provider + provider_id ก่อน
            |--------------------------------------------------------------------------
            */

            $user = User::where('provider', $provider)
                ->where('provider_id', $providerId)
                ->where('role', 'customer')
                ->first();

            /*
            |--------------------------------------------------------------------------
            | ถ้ายังไม่เจอ ให้ค้นหาจาก Email เฉพาะ Customer
            |--------------------------------------------------------------------------
            */

            if (!$user && $socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())
                    ->where('role', 'customer')
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | ถ้ายังไม่มี User ให้สร้างใหม่
            |--------------------------------------------------------------------------
            */

            if (!$user) {
                $user = User::create([
                    'name' => $rawName,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'avatar' => $socialUser->getAvatar(),
                    'role' => 'customer',
                    'status' => 'active',
                ]);
            } else {
                /*
                |--------------------------------------------------------------------------
                | อัปเดตข้อมูล Social Login ของ User เดิม
                |--------------------------------------------------------------------------
                */

                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ตรวจสอบสถานะบัญชี
            |--------------------------------------------------------------------------
            */

            if ($user->status !== 'active') {
                DB::rollBack();

                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'บัญชีของคุณถูกปิดการใช้งาน'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | แยกชื่อสำหรับ Customer
            |--------------------------------------------------------------------------
            */

            $parts = preg_split(
                '/\s+/u',
                $rawName,
                2
            );

            $firstName = $parts[0] ?? $rawName;
            $lastName = $parts[1] ?? '-';

            /*
            |--------------------------------------------------------------------------
            | ค้นหา Customer จาก user_id ก่อน
            |--------------------------------------------------------------------------
            */

            $customer = Customer::where(
                'user_id',
                $user->user_id
            )->first();

            /*
            |--------------------------------------------------------------------------
            | ถ้ายังไม่เจอ ให้ค้นหาจาก email
            |--------------------------------------------------------------------------
            */

            if (!$customer) {
                $customer = Customer::where(
                    'email',
                    $email
                )->first();
            }

            /*
            |--------------------------------------------------------------------------
            | ถ้ายังไม่มี Customer ให้สร้าง
            |--------------------------------------------------------------------------
            */

            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => $user->user_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => $user->password,
                ]);
            } else {
                /*
                |--------------------------------------------------------------------------
                | ผูก Customer เดิมเข้ากับ User
                |--------------------------------------------------------------------------
                */

                $customerData = [];

                if (!$customer->user_id) {
                    $customerData['user_id'] = $user->user_id;
                }

                if (!$customer->first_name) {
                    $customerData['first_name'] = $firstName;
                }

                if (!$customer->last_name) {
                    $customerData['last_name'] = $lastName;
                }

                if (!empty($customerData)) {
                    $customer->update($customerData);
                }
            }

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Login Laravel Auth
            |--------------------------------------------------------------------------
            */

            Auth::login($user, true);

            /*
            |--------------------------------------------------------------------------
            | สร้าง Session ใหม่
            |--------------------------------------------------------------------------
            */

            $request->session()->regenerate();

            /*
            |--------------------------------------------------------------------------
            | Session สำหรับระบบลูกค้า
            |--------------------------------------------------------------------------
            */

            $request->session()->put([
                'customer_logged_in' => true,
                'customer_id' => $customer->customer_id,
                'customer_name' => trim(
                    ($customer->first_name ?? '') . ' ' .
                    ($customer->last_name ?? '')
                ),
                'customer_email' => $customer->email,
            ]);

            /*
            |--------------------------------------------------------------------------
            | ส่งไป Dashboard ลูกค้า
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->intended(route('customer.dashboard'))
                ->with(
                    'success',
                    'เข้าสู่ระบบด้วย Google สำเร็จแล้ว!'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'ไม่สามารถเข้าสู่ระบบด้วยบัญชี Google ได้ กรุณาลองใหม่อีกครั้ง'
                );
        }
    }
}