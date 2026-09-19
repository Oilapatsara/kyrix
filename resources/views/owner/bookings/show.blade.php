@extends('layouts.owner')

@section('title', 'รายละเอียดการเช่า | KYRIX Admin')

@push('styles')
    <style>
        :root {
            --maroon-900: #430d17;
            --maroon-800: #5c1522;
            --maroon-700: #6f1a2b;
            --gold: #c79a5c;
            --gold-dark: #a97f45;
            --rose-bg: #f7e7ea;
            --rose-text: #7f2138;
            --cream: #faf7f4;
            --ink: #241417;
            --muted: #8a7a7d;
            --line: #efe6e4;
            --danger-bg: #fff1f2;
            --danger-text: #b42318;
            --danger-line: #fecdd3;
        }

        .kyrix-admin-container {
            padding: 0;
            color: var(--ink);
            max-width: 1200px;
            margin: 0 auto;
        }

        /* HEADER */
        .admin-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .admin-heading .eyebrow {
            display: inline-block;
            color: var(--gold-dark);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .admin-heading h1 {
            margin: 0;
            font-family: 'Prompt', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--maroon-900);
        }

        .admin-heading p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        /* GRID */
        .detail-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
            align-items: start;
        }

        /* CARDS */
        .detail-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(67, 13, 23, 0.03);
            padding: 24px;
            margin-bottom: 24px;
        }

        .detail-card:last-child {
            margin-bottom: 0;
        }

        .card-title {
            font-size: 15px;
            font-weight: 750;
            color: var(--maroon-900);
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--gold);
            font-size: 16px;
        }

        /* INFO ROWS */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 10px 0;
            font-size: 13px;
            border-bottom: 1px dashed var(--line);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--muted);
            font-weight: 550;
        }

        .info-value {
            color: var(--ink);
            font-weight: 650;
            text-align: right;
        }

        /* PRODUCT TABLE */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .product-table th {
            text-align: left;
            padding: 10px 12px;
            background: var(--cream);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-table td {
            padding: 14px 12px;
            border-top: 1px solid var(--line);
            vertical-align: middle;
        }

        .product-thumb {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--line);
            background: var(--cream);
        }

        /* STATUS */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pending {
            background: #fff5dd;
            color: #9a6b00;
        }

        .status-confirmed {
            background: #edf5ff;
            color: #4773a6;
        }

        .status-renting {
            background: var(--rose-bg);
            color: var(--rose-text);
        }

        .status-returned {
            background: #eef7f7;
            color: #477f80;
        }

        .status-completed {
            background: #eef7ef;
            color: #4f7e53;
        }

        .status-cancelled {
            background: #fff1f2;
            color: #b42318;
            border: 1px solid #fecdd3;
        }

        /* CANCELLED BOX */
        .cancelled-box {
            margin-bottom: 24px;
            padding: 18px 20px;
            background: var(--danger-bg);
            border: 1px solid var(--danger-line);
            border-radius: 14px;
        }

        .cancelled-box-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--danger-text);
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .cancelled-box-title i {
            font-size: 16px;
        }

        .cancelled-reason {
            background: #fff;
            border: 1px solid var(--danger-line);
            border-radius: 10px;
            padding: 12px 14px;
            color: #5f3b40;
            font-size: 13px;
            line-height: 1.7;
            white-space: pre-line;
        }

        .cancelled-meta {
            margin-top: 10px;
            font-size: 11.5px;
            color: #9d747a;
        }

        /* BUTTONS */
        .admin-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 650;
            transition: .2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .admin-btn.primary {
            background: linear-gradient(135deg,
                    var(--maroon-700),
                    var(--maroon-900));
            color: #fff;
            box-shadow: 0 4px 12px rgba(111, 26, 43, .2);
        }

        .admin-btn.primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(111, 26, 43, .3);
        }

        .admin-btn.secondary {
            background: #fff;
            color: var(--maroon-800);
            border-color: var(--line);
        }

        .admin-btn.secondary:hover {
            background: var(--cream);
            border-color: var(--gold);
        }

        /* FORMS */
        .form-control {
            width: 100%;
            min-height: 42px;
            padding: 9px 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 13.5px;
            outline: none;
            background: #fff;
            color: var(--ink);
            transition: .2s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(199, 154, 92, 0.15);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--maroon-900);
            margin-bottom: 6px;
        }

        .locked-status-box {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
        }

        .locked-status-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 14px;
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 10px;
            color: var(--muted);
            font-size: 12.5px;
            line-height: 1.6;
        }

        .locked-status-note i {
            color: var(--gold-dark);
            margin-top: 2px;
        }

        .shipping-section {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
        }

        .shipping-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            color: var(--maroon-900);
            font-size: 14px;
            font-weight: 800;
        }

        .shipping-section-title i {
            color: var(--gold);
        }

        .form-help {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.5;
        }

        /* RESPONSIVE */
        @media (max-width: 960px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .admin-heading h1 {
                font-size: 23px;
            }

            .info-row {
                align-items: flex-start;
                flex-direction: column;
                gap: 4px;
            }

            .info-value {
                text-align: left;
            }

            .admin-header>div:last-child {
                width: 100%;
            }

            .admin-btn.secondary {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="kyrix-admin-container">

        @php
            $rentalId = $rental->rental_id ?? ($rental->id ?? null);

            $statusCode = strtolower($rental->status ?? 'pending');

            $statusText = match ($statusCode) {
                'pending', 'pending_payment' => 'รอชำระเงิน',

                'pending_verification' => 'รอตรวจสอบสลิป',

                'confirmed' => 'ยืนยันแล้ว',

                'ready_pickup' => 'รอรับชุด',

                'renting' => 'กำลังเช่า',

                'pending_return' => 'รอตรวจรับคืน',

                'returned' => 'คืนชุดแล้ว',

                'completed' => 'เสร็จสิ้น',

                'cancelled' => 'ยกเลิกแล้ว',

                default => ucfirst($rental->status ?? '-'),
            };

            $statusClass = match ($statusCode) {
                'pending', 'pending_payment', 'pending_verification', 'pending_return' => 'status-pending',

                'confirmed', 'ready_pickup' => 'status-confirmed',

                'renting' => 'status-renting',

                'returned' => 'status-returned',

                'completed' => 'status-completed',

                'cancelled' => 'status-cancelled',

                default => 'status-pending',
            };

            $cancelReason = null;
            $cancelledAt = null;

            // ดึงเหตุผลการยกเลิกจาก note
            if ($statusCode === 'cancelled' && !empty($rental->note)) {
                $noteLines = preg_split("/\r\n|\n|\r/", $rental->note);

                foreach ($noteLines as $line) {
                    if (str_contains($line, 'เหตุผล:')) {
                        $cancelReason = trim(str_replace('เหตุผล:', '', $line));
                    }

                    if (str_contains($line, 'ยกเลิกโดยลูกค้าเมื่อ')) {
                        $cancelledAt = trim($line);
                    }
                }
            }

            // ข้อมูลลูกค้า
            $customer = $rental->customer ?? null;

            $customerName = 'ไม่ระบุ';

            if ($customer) {
                $customerName =
                    $customer->full_name ?? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));

                if (trim($customerName) === '') {
                    $customerName = 'ไม่ระบุ';
                }
            }
        @endphp

        {{-- HEADER --}}
        <div class="admin-header">
            <div class="admin-heading">

                <span class="eyebrow">
                    KYRIX RENTAL · BOOKING DETAIL
                </span>

                <div
                    style="
                    display: flex;
                    align-items: center;
                    gap: 14px;
                    margin-top: 4px;
                    flex-wrap: wrap;
                ">
                    <h1>
                        รายละเอียดการเช่า #{{ $rental->formatted_code ?? $rentalId }}
                    </h1>

                    <span class="status-badge {{ $statusClass }}">

                        @if ($statusCode === 'cancelled')
                            <i class="fa-solid fa-circle-xmark"></i>
                        @elseif ($statusCode === 'completed')
                            <i class="fa-solid fa-circle-check"></i>
                        @elseif ($statusCode === 'returned')
                            <i class="fa-solid fa-box"></i>
                        @elseif ($statusCode === 'confirmed' || $statusCode === 'ready_pickup')
                            <i class="fa-solid fa-check"></i>
                        @elseif ($statusCode === 'renting')
                            <i class="fa-solid fa-shirt"></i>
                        @else
                            <i class="fa-solid fa-clock"></i>
                        @endif

                        {{ $statusText }}
                    </span>
                </div>

                <p>
                    ตรวจสอบข้อมูลรายการเช่า ข้อมูลลูกค้า
                    และอัปเดตสถานะการดำเนินงานคำสั่งซื้อ
                </p>
            </div>

            <div>
                <a href="{{ route('owner.bookings.index') }}" class="admin-btn secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    กลับหน้าจัดการรายการเช่า
                </a>
            </div>
        </div>

        {{-- CANCELLED NOTICE --}}
        @if ($statusCode === 'cancelled')
            <div class="cancelled-box">

                <div class="cancelled-box-title">
                    <i class="fa-solid fa-circle-xmark"></i>
                    รายการนี้ถูกยกเลิกแล้ว
                </div>

                <div class="cancelled-reason">
                    @if ($cancelReason)
                        {{ $cancelReason }}
                    @else
                        ไม่พบเหตุผลการยกเลิกในข้อมูลรายการ
                    @endif
                </div>

                @if ($cancelledAt)
                    <div class="cancelled-meta">
                        <i class="fa-regular fa-clock"></i>
                        {{ $cancelledAt }}
                    </div>
                @endif

            </div>
        @endif

        {{-- 2 COLUMN --}}
        <div class="detail-grid">

            {{-- LEFT COLUMN --}}
            <div>

                {{-- PRODUCTS --}}
                <div class="detail-card">

                    <div class="card-title">
                        <i class="fa-solid fa-shirt"></i>
                        รายการชุดในคำขอเช่านี้
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="product-table">

                            <thead>
                                <tr>
                                    <th style="width: 15%;">
                                        รูปภาพ
                                    </th>

                                    <th style="width: 55%;">
                                        ชื่อชุด / รหัส
                                    </th>

                                    <th style="width: 15%; text-align: center;">
                                        จำนวน
                                    </th>

                                    <th style="width: 15%; text-align: right;">
                                        ราคา
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($rental->details ?? [] as $detail)

                                    @php
                                        $product = $detail->product ?? null;
                                        $img = null;

                                        if ($product && $product->images && $product->images->count() > 0) {
                                            $img =
                                                $product->images->first()->image_path ??
                                                ($product->images->first()->url ?? null);
                                        } elseif ($product && !empty($product->image)) {
                                            $img = $product->image;
                                        }
                                    @endphp

                                    <tr>

                                        <td>
                                            @if ($img)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])
                                                    ? $img
                                                    : asset('storage/' . ltrim($img, '/')) }}"
                                                    class="product-thumb" alt="รูปชุด">
                                            @else
                                                <div class="product-thumb"
                                                    style="
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    color: var(--rose-text);
                                                    background: var(--rose-bg);
                                                ">
                                                    <i class="fa-solid fa-shirt"></i>
                                                </div>
                                            @endif
                                        </td>

                                        <td>

                                            <div
                                                style="
                                                font-weight: 700;
                                                color: var(--maroon-900);
                                            ">
                                                {{ $product->name ?? ($product->product_name ?? 'ชุดสินค้า') }}
                                            </div>

                                            <div
                                                style="
                                                font-size: 11px;
                                                color: var(--muted);
                                                margin-top: 2px;
                                            ">
                                                รหัส:
                                                {{ $product->product_code ?? '-' }}
                                            </div>

                                        </td>

                                        <td
                                            style="
                                            text-align: center;
                                            font-weight: 600;
                                        ">
                                            {{ $detail->quantity ?? 1 }}
                                        </td>

                                        <td
                                            style="
                                            text-align: right;
                                            font-weight: 750;
                                            color: var(--maroon-900);
                                        ">
                                            ฿{{ number_format($detail->rental_price ?? ($detail->price ?? ($product->rental_price ?? 0)), 2) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4"
                                            style="
                                            padding: 30px;
                                            text-align: center;
                                            color: var(--muted);
                                        ">
                                            ไม่มีรายการชุดในคำขอนี้
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>
                    </div>

                </div>

                {{-- PAYMENT --}}
                <div class="detail-card">

                    <div class="card-title">
                        <i class="fa-solid fa-receipt"></i>
                        ข้อมูลการชำระเงิน
                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            ค่าเช่าชุดรวม
                        </span>

                        <span class="info-value">
                            ฿{{ number_format($rental->total_amount ?? 0, 2) }}
                        </span>

                    </div>

                    @if (!empty($rental->discount_amount) && $rental->discount_amount > 0)

                        <div class="info-row"
                            style="
                            background: #f0fdf4;
                            border-radius: 8px;
                            padding: 6px 10px;
                            margin: 4px 0;
                        ">

                            <span class="info-label"
                                style="
                                color: #16a34a;
                                font-weight: 700;
                            ">

                                <i class="fa-solid fa-gift"></i>

                                ส่วนลดโปรโมชั่น

                                @if ($rental->discount_reason)
                                    <br>

                                    <small
                                        style="
                                        font-size: 11px;
                                        font-weight: normal;
                                        color: #15803d;
                                    ">
                                        {{ $rental->discount_reason }}
                                    </small>
                                @endif

                            </span>

                            <span class="info-value"
                                style="
                                color: #16a34a;
                                font-weight: 750;
                            ">
                                -฿{{ number_format($rental->discount_amount, 2) }}
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                ค่าเช่าสุทธิหลังหักส่วนลด
                            </span>

                            <span class="info-value">
                                ฿{{ number_format($rental->net_rental_amount ?? 0, 2) }}
                            </span>

                        </div>

                    @endif

                    <div class="info-row">

                        <span class="info-label">
                            เงินมัดจำประกันชุด
                        </span>

                        <span class="info-value">
                            ฿{{ number_format($rental->deposit_amount ?? 100, 2) }}
                        </span>

                    </div>

                    @if (!empty($rental->service_fee) && $rental->service_fee > 0)
                        <div class="info-row">

                            <span class="info-label">
                                ค่าบริการเสริม
                                ({{ $rental->service_type }})
                            </span>

                            <span class="info-value">
                                ฿{{ number_format($rental->service_fee, 2) }}
                            </span>

                        </div>
                    @endif

                    <div class="info-row"
                        style="
                        border-top: 1px solid var(--line);
                        margin-top: 8px;
                        padding-top: 8px;
                    ">

                        <span class="info-label"
                            style="
                            font-weight: 750;
                            color: var(--maroon-900);
                        ">
                            ยอดรวมสุทธิที่ลูกค้าต้องชำระ
                        </span>

                        <span class="info-value"
                            style="
                            font-size: 18px;
                            font-weight: 800;
                            color: var(--maroon-900);
                        ">
                            ฿{{ number_format($rental->grand_total ?? 0, 2) }}
                        </span>

                    </div>

                    {{-- PAYMENT HISTORY --}}
                    <div
                        style="
                        margin-top: 14px;
                        padding-top: 12px;
                        border-top: 1px dashed var(--line);
                    ">

                        <div
                            style="
                            font-size: 12px;
                            font-weight: 700;
                            color: var(--muted);
                            margin-bottom: 8px;
                        ">
                            ประวัติสลิปการชำระเงิน:
                        </div>

                        @forelse ($rental->payments ?? [] as $payment)
                            <div
                                style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                gap: 12px;
                                padding: 6px 0;
                                font-size: 12.5px;
                            ">

                                <div>

                                    <span>
                                        ยอดตามสลิป:

                                        <strong>
                                            ฿{{ number_format($payment->payment_amount ?? ($payment->amount ?? 0), 2) }}
                                        </strong>
                                    </span>

                                    <span
                                        style="
                                        font-size: 11px;
                                        color: #888;
                                    ">
                                        ({{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : '-' }})
                                    </span>

                                </div>

                                <div>

                                    @if ($payment->status === 'approved')
                                        <span
                                            style="
                                            color: #16a34a;
                                            font-weight: 700;
                                        ">
                                            <i class="fa-solid fa-check"></i>
                                            อนุมัติแล้ว
                                        </span>
                                    @else
                                        <span
                                            style="
                                            color: #d97706;
                                            font-weight: 700;
                                        ">
                                            รอตรวจสอบ
                                        </span>
                                    @endif

                                    @if ($payment->slip_url)
                                        <a href="{{ $payment->slip_url }}" target="_blank"
                                            style="
                                            margin-left: 8px;
                                            color: var(--maroon-900);
                                            text-decoration: underline;
                                        ">
                                            <i class="fa-solid fa-image"></i>
                                            สลิป
                                        </a>
                                    @endif

                                </div>

                            </div>

                        @empty

                            <div
                                style="
                                color: var(--muted);
                                font-size: 12px;
                                text-align: center;
                                padding: 10px 0;
                            ">
                                ยังไม่มีประวัติการแจ้งชำระเงินในระบบ
                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div>

                {{-- CUSTOMER --}}
                <div class="detail-card">

                    <div class="card-title">
                        <i class="fa-solid fa-user-tag"></i>
                        ข้อมูลลูกค้า
                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            ชื่อ-นามสกุล
                        </span>

                        <span class="info-value">
                            {{ $customerName }}
                        </span>

                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            เบอร์โทรศัพท์
                        </span>

                        <span class="info-value">
                            {{ $customer->phone ?? '-' }}
                        </span>

                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            อีเมลติดต่อ
                        </span>

                        <span class="info-value" style="font-size: 12px;">
                            {{ $customer->email ?? '-' }}
                        </span>

                    </div>

                </div>

                {{-- SCHEDULE + STATUS --}}
                <div class="detail-card">

                    <div class="card-title">
                        <i class="fa-solid fa-calendar-days"></i>
                        กำหนดการ & จัดการสถานะ
                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            วันรับชุด
                        </span>

                        <span class="info-value">
                            {{ !empty($rental->start_date) ? \Carbon\Carbon::parse($rental->start_date)->format('d/m/Y') : '-' }}
                        </span>

                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            วันคืนชุด
                        </span>

                        <span class="info-value">
                            {{ !empty($rental->end_date) ? \Carbon\Carbon::parse($rental->end_date)->format('d/m/Y') : '-' }}
                        </span>

                    </div>

                    <div class="info-row"
                        style="
                        margin-top: 6px;
                        padding-top: 12px;
                        border-top: 1px solid var(--line);
                    ">

                        <span class="info-label"
                            style="
                            font-weight: 750;
                            color: var(--maroon-900);
                        ">
                            ยอดรวมทั้งสิ้น
                        </span>

                        <span class="info-value"
                            style="
                            font-size: 18px;
                            font-weight: 800;
                            color: var(--maroon-900);
                        ">
                            ฿{{ number_format($rental->grand_total ?? 0, 2) }}
                        </span>

                    </div>

                    {{-- SHIPPING INFO --}}
                    @if ($rental->shipping_carrier || $rental->tracking_number || $rental->shipping_status || $rental->return_due_at)

                        <div class="shipping-section">

                            <div class="shipping-section-title">
                                <i class="fa-solid fa-truck-fast"></i>
                                ข้อมูลการจัดส่ง
                            </div>

                            @if ($rental->shipping_carrier)
                                <div class="info-row">

                                    <span class="info-label">
                                        บริษัทขนส่ง
                                    </span>

                                    <span class="info-value">
                                        {{ $rental->shipping_carrier }}
                                    </span>

                                </div>
                            @endif

                            @if ($rental->tracking_number)
                                <div class="info-row">

                                    <span class="info-label">
                                        เลขพัสดุ
                                    </span>

                                    <span class="info-value" style="color: var(--maroon-900);">
                                        {{ $rental->tracking_number }}
                                    </span>

                                </div>
                            @endif

                            @if ($rental->shipping_status)
                                <div class="info-row">

                                    <span class="info-label">
                                        สถานะการจัดส่ง
                                    </span>

                                    <span class="info-value">
                                        {{ $rental->shipping_status }}
                                    </span>

                                </div>
                            @endif

                            @if ($rental->shipped_at)
                                <div class="info-row">

                                    <span class="info-label">
                                        วันที่ส่งพัสดุ
                                    </span>

                                    <span class="info-value">
                                        {{ $rental->shipped_at->format('d/m/Y H:i') }} น.
                                    </span>

                                </div>
                            @endif

                            @if ($rental->estimated_delivery_at)
                                <div class="info-row">

                                    <span class="info-label">
                                        คาดว่าจะถึง
                                    </span>

                                    <span class="info-value">
                                        {{ $rental->estimated_delivery_at->format('d/m/Y H:i') }} น.
                                    </span>

                                </div>
                            @endif

                            {{-- AUTO TRACKING LINK --}}
                            @if ($rental->tracking_url)
                                <div style="margin-top: 12px;">

                                    <a href="{{ $rental->tracking_url }}" target="_blank" rel="noopener noreferrer"
                                        class="admin-btn secondary" style="width: 100%;">
                                        <i class="fa-solid fa-location-arrow"></i>
                                        เปิดหน้าติดตามพัสดุ
                                    </a>

                                </div>
                            @endif

                            @if ($rental->return_due_at)

                                @php
                                    $isOverdue =
                                        now()->greaterThan($rental->return_due_at) &&
                                        !in_array($rental->status, ['returned', 'completed', 'cancelled'], true);
                                @endphp

                                <div
                                    style="
                                    margin-top: 14px;
                                    padding: 14px;
                                    border-radius: 10px;
                                    background: {{ $isOverdue ? '#fff1f2' : '#faf7f4' }};
                                    border: 1px solid {{ $isOverdue ? '#fecdd3' : 'var(--line)' }};
                                ">

                                    <div
                                        style="
                                        font-size: 11px;
                                        color: var(--muted);
                                        margin-bottom: 4px;
                                    ">
                                        กำหนดคืนชุด
                                    </div>

                                    <div
                                        style="
                                        font-size: 15px;
                                        font-weight: 800;
                                        color: {{ $isOverdue ? '#b42318' : 'var(--maroon-900)' }};
                                    ">
                                        {{ $rental->return_due_at->format('d/m/Y H:i') }} น.
                                    </div>

                                    @if ($isOverdue)
                                        <div
                                            style="
                                            margin-top: 5px;
                                            font-size: 11px;
                                            color: #b42318;
                                        ">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                            เกินกำหนดคืนแล้ว
                                        </div>
                                    @else
                                        <div
                                            style="
                                            margin-top: 5px;
                                            font-size: 11px;
                                            color: var(--muted);
                                        ">
                                            ลูกค้าต้องคืนชุดภายในวันและเวลานี้
                                        </div>
                                    @endif

                                </div>

                            @endif

                            @if ($rental->return_tracking_no)
                                <div class="info-row">

                                    <span class="info-label">
                                        เลขพัสดุส่งคืน
                                    </span>

                                    <span class="info-value" style="color: #166534;">
                                        {{ $rental->return_tracking_no }}
                                    </span>

                                </div>
                            @endif

                        </div>

                    @endif

                    {{-- CANCELLED --}}
                    @if ($statusCode === 'cancelled')
                        <div class="locked-status-box">

                            <div class="locked-status-note">

                                <i class="fa-solid fa-lock"></i>

                                <div>

                                    <strong style="color: var(--maroon-900);">
                                        รายการถูกยกเลิกแล้ว
                                    </strong>

                                    <br>

                                    รายการนี้ถูกยกเลิกโดยลูกค้าในขั้นตอน
                                    <strong>รอชำระเงิน</strong>
                                    ระบบได้คืนจำนวนชุดกลับเข้าสู่ Stock
                                    อัตโนมัติแล้ว

                                </div>

                            </div>

                        </div>
                    @else
                        {{-- STATUS FORM --}}
                        <div
                            style="
                            margin-top: 22px;
                            padding-top: 20px;
                            border-top: 1px solid var(--line);
                        ">

                            <form action="{{ route('owner.bookings.updateStatus', $rentalId) }}" method="POST">

                                @csrf

                                {{-- STATUS --}}
                                <div class="form-group">

                                    <label class="form-label">
                                        อัปเดตสถานะการเช่า
                                    </label>

                                    <select name="status" class="form-control" required>

                                        <option value="pending_payment"
                                            {{ in_array($statusCode, ['pending', 'pending_payment'], true) ? 'selected' : '' }}>
                                            รอชำระเงิน
                                        </option>

                                        <option value="pending_verification"
                                            {{ $statusCode === 'pending_verification' ? 'selected' : '' }}>
                                            รอตรวจสอบสลิป
                                        </option>

                                        <option value="confirmed" {{ $statusCode === 'confirmed' ? 'selected' : '' }}>
                                            ยืนยันแล้ว
                                        </option>

                                        <option value="ready_pickup"
                                            {{ $statusCode === 'ready_pickup' ? 'selected' : '' }}>
                                            รอรับชุด
                                        </option>

                                        <option value="renting" {{ $statusCode === 'renting' ? 'selected' : '' }}>
                                            กำลังเช่า
                                        </option>

                                        <option value="pending_return"
                                            {{ $statusCode === 'pending_return' ? 'selected' : '' }}>
                                            รอตรวจรับคืน
                                        </option>

                                        <option value="returned" {{ $statusCode === 'returned' ? 'selected' : '' }}>
                                            คืนชุดแล้ว
                                        </option>

                                        <option value="completed" {{ $statusCode === 'completed' ? 'selected' : '' }}>
                                            เสร็จสิ้น
                                        </option>

                                    </select>

                                </div>

                                {{-- SHIPPING --}}
                                <div class="shipping-section">

                                    <div class="shipping-section-title">
                                        <i class="fa-solid fa-truck"></i>
                                        ข้อมูลการจัดส่ง
                                    </div>

                                    {{-- บริษัทขนส่ง --}}
                                    <div class="form-group">

                                        <label class="form-label">
                                            บริษัทขนส่ง
                                        </label>

                                        <select name="shipping_carrier" class="form-control">

                                            <option value="">
                                                -- เลือกบริษัทขนส่ง --
                                            </option>

                                            <option value="ไปรษณีย์ไทย"
                                                {{ ($rental->shipping_carrier ?? '') === 'ไปรษณีย์ไทย' ? 'selected' : '' }}>
                                                ไปรษณีย์ไทย
                                            </option>

                                            <option value="Flash Express"
                                                {{ ($rental->shipping_carrier ?? '') === 'Flash Express' ? 'selected' : '' }}>
                                                Flash Express
                                            </option>

                                            <option value="J&T Express"
                                                {{ ($rental->shipping_carrier ?? '') === 'J&T Express' ? 'selected' : '' }}>
                                                J&T Express
                                            </option>

                                            <option value="KEX"
                                                {{ ($rental->shipping_carrier ?? '') === 'KEX' ? 'selected' : '' }}>
                                                KEX
                                            </option>

                                            <option value="BEST Express"
                                                {{ ($rental->shipping_carrier ?? '') === 'BEST Express' ? 'selected' : '' }}>
                                                BEST Express
                                            </option>

                                            <option value="Ninja Van"
                                                {{ ($rental->shipping_carrier ?? '') === 'Ninja Van' ? 'selected' : '' }}>
                                                Ninja Van
                                            </option>

                                            <option value="DHL"
                                                {{ ($rental->shipping_carrier ?? '') === 'DHL' ? 'selected' : '' }}>
                                                DHL
                                            </option>

                                            <option value="อื่นๆ"
                                                {{ ($rental->shipping_carrier ?? '') === 'อื่นๆ' ? 'selected' : '' }}>
                                                อื่นๆ
                                            </option>

                                        </select>

                                    </div>

                                    {{-- เลขพัสดุ --}}
                                    <div class="form-group">

                                        <label class="form-label">
                                            เลขพัสดุ
                                        </label>

                                        <input type="text" name="tracking_number"
                                            value="{{ $rental->tracking_number ?? '' }}" class="form-control"
                                            placeholder="เช่น TH123456789">

                                    </div>

                                    {{-- สถานะขนส่ง --}}
                                    <div class="form-group">

                                        <label class="form-label">
                                            สถานะการขนส่ง
                                        </label>

                                        <select name="shipping_status" class="form-control">

                                            <option value="">
                                                -- เลือกสถานะการขนส่ง --
                                            </option>

                                            <option value="กำลังเตรียมสินค้า"
                                                {{ ($rental->shipping_status ?? '') === 'กำลังเตรียมสินค้า' ? 'selected' : '' }}>
                                                กำลังเตรียมสินค้า
                                            </option>

                                            <option value="ส่งพัสดุแล้ว"
                                                {{ ($rental->shipping_status ?? '') === 'ส่งพัสดุแล้ว' ? 'selected' : '' }}>
                                                ส่งพัสดุแล้ว
                                            </option>

                                            <option value="กำลังขนส่ง"
                                                {{ ($rental->shipping_status ?? '') === 'กำลังขนส่ง' ? 'selected' : '' }}>
                                                กำลังขนส่ง
                                            </option>

                                            <option value="กำลังนำจ่าย"
                                                {{ ($rental->shipping_status ?? '') === 'กำลังนำจ่าย' ? 'selected' : '' }}>
                                                กำลังนำจ่าย
                                            </option>

                                            <option value="จัดส่งสำเร็จ"
                                                {{ ($rental->shipping_status ?? '') === 'จัดส่งสำเร็จ' ? 'selected' : '' }}>
                                                จัดส่งสำเร็จ
                                            </option>

                                        </select>

                                    </div>

                                    {{-- AUTO TRACKING INFO --}}
                                    <div
                                        style="
                                        margin-top: 6px;
                                        margin-bottom: 16px;
                                        padding: 12px 14px;
                                        border: 1px dashed var(--line);
                                        border-radius: 10px;
                                        background: var(--cream);
                                        color: var(--muted);
                                        font-size: 12px;
                                        line-height: 1.6;
                                    ">

                                        <i class="fa-solid fa-circle-info" style="color: var(--gold-dark);"></i>

                                        ระบบจะสร้างลิงก์ติดตามพัสดุอัตโนมัติจาก

                                        <strong style="color: var(--maroon-900);">
                                            บริษัทขนส่ง + เลขพัสดุ
                                        </strong>

                                        ไม่ต้องกรอกลิงก์เอง

                                    </div>

                                    {{-- วันเวลาส่ง --}}
                                    <div class="form-group">

                                        <label class="form-label">
                                            วันที่และเวลาส่งพัสดุ
                                        </label>

                                        <input type="datetime-local" name="shipped_at"
                                            value="{{ $rental->shipped_at ? $rental->shipped_at->format('Y-m-d\TH:i') : '' }}"
                                            class="form-control">

                                    </div>

                                    {{-- วันเวลาคาดว่าจะถึง --}}
                                    <div class="form-group">

                                        <label class="form-label">
                                            วันที่และเวลาคาดว่าจะถึง
                                        </label>

                                        <input type="datetime-local" name="estimated_delivery_at"
                                            value="{{ $rental->estimated_delivery_at ? $rental->estimated_delivery_at->format('Y-m-d\TH:i') : '' }}"
                                            class="form-control">

                                    </div>

                                    {{-- กำหนดคืน --}}
                                    <div class="form-group">

                                        <label class="form-label">
                                            วันและเวลาที่ต้องคืนชุด
                                        </label>

                                        <input type="datetime-local" name="return_due_at"
                                            value="{{ $rental->return_due_at ? $rental->return_due_at->format('Y-m-d\TH:i') : '' }}"
                                            class="form-control">

                                        <small class="form-help">
                                            ลูกค้าจะเห็นข้อมูลวันและเวลานี้ในหน้ารายละเอียดการเช่า
                                        </small>

                                    </div>

                                    {{-- เลขพัสดุส่งคืน --}}
                                    <div class="form-group">

                                        <label class="form-label">
                                            เลขพัสดุสำหรับส่งคืนชุด
                                        </label>

                                        <input type="text" name="return_tracking_no"
                                            value="{{ $rental->return_tracking_no ?? '' }}" class="form-control"
                                            placeholder="เช่น TH987654321">

                                    </div>

                                </div>

                                {{-- NOTE --}}
                                <div class="form-group">

                                    <label class="form-label">
                                        หมายเหตุ
                                    </label>

                                    <textarea name="note" class="form-control" rows="4"
                                        style="
                                        min-height: 100px;
                                        resize: vertical;
                                    "
                                        placeholder="หมายเหตุเพิ่มเติม">{{ $rental->note ?? '' }}</textarea>

                                </div>

                                {{-- SUBMIT --}}
                                <button type="submit" class="admin-btn primary"
                                    style="
                                    width: 100%;
                                    margin-top: 6px;
                                ">

                                    <i class="fa-solid fa-floppy-disk"></i>

                                    บันทึกสถานะและข้อมูลการจัดส่ง

                                </button>

                            </form>

                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
@endsection
