<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class OwnerCustomerController extends Controller
{
    /**
     * แสดงรายการลูกค้าทั้งหมด พร้อมระบบค้นหาและแบ่งหน้า
     */
    public function index(Request $request)
    {
        $query = Customer::withCount('rentals')
            ->with(['rentals']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest('customer_id')->paginate(15)->withQueryString();

        return view('owner.customers.index', compact('customers'));
    }

    /**
     * แสดงฟอร์มสร้างข้อมูลลูกค้าใหม่
     */
    public function create()
    {
        return view('owner.customers.create');
    }

    /**
     * บันทึกข้อมูลลูกค้าใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:customers,email',
            'phone'      => 'nullable|string|max:20',
        ]);

        Customer::create($request->all());

        return redirect()->route('owner.customers.index')
            ->with('success', 'เพิ่มข้อมูลลูกค้าเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดข้อมูลของลูกค้าแต่ละคน
     */
    public function show($id)
    {
        $customer = Customer::with([
            'rentals.details.product',
            'rentals.latestPayment',
            'reviews.product'
        ])->findOrFail($id);

        $totalSpent = $customer->rentals->whereIn('status', ['confirmed', 'renting', 'returned', 'completed'])->sum('total_amount');

        return view('owner.customers.show', compact('customer', 'totalSpent'));
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลลูกค้า
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('owner.customers.edit', compact('customer'));
    }

    /**
     * อัปเดตข้อมูลลูกค้าลงในฐานข้อมูล
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            // ยกเว้น email ของตัวเองเวลาเช็ค unique
            'email'      => 'required|email|max:255|unique:customers,email,' . $customer->customer_id . ',customer_id',
            'phone'      => 'nullable|string|max:20',
        ]);

        $customer->update($request->all());

        return redirect()->route('owner.customers.index')
            ->with('success', 'อัปเดตข้อมูลลูกค้าเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลลูกค้า
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('owner.customers.index')
            ->with('success', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}