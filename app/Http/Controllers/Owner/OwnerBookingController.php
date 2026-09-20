<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerBookingController extends Controller
{
    /**
     * แสดงรายการจอง/เช่าทั้งหมด
     * พร้อมค้นหาและกรองสถานะ
     */
    public function index(Request $request)
    {
        $query = Rental::with([
            'customer',
            'details.product.images',
            'latestPayment',
        ]);

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $status = $request->status;

            if ($status === 'pending') {
                $query->whereIn('status', [
                    'pending',
                    'pending_payment',
                    'pending_verification',
                ]);
            } else {
                $query->where('status', $status);
            }
        }

        // ค้นหาตามรหัสเช่า / ID / เบอร์โทร / ลูกค้า
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'rental_code',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'rental_id',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'recipient_phone',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas('customer', function ($cq) use ($search) {
                    $cq->where(
                        'first_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    );
                });
            });
        }

        $bookings = $query
            ->latest('rental_id')
            ->paginate(10)
            ->withQueryString();

        // จำนวนรายการแยกตามสถานะ
        $counts = [
            'all' => Rental::count(),

            'pending' => Rental::whereIn('status', [
                'pending',
                'pending_payment',
                'pending_verification',
            ])->count(),

            'confirmed' => Rental::where(
                'status',
                'confirmed'
            )->count(),

            'renting' => Rental::where(
                'status',
                'renting'
            )->count(),

            'returned' => Rental::whereIn('status', [
                'returned',
                'completed',
            ])->count(),

            'cancelled' => Rental::where(
                'status',
                'cancelled'
            )->count(),
        ];

        return view(
            'owner.bookings.index',
            compact(
                'bookings',
                'counts'
            )
        );
    }

    /**
     * แสดงรายละเอียดการจอง
     */
    public function show($id)
    {
        $rental = Rental::with([
            'customer',
            'details.product.images',
            'payments',
            'reviews',
        ])->findOrFail($id);

        return view(
            'owner.bookings.show',
            compact('rental')
        );
    }

    /**
     * อัปเดตสถานะ
     * ข้อมูลจัดส่งจากร้านไปลูกค้า
     * ข้อมูลส่งคืนจากลูกค้ามาร้าน
     */
    public function updateStatus(
        Request $request,
        $id
    ) {
        $data = $request->validate([
            // สถานะการเช่า
            'status' => [
                'required',
                'in:pending,pending_payment,pending_verification,confirmed,ready_pickup,renting,pending_return,returned,completed',
            ],

            // ข้อมูลพัสดุขาไป
            'tracking_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'shipping_carrier' => [
                'nullable',
                'string',
                'max:100',
            ],

            'shipping_status' => [
                'nullable',
                'string',
                'max:100',
            ],

            'shipped_at' => [
                'nullable',
                'date',
            ],

            'estimated_delivery_at' => [
                'nullable',
                'date',
            ],

            // กำหนดคืนชุด
            'return_due_at' => [
                'nullable',
                'date',
            ],

            // ข้อมูลพัสดุขากลับ
            'return_tracking_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'return_shipping_carrier' => [
                'nullable',
                'string',
                'max:100',
            ],

            'return_shipping_status' => [
                'nullable',
                'string',
                'max:100',
            ],

            'return_shipped_at' => [
                'nullable',
                'date',
            ],

            'return_estimated_delivery_at' => [
                'nullable',
                'date',
            ],

            // หมายเหตุ
            'note' => [
                'nullable',
                'string',
            ],
        ], [
            'status.required' =>
                'กรุณาเลือกสถานะการเช่า',

            'shipped_at.date' =>
                'วันที่และเวลาส่งพัสดุไม่ถูกต้อง',

            'estimated_delivery_at.date' =>
                'วันที่และเวลาคาดว่าจะถึงไม่ถูกต้อง',

            'return_due_at.date' =>
                'วันและเวลาที่ต้องคืนชุดไม่ถูกต้อง',

            'return_shipped_at.date' =>
                'วันที่และเวลาส่งคืนชุดไม่ถูกต้อง',

            'return_estimated_delivery_at.date' =>
                'วันที่และเวลาพัสดุส่งคืนคาดว่าจะถึงไม่ถูกต้อง',
        ]);

        try {
            $rental = DB::transaction(function () use (
                $data,
                $id
            ) {
                // Lock รายการเช่า
                $rental = Rental::with('details.product')
                    ->lockForUpdate()
                    ->findOrFail($id);

                $oldStatus = $rental->status;
                $newStatus = $data['status'];

                // ถ้าถูกยกเลิกแล้ว
                if ($oldStatus === 'cancelled') {
                    throw new \RuntimeException(
                        'รายการนี้ถูกยกเลิกโดยลูกค้าแล้ว ไม่สามารถเปลี่ยนสถานะหรือแก้ไขข้อมูลได้'
                    );
                }

                // อัปเดตข้อมูลรายการเช่า
                $rental->update([
                    'status' => $newStatus,

                    // พัสดุขาไป
                    'tracking_number' =>
                        array_key_exists(
                            'tracking_number',
                            $data
                        )
                            ? $data['tracking_number']
                            : $rental->tracking_number,

                    'shipping_carrier' =>
                        array_key_exists(
                            'shipping_carrier',
                            $data
                        )
                            ? $data['shipping_carrier']
                            : $rental->shipping_carrier,

                    'shipping_status' =>
                        array_key_exists(
                            'shipping_status',
                            $data
                        )
                            ? $data['shipping_status']
                            : $rental->shipping_status,

                    'shipped_at' =>
                        array_key_exists(
                            'shipped_at',
                            $data
                        )
                            ? $data['shipped_at']
                            : $rental->shipped_at,

                    'estimated_delivery_at' =>
                        array_key_exists(
                            'estimated_delivery_at',
                            $data
                        )
                            ? $data['estimated_delivery_at']
                            : $rental->estimated_delivery_at,

                    // กำหนดคืน
                    'return_due_at' =>
                        array_key_exists(
                            'return_due_at',
                            $data
                        )
                            ? $data['return_due_at']
                            : $rental->return_due_at,

                    // พัสดุขากลับ
                    'return_tracking_no' =>
                        array_key_exists(
                            'return_tracking_no',
                            $data
                        )
                            ? $data['return_tracking_no']
                            : $rental->return_tracking_no,

                    'return_shipping_carrier' =>
                        array_key_exists(
                            'return_shipping_carrier',
                            $data
                        )
                            ? $data['return_shipping_carrier']
                            : $rental->return_shipping_carrier,

                    'return_shipping_status' =>
                        array_key_exists(
                            'return_shipping_status',
                            $data
                        )
                            ? $data['return_shipping_status']
                            : $rental->return_shipping_status,

                    'return_shipped_at' =>
                        array_key_exists(
                            'return_shipped_at',
                            $data
                        )
                            ? $data['return_shipped_at']
                            : $rental->return_shipped_at,

                    'return_estimated_delivery_at' =>
                        array_key_exists(
                            'return_estimated_delivery_at',
                            $data
                        )
                            ? $data['return_estimated_delivery_at']
                            : $rental->return_estimated_delivery_at,

                    // หมายเหตุ
                    'note' =>
                        array_key_exists(
                            'note',
                            $data
                        )
                            ? $data['note']
                            : $rental->note,
                ]);

                // ตรวจว่ารายการเพิ่งเปลี่ยนเป็นคืนแล้วหรือเสร็จสิ้น
                $isNowReturned = in_array(
                    $newStatus,
                    [
                        'returned',
                        'completed',
                    ],
                    true
                );

                $wasPreviouslyReturned = in_array(
                    $oldStatus,
                    [
                        'returned',
                        'completed',
                    ],
                    true
                );

                if (
                    $isNowReturned &&
                    !$wasPreviouslyReturned
                ) {
                    // บันทึกข้อมูลตรวจรับ
                    if (!$rental->inspected_at) {
                        $rental->update([
                            'inspected_at' => now(),

                            'condition_status' =>
                                $rental->condition_status
                                ?? 'good',

                            'deposit_status' =>
                                (
                                    $rental->deposit_status === 'pending' ||
                                    empty($rental->deposit_status)
                                )
                                    ? 'refunded'
                                    : $rental->deposit_status,

                            'deposit_refund_amount' =>
                                $rental->deposit_refund_amount
                                ?: (
                                    $rental->deposit_amount
                                    ?: 100
                                ),
                        ]);
                    }

                    // คืน Stock หลังคืนชุด
                    foreach ($rental->details as $detail) {
                        if (!$detail->product) {
                            continue;
                        }

                        $quantity = max(
                            1,
                            (int) (
                                $detail->quantity ?? 1
                            )
                        );

                        $product = $detail->product;

                        // เพิ่ม Stock
                        $product->increment(
                            'stock',
                            $quantity
                        );

                        $product->refresh();

                        // ถ้ามี Stock แล้ว
                        // เปลี่ยน rented / busy -> available
                        if (
                            $product->stock > 0 &&
                            in_array(
                                $product->status,
                                [
                                    'rented',
                                    'busy',
                                ],
                                true
                            )
                        ) {
                            $product->update([
                                'status' => 'available',
                            ]);
                        }
                    }
                }

                return $rental->fresh();
            });

            return redirect()
                ->back()
                ->with(
                    'success',
                    "อัปเดตข้อมูลรายการเช่า {$rental->formatted_code} เรียบร้อยแล้ว"
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
                    'ไม่สามารถอัปเดตข้อมูลรายการเช่าได้ กรุณาลองใหม่อีกครั้ง'
                );
        }
    }
}