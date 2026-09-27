<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN PAGE
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $data = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|string',
            ],
            [
                'email.required' => 'กรุณากรอกอีเมล',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'password.required' => 'กรุณากรอกรหัสผ่าน',
            ]
        );

        $remember = $request->boolean('remember');

        /*
        |--------------------------------------------------------------------------
        | ล้าง Session ลูกค้าเก่าก่อน Login
        |--------------------------------------------------------------------------
        */

        $request->session()->forget([
            'customer_logged_in',
            'customer_id',
            'customer_name',
            'customer_email',
        ]);

        /*
        |--------------------------------------------------------------------------
        | OWNER LOGIN
        |--------------------------------------------------------------------------
        */

        $owner = User::where('email', $data['email'])
            ->where('role', 'owner')
            ->first();

        if ($owner && Hash::check($data['password'], $owner->password)) {

            if ($owner->status !== 'active') {
                return back()
                    ->withErrors([
                        'email' => 'บัญชีเจ้าของร้านถูกปิดการใช้งาน',
                    ])
                    ->withInput(
                        $request->only('email')
                    );
            }

            Auth::login($owner, $remember);

            $request->session()->regenerate();

            return redirect()->route('owner.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER LOGIN - ใช้ users เป็นบัญชีหลัก
        |--------------------------------------------------------------------------
        */

        $user = User::where('email', $data['email'])
            ->where('role', 'customer')
            ->first();

        if ($user && Hash::check($data['password'], $user->password)) {

            if ($user->status !== 'active') {
                return back()
                    ->withErrors([
                        'email' => 'บัญชีลูกค้าถูกปิดการใช้งาน',
                    ])
                    ->withInput(
                        $request->only('email')
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | หา Customer ที่ผูกกับ user_id
            |--------------------------------------------------------------------------
            */

            $customer = Customer::where(
                'user_id',
                $user->user_id
            )->first();

            /*
            |--------------------------------------------------------------------------
            | กรณีมี users แต่ customer ยังไม่ผูก
            |--------------------------------------------------------------------------
            |
            | ใช้อีเมลค้นหาเพื่อรองรับข้อมูลเก่าที่ user_id ยังว่าง
            |
            */

            if (!$customer) {
                $customer = Customer::where(
                    'email',
                    $user->email
                )->first();

                if ($customer) {
                    $customer->update([
                        'user_id' => $user->user_id,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ต้องมี Customer สำหรับระบบเช่าชุด
            |--------------------------------------------------------------------------
            */

            if (!$customer) {
                return back()
                    ->withErrors([
                        'email' => 'ไม่พบข้อมูลลูกค้าในระบบ กรุณาติดต่อเจ้าของร้าน',
                    ])
                    ->withInput(
                        $request->only('email')
                    );
            }

            $request->session()->regenerate();

            $request->session()->put([
                'customer_logged_in' => true,
                'customer_id' => $customer->customer_id,
                'customer_name' => trim(
                    ($customer->first_name ?? '') . ' ' .
                    ($customer->last_name ?? '')
                ),
                'customer_email' => $customer->email,
            ]);

            return redirect()->intended(
                route('customer.dashboard')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER LOGIN แบบข้อมูลเก่า
        |--------------------------------------------------------------------------
        |
        | รองรับลูกค้าเดิมที่ยังมีเฉพาะ customers
        | และยังไม่มี user_id ใน users
        |
        */

        $legacyCustomer = Customer::where(
            'email',
            $data['email']
        )->first();

        if (
            $legacyCustomer &&
            $legacyCustomer->password &&
            Hash::check(
                $data['password'],
                $legacyCustomer->password
            )
        ) {

            /*
            |--------------------------------------------------------------------------
            | พยายามผูก Customer เก่าเข้ากับ User
            |--------------------------------------------------------------------------
            */

            try {

                $existingUser = User::where(
                    'email',
                    $legacyCustomer->email
                )
                    ->where('role', 'customer')
                    ->first();

                if (!$existingUser) {

                    $existingUser = User::create([
                        'name' => trim(
                            ($legacyCustomer->first_name ?? '') . ' ' .
                            ($legacyCustomer->last_name ?? '')
                        ),
                        'email' => $legacyCustomer->email,
                        'password' => $legacyCustomer->password,
                        'role' => 'customer',
                        'status' => 'active',
                    ]);
                }

                if (!$legacyCustomer->user_id) {
                    $legacyCustomer->update([
                        'user_id' => $existingUser->user_id,
                    ]);
                }

            } catch (\Throwable $e) {
                /*
                |--------------------------------------------------------------------------
                | ถ้าผูกบัญชีไม่สำเร็จ ยังให้ลูกค้าเก่า Login ได้
                |--------------------------------------------------------------------------
                */
                report($e);
            }

            $request->session()->regenerate();

            $request->session()->put([
                'customer_logged_in' => true,
                'customer_id' => $legacyCustomer->customer_id,
                'customer_name' => trim(
                    ($legacyCustomer->first_name ?? '') . ' ' .
                    ($legacyCustomer->last_name ?? '')
                ),
                'customer_email' => $legacyCustomer->email,
            ]);

            return redirect()->intended(
                route('customer.dashboard')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN ไม่สำเร็จ
        |--------------------------------------------------------------------------
        */

        return back()
            ->withErrors([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ])
            ->withInput(
                $request->only('email')
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER PAGE
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        return view('auth.register');
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {
        $data = $request->validate(
            [
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:150',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
                'email.required' => 'กรุณากรอกอีเมล',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
                'password.confirmed' => 'ยืนยันรหัสผ่านไม่ตรงกัน',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ตรวจอีเมลซ้ำ
        |--------------------------------------------------------------------------
        */

        if (
            User::where('email', $data['email'])->exists() ||
            Customer::where('email', $data['email'])->exists()
        ) {
            return back()
                ->withErrors([
                    'email' => 'อีเมลนี้ถูกใช้งานแล้ว',
                ])
                ->withInput(
                    $request->except(
                        'password',
                        'password_confirmation'
                    )
                );
        }

        try {

            $customer = DB::transaction(function () use ($data) {

                $rawName = trim($data['name']);

                $parts = preg_split(
                    '/\s+/u',
                    $rawName,
                    2
                );

                $firstName = $parts[0] ?? $rawName;
                $lastName = $parts[1] ?? '-';

                /*
                |--------------------------------------------------------------------------
                | เข้ารหัสรหัสผ่านครั้งเดียว
                |--------------------------------------------------------------------------
                */

                $hashedPassword = Hash::make(
                    $data['password']
                );

                /*
                |--------------------------------------------------------------------------
                | สร้าง User
                |--------------------------------------------------------------------------
                */

                $user = User::create([
                    'name' => $rawName,
                    'email' => $data['email'],
                    'password' => $hashedPassword,
                    'role' => 'customer',
                    'status' => 'active',
                ]);

                /*
                |--------------------------------------------------------------------------
                | สร้าง Customer และผูก user_id
                |--------------------------------------------------------------------------
                */

                $customer = Customer::create([
                    'user_id' => $user->user_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $data['email'],
                    'password' => $hashedPassword,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);

                return $customer;
            });

            /*
            |--------------------------------------------------------------------------
            | Login Customer อัตโนมัติ
            |--------------------------------------------------------------------------
            */

            $request->session()->regenerate();

            $request->session()->put([
                'customer_logged_in' => true,
                'customer_id' => $customer->customer_id,
                'customer_name' => trim(
                    ($customer->first_name ?? '') . ' ' .
                    ($customer->last_name ?? '')
                ),
                'customer_email' => $customer->email,
            ]);

            return redirect()
                ->route('home')
                ->with(
                    'success',
                    'ยินดีต้อนรับสู่ KYRIX สมัครสมาชิกสำเร็จแล้ว!'
                );

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withErrors([
                    'register' =>
                        'สมัครสมาชิกไม่สำเร็จ กรุณาลองใหม่อีกครั้ง',
                ])
                ->withInput(
                    $request->except(
                        'password',
                        'password_confirmation'
                    )
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD PAGE
    |--------------------------------------------------------------------------
    */

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD
    |--------------------------------------------------------------------------
    */

    public function forgotPassword(Request $request)
    {
        $data = $request->validate(
            [
                'email' => 'required|email',
                'new_password' => 'required|string|min:6|confirmed',
            ],
            [
                'email.required' => 'กรุณากรอกอีเมล',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'new_password.required' => 'กรุณากรอกรหัสผ่านใหม่',
                'new_password.min' =>
                    'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',
                'new_password.confirmed' =>
                    'ยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
            ]
        );

        $newHashedPassword = Hash::make(
            $data['new_password']
        );

        /*
        |--------------------------------------------------------------------------
        | OWNER
        |--------------------------------------------------------------------------
        */

        $owner = User::where('email', $data['email'])
            ->where('role', 'owner')
            ->first();

        if ($owner) {

            $owner->update([
                'password' => $newHashedPassword,
            ]);

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'ตั้งค่ารหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบอีกครั้ง'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        $customer = Customer::where(
            'email',
            $data['email']
        )->first();

        if ($customer) {

            DB::transaction(function () use (
                $customer,
                $newHashedPassword
            ) {

                /*
                |--------------------------------------------------------------------------
                | เปลี่ยนรหัสผ่าน Customer
                |--------------------------------------------------------------------------
                */

                $customer->update([
                    'password' => $newHashedPassword,
                ]);

                /*
                |--------------------------------------------------------------------------
                | เปลี่ยนรหัสผ่าน User ที่ผูกกัน
                |--------------------------------------------------------------------------
                */

                if ($customer->user_id) {

                    User::where(
                        'user_id',
                        $customer->user_id
                    )->update([
                        'password' => $newHashedPassword,
                    ]);
                }
            });

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'ตั้งค่ารหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบอีกครั้ง'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ไม่พบบัญชี
        |--------------------------------------------------------------------------
        */

        return back()
            ->withErrors([
                'email' =>
                    'ไม่พบบัญชีที่ใช้อีเมลนี้ในระบบ',
            ])
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Logout Laravel Auth
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        /*
        |--------------------------------------------------------------------------
        | ล้าง Session Customer
        |--------------------------------------------------------------------------
        */

        $request->session()->forget([
            'customer_logged_in',
            'customer_id',
            'customer_name',
            'customer_email',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Destroy Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with(
                'success',
                'ออกจากระบบเรียบร้อยแล้ว'
            );
    }
}
