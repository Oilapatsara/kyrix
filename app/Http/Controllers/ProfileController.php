<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Rental;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /** ดึง Customer ที่ login อยู่จาก session */
    private function getCustomer(): Customer
    {
        return Customer::findOrFail(session('customer_id'));
    }

    public function index()
    {
        $customer = $this->getCustomer();

        $stats = [
            'total_rentals'     => Rental::where('customer_id', $customer->customer_id)->count(),
            'active_rentals'    => Rental::where('customer_id', $customer->customer_id)
                                         ->whereNotIn('status', ['completed', 'cancelled'])
                                         ->count(),
            'completed_rentals' => Rental::where('customer_id', $customer->customer_id)
                                         ->where('status', 'completed')
                                         ->count(),
            'reviews_count'     => Review::where('customer_id', $customer->customer_id)->count(),
        ];

        return view('profile.index', compact('customer', 'stats'));
    }

    public function update(Request $request)
    {
        $customer = $this->getCustomer();

        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => ['required', 'email', 'max:150',
                          Rule::unique('customers', 'email')->ignore($customer->customer_id, 'customer_id')],
            'phone'   => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
        ], [
            'name.required'  => 'กรุณากรอกชื่อ-นามสกุล',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email'    => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'   => 'อีเมลนี้ถูกใช้งานแล้ว',
        ]);

        $rawName  = trim($request->name);
        $parts    = preg_split('/\s+/u', $rawName, 2);
        $firstName = $parts[0] ?? $rawName;
        $lastName  = $parts[1] ?? '-';

        $customer->update([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'address'    => $request->address,
        ]);

        // อัปเดต session ชื่อด้วย
        session(['customer_name'  => trim($firstName . ' ' . $lastName),
                 'customer_email' => $request->email]);

        return back()->with('success', 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
            'password.required'         => 'กรุณากรอกรหัสผ่านใหม่',
            'password.min'              => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed'        => 'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
        ]);

        $customer = $this->getCustomer();

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
        }

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'เปลี่ยนรหัสผ่านสำเร็จเรียบร้อย');
    }
}
