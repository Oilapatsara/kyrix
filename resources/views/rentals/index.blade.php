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
            margin: 0;
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

        .payment-paid {
            color: #166534 !important;
        }

        .payment-outstanding {
            color: #dc2626 !important;
        }

        .payment-status-approved {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #166534;
            font-weight: 700;
        }

        .payment-status-pending {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #b45309;
            font-weight: 700;
        }

        .payment-status-rejected {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #b91c1c;
            font-weight: 700;
        }

        .payment-status-none {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #dc2626;
            font-weight: 700;
        }

        .payment-summary {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 10px;
            background: #f7faf7;
            border: 1px solid #dcebdd;
        }

        .payment-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 4px 0;
            font-size: 13px;
        }

        .payment-summary-row .label {
            color: var(--text-muted);
        }

        .payment-summary-row .value {
            font-weight: 700;
        }

        .payment-summary-row.total {
            margin-top: 7px;
            padding-top: 9px;
            border-top: 1px solid #d8e5d9;
        }

        .payment-summary-row.total .value {
            font-size: 17px;
            color: var(--primary);
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
            background: #faf8f5;
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

        .payment-success-note {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #166534;
            font-size: 13px;
            font-weight: 600;
        }

        .payment-pending-note {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #b45309;
            font-size: 13px;
        }

        .payment-rejected-note {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
        }

        .payment-none-note {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .info-table-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .rentals-wrapper {
                padding: 0 16px;
                margin-top: 25px;
            }

            .rentals-header h1 {
                font-size: 22px;
            }

            .booking-card {
                padding: 18px;
            }

            .info-table-grid {
                grid-template-columns: repeat(2, 1fr);
                padding: 14px;
            }

            .dress-row {
                align-items: flex-start;
            }

            .dress-thumb {
                width: 60px;
                height: 74px;
            }

            .payment-summary-row {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 520px) {
            .info-table-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .info-val {
                font-size: 13px;
            }

            .booking-actions {
                align-items: flex-start;
            }
        }
    </style>
@endpush


@section('content')

    <div class="rentals-wrapper">

        {{-- =========================================================
         HEADER
    ========================================================== --}}

        <div class="rentals-header">

            <div>

                <h1>
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i>

                    <span>
                        การเช่าของฉัน (My Rentals)
                    </span>
                </h1>

                <p
                    style="
                    color: var(--text-muted);
                    font-size: 14px;
                    margin-top: 4px;
                ">
                    ดูรายการจองเช่าชุด ตรวจสอบสถานะการเช่าและสถานะการชำระเงิน
                </p>

            </div>

            <div
                style="
                display:flex;
                gap:10px;
                flex-wrap:wrap;
            ">

                <a href="{{ route('rentals.history') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-box-archive"></i>
                    ดูประวัติการเช่า
                </a>

                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    เช่าชุดเพิ่ม
                </a>

            </div>

        </div>


        {{-- =========================================================
         RENTALS
    ========================================================== --}}

        @forelse($activeRentals as $rental)

            @php

                /*
            |--------------------------------------------------------------------------
            | PAYMENT DATA
            |--------------------------------------------------------------------------
            |
            | อ่านจาก payments โดยตรง
            | ใช้ amount + status ซึ่งตรงกับฐานข้อมูลจริง
            |
            */

                $payments = collect($rental->payments ?? []);

                /*
            |--------------------------------------------------------------------------
            | Payment ล่าสุด
            |--------------------------------------------------------------------------
            */

                $latestPayment = $payments->sortByDesc('payment_id')->first();

                /*
            |--------------------------------------------------------------------------
            | ยอดที่อนุมัติแล้ว
            |--------------------------------------------------------------------------
            */

                $approvedPayments = $payments->filter(function ($payment) {
                    return strtolower(trim((string) ($payment->status ?? ''))) === 'approved';
                });

                $paidAmount = (float) $approvedPayments->sum(function ($payment) {
                    return (float) ($payment->amount ?? 0);
                });

                /*
            |--------------------------------------------------------------------------
            | ยอดรวมรายการเช่า
            |--------------------------------------------------------------------------
            */

                $grandTotal = (float) ($rental->grand_total ?? 0);

                /*
            |--------------------------------------------------------------------------
            | คงค้าง
            |--------------------------------------------------------------------------
            */

                $remainingAmount = max(0, $grandTotal - $paidAmount);

                /*
            |--------------------------------------------------------------------------
            | สถานะการชำระเงินจริงจาก Payment ล่าสุด
            |--------------------------------------------------------------------------
            */

                $paymentStatus = strtolower(trim((string) ($latestPayment->status ?? '')));
            @endphp


            <div class="booking-card">

                {{-- =================================================
                 TOP
            ================================================== --}}

                <div class="booking-card-top">

                    <div>

                        <span
                            style="
                            font-size:12px;
                            color:var(--text-muted);
                        ">
                            รายการจอง:
                        </span>

                        <h3
                            style="
                            font-family:'Plus Jakarta Sans', monospace;
                            font-size:22px;
                            color:var(--primary);
                            font-weight:800;
                            margin:0;
                        ">
                            {{ $rental->formatted_code }}
                        </h3>

                        <span
                            style="
                            font-size:12px;
                            color:#888;
                        ">
                            วันที่จอง:
                            {{ $rental->created_at ? $rental->created_at->format('d/m/Y H:i') : '-' }}
                            น.
                        </span>

                    </div>


                    {{-- STATUS --}}

                    <div
                        style="
                        display:flex;
                        align-items:center;
                        gap:10px;
                        flex-wrap:wrap;
                    ">

                        {{-- สถานะการเช่า --}}

                        <span class="badge {{ $rental->status_badge_class }}"
                            style="
                            font-size:13px;
                            padding:6px 14px;
                        ">
                            สถานะการเช่า:

                            <strong>
                                {{ $rental->status_label }}
                            </strong>
                        </span>


                        {{-- สถานะการชำระ --}}

                        @if ($paymentStatus === 'approved')
                            <span class="badge badge-success"
                                style="
                                font-size:13px;
                                padding:6px 14px;
                            ">
                                การชำระเงิน:
                                <strong>
                                    ชำระแล้ว
                                </strong>
                            </span>
                        @elseif($paymentStatus === 'pending')
                            <span class="badge badge-warning"
                                style="
                                font-size:13px;
                                padding:6px 14px;
                            ">
                                การชำระเงิน:
                                <strong>
                                    รอตรวจสอบ
                                </strong>
                            </span>
                        @elseif($paymentStatus === 'rejected')
                            <span class="badge badge-danger"
                                style="
                                font-size:13px;
                                padding:6px 14px;
                            ">
                                การชำระเงิน:
                                <strong>
                                    ถูกปฏิเสธ
                                </strong>
                            </span>
                        @else
                            <span class="badge badge-danger"
                                style="
                                font-size:13px;
                                padding:6px 14px;
                            ">
                                การชำระเงิน:
                                <strong>
                                    ยังไม่ชำระ
                                </strong>
                            </span>
                        @endif

                    </div>

                </div>


                {{-- =================================================
                 DRESS ITEMS
            ================================================== --}}

                <div style="margin-bottom:12px;">

                    @foreach ($rental->details as $detail)
                        @php

                            $imageUrl = $detail->product->main_image_url ?? '';

                        @endphp


                        <div class="dress-row">

                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" class="dress-thumb"
                                    alt="{{ $detail->product->product_name ?? 'ชุด' }}">
                            @else
                                <div class="dress-thumb"
                                    style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#aaa;
                                ">
                                    <i class="fa-solid fa-shirt"></i>
                                </div>
                            @endif


                            <div style="flex:1;">

                                <h4
                                    style="
                                    font-size:16px;
                                    font-weight:700;
                                    margin-bottom:4px;
                                ">

                                    <a href="{{ route('products.show', $detail->product_id) }}"
                                        style="
                                        color:var(--text-main);
                                    ">
                                        {{ $detail->product->product_name ?? 'ชุดเช่า' }}
                                    </a>

                                </h4>


                                <div
                                    style="
                                    font-size:13px;
                                    color:var(--text-muted);
                                    display:flex;
                                    gap:14px;
                                    flex-wrap:wrap;
                                ">

                                    <span>
                                        รหัส:
                                        <strong>
                                            {{ $detail->product->product_code ?? '-' }}
                                        </strong>
                                    </span>

                                    <span>
                                        ไซซ์:
                                        <strong>
                                            {{ $detail->selected_size ?? 'M' }}
                                        </strong>
                                    </span>

                                    <span>
                                        สี:
                                        <strong>
                                            {{ $detail->selected_color ?? 'ตามแบบ' }}
                                        </strong>
                                    </span>

                                    <span>
                                        จำนวน:
                                        <strong>
                                            {{ $detail->quantity }} ชุด
                                        </strong>
                                    </span>

                                </div>

                            </div>


                            <div
                                style="
                                font-size:16px;
                                font-weight:800;
                                color:var(--primary);
                                text-align:right;
                            ">
                                ฿{{ number_format((float) $detail->subtotal, 2) }}
                            </div>

                        </div>
                    @endforeach

                </div>


                {{-- =================================================
                 RENTAL / PAYMENT SUMMARY
            ================================================== --}}

                <div class="info-table-grid">

                    {{-- วันที่เริ่มเช่า --}}

                    <div class="info-col">

                        <span class="info-label">
                            วันที่เริ่มเช่า
                        </span>

                        <span class="info-val" style="color:var(--primary);">
                            {{ $rental->start_date ? date('d/m/Y', strtotime($rental->start_date)) : '-' }}
                        </span>

                    </div>


                    {{-- วันที่คืนชุด --}}

                    <div class="info-col">

                        <span class="info-label">
                            วันที่คืนชุด
                        </span>

                        <span class="info-val" style="color:#b91c1c;">
                            {{ $rental->end_date ? date('d/m/Y', strtotime($rental->end_date)) : '-' }}
                        </span>

                    </div>


                    {{-- ยอดทั้งหมด --}}

                    <div class="info-col">

                        <span class="info-label">
                            ยอดทั้งหมด (รวมมัดจำ)
                        </span>

                        <span class="info-val">
                            ฿{{ number_format($grandTotal, 2) }}
                        </span>

                        <span
                            style="
                            font-size:11px;
                            color:var(--text-muted);
                        ">
                            (มัดจำ:
                            ฿{{ number_format((float) ($rental->deposit_amount ?? 0), 2) }})
                        </span>

                    </div>


                    {{-- ชำระแล้ว --}}

                    <div class="info-col">

                        <span class="info-label">
                            ชำระแล้ว
                        </span>

                        <span class="info-val payment-paid">
                            ฿{{ number_format($paidAmount, 2) }}
                        </span>


                        @if ($remainingAmount > 0)
                            <span class="payment-outstanding"
                                style="
                                font-size:11px;
                                font-weight:600;
                            ">
                                คงค้าง:
                                ฿{{ number_format($remainingAmount, 2) }}
                            </span>
                        @else
                            <span
                                style="
                                font-size:11px;
                                color:#166534;
                                font-weight:600;
                            ">
                                คงค้าง:
                                ฿0.00
                            </span>
                        @endif

                    </div>

                </div>


                {{-- =================================================
                 PAYMENT SUMMARY DETAIL
            ================================================== --}}

                <div class="payment-summary">

                    <div class="payment-summary-row">

                        <span class="label">
                            ยอดรวมที่ต้องชำระ
                        </span>

                        <span class="value">
                            ฿{{ number_format($grandTotal, 2) }}
                        </span>

                    </div>


                    <div class="payment-summary-row">

                        <span class="label">
                            ชำระแล้ว
                        </span>

                        <span class="value" style="color:#166534;">
                            ฿{{ number_format($paidAmount, 2) }}
                        </span>

                    </div>


                    <div class="payment-summary-row total">

                        <span class="label">
                            ยอดคงค้าง
                        </span>

                        <span class="value"
                            style="
                            color:
                                {{ $remainingAmount > 0 ? '#dc2626' : '#166534' }};
                        ">
                            ฿{{ number_format($remainingAmount, 2) }}
                        </span>

                    </div>

                </div>


                {{-- =================================================
                 NOTE
            ================================================== --}}

                @if ($rental->note)
                    <div
                        style="
                        font-size:13px;
                        color:var(--text-muted);
                        margin:14px 0;
                        white-space:pre-line;
                    ">

                        <i class="fa-regular fa-note-sticky"></i>

                        หมายเหตุ:

                        {{ $rental->note }}

                    </div>
                @endif


                {{-- =================================================
                 BOTTOM ACTIONS
            ================================================== --}}

                <div class="booking-actions">

                    <div>

                        {{-- APPROVED --}}

                        @if ($paymentStatus === 'approved')
                            <span class="payment-success-note">
                                <i class="fa-solid fa-circle-check"></i>

                                ชำระเงินเรียบร้อยแล้ว
                            </span>


                            {{-- PENDING --}}
                        @elseif($paymentStatus === 'pending')
                            <span class="payment-pending-note">
                                <i class="fa-solid fa-clock"></i>

                                แนบสลิปแล้ว
                                กำลังรอร้านตรวจสอบยอดเงิน
                            </span>


                            {{-- REJECTED --}}
                        @elseif($paymentStatus === 'rejected')
                            <span class="payment-rejected-note">
                                <i class="fa-solid fa-triangle-exclamation"></i>

                                สลิปถูกปฏิเสธ
                                กรุณาชำระเงินและแนบสลิปใหม่
                            </span>


                            {{-- NO PAYMENT --}}
                        @else
                            <span class="payment-none-note">
                                <i class="fa-solid fa-triangle-exclamation"></i>

                                กรุณาชำระเงินและแนบสลิป
                                เพื่อยืนยันคิวชุด
                            </span>
                        @endif

                    </div>


                    <div
                        style="
                        display:flex;
                        gap:8px;
                        flex-wrap:wrap;
                    ">

                        {{-- ยังไม่ชำระ / ถูกปฏิเสธ --}}

                        @if (!$latestPayment || $paymentStatus === 'rejected')
                            <a href="{{ route('rentals.payment', $rental->rental_id) }}"
                                class="btn btn-gold btn-sm">

                                <i class="fa-solid fa-credit-card"></i>

                                ชำระเงิน / แนบสลิป

                            </a>
                        @endif


                        <a href="{{ route('rentals.show', $rental->rental_id) }}"
                            class="btn btn-secondary btn-sm">

                            <i class="fa-solid fa-file-invoice"></i>

                            ดูรายละเอียดบิล

                        </a>

                    </div>

                </div>

            </div>

        @empty

            {{-- =====================================================
             EMPTY
        ====================================================== --}}

            <div
                style="
                background:#fff;
                border-radius:var(--radius-lg);
                border:1px solid var(--border);
                padding:70px 20px;
                text-align:center;
            ">

                <i class="fa-solid fa-calendar-xmark"
                    style="
                    font-size:50px;
                    color:#cbd5e1;
                    margin-bottom:16px;
                "></i>

                <h3
                    style="
                    font-size:20px;
                    font-weight:700;
                    margin-bottom:8px;
                ">
                    ยังไม่มีรายการเช่าที่กำลังดำเนินการ
                </h3>

                <p
                    style="
                    color:var(--text-muted);
                    font-size:14px;
                    margin-bottom:24px;
                ">
                    คุณสามารถเลือกดูชุดที่ชอบและกดจองเช่าชุดได้ทันที
                </p>

                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>

                    เลือกดูชุดทั้งหมด
                </a>

            </div>
        @endforelse

    </div>

@endsection
