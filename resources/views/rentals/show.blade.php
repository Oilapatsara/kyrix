@extends('layouts.customer')

@section('title', 'รายละเอียดการเช่า ' . ($rental->rental_code ?? 'KR-'.$rental->rental_id) . ' | KYRIX')

@push('styles')
<style>
    .order-detail-wrap {
        max-width: 1100px;
        margin: 40px auto 80px;
        padding: 0 24px;
    }
    .top-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .invoice-box {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 36px;
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 20px;
    }

    /* 8-Stage Tracker Timeline */
    .timeline-wrap {
        margin: 24px 0 32px;
        padding: 20px;
        background: #faf8f5;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        overflow-x: auto;
    }
    .timeline-steps {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 760px;
        position: relative;
    }
    .timeline-line {
        position: absolute;
        top: 20px;
        left: 30px;
        right: 30px;
        height: 4px;
        background: #e2dcd5;
        z-index: 1;
    }
    .timeline-progress {
        position: absolute;
        top: 20px;
        left: 30px;
        height: 4px;
        background: var(--primary);
        z-index: 2;
    }
    .step-node {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 90px;
    }
    .step-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #e2dcd5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: #999;
        margin-bottom: 8px;
    }
    .step-node.completed .step-circle {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
    .step-node.active .step-circle {
        background: #fff;
        border-color: var(--primary);
        color: var(--primary);
        box-shadow: 0 0 0 5px rgba(122,31,43,0.15);
    }
    .step-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .step-node.active .step-label,
    .step-node.completed .step-label {
        color: var(--text-main);
        font-weight: 700;
    }

    /* Items Table */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin: 24px 0;
    }
    .items-table th {
        background: #faf8f5;
        padding: 12px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
    }
    .items-table td {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
    }

    /* Summary Grid */
    .breakdown-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 30px;
        margin-top: 30px;
    }
    .info-card {
        background: #faf8f5;
        border-radius: var(--radius-md);
        padding: 20px;
        font-size: 13px;
        line-height: 1.8;
    }
    .info-card h4 {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .totals-box {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 20px;
    }
    .totals-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin-bottom: 8px;
        color: var(--text-muted);
    }
    .totals-row.final {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px solid var(--border);
        font-size: 20px;
        font-weight: 800;
        color: var(--primary);
    }

    .slip-img-card {
        max-width: 180px;
        border-radius: 8px;
        border: 1px solid var(--border);
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="order-detail-wrap">
    <div class="top-actions">
        <a href="{{ route('rentals.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> กลับไปหน้ารายการการเช่า
        </a>
        <div>
            <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-print"></i> พิมพ์ใบเสร็จ
            </button>
        </div>
    </div>

    <div class="invoice-box">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <span class="logo" style="font-size: 24px;">KYRIX</span>
                <div style="font-size: 12px; color: var(--gold); font-weight: 600; letter-spacing: 2px; margin-bottom: 12px;">DRESS RENTAL BOUTIQUE</div>
                <div style="font-size: 13px; color: var(--text-muted);">
                    88/9 สุขุมวิท 55 ทองหล่อ กทม. 10110 | โทร: 089-123-4567
                </div>
            </div>

            <div style="text-align: right;">
                <span style="font-size: 12px; color: var(--text-muted);">เลขที่การเช่า (Rental Code)</span>
                <h2 style="font-family: 'Plus Jakarta Sans', monospace; font-size: 24px; color: var(--primary); font-weight: 800; margin: 2px 0;">
                    {{ $rental->rental_code ?? 'KR-2026-'.$rental->rental_id }}
                </h2>
                <div style="margin-top: 6px;">
                    <span class="badge {{ $rental->status_badge_class }}" style="font-size: 13px; padding: 6px 14px;">
                        <i class="fa-solid fa-circle-dot"></i> {{ $rental->status_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 8-Stage Tracker Timeline -->
        @php
            $step = $rental->step_index;
            $progressPercent = match($step) {
                1 => 0,
                2 => 14,
                3 => 28,
                4 => 42,
                5 => 57,
                6 => 71,
                7 => 85,
                8 => 100,
                default => 0,
            };
            $stages = [
                1 => ['num' => '1', 'label' => 'รอชำระ', 'icon' => 'fa-credit-card'],
                2 => ['num' => '2', 'label' => 'รอตรวจสอบ', 'icon' => 'fa-receipt'],
                3 => ['num' => '3', 'label' => 'ยืนยันการเช่า', 'icon' => 'fa-check'],
                4 => ['num' => '4', 'label' => 'รอรับชุด', 'icon' => 'fa-box'],
                5 => ['num' => '5', 'label' => 'กำลังเช่า', 'icon' => 'fa-person-dress'],
                6 => ['num' => '6', 'label' => 'รอคืน', 'icon' => 'fa-arrow-rotate-left'],
                7 => ['num' => '7', 'label' => 'คืนแล้ว', 'icon' => 'fa-shield-heart'],
                8 => ['num' => '8', 'label' => 'เสร็จสิ้น', 'icon' => 'fa-circle-check'],
            ];
        @endphp
        <div class="timeline-wrap">
            <div class="timeline-steps">
                <div class="timeline-line"></div>
                <div class="timeline-progress" style="width: {{ $progressPercent }}%;"></div>
                @foreach($stages as $stageNum => $stage)
                    @php
                        $isCompleted = $step > $stageNum;
                        $isActive = $step === $stageNum;
                    @endphp
                    <div class="step-node {{ $isCompleted ? 'completed' : ($isActive ? 'active' : '') }}">
                        <div class="step-circle">
                            @if($isCompleted)
                                <i class="fa-solid fa-check"></i>
                            @else
                                <i class="fa-solid {{ $stage['icon'] }}"></i>
                            @endif
                        </div>
                        <div class="step-label">{{ $stage['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Dates Highlight -->
        <div style="background: #faf8f5; padding: 16px 20px; border-radius: 10px; display: flex; justify-content: space-around; text-align: center; border: 1px solid var(--border); margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">วันที่ทำรายการ</span>
                <strong>{{ $rental->rental_date ? date('d/m/Y', strtotime($rental->rental_date)) : $rental->created_at->format('d/m/Y') }}</strong>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">วันเริ่มรับชุด</span>
                <strong style="color: var(--primary);">{{ date('d/m/Y', strtotime($rental->start_date)) }}</strong>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">วันกำหนดส่งคืนชุด</span>
                <strong style="color: #b91c1c;">{{ date('d/m/Y', strtotime($rental->end_date)) }}</strong>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">วิธีรับชุด</span>
                <strong>{{ $rental->delivery_method === 'delivery' ? 'จัดส่งถึงที่อยู่' : 'รับที่หน้าร้าน KYRIX' }}</strong>
            </div>
        </div>

        <!-- Rented Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>ชุดที่เช่า</th>
                    <th>ขนาด / สี</th>
                    <th>ระยะเวลา</th>
                    <th>ค่าเช่า / วัน</th>
                    <th style="text-align: right;">ยอดรวม</th>
                    @if($rental->can_review)
                        <th style="text-align: center;">รีวิว</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($rental->details as $detail)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ $detail->product->main_image_url ?? '' }}" style="width: 50px; height: 60px; border-radius: 6px; object-fit: cover;" alt="dress">
                                <div>
                                    <a href="{{ route('products.show', $detail->product_id) }}" style="font-weight: 700; color: var(--text-main);">
                                        {{ $detail->product->product_name ?? 'ชุดเช่า' }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        รหัสชุด: {{ $detail->product->product_code ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>ไซซ์: <strong>{{ $detail->selected_size ?? 'M' }}</strong></div>
                            <div style="font-size: 12px; color: var(--text-muted);">สี: {{ $detail->selected_color ?? 'ตามแบบ' }}</div>
                        </td>
                        <td>{{ $detail->rental_days ?? 1 }} วัน</td>
                        <td>฿{{ number_format($detail->price) }}</td>
                        <td style="text-align: right; font-weight: 700; color: var(--primary);">
                            ฿{{ number_format($detail->subtotal) }}
                        </td>
                        @if($rental->can_review)
                            <td style="text-align: center;">
                                <a href="{{ route('reviews.create', ['rental' => $rental->rental_id, 'product' => $detail->product_id]) }}" class="btn btn-gold btn-sm">
                                    <i class="fa-solid fa-star"></i> เขียนรีวิว
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Details Grid -->
        <div class="breakdown-grid">
            <!-- Left Info -->
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div class="info-card">
                    <h4><i class="fa-solid fa-truck"></i> ข้อมูลการจัดส่ง / รับชุด</h4>
                    <div><strong>ผู้รับ:</strong> {{ $rental->customer->name ?? 'ลูกค้า' }}</div>
                    <div><strong>เบอร์โทรติดต่อ:</strong> {{ $rental->recipient_phone ?? $rental->customer->phone ?? '-' }}</div>
                    <div><strong>สถานที่รับ/จัดส่ง:</strong> {{ $rental->delivery_address }}</div>
                    @if($rental->tracking_number)
                        <div style="margin-top: 8px; color: var(--primary); font-weight: 700;">
                            <i class="fa-solid fa-barcode"></i> เลขพัสดุจัดส่ง: {{ $rental->tracking_number }}
                        </div>
                    @endif
                    @if($rental->return_tracking_no)
                        <div style="margin-top: 4px; color: #166534; font-weight: 700;">
                            <i class="fa-solid fa-box"></i> เลขพัสดุส่งคืนชุด: {{ $rental->return_tracking_no }}
                        </div>
                    @endif
                </div>

                <!-- Payments Info / Slip -->
                <div class="info-card">
                    <h4><i class="fa-solid fa-receipt"></i> ข้อมูลการชำระเงิน & สลิป</h4>
                    @if($rental->payments->count() > 0)
                        @foreach($rental->payments as $payment)
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed #ddd;">
                                <div>
                                    <span>ยอด: <strong>฿{{ number_format($payment->payment_amount) }}</strong></span>
                                    <span style="font-size: 11px; color: #888;">({{ $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : '-' }})</span>
                                    <div style="font-size: 11px;">
                                        สถานะ: 
                                        @if($payment->status === 'approved')
                                            <span style="color: #166534; font-weight: 700;">ตรวจสอบอนุมัติแล้ว</span>
                                        @else
                                            <span style="color: #b45309; font-weight: 700;">รอเจ้าหน้าที่ตรวจสลิป</span>
                                        @endif
                                    </div>
                                </div>
                                @if($payment->slip_url)
                                    <a href="{{ $payment->slip_url }}" target="_blank">
                                        <img src="{{ $payment->slip_url }}" class="slip-img-card" alt="สลิป">
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p style="color: var(--text-muted); margin: 0;">ยังไม่มีข้อมูลการชำระเงินหรือแนบสลิป</p>
                    @endif
                </div>
            </div>

            <!-- Right Totals -->
            <div class="totals-box">
                <h4 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                    แจกแจงค่าใช้จ่าย
                </h4>
                <div class="totals-row">
                    <span>ค่าเช่าชุดรวม:</span>
                    <strong>฿{{ number_format($rental->total_amount) }}</strong>
                </div>
                <div class="totals-row">
                    <span>เงินมัดจำประกันชุด:</span>
                    <strong style="color: #b45309;">฿{{ number_format($rental->deposit_amount) }}</strong>
                </div>
                <div class="totals-row">
                    <span>บริการเสริม ({{ $rental->service_type ?? 'มาตรฐาน' }}):</span>
                    <strong>฿{{ number_format($rental->service_fee) }}</strong>
                </div>
                <div class="totals-row final">
                    <span>ยอดรวมสุทธิ:</span>
                    <span>฿{{ number_format($rental->grand_total) }}</span>
                </div>

                <div style="background: var(--gold-light); padding: 12px; border-radius: 8px; font-size: 12.5px; color: #855d14; margin-top: 14px; line-height: 1.5; border: 1px solid #f3e5c8;">
                    <i class="fa-solid fa-shield"></i> เงินมัดจำประกันชุด <strong>฿{{ number_format($rental->deposit_amount) }}</strong> จะได้รับคืนทันทีในวันที่ส่งคืนชุด หากตรวจสภาพแล้วชุดไม่มีการเสียหาย (กรณีชุดมีความเสียหาย ทางร้านขอสงวนสิทธิ์ไม่คืนเงินมัดจำ)
                </div>
            </div>
        </div>

        <!-- Inspection Result & Deposit Status (When returned/completed) -->
        @if(in_array($rental->status, ['returned', 'completed']) || $rental->condition_status)
            <div style="margin-top: 30px; padding: 22px; border-radius: var(--radius-md); border: 2px solid {{ $rental->condition_status === 'damaged' ? '#fca5a5' : '#86efac' }}; background: {{ $rental->condition_status === 'damaged' ? '#fff5f5' : '#f0fdf4' }};">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="font-size: 28px; color: {{ $rental->condition_status === 'damaged' ? '#dc2626' : '#16a34a' }};">
                        @if($rental->condition_status === 'damaged')
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        @else
                            <i class="fa-solid fa-shield-halved"></i>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <h4 style="font-size: 16px; font-weight: 800; color: {{ $rental->condition_status === 'damaged' ? '#991b1b' : '#166534' }}; margin-bottom: 6px;">
                            @if($rental->condition_status === 'damaged')
                                ผลการตรวจสภาพชุด: พบความเสียหาย / ชำรุด (ไม่คืนเงินมัดจำ)
                            @else
                                ผลการตรวจสภาพชุด: สมบูรณ์ ไม่มีความเสียหาย (คืนเงินมัดจำแล้ว)
                            @endif
                        </h4>
                        <div style="font-size: 13.5px; color: #374151; margin-bottom: 10px; line-height: 1.6;">
                            @if($rental->condition_status === 'damaged')
                                ทางร้านได้ทำการตรวจสอบสภาพชุดแล้ว <strong>ตรวจพบความเสียหาย</strong> จึงขอสงวนสิทธิ์ไม่คืนเงินมัดจำประกันชุดจำนวน <strong>฿{{ number_format($rental->deposit_amount ?: 100, 2) }}</strong> ให้แก่ท่านตามเงื่อนไขของทางร้าน
                                @if($rental->damage_note)
                                    <div style="margin-top: 8px; padding: 10px 14px; background: #fee2e2; border-radius: 6px; color: #991b1b; font-size: 13px;">
                                        <strong>สาเหตุ/รายละเอียดความเสียหาย:</strong> {{ $rental->damage_note }}
                                    </div>
                                @endif
                            @else
                                ทางร้านได้ทำการตรวจสอบสภาพชุดแล้ว <strong>ชุดอยู่ในสภาพสมบูรณ์ ไม่พบความเสียหายใดๆ</strong> และได้ดำเนินการคืนเงินมัดจำประกันชุดจำนวน <strong>฿{{ number_format($rental->deposit_refund_amount ?: ($rental->deposit_amount ?: 100), 2) }}</strong> ให้แก่ท่านเรียบร้อยแล้ว
                            @endif
                        </div>

                        @if($rental->inspected_at)
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 10px;">
                                <i class="fa-regular fa-clock"></i> ตรวจรับและบันทึกข้อมูลเมื่อ: {{ $rental->inspected_at->format('d/m/Y H:i') }} น.
                            </div>
                        @endif

                        @if($rental->refund_slip || $rental->damage_image)
                            <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 10px;">
                                @if($rental->refund_slip)
                                    <div>
                                        <div style="font-size: 12px; font-weight: 700; color: #166534; margin-bottom: 4px;">
                                            <i class="fa-solid fa-file-invoice-dollar"></i> หลักฐานสลิปโอนคืนเงินมัดจำ:
                                        </div>
                                        <a href="{{ $rental->refund_slip_url }}" target="_blank">
                                            <img src="{{ $rental->refund_slip_url }}" style="max-width: 140px; max-height: 180px; border-radius: 8px; border: 1px solid #86efac; box-shadow: 0 2px 6px rgba(0,0,0,0.08);" alt="สลิปโอนคืนมัดจำ">
                                        </a>
                                    </div>
                                @endif
                                @if($rental->damage_image)
                                    <div>
                                        <div style="font-size: 12px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">
                                            <i class="fa-solid fa-camera"></i> ภาพถ่ายหลักฐานความเสียหาย:
                                        </div>
                                        <a href="{{ $rental->damage_image_url }}" target="_blank">
                                            <img src="{{ $rental->damage_image_url }}" style="max-width: 140px; max-height: 180px; border-radius: 8px; border: 1px solid #fca5a5; box-shadow: 0 2px 6px rgba(0,0,0,0.08);" alt="รูปหลักฐานความเสียหาย">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Action: Upload Slip Form (if pending_payment) -->
        @if($rental->status === 'pending_payment')
            <div id="slipSection" style="margin-top: 36px; padding: 26px; border: 2px dashed var(--primary); border-radius: var(--radius-md); background: var(--primary-soft);">
                <h3 style="font-size: 18px; font-weight: 800; color: var(--primary); margin-bottom: 8px;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> แนบสลิปโอนเงินสำหรับรายการนี้
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 18px;">
                    โอนเงินเข้าบัญชี กสิกรไทย 123-4-56789-0 (บจก. ไคริกซ์ เดรส เรนทอล) ยอดรวม ฿{{ number_format($rental->grand_total) }} แล้วแนบสลิปด้านล่าง
                </p>

                <form action="{{ route('rentals.upload-slip', $rental->rental_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_method" value="qr">
                    <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
                        <input type="file" name="slip_image" accept="image/*" required style="font-size: 14px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-upload"></i> อัปโหลดสลิปยืนยันการชำระ
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Action: Request Return Form (if renting or ready_pickup) -->
        @if(in_array($rental->status, ['renting', 'ready_pickup', 'confirmed']))
            <div id="returnSection" style="margin-top: 36px; padding: 26px; border: 1px solid var(--border); border-radius: var(--radius-md); background: #faf8f5;">
                <h3 style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
                    <i class="fa-solid fa-arrow-rotate-left" style="color: var(--primary);"></i> แจ้งส่งคืนชุด (Return Request)
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 18px;">
                    หากใช้งานชุดเสร็จเรียบร้อยแล้ว หรือจัดส่งพัสดุคืนแล้ว สามารถแจ้งข้อมูลการส่งคืนให้ทางร้านทราบได้ที่นี่
                </p>

                <form action="{{ route('rentals.request-return', $rental->rental_id) }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 14px; align-items: end;">
                        <div>
                            <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 4px;">วิธีการคืนชุด:</label>
                            <select name="return_method" class="input-field" style="margin-top: 0;" required>
                                <option value="นำมาคืนที่หน้าร้านทองหล่อ">นำมาคืนที่หน้าร้าน KYRIX (ทองหล่อ)</option>
                                <option value="ส่งพัสดุ (EMS / Flash / Kerry)">ส่งพัสดุไปรษณีย์ (EMS / Flash / Kerry)</option>
                                <option value="ส่งผ่านแมสเซนเจอร์ (Grab / Lineman)">ส่งผ่านแมสเซนเจอร์ (Grab / Lineman)</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 4px;">เลขพัสดุส่งคืน (ถ้ามี):</label>
                            <input type="text" name="return_tracking_no" class="input-field" style="margin-top: 0;" placeholder="เช่น TH123456789">
                        </div>
                        <button type="submit" class="btn btn-secondary" style="height: 44px;">
                            บันทึกการแจ้งส่งคืน
                        </button>
                    </div>
                </form>
            </div>
        @elseif($rental->status === 'pending_return')
            <div style="margin-top: 36px; padding: 22px; border: 1px solid #f3d28c; border-radius: var(--radius-md); background: #fff8e8; color: #8a5a0a;">
                <strong><i class="fa-solid fa-clock"></i> แจ้งส่งคืนแล้ว</strong>
                <div style="font-size: 13px; margin-top: 6px;">
                    ทางร้านได้รับคำขอส่งคืนชุดแล้ว กรุณารอเจ้าของร้านตรวจรับชุดและจัดการเงินมัดจำ
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
