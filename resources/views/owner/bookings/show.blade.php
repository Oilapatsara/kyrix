@extends('layouts.owner')

@section('title', 'รายละเอียดการเช่า | KYRIX Admin')

@push('styles')
<style>
    :root {
        --maroon-900: #430d17;
        --maroon-800: #5c1522;
        --maroon-700: #6f1a2b;
        --gold:       #c79a5c;
        --gold-dark:  #a97f45;
        --rose-bg:    #f7e7ea;
        --rose-text:  #7f2138;
        --cream:      #faf7f4;
        --ink:        #241417;
        --muted:      #8a7a7d;
        --line:       #efe6e4;
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
        font-family: "Playfair Display", "Noto Sans Thai", serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--maroon-900);
    }

    .admin-heading p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    /* GRID LAYOUT (Balanced 1.4fr : 1fr) */
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

    /* STATUS BADGES */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-pending { background: #fff5dd; color: #9a6b00; }
    .status-confirmed { background: #edf5ff; color: #4773a6; }
    .status-renting { background: var(--rose-bg); color: var(--rose-text); }
    .status-returned { background: #eef7f7; color: #477f80; }
    .status-completed { background: #eef7ef; color: #4f7e53; }
    .status-cancelled { background: #f5f5f5; color: #888; }

    /* BUTTONS & FORMS */
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
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
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

    .form-control {
        width: 100%;
        height: 42px;
        padding: 0 14px;
        border: 1px solid var(--line);
        border-radius: 10px;
        font-size: 13.5px;
        outline: none;
        background: #fff;
        color: var(--ink);
        transition: .2s;
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

    @media (max-width: 960px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="kyrix-admin-container">

    @php
        $rentalId = $rental->rental_id ?? $rental->id;
        $statusCode = strtolower($rental->status ?? 'pending');
        $statusText = match($statusCode) {
            'pending'   => 'รอการยืนยัน',
            'confirmed' => 'ยืนยันแล้ว',
            'renting'   => 'กำลังเช่า',
            'returned'  => 'คืนชุดแล้ว',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก',
            default     => ucfirst($rental->status)
        };
        $statusClass = match($statusCode) {
            'pending'   => 'status-pending',
            'confirmed' => 'status-confirmed',
            'renting'   => 'status-renting',
            'returned'  => 'status-returned',
            'completed' => 'status-completed',
            'cancelled' => 'status-cancelled',
            default     => 'status-default'
        };
    @endphp

    <!-- HEADER -->
    <div class="admin-header">
        <div class="admin-heading">
            <span class="eyebrow">KYRIX RENTAL · BOOKING DETAIL</span>
            <div style="display: flex; align-items: center; gap: 14px; margin-top: 4px;">
                <h1>รายละเอียดการเช่า #{{ $rental->booking_code ?? $rentalId }}</h1>
                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
            </div>
            <p>ตรวจสอบข้อมูลรายการเช่า ข้อมูลลูกค้า และอัปเดตสถานะการดำเนินงานคำสั่งซื้อ</p>
        </div>
        <div>
            <a href="{{ route('owner.bookings.index') }}" class="admin-btn secondary">
                <i class="fa-solid fa-arrow-left"></i> กลับหน้าจัดการรายการเช่า
            </a>
        </div>
    </div>

    <!-- 2-COLUMN BALANCED LAYOUT -->
    <div class="detail-grid">
        
        <!-- LEFT COLUMN -->
        <div>
            <!-- รายการชุดที่เช่า -->
            <div class="detail-card">
                <div class="card-title">
                    <i class="fa-solid fa-shirt"></i> รายการชุดในคำขอเช่านี้
                </div>
                <div style="overflow-x: auto;">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">รูปภาพ</th>
                                <th style="width: 55%;">ชื่อชุด / รหัส</th>
                                <th style="width: 15%; text-align: center;">จำนวน</th>
                                <th style="width: 15%; text-align: right;">ราคา</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rental->details ?? [] as $detail)
                            @php
                                $product = $detail->product ?? null;
                                $img = null;
                                if ($product && $product->images && $product->images->count() > 0) {
                                    $img = $product->images->first()->image_path ?? $product->images->first()->url ?? null;
                                } elseif ($product && !empty($product->image)) {
                                    $img = $product->image;
                                }
                            @endphp
                            <tr>
                                <td>
                                    @if($img)
                                        <img src="{{ Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/' . ltrim($img, '/')) }}" class="product-thumb">
                                    @else
                                        <div class="product-thumb" style="display: flex; align-items: center; justify-content: center; color: var(--rose-text); background: var(--rose-bg);">
                                            <i class="fa-solid fa-shirt"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--maroon-900);">{{ $product->product_name ?? 'ชุดสินค้า' }}</div>
                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">รหัส: {{ $product->product_code ?? '-' }}</div>
                                </td>
                                <td style="text-align: center; font-weight: 600;">{{ $detail->quantity ?? 1 }}</td>
                                <td style="text-align: right; font-weight: 750; color: var(--maroon-900);">฿{{ number_format($detail->price ?? $product->rental_price ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="padding: 30px; text-align: center; color: var(--muted);">ไม่มีรายการชุดในคำขอนี้</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ประวัติการชำระเงิน -->
            <div class="detail-card">
                <div class="card-title">
                    <i class="fa-solid fa-receipt"></i> ข้อมูลการชำระเงิน
                </div>
                @forelse($rental->payments ?? [] as $payment)
                <div class="info-row">
                    <span class="info-label">ยอดแจ้งโอนชำระ</span>
                    <span class="info-value" style="font-size: 15px; color: var(--maroon-900);">฿{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">สถานะการชำระ</span>
                    <span class="info-value">
                        <span style="color: #4f7e53; font-weight: 700;">{{ ucfirst($payment->status) }}</span>
                    </span>
                </div>
                @empty
                <div style="color: var(--muted); font-size: 13px; text-align: center; padding: 15px 0;">ยังไม่มีประวัติการแจ้งชำระเงินในระบบ</div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div>
            <!-- ข้อมูลลูกค้า -->
            <div class="detail-card">
                <div class="card-title">
                    <i class="fa-solid fa-user-tag"></i> ข้อมูลลูกค้า
                </div>
                @php $customer = $rental->customer ?? null; @endphp
                <div class="info-row">
                    <span class="info-label">ชื่อ-นามสกุล</span>
                    <span class="info-value">{{ $customer->name ?? $customer->customer_name ?? 'ไม่ระบุ' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">เบอร์โทรศัพท์</span>
                    <span class="info-value">{{ $customer->phone ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">อีเมลติดต่อ</span>
                    <span class="info-value" style="font-size: 12px;">{{ $customer->email ?? '-' }}</span>
                </div>
            </div>

            <!-- กำหนดการเช่า & อัปเดตสถานะ -->
            <div class="detail-card">
                <div class="card-title">
                    <i class="fa-solid fa-calendar-days"></i> กำหนดการ & จัดการสถานะ
                </div>
                <div class="info-row">
                    <span class="info-label">วันรับชุด</span>
                    <span class="info-value">{{ !empty($rental->start_date) ? \Carbon\Carbon::parse($rental->start_date)->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">วันคืนชุด</span>
                    <span class="info-value">{{ !empty($rental->end_date) ? \Carbon\Carbon::parse($rental->end_date)->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="info-row" style="margin-top: 6px; padding-top: 12px; border-top: 1px solid var(--line);">
                    <span class="info-label" style="font-weight: 750; color: var(--maroon-900);">ยอดรวมทั้งสิ้น</span>
                    <span class="info-value" style="font-size: 18px; font-weight: 800; color: var(--maroon-900);">฿{{ number_format($rental->total_amount ?? 0, 2) }}</span>
                </div>

                <!-- ฟอร์มเปลี่ยนสถานะ -->
                <div style="margin-top: 22px; padding-top: 20px; border-top: 1px solid var(--line);">
                    <form action="{{ route('owner.bookings.updateStatus', $rentalId) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">อัปเดตสถานะการเช่า</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $statusCode == 'pending' ? 'selected' : '' }}>รอการยืนยัน</option>
                                <option value="confirmed" {{ $statusCode == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว</option>
                                <option value="renting" {{ $statusCode == 'renting' ? 'selected' : '' }}>กำลังเช่า</option>
                                <option value="returned" {{ $statusCode == 'returned' ? 'selected' : '' }}>คืนชุดแล้ว</option>
                                <option value="cancelled" {{ $statusCode == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">หมายเหตุ / Tracking Number</label>
                            <input type="text" name="tracking_number" value="{{ $rental->tracking_number ?? '' }}" class="form-control" placeholder="กรอกเลขพัสดุขนส่ง (ถ้ามี)">
                        </div>
                        <button type="submit" class="admin-btn primary" style="width: 100%; margin-top: 6px;">
                            <i class="fa-solid fa-floppy-disk"></i> บันทึกสถานะคำสั่งซื้อ
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection