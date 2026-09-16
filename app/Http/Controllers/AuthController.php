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
                        'email' => 'บัญชีเจ้าของร้านถูกปิดการใช้งาน'
                    ])
                    ->withInput($request->only('email'));
            }


            /*
            |--------------------------------------------------------------------------
            | Laravel Auth
            |--------------------------------------------------------------------------
            */

            Auth::login($owner, $remember);

            $request->session()->regenerate();


            return redirect()->route('owner.dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER LOGIN
        |--------------------------------------------------------------------------
        */

        $user = User::where('email', $data['email'])->first();
        $customer = Customer::where('email', $data['email'])->first();

        $passwordMatches = false;
        if ($user && Hash::check($data['password'], $user->password)) {
            $passwordMatches = true;
        } elseif ($customer && $customer->password && Hash::check($data['password'], $customer->password)) {
            $passwordMatches = true;
        }

        if ($passwordMatches) {
            DB::beginTransaction();
            try {
                if (!$user && $customer) {
                    $rawName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
                    $user = User::create([
                        'name' => $rawName !== '' ? $rawName : 'ลูกค้า',
                        'email' => $customer->email,
                        'password' => $customer->password ?? Hash::make(Str::random(16)),
                        'role' => 'customer',
                        'status' => 'active',
                    ]);
                }

                if (!$customer && $user) {
                    $rawName = trim($user->name);
                    $parts = preg_split('/\s+/u', $rawName, 2);
                    $customer = Customer::create([
                        'user_id' => $user->user_id,
                        'first_name' => $parts[0] ?? $rawName,
                        'last_name' => $parts[1] ?? '-',
                        'email' => $user->email,
                        'password' => $user->password,
                    ]);
                } else if ($customer && $user && !$customer->user_id) {
                    $customer->update(['user_id' => $user->user_id]);
                }

                DB::commit();

                Auth::login($user, $remember);

                $request->session()->regenerate();
                $request->session()->put([
                    'customer_logged_in' => true,
                    'customer_id' => $customer->customer_id,
                    'customer_name' => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                    'customer_email' => $customer->email,
                ]);

                return redirect()->intended(route('customer.dashboard'));
            } catch (\Throwable $e) {
                DB::rollBack();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN ไม่สำเร็จ
        |--------------------------------------------------------------------------
        */

        return back()
            ->withErrors([
                'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'
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
            Customer::where('email', $data['email'])->exists()
            ||
            User::where('email', $data['email'])->exists()
        ) {

            return back()
                ->withErrors([
                    'email' => 'อีเมลนี้ถูกใช้งานแล้ว'
                ])
                ->withInput(
                    $request->except(
                        'password',
                        'password_confirmation'
                    )
                );
        }


        DB::beginTransaction();

        try {

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
            | สร้าง User & Customer เชื่อมต่อ user_id
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'name' => $rawName,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'customer',
                'status' => 'active',
            ]);

            $customer = Customer::create([
                'user_id' => $user->user_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);


            DB::commit();

            Auth::login($user, true);


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
                    $customer->first_name . ' ' . $customer->last_name
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

            DB::rollBack();

            return back()
                ->withErrors([
                    'register' =>
                        'สมัครสมาชิกไม่สำเร็จ กรุณาลองใหม่อีกครั้ง'
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
                'new_password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',
                'new_password.confirmed' => 'ยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | เปลี่ยนรหัสผ่านเจ้าของร้าน
        |--------------------------------------------------------------------------
        */

        $owner = User::where('email', $data['email'])
            ->where('role', 'owner')
            ->first();

        if ($owner) {

            $owner->update([
                'password' => Hash::make(
                    $data['new_password']
                ),
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
        | เปลี่ยนรหัสผ่านลูกค้า
        |--------------------------------------------------------------------------
        */

        $customer = Customer::where(
            'email',
            $data['email']
        )->first();

        if ($customer) {

            $customer->update([
                'password' => Hash::make(
                    $data['new_password']
                ),
            ]);

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'ตั้งค่ารหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบอีกครั้ง'
                );
        }


        return back()
            ->withErrors([
                'email' =>
                    'ไม่พบบัญชีที่ใช้อีเมลนี้ในระบบ'
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
        | Logout Owner
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Logout Customer
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