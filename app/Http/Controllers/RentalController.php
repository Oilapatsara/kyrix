<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalDetail;
use Carbon\Carbon;
use Farzai\PromptPay\PromptPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
     * สร้างรายการเช่า
     */
    public function book(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate(
            [
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'quantity' => 'required|integer|min:1|max:' . max(1, (int) $product->stock),
                'size' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:500',
            ],
            [
                'start_date.required' => 'กรุณาระบุวันที่เริ่มเช่า',
                'start_date.after_or_equal' => 'วันที่เริ่มเช่าต้องไม่ย้อนหลัง',
                'end_date.required' => 'กรุณาระบุวันที่คืนชุด',
                'end_date.after_or_equal' => 'วันที่คืนชุดต้องไม่น้อยกว่าวันที่เริ่มเช่า',
                'quantity.required' => 'กรุณาระบุจำนวนชุด',
                'quantity.min' => 'จำนวนชุดต้องอย่างน้อย 1 ชุด',
                'quantity.max' => 'จำนวนชุดเกินสต็อกที่มี (' . $product->stock . ' ชุด)',
                'size.max' => 'ขนาดต้องไม่เกิน 50 ตัวอักษร',
                'color.max' => 'สีต้องไม่เกิน 255 ตัวอักษร',
            ]
        );

        $customer = $this->getCustomer();

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // จำนวนวันใช้สำหรับแสดงระยะเวลาเช่า
        $days = max(
            1,
            $start->diffInDays($end) + 1
        );

        $quantity = (int) $request->quantity;

        // ระบบคิดราคาเช่าต่อ 1 ครั้ง ไม่คูณจำนวนวัน
        $pricePerRental = (float) $product->rental_price;
        $subtotal = $pricePerRental * $quantity;

        $depositTotal = (float) $product->deposit * $quantity;

        DB::beginTransaction();

        try {
            // Lock สินค้าก่อนตรวจและตัดสต็อก
            $product = Product::where(
                'product_id',
                $productId
            )
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $product->status !== 'available' ||
                (int) $product->stock < $quantity
            ) {
                throw new \RuntimeException(
                    'ชุดนี้ไม่อยู่ในสถานะว่างพร้อมเช่าหรือสต็อกไม่เพียงพอ'
                );
            }

            // จัดการไซซ์
            $selectedSize = $this->normalizeSelectedSize(
                $request->size
            );

            // จัดการสี
            $selectedColor = $this->resolveSelectedColor(
                $request->color,
                $product
            );

            // รหัสการเช่า
            $rentalCode =
                'KR-' .
                date('Ym') .
                '-' .
                str_pad(
                    Rental::count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            // คำนวณส่วนลดโปรโมชั่น
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

                // สถานะเริ่มต้น
                'status' => 'pending_payment',

                // ระบบคืนชุด
                'return_status' => 'not_returned',
                'return_method' => null,
                'return_tracking_number' => null,
                'return_shipping_carrier' => null,
                'return_tracking_no' => null,
                'return_shipping_status' => null,
                'return_shipped_at' => null,
                'return_estimated_delivery_at' => null,
                'return_requested_at' => null,
                'return_received_at' => null,

                'note' => $request->note,
            ]);

            // ใช้ที่อยู่และเบอร์โทรล่าสุดของลูกค้า
            $rental->delivery_address = trim(
                (string) ($customer->address ?? '')
            );

            $rental->recipient_phone = trim(
                (string) ($customer->phone ?? '')
            );

            $rental->save();

            // สร้าง Rental Detail
            RentalDetail::create([
                'rental_id' => $rental->rental_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'price' => $pricePerRental,
                'subtotal' => $subtotal,
                'selected_size' => $selectedSize,
                'selected_color' => $selectedColor,
                'rental_days' => $days,
            ]);

            // ตัดสต็อก
            $product->decrement(
                'stock',
                $quantity
            );

            // เพิ่มจำนวนครั้งที่เช่า
            $product->increment(
                'rental_count'
            );

            $product->refresh();

            // ถ้าสต็อกหมด ให้เปลี่ยนเป็น rented
            if ((int) $product->stock <= 0) {
                $product->update([
                    'status' => 'rented',
                ]);
            }

            DB::commit();

            return redirect()
                ->route(
                    'rentals.payment',
                    $rental->rental_id
                )
                ->with(
                    'success',
                    'บันทึกการจองเช่าชุดเรียบร้อยแล้ว กรุณาดำเนินการชำระเงิน'
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'เกิดข้อผิดพลาดในการบันทึกการจอง: ' .
                    $e->getMessage()
                )
                ->withInput();
        }
    }

    /**
     * หน้า Payment / Checkout
     */
    public function payment($id)
    {
        $customer = $this->getCustomer();

        $rental = Rental::with([
            'details.product.mainImage',
            'payments',
        ])->find($id);

        if (!$rental) {
            abort(
                404,
                'ไม่พบรายการเช่า ID: ' . $id
            );
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

        if ($rental->status === 'cancelled') {
            return redirect()
                ->route('rentals.history')
                ->with(
                    'error',
                    'รายการเช่านี้ถูกยกเลิกแล้ว ไม่สามารถชำระเงินได้'
                );
        }

        $rentalAmount = (float) (
            $rental->total_amount ?? 0
        );

        $discountAmount = (float) (
            $rental->discount_amount ?? 0
        );

        $netRentalAmount = max(
            0,
            $rentalAmount - $discountAmount
        );

        $depositAmount = (float) (
            $rental->deposit_amount ?? 0
        );

        $serviceFee = (float) (
            $rental->service_fee ?? 0
        );

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
            (string) env(
                'PROMPTPAY_PHONE',
                ''
            )
        );

        $promptPayQr = null;

        if (
            $promptPayPhone !== '' &&
            $paymentAmount > 0
        ) {
            try {
                $promptPayQr = PromptPay::qrCode(
                    $promptPayPhone,
                    (float) $paymentAmount
                )
                    ->toDataUri('svg')
                    ->getData();
            } catch (\Throwable $e) {
                $promptPayQr = null;
            }
        }

        return view(
            'rentals.payment',
            compact(
                'rental',
                'customer',
                'promptPayQr',
                'promptPayPhone',
                'paymentAmount'
            )
        );
    }

    /**
     * บันทึกที่อยู่จัดส่ง
     */
    public function updateAddress(
        Request $request,
        $id
    ) {
        $customer = $this->getCustomer();

        $rental = Rental::where(
            'customer_id',
            $customer->customer_id
        )->findOrFail($id);

        $data = $request->validate(
            [
                'delivery_address' => [
                    'required',
                    'string',
                    'max:1000',
                ],
                'recipient_phone' => [
                    'required',
                    'string',
                    'max:20',
                ],
            ],
            [
                'delivery_address.required' =>
                    'กรุณากรอกที่อยู่สำหรับจัดส่ง',

                'delivery_address.max' =>
                    'ที่อยู่จัดส่งต้องไม่เกิน 1,000 ตัวอักษร',

                'recipient_phone.required' =>
                    'กรุณาระบุเบอร์โทรศัพท์',

                'recipient_phone.max' =>
                    'เบอร์โทรต้องไม่เกิน 20 ตัวอักษร',
            ]
        );

        $deliveryAddress = trim(
            (string) (
                $data['delivery_address'] ?? ''
            )
        );

        $recipientPhone = trim(
            (string) (
                $data['recipient_phone'] ?? ''
            )
        );

        // บันทึกเป็นข้อมูลหลักของลูกค้า
        $customer->address = $deliveryAddress;
        $customer->phone = $recipientPhone;
        $customer->save();

        // บันทึกลงรายการเช่าปัจจุบัน
        $rental->delivery_address = $deliveryAddress;
        $rental->recipient_phone = $recipientPhone;
        $rental->save();

        return back()->with(
            'success',
            'บันทึกที่อยู่และเบอร์โทรศัพท์เรียบร้อยแล้ว'
        );
    }

    /**
     * บันทึกการชำระเงิน
     */
    public function submitPayment(
        Request $request,
        $id
    ) {
        $customer = $this->getCustomer();

        $rental = Rental::where(
            'customer_id',
            $customer->customer_id
        )->findOrFail($id);

        if (
            $rental->status !==
            'pending_payment'
        ) {
            return back()->with(
                'error',
                'รายการนี้ไม่อยู่ในสถานะที่สามารถชำระเงินได้'
            );
        }

        $request->validate(
            [
                'delivery_method' =>
                    'required|in:delivery,pickup',

                'recipient_name' => [
                    'required',
                    'string',
                    'max:200',
                ],

                'recipient_phone' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'delivery_address' => [
                    'nullable',
                    'string',
                    'max:1000',
                    'required_if:delivery_method,delivery',
                ],

                'payment_method' =>
                    'required|in:transfer,qr',

                'slip_image' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,webp',
                    'max:5120',
                ],

                'note' =>
                    'nullable|string|max:500',
            ],
            [
                'delivery_method.required' =>
                    'กรุณาเลือกวิธีรับชุด',

                'delivery_method.in' =>
                    'วิธีรับชุดไม่ถูกต้อง',

                'recipient_name.required' =>
                    'กรุณาระบุชื่อผู้รับ',

                'recipient_name.max' =>
                    'ชื่อผู้รับต้องไม่เกิน 200 ตัวอักษร',

                'recipient_phone.required' =>
                    'กรุณาระบุเบอร์โทรผู้รับ',

                'recipient_phone.max' =>
                    'เบอร์โทรต้องไม่เกิน 20 ตัวอักษร',

                'delivery_address.required_if' =>
                    'กรุณาระบุที่อยู่สำหรับจัดส่ง',

                'delivery_address.max' =>
                    'ที่อยู่จัดส่งต้องไม่เกิน 1,000 ตัวอักษร',

                'payment_method.required' =>
                    'กรุณาเลือกวิธีการชำระเงิน',

                'slip_image.required' =>
                    'กรุณาแนบรูปภาพสลิปหลักฐานการโอนเงิน',

                'slip_image.image' =>
                    'ไฟล์ต้องเป็นรูปภาพเท่านั้น',

                'slip_image.max' =>
                    'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB',
            ]
        );

        // วิธีรับชุด
        $deliveryMethod =
            $request->delivery_method;

        // ชื่อผู้รับ
        $recipientName = trim(
            (string) (
                $request->recipient_name ?? ''
            )
        );

        // เบอร์โทรผู้รับ
        $recipientPhone = trim(
            (string) (
                $request->recipient_phone ?? ''
            )
        );

        // จัดส่ง
        if (
            $deliveryMethod === 'delivery'
        ) {
            $deliveryAddress = trim(
                (string) (
                    $request->delivery_address ?? ''
                )
            );

            // ถ้าไม่มีค่าใน Checkout ใช้ Profile เป็นตัวสำรอง
            if ($deliveryAddress === '') {
                $deliveryAddress = trim(
                    (string) (
                        $customer->address ?? ''
                    )
                );
            }

            // ถ้ายังไม่มีที่อยู่
            if ($deliveryAddress === '') {
                return back()
                    ->with(
                        'error',
                        'ไม่พบที่อยู่สำหรับจัดส่ง กรุณาระบุที่อยู่ก่อนดำเนินการต่อ'
                    )
                    ->withInput();
            }

            // บันทึกที่อยู่ล่าสุดกลับเป็นข้อมูลหลักของลูกค้า
            $customer->address =
                $deliveryAddress;

            $customer->phone =
                $recipientPhone;

            $customer->save();
        } else {
            // รับที่ร้าน
            $deliveryAddress = null;
        }

        // คำนวณยอดเงิน
        $rentalAmount =
            (float) (
                $rental->total_amount ?? 0
            );

        $discountAmount =
            (float) (
                $rental->discount_amount ?? 0
            );

        $depositAmount =
            (float) (
                $rental->deposit_amount ?? 0
            );

        $serviceFee =
            (float) (
                $rental->service_fee ?? 0
            );

        $netRentalAmount = max(
            0,
            $rentalAmount -
            $discountAmount
        );

        // ค่าส่ง = 0
        $shippingFee = 0;

        $paymentAmount = round(
            $netRentalAmount +
            $depositAmount +
            $serviceFee +
            $shippingFee,
            2
        );

        // โฟลเดอร์เก็บสลิป
        $uploadDir = public_path(
            'uploads/slips'
        );

        if (!is_dir($uploadDir)) {
            mkdir(
                $uploadDir,
                0755,
                true
            );
        }

        // อัปโหลดสลิป
        $file = $request->file(
            'slip_image'
        );

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

        // บันทึกข้อมูลผู้รับลง Rental
        $rental->delivery_method =
            $deliveryMethod;

        $rental->recipient_name =
            $recipientName;

        $rental->recipient_phone =
            $recipientPhone;

        $rental->delivery_address =
            $deliveryAddress;

        $rental->save();

        // สร้างข้อความรายละเอียดการชำระเงิน
        $paymentNote = trim(
            (string) (
                $request->note ?? ''
            )
        );

        if ($paymentNote === '') {
            $paymentNote =
                'ชำระเงินค่าเช่าชุด ' .
                (
                    $deliveryMethod === 'delivery'
                        ? 'จัดส่งฟรี'
                        : 'รับที่ร้าน'
                ) .
                ' (ค่าเช่า ฿' .
                number_format(
                    $netRentalAmount,
                    2
                ) .
                ' + มัดจำ ฿' .
                number_format(
                    $depositAmount,
                    2
                ) .
                ' + ค่าบริการ ฿' .
                number_format(
                    $serviceFee,
                    2
                ) .
                ' + ค่าส่ง ฿0.00)';
        }

        // บันทึก Payment
        Payment::create([
            'rental_id' =>
                $rental->rental_id,

            'amount' =>
                $paymentAmount,

            'payment_method' =>
                $request->payment_method,

            'qr_code' =>
                null,

            'slip_image' =>
                $slipPath,

            'status' =>
                'pending',

            'note' =>
                $paymentNote,

            'paid_at' =>
                null,
        ]);

        // ลูกค้าเพิ่งส่งสลิป
        // ต้องรอเจ้าของร้านตรวจสอบก่อน
        $rental->update([
            'status' =>
                'pending_verification',
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
        $customer =
            $this->getCustomer();

        $activeRentals =
            Rental::where(
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
        $customer =
            $this->getCustomer();

        $pastRentals =
            Rental::where(
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
        $customer =
            $this->getCustomer();

        $rental =
            Rental::where(
                'customer_id',
                $customer->customer_id
            )
                ->with([
                    'details.product.mainImage',
                    'payments',
                ])
                ->findOrFail($id);

        // รองรับทั้งฟิลด์ใหม่และฟิลด์เดิม
        $returnTrackingNumber = trim(
            (string) (
                $rental->return_tracking_number
                ?: $rental->return_tracking_no
                ?: ''
            )
        );

        // สร้าง URL สำหรับติดตามพัสดุส่งคืน
        $returnTrackingUrl =
            $this->buildReturnTrackingUrl(
                $rental->return_shipping_carrier,
                $returnTrackingNumber
            );

        return view(
            'rentals.show',
            compact(
                'rental',
                'returnTrackingUrl'
            )
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
     *
     * Flow:
     *
     * not_returned
     *      ↓
     * returned_requested
     *      ↓
     * returned
     *
     * วิธีคืน:
     * parcel = ส่งพัสดุ
     * store  = คืนที่ร้าน
     */
    public function requestReturn(
        Request $request,
        $id
    ) {
        $customer =
            $this->getCustomer();

        $data = $request->validate(
            [
                'return_method' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'return_tracking_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'return_shipping_carrier' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'return_tracking_no' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
            ],
            [
                'return_method.required' =>
                    'กรุณาเลือกวิธีการคืนชุด',

                'return_tracking_number.max' =>
                    'เลขพัสดุต้องไม่เกิน 100 ตัวอักษร',

                'return_shipping_carrier.max' =>
                    'ชื่อบริษัทขนส่งต้องไม่เกิน 100 ตัวอักษร',

                'return_tracking_no.max' =>
                    'เลขพัสดุต้องไม่เกิน 100 ตัวอักษร',
            ]
        );

        /*
         * ใช้ transaction เพื่อให้ lockForUpdate() ทำงานจริง
         */
        try {
            $result = DB::transaction(
                function () use (
                    $customer,
                    $id,
                    $data
                ) {
                    $rental =
                        Rental::where(
                            'customer_id',
                            $customer->customer_id
                        )
                            ->lockForUpdate()
                            ->findOrFail($id);

                    /*
                     * อนุญาตให้แจ้งคืนเมื่อรายการอยู่ในช่วงเช่า
                     */
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
                        throw new \RuntimeException(
                            'รายการนี้ยังไม่พร้อมแจ้งคืนชุด'
                        );
                    }

                    /*
                     * ป้องกันการแจ้งคืนซ้ำ
                     */
                    if (
                        $rental->return_status ===
                        'returned_requested'
                    ) {
                        throw new \RuntimeException(
                            'รายการนี้แจ้งคืนชุดแล้ว กรุณารอร้านตรวจรับคืน'
                        );
                    }

                    if (
                        $rental->return_status ===
                        'returned'
                    ) {
                        throw new \RuntimeException(
                            'รายการนี้คืนชุดเรียบร้อยแล้ว'
                        );
                    }

                    /*
                     * แปลงวิธีคืนให้เป็นค่ามาตรฐาน
                     */
                    $rawReturnMethod = trim(
                        (string) (
                            $data['return_method'] ?? ''
                        )
                    );

                    $returnMethodLower =
                        mb_strtolower(
                            $rawReturnMethod
                        );

                    $returnMethod = null;

                    if (
                        in_array(
                            $returnMethodLower,
                            [
                                'parcel',
                                'shipping',
                                'delivery',
                                'ส่งพัสดุ',
                                'ส่งพัสดุคืน',
                            ],
                            true
                        )
                    ) {
                        $returnMethod = 'parcel';
                    }

                    if (
                        in_array(
                            $returnMethodLower,
                            [
                                'store',
                                'return_store',
                                'คืนที่ร้าน',
                                'คืนหน้าร้าน',
                                'หน้าร้าน',
                            ],
                            true
                        )
                    ) {
                        $returnMethod = 'store';
                    }

                    if (!$returnMethod) {
                        throw new \RuntimeException(
                            'วิธีการคืนชุดไม่ถูกต้อง กรุณาเลือก ส่งพัสดุ หรือ คืนที่ร้าน'
                        );
                    }

                    /*
                     * ดึงเลขพัสดุ
                     */
                    $returnTrackingNumber = trim(
                        (string) (
                            $data['return_tracking_number']
                            ?? $data['return_tracking_no']
                            ?? ''
                        )
                    );

                    /*
                     * ดึงบริษัทขนส่ง
                     */
                    $returnCarrier = trim(
                        (string) (
                            $data['return_shipping_carrier']
                            ?? ''
                        )
                    );

                    /*
                     * กรณีส่งพัสดุ
                     */
                    if (
                        $returnMethod === 'parcel'
                    ) {
                        if (
                            $returnTrackingNumber === ''
                        ) {
                            throw new \RuntimeException(
                                'กรุณากรอกเลขพัสดุสำหรับการส่งคืนชุด'
                            );
                        }

                        $returnShippingStatus =
                            'ส่งพัสดุแล้ว';

                        $returnShippedAt = now();
                    } else {
                        /*
                         * กรณีคืนที่ร้าน
                         */
                        $returnTrackingNumber = null;
                        $returnCarrier = null;

                        $returnShippingStatus =
                            'รอลูกค้านำชุดมาคืนที่ร้าน';

                        $returnShippedAt = null;
                    }

                    /*
                     * สร้างข้อความลง note
                     */
                    $returnNoteParts = [];

                    $returnNoteParts[] =
                        'วิธีคืน: ' .
                        (
                            $returnMethod === 'parcel'
                                ? 'ส่งพัสดุ'
                                : 'คืนที่ร้าน'
                        );

                    if (
                        $returnCarrier !== ''
                    ) {
                        $returnNoteParts[] =
                            'ขนส่ง: ' .
                            $returnCarrier;
                    }

                    if (
                        $returnTrackingNumber !== null &&
                        $returnTrackingNumber !== ''
                    ) {
                        $returnNoteParts[] =
                            'เลขพัสดุ: ' .
                            $returnTrackingNumber;
                    }

                    /*
                     * ที่อยู่ร้าน
                     * บันทึกไว้ใน note กรณีคืนที่ร้าน
                     */
                    if (
                        $returnMethod === 'store'
                    ) {
                        $returnNoteParts[] =
                            'สถานที่คืน: ร้าน KYRIX 77 ตำบลในเมือง อำเภอเมือง จังหวัดนครราชสีมา 30000';

                        $returnNoteParts[] =
                            'โทรร้าน: 0652599072';
                    }

                    $returnNote =
                        implode(
                            ' | ',
                            $returnNoteParts
                        );

                    $note =
                        $rental->note;

                    $returnNoteFull =
                        'แจ้งคืนจากลูกค้าเมื่อ ' .
                        now()->format('d/m/Y H:i') .
                        "\n" .
                        $returnNote;

                    $note =
                        $note
                            ? $note .
                                "\n" .
                                $returnNoteFull
                            : $returnNoteFull;

                    /*
                     * บันทึกข้อมูลคืนชุด
                     */
                    $updateData = [
                        // ระบบหลัก
                        'return_method' =>
                            $returnMethod,

                        'return_tracking_number' =>
                            $returnTrackingNumber,

                        'return_status' =>
                            'returned_requested',

                        'return_requested_at' =>
                            now(),

                        'return_received_at' =>
                            null,

                        // สถานะรายการเช่า
                        'status' =>
                            'pending_return',

                        // ระบบคืนชุดเดิม
                        'return_shipping_carrier' =>
                            $returnCarrier !== ''
                                ? $returnCarrier
                                : null,

                        'return_tracking_no' =>
                            $returnTrackingNumber,

                        'return_shipping_status' =>
                            $returnShippingStatus,

                        'return_shipped_at' =>
                            $returnShippedAt,

                        'return_estimated_delivery_at' =>
                            null,

                        // หมายเหตุ
                        'note' =>
                            $note,
                    ];

                    $rental->update(
                        $updateData
                    );

                    return [
                        'return_method' =>
                            $returnMethod,
                    ];
                }
            );

            /*
             * ข้อความแจ้งผล
             */
            if (
                $result['return_method'] === 'parcel'
            ) {
                return back()->with(
                    'success',
                    'แจ้งส่งคืนชุดเรียบร้อยแล้ว กรุณารอร้านตรวจรับคืน'
                );
            }

            return back()->with(
                'success',
                'แจ้งคืนชุดที่ร้านเรียบร้อยแล้ว กรุณานำชุดมาคืนที่ร้าน'
            );
        } catch (\RuntimeException $e) {
            return back()
                ->with(
                    'error',
                    $e->getMessage()
                )
                ->withInput();
        } catch (\Throwable $e) {
            return back()
                ->with(
                    'error',
                    'ไม่สามารถแจ้งคืนชุดได้ กรุณาลองใหม่อีกครั้ง'
                )
                ->withInput();
        }
    }

    /**
     * สร้าง URL สำหรับติดตามพัสดุส่งคืน
     */
    private function buildReturnTrackingUrl(
        ?string $carrier,
        ?string $trackingNo
    ): ?string {
        $carrier = trim(
            mb_strtolower(
                (string) $carrier
            )
        );

        $trackingNo = trim(
            (string) $trackingNo
        );

        if (
            $carrier === '' ||
            $trackingNo === ''
        ) {
            return null;
        }

        /*
         * ไปรษณีย์ไทย
         *
         * ต้องเป็น URL ปกติ
         * ห้ามใส่ Markdown
         */
        if (
            str_contains(
                $carrier,
                'ไปรษณีย์ไทย'
            ) ||
            str_contains(
                $carrier,
                'thailand post'
            ) ||
            str_contains(
                $carrier,
                'thai post'
            )
        ) {
            return
                'https://track.thailandpost.co.th/?trackNumber=' .
                urlencode($trackingNo);
        }

        /*
         * J&T Express
         */
        if (
            str_contains(
                $carrier,
                'j&t'
            ) ||
            str_contains(
                $carrier,
                'j&t express'
            ) ||
            str_contains(
                $carrier,
                'jnt'
            )
        ) {
            return
                'https://www.jtexpress.co.th/service/track?waybillNo=' .
                urlencode($trackingNo);
        }

        /*
         * KEX / Kerry
         */
        if (
            str_contains(
                $carrier,
                'kex'
            ) ||
            str_contains(
                $carrier,
                'kerry'
            )
        ) {
            return
                'https://th.kex-express.com/en/track-parcel';
        }

        /*
         * Flash Express
         */
        if (
            str_contains(
                $carrier,
                'flash'
            )
        ) {
            return
                'https://www.flashexpress.co.th/fle/tracking';
        }

        return null;
    }

    /**
     * ลูกค้าขอยกเลิกรายการเช่า
     */
    public function cancel(
        Request $request,
        $id
    ) {
        $customer =
            $this->getCustomer();

        $data =
            $request->validate(
                [
                    'cancel_reason' =>
                        'required|string|max:500',
                ],
                [
                    'cancel_reason.required' =>
                        'กรุณาระบุเหตุผลในการยกเลิก',

                    'cancel_reason.max' =>
                        'เหตุผลในการยกเลิกต้องไม่เกิน 500 ตัวอักษร',
                ]
            );

        try {
            DB::transaction(
                function () use (
                    $customer,
                    $id,
                    $data
                ) {
                    $rental =
                        Rental::where(
                            'customer_id',
                            $customer->customer_id
                        )
                            ->with('details')
                            ->lockForUpdate()
                            ->findOrFail($id);

                    if (
                        $rental->status !==
                        'pending_payment'
                    ) {
                        throw new \RuntimeException(
                            'รายการนี้ไม่สามารถยกเลิกได้ เนื่องจากรายการได้เข้าสู่ขั้นตอนชำระเงินหรือขั้นตอนถัดไปแล้ว'
                        );
                    }

                    foreach (
                        $rental->details
                        as $detail
                    ) {
                        $product =
                            Product::where(
                                'product_id',
                                $detail->product_id
                            )
                                ->lockForUpdate()
                                ->first();

                        if (!$product) {
                            continue;
                        }

                        $quantity =
                            max(
                                1,
                                (int) (
                                    $detail->quantity ??
                                    1
                                )
                            );

                        /*
                         * คืนสต็อก
                         */
                        $product->increment(
                            'stock',
                            $quantity
                        );

                        /*
                         * ลดจำนวนครั้งเช่า
                         */
                        $product->rental_count =
                            max(
                                0,
                                (int) (
                                    $product->rental_count ??
                                    0
                                ) -
                                $quantity
                            );

                        /*
                         * ถ้ามีสต็อกกลับมา
                         * ให้เปิดพร้อมเช่า
                         */
                        if (
                            (int) $product->stock > 0 &&
                            in_array(
                                $product->status,
                                [
                                    'rented',
                                    'busy',
                                ],
                                true
                            )
                        ) {
                            $product->status =
                                'available';
                        }

                        $product->save();
                    }

                    $cancelNote =
                        'ยกเลิกโดยลูกค้าเมื่อ ' .
                        now()->format('d/m/Y H:i') .
                        "\nเหตุผล: " .
                        trim(
                            $data['cancel_reason']
                        );

                    $note =
                        $rental->note;

                    $note =
                        $note
                            ? $note .
                                "\n" .
                                $cancelNote
                            : $cancelNote;

                    $rental->update([
                        'status' =>
                            'cancelled',

                        'note' =>
                            $note,
                    ]);
                }
            );

            return redirect()
                ->route(
                    'rentals.history'
                )
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
     * ทำความสะอาดขนาด
     */
    private function normalizeSelectedSize(
        ?string $size
    ): ?string {
        $size =
            trim(
                (string) $size
            );

        if (
            $size === ''
        ) {
            return null;
        }

        $size =
            preg_replace(
                '/\s+/u',
                ' ',
                $size
            );

        return mb_substr(
            $size,
            0,
            50
        );
    }

    /**
     * แปลงค่าที่ส่งเข้ามาให้เป็นชื่อสีที่เหมาะสม
     */
    private function resolveSelectedColor(
        ?string $selectedColor,
        Product $product
    ): ?string {
        $selectedColor =
            trim(
                (string) $selectedColor
            );

        if (
            $selectedColor === ''
        ) {
            return null;
        }

        $selectedColor =
            preg_replace(
                '/\s+/u',
                ' ',
                $selectedColor
            );

        $knownColors = [
            'แดงเบอร์กันดี',
            'สีแดงเบอร์กันดี',
            'ทองแชมเปญ',
            'สีทองแชมเปญ',
            'สีขาวงาช้าง',
            'ขาวงาช้าง',
            'สีชมพูนม',
            'ชมพูนม',
            'สีชมพู',
            'สีแดง',
            'สีส้ม',
            'สีเหลือง',
            'สีเขียว',
            'สีมิ้นท์',
            'สีฟ้า',
            'สีฟ้าอ่อน',
            'สีฟ้าเข้ม',
            'สีน้ำเงิน',
            'สีม่วง',
            'สีม่วงอ่อน',
            'สีชมพูอ่อน',
            'สีชมพูเข้ม',
            'สีขาว',
            'สีครีม',
            'สีเบจ',
            'สีขาวครีม',
            'สีน้ำตาล',
            'สีเทา',
            'สีเทาเข้ม',
            'สีดำ',
            'ดำชาร์โคล',
            'น้ำตาล',
            'ดำ',
            'แดง',
            'ชมพู',
            'ฟ้า',
            'เขียว',
            'ม่วง',
            'ขาว',
            'ครีม',
            'เบจ',
            'เทา',
        ];

        foreach (
            $knownColors as $color
        ) {
            if (
                mb_stripos(
                    $selectedColor,
                    $color
                ) !== false
            ) {
                return $color;
            }
        }

        // ตรวจจาก available_colors
        $availableColors =
            trim(
                (string) (
                    $product->available_colors ??
                    ''
                )
            );

        if (
            $availableColors !== ''
        ) {
            $colorList =
                array_filter(
                    array_map(
                        static fn($value) =>
                            trim(
                                (string) $value
                            ),
                        preg_split(
                            '/[,|\/]+/u',
                            $availableColors
                        )
                    )
                );

            foreach (
                $colorList as $color
            ) {
                if (
                    $color === ''
                ) {
                    continue;
                }

                if (
                    mb_strtolower($color) ===
                    mb_strtolower($selectedColor)
                ) {
                    return mb_substr(
                        $color,
                        0,
                        255
                    );
                }

                foreach (
                    $knownColors as $knownColor
                ) {
                    if (
                        mb_stripos(
                            $color,
                            $knownColor
                        ) !== false &&
                        mb_stripos(
                            $selectedColor,
                            $knownColor
                        ) !== false
                    ) {
                        return $knownColor;
                    }
                }
            }
        }

        // ตรวจจาก product.color
        $productColor =
            trim(
                (string) (
                    $product->color ??
                    ''
                )
            );

        if (
            $productColor !== ''
        ) {
            foreach (
                $knownColors as $knownColor
            ) {
                if (
                    mb_stripos(
                        $productColor,
                        $knownColor
                    ) !== false
                ) {
                    return $knownColor;
                }
            }

            if (
                mb_strtolower(
                    $productColor
                ) ===
                mb_strtolower(
                    $selectedColor
                )
            ) {
                return mb_substr(
                    $productColor,
                    0,
                    255
                );
            }
        }

        // ตรวจจากชื่อ/รายละเอียดสินค้า
        $textSources = [
            (string) (
                $product->product_name ??
                ''
            ),

            (string) (
                $product->description ??
                ''
            ),

            (string) (
                $product->available_colors ??
                ''
            ),

            (string) (
                $product->color ??
                ''
            ),
        ];

        foreach (
            $textSources as $source
        ) {
            $source =
                trim($source);

            if (
                $source === ''
            ) {
                continue;
            }

            foreach (
                $knownColors as $knownColor
            ) {
                if (
                    mb_stripos(
                        $source,
                        $knownColor
                    ) !== false
                ) {
                    return $knownColor;
                }
            }
        }

        return mb_substr(
            $selectedColor,
            0,
            255
        );
    }

    /**
     * ดึงข้อมูลลูกค้าที่ Login อยู่
     */
    private function getCustomer(): Customer
    {
        $customerId =
            session(
                'customer_id'
            );

        if (
            !$customerId
        ) {
            abort(
                403,
                'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
            );
        }

        $customer =
            Customer::find(
                $customerId
            );

        if (
            !$customer
        ) {
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