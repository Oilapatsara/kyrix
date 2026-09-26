<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * แสดงหน้าตะกร้า
     */
    public function index()
    {
        $cart = $this->getCart();
        $oldCart = $cart;

        // ทำความสะอาดข้อมูลตะกร้า
        $cart = $this->normalizeCart($cart);

        // บันทึกข้อมูลที่ถูก normalize แล้ว
        if ($cart !== $oldCart) {
            $this->saveCart($cart);
        }

        $totals = $this->calculateTotals($cart);

        return view('cart.index', compact('cart', 'totals'));
    }

    /**
     * เพิ่มสินค้าเข้าตะกร้า
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'start_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'nullable',
                'date',
            ],
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:255',
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'service_type' => 'nullable|string|max:50',
            'action' => 'nullable|string|max:30',
        ]);

        $product = Product::with('mainImage')
            ->findOrFail($request->product_id);

        /**
         * ตรวจสต็อก
         */
        $stock = max(0, (int) ($product->stock ?? 0));

        if ($stock <= 0) {
            return back()->with(
                'error',
                'ชุดนี้หมดสต็อก ไม่สามารถเพิ่มลงตะกร้าได้'
            );
        }

        /**
         * จำนวนสินค้า
         */
        $quantity = max(
            1,
            (int) ($request->input('quantity', 1))
        );

        if ($quantity > $stock) {
            $quantity = $stock;
        }

        /**
         * กำหนดวันที่เริ่ม/คืน
         */
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::today();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::today()->addDays(2);

        /**
         * ป้องกันวันที่ผิด
         */
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy()->addDays(2);
        }

        /**
         * จำนวนวัน
         */
        $days = max(
            1,
            $startDate->diffInDays($endDate) + 1
        );

        /**
         * Size / Color
         */
        $size = trim(
            (string) (
                $request->input('size')
                ?? $product->size
                ?? 'M'
            )
        );

        $color = trim(
            (string) (
                $request->input('color')
                ?? $product->color
                ?? 'ตามแบบ'
            )
        );

        /**
         * Service
         */
        $serviceType = $request->input(
            'service_type',
            'none'
        );

        $serviceFee = match ($serviceType) {
            'cleaning' => 150.00,
            'express_delivery' => 100.00,
            'premium_care' => 200.00,
            default => 0.00,
        };

        /**
         * ราคาเช่าต่อครั้ง
         */
        $rentalPrice = (float) (
            $product->rental_price ?? 0
        );

        /**
         * เงินมัดจำ
         */
        $depositPrice = (float) (
            $product->deposit ?? 100
        );

        /**
         * สร้าง Key
         */
        $itemKey = implode('_', [
            $product->product_id,
            md5($size),
            md5($color),
        ]);

        /**
         * ดึงตะกร้า
         */
        $cart = $this->getCart();

        /**
         * ถ้ามีรายการเดิมอยู่แล้ว
         */
        if (isset($cart[$itemKey])) {
            $oldQuantity = max(
                1,
                (int) (
                    $cart[$itemKey]['quantity']
                    ?? 1
                )
            );

            $newQuantity = min(
                $stock,
                $oldQuantity + $quantity
            );
        } else {
            $newQuantity = min(
                $stock,
                $quantity
            );
        }

        /**
         * บันทึกข้อมูลลงตะกร้า
         */
        $cart[$itemKey] = [
            'item_key' => $itemKey,
            'product_id' => $product->product_id,
            'product_name' => $product->product_name,
            'product_code' => $product->product_code,
            'image_url' => $product->main_image_url,
            'size' => $size,
            'color' => $color,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days' => $days,
            'quantity' => $newQuantity,
            'daily_price' => $rentalPrice,
            'subtotal' => $rentalPrice * $newQuantity,
            'deposit' => $depositPrice * $newQuantity,
            'service_type' => $serviceType,
            'service_fee' => $serviceFee,
        ];

        /**
         * บันทึกตะกร้า
         */
        $this->saveCart($cart);

        /**
         * เช่าทันที
         */
        if ($request->input('action') === 'rent_now') {
            return redirect()
                ->route('checkout.index')
                ->with(
                    'success',
                    'พร้อมดำเนินการเช่าทันที'
                );
        }

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'เพิ่มชุดลงในตะกร้าเรียบร้อยแล้ว'
            );
    }

    /**
     * อัปเดตข้อมูลในตะกร้า
     */
    public function update(
        Request $request,
        $itemKey
    ) {
        $cart = $this->getCart();

        if (!isset($cart[$itemKey])) {
            return back()->with(
                'error',
                'ไม่พบรายการในตะกร้า'
            );
        }

        $request->validate([
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:255',
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'service_type' => 'nullable|string|max:50',
        ]);

        $item = $cart[$itemKey];

        $product = Product::find(
            $item['product_id'] ?? null
        );

        if (!$product) {
            unset($cart[$itemKey]);
            $this->saveCart($cart);

            return back()->with(
                'error',
                'ไม่พบสินค้านี้ในระบบแล้ว'
            );
        }

        $stock = max(
            0,
            (int) ($product->stock ?? 0)
        );

        $quantity = max(
            1,
            (int) (
                $request->input(
                    'quantity',
                    $item['quantity'] ?? 1
                )
            )
        );

        if ($stock > 0) {
            $quantity = min(
                $quantity,
                $stock
            );
        }

        $startDate = Carbon::parse(
            $request->start_date
        );

        $endDate = Carbon::parse(
            $request->end_date
        );

        $days = max(
            1,
            $startDate->diffInDays($endDate) + 1
        );

        $serviceType = $request->input(
            'service_type',
            $item['service_type'] ?? 'none'
        );

        $serviceFee = match ($serviceType) {
            'cleaning' => 150.00,
            'express_delivery' => 100.00,
            'premium_care' => 200.00,
            default => 0.00,
        };

        $rentalPrice = (float) (
            $product->rental_price ?? 0
        );

        $depositPrice = (float) (
            $product->deposit ?? 100
        );

        $size = trim(
            (string) (
                $request->input(
                    'size',
                    $item['size'] ?? 'M'
                )
            )
        );

        $color = trim(
            (string) (
                $request->input(
                    'color',
                    $item['color'] ?? 'ตามแบบ'
                )
            )
        );

        /**
         * ถ้า Size / Color เปลี่ยน
         * ให้สร้าง Key ใหม่
         */
        $newItemKey = implode('_', [
            $product->product_id,
            md5($size),
            md5($color),
        ]);

        if ($newItemKey !== $itemKey) {
            unset($cart[$itemKey]);

            if (isset($cart[$newItemKey])) {
                $quantity += (int) (
                    $cart[$newItemKey]['quantity']
                    ?? 1
                );
            }

            $itemKey = $newItemKey;
        }

        if ($stock > 0) {
            $quantity = min(
                $quantity,
                $stock
            );
        }

        $cart[$itemKey] = [
            'item_key' => $itemKey,
            'product_id' => $product->product_id,
            'product_name' => $product->product_name,
            'product_code' => $product->product_code,
            'image_url' => $product->main_image_url,
            'size' => $size,
            'color' => $color,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days' => $days,
            'quantity' => $quantity,
            'daily_price' => $rentalPrice,
            'subtotal' => $rentalPrice * $quantity,
            'deposit' => $depositPrice * $quantity,
            'service_type' => $serviceType,
            'service_fee' => $serviceFee,
        ];

        $this->saveCart($cart);

        return back()->with(
            'success',
            'อัปเดตรายละเอียดในตะกร้าแล้ว'
        );
    }

    /**
     * ลบสินค้าออกจากตะกร้า
     */
    public function remove($itemKey)
    {
        $cart = $this->getCart();

        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            $this->saveCart($cart);
        }

        return back()->with(
            'success',
            'ลบชุดออกจากตะกร้าแล้ว'
        );
    }

    /**
     * ล้างตะกร้า
     */
    public function clear()
    {
        $this->saveCart([]);

        return back()->with(
            'success',
            'ล้างตะกร้าเรียบร้อยแล้ว'
        );
    }

    /**
     * ดึงตะกร้าของลูกค้า
     */
    private function getCart(): array
    {
        $customerId = session('customer_id');

        /**
         * ถ้าล็อกอินแล้ว
         * ให้ดึงตะกร้าจากฐานข้อมูล
         */
        if ($customerId) {
            $savedCart = DB::table('rental_carts')
                ->where('customer_id', $customerId)
                ->value('cart_data');

            if ($savedCart) {
                $cart = json_decode(
                    $savedCart,
                    true
                );

                return is_array($cart)
                    ? $cart
                    : [];
            }

            /**
             * ถ้ายังไม่มีตะกร้าในฐานข้อมูล
             * ตรวจ Session เดิมก่อน
             */
            $sessionCart = session()->get('cart', []);

            if (!empty($sessionCart)) {
                $this->saveCart($sessionCart);
                return $sessionCart;
            }

            return [];
        }

        /**
         * ยังไม่ได้ล็อกอิน
         * ใช้ Session ตามเดิม
         */
        return session()->get('cart', []);
    }

    /**
     * บันทึกตะกร้า
     */
    private function saveCart(array $cart): void
    {
        $customerId = session('customer_id');

        /**
         * ถ้าล็อกอินแล้ว
         * บันทึกลงฐานข้อมูล
         */
        if ($customerId) {
            DB::table('rental_carts')->updateOrInsert(
                [
                    'customer_id' => $customerId,
                ],
                [
                    'cart_data' => json_encode(
                        $cart,
                        JSON_UNESCAPED_UNICODE |
                        JSON_UNESCAPED_SLASHES
                    ),
                    'updated_at' => now(),
                ]
            );

            /**
             * เก็บ Session ไว้ด้วย
             * เผื่อใช้ใน request ปัจจุบัน
             */
            session()->put('cart', $cart);

            return;
        }

        /**
         * ถ้ายังไม่ได้ล็อกอิน
         * เก็บ Session ตามเดิม
         */
        session()->put('cart', $cart);
    }

    /**
     * ทำความสะอาดข้อมูลตะกร้าเก่า
     */
    private function normalizeCart(
        array $cart
    ): array {
        foreach ($cart as $key => &$item) {
            if (!isset($item['quantity'])) {
                $item['quantity'] = 1;
            }

            if (!isset($item['size'])) {
                $item['size'] = 'M';
            }

            if (!isset($item['color'])) {
                $item['color'] = 'ตามแบบ';
            }

            if (!isset($item['days'])) {
                $item['days'] = 1;
            }

            if (!isset($item['service_fee'])) {
                $item['service_fee'] = 0;
            }

            if (!isset($item['daily_price'])) {
                $item['daily_price'] = (float) (
                    $item['subtotal'] ?? 0
                );
            }

            if (!isset($item['subtotal'])) {
                $item['subtotal'] =
                    (float) $item['daily_price']
                    * (int) $item['quantity'];
            }

            if (!isset($item['deposit'])) {
                $item['deposit'] =
                    100 * (int) $item['quantity'];
            }
        }

        unset($item);

        return $cart;
    }

    /**
     * คำนวณยอดรวมตะกร้า
     */
    private function calculateTotals(
        array $cart
    ): array {
        $rentalTotal = 0;
        $depositTotal = 0;
        $serviceTotal = 0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $quantity = max(
                1,
                (int) (
                    $item['quantity'] ?? 1
                )
            );

            $itemCount += $quantity;

            $rentalTotal += (float) (
                $item['subtotal']
                ?? (
                    ($item['daily_price'] ?? 0)
                    * $quantity
                )
            );

            $depositTotal += (float) (
                $item['deposit']
                ?? (
                    100 * $quantity
                )
            );

            $serviceTotal += (float) (
                $item['service_fee'] ?? 0
            );
        }

        $promo = \App\Services\PromotionService::calculateDiscount(
            $itemCount,
            $rentalTotal
        );

        $netRentalTotal = (float) (
            $promo['net_rental_total']
            ?? $rentalTotal
        );

        $grandTotal =
            $netRentalTotal
            + $depositTotal
            + $serviceTotal;

        return [
            'rental_total' => $rentalTotal,
            'discount_amount' => (float) (
                $promo['discount_amount'] ?? 0
            ),
            'discount_percent' => (float) (
                $promo['discount_percent'] ?? 0
            ),
            'discount_reason' =>
                $promo['discount_reason'] ?? null,
            'is_discounted' =>
                (bool) (
                    $promo['is_applied'] ?? false
                ),
            'net_rental_total' =>
                $netRentalTotal,
            'deposit_total' =>
                $depositTotal,
            'service_total' =>
                $serviceTotal,
            'grand_total' =>
                $grandTotal,
            'item_count' =>
                $itemCount,
            'hint_message' =>
                $promo['hint_message'] ?? null,
        ];
    }
}