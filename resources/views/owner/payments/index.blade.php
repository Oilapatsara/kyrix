@extends('layouts.owner')

@section('title', 'ตรวจสอบการชำระเงิน | KYRIX Admin')

@push('styles')
<!-- ใช้ฟอนต์ Noto Sans Thai ทั้งหน้า -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- โหลด SweetAlert2 สำหรับ Lightbox ดูสลิป -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    body, h1, h2, h3, h4, h5, h6, p, span, a, button, input, table, div {
        font-family: 'Noto Sans Thai', sans-serif !important;
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
        margin-bottom: 24px;
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
        font-size: 28px;
        font-weight: 700;
        color: var(--maroon-900);
    }

    .admin-heading p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    /* STATS CARDS (INTERACTIVE) */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 3px 15px rgba(111, 26, 43, .03);
        text-decoration: none;
        display: block;
        transition: all 0.2s ease;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(111, 26, 43, .08);
        border-color: var(--gold);
    }

    .stat-card.pending { border-left: 4px solid #d97706; }
    .stat-card.approved { border-left: 4px solid #16a34a; }
    .stat-card.rejected { border-left: 4px solid #dc2626; }

    /* Active state when clicked */
    .stat-card.active-filter {
        background: var(--cream);
        border-color: var(--gold);
        box-shadow: 0 0 0 2px rgba(199, 154, 92, 0.3);
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
    }

    .stat-value {
        font-size: 22px;
        font-weight: 750;
        color: var(--maroon-900);
        margin-top: 6px;
    }

    .stat-hint {
        font-size: 11px;
        color: var(--gold-dark);
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
    }

    /* FILTER BAR */
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--cream);
        padding: 12px 18px;
        border-radius: 12px;
        border: 1px solid var(--line);
        margin-bottom: 20px;
        font-size: 13px;
    }

    .filter-reset {
        color: var(--maroon-800);
        font-weight: 700;
        text-decoration: underline;
        transition: color .2s;
    }
    .filter-reset:hover { color: var(--maroon-900); }

    /* CONTENT CARD & TABLE */
    .content-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 3px 18px rgba(111, 26, 43, .04);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .kyrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .kyrix-table th {
        text-align: left;
        padding: 14px 18px;
        background: var(--cream);
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--line);
    }

    .kyrix-table td {
        padding: 14px 18px;
        border-top: 1px solid var(--line);
        color: #3a2b2e;
        font-size: 13px;
        vertical-align: middle;
    }

    .kyrix-table tr:hover {
        background-color: #fdfbfb;
    }

    /* SLIP THUMBNAIL */
    .slip-thumb-wrap {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: transform .2s ease;
    }
    .slip-thumb-wrap:hover { transform: scale(1.03); }

    .slip-thumb {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--gold);
        background: var(--cream);
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .slip-placeholder {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: var(--rose-bg);
        color: var(--rose-text);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 1px solid var(--line);
    }

    /* BADGES */
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
    .status-approved { background: #eef7ef; color: #4f7e53; }
    .status-rejected { background: #fdf2f2; color: #991b1b; }

    /* ACTION BUTTONS */
    .action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-approve {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 650;
        background: #eef7ef;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
        cursor: pointer;
        transition: .2s;
    }
    .btn-approve:hover { background: #d0e8d1; }

    .btn-reject {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 650;
        background: #fdf2f2;
        color: #c62828;
        border: 1px solid #ffcdd2;
        cursor: pointer;
        transition: .2s;
    }
    .btn-reject:hover { background: #ffcdd2; }

    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: var(--muted);
    }
    .empty-icon {
        font-size: 36px;
        color: var(--rose-text);
        opacity: .6;
        margin-bottom: 12px;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="kyrix-admin-container">

    <!-- HEADER -->
    <div class="admin-header">
        <div class="admin-heading">
            <span class="eyebrow">KYRIX RENTAL · PAYMENTS</span>
            <h1>ตรวจสอบการชำระเงิน / สลิป</h1>
            <p>คลิกการ์ดด้านบนเพื่อกรองข้อมูลตามสถานะ หรือคลิกรูปสลิปเพื่อตรวจสอบหลักฐานการโอน</p>
        </div>
    </div>

    <!-- STATS CARDS (CLICKABLE) -->
    @if(isset($counts))
    <div class="stats-grid">
        <a href="{{ route('owner.payments.index', ['status' => 'pending']) }}" class="stat-card pending {{ request('status') == 'pending' ? 'active-filter' : '' }}">
            <div class="stat-label">รอตรวจสอบ</div>
            <div class="stat-value">{{ number_format($counts['pending'] ?? 0) }} รายการ</div>
        </a>
        <a href="{{ route('owner.payments.index', ['status' => 'approved']) }}" class="stat-card approved {{ request('status') == 'approved' ? 'active-filter' : '' }}">
            <div class="stat-label">อนุมัติแล้ว</div>
            <div class="stat-value">{{ number_format($counts['approved'] ?? 0) }} รายการ</div>
        </a>
        <a href="{{ route('owner.payments.index', ['status' => 'rejected']) }}" class="stat-card rejected {{ request('status') == 'rejected' ? 'active-filter' : '' }}">
            <div class="stat-label">ปฏิเสธ / ไม่ถูกต้อง</div>
            <div class="stat-value">{{ number_format($counts['rejected'] ?? 0) }} รายการ</div>
        </a>
    </div>

    @if(request('status'))
    <div class="filter-bar">
        <span>กำลังแสดงผลข้อมูลเฉพาะสถานะ: <strong style="color: var(--maroon-900);">{{ request('status') == 'pending' ? 'รอตรวจสอบ' : (request('status') == 'approved' ? 'อนุมัติแล้ว' : 'ปฏิเสธ') }}</strong></span>
        <a href="{{ route('owner.payments.index') }}" class="filter-reset">แสดงทั้งหมด</a>
    </div>
    @endif
    @endif

    <!-- TABLE CARD -->
    <div class="content-card">
        <div class="table-wrapper">
            <table class="kyrix-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">รหัสการชำระเงิน</th>
                        <th style="width: 25%;">ลูกค้า / เลขอ้างอิงเช่า</th>
                        <th style="width: 18%; text-align: center;">หลักฐานสลิปโอนเงิน</th>
                        <th style="width: 12%; text-align: center;">จำนวนเงิน</th>
                        <th style="width: 12%; text-align: center;">สถานะ</th>
                        <th style="width: 18%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    @php
                        $status = strtolower($payment->status ?? 'pending');
                        $statusText = match($status) {
                            'pending'  => 'รอตรวจสอบ',
                            'approved' => 'อนุมัติแล้ว',
                            'rejected' => 'ปฏิเสธ',
                            default    => ucfirst($payment->status)
                        };
                        $statusClass = match($status) {
                            'pending'  => 'status-pending',
                            'approved' => 'status-approved',
                            'rejected' => 'status-rejected',
                            default    => 'status-pending'
                        };
                        $paymentId = $payment->payment_id ?? $payment->id;

                        // ดึงรูปสลิป
                        $slipImage = $payment->slip_path ?? $payment->slip_image ?? $payment->image ?? $payment->proof ?? null;
                        $slipUrl = $slipImage ? (Str::startsWith($slipImage, ['http://', 'https://']) ? $slipImage : asset('storage/' . ltrim($slipImage, '/'))) : null;

                        $customerName = $payment->customer->name ?? $payment->rental->customer->name ?? 'ไม่ระบุชื่อ';
                        $amountFormatted = number_format($payment->amount ?? 0, 2);
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: var(--maroon-900);">#PAY-{{ $paymentId }}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 650; color: #2d1e21;">{{ $customerName }}</div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">เลขอ้างอิงเช่า: #{{ $payment->rental_id ?? '-' }}</div>
                        </td>
                        <td style="text-align: center;">
                            @if($slipUrl)
                                <div class="slip-thumb-wrap" onclick="showSlipModal('{{ $slipUrl }}', 'PAY-{{ $paymentId }}', '{{ $customerName }}', '{{ $amountFormatted }}')">
                                    <img src="{{ $slipUrl }}" alt="Slip" class="slip-thumb">
                                    <span style="font-size: 11.5px; font-weight: 650; color: var(--maroon-800); text-decoration: underline;">ดูสลิป</span>
                                </div>
                            @else
                                <div style="display: inline-flex; align-items: center; gap: 6px; color: var(--muted); font-size: 12px;">
                                    <div class="slip-placeholder"><i class="fa-solid fa-image-slash"></i></div>
                                    <span>ไม่มีสลิป</span>
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: 750; color: var(--maroon-900);">
                            ฿{{ $amountFormatted }}
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($status == 'pending')
                                <div class="action-group">
                                    <form action="{{ route('owner.payments.approve', $paymentId) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-approve" onclick="return confirm('ยืนยันการอนุมัติยอดเงินนี้ใช่หรือไม่?')" title="อนุมัติ">
                                            <i class="fa-solid fa-check"></i> อนุมัติ
                                        </button>
                                    </form>
                                    @if(Route::has('owner.payments.reject'))
                                    <form action="{{ route('owner.payments.reject', $paymentId) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-reject" onclick="return confirm('ยืนยันการปฏิเสธรายการนี้ใช่หรือไม่?')" title="ปฏิเสธ">
                                            <i class="fa-solid fa-xmark"></i> ปฏิเสธ
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @else
                                <span style="font-size: 12px; color: var(--muted); font-weight: 600;">ตรวจสอบแล้ว</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-receipt"></i></div>
                                <div style="font-weight: 600; font-size: 14px; color: var(--maroon-900);">ไม่พบรายการชำระเงินในสถานะนี้</div>
                                <div style="font-size: 12px; margin-top: 4px;">ลองคลิก "แสดงทั้งหมด" เพื่อดูรายการชำระเงินทั้งหมดในระบบ</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if(method_exists($payments, 'hasPages') && $payments->hasPages())
            <div style="padding: 16px 20px; background: var(--cream); border-top: 1px solid var(--line);">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- SLIP LIGHTBOX MODAL -->
<script>
    function showSlipModal(imageUrl, payCode, customerName, amount) {
        Swal.fire({
            title: `<span style="color: #430d17; font-size: 18px; font-weight: 700;">หลักฐานการโอนเงิน (${payCode})</span>`,
            html: `
                <div style="margin-bottom: 12px; font-size: 13px; color: #440e18; background: #f7e7ea; padding: 8px 12px; border-radius: 8px;">
                    ลูกค้า: <strong>${customerName}</strong> | ยอดโอน: <strong style="color: #6f1a2b;">฿${amount}</strong>
                </div>
                <div style="background: #faf7f4; padding: 12px; border-radius: 12px; border: 1px solid #efe6e4; max-height: 500px; overflow-y: auto; text-align: center;">
                    <img src="${imageUrl}" alt="Payment Slip" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                </div>
            `,
            width: '620px',
            showCloseButton: true,
            showConfirmButton: false,
            focusConfirm: false
        });
    }
</script>
@endsection