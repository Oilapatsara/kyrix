<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Rental;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * ดึง Customer ที่ login อยู่จาก session
     */
    private function getCustomer(): Customer
    {
        $customerId = session('customer_id');

        if (!$customerId) {
            abort(
                403,
                'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
            );
        }

        $customer = Customer::find($customerId);

        if (!$customer) {
            session()->forget([
                'customer_logged_in',
                'customer_id',
                'customer_name',
                'customer_email',
                'customer_profile_image',
            ]);

            abort(
                403,
                'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
            );
        }

        return $customer;
    }

    /**
     * หน้า Profile
     */
    public function index()
    {
        $customer = $this->getCustomer();

        /*
        |--------------------------------------------------------------------------
        | ทำให้ Session ของรูปตรงกับข้อมูลใน Database
        |--------------------------------------------------------------------------
        */
        session([
            'customer_profile_image' => $customer->profile_image,
            'customer_name' => trim(
                ($customer->first_name ?? '') .
                ' ' .
                ($customer->last_name ?? '')
            ),
            'customer_email' => $customer->email,
        ]);

        $stats = [
            'total_rentals' => Rental::where(
                'customer_id',
                $customer->customer_id
            )->count(),

            'active_rentals' => Rental::where(
                'customer_id',
                $customer->customer_id
            )
                ->whereNotIn('status', [
                    'completed',
                    'cancelled',
                ])
                ->count(),

            'completed_rentals' => Rental::where(
                'customer_id',
                $customer->customer_id
            )
                ->where(
                    'status',
                    'completed'
                )
                ->count(),

            'reviews_count' => Review::where(
                'customer_id',
                $customer->customer_id
            )->count(),
        ];

        return view(
            'profile.index',
            compact(
                'customer',
                'stats'
            )
        );
    }

    /**
     * อัปเดตข้อมูลส่วนตัว
     */
    public function update(Request $request)
    {
        $customer = $this->getCustomer();

        $request->validate([
            'name' => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique(
                    'customers',
                    'email'
                )->ignore(
                    $customer->customer_id,
                    'customer_id'
                ),
            ],

            'phone' => 'nullable|string|max:30',

            'address' => 'nullable|string|max:1000',
        ], [
            'name.required' =>
                'กรุณากรอกชื่อ-นามสกุล',

            'email.required' =>
                'กรุณากรอกอีเมล',

            'email.email' =>
                'รูปแบบอีเมลไม่ถูกต้อง',

            'email.unique' =>
                'อีเมลนี้ถูกใช้งานแล้ว',
        ]);

        $rawName = trim(
            $request->name
        );

        $parts = preg_split(
            '/\s+/u',
            $rawName,
            2
        );

        $firstName =
            $parts[0] ?? $rawName;

        $lastName =
            $parts[1] ?? '-';

        $customer->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        /*
        |--------------------------------------------------------------------------
        | อัปเดต Session ชื่อ อีเมล และรูป
        |--------------------------------------------------------------------------
        */
        session([
            'customer_name' => trim(
                $firstName .
                ' ' .
                $lastName
            ),

            'customer_email' =>
                $request->email,

            'customer_profile_image' =>
                $customer->profile_image,
        ]);

        return back()->with(
            'success',
            'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว'
        );
    }

    /**
     * อัปโหลด / เปลี่ยนรูปโปรไฟล์
     */
    public function updateProfileImage(Request $request)
    {
        $customer = $this->getCustomer();

        $request->validate([
            'profile_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'profile_image.required' =>
                'กรุณาเลือกรูปโปรไฟล์',

            'profile_image.image' =>
                'ไฟล์ที่เลือกต้องเป็นรูปภาพ',

            'profile_image.mimes' =>
                'รองรับเฉพาะ JPG, JPEG, PNG และ WEBP',

            'profile_image.max' =>
                'ขนาดรูปต้องไม่เกิน 2 MB',
        ]);

        if ($request->hasFile('profile_image')) {

            /*
            |--------------------------------------------------------------------------
            | ลบรูปเก่า
            |--------------------------------------------------------------------------
            */
            if ($customer->profile_image) {
                Storage::disk('public')->delete(
                    $customer->profile_image
                );
            }

            /*
            |--------------------------------------------------------------------------
            | บันทึกรูปใหม่
            |--------------------------------------------------------------------------
            */
            $path = $request
                ->file('profile_image')
                ->store(
                    'profile',
                    'public'
                );

            /*
            |--------------------------------------------------------------------------
            | บันทึกลง Database
            |--------------------------------------------------------------------------
            */
            $customer->profile_image =
                $path;

            $customer->save();

            /*
            |--------------------------------------------------------------------------
            | อัปเดต Session รูปโปรไฟล์ทันที
            |--------------------------------------------------------------------------
            */
            session([
                'customer_profile_image' =>
                    $path,
            ]);
        }

        return back()->with(
            'success',
            'เปลี่ยนรูปโปรไฟล์เรียบร้อยแล้ว'
        );
    }

    /**
     * เปลี่ยนรหัสผ่าน
     */
    public function updatePassword(
        Request $request
    ) {
        $customer = $this->getCustomer();

        $user = \App\Models\User::where(
            'user_id',
            $customer->user_id
        )
            ->orWhere(
                'email',
                $customer->email
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Google Social Login
        |--------------------------------------------------------------------------
        */
        if (
            $user &&
            $user->provider === 'google' &&
            empty($customer->password)
        ) {

            $request->validate([
                'password' =>
                    'required|string|min:6|confirmed',
            ], [
                'password.required' =>
                    'กรุณากรอกรหัสผ่านใหม่',

                'password.min' =>
                    'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',

                'password.confirmed' =>
                    'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
            ]);

        } else {

            $request->validate([
                'current_password' =>
                    'required|string',

                'password' =>
                    'required|string|min:6|confirmed',
            ], [
                'current_password.required' =>
                    'กรุณากรอกรหัสผ่านปัจจุบัน',

                'password.required' =>
                    'กรุณากรอกรหัสผ่านใหม่',

                'password.min' =>
                    'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',

                'password.confirmed' =>
                    'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
            ]);

            if (
                $customer->password &&
                !Hash::check(
                    $request->current_password,
                    $customer->password
                )
            ) {
                return back()->withErrors([
                    'current_password' =>
                        'รหัสผ่านปัจจุบันไม่ถูกต้อง',
                ]);
            }
        }

        $newHashedPassword =
            Hash::make(
                $request->password
            );

        $customer->update([
            'password' =>
                $newHashedPassword,
        ]);

        if ($user) {
            $user->update([
                'password' =>
                    $newHashedPassword,
            ]);
        }

        return back()->with(
            'success',
            'เปลี่ยนรหัสผ่านสำเร็จเรียบร้อย'
        );
    }
}