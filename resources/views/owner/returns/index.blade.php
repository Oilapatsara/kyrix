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

    /* MODAL STYLES */
    .kyrix-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(36, 20, 23, 0.65);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .kyrix-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .kyrix-modal-card {
        background: #fff;
        border-radius: 18px;
        width: 100%;
        max-width: 580px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(67, 13, 23, 0.25);
        border: 1px solid var(--line);
        transform: translateY(15px);
        transition: transform 0.25s ease;
    }
    .kyrix-modal-overlay.active .kyrix-modal-card {
        transform: translateY(0);
    }
    .modal-header-kyrix {
        padding: 20px 24px;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--cream);
    }
    .modal-header-kyrix h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 750;
        color: var(--maroon-900);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-close-btn {
        background: transparent;
        border: none;
        font-size: 18px;
        color: var(--muted);
        cursor: pointer;
        padding: 4px;
        line-height: 1;
        transition: .2s;
    }
    .modal-close-btn:hover {
        color: var(--maroon-900);
    }
    .modal-body-kyrix {
        padding: 24px;
    }
    .order-summary-box {
        background: #fdfbf9;
        border: 1px solid #ebdcd5;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        font-size: 13px;
    }
    .order-summary-box .row-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .order-summary-box .row-info:last-child {
        margin-bottom: 0;
    }
    
    /* CONDITION SELECTION CARDS */
    .condition-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 18px;
    }
    .condition-card {
        border: 2px solid var(--line);
        border-radius: 14px;
        padding: 16px;
        cursor: pointer;
        transition: all .2s ease;
        text-align: center;
        position: relative;
    }
    .condition-card:hover {
        border-color: var(--gold);
        background: #faf8f6;
    }
    .condition-card.selected-good {
        border-color: #16a34a;
        background: #f0fdf4;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.12);
    }
    .condition-card.selected-damaged {
        border-color: #dc2626;
        background: #fef2f2;
        box-shadow: 0 4px 14px rgba(220, 38, 38, 0.12);
    }
    .condition-card input[type="radio"] {
        display: none;
    }
    .condition-icon {
        font-size: 26px;
        margin-bottom: 8px;
    }
    .condition-title {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 4px;
    }
    .condition-desc {
        font-size: 11.5px;
        line-height: 1.4;
    }
    .desc-good { color: #166534; font-weight: 600; }
    .desc-damaged { color: #991b1b; font-weight: 600; }

    .condition-detail-box {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid var(--line);
        margin-bottom: 16px;
    }
    .form-group-k {
        margin-bottom: 14px;
    }
    .form-group-k:last-child {
        margin-bottom: 0;
    }
    .form-group-k label {
        display: block;
        font-size: 12.5px;
        font-weight: 650;
        color: var(--maroon-900);
        margin-bottom: 6px;
    }
    .form-input-k {
        width: 100%;
        padding: 9px 14px;
        border: 1px solid var(--line);
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        transition: .2s;
        box-sizing: border-box;
        font-family: inherit;
    }
    .form-input-k:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(199, 154, 92, 0.15);
    }
    .modal-footer-kyrix {
        padding: 16px 24px;
        border-top: 1px solid var(--line);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: var(--cream);
    }
    .btn-modal-cancel {
        padding: 8px 18px;
        background: #fff;
        border: 1px solid var(--line);
        color: var(--ink);
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
    }
    .btn-modal-submit {
        padding: 8px 22px;
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        border: none;
        color: #fff;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 650;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(111, 26, 43, 0.2);
    }
    .btn-modal-submit:hover {
        background: var(--maroon-900);
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .condition-grid { grid-template-columns: 1fr; }
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
                        $rawStatus = strtolower($rental->status ?? 'renting');
                        $status = $currentTab === 'overdue' ? 'overdue' : $rawStatus;

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
                            'confirmed' => 'ยืนยันแล้ว',
                            'renting', 'active' => 'กำลังเช่า',
                            'pending_return' => 'ลูกค้าแจ้งคืนแล้ว',
                            'overdue' => $daysLate ? 'เกินกำหนด ' . $daysLate . ' วัน' : 'เกินกำหนด',
                            'returned', 'completed' => 'คืนแล้ว',
                            default => ucfirst($rental->status)
                        };
                        $statusClass = match($status) {
                            'confirmed', 'renting', 'active' => 'status-active',
                            'pending_return' => 'status-overdue',
                            'overdue' => 'status-overdue',
                            'returned', 'completed' => 'status-returned',
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
                            @php
                                $itemNames = [];
                                foreach($rentalItems as $d) {
                                    if ($d->product) {
                                        $itemNames[] = $d->product->product_name . ($d->selected_size ? ' ('.$d->selected_size.')' : '');
                                    }
                                }
                                $itemSummary = implode(', ', $itemNames);
                            @endphp

                            @if(!in_array($rawStatus, ['returned', 'completed']))
                                <div class="action-stack">
                                    <button type="button" class="btn-return {{ $status === 'overdue' ? 'is-urgent' : '' }}" 
                                        onclick="openInspectionModal('{{ $rentalId }}', '{{ $rental->formatted_code ?? ('KR-' . $rentalId) }}', '{{ addslashes($customerName) }}', '{{ $contactPhone }}', '100.00', '{{ addslashes($itemSummary) }}')">
                                        <i class="fa-solid fa-clipboard-check"></i> ตรวจรับคืน & จัดการมัดจำ
                                    </button>

                                    @if($status === 'overdue' && !empty($contactPhone))
                                        <a href="tel:{{ $contactPhone }}" class="btn-call">
                                            <i class="fa-solid fa-phone"></i> โทรหาลูกค้า
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                    @if($rental->condition_status === 'good')
                                        <span class="status-badge" style="background: #eef7ef; color: #16a34a; font-size: 11px;">
                                            <i class="fa-solid fa-circle-check"></i> ชุดสมบูรณ์
                                        </span>
                                        <span style="font-size: 11px; color: #16a34a; font-weight: 700;">
                                            คืนมัดจำแล้ว ฿100.00
                                        </span>
                                        @if($rental->refund_slip)
                                            <a href="{{ $rental->refund_slip_url }}" target="_blank" style="font-size: 11px; color: var(--gold-dark); text-decoration: underline; margin-top: 2px;">
                                                <i class="fa-solid fa-receipt"></i> ดูสลิปคืนเงิน
                                            </a>
                                        @endif
                                    @elseif($rental->condition_status === 'damaged')
                                        <span class="status-badge" style="background: #fef2f2; color: #dc2626; font-size: 11px;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> ชุดชำรุดเสียหาย
                                        </span>
                                        <span style="font-size: 11px; color: #dc2626; font-weight: 700;">
                                            ยึดมัดจำ ฿100.00 (ไม่คืนเงิน)
                                        </span>
                                        @if($rental->damage_note)
                                            <span style="font-size: 10.5px; color: #888; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $rental->damage_note }}">
                                                เหตุ: {{ $rental->damage_note }}
                                            </span>
                                        @endif
                                        @if($rental->damage_image)
                                            <a href="{{ $rental->damage_image_url }}" target="_blank" style="font-size: 11px; color: #dc2626; text-decoration: underline; margin-top: 2px;">
                                                <i class="fa-solid fa-image"></i> ดูรูปความเสียหาย
                                            </a>
                                        @endif
                                    @else
                                        <span class="status-badge status-returned">
                                            <i class="fa-solid fa-check"></i> รับคืนแล้ว
                                        </span>
                                    @endif
                                </div>
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

<!-- INSPECTION & DEPOSIT MODAL -->
<div class="kyrix-modal-overlay" id="inspectionModal" onclick="closeInspectionModal(event)">
    <div class="kyrix-modal-card" onclick="event.stopPropagation()">
        <form id="inspectionForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header-kyrix">
                <h3><i class="fa-solid fa-clipboard-check" style="color: var(--gold);"></i> ตรวจสภาพชุด & จัดการเงินมัดจำ</h3>
                <button type="button" class="modal-close-btn" onclick="closeInspectionModal()">&times;</button>
            </div>
            <div class="modal-body-kyrix">
                <!-- Order Summary -->
                <div class="order-summary-box">
                    <div class="row-info">
                        <span style="color: var(--muted);">รหัสการเช่า:</span>
                        <strong id="modalRentalCode" style="color: var(--maroon-900);">#KR-0000</strong>
                    </div>
                    <div class="row-info">
                        <span style="color: var(--muted);">ลูกค้า:</span>
                        <span id="modalCustomerName" style="font-weight: 600;">-</span>
                    </div>
                    <div class="row-info">
                        <span style="color: var(--muted);">ชุดที่เช่า:</span>
                        <span id="modalItemSummary" style="font-weight: 600; color: #444;">-</span>
                    </div>
                    <div class="row-info" style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2d8d5;">
                        <span style="color: var(--maroon-900); font-weight: 700;">เงินมัดจำประกันชุด:</span>
                        <strong id="modalDepositAmount" style="font-size: 15px; color: #b45309;">฿100.00</strong>
                    </div>
                </div>

                <!-- Condition Selection Grid -->
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--maroon-900); margin-bottom: 10px;">
                    ผลการตรวจสภาพชุด (เลือกข้อใดข้อหนึ่ง) <span style="color: #dc2626;">*</span>
                </label>
                <div class="condition-grid">
                    <!-- Option 1: Good -->
                    <div class="condition-card selected-good" id="cardGood" onclick="selectCondition('good')">
                        <input type="radio" name="condition_status" value="good" id="radioGood" checked required>
                        <div class="condition-icon" style="color: #16a34a;"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="condition-title" style="color: #166534;">ชุดสมบูรณ์ ไม่เสียหาย</div>
                        <div class="condition-desc desc-good">คืนเงินมัดจำ ฿100 ทันที</div>
                    </div>

                    <!-- Option 2: Damaged -->
                    <div class="condition-card" id="cardDamaged" onclick="selectCondition('damaged')">
                        <input type="radio" name="condition_status" value="damaged" id="radioDamaged">
                        <div class="condition-icon" style="color: #dc2626;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div class="condition-title" style="color: #991b1b;">ชุดชำรุด / มีความเสียหาย</div>
                        <div class="condition-desc desc-damaged">ไม่คืนเงินมัดจำ (ยึดมัดจำ)</div>
                    </div>
                </div>

                <!-- Dynamic Good condition box -->
                <div id="goodDetailBox" class="condition-detail-box" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div style="font-size: 13px; color: #166534; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa-solid fa-hand-holding-dollar"></i> ระบบจะคืนเงินมัดจำเต็มจำนวน ฿<span id="refundAmountText">100.00</span> ให้ลูกค้า
                    </div>
                    <div class="form-group-k">
                        <label for="refund_slip">แนบสลิปหลักฐานโอนคืนเงินมัดจำ (ถ้ามี):</label>
                        <input type="file" name="refund_slip" id="refund_slip" class="form-input-k" accept="image/*">
                        <small style="color: var(--muted); font-size: 11px;">สามารถแนบภาพสลิปโอนคืนเงิน เพื่อให้ลูกค้าตรวจสอบได้ในประวัติการเช่า</small>
                    </div>
                </div>

                <!-- Dynamic Damaged condition box -->
                <div id="damagedDetailBox" class="condition-detail-box" style="display: none; background: #fef2f2; border-color: #fecaca;">
                    <div style="font-size: 13px; color: #991b1b; font-weight: 700; margin-bottom: 10px;">
                        <i class="fa-solid fa-ban"></i> ทางร้านจะยึดเงินมัดจำ ฿<span id="forfeitAmountText">100.00</span> เต็มจำนวน (ไม่คืนเงิน)
                    </div>
                    <div class="form-group-k">
                        <label for="damage_note">ระบุสาเหตุความเสียหาย / ตำหนิที่ตรวจพบ <span style="color: #dc2626;">*</span>:</label>
                        <input type="text" name="damage_note" id="damage_note" class="form-input-k" placeholder="เช่น ซิปแตก, ผ้ามีรอยขาดที่ชายกระโปรง, คราบเปื้อนฝังแน่น">
                    </div>
                    <div class="form-group-k">
                        <label for="damage_image">แนบรูปถ่ายสภาพชุดชำรุดเพื่อเป็นหลักฐาน:</label>
                        <input type="file" name="damage_image" id="damage_image" class="form-input-k" accept="image/*">
                        <small style="color: #991b1b; font-size: 11px;">รูปภาพจะแสดงในหน้ารายละเอียดการเช่าของลูกค้าเพื่อความโปร่งใส</small>
                    </div>
                </div>

                <!-- Common note -->
                <div class="form-group-k" style="margin-top: 14px;">
                    <label for="return_note">บันทึกเพิ่มเติมของทางร้าน (ถ้ามี):</label>
                    <textarea name="return_note" id="return_note" class="form-input-k" rows="2" placeholder="ระบุข้อความบันทึกช่วยจำสำหรับออเดอร์นี้..."></textarea>
                </div>
            </div>
            <div class="modal-footer-kyrix">
                <button type="button" class="btn-modal-cancel" onclick="closeInspectionModal()">ยกเลิก</button>
                <button type="submit" class="btn-modal-submit" id="btnSubmitModal">
                    <i class="fa-solid fa-check"></i> ยืนยันการตรวจรับชุด
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openInspectionModal(rentalId, rentalCode, customerName, contactPhone, depositAmount, itemSummary) {
    const form = document.getElementById('inspectionForm');
    form.action = "{{ url('owner/returns') }}/" + rentalId + "/confirm";
    
    document.getElementById('modalRentalCode').innerText = '#' + rentalCode;
    document.getElementById('modalCustomerName').innerText = customerName + (contactPhone ? ' (' + contactPhone + ')' : '');
    document.getElementById('modalItemSummary').innerText = itemSummary || 'รายการชุดเช่า';
    document.getElementById('modalDepositAmount').innerText = '฿' + depositAmount;
    document.getElementById('refundAmountText').innerText = depositAmount;
    document.getElementById('forfeitAmountText').innerText = depositAmount;
    
    // Reset to good condition by default
    selectCondition('good');
    document.getElementById('damage_note').value = '';
    document.getElementById('return_note').value = '';
    if (document.getElementById('refund_slip')) document.getElementById('refund_slip').value = '';
    if (document.getElementById('damage_image')) document.getElementById('damage_image').value = '';
    
    const modal = document.getElementById('inspectionModal');
    modal.classList.add('active');
}

function closeInspectionModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('inspectionModal').classList.remove('active');
}

