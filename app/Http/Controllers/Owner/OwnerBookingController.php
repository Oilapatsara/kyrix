<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\RentalDetail;
use App\Models\Product;
use Illuminate\Http\Request;

class OwnerBookingController extends Controller
{
    /**
     * แสดงรายการจอง/เช่าทั้งหมด พร้อมระบบค้นหาและกรองสถานะ
     */
    public function index(Request $request)
    {
        $query = Rental::with(['customer', 'details.product.images', 'latestPayment']);

        // กรองตามสถานะ (Status Filtering)
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'pending') {
                $query->whereIn('status', ['pending', 'pending_payment', 'pending_verification']);
            } else {
                $query->where('status', $status);
            }
        }

        // ค้นหาตามรหัสเช่า, เบอร์โทร หรือข้อมูลลูกค้า
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('rental_code', 'like', "%{$search}%")
                  ->orWhere('rental_id', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->latest('rental_id')->paginate(15)->withQueryString();

        // นับจำนวนรายการแยกตามสถานะสำหรับแสดง Badge เมนูด้านบน
        $counts = [
            'all'       => Rental::count(),
            'pending'   => Rental::whereIn('status', ['pending', 'pending_payment', 'pending_verification'])->count(),
            'confirmed' => Rental::where('status', 'confirmed')->count(),
            'renting'   => Rental::where('status', 'renting')->count(),
            'returned'  => Rental::whereIn('status', ['returned', 'completed'])->count(),
            'cancelled' => Rental::where('status', 'cancelled')->count(),
        ];

        return view('owner.bookings.index', compact('bookings', 'counts'));
    }

    /**
     * แสดงรายละเอียดข้อมูลการจองเชิงลึก
     */
    public function show($id)
    {
        $rental = Rental::with([
            'customer',
            'details.product.images',
            'payments',
            'reviews'
        ])->findOrFail($id);

        return view('owner.bookings.show', compact('rental'));
    }

    /**
     * อัปเดตสถานะการเช่าและจัดการสต็อกสินค้าอัตโนมัติ
     */
    public function updateStatus(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);

        $data = $request->validate([
            'status'             => 'required|in:pending,confirmed,renting,returned,completed,cancelled',
            'tracking_number'    => 'nullable|string|max:100',
            'return_tracking_no' => 'nullable|string|max:100',
            'note'               => 'nullable|string',
        ]);

        $oldStatus = $rental->status;
        $rental->update($data);

        // หากเปลี่ยนเป็นสถานะคืนชุดแล้ว (returned หรือ completed) และแต่เดิมยังไม่ได้คืน ให้นำสต็อกสินค้ากลับเข้าคลัง
        $isNowReturned = in_array($data['status'], ['returned', 'completed']);
        $wasPreviouslyReturned = in_array($oldStatus, ['returned', 'completed']);

        if ($isNowReturned && !$wasPreviouslyReturned) {
            foreach ($rental->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('stock', $detail->quantity);
                }
            }
        }

        return redirect()->back()->with('success', "อัปเดตสถานะคำสั่งเช่า {$rental->formatted_code} เรียบร้อยแล้ว");
    }
}