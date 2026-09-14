<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->user_id]);

        $stats = [
            'total_rentals' => Rental::where('customer_id', $customer->customer_id)->count(),
            'active_rentals' => Rental::where('customer_id', $customer->customer_id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'completed_rentals' => Rental::where('customer_id', $customer->customer_id)
                ->where('status', 'completed')
                ->count(),
            'reviews_count' => Review::where('customer_id', $customer->customer_id)->count(),
        ];

        return view('profile.index', compact('user', 'customer', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->user_id]);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $rawName = trim($request->name);
        $parts = preg_split('/\s+/', $rawName, 2);
        $firstName = $parts[0] ?? $rawName;
        $lastName = $parts[1] ?? '-';

        $customer->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return back()->with('success', 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
            'password.required' => 'กรุณากรอกรหัสผ่านใหม่',
            'password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'เปลี่ยนรหัสผ่านสำเร็จเรียบร้อย');
    }
}
