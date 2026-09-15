@extends('layouts.owner')

@section('title', 'รับคืนชุด | KYRIX Admin')

@push('styles')
<!-- ใช้ฟอนต์ Noto Sans Thai ทั้งหน้า -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- โหลด SweetAlert2 สำหรับแจ้งเตือน -->
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
        font-family: 'Prompt', sans-serif !important;
    }

    .kyrix-admin-container {
        padding: 0;
        color: var(--ink);
        width: 100%;
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

    /* STATS CARDS (INTERACTIVE & CLICKABLE) */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
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

    .stat-card.active { border-left: 4px solid #3b82f6; }
    .stat-card.overdue { border-left: 4px solid #dc2626; }
    .stat-card.returned { border-left: 4px solid #16a34a; }

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

    /* TABS */
    .tabs-wrap {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--line);
        padding-bottom: 14px;
    }

    .tab-btn {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 650;
        text-decoration: none;
        transition: .2s ease;
        border: 1px solid var(--line);
        background: #fff;
        color: var(--muted);
    }

    .tab-btn:hover {
        background: var(--cream);
        color: var(--maroon-800);
    }

    .tab-btn.active-tab {
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(111, 26, 43, .2);
    }

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
        min-width: 920px;
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

    .cell-truncate {
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dress-thumb-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dress-thumb-row + .dress-thumb-row {
        margin-top: 4px;
    }

    .kyrix-table tr:hover {
        background-color: #fdfbfb;
    }

    /* แถวที่เกินกำหนดคืน ให้เด่นขึ้นเล็กน้อย */
    .kyrix-table tr.row-overdue {
        background-color: rgba(220, 38, 38, 0.035);
    }

    .kyrix-table tr.row-overdue:hover {
        background-color: rgba(220, 38, 38, 0.06);
    }

    /* STATUS BADGES */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-active { background: #edf5ff; color: #2563eb; }
    .status-overdue { background: #fdf2f2; color: #dc2626; }
    .status-returned { background: #eef7ef; color: #16a34a; }
    .status-default { background: #f5f5f5; color: #666; }

    /* คำเตือนวันที่เกินกำหนดใต้วันครบกำหนดคืน */
    .overdue-hint {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        margin-top: 5px;
        font-size: 10.5px;
        font-weight: 700;
        color: #dc2626;
    }

    /* ACTION BUTTONS */
    .action-stack {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .btn-return {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 650;
        background: #fdf8ef;
        color: var(--gold-dark);
        border: 1px solid #f3e6d0;
        cursor: pointer;
        transition: .2s;
        text-decoration: none;
        width: 100%;
    }
    .btn-return:hover {
        background: var(--gold);
        color: #fff;
    }

    /* ปุ่มบันทึกรับคืน เมื่อเกินกำหนด ให้เด่นขึ้นเป็นสีแดง/เร่งด่วน */
    .btn-return.is-urgent {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
        box-shadow: 0 4px 12px rgba(220, 38, 38, .25);
    }
    .btn-return.is-urgent:hover {
        background: #b91c1c;
    }

    .btn-call {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 650;
        background: #fff;
        color: var(--maroon-800);
        border: 1px solid var(--line);
        cursor: pointer;
        transition: .2s;
        text-decoration: none;
        width: 100%;
    }
    .btn-call:hover {
        background: var(--rose-bg);
        border-color: var(--rose-text);
        color: var(--rose-text);
    }

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

    @php $currentTab = $tab ?? request('tab', 'active'); @endphp

    <!-- HEADER -->
    <div class="admin-header">
        <div class="admin-heading">
            <span class="eyebrow">KYRIX RENTAL · RETURNS</span>
            <h1>จัดการรับคืนชุด</h1>
            <p>คลิกการ์ดสถิติด้านบนเพื่อตรวจสอบข้อมูลจริง หรือบันทึกรับคืนชุดเข้าคลัง</p>
        </div>
    </div>

    <!-- STATS CARDS (CLICKABLE & INTERACTIVE) -->
    @if(isset($counts))
    <div class="stats-grid">
        <a href="{{ route('owner.returns.index', ['tab' => 'active']) }}" class="stat-card active {{ $currentTab == 'active' ? 'active-filter' : '' }}">
            <div class="stat-label">กำลังเช่า / รอคืน</div>
            <div class="stat-value">{{ number_format($counts['active'] ?? 0) }} รายการ</div>
        </a>
        <a href="{{ route('owner.returns.index', ['tab' => 'overdue']) }}" class="stat-card overdue {{ $currentTab == 'overdue' ? 'active-filter' : '' }}">
            <div class="stat-label">เกินกำหนดคืน</div>
            <div class="stat-value" style="color: #dc2626;">{{ number_format($counts['overdue'] ?? 0) }} รายการ</div>
        </a>
        <a href="{{ route('owner.returns.index', ['tab' => 'returned']) }}" class="stat-card returned {{ $currentTab == 'returned' ? 'active-filter' : '' }}">
            <div class="stat-label">คืนเรียบร้อยแล้ว</div>
            <div class="stat-value">{{ number_format($counts['returned'] ?? 0) }} รายการ</div>
        </a>
    </div>
    @endif

    <!-- TABS -->
    <div class="tabs-wrap">
        <a href="{{ route('owner.returns.index', ['tab' => 'active']) }}" class="tab-btn {{ $currentTab == 'active' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-clock-rotate-left mr-1"></i> กำลังเช่าอยู่
        </a>
        <a href="{{ route('owner.returns.index', ['tab' => 'overdue']) }}" class="tab-btn {{ $currentTab == 'overdue' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> เกินกำหนด
        </a>
        <a href="{{ route('owner.returns.index', ['tab' => 'returned']) }}" class="tab-btn {{ $currentTab == 'returned' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-circle-check mr-1"></i> คืนเรียบร้อยแล้ว
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="content-card">
        <div class="table-wrapper">
            <table class="kyrix-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">เลขที่เช่า</th>
                        <th style="width: 22%;">ลูกค้า</th>
                        <th style="width: 22%;">ชุดที่เช่า</th>
                        <th style="width: 16%; text-align: center;">กำหนดคืน</th>
                        <th style="width: 14%; text-align: center;">สถานะ</th>
                        <th style="width: 14%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $rental)
                    @php
                        $rentalId = $rental->rental_id ?? $rental->id;
                        $status = strtolower($rental->display_status ?? $rental->status ?? 'renting');

                        // ---- ข้อมูลลูกค้า: join กับตาราง customers (first_name + last_name) ----
                        $customer = $rental->customer;
                        $customerName = 'ไม่ระบุชื่อ';

                        if ($customer) {
                            $customerName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
                            $customerName = $customerName !== '' ? $customerName : 'ไม่ระบุชื่อ';
                        }

                        // เบอร์ติดต่อ: ใช้เบอร์ผู้รับของออเดอร์นี้ก่อน ถ้าไม่มีค่อย fallback ไปเบอร์ลูกค้า
                        $contactPhone = $rental->recipient_phone ?: ($customer->phone ?? null);

                        // ---- ชุดที่เช่า: join rentals -> rental_details -> products ----
                        $rentalItems = $rental->rentalDetails ?? collect();

                        // ---- จำนวนวันที่เกินกำหนด (ใช้กับสถานะ overdue เท่านั้น) ----
                        $daysLate = null;
                        if ($status === 'overdue' && $rental->end_date) {
                            $daysLate = \Carbon\Carbon::parse($rental->end_date)->diffInDays(\Carbon\Carbon::today());
                        }

                        $statusText = match($status) {
                            'renting', 'active' => 'กำลังเช่า',
                            'overdue' => $daysLate ? 'เกินกำหนด ' . $daysLate . ' วัน' : 'เกินกำหนด',
                            'returned' => 'คืนแล้ว',
                            default => ucfirst($rental->status)
                        };
                        $statusClass = match($status) {
                            'renting', 'active' => 'status-active',
                            'overdue' => 'status-overdue',
                            'returned' => 'status-returned',
                            default => 'status-default'
                        };
                    @endphp
                    <tr class="{{ $status === 'overdue' ? 'row-overdue' : '' }}">
                        <td>
                            <strong style="color: var(--maroon-900);">#RENT-{{ $rentalId }}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 650; color: #2d1e21;">
                                {{ $customerName }}
                            </div>
                            @if(!empty($contactPhone))
                                <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                    <i class="fa-solid fa-phone mr-1"></i> {{ $contactPhone }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @forelse($rentalItems as $detail)
                                <div class="dress-thumb-row">
                                    <span class="cell-truncate" title="{{ $detail->product->product_name ?? 'ไม่พบข้อมูลชุด' }}" style="font-weight: 600; color: #2d1e21;">
                                        {{ $detail->product->product_name ?? 'ไม่พบข้อมูลชุด' }}
                                    </span>
                                </div>
                                @if($detail->selected_size || $detail->selected_color)
                                    <div style="font-size: 11px; color: var(--muted);">
                                        {{ $detail->selected_size }}{{ $detail->selected_size && $detail->selected_color ? ' · ' : '' }}{{ $detail->selected_color }}
                                        @if($detail->quantity > 1) · x{{ $detail->quantity }} @endif
                                    </div>
                                @endif
                            @empty
                                <span style="color: var(--muted); font-size: 12px;">-</span>
                            @endforelse
                        </td>
                        <td style="text-align: center; font-weight: 600; color: #444;">
                            {{ $rental->end_date ? \Carbon\Carbon::parse($rental->end_date)->format('d/m/Y') : '-' }}
                            @if($status === 'overdue' && $daysLate !== null)
                                <div class="overdue-hint">
                                    <i class="fa-solid fa-triangle-exclamation"></i> เลยกำหนด {{ $daysLate }} วัน
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $statusClass }}">
                                @if($status === 'overdue')
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                @endif
                                {{ $statusText }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($status != 'returned')
                                <div class="action-stack">
                                    <form action="{{ route('owner.returns.confirm', $rentalId) }}" method="POST" style="margin: 0; width: 100%;" onsubmit="return confirm('ยืนยันการบันทึกรับคืนชุดสำหรับรายการ #RENT-{{ $rentalId }} ใช่หรือไม่?')">
                                        @csrf
                                        <button type="submit" class="btn-return {{ $status === 'overdue' ? 'is-urgent' : '' }}">
                                            <i class="fa-solid fa-box-archive"></i> บันทึกรับคืน
                                        </button>
                                    </form>

                                    @if($status === 'overdue' && !empty($contactPhone))
                                        <a href="tel:{{ $contactPhone }}" class="btn-call">
                                            <i class="fa-solid fa-phone"></i> โทรหาลูกค้า
                                        </a>
                                    @endif
                                </div>
                            @else
                                <span style="font-size: 12px; color: var(--muted); font-weight: 600;">รับคืนสำเร็จแล้ว</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-rotate-left"></i></div>
                                <div style="font-weight: 600; font-size: 14px; color: var(--maroon-900);">ไม่พบรายการในหมวดหมู่นี้</div>
                                <div style="font-size: 12px; margin-top: 4px;">เมื่อมีรายการเช่าที่ตรงกับสถานะดังกล่าว ข้อมูลจะแสดงผลที่นี่</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if(method_exists($rentals, 'hasPages') && $rentals->hasPages())
            <div style="padding: 16px 20px; background: var(--cream); border-top: 1px solid var(--line);">
                {{ $rentals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection