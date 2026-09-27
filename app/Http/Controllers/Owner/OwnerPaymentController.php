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

        // ดึงข้อมูล Payment พร้อม Rental และ Customer
        $query = Payment::query()
            ->with([
                'rental.customer',
            ])
            ->orderByDesc('payment_id');

        // Filter Status
        if (in_array(
            $status,
            [
                'pending',
                'approved',
                'rejected',
            ],
            true
        )) {
            $query->where(
                'status',
                $status
            );
        }

        // Pagination
        $payments = $query
            ->paginate(15)
            ->withQueryString();

        // จำนวนแต่ละสถานะ
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
     * Flow ปกติ:
     * pending
     *   ↓
     * approved
     *
     * Rental:
     * pending_verification
     *   ↓
     * renting
     *
     * กรณีรายการเก่าที่ Rental เป็น renting แล้ว:
     * pending
     *   ↓
     * approved
     *
     * Rental จะยังคงเป็น renting
     */
    public function approve($id)
    {
        try {
            DB::transaction(
                function () use ($id) {
                    // Lock Payment เพื่อป้องกันการกดอนุมัติซ้ำ
                    $payment = Payment::query()
                        ->lockForUpdate()
                        ->findOrFail($id);

                    // ต้องอนุมัติเฉพาะ Payment ที่ยัง pending
                    if ($payment->status !== 'pending') {
                        if ($payment->status === 'approved') {
                            throw new \RuntimeException(
                                'รายการชำระเงินนี้ได้รับการอนุมัติแล้ว'
                            );
                        }

                        throw new \RuntimeException(
                            'รายการชำระเงินนี้ไม่อยู่ในสถานะที่สามารถอนุมัติได้'
                        );
                    }

                    // ตรวจสอบ Rental
                    $rental = $payment->rental;

                    if (!$rental) {
                        throw new \RuntimeException(
                            'ไม่พบรายการเช่าที่เชื่อมกับการชำระเงินนี้'
                        );
                    }

                    /*
                     * อนุญาต 2 กรณี
                     *
                     * 1. pending_verification
                     *    = flow ปกติ
                     *
                     * 2. renting
                     *    = รายการเก่าที่ถูกเปลี่ยนเป็น renting
                     *      ก่อนอนุมัติ Payment
                     */
                    if (!in_array(
                        $rental->status,
                        [
                            'pending_verification',
                            'renting',
                        ],
                        true
                    )) {
                        throw new \RuntimeException(
                            'รายการเช่านี้ไม่อยู่ในสถานะที่สามารถตรวจสอบการชำระเงินได้'
                        );
                    }

                    // อัปเดต Payment
                    // status = approved
                    // paid_at = เวลาที่เจ้าของร้านอนุมัติ
                    $payment->update([
                        'status' => 'approved',
                        'paid_at' => now(),
                    ]);

                    /*
                     * ถ้า Rental ยังรอตรวจสอบ
                     * เปลี่ยนเป็นกำลังเช่า
                     *
                     * ถ้า Rental เป็น renting อยู่แล้ว
                     * ไม่ต้องเปลี่ยนซ้ำ
                     */
                    if ($rental->status === 'pending_verification') {
                        $rental->update([
                            'status' => 'renting',
                        ]);
                    }
                }
            );

            return redirect()
                ->back()
                ->with(
                    'success',
                    'อนุมัติการชำระเงินเรียบร้อยแล้ว ยอดเงินถูกบันทึกเป็นยอดชำระแล้ว'
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
     * pending
     *   ↓
     * rejected
     *
     * Rental:
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
                'note' => 'nullable|string|max:500',
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
                    // Lock Payment
                    $payment = Payment::query()
                        ->lockForUpdate()
                        ->findOrFail($id);

                    // ต้องเป็น pending เท่านั้น
                    if ($payment->status !== 'pending') {
                        if ($payment->status === 'approved') {
                            throw new \RuntimeException(
                                'รายการนี้อนุมัติการชำระเงินแล้ว ไม่สามารถปฏิเสธได้'
                            );
                        }

                        throw new \RuntimeException(
                            'รายการชำระเงินนี้ไม่อยู่ในสถานะที่สามารถปฏิเสธได้'
                        );
                    }

                    // ตรวจสอบ Rental
                    $rental = $payment->rental;

                    if (!$rental) {
                        throw new \RuntimeException(
                            'ไม่พบรายการเช่าที่เชื่อมกับการชำระเงินนี้'
                        );
                    }

                    // ปฏิเสธได้เฉพาะ Rental ที่กำลังรอตรวจสอบ
                    if ($rental->status !== 'pending_verification') {
                        throw new \RuntimeException(
                            'รายการเช่านี้ไม่ได้อยู่ในสถานะรอตรวจสอบการชำระเงิน จึงไม่สามารถปฏิเสธรายการนี้ได้'
                        );
                    }

                    // รับหมายเหตุจากเจ้าของร้าน
                    $note = trim(
                        (string) (
                            $request->input(
                                'note',
                                $payment->note
                            ) ?? ''
                        )
                    );

                    // อัปเดต Payment
                    $payment->update([
                        'status' => 'rejected',
                        'note' => $note !== ''
                            ? $note
                            : $payment->note,
                        'paid_at' => null,
                    ]);

                    // Rental กลับไปสถานะรอชำระเงิน
                    $rental->update([
                        'status' => 'pending_payment',
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