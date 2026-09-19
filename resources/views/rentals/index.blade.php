@extends('layouts.customer')

@section('title', 'การเช่าของฉัน (My Rentals) | KYRIX')

@push('styles')
    <style>
        .rentals-wrapper {
            max-width: 1100px;
            margin: 40px auto 80px;
            padding: 0 24px;
        }

        .rentals-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .rentals-header h1 {
            font-size: 26px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .booking-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 26px;
            margin-bottom: 24px;
            transition: all 0.2s;
        }

        .booking-card:hover {
            box-shadow: var(--shadow-md);
            border-color: rgba(122, 31, 43, 0.25);
        }

        .booking-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .info-table-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            background: #faf8f5;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            margin: 18px 0;
            text-align: center;
        }

        .info-col {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .info-val {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
        }

        .dress-row {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 12px 0;
        }

        .dress-thumb {
            width: 70px;
            height: 85px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .booking-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 12px;
        }

        @media (max-width: 768px) {
            .info-table-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <div class="rentals-wrapper">
        <div class="rentals-header">
            <div>
                <h1>
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i>
                    <span>การเช่าของฉัน (My Rentals)</span>
                </h1>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
                    ดูรายการจองเช่าชุด ตรวจสอบสถานะการเช่าและสถานะการชำระเงิน
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('rentals.history') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-box-archive"></i> ดูประวัติการเช่า
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> เช่าชุดเพิ่ม
                </a>
            </div>
        </div>

        @forelse($activeRentals as $rental)
            <div class="booking-card">
                <div class="booking-card-top">
                    <div>
                        <span style="font-size: 12px; color: var(--text-muted);">รายการจอง:</span>
                        <h3
                            style="font-family: 'Plus Jakarta Sans', monospace; font-size: 22px; color: var(--primary); font-weight: 800; margin: 0;">
                            {{ $rental->formatted_code }}
                        </h3>
                        <span style="font-size: 12px; color: #888;">วันที่จอง:
                            {{ $rental->created_at->format('d/m/Y H:i') }} น.</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <!-- สถานะการเช่า -->
                        <span class="badge {{ $rental->status_badge_class }}" style="font-size: 13px; padding: 6px 14px;">
                            สถานะการเช่า: <strong>{{ $rental->status_label }}</strong>
                        </span>

                        <!-- สถานะการชำระ -->
                        <span class="badge {{ $rental->payment_badge_class }}" style="font-size: 13px; padding: 6px 14px;">
                            การชำระเงิน: <strong>{{ $rental->payment_status_label }}</strong>
                        </span>
                    </div>
                </div>

                <!-- ชุดที่เช่า -->
                <div style="margin-bottom: 12px;">
                    @foreach ($rental->details as $detail)
                        <div class="dress-row">
                            <img src="{{ $detail->product->main_image_url ?? '' }}" class="dress-thumb" alt="ชุด">
                            <div style="flex: 1;">
                                <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">
                                    <a href="{{ route('products.show', $detail->product_id) }}"
                                        style="color: var(--text-main);">
                                        {{ $detail->product->product_name ?? 'ชุดเช่า' }}
                                    </a>
                                </h4>
                                <div
                                    style="font-size: 13px; color: var(--text-muted); display: flex; gap: 14px; flex-wrap: wrap;">
                                    <span>รหัส: <strong>{{ $detail->product->product_code ?? '-' }}</strong></span>
                                    <span>ไซซ์: <strong>{{ $detail->selected_size ?? 'M' }}</strong></span>
                                    <span>สี: <strong>{{ $detail->selected_color ?? 'ตามแบบ' }}</strong></span>
                                    <span>จำนวน: <strong>{{ $detail->quantity }} ชุด</strong></span>
                                </div>
                            </div>
                            <div style="font-size: 16px; font-weight: 800; color: var(--primary); text-align: right;">
                                ฿{{ number_format($detail->subtotal) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ข้อมูล วันที่, ยอดทั้งหมด, เงินมัดจำ, ชำระแล้ว -->
                <div class="info-table-grid">
                    <div class="info-col">
                        <span class="info-label">วันที่เริ่มเช่า</span>
                        <span class="info-val"
                            style="color: var(--primary);">{{ date('d/m/Y', strtotime($rental->start_date)) }}</span>
                    </div>
                    <div class="info-col">
                        <span class="info-label">วันที่คืนชุด</span>
                        <span class="info-val"
                            style="color: #b91c1c;">{{ date('d/m/Y', strtotime($rental->end_date)) }}</span>
                    </div>
                    <div class="info-col">
                        <span class="info-label">ยอดทั้งหมด (รวมมัดจำ)</span>
                        <span class="info-val">฿{{ number_format($rental->grand_total) }}</span>
                        <span style="font-size: 11px; color: var(--text-muted);">(มัดจำ:
                            ฿{{ number_format($rental->deposit_amount) }})</span>
                    </div>
                    <div class="info-col">
                        <span class="info-label">ชำระแล้วเท่าไร</span>
                        <span class="info-val" style="color: #166534;">
                            ฿{{ number_format($rental->paid_amount) }}
                        </span>
                        @if ($rental->paid_amount < $rental->grand_total)
                            <span style="font-size: 11px; color: #dc2626;">(คงค้าง:
                                ฿{{ number_format($rental->grand_total - $rental->paid_amount) }})</span>
                        @endif
                    </div>
                </div>

                @if ($rental->note)
                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
                        <i class="fa-regular fa-note-sticky"></i> หมายเหตุ: {{ $rental->note }}
                    </div>
                @endif

                <!-- Bottom Actions -->
                <div class="booking-actions">
                    <div>
                        @if (!$rental->latestPayment || $rental->latestPayment->status === 'rejected')
                            <span style="color: #dc2626; font-size: 13px; font-weight: 600;">
                                <i class="fa-solid fa-triangle-exclamation"></i> กรุณาชำระเงินและแนบสลิปเพื่อยืนยันคิวชุด
                            </span>
                        @elseif($rental->latestPayment->status === 'pending')
                            <span style="color: #b45309; font-size: 13px;">
                                <i class="fa-solid fa-clock"></i> แนบสลิปแล้ว กำลังรอร้านตรวจสอบยอดเงิน
                            </span>
                        @elseif($rental->latestPayment->status === 'approved')
                            <span style="color: #166534; font-size: 13px; font-weight: 600;">
                                <i class="fa-solid fa-circle-check"></i> ชำระเงินเรียบร้อยแล้ว
                            </span>
                        @endif
                    </div>

                    <div style="display: flex; gap: 8px;">
                        @if (!$rental->latestPayment || $rental->latestPayment->status === 'rejected')
                            <a href="{{ route('rentals.payment', $rental->rental_id) }}" class="btn btn-gold btn-sm">
                                <i class="fa-solid fa-credit-card"></i> ชำระเงิน / แนบสลิป
                            </a>
                        @endif
                        <a href="{{ route('rentals.show', $rental->rental_id) }}" class="btn btn-secondary btn-sm">
                            ดูรายละเอียดบิล
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div
                style="background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 70px 20px; text-align: center;">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 50px; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">ยังไม่มีรายการเช่าที่กำลังดำเนินการ</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">
                    คุณสามารถเลือกดูชุดที่ชอบและกดจองเช่าชุดได้ทันที</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> เลือกดูชุดทั้งหมด
                </a>
            </div>
        @endforelse
    </div>
@endsection
