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

        $request->validate([
            'deposit_refund' => 'required|in:full,partial,none',
            'deduction'      => 'nullable|numeric|min:0',
            'return_note'    => 'nullable|string|max:500',
        ]);

        $note = "รับคืนชุดเรียบร้อย: สภาพชุดสมบูรณ์";
        if ($request->deposit_refund === 'full') {
            $note .= " | คืนเงินมัดจำเต็มจำนวน ฿" . number_format($rental->deposit_amount, 2);
        } elseif ($request->deposit_refund === 'partial') {
            $note .= " | หักค่าเสียหาย/ปรับ ฿" . number_format($request->deduction, 2) . " คืนมัดจำส่วนที่เหลือ";
        } else {
            $note .= " | ยึดเงินมัดจำเนื่องจากชุดชำรุดเสียหายหนัก";
        }

        if ($request->return_note) {
            $note .= " (" . $request->return_note . ")";
        }

        $rental->update([
            'status' => 'returned',
            'note'   => $rental->note ? ($rental->note . "\n" . $note) : $note,
        ]);

        // Restore stock
        foreach ($rental->details as $detail) {
            if ($detail->product) {
                $detail->product->increment('stock', $detail->quantity);
            }
        }

        return back()->with('success', "บันทึกการรับคืนชุดและจัดการเงินมัดจำสำหรับคำสั่ง {$rental->formatted_code} เรียบร้อยแล้ว");
    }
}
