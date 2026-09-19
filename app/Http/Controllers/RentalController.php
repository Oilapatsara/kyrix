<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Farzai\PromptPay\PromptPay;

class RentalController extends Controller
{
    /**
     * สร้างรายการเช่า
     */
    public function book(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'note' => 'nullable|string|max:500',
        ], [
            'start_date.required' => 'กรุณาระบุวันที่เริ่มเช่า',
            'start_date.after_or_equal' => 'วันที่เริ่มเช่าต้องไม่ย้อนหลัง',
            'end_date.required' => 'กรุณาระบุวันที่คืนชุด',
            'end_date.after_or_equal' => 'วันที่คืนชุดต้องไม่น้อยกว่าวันที่เริ่มเช่า',
            'quantity.required' => 'กรุณาระบุจำนวนชุด',
            'quantity.min' => 'จำนวนชุดต้องอย่างน้อย 1 ชุด',
            'quantity.max' => 'จำนวนชุดเกินสต็อกที่มี (' . $product->stock . ' ชุด)',
        ]);

        $customer = $this->getCustomer();

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // นับจำนวนวันแบบรวมวันเริ่มและวันสิ้นสุด
        $days = max(1, $start->diffInDays($end) + 1);

        $quantity = (int) $request->quantity;
        $pricePerDay = (float) $product->rental_price;

        $subtotal = $pricePerDay * $days * $quantity;
        $depositTotal = (float) $product->deposit * $quantity;

        DB::beginTransaction();

        try {
            // Lock product ป้องกัน stock ชนกัน
            $product = Product::where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $product->status !== 'available' ||
                $product->stock < $quantity
            ) {
                throw new \RuntimeException(
                    'ชุดนี้ไม่อยู่ในสถานะว่างพร้อมเช่าหรือสต็อกไม่เพียงพอ'
                );
            }

            // สร้างรหัสรายการเช่า
            $rentalCode = 'KR-' . date('Ym') . '-' .
                str_pad(
                    Rental::count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            // คำนวณโปรโมชั่น
            $promo = \App\Services\PromotionService::calculateDiscount(
                $quantity,
                $subtotal
            );

            // สร้างรายการเช่า
            $rental = Rental::create([
                'rental_code' => $rentalCode,
                'customer_id' => $customer->customer_id,
                'rental_date' => now()->toDateString(),
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'total_amount' => $subtotal,
                'discount_amount' => $promo['discount_amount'],
                'discount_reason' => $promo['discount_reason'],
                'deposit_amount' => $depositTotal,
                'status' => 'pending_payment',
                'note' => $request->note,
            ]);

            // รายละเอียดชุด
            RentalDetail::create([
                'rental_id' => $rental->rental_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'price' => $pricePerDay,
                'subtotal' => $subtotal,
                'selected_size' => $request->size,
                'selected_color' => $request->color,
                'rental_days' => $days,
            ]);

            // ตัด Stock
            $product->decrement('stock', $quantity);

            // เพิ่มจำนวนครั้งเช่า
            $product->increment('rental_count');

            // ถ้า stock หมด ให้เปลี่ยนสถานะเป็น rented
            if ($product->fresh()->stock <= 0) {
                $product->update([
                    'status' => 'rented',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('rentals.payment', $rental->rental_id)
                ->with(
                    'success',
                    'บันทึกการจองเช่าชุดเรียบร้อยแล้ว กรุณาดำเนินการชำระเงิน'
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'เกิดข้อผิดพลาดในการบันทึกการจอง: ' . $e->getMessage()
                )
                ->withInput();
        }
    }

    /**
     * หน้า Payment
     */
    public function payment($id)
    {
        $customer = $this->getCustomer();

        $rental = Rental::with([
            'details.product.mainImage',
            'payments',
        ])->find($id);

        if (!$rental) {
            abort(404, 'ไม่พบรายการเช่า ID: ' . $id);
        }

        if (
            (int) $rental->customer_id !==
            (int) $customer->customer_id
        ) {
            abort(
                403,
                'รายการเช่านี้ไม่ได้เป็นของบัญชีลูกค้าปัจจุบัน'
            );
        }

        // ถ้ายกเลิกแล้ว ไม่ควรเข้าสู่ขั้นตอนชำระเงิน
        if ($rental->status === 'cancelled') {
            return redirect()
                ->route('rentals.history')
                ->with(
                    'error',
                    'รายการเช่านี้ถูกยกเลิกแล้ว ไม่สามารถชำระเงินได้'
                );
        }

        // คำนวณยอดชำระ
        $rentalAmount = (float) ($rental->total_amount ?? 0);
        $discountAmount = (float) ($rental->discount_amount ?? 0);
        $netRentalAmount = max(
            0,
            $rentalAmount - $discountAmount
        );

        $depositAmount = (float) ($rental->deposit_amount ?? 0);
        $serviceFee = (float) ($rental->service_fee ?? 0);

        $paymentAmount = round(
            $netRentalAmount +
            $depositAmount +
            $serviceFee,
            2
        );

        // PromptPay
        $promptPayPhone = preg_replace(
            '/\D/',
            '',
            (string) env('PROMPTPAY_PHONE')
        );

        $promptPayQr = null;

        if (
            $promptPayPhone !== '' &&
            $paymentAmount > 0
        ) {
            $promptPayQr = PromptPay::qrCode(
                $promptPayPhone,
                $paymentAmount
            )
                ->toDataUri('svg')
                ->getData();
        }

        return view(
            'rentals.payment',
            compact(
                'rental',
                'promptPayQr',
                'promptPayPhone',
                'paymentAmount'
            )
        );
    }

    /**
     * บันทึกการชำระเงิน
     */
    public function submitPayment(Request $request, $id)
    {
        $customer = $this->getCustomer();

        $rental = Rental::where(
            'customer_id',
            $customer->customer_id
        )->findOrFail($id);

        // ชำระเงินได้เฉพาะรายการที่ยังรอชำระ
        if ($rental->status !== 'pending_payment') {
            return back()->with(
                'error',
                'รายการนี้ไม่อยู่ในสถานะที่สามารถชำระเงินได้'
            );
        }

        $request->validate([
            'payment_method' => 'required|in:transfer,qr',
            'slip_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'note' => 'nullable|string|max:500',
        ], [
            'payment_method.required' => 'กรุณาเลือกวิธีการชำระเงิน',
            'slip_image.required' => 'กรุณาแนบรูปภาพสลิปหลักฐานการโอนเงิน',
            'slip_image.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'slip_image.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB',
        ]);

        /*
         * คำนวณยอดที่ต้องชำระให้ตรงกับหน้า Payment
         *
         * ค่าเช่าสุทธิ
         * = total_amount - discount_amount
         *
         * ยอดชำระ
         * = ค่าเช่าสุทธิ + deposit_amount + service_fee
         */
        $rentalAmount = (float) ($rental->total_amount ?? 0);
        $discountAmount = (float) ($rental->discount_amount ?? 0);
        $depositAmount = (float) ($rental->deposit_amount ?? 0);
        $serviceFee = (float) ($rental->service_fee ?? 0);

        $netRentalAmount = max(
            0,
            $rentalAmount - $discountAmount
        );

        $paymentAmount = round(
            $netRentalAmount +
            $depositAmount +
            $serviceFee,
            2
        );

        // สร้างโฟลเดอร์เก็บสลิป
        $uploadDir = public_path('uploads/slips');

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // รับไฟล์สลิป
        $file = $request->file('slip_image');

        $filename =
            'slip_' .
            time() .
            '_' .
            uniqid() .
            '.' .
            $file->getClientOriginalExtension();

        $file->move(
            $uploadDir,
            $filename
        );

        $slipPath =
            'uploads/slips/' .
            $filename;

        // บันทึก Payment ลงฐานข้อมูล
        Payment::create([
            'rental_id' => $rental->rental_id,

            // ชื่อตรงกับ column จริงในตาราง payments
            'amount' => $paymentAmount,

            'paid_at' => now(),

            'payment_method' => $request->payment_method,

            'slip_image' => $slipPath,

            'status' => 'pending',

            'note' => $request->note ??
                'ชำระเงินค่าเช่าชุด (ค่าเช่า ฿' .
                number_format($netRentalAmount, 2) .
                ' + มัดจำ ฿' .
                number_format($depositAmount, 2) .
                ' + ค่าบริการ ฿' .
                number_format($serviceFee, 2) .
                ')',
        ]);

        // เปลี่ยนสถานะรายการเช่า
        $rental->update([
            'status' => 'pending_verification',
        ]);

        return redirect()
            ->route(
                'rentals.show',
                $rental->rental_id
            )
            ->with(
                'success',
                'ส่งหลักฐานการชำระเงินเรียบร้อยแล้ว สถานะ: รอตรวจสอบสลิป'
            );
    }

    /**
     * รายการเช่าปัจจุบัน
     */
    public function index()
    {
        $customer = $this->getCustomer();

        $activeRentals = Rental::where(
            'customer_id',
            $customer->customer_id
        )
            ->whereNotIn('status', [
                'returned',
                'completed',
                'cancelled',
            ])
            ->with([
                'details.product.mainImage',
                'payments',
            ])
            ->latest()
            ->get();

        return view(
            'rentals.index',
            compact('activeRentals')
        );
    }

    /**
     * ประวัติการเช่า
     */
    public function history()
    {
        $customer = $this->getCustomer();

        $pastRentals = Rental::where(
            'customer_id',
            $customer->customer_id
        )
            ->whereIn('status', [
                'returned',
                'completed',
                'cancelled',
            ])
            ->with([
                'details.product.mainImage',
                'payments',
            ])
            ->latest()
            ->paginate(10);

        return view(
            'rentals.history',
            compact('pastRentals')
        );
    }

    /**
     * รายละเอียดรายการเช่า
     */
    public function show($id)
    {
        $customer = $this->getCustomer();

        $rental = Rental::where(
            'customer_id',
            $customer->customer_id
        )
            ->with([
                'details.product.mainImage',
                'payments',
            ])
            ->findOrFail($id);

        return view(
            'rentals.show',
            compact('rental')
        );
    }

    /**
     * อัปโหลดสลิป
     */
    public function uploadSlip(
        Request $request,
        $id
    ) {
        return $this->submitPayment(
            $request,
            $id
        );
    }

    /**
     * แจ้งคืนชุด
     */
    public function requestReturn(
        Request $request,
        $id
    ) {
        $customer = $this->getCustomer();

        $rental = Rental::where(
            'customer_id',
            $customer->customer_id
        )->findOrFail($id);

        if (
            !in_array(
                $rental->status,
                [
                    'confirmed',
                    'ready_pickup',
                    'renting',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'รายการนี้ยังไม่พร้อมแจ้งคืนชุด'
            );
        }

        $data = $request->validate([
            'return_method' => 'nullable|string|max:100',
            'return_tracking_no' => 'nullable|string|max:100',
        ]);

        $returnTrackingNo =
            $data['return_tracking_no'] ?? null;

        $returnNote = trim(
            ($data['return_method'] ?? '') .
            (
                $returnTrackingNo
                    ? ' | เลขพัสดุส่งคืน: ' .
                        $returnTrackingNo
                    : ''
            )
        );

        $note = $rental->note;

        if ($returnNote !== '') {
            $note = $note
                ? $note .
                    "\nแจ้งคืนจากลูกค้า: " .
                    $returnNote
                : 'แจ้งคืนจากลูกค้า: ' .
                    $returnNote;
        }

        $rental->update([
            'status' => 'pending_return',
            'return_tracking_no' =>
                $returnTrackingNo ??
                $rental->return_tracking_no,
            'note' => $note,
        ]);

        return back()->with(
            'success',
            'แจ้งส่งคืนชุดเรียบร้อยแล้ว รอเจ้าของร้านตรวจรับและจัดการเงินมัดจำ'
        );
    }

    /**
     * ลูกค้าขอยกเลิกรายการเช่า
     *
     * ยกเลิกได้เฉพาะรายการที่ยังไม่ได้ชำระเงิน
     *
     * เมื่อยกเลิก:
     * - rentals.status = cancelled
     * - rentals.note เก็บเหตุผล
     * - คืน stock
     * - ลด rental_count
     * - ไม่ต้องให้ Owner อนุมัติ
     */
    public function cancel(Request $request, $id)
    {
        $customer = $this->getCustomer();

        $data = $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ], [
            'cancel_reason.required' => 'กรุณาระบุเหตุผลในการยกเลิก',
            'cancel_reason.max' => 'เหตุผลในการยกเลิกต้องไม่เกิน 500 ตัวอักษร',
        ]);

        try {
            DB::transaction(function () use (
                $customer,
                $id,
                $data
            ) {
                // Lock รายการเช่าเพื่อป้องกันข้อมูลชนกัน
                $rental = Rental::where(
                    'customer_id',
                    $customer->customer_id
                )
                    ->with('details')
                    ->lockForUpdate()
                    ->findOrFail($id);

                // อนุญาตเฉพาะรายการที่ยังไม่ได้ชำระ
                if ($rental->status !== 'pending_payment') {
                    throw new \RuntimeException(
                        'รายการนี้ไม่สามารถยกเลิกได้ เนื่องจากรายการได้เข้าสู่ขั้นตอนชำระเงินหรือขั้นตอนถัดไปแล้ว'
                    );
                }

                // คืน Stock กลับให้สินค้า
                foreach ($rental->details as $detail) {
                    $product = Product::where(
                        'product_id',
                        $detail->product_id
                    )
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        continue;
                    }

                    $quantity = max(
                        1,
                        (int) ($detail->quantity ?? 1)
                    );

                    // คืน stock
                    $product->increment(
                        'stock',
                        $quantity
                    );

                    // ลดจำนวนครั้งเช่า
                    $product->rental_count = max(
                        0,
                        (int) ($product->rental_count ?? 0) - $quantity
                    );

                    // ถ้า Stock กลับมาแล้ว
                    if (
                        (int) $product->stock > 0 &&
                        in_array(
                            $product->status,
                            ['rented', 'busy'],
                            true
                        )
                    ) {
                        $product->status = 'available';
                    }

                    $product->save();
                }

                // เก็บประวัติการยกเลิกลงใน note
                $cancelNote =
                    'ยกเลิกโดยลูกค้าเมื่อ ' .
                    now()->format('d/m/Y H:i') .
                    "\nเหตุผล: " .
                    trim($data['cancel_reason']);

                $note = $rental->note;

                $note = $note
                    ? $note . "\n" . $cancelNote
                    : $cancelNote;

                // เปลี่ยนสถานะเป็น cancelled
                $rental->update([
                    'status' => 'cancelled',
                    'note' => $note,
                ]);
            });

            // หลังยกเลิกสำเร็จ → ไปหน้าประวัติการเช่า
            return redirect()
                ->route('rentals.history')
                ->with(
                    'success',
                    'ยกเลิกรายการเช่าเรียบร้อยแล้ว ระบบคืนจำนวนชุดเข้าสต็อกแล้ว'
                );
        } catch (\RuntimeException $e) {
            return back()->with(
                'error',
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                'ไม่สามารถยกเลิกรายการเช่าได้ กรุณาลองใหม่อีกครั้ง'
            );
        }
    }

    /**
     * ดึงข้อมูลลูกค้าที่ Login อยู่
     */
    private function getCustomer(): Customer
    {
        $customerId = session('customer_id');

        if (!$customerId) {
            abort(
                403,
                'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
            );
        }

        $customer = Customer::find(
            $customerId
        );

        if (!$customer) {
            session()->forget([
                'customer_logged_in',
                'customer_id',
                'customer_name',
                'customer_email',
            ]);

            abort(
                403,
                'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
            );
        }

        return $customer;
    }
}