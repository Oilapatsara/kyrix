<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * ============================================================
     * GOOGLE LOGIN / REGISTER
     * ============================================================
     */

    /**
     * ส่งผู้ใช้ไป Google
     *
     * URL:
     * /auth/google
     */
    public function redirect($provider = 'google')
    {
        // ระบบนี้ใช้ Google เท่านั้น
        if ($provider !== 'google') {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | ตรวจสอบ Google OAuth
        |--------------------------------------------------------------------------
        */

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirect = config('services.google.redirect');

        if (empty($clientId)) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'ไม่พบ GOOGLE_CLIENT_ID กรุณาตรวจสอบไฟล์ .env และ config/services.php'
                );
        }

        if (empty($clientSecret)) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'ไม่พบ GOOGLE_CLIENT_SECRET กรุณาตรวจสอบไฟล์ .env และ config/services.php'
                );
        }

        if (empty($redirect)) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'ไม่พบ GOOGLE_REDIRECT_URI กรุณาตรวจสอบไฟล์ .env และ config/services.php'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | เริ่ม Google OAuth
        |--------------------------------------------------------------------------
        */

        return Socialite::driver('google')
            ->redirect();
    }


    /**
     * รับข้อมูลกลับจาก Google
     *
     * URL:
     * /auth/google/callback
     */
    public function callback(Request $request, $provider = 'google')
    {
        // ระบบนี้ใช้ Google เท่านั้น
        if ($provider !== 'google') {
            abort(404);
        }

        $transactionStarted = false;

        try {

            /*
            |--------------------------------------------------------------------------
            | ตรวจสอบ Google OAuth Configuration
            |--------------------------------------------------------------------------
            */

            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            $redirect = config('services.google.redirect');

            if (
                empty($clientId) ||
                empty($clientSecret) ||
                empty($redirect)
            ) {
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'การตั้งค่า Google Login ไม่สมบูรณ์ กรุณาตรวจสอบ .env และ config/services.php'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | รับข้อมูลจาก Google
            |--------------------------------------------------------------------------
            */

            $socialUser = Socialite::driver('google')->user();

            $googleId = $socialUser->getId();
            $email = $socialUser->getEmail();

            $name = $socialUser->getName()
                ?: $socialUser->getNickname()
                ?: 'ผู้ใช้งาน Google';

            $avatar = $socialUser->getAvatar();


            /*
            |--------------------------------------------------------------------------
            | ตรวจสอบข้อมูลจาก Google
            |--------------------------------------------------------------------------
            */

            if (empty($googleId)) {
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'ไม่สามารถรับรหัสบัญชี Google ได้ กรุณาลองใหม่อีกครั้ง'
                    );
            }

            if (empty($email)) {
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'ไม่สามารถรับอีเมลจากบัญชี Google ได้ กรุณาลองใหม่อีกครั้ง'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | เริ่ม Database Transaction
            |--------------------------------------------------------------------------
            */

            DB::beginTransaction();
            $transactionStarted = true;


            /*
            |--------------------------------------------------------------------------
            | ค้นหา User จาก Google
            |--------------------------------------------------------------------------
            */

            $user = User::where('provider', 'google')
                ->where('provider_id', $googleId)
                ->first();


            /*
            |--------------------------------------------------------------------------
            | ถ้าไม่พบ ให้ค้นหาจาก Email
            |--------------------------------------------------------------------------
            */

            if (!$user) {
                $user = User::where('email', $email)->first();
            }


            /*
            |--------------------------------------------------------------------------
            | ถ้ายังไม่มี User ให้สร้างใหม่
            |--------------------------------------------------------------------------
            */

            if (!$user) {

                $user = User::create([
                    'name' => $name,

                    'email' => $email,

                    'password' => Hash::make(
                        Str::random(64)
                    ),

                    'provider' => 'google',

                    'provider_id' => $googleId,

                    'avatar' => $avatar,

                    'role' => 'customer',

                    'status' => 1,
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | มี User อยู่แล้ว
                | อัปเดตข้อมูล Google
                |--------------------------------------------------------------------------
                */

                $user->update([
                    'provider' => 'google',
                    'provider_id' => $googleId,
                    'avatar' => $avatar,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | ตรวจสอบ Role
            |--------------------------------------------------------------------------
            |
            | ถ้าเป็น Owner/Admin อยู่แล้ว
            | จะไม่เปลี่ยน role เป็น customer
            |
            */

            if (empty($user->role)) {
                $user->update([
                    'role' => 'customer',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | แยกชื่อ - นามสกุล
            |--------------------------------------------------------------------------
            */

            $rawName = trim($name);

            $parts = preg_split(
                '/\s+/u',
                $rawName,
                2
            );

            $firstName = $parts[0] ?? $rawName;
            $lastName = $parts[1] ?? '-';


            /*
            |--------------------------------------------------------------------------
            | ค้นหา Customer จาก user_id
            |--------------------------------------------------------------------------
            */

            $customer = Customer::where(
                'user_id',
                $user->user_id
            )->first();


            /*
            |--------------------------------------------------------------------------
            | ถ้ายังไม่พบ Customer
            | ค้นหาจาก Email
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
            | ถ้ายังไม่มี Customer
            | สร้าง Customer ใหม่
            |--------------------------------------------------------------------------
            */

            if (!$customer) {

                $customer = Customer::create([
                    'user_id' => $user->user_id,

                    'first_name' => $firstName,

                    'last_name' => $lastName,

                    'email' => $email,

                    'password' => Hash::make(
                        Str::random(64)
                    ),
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | Customer มีอยู่แล้ว
                |--------------------------------------------------------------------------
                */

                $updateData = [];


                /*
                | เชื่อม User
                */

                if (empty($customer->user_id)) {
                    $updateData['user_id'] = $user->user_id;
                }


                /*
                | อัปเดตชื่อ ถ้ายังไม่มี
                */

                if (empty($customer->first_name)) {
                    $updateData['first_name'] = $firstName;
                }


                if (
                    empty($customer->last_name) ||
                    $customer->last_name === '-'
                ) {
                    $updateData['last_name'] = $lastName;
                }


                /*
                | อัปเดต Email ถ้ายังไม่มี
                */

                if (empty($customer->email)) {
                    $updateData['email'] = $email;
                }


                /*
                | บันทึกข้อมูลที่เปลี่ยน
                */

                if (!empty($updateData)) {
                    $customer->update($updateData);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();
            $transactionStarted = false;


            /*
            |--------------------------------------------------------------------------
            | Login Laravel
            |--------------------------------------------------------------------------
            */

            Auth::login($user, true);


            /*
            |--------------------------------------------------------------------------
            | Regenerate Session
            |--------------------------------------------------------------------------
            */

            $request->session()->regenerate();


            /*
            |--------------------------------------------------------------------------
            | Session Customer
            |--------------------------------------------------------------------------
            */

            $request->session()->put([
                'customer_logged_in' => true,

                'customer_id' => $customer->customer_id,

                'customer_name' => trim(
                    ($customer->first_name ?? '') .
                    ' ' .
                    ($customer->last_name ?? '')
                ),

                'customer_email' => $customer->email,
            ]);


            /*
            |--------------------------------------------------------------------------
            | ส่งไปหน้า Dashboard
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('customer.dashboard')
                ->with(
                    'success',
                    'เข้าสู่ระบบด้วย Google สำเร็จแล้ว!'
                );


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback เฉพาะเมื่อ Transaction ถูกเปิดจริง
            |--------------------------------------------------------------------------
            */

            if ($transactionStarted) {
                DB::rollBack();
            }


            /*
            |--------------------------------------------------------------------------
            | บันทึก Error ไว้ใน Laravel Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Google Login Error',
                [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | กลับหน้า Login
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'ไม่สามารถเข้าสู่ระบบด้วย Google ได้ กรุณาลองใหม่อีกครั้ง'
                );
        }
    }
}