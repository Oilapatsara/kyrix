<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
     * ส่งผู้ใช้ไปเข้าสู่ระบบด้วย Google
     *
     * URL:
     * /auth/google
     */
    public function redirect($provider = 'google')
    {
        // ระบบนี้รองรับ Google เท่านั้น
        if ($provider !== 'google') {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | ตรวจสอบ Google OAuth Configuration
        |--------------------------------------------------------------------------
        */

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');

        /*
        | ถ้าไม่ได้ตั้งค่า Google OAuth
        | จะไม่ปล่อยให้ Google ขึ้น
        | "Missing required parameter: client_id"
        |
        | แต่จะกลับหน้า Login พร้อมข้อความแทน
        */

        if (
            empty($clientId) ||
            empty($clientSecret) ||
            empty($redirectUri)
        ) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'ยังไม่ได้ตั้งค่า Google Login กรุณาตรวจสอบ GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET และ GOOGLE_REDIRECT_URI ในไฟล์ .env'
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
     * รับข้อมูลจาก Google หลังจาก Login สำเร็จ
     *
     * URL:
     * /auth/google/callback
     */
    public function callback(
        Request $request,
        $provider = 'google'
    ) {
        // ระบบนี้รองรับ Google เท่านั้น
        if ($provider !== 'google') {
            abort(404);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | ตรวจสอบ Configuration อีกครั้ง
            |--------------------------------------------------------------------------
            */

            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            $redirectUri = config('services.google.redirect');

            if (
                empty($clientId) ||
                empty($clientSecret) ||
                empty($redirectUri)
            ) {
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'การตั้งค่า Google Login ไม่สมบูรณ์ กรุณาตรวจสอบไฟล์ .env'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | รับข้อมูลจาก Google
            |--------------------------------------------------------------------------
            */

            $socialUser = Socialite::driver('google')->user();


            /*
            |--------------------------------------------------------------------------
            | ตรวจสอบข้อมูลสำคัญจาก Google
            |--------------------------------------------------------------------------
            */

            $googleId = $socialUser->getId();
            $email = $socialUser->getEmail();

            $name = $socialUser->getName()
                ?: $socialUser->getNickname()
                ?: 'ผู้ใช้งาน Google';

            $avatar = $socialUser->getAvatar();


            /*
            | Google ต้องมี ID
            */

            if (empty($googleId)) {
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'ไม่สามารถรับข้อมูลบัญชี Google ได้ กรุณาลองใหม่อีกครั้ง'
                    );
            }


            /*
            | ถ้า Google ไม่ส่ง Email มา
            | สร้าง Email สำรองเพื่อไม่ให้ฐานข้อมูลพัง
            */

            if (empty($email)) {
                $email = 'google_' . $googleId . '@example.com';
            }


            /*
            |--------------------------------------------------------------------------
            | เริ่ม Transaction
            |--------------------------------------------------------------------------
            */

            DB::beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | 1. ค้นหา User จาก Google ID
            |--------------------------------------------------------------------------
            */

            $user = User::where('provider', 'google')
                ->where('provider_id', $googleId)
                ->first();


            /*
            |--------------------------------------------------------------------------
            | 2. ถ้ายังไม่เจอ ให้ค้นหาจาก Email
            |--------------------------------------------------------------------------
            */

            if (!$user) {

                $user = User::where('email', $email)
                    ->first();
            }


            /*
            |--------------------------------------------------------------------------
            | 3. ถ้ายังไม่มี User
            |    ให้สร้าง User ใหม่
            |--------------------------------------------------------------------------
            */

            if (!$user) {

                $user = User::create([

                    'name' => $name,

                    'email' => $email,

                    /*
                    | Google Login ไม่จำเป็นต้องใช้ Password จริง
                    */

                    'password' => Hash::make(
                        Str::random(64)
                    ),

                    'provider' => 'google',

                    'provider_id' => $googleId,

                    'avatar' => $avatar,

                    /*
                    | ผู้สมัครผ่าน Google
                    | ให้เป็น customer
                    */

                    'role' => 'customer',

                    'status' => 1,
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | 4. User มีอยู่แล้ว
                |    อัปเดตข้อมูล Google
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
            | 5. แยกชื่อ - นามสกุล
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
            | 6. ค้นหา Customer จาก user_id
            |--------------------------------------------------------------------------
            */

            $customer = Customer::where(
                'user_id',
                $user->user_id
            )->first();


            /*
            |--------------------------------------------------------------------------
            | 7. ถ้ายังไม่มี Customer
            |    ค้นหาจาก Email
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
            | 8. ถ้ายังไม่มี Customer
            |    สร้าง Customer ใหม่
            |--------------------------------------------------------------------------
            */

            if (!$customer) {

                $customer = Customer::create([

                    'user_id' => $user->user_id,

                    'first_name' => $firstName,

                    'last_name' => $lastName,

                    'email' => $email,

                    /*
                    | Customer มี Password สำรองไว้
                    | แต่การ Login ครั้งนี้ใช้ User + Google
                    */

                    'password' => Hash::make(
                        Str::random(64)
                    ),
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | 9. ถ้ามี Customer อยู่แล้ว
                |    แต่ยังไม่มี user_id
                |--------------------------------------------------------------------------
                */

                $updateData = [];

                if (empty($customer->user_id)) {
                    $updateData['user_id'] = $user->user_id;
                }

                /*
                | อัปเดตชื่อถ้าข้อมูลยังว่าง
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

                if (!empty($updateData)) {
                    $customer->update($updateData);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | 10. Commit Database
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | 11. Login เข้าระบบ Laravel
            |--------------------------------------------------------------------------
            */

            Auth::login(
                $user,
                true
            );


            /*
            |--------------------------------------------------------------------------
            | 12. ป้องกัน Session Fixation
            |--------------------------------------------------------------------------
            */

            $request->session()->regenerate();


            /*
            |--------------------------------------------------------------------------
            | 13. สร้าง Session สำหรับ Customer
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
            | 14. ส่งไป Dashboard ลูกค้า
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->intended(
                    route('customer.dashboard')
                )
                ->with(
                    'success',
                    'เข้าสู่ระบบด้วย Google สำเร็จแล้ว!'
                );


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ถ้าเกิด Error ให้ Rollback
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            /*
            |--------------------------------------------------------------------------
            | เก็บ Error ไว้ใน Log
            |--------------------------------------------------------------------------
            |
            | ผู้ใช้จะไม่เห็นรายละเอียด Database หรือ OAuth
            | บนหน้าเว็บ
            |
            */

            \Log::error(
                'Google Social Login Error',
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