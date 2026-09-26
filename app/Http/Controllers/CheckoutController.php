<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalDetail;
use Farzai\PromptPay\PromptPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * แสดงหน้า Checkout
     *
     * รับ selected[] จากหน้าตะกร้า
     * เพื่อกำหนดว่าจะ checkout เฉพาะรายการไหน
     */
    public function index(Request $request)
    {
        /*
         * ตะกร้าทั้งหมด
         */
        $allCart = session()->get('cart', []);

        if (empty($allCart)) {
            return redirect()
                ->route('products.index')
                ->with(
                    'warning',
                    'กรุณาเลือกชุดลงตะกร้าก่อนดำเนินการชำระเงิน'
                );
        }

        /*
         * ---------------------------------------------------------
         * เลือกรายการที่จะ Checkout
         * ---------------------------------------------------------
         */
        if ($request->has('selected')) {

            $selectedKeys = $this->sanitizeSelectedKeys(
                $request->input('selected', []),
                $allCart
            );

            if (empty($selectedKeys)) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'warning',
                        'กรุณาเลือกชุดอย่างน้อย 1 รายการก่อนดำเนินการเช่า'
                    );
            }

            session()->put(
                'checkout_selected_keys',
                $selectedKeys
            );
        } else {

            $selectedKeys = session(
                'checkout_selected_keys',
                []
            );

            if (empty($selectedKeys)) {
                $selectedKeys = array_keys($allCart);
            }

            $selectedKeys = $this->sanitizeSelectedKeys(
                $selectedKeys,
                $allCart
            );

            if (empty($selectedKeys)) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'warning',
                        'ไม่พบรายการที่เลือก กรุณาเลือกชุดใหม่อีกครั้ง'
                    );
            }
        }

        /*
         * ---------------------------------------------------------
         * เอาเฉพาะรายการที่เลือก
         * ---------------------------------------------------------
         */
        $cart = $this->filterCartByKeys(
            $allCart,
            $selectedKeys
        );

        if (empty($cart)) {
            session()->forget('checkout_selected_keys');

            return redirect()
                ->route('cart.index')
                ->with(
                    'warning',
                    'ไม่พบรายการที่เลือก กรุณาเลือกชุดใหม่อีกครั้ง'
                );
        }

        /*
         * ---------------------------------------------------------
         * ข้อมูลลูกค้า
         * ---------------------------------------------------------
         */
        $customer = $this->getCustomer();
        $user = $customer->user;

        /*
         * ---------------------------------------------------------
         * คำนวณเฉพาะรายการที่เลือก
         * ---------------------------------------------------------
         */
        $rentalTotal = 0;
        $depositTotal = 0;

        /*
         * ระบบไม่มีบริการเสริม
         */
        $serviceTotal = 0;

        $itemCount = count($cart);

        foreach ($cart as $item) {

            $rentalTotal += (float) (
                $item['subtotal'] ?? 0
            );

            $depositTotal += (float) (
                $item['deposit'] ?? 0
            );
        }

        /*
         * ---------------------------------------------------------
         * โปรโมชั่น
         * ---------------------------------------------------------
         */
        $promo = \App\Services\PromotionService::calculateDiscount(
            $itemCount,
            $rentalTotal
        );

        $discountAmount = (float) (
            $promo['discount_amount'] ?? 0
        );

        $discountReason =
            $promo['discount_reason'] ?? null;

        $netRentalTotal = (float) (
            $promo['net_rental_total']
            ?? $rentalTotal
        );

        /*
         * ---------------------------------------------------------
         * ยอดสุทธิ
         * ---------------------------------------------------------
         */
        $grandTotal =
            $netRentalTotal +
            $depositTotal;

        /*
         * ---------------------------------------------------------
         * PromptPay QR
         * ใช้ PromptPay ของร้าน + ยอดเงินของออเดอร์
         * ---------------------------------------------------------
         */
        $promptPayPhone = preg_replace(
            '/\D/',
            '',
            (string) env('PROMPTPAY_PHONE', '')
        );

        $promptPayQr = null;

        if (
            $promptPayPhone !== ''
            && $grandTotal > 0
        ) {
            try {

                $promptPayQr = PromptPay::qrCode(
                    $promptPayPhone,
                    (float) $grandTotal
                )
                    ->toDataUri('svg')
                    ->getData();

            } catch (\Throwable $e) {

                $promptPayQr = null;
            }
        }

        /*
         * ---------------------------------------------------------
         * ส่งข้อมูลไปหน้า Checkout
         * ---------------------------------------------------------
         */
        return view(
            'checkout.index',
            compact(
                'cart',
                'customer',
                'user',
                'rentalTotal',
                'discountAmount',
                'discountReason',
                'netRentalTotal',
                'promo',
                'depositTotal',
                'serviceTotal',
                'grandTotal',
                'selectedKeys',
                'promptPayQr',
                'promptPayPhone'
            )
        );
    }

    /**
     * บันทึกการเช่า
     */
    public function process(Request $request)
    {
        /*
         * ---------------------------------------------------------
         * ตะกร้าทั้งหมด
         * ---------------------------------------------------------
         */
        $allCart = session()->get('cart', []);

        if (empty($allCart)) {
            return redirect()
                ->route('products.index')
                ->with(
                    'error',
                    'ไม่มีชุดในตะกร้า'
                );
        }

        /*
         * ---------------------------------------------------------
         * ดึงรายการที่ต้องการ Checkout
         * ---------------------------------------------------------
         */
        if ($request->has('selected')) {

            $selectedKeys = $this->sanitizeSelectedKeys(
                $request->input('selected', []),
                $allCart
            );

        } else {

            $selectedKeys = $this->sanitizeSelectedKeys(
                session('checkout_selected_keys', []),
                $allCart
            );
        }

        /*
         * ---------------------------------------------------------
         * ป้องกันการสั่งเช่าโดยไม่มีรายการเลือก
         * ---------------------------------------------------------
         */
        if (empty($selectedKeys)) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'warning',
                    'กรุณาเลือกชุดอย่างน้อย 1 รายการก่อนดำเนินการเช่า'
                );
        }

        /*
         * ---------------------------------------------------------
         * เอาเฉพาะรายการที่เลือก
         * ---------------------------------------------------------
         */
        $cart = $this->filterCartByKeys(
            $allCart,
            $selectedKeys
        );

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'warning',
                    'ไม่พบรายการที่เลือก กรุณาเลือกชุดใหม่อีกครั้ง'
                );
        }

        /*
         * ---------------------------------------------------------
         * ตรวจสอบข้อมูล Checkout
         * ---------------------------------------------------------
         */
        $request->validate(
            [
                'delivery_method' => 'required|in:pickup,delivery',

                'recipient_phone' => [
                    'required',
                    'string',
                    'max:30',
                ],

                'delivery_address' => [
                    'required_if:delivery_method,delivery',
                    'nullable',
                    'string',
                ],

                'payment_method' => [
                    'required',
                    'in:qr,transfer,credit_card,cash',
                ],

                'slip_image' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,png,jpg,webp',
                    'max:5120',
                ],

                'note' => [
                    'nullable',
                    'string',
                    'max:500',
                ],
            ],
            [
                'delivery_method.required' =>
                    'กรุณาเลือกวิธีรับชุด',

                'recipient_phone.required' =>
                    'กรุณากรอกเบอร์โทรศัพท์สำหรับติดต่อ',

                'delivery_address.required_if' =>
                    'กรุณาระบุที่อยู่สำหรับจัดส่งชุด',

                'slip_image.image' =>
                    'ไฟล์สลิปต้องเป็นไฟล์รูปภาพเท่านั้น',

                'slip_image.max' =>
                    'ขนาดไฟล์รูปสลิปต้องไม่เกิน 5MB',
            ]
        );

        /*
         * ---------------------------------------------------------
         * ลูกค้า
         * ---------------------------------------------------------
         */
        $customer = $this->getCustomer();

        /*
         * ---------------------------------------------------------
         * อัปเดตข้อมูลลูกค้า
         * ---------------------------------------------------------
         */
        if (
            $request->filled('recipient_phone')
            && empty($customer->phone)
        ) {
            $customer->update(
                [
                    'phone' => $request->recipient_phone,
                ]
            );
        }

        if (
            $request->filled('delivery_address')
            && empty($customer->address)
        ) {
            $customer->update(
                [
                    'address' => $request->delivery_address,
                ]
            );
        }

        /*
         * ---------------------------------------------------------
         * คำนวณยอดจากเฉพาะรายการที่เลือก
         * ---------------------------------------------------------
         */
        $rentalTotal = 0;
        $depositTotal = 0;

        /*
         * ระบบไม่มีบริการเสริม
         */
        $serviceTotal = 0;

        $earliestStart = null;
        $latestEnd = null;

        $itemCount = count($cart);

        foreach ($cart as $item) {

            $rentalTotal += (float) (
                $item['subtotal'] ?? 0
            );

            $depositTotal += (float) (
                $item['deposit'] ?? 0
            );

            /*
             * วันที่เริ่มเช่าเร็วที่สุด
             */
            if (
                !$earliestStart
                || (
                    !empty($item['start_date'])
                    && $item['start_date'] < $earliestStart
                )
            ) {
                $earliestStart =
                    $item['start_date'] ?? null;
            }

            /*
             * วันที่คืนล่าสุด
             */
            if (
                !$latestEnd
                || (
                    !empty($item['end_date'])
                    && $item['end_date'] > $latestEnd
                )
            ) {
                $latestEnd =
                    $item['end_date'] ?? null;
            }
        }

        /*
         * ---------------------------------------------------------
         * โปรโมชั่น
         * ---------------------------------------------------------
         */
        $promo = \App\Services\PromotionService::calculateDiscount(
            $itemCount,
            $rentalTotal
        );

        $discountAmount = (float) (
            $promo['discount_amount'] ?? 0
        );

        $discountReason =
            $promo['discount_reason'] ?? null;

        $netRentalTotal = (float) (
            $promo['net_rental_total']
            ?? $rentalTotal
        );

        /*
         * ---------------------------------------------------------
         * ยอดสุทธิ
         * ---------------------------------------------------------
         */
        $grandTotal =
            $netRentalTotal +
            $depositTotal;

        /*
         * ---------------------------------------------------------
         * สร้าง Dynamic PromptPay QR
         * ---------------------------------------------------------
         */
        $promptPayPhone = preg_replace(
            '/\D/',
            '',
            (string) env('PROMPTPAY_PHONE', '')
        );

        $promptPayQr = null;

        if (
            $request->payment_method === 'qr'
            && $promptPayPhone !== ''
            && $grandTotal > 0
        ) {
            try {

                $promptPayQr = PromptPay::qrCode(
                    $promptPayPhone,
                    (float) $grandTotal
                )
                    ->toDataUri('svg')
                    ->getData();

            } catch (\Throwable $e) {

                throw new \RuntimeException(
                    'ไม่สามารถสร้าง QR PromptPay ได้: ' .
                    $e->getMessage()
                );
            }
        }

        /*
         * ---------------------------------------------------------
         * สร้าง Rental Code
         * ---------------------------------------------------------
         */
        $count = Rental::count() + 1;

        $rentalCode =
            'KR-' .
            date('Ym') .
            '-' .
            str_pad(
                $count,
                4,
                '0',
                STR_PAD_LEFT
            );

        /*
         * ---------------------------------------------------------
         * Transaction
         * ---------------------------------------------------------
         */
        DB::beginTransaction();

        try {

            /*
             * มีสลิปหรือไม่
             */
            $hasSlip =
                $request->hasFile('slip_image');

            $initialStatus =
                $hasSlip
                    ? 'pending_verification'
                    : 'pending_payment';

            /*
             * -----------------------------------------------------
             * สร้าง Rental
             * -----------------------------------------------------
             */
            $rental = Rental::create(
                [
                    'rental_code' =>
                        $rentalCode,

                    'customer_id' =>
                        $customer->customer_id,

                    'rental_date' =>
                        now()->toDateString(),

                    'start_date' =>
                        $earliestStart
                        ?? now()->toDateString(),

                    'end_date' =>
                        $latestEnd
                        ?? now()->addDays(3)->toDateString(),

                    'total_amount' =>
                        $rentalTotal,

                    'discount_amount' =>
                        $discountAmount,

                    'discount_reason' =>
                        $discountReason,

                    'deposit_amount' =>
                        $depositTotal,

                    /*
                     * ไม่มีบริการเสริม
                     */
                    'service_type' =>
                        null,

                    'service_fee' =>
                        0,

                    'delivery_method' =>
                        $request->delivery_method,

                    'delivery_address' =>
                        $request->delivery_method === 'delivery'
                            ? $request->delivery_address
                            : 'รับที่หน้าร้าน KYRIX',

                    'recipient_phone' =>
                        $request->recipient_phone,

                    'status' =>
                        $initialStatus,

                    'note' =>
                        $request->note,
                ]
            );

            /*
             * -----------------------------------------------------
             * สร้าง RentalDetail
             * -----------------------------------------------------
             */
            foreach ($cart as $item) {

                $product = Product::where(
                    'product_id',
                    $item['product_id']
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * ตรวจสอบสต็อก
                 */
                if (
                    (int) $product->stock < 1
                    || $product->status !== 'available'
                ) {
                    throw new \RuntimeException(
                        "ชุด {$product->product_name} ไม่พร้อมให้เช่าในขณะนี้"
                    );
                }

                /*
                 * จำนวนเช่า
                 */
                $quantity = 1;

                /*
                 * จำนวนเงินของรายการ
                 */
                $itemSubtotal = (float) (
                    $item['subtotal'] ?? 0
                );

                /*
                 * สร้างรายละเอียดการเช่า
                 */
                RentalDetail::create(
                    [
                        'rental_id' =>
                            $rental->rental_id,

                        'product_id' =>
                            $item['product_id'],

                        'quantity' =>
                            $quantity,

                        'selected_size' =>
                            $item['size'] ?? null,

                        'selected_color' =>
                            $item['color'] ?? null,

                        'rental_days' =>
                            $item['days'] ?? 1,

                        'price' =>
                            $item['daily_price'] ?? 0,

                        'subtotal' =>
                            $itemSubtotal,

                        'created_at' =>
                            now(),
                    ]
                );

                /*
                 * ตัด Stock
                 */
                $product->decrement(
                    'stock',
                    $quantity
                );

                $product->increment(
                    'rental_count'
                );

                /*
                 * ถ้าสต็อกหมด
                 */
                $product->refresh();

                if (
                    (int) $product->stock <= 0
                ) {
                    $product->update(
                        [
                            'status' => 'rented',
                        ]
                    );
                }
            }

            /*
             * -----------------------------------------------------
             * เตรียม Slip
             * -----------------------------------------------------
             */
            $slipPath = null;

            if ($hasSlip) {

                $uploadDir =
                    public_path(
                        'uploads/slips'
                    );

                if (!is_dir($uploadDir)) {

                    mkdir(
                        $uploadDir,
                        0755,
                        true
                    );
                }

                $file =
                    $request->file(
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
            }

            /*
             * -----------------------------------------------------
             * สร้าง Payment
             *
             * จำนวนเงินใช้ amount
             * QR ใช้ qr_code
             * ไม่มี payment_amount / payment_date
             * -----------------------------------------------------
             */
            Payment::create(
                [
                    'rental_id' =>
                        $rental->rental_id,

                    'amount' =>
                        $grandTotal,

                    'payment_method' =>
                        $request->payment_method === 'credit_card'
                            ? 'other'
                            : $request->payment_method,

                    'qr_code' =>
                        $promptPayQr,

                    'slip_image' =>
                        $slipPath,

                    'status' =>
                        'pending',

                    'note' =>
                        'แนบสลิปโอนเงินผ่านระบบหน้าเว็บไซต์',
                ]
            );

            /*
             * -----------------------------------------------------
             * Commit
             * -----------------------------------------------------
             */
            DB::commit();

            /*
             * -----------------------------------------------------
             * เอาเฉพาะรายการที่ checkout สำเร็จออกจากตะกร้า
             * -----------------------------------------------------
             */
            $remainingCart = $allCart;

            foreach ($selectedKeys as $selectedKey) {

                unset(
                    $remainingCart[$selectedKey]
                );
            }

            /*
             * บันทึก Session
             */
            session()->put(
                'cart',
                $remainingCart
            );

            /*
             * ล้าง selected checkout
             */
            session()->forget(
                'checkout_selected_keys'
            );

            /*
             * -----------------------------------------------------
             * ถ้าล็อกอินอยู่
             * ให้บันทึกตะกร้าที่เหลือลง DB ด้วย
             * -----------------------------------------------------
             */
            $customerId =
                session('customer_id');

            if ($customerId) {

                DB::table('rental_carts')
                    ->updateOrInsert(
                        [
                            'customer_id' =>
                                $customerId,
                        ],
                        [
                            'cart_data' =>
                                json_encode(
                                    $remainingCart,
                                    JSON_UNESCAPED_UNICODE
                                    | JSON_UNESCAPED_SLASHES
                                ),

                            'updated_at' =>
                                now(),
                        ]
                    );
            }

            /*
             * -----------------------------------------------------
             * ไปหน้ารายละเอียดการเช่า
             * -----------------------------------------------------
             */
            return redirect()
                ->route(
                    'rentals.show',
                    $rental->rental_id
                )
                ->with(
                    'success',
                    'บันทึกการเช่าเรียบร้อยแล้ว รหัสการจอง: ' .
                    $rental->rental_code
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' .
                    $e->getMessage()
                )
                ->withInput();
        }
    }

    /**
     * กรอง Selected Keys ให้เหลือเฉพาะ key ที่มีอยู่จริงใน Cart
     */
    private function sanitizeSelectedKeys(
        $selectedKeys,
        array $cart
    ): array {
        if (!is_array($selectedKeys)) {
            $selectedKeys = [];
        }

        $selectedKeys = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'strval',
                        $selectedKeys
                    ),
                    function ($key) {
                        return $key !== '';
                    }
                )
            )
        );

        return array_values(
            array_filter(
                $selectedKeys,
                function ($key) use ($cart) {
                    return array_key_exists(
                        $key,
                        $cart
                    );
                }
            )
        );
    }

    /**
     * เอาเฉพาะรายการ Cart ที่เลือก
     */
    private function filterCartByKeys(
        array $cart,
        array $selectedKeys
    ): array {
        $selectedCart = [];

        foreach ($selectedKeys as $key) {

            if (array_key_exists($key, $cart)) {

                $selectedCart[$key] =
                    $cart[$key];
            }
        }

        return $selectedCart;
    }

    /**
     * ดึงข้อมูลลูกค้า
     */
    private function getCustomer(): Customer
    {
        $customerId =
            session('customer_id');

        if (!$customerId) {

            abort(
                403,
                'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
            );
        }

        $customer =
            Customer::with('user')
                ->find($customerId);

        if (!$customer) {

            session()->forget(
                [
                    'customer_logged_in',
                    'customer_id',
                    'customer_name',
                    'customer_email',
                    'checkout_selected_keys',
                ]
            );

            abort(
                403,
                'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่'
            );
        }

        return $customer;
    }
}