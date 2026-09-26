<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerReturnController extends Controller
{
    /**
     * หน้าจัดการรับคืนชุด
     */
    public function index(Request $request)
    {
        $today = Carbon::today();

        $query = Rental::with([
            'customer',
            'details.product.images',
            'latestPayment',
        ]);

        $tab = $request->get('tab', 'active');

        /**
         * --------------------------------------------------------------------------
         * TAB
         * --------------------------------------------------------------------------
         */
        if ($tab === 'overdue') {
            $query
                ->whereIn('status', [
                    'renting',
                    'confirmed',
                    'pending_return',
                ])
                ->where('end_date', '<', $today);
        } elseif ($tab === 'returned') {
            $query->whereIn('status', [
                'returned',
                'completed',
            ]);
        } else {
            $query->whereIn('status', [
                'renting',
                'pending_return',
                'confirmed',
            ]);
        }

        /**
         * --------------------------------------------------------------------------
         * SEARCH
         * --------------------------------------------------------------------------
         */
        if ($request->filled('search')) {
            $search = trim(
                (string) $request->search
            );

            $query->where(function ($q) use ($search) {
                $q->where(
                    'rental_code',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'recipient_phone',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'customer',
                        function ($cq) use ($search) {
                            $cq
                                ->where(
                                    'first_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            });
        }

        /**
         * --------------------------------------------------------------------------
         * PAGINATION
         * --------------------------------------------------------------------------
         */
        $rentals = $query
            ->orderBy('end_date', 'asc')
            ->paginate(12)
            ->withQueryString();

        /**
         * --------------------------------------------------------------------------
         * COUNTS
         * --------------------------------------------------------------------------
         */
        $counts = [
            'active' => Rental::whereIn(
                'status',
                [
                    'renting',
                    'pending_return',
                    'confirmed',
                ]
            )->count(),

            'overdue' => Rental::whereIn(
                'status',
                [
                    'renting',
                    'confirmed',
                    'pending_return',
                ]
            )
                ->where(
                    'end_date',
                    '<',
                    $today
                )
                ->count(),

            'returned' => Rental::whereIn(
                'status',
                [
                    'returned',
                    'completed',
                ]
            )->count(),
        ];

        return view(
            'owner.returns.index',
            compact(
                'rentals',
                'counts',
                'tab'
            )
        );
    }

    /**
     * อัปเดตสถานะพัสดุส่งคืน
     */
    public function updateReturnShippingStatus(
        Request $request,
        $id
    ) {
        $rental = Rental::findOrFail($id);

        /**
         * --------------------------------------------------------------------------
         * ป้องกันการแก้ไขรายการที่ตรวจรับเสร็จแล้ว
         * --------------------------------------------------------------------------
         */
        if (
            in_array(
                $rental->status,
                [
                    'returned',
                    'completed',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'รายการนี้ตรวจรับคืนชุดเสร็จแล้ว ไม่สามารถเปลี่ยนสถานะพัสดุได้'
            );
        }

        /**
         * --------------------------------------------------------------------------
         * Validate
         * --------------------------------------------------------------------------
         */
        $request->validate(
            [
                'return_shipping_status' =>
                    'required|in:ลูกค้าส่งคืนแล้ว,ส่งพัสดุแล้ว,กำลังขนส่ง,กำลังนำจ่าย,ถึงร้านแล้ว',
            ],
            [
                'return_shipping_status.required' =>
                    'กรุณาเลือกสถานะพัสดุส่งคืน',
            ]
        );

        /**
         * --------------------------------------------------------------------------
         * ต้องมีเลขพัสดุสำหรับการอัปเดตสถานะขนส่ง
         * --------------------------------------------------------------------------
         */
        $trackingNumber = trim(
            (string) (
                $rental->return_tracking_number
                ?: $rental->return_tracking_no
                ?: ''
            )
        );

        $carrier = trim(
            (string) (
                $rental->return_shipping_carrier
                ?: ''
            )
        );

        if (
            $trackingNumber === '' ||
            $carrier === ''
        ) {
            return back()->with(
                'error',
                'รายการนี้ยังไม่มีข้อมูลบริษัทขนส่งหรือเลขพัสดุส่งคืน'
            );
        }

        $shippingStatus =
            $request->return_shipping_status;

        $updateData = [
            /**
             * ฟิลด์ระบบเดิม
             */
            'return_shipping_status' =>
                $shippingStatus,
        ];

        /**
         * --------------------------------------------------------------------------
         * บันทึกวันที่ลูกค้าส่งพัสดุ
         * --------------------------------------------------------------------------
         */
        if (
            in_array(
                $shippingStatus,
                [
                    'ลูกค้าส่งคืนแล้ว',
                    'ส่งพัสดุแล้ว',
                ],
                true
            ) &&
            empty(
                $rental->return_shipped_at
            )
        ) {
            $updateData[
                'return_shipped_at'
            ] = now();
        }

        /**
         * --------------------------------------------------------------------------
         * ยังไม่เปลี่ยน return_status เป็น returned
         *
         * เพราะ "ถึงร้านแล้ว" หมายถึงของมาถึงร้าน
         * แต่ Owner ยังต้องตรวจสภาพชุดและกดรับคืน
         *
         * ดังนั้นยังคงเป็น:
         *
         * returned_requested = แจ้งคืนแล้ว
         *
         * จนกว่า confirmReturn() จะทำงาน
         * --------------------------------------------------------------------------
         */
        if (
            ($rental->return_status ?? 'not_returned') ===
            'not_returned'
        ) {
            $updateData[
                'return_status'
            ] = 'returned_requested';

            $updateData[
                'return_requested_at'
            ] = $rental->return_requested_at ?? now();
        }

        /**
         * --------------------------------------------------------------------------
         * อัปเดตข้อมูล
         * --------------------------------------------------------------------------
         */
        $rental->update(
            $updateData
        );

        return back()->with(
            'success',
            'อัปเดตสถานะพัสดุส่งคืนเป็น "' .
                $shippingStatus .
                '" เรียบร้อยแล้ว'
        );
    }

    /**
     * ตรวจรับคืนชุด
     *
     * ระบบใหม่:
     *
     * returned_requested
     *        ↓
     * returned
     *
     * พร้อมบันทึก:
     *
     * return_received_at = now()
     */
    public function confirmReturn(
        Request $request,
        $id
    ) {
        try {
            DB::transaction(function () use (
                $request,
                $id
            ) {
                /**
                 * ------------------------------------------------------------------
                 * Lock รายการเช่า
                 * ------------------------------------------------------------------
                 */
                $rental = Rental::with(
                    'details.product'
                )
                    ->lockForUpdate()
                    ->findOrFail(
                        $id
                    );

                /**
                 * ------------------------------------------------------------------
                 * ป้องกันตรวจรับซ้ำ
                 * ------------------------------------------------------------------
                 */
                if (
                    in_array(
                        $rental->status,
                        [
                            'returned',
                            'completed',
                        ],
                        true
                    ) ||
                    $rental->return_status === 'returned'
                ) {
                    throw new \RuntimeException(
                        'รายการนี้ถูกตรวจรับคืนแล้ว'
                    );
                }

                /**
                 * ------------------------------------------------------------------
                 * ตรวจว่ารายการถูกแจ้งคืนแล้วหรือยัง
                 * ------------------------------------------------------------------
                 */
                $returnStatus =
                    $rental->return_status ??
                    'not_returned';

                if (
                    !in_array(
                        $returnStatus,
                        [
                            'returned_requested',
                        ],
                        true
                    )
                ) {
                    throw new \RuntimeException(
                        'รายการนี้ยังไม่มีการแจ้งคืนชุดจากลูกค้า'
                    );
                }

                /**
                 * ------------------------------------------------------------------
                 * ตรวจวิธีคืน
                 *
                 * parcel = ส่งพัสดุ
                 * store  = คืนที่ร้าน
                 *
                 * รองรับรายการเก่าที่ไม่มี return_method
                 * โดยดูจากเลขพัสดุเดิม
                 * ------------------------------------------------------------------
                 */
                $returnMethod =
                    $rental->return_method;

                $trackingNumber = trim(
                    (string) (
                        $rental->return_tracking_number
                        ?: $rental->return_tracking_no
                        ?: ''
                    )
                );

                if (
                    !$returnMethod
                ) {
                    $returnMethod =
                        $trackingNumber !== ''
                            ? 'parcel'
                            : 'store';
                }

                /**
                 * ------------------------------------------------------------------
                 * กรณีส่งพัสดุ
                 *
                 * ต้องรอพัสดุถึงร้านก่อน
                 * ------------------------------------------------------------------
                 */
                if (
                    $returnMethod === 'parcel'
                ) {
                    $shippingStatus =
                        trim(
                            (string) (
                                $rental->return_shipping_status
                                ?? ''
                            )
                        );

                    if (
                        $trackingNumber === ''
                    ) {
                        throw new \RuntimeException(
                            'รายการส่งพัสดุนี้ยังไม่มีเลขพัสดุ'
                        );
                    }

                    if (
                        $shippingStatus !==
                        'ถึงร้านแล้ว'
                    ) {
                        throw new \RuntimeException(
                            'ยังไม่สามารถตรวจรับชุดได้ เนื่องจากพัสดุส่งคืนยังไม่ถึงร้าน'
                        );
                    }
                }

                /**
                 * ------------------------------------------------------------------
                 * Validate inspection
                 * ------------------------------------------------------------------
                 */
                $validated = $request->validate(
                    [
                        'condition_status' =>
                            'required|in:good,damaged',

                        'damage_note' =>
                            'nullable|string|max:1000',

                        'damage_image' =>
                            'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

                        'refund_slip' =>
                            'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

                        'return_note' =>
                            'nullable|string|max:500',
                    ],
                    [
                        'condition_status.required' =>
                            'กรุณาระบุผลการตรวจสภาพชุด (สมบูรณ์ หรือ มีความเสียหาย)',

                        'condition_status.in' =>
                            'ผลการตรวจสภาพชุดไม่ถูกต้อง',

                        'damage_image.image' =>
                            'รูปภาพหลักฐานความเสียหายต้องเป็นไฟล์รูปภาพเท่านั้น',

                        'damage_image.mimes' =>
                            'รูปภาพความเสียหายต้องเป็น jpeg, png, jpg หรือ webp',

                        'damage_image.max' =>
                            'ขนาดรูปภาพความเสียหายต้องไม่เกิน 5MB',

                        'refund_slip.image' =>
                            'สลิปหลักฐานการคืนเงินต้องเป็นไฟล์รูปภาพเท่านั้น',

                        'refund_slip.mimes' =>
                            'สลิปต้องเป็น jpeg, png, jpg หรือ webp',

                        'refund_slip.max' =>
                            'ขนาดรูปภาพสลิปต้องไม่เกิน 5MB',
                    ]
                );

                $condition =
                    $validated['condition_status'];

                /**
                 * ------------------------------------------------------------------
                 * กำหนดเงินมัดจำ
                 *
                 * คง logic เดิมของระบบไว้
                 * ------------------------------------------------------------------
                 */
                $depositAmount =
                    100.00;

                $damageImagePath =
                    null;

                $refundSlipPath =
                    null;

                /**
                 * ------------------------------------------------------------------
                 * Upload Directory
                 * ------------------------------------------------------------------
                 */
                $returnDir =
                    public_path(
                        'uploads/returns'
                    );

                if (
                    !is_dir(
                        $returnDir
                    )
                ) {
                    mkdir(
                        $returnDir,
                        0755,
                        true
                    );
                }

                /**
                 * ------------------------------------------------------------------
                 * DAMAGE IMAGE
                 * ------------------------------------------------------------------
                 */
                if (
                    $request->hasFile(
                        'damage_image'
                    )
                ) {
                    $file =
                        $request->file(
                            'damage_image'
                        );

                    $filename =
                        'damage_' .
                        time() .
                        '_' .
                        uniqid() .
                        '.' .
                        $file->getClientOriginalExtension();

                    $file->move(
                        $returnDir,
                        $filename
                    );

                    $damageImagePath =
                        'uploads/returns/' .
                        $filename;
                }

                /**
                 * ------------------------------------------------------------------
                 * REFUND SLIP
                 * ------------------------------------------------------------------
                 */
                if (
                    $request->hasFile(
                        'refund_slip'
                    )
                ) {
                    $file =
                        $request->file(
                            'refund_slip'
                        );

                    $filename =
                        'refund_' .
                        time() .
                        '_' .
                        uniqid() .
                        '.' .
                        $file->getClientOriginalExtension();

                    $file->move(
                        $returnDir,
                        $filename
                    );

                    $refundSlipPath =
                        'uploads/returns/' .
                        $filename;
                }

                /**
                 * ------------------------------------------------------------------
                 * ตรวจสภาพชุด + เงินมัดจำ
                 * ------------------------------------------------------------------
                 */
                if (
                    $condition === 'good'
                ) {
                    $depositStatus =
                        'refunded';

                    $refundAmount =
                        $depositAmount;

                    $note =
                        'รับคืนชุดเรียบร้อย: ' .
                        'สภาพชุดสมบูรณ์ ไม่พบความเสียหาย | ' .
                        'คืนเงินมัดจำเต็มจำนวน ฿' .
                        number_format(
                            $refundAmount,
                            2
                        );
                } else {
                    $depositStatus =
                        'forfeited';

                    $refundAmount =
                        0.00;

                    $reason =
                        $request->filled(
                            'damage_note'
                        )
                            ? ' (สาเหตุ: ' .
                            $request->damage_note .
                            ')'
                            : '';

                    $note =
                        'รับคืนชุดเรียบร้อย: ' .
                        'ตรวจพบชุดชำรุด/เสียหาย | ' .
                        'ยึดเงินมัดจำ ฿' .
                        number_format(
                            $depositAmount,
                            2
                        ) .
                        ' ไม่คืนเงินมัดจำ' .
                        $reason;
                }

                /**
                 * ------------------------------------------------------------------
                 * NOTE
                 * ------------------------------------------------------------------
                 */
                if (
                    $request->filled(
                        'return_note'
                    )
                ) {
                    $note .=
                        ' | บันทึกเพิ่มเติม: ' .
                        $request->return_note;
                }

                /**
                 * ------------------------------------------------------------------
                 * UPDATE RENTAL
                 *
                 * จุดสำคัญ:
                 *
                 * return_status = returned
                 * return_received_at = now()
                 *
                 * ------------------------------------------------------------------
                 */
                $updateData = [
                    'status' =>
                        'returned',

                    'return_status' =>
                        'returned',

                    'return_received_at' =>
                        now(),

                    'condition_status' =>
                        $condition,

                    'deposit_status' =>
                        $depositStatus,

                    'deposit_refund_amount' =>
                        $refundAmount,

                    'damage_note' =>
                        $condition === 'damaged'
                            ? $request->damage_note
                            : null,

                    'inspected_at' =>
                        now(),

                    'note' =>
                        $rental->note
                            ? (
                                $rental->note .
                                "\n" .
                                $note
                            )
                            : $note,
                ];

                /**
                 * ------------------------------------------------------------------
                 * ถ้าเป็นพัสดุคืนและตรวจรับแล้ว
                 * ------------------------------------------------------------------
                 */
                if (
                    $returnMethod === 'parcel'
                ) {
                    $updateData[
                        'return_shipping_status'
                    ] = 'ถึงร้านแล้ว';

                    /**
                     * ถ้ามีเลขพัสดุใหม่ ให้ sync ลงฟิลด์เก่าด้วย
                     */
                    if (
                        $trackingNumber !== ''
                    ) {
                        $updateData[
                            'return_tracking_number'
                        ] = $trackingNumber;

                        $updateData[
                            'return_tracking_no'
                        ] = $trackingNumber;
                    }
                }

                /**
                 * ------------------------------------------------------------------
                 * SAVE DAMAGE IMAGE
                 * ------------------------------------------------------------------
                 */
                if (
                    $damageImagePath
                ) {
                    $updateData[
                        'damage_image'
                    ] =
                        $damageImagePath;
                }

                /**
                 * ------------------------------------------------------------------
                 * SAVE REFUND SLIP
                 * ------------------------------------------------------------------
                 */
                if (
                    $refundSlipPath
                ) {
                    $updateData[
                        'refund_slip'
                    ] =
                        $refundSlipPath;
                }

                /**
                 * ------------------------------------------------------------------
                 * UPDATE
                 * ------------------------------------------------------------------
                 */
                $rental->update(
                    $updateData
                );

                /**
                 * ------------------------------------------------------------------
                 * RESTORE STOCK
                 * ------------------------------------------------------------------
                 */
                foreach (
                    $rental->details as $detail
                ) {
                    if (
                        !$detail->product
                    ) {
                        continue;
                    }

                    $quantity = max(
                        1,
                        (int) (
                            $detail->quantity ??
                            1
                        )
                    );

                    $product =
                        $detail->product;

                    $product->increment(
                        'stock',
                        $quantity
                    );

                    /**
                     * เปลี่ยนสถานะสินค้าเป็น available
                     * เมื่อมีสต็อกกลับมา
                     */
                    $product->refresh();

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
                        $product->update([
                            'status' =>
                                'available',
                        ]);
                    }
                }

                /**
                 * ------------------------------------------------------------------
                 * SUCCESS MESSAGE
                 * ------------------------------------------------------------------
                 */
                if (
                    $condition === 'good'
                ) {
                    $resultMsg =
                        'ตรวจรับคืนชุดเรียบร้อย: ' .
                        'ชุดสมบูรณ์ คืนเงินมัดจำ ฿' .
                        number_format(
                            $depositAmount,
                            2
                        ) .
                        ' ทันที';
                } else {
                    $resultMsg =
                        'ตรวจรับคืนชุดเรียบร้อย: ' .
                        'ชุดเสียหาย บันทึกยึดเงินมัดจำ ฿' .
                        number_format(
                            $depositAmount,
                            2
                        ) .
                        ' (ไม่คืนเงิน)';
                }

                $rentalCode =
                    $rental->formatted_code ??
                    $rental->rental_code ??
                    ('KR-' . $rental->rental_id);

                session()->flash(
                    'return_success_message',
                    $resultMsg .
                    ' สำหรับคำสั่ง #' .
                    $rentalCode
                );
            });

            return back()->with(
                'success',
                session('return_success_message')
                    ?: 'ตรวจรับคืนชุดเรียบร้อยแล้ว'
            );
        } catch (\RuntimeException $e) {
            return back()->with(
                'error',
                $e->getMessage()
            );
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                'ไม่สามารถตรวจรับคืนชุดได้ กรุณาลองใหม่อีกครั้ง'
            );
        }
    }
}