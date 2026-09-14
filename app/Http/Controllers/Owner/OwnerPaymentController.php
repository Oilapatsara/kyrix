<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Rental;
use Illuminate\Http\Request;

class OwnerPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['rental.customer', 'rental.details.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('rental', function ($q) use ($search) {
                $q->where('rental_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest('payment_id')->paginate(15)->withQueryString();

        $counts = [
            'all'      => Payment::count(),
            'pending'  => Payment::where('status', 'pending')->count(),
            'approved' => Payment::where('status', 'approved')->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
        ];

        return view('owner.payments.index', compact('payments', 'counts'));
    }

    public function approve($id)
    {
        $payment = Payment::with('rental')->findOrFail($id);
        $payment->update([
            'status'       => 'approved',
            'payment_date' => $payment->payment_date ?? now(),
        ]);

        // If rental is pending, advance to confirmed
        if ($payment->rental && in_array($payment->rental->status, ['pending', 'pending_payment', 'pending_verification'])) {
            $payment->rental->update(['status' => 'confirmed']);
        }

        return back()->with('success', "อนุมัติการชำระเงินยอด ฿" . number_format($payment->payment_amount, 2) . " เรียบร้อยแล้ว");
    }

    public function reject(Request $request, $id)
    {
        $payment = Payment::with('rental')->findOrFail($id);

        $request->validate([
            'note' => 'required|string|max:255',
        ], [
            'note.required' => 'กรุณาระบุเหตุผลการปฏิเสธสลิป',
        ]);

        $payment->update([
            'status' => 'rejected',
            'note'   => $request->note,
        ]);

        return back()->with('error', "ปฏิเสธสลิปการชำระเงินแล้ว เหตุผล: {$request->note}");
    }
}
