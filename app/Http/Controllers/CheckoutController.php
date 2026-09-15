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

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('warning', 'กรุณาเลือกชุดลงตะกร้าก่อนดำเนินการชำระเงิน');
        }

        $customer = $this->getCustomer();
        $user = $customer->user;

        $rentalTotal = 0;
        $depositTotal = 0;
        $serviceTotal = 0;

        foreach ($cart as $item) {
            $rentalTotal += $item['subtotal'];
            $depositTotal += $item['deposit'];
            $serviceTotal += $item['service_fee'];
        }

        $grandTotal = $rentalTotal + $depositTotal + $serviceTotal;

        return view('checkout.index', compact(
            'cart',
            'customer',
            'user',
            'rentalTotal',
            'depositTotal',
            'serviceTotal',
            'grandTotal'
        ));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'ไม่มีชุดในตะกร้า');
        }

        $request->validate([
            'delivery_method' => 'required|in:pickup,delivery',
            'recipient_phone' => 'required|string|max:30',
            'delivery_address' => 'required_if:delivery_method,delivery|nullable|string',
            'payment_method' => 'required|in:qr,transfer,credit_card,cash',
            'slip_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'note' => 'nullable|string|max:500',
        ], [
            'delivery_method.required' => 'กรุณาเลือกวิธีรับชุด',
            'recipient_phone.required' => 'กรุณากรอกเบอร์โทรศัพท์สำหรับติดต่อ',
            'delivery_address.required_if' => 'กรุณาระบุที่อยู่สำหรับจัดส่งชุด',
            'slip_image.image' => 'ไฟล์สลิปต้องเป็นไฟล์รูปภาพเท่านั้น',
            'slip_image.max' => 'ขนาดไฟล์รูปสลิปต้องไม่เกิน 5MB',
        ]);

        $customer = $this->getCustomer();

        // Update customer profile phone & address if changed
        if ($request->filled('recipient_phone') && empty($customer->phone)) {
            $customer->update(['phone' => $request->recipient_phone]);
        }
        if ($request->filled('delivery_address') && empty($customer->address)) {
            $customer->update(['address' => $request->delivery_address]);
        }

        $rentalTotal = 0;
        $depositTotal = 0;
        $serviceTotal = 0;
        $earliestStart = null;
        $latestEnd = null;
        $serviceTypes = [];

        foreach ($cart as $item) {
            $rentalTotal += $item['subtotal'];
            $depositTotal += $item['deposit'];
            $serviceTotal += $item['service_fee'];

            if (!$earliestStart || $item['start_date'] < $earliestStart) {
                $earliestStart = $item['start_date'];
            }
            if (!$latestEnd || $item['end_date'] > $latestEnd) {
                $latestEnd = $item['end_date'];
            }
            if ($item['service_type'] !== 'none') {
                $serviceTypes[] = $item['service_type'];
            }
        }

        $grandTotal = $rentalTotal + $depositTotal + $serviceTotal;

        // Generate unique rental code
        $count = Rental::count() + 1;
        $rentalCode = 'KR-' . date('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $hasSlip = $request->hasFile('slip_image');
            $initialStatus = $hasSlip ? 'pending_verification' : 'pending_payment';

            $rental = Rental::create([
                'rental_code' => $rentalCode,
                'customer_id' => $customer->customer_id,
                'rental_date' => now()->toDateString(),
                'start_date' => $earliestStart ?? now()->toDateString(),
                'end_date' => $latestEnd ?? now()->addDays(3)->toDateString(),
                'total_amount' => $rentalTotal,
                'deposit_amount' => $depositTotal,
                'service_type' => !empty($serviceTypes) ? implode(', ', array_unique($serviceTypes)) : null,
                'service_fee' => $serviceTotal,
                'delivery_method' => $request->delivery_method,
                'delivery_address' => $request->delivery_method === 'delivery' ? $request->delivery_address : 'รับที่หน้าร้าน KYRIX',
                'recipient_phone' => $request->recipient_phone,
                'status' => $initialStatus,
                'note' => $request->note,
            ]);

            foreach ($cart as $item) {
                $product = Product::where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock < 1 || $product->status !== 'available') {
                    throw new \RuntimeException("ชุด {$product->product_name} ไม่พร้อมให้เช่าในขณะนี้");
                }

                RentalDetail::create([
                    'rental_id' => $rental->rental_id,
                    'product_id' => $item['product_id'],
                    'quantity' => 1,
                    'selected_size' => $item['size'],
                    'selected_color' => $item['color'],
                    'rental_days' => $item['days'],
                    'price' => $item['daily_price'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => now(),
                ]);

                $product->decrement('stock');
                $product->increment('rental_count');

                if ($product->fresh()->stock <= 0) {
                    $product->update(['status' => 'rented']);
                }
            }

            // Save Payment & Slip if uploaded
            if ($hasSlip) {
                $uploadDir = public_path('uploads/slips');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $file = $request->file('slip_image');
                $filename = 'slip_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $slipPath = 'uploads/slips/' . $filename;

                Payment::create([
                    'rental_id' => $rental->rental_id,
                    'payment_amount' => $grandTotal,
                    'payment_date' => now(),
                    'payment_method' => $request->payment_method === 'credit_card' ? 'other' : $request->payment_method,
                    'slip_image' => $slipPath,
                    'status' => 'pending',
                    'note' => 'แนบสลิปโอนเงินผ่านระบบหน้าเว็บไซต์',
                ]);
            }

            DB::commit();

            // Clear Cart
            session()->forget('cart');

            return redirect()->route('rentals.show', $rental->rental_id)
                ->with('success', 'บันทึกการเช่าเรียบร้อยแล้ว รหัสการจอง: ' . $rental->rental_code);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage())->withInput();
        }
    }

    private function getCustomer(): Customer
    {
        $customerId = session('customer_id');

        if (!$customerId) {
            abort(403, 'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่');
        }

        $customer = Customer::find($customerId);

        if (!$customer) {
            session()->forget([
                'customer_logged_in',
                'customer_id',
                'customer_name',
                'customer_email',
            ]);

            abort(403, 'ไม่พบข้อมูลลูกค้า กรุณาเข้าสู่ระบบใหม่');
        }

        return $customer;
    }
}
