<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OwnerReturnController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $query = Rental::with(['customer', 'details.product.images', 'latestPayment']);

        $tab = $request->get('tab', 'active');

        if ($tab === 'overdue') {
            $query->whereIn('status', ['renting', 'confirmed'])
                  ->where('end_date', '<', $today);
        } elseif ($tab === 'returned') {
            $query->whereIn('status', ['returned', 'completed']);
        } else { // active
            $query->whereIn('status', ['renting', 'pending_return', 'confirmed']);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('rental_code', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $rentals = $query->orderBy('end_date', 'asc')->paginate(12)->withQueryString();

        $counts = [
            'active'   => Rental::whereIn('status', ['renting', 'pending_return', 'confirmed'])->count(),
            'overdue'  => Rental::whereIn('status', ['renting', 'confirmed'])->where('end_date', '<', $today)->count(),
            'returned' => Rental::whereIn('status', ['returned', 'completed'])->count(),
        ];

        return view('owner.returns.index', compact('rentals', 'counts', 'tab'));
    }

    public function confirmReturn(Request $request, $id)
    {
        $rental = Rental::with('details.product')->findOrFail($id);

        if (in_array($rental->status, ['returned', 'completed'], true)) {
            return back()->with('error', 'รายการนี้ถูกตรวจรับคืนแล้ว');
        }

        $request->validate([
            'condition_status' => 'required|in:good,damaged',
            'damage_note'      => 'nullable|string|max:1000',
            'damage_image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'refund_slip'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'return_note'      => 'nullable|string|max:500',
        ], [
            'condition_status.required' => 'กรุณาระบุผลการตรวจสภาพชุด (สมบูรณ์ หรือ มีความเสียหาย)',
            'damage_image.image'        => 'รูปภาพหลักฐานความเสียหายต้องเป็นไฟล์รูปภาพเท่านั้น',
            'damage_image.max'          => 'ขนาดรูปภาพความเสียหายต้องไม่เกิน 5MB',
            'refund_slip.image'         => 'สลิปหลักฐานการคืนเงินต้องเป็นไฟล์รูปภาพเท่านั้น',
            'refund_slip.max'           => 'ขนาดรูปภาพสลิปต้องไม่เกิน 5MB',
        ]);

        $condition = $request->condition_status;
        $damageImagePath = null;
        $refundSlipPath = null;

        // Directory setup
        $returnDir = public_path('uploads/returns');
        if (!is_dir($returnDir)) {
            mkdir($returnDir, 0755, true);
        }

        // Handle Damage Image Upload
        if ($request->hasFile('damage_image')) {
            $file = $request->file('damage_image');
            $filename = 'damage_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($returnDir, $filename);
            $damageImagePath = 'uploads/returns/' . $filename;
        }

        // Handle Refund Slip Upload
        if ($request->hasFile('refund_slip')) {
            $file = $request->file('refund_slip');
            $filename = 'refund_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($returnDir, $filename);
            $refundSlipPath = 'uploads/returns/' . $filename;
        }

        // เงินมัดจำประกันชุดกำหนดไว้ที่ 100 บาทเสมอ (ไม่เกี่ยวกับค่าเช่าชุด)
        $depositAmount = 100.00;

        if ($condition === 'good') {
            $depositStatus = 'refunded';
            $refundAmount = $depositAmount;
            $note = "รับคืนชุดเรียบร้อย: สภาพชุดสมบูรณ์ ไม่พบความเสียหาย | คืนเงินมัดจำเต็มจำนวน ฿" . number_format($refundAmount, 2);
        } else {
            $depositStatus = 'forfeited';
            $refundAmount = 0.00;
            $reason = $request->damage_note ? " (สาเหตุ: {$request->damage_note})" : '';
            $note = "รับคืนชุดเรียบร้อย: ตรวจพบชุดชำรุด/เสียหาย | ยึดเงินมัดจำ ฿" . number_format($depositAmount, 2) . " ไม่คืนเงินมัดจำ{$reason}";
        }

        if ($request->return_note) {
            $note .= " | บันทึกเพิ่มเติม: " . $request->return_note;
        }

        // Update Rental
        $updateData = [
            'status'                => 'returned',
            'condition_status'      => $condition,
            'deposit_status'        => $depositStatus,
            'deposit_refund_amount' => $refundAmount,
            'damage_note'           => $request->damage_note,
            'inspected_at'          => now(),
            'note'                  => $rental->note ? ($rental->note . "\n" . $note) : $note,
        ];

        if ($damageImagePath) {
            $updateData['damage_image'] = $damageImagePath;
        }
        if ($refundSlipPath) {
            $updateData['refund_slip'] = $refundSlipPath;
        }

        $rental->update($updateData);

        // Restore stock
        foreach ($rental->details as $detail) {
            if ($detail->product) {
                $detail->product->increment('stock', $detail->quantity);
                if ($detail->product->status === 'rented') {
                    $detail->product->update(['status' => 'available']);
                }
            }
        }

        $resultMsg = $condition === 'good'
            ? "ตรวจรับคืนชุดเรียบร้อย: ชุดสมบูรณ์ คืนเงินมัดจำ ฿" . number_format($depositAmount, 2) . " ทันที"
            : "ตรวจรับคืนชุดเรียบร้อย: ชุดเสียหาย บันทึกยึดเงินมัดจำ ฿" . number_format($depositAmount, 2) . " (ไม่คืนเงิน)";

        return back()->with('success', "{$resultMsg} สำหรับคำสั่ง #{$rental->formatted_code}");
    }
}