function selectCondition(type) {
    const cardGood = document.getElementById('cardGood');
    const cardDamaged = document.getElementById('cardDamaged');
    const radioGood = document.getElementById('radioGood');
    const radioDamaged = document.getElementById('radioDamaged');
    const goodBox = document.getElementById('goodDetailBox');
    const damagedBox = document.getElementById('damagedDetailBox');
    const damageNote = document.getElementById('damage_note');
    const btnSubmit = document.getElementById('btnSubmitModal');
    
    if (type === 'good') {
        cardGood.classList.add('selected-good');
        cardDamaged.classList.remove('selected-damaged');
        radioGood.checked = true;
        goodBox.style.display = 'block';
        damagedBox.style.display = 'none';
        damageNote.required = false;
        btnSubmit.innerHTML = '<i class="fa-solid fa-circle-check"></i> ยืนยันรับคืน & คืนมัดจำ ฿' + document.getElementById('refundAmountText').innerText;
        btnSubmit.style.background = 'linear-gradient(135deg, #15803d, #166534)';
    } else {
        cardDamaged.classList.add('selected-damaged');
        cardGood.classList.remove('selected-good');
        radioDamaged.checked = true;
        goodBox.style.display = 'none';
        damagedBox.style.display = 'block';
        damageNote.required = true;
        btnSubmit.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> ยืนยันรับคืน & ยึดเงินมัดจำ (ชุดเสียหาย)';
        btnSubmit.style.background = 'linear-gradient(135deg, #dc2626, #991b1b)';
    }
}
</script>
@endsection
