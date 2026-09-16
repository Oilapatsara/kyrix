<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerPaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        /*
        |--------------------------------------------------------------------------
        | ดึงข้อมูล Payment พร้อม Rental และ Customer
        |--------------------------------------------------------------------------
        */
        $query = Payment::query()
            ->with([
                'rental.customer',
            ])
            ->orderByDesc('payment_id');

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $payments = $query
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | จำนวนแต่ละสถานะ (ปรับเป็น 1 Query เพื่อลดภาระ Database)
        |--------------------------------------------------------------------------
        */
        $statusCounts = Payment::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'pending'  => $statusCounts->get('pending', 0),
            'approved' => $statusCounts->get('approved', 0),
            'rejected' => $statusCounts->get('rejected', 0),
        ];

        return view('owner.payments.index', compact(
            'payments',
            'counts'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | อนุมัติการชำระเงิน (Approve Payment)
    |--------------------------------------------------------------------------
    */
    public function approve($id)
    {
        $payment = Payment::findOrFail($id);

        DB::transaction(function () use ($payment) {
            // 1. อัปเดตสถานะการชำระเงินเป็น approved
            $payment->update([
                'status' => 'approved',
            ]);

            // 2. อัปเดตสถานะใบเช่า (Rental) เป็น renting
            if ($payment->rental) {
                $payment->rental->update([
                    'status' => 'renting',
                ]);
            }
        });

        return redirect()->back()->with('success', 'อนุมัติการชำระเงินเรียบร้อยแล้ว');
    }

    /*
    |--------------------------------------------------------------------------
    | ปฏิเสธการชำระเงิน (Reject Payment)
    |--------------------------------------------------------------------------
    */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $payment = Payment::findOrFail($id);

        $payment->update([
            'status' => 'rejected',
            'note'   => $request->input('note', $payment->note),
        ]);

        return redirect()->back()->with('success', 'ปฏิเสธรายการชำระเงินเรียบร้อยแล้ว');
    }
}