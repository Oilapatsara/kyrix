<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $totals = $this->calculateTotals($cart);

        return view('cart.index', compact('cart', 'totals'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'service_type' => 'nullable|string',
            'action' => 'nullable|string', // 'add_to_cart' or 'rent_now'
        ]);

        $product = Product::with('mainImage')->findOrFail($request->product_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = max(1, $startDate->diffInDays($endDate) + 1);

        $serviceType = $request->input('service_type', 'none');
        $serviceFee = match ($serviceType) {
            'cleaning' => 150.00,
            'express_delivery' => 100.00,
            'premium_care' => 200.00,
            default => 0.00,
        };

        $itemKey = $product->product_id . '_' . ($request->size ?? 'standard') . '_' . ($request->color ?? 'standard');

        $cart = session()->get('cart', []);

        $cart[$itemKey] = [
            'item_key' => $itemKey,
            'product_id' => $product->product_id,
            'product_name' => $product->product_name,
            'product_code' => $product->product_code,
            'image_url' => $product->main_image_url,
            'size' => $request->size ?? $product->size ?? 'M',
            'color' => $request->color ?? $product->color ?? 'ตามแบบ',
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days' => $days,
            'daily_price' => (float)$product->rental_price,
            'subtotal' => (float)$product->rental_price * $days,
            'deposit' => 100.00,
            'service_type' => $serviceType,
            'service_fee' => $serviceFee,
        ];

        session()->put('cart', $cart);

        if ($request->input('action') === 'rent_now') {
            return redirect()->route('checkout.index')->with('success', 'พร้อมดำเนินการเช่าทันที');
        }

        return redirect()->route('cart.index')->with('success', 'เพิ่มชุดลงในตะกร้าเรียบร้อยแล้ว');
    }

    public function update(Request $request, $itemKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$itemKey])) {
            $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'size' => 'nullable|string',
                'color' => 'nullable|string',
                'service_type' => 'nullable|string',
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $days = max(1, $startDate->diffInDays($endDate) + 1);

            $serviceType = $request->input('service_type', $cart[$itemKey]['service_type']);
            $serviceFee = match ($serviceType) {
                'cleaning' => 150.00,
                'express_delivery' => 100.00,
                'premium_care' => 200.00,
                default => 0.00,
            };

            $cart[$itemKey]['start_date'] = $startDate->format('Y-m-d');
            $cart[$itemKey]['end_date'] = $endDate->format('Y-m-d');
            $cart[$itemKey]['days'] = $days;
            $cart[$itemKey]['subtotal'] = $cart[$itemKey]['daily_price'] * $days;
            $cart[$itemKey]['service_type'] = $serviceType;
            $cart[$itemKey]['service_fee'] = $serviceFee;

            if ($request->filled('size')) {
                $cart[$itemKey]['size'] = $request->size;
            }
            if ($request->filled('color')) {
                $cart[$itemKey]['color'] = $request->color;
            }

            session()->put('cart', $cart);

            return back()->with('success', 'อัปเดตรายละเอียดในตะกร้าแล้ว');
        }

        return back()->with('error', 'ไม่พบรายการในตะกร้า');
    }

    public function remove($itemKey)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'ลบชุดออกจากตะกร้าแล้ว');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'ล้างตะกร้าเรียบร้อยแล้ว');
    }

    private function calculateTotals(array $cart): array
    {
        $rentalTotal = 0;
        $depositTotal = 0;
        $serviceTotal = 0;

        foreach ($cart as $item) {
            $rentalTotal += $item['subtotal'];
            $depositTotal += $item['deposit'];
            $serviceTotal += $item['service_fee'];
        }

        $grandTotal = $rentalTotal + $depositTotal + $serviceTotal;

        return [
            'rental_total' => $rentalTotal,
            'deposit_total' => $depositTotal,
            'service_total' => $serviceTotal,
            'grand_total' => $grandTotal,
            'item_count' => count($cart),
        ];
    }
}
