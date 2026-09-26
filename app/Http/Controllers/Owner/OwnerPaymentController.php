<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerPaymentController extends Controller
{
    /**
     * หน้าตรวจสอบการชำระเงิน
     */
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
        if (
            in_array(
                $status,
                [
                    'pending',
                    'approved',
                    'rejected',
                ],
                true
            )
        ) {
            $query->where(
                'status',
                $status
            );
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
        | จำนวนแต่ละสถานะ
        |--------------------------------------------------------------------------
        */
        $statusCounts = Payment::query()
            ->select(
                'status',
                DB::raw('count(*) as count')
            )
            ->groupBy('status')
            ->pluck(
                'count',
                'status'
            );

        $counts = [
            'pending' => $statusCounts->get(
                'pending',
                0
            ),

            'approved' => $statusCounts->get(
                'approved',
                0
            ),

            'rejected' => $statusCounts->get(
                'rejected',
                0
            ),
        ];

        return view(
            'owner.payments.index',
            compact(
                'payments',
                'counts'
            )
        );
    }

    /**
     * อนุมัติการชำระเงิน
     *
     * Flow:
     *
     * pending
     *   ↓
     * approved
     *
     * และ Rental:
     *
     * pending_verification
     *   ↓
     * renting
     */
    public function approve($id)
    {
        try {
            DB::transaction(
                function () use ($id) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Payment
                    |--------------------------------------------------------------------------
                    */
                    $payment = Payment::query()
                        ->lockForUpdate()
                        ->findOrFail($id);

                    /*
                    |--------------------------------------------------------------------------
                    | ป้องกันอนุมัติซ้ำ
                    |--------------------------------------------------------------------------
                    */
                    if (
                        $payment->status === 'approved'
                    ) {
                        throw new \RuntimeException(
                            'รายการชำระเงินนี้ได้รับการอนุมัติแล้ว'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ตรวจสอบว่ามี Rental
                    |--------------------------------------------------------------------------
                    */
                    $rental = $payment->rental;

                    if (!$rental) {
                        throw new \RuntimeException(
                            'ไม่พบรายการเช่าที่เชื่อมกับการชำระเงินนี้'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | อนุมัติเฉพาะรายการที่รอตรวจสอบ
                    |--------------------------------------------------------------------------
                    */
                    if (
                        !in_array(
                            $payment->status,
                            [
                                'pending',
                            ],
                            true
                        )
                    ) {
                        throw new \RuntimeException(
                            'รายการชำระเงินนี้ไม่อยู่ในสถานะที่สามารถอนุมัติได้'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 1. อัปเดต Payment
                    |--------------------------------------------------------------------------
                    |
                    | สำคัญ:
                    | - status = approved
                    | - paid_at = เวลาที่เจ้าของร้านอนุมัติ
                    |
                    */
                    $payment->update([
                        'status' => 'approved',
                        'paid_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | 2. อัปเดต Rental
                    |--------------------------------------------------------------------------
                    |
                    | เมื่ออนุมัติการชำระเงินแล้ว
                    | รายการเช่าต้องเข้าสู่ "กำลังเช่า"
                    |
                    */
                    $rental->update([
                        'status' => 'renting',
                    ]);
                }
            );

            return redirect()
                ->back()
                ->with(
                    'success',
                    'อนุมัติการชำระเงินเรียบร้อยแล้ว รายการเช่าเปลี่ยนเป็น "กำลังเช่า"'
                );
        } catch (\RuntimeException $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'ไม่สามารถอนุมัติการชำระเงินได้ กรุณาลองใหม่อีกครั้ง'
                );
        }
    }

    /**
     * ปฏิเสธการชำระเงิน
     *
     * Flow:
     *
     * pending
     *   ↓
     * rejected
     *
     * Rental:
     *
     * pending_verification
     *   ↓
     * pending_payment
     */
    public function reject(
        Request $request,
        $id
    ) {
        $request->validate(
            [
                'note' =>
                    'nullable|string|max:500',
            ],
            [
                'note.max' =>
                    'หมายเหตุต้องไม่เกิน 500 ตัวอักษร',
            ]
        );

        try {
            DB::transaction(
                function () use (
                    $request,
                    $id
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Payment
                    |--------------------------------------------------------------------------
                    */
                    $payment = Payment::query()
                        ->lockForUpdate()
                        ->findOrFail($id);

                    /*
                    |--------------------------------------------------------------------------
                    | ป้องกันปฏิเสธรายการที่อนุมัติแล้ว
                    |--------------------------------------------------------------------------
                    */
                    if (
                        $payment->status === 'approved'
                    ) {
                        throw new \RuntimeException(
                            'รายการนี้อนุมัติการชำระเงินแล้ว ไม่สามารถปฏิเสธได้'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ตรวจสอบ Rental
                    |--------------------------------------------------------------------------
                    */
                    $rental = $payment->rental;

                    if (!$rental) {
                        throw new \RuntimeException(
                            'ไม่พบรายการเช่าที่เชื่อมกับการชำระเงินนี้'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ต้องเป็น pending เท่านั้น
                    |--------------------------------------------------------------------------
                    */
                    if (
                        $payment->status !== 'pending'
                    ) {
                        throw new \RuntimeException(
                            'รายการชำระเงินนี้ไม่อยู่ในสถานะที่สามารถปฏิเสธได้'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | หมายเหตุ
                    |--------------------------------------------------------------------------
                    */
                    $note = trim(
                        (string) (
                            $request->input(
                                'note',
                                $payment->note
                            ) ?? ''
                        )
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | อัปเดต Payment
                    |--------------------------------------------------------------------------
                    */
                    $payment->update([
                        'status' => 'rejected',
                        'note' => $note !== ''
                            ? $note
                            : $payment->note,

                        /*
                        | ป้องกันกรณีมี paid_at จากข้อมูลเก่า
                        */
                        'paid_at' => null,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Rental กลับไปสถานะรอชำระเงิน
                    |--------------------------------------------------------------------------
                    */
                    $rental->update([
                        'status' =>
                            'pending_payment',
                    ]);
                }
            );

            return redirect()
                ->back()
                ->with(
                    'success',
                    'ปฏิเสธรายการชำระเงินเรียบร้อยแล้ว รายการเช่ากลับไปสถานะ "รอชำระเงิน"'
                );
        } catch (\RuntimeException $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'ไม่สามารถปฏิเสธการชำระเงินได้ กรุณาลองใหม่อีกครั้ง'
                );
        }
    }
}