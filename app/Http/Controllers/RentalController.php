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
            'quantity' => 'required|integer|min:1|max:' . max(1, (int) $product->stock),
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ], [
            'start_date.required' => 'กรุณาระบุวันที่เริ่มเช่า',
            'start_date.after_or_equal' => 'วันที่เริ่มเช่าต้องไม่ย้อนหลัง',
            'end_date.required' => 'กรุณาระบุวันที่คืนชุด',
            'end_date.after_or_equal' => 'วันที่คืนชุดต้องไม่น้อยกว่าวันที่เริ่มเช่า',
            'quantity.required' => 'กรุณาระบุจำนวนชุด',
            'quantity.min' => 'จำนวนชุดต้องอย่างน้อย 1 ชุด',
            'quantity.max' => 'จำนวนชุดเกินสต็อกที่มี (' . $product->stock . ' ชุด)',
            'size.max' => 'ขนาดต้องไม่เกิน 50 ตัวอักษร',
            'color.max' => 'สีต้องไม่เกิน 255 ตัวอักษร',
        ]);
        $customer = $this->getCustomer();
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = max(1, $start->diffInDays($end) + 1);
        $quantity = (int) $request->quantity;
        $pricePerDay = (float) $product->rental_price;
        $subtotal = $pricePerDay * $days * $quantity;
        $depositTotal = (float) $product->deposit * $quantity;
        DB::beginTransaction();
        try {
            $product = Product::where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();
            if ($product->status !== 'available' || (int) $product->stock < $quantity) {
                throw new \RuntimeException('ชุดนี้ไม่อยู่ในสถานะว่างพร้อมเช่าหรือสต็อกไม่เพียงพอ');
            }
            $selectedSize = $this->normalizeSelectedSize($request->size);
            $selectedColor = $this->resolveSelectedColor($request->color, $product);
            $rentalCode = 'KR-' . date('Ym') . '-' . str_pad(
                Rental::count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
            $promo = \App\Services\PromotionService::calculateDiscount(
                $quantity,
                $subtotal
            );
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
            RentalDetail::create([
                'rental_id' => $rental->rental_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'price' => $pricePerDay,
                'subtotal' => $subtotal,
                'selected_size' => $selectedSize,
                'selected_color' => $selectedColor,
                'rental_days' => $days,
            ]);
            $product->decrement('stock', $quantity);
            $product->increment('rental_count');
            $product->refresh();
            if ((int) $product->stock <= 0) {
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
        if ((int) $rental->customer_id !== (int) $customer->customer_id) {
            abort(403, 'รายการเช่านี้ไม่ได้เป็นของบัญชีลูกค้าปัจจุบัน');
        }
        if ($rental->status === 'cancelled') {
            return redirect()
                ->route('rentals.history')
                ->with(
                    'error',
                    'รายการเช่านี้ถูกยกเลิกแล้ว ไม่สามารถชำระเงินได้'
                );
        }
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
        $promptPayPhone = preg_replace(
            '/\D/',
            '',
            (string) env('PROMPTPAY_PHONE')
        );
        $promptPayQr = null;
        if ($promptPayPhone !== '' && $paymentAmount > 0) {
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
        $uploadDir = public_path('uploads/slips');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $file = $request->file('slip_image');
        $filename = 'slip_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(
            $uploadDir,
            $filename
        );
        $slipPath = 'uploads/slips/' . $filename;
        Payment::create([
            'rental_id' => $rental->rental_id,
            'amount' => $paymentAmount,
            'paid_at' => now(),
            'payment_method' => $request->payment_method,
            'slip_image' => $slipPath,
            'status' => 'pending',
            'note' => $request->note ?? 'ชำระเงินค่าเช่าชุด (ค่าเช่า ฿' .
                number_format($netRentalAmount, 2) .
                ' + มัดจำ ฿' .
                number_format($depositAmount, 2) .
                ' + ค่าบริการ ฿' .
                number_format($serviceFee, 2) .
                ')',
        ]);
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
        if (!in_array(
            $rental->status,
            [
                'confirmed',
                'ready_pickup',
                'renting',
            ],
            true
        )) {
            return back()->with(
                'error',
                'รายการนี้ยังไม่พร้อมแจ้งคืนชุด'
            );
        }
        $data = $request->validate([
            'return_method' => 'required|string|max:100',
            'return_shipping_carrier' => 'nullable|string|max:100',
            'return_tracking_no' => 'nullable|string|max:100',
        ], [
            'return_method.required' => 'กรุณาเลือกวิธีการคืนชุด',
            'return_shipping_carrier.max' => 'ชื่อบริษัทขนส่งต้องไม่เกิน 100 ตัวอักษร',
            'return_tracking_no.max' => 'เลขพัสดุต้องไม่เกิน 100 ตัวอักษร',
        ]);
        $returnMethod = trim($data['return_method'] ?? '');
        $returnCarrier = !empty($data['return_shipping_carrier'])
            ? trim($data['return_shipping_carrier'])
            : null;
        $returnTrackingNo = !empty($data['return_tracking_no'])
            ? trim($data['return_tracking_no'])
            : null;
        if ($returnTrackingNo) {
            $returnShippingStatus = 'ส่งพัสดุแล้ว';
            $returnShippedAt = now();
        } else {
            $returnShippingStatus = 'ลูกค้ายังไม่ได้ส่งคืน';
            $returnShippedAt = null;
        }
        $returnNoteParts = [];
        if ($returnMethod !== '') {
            $returnNoteParts[] = $returnMethod;
        }
        if ($returnCarrier) {
            $returnNoteParts[] = 'ขนส่ง: ' . $returnCarrier;
        }
        if ($returnTrackingNo) {
            $returnNoteParts[] = 'เลขพัสดุส่งคืน: ' . $returnTrackingNo;
        }
        $returnNote = implode(
            ' | ',
            $returnNoteParts
        );
        $note = $rental->note;
        if ($returnNote !== '') {
            $note = $note
                ? $note . "\nแจ้งคืนจากลูกค้า: " . $returnNote
                : 'แจ้งคืนจากลูกค้า: ' . $returnNote;
        }
        $updateData = [
            'status' => 'pending_return',
            'note' => $note,
        ];
        if ($returnMethod === 'ส่งพัสดุ') {
            $updateData['return_shipping_carrier'] = $returnCarrier;
            $updateData['return_tracking_no'] = $returnTrackingNo;
            $updateData['return_shipping_status'] = $returnShippingStatus;
            $updateData['return_shipped_at'] = $returnShippedAt;
        } else {
            $updateData['return_shipping_carrier'] = null;
            $updateData['return_tracking_no'] = $returnTrackingNo;
            $updateData['return_shipping_status'] = $returnShippingStatus;
            $updateData['return_shipped_at'] = $returnShippedAt;
        }
        $rental->update($updateData);
        return back()->with(
            'success',
            'แจ้งส่งคืนชุดเรียบร้อยแล้ว รอเจ้าของร้านตรวจรับและจัดการเงินมัดจำ'
        );
    }

    /**
     * ลูกค้าขอยกเลิกรายการเช่า
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
                $rental = Rental::where(
                    'customer_id',
                    $customer->customer_id
                )
                    ->with('details')
                    ->lockForUpdate()
                    ->findOrFail($id);
                if ($rental->status !== 'pending_payment') {
                    throw new \RuntimeException(
                        'รายการนี้ไม่สามารถยกเลิกได้ เนื่องจากรายการได้เข้าสู่ขั้นตอนชำระเงินหรือขั้นตอนถัดไปแล้ว'
                    );
                }
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
                    $product->increment(
                        'stock',
                        $quantity
                    );
                    $product->rental_count = max(
                        0,
                        (int) ($product->rental_count ?? 0) - $quantity
                    );
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
                $cancelNote = 'ยกเลิกโดยลูกค้าเมื่อ ' .
                    now()->format('d/m/Y H:i') .
                    "\nเหตุผล: " .
                    trim($data['cancel_reason']);
                $note = $rental->note;
                $note = $note
                    ? $note . "\n" . $cancelNote
                    : $cancelNote;
                $rental->update([
                    'status' => 'cancelled',
                    'note' => $note,
                ]);
            });
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
     * ทำความสะอาดขนาด
     */
    private function normalizeSelectedSize(?string $size): ?string
    {
        $size = trim((string) $size);
        if ($size === '') {
            return null;
        }
        $size = preg_replace('/\s+/u', ' ', $size);
        return mb_substr($size, 0, 50);
    }

    /**
     * แปลงค่าที่ส่งเข้ามาให้เป็นชื่อสีที่เหมาะสม
     * รองรับกรณีข้อมูลสีในสินค้าเก็บผิดเป็นข้อความรายละเอียด
     */
    private function resolveSelectedColor(
        ?string $selectedColor,
        Product $product
    ): ?string {
        $selectedColor = trim((string) $selectedColor);
        if ($selectedColor === '') {
            return null;
        }
        $selectedColor = preg_replace('/\s+/u', ' ', $selectedColor);

        /*
         * รายการสีที่ระบบรองรับ
         * เรียงจากชื่อที่เฉพาะเจาะจงก่อนชื่อทั่วไป
         */
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

        /*
         * 1. ตรวจค่าที่ผู้ใช้ส่งมาโดยตรงก่อน
         */
        foreach ($knownColors as $color) {
            if (mb_stripos($selectedColor, $color) !== false) {
                return $color;
            }
        }

        /*
         * 2. ตรวจจาก available_colors ของสินค้า
         */
        $availableColors = trim((string) ($product->available_colors ?? ''));
        if ($availableColors !== '') {
            $colorList = array_filter(
                array_map(
                    static fn($value) => trim((string) $value),
                    preg_split('/[,|\/]+/u', $availableColors)
                )
            );
            foreach ($colorList as $color) {
                if ($color === '') {
                    continue;
                }
                if (
                    mb_strtolower($color) ===
                    mb_strtolower($selectedColor)
                ) {
                    return mb_substr($color, 0, 255);
                }
                foreach ($knownColors as $knownColor) {
                    if (
                        mb_stripos($color, $knownColor) !== false &&
                        mb_stripos($selectedColor, $knownColor) !== false
                    ) {
                        return $knownColor;
                    }
                }
            }
        }

        /*
         * 3. ตรวจจาก color ของสินค้า
         */
        $productColor = trim((string) ($product->color ?? ''));
        if ($productColor !== '') {
            foreach ($knownColors as $knownColor) {
                if (mb_stripos($productColor, $knownColor) !== false) {
                    return $knownColor;
                }
            }
            if (
                mb_strtolower($productColor) ===
                mb_strtolower($selectedColor)
            ) {
                return mb_substr($productColor, 0, 255);
            }
        }

        /*
         * 4. ตรวจจากชื่อสินค้าและรายละเอียดสินค้า
         * กรณีฐานข้อมูลมีข้อมูลสีผิด เช่น
         * "Detached Sleeves / Arm Warmers..."
         * แต่ในรายละเอียดสินค้าระบุ "สีน้ำเงิน"
         */
        $textSources = [
            (string) ($product->product_name ?? ''),
            (string) ($product->description ?? ''),
            (string) ($product->available_colors ?? ''),
            (string) ($product->color ?? ''),
        ];

        foreach ($textSources as $source) {
            $source = trim($source);
            if ($source === '') {
                continue;
            }
            foreach ($knownColors as $knownColor) {
                if (mb_stripos($source, $knownColor) !== false) {
                    return $knownColor;
                }
            }
        }

        /*
         * 5. ถ้าไม่พบสีที่รู้จัก
         * ป้องกันไม่ให้ข้อมูลเกินฐานข้อมูล
         */
        return mb_substr($selectedColor, 0, 255);
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