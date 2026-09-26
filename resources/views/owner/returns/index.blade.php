@extends('layouts.owner')

@section('title', 'รับคืนชุด | KYRIX Admin')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        }

        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        a,
        button,
        input,
        select,
        textarea,
        table,
        div {
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

        /* STATS */
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
            transition: all .2s ease;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(111, 26, 43, .08);
            border-color: var(--gold);
        }

        .stat-card.active {
            border-left: 4px solid #3b82f6;
        }

        .stat-card.overdue {
            border-left: 4px solid #dc2626;
        }

        .stat-card.returned {
            border-left: 4px solid #16a34a;
        }

        .stat-card.active-filter {
            background: var(--cream);
            border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(199, 154, 92, .3);
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

        /* TABS */
        .tabs-wrap {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 14px;
            flex-wrap: wrap;
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

        /* TABLE */
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
            min-width: 1180px;
        }

        .kyrix-table th {
            text-align: left;
            padding: 14px 18px;
            background: var(--cream);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
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

        .kyrix-table tr.row-overdue {
            background-color: rgba(220, 38, 38, .035);
        }

        .kyrix-table tr.row-overdue:hover {
            background-color: rgba(220, 38, 38, .06);
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

        .dress-thumb-row+.dress-thumb-row {
            margin-top: 4px;
        }

        /* STATUS */
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

        .status-active {
            background: #edf5ff;
            color: #2563eb;
        }

        .status-overdue {
            background: #fdf2f2;
            color: #dc2626;
        }

        .status-returned {
            background: #eef7ef;
            color: #16a34a;
        }

        .status-default {
            background: #f5f5f5;
            color: #666;
        }

        .return-waiting {
            background: #fff7ed;
            color: #9a3412;
        }

        .return-shipping {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .return-ready {
            background: #f0fdf4;
            color: #166534;
        }

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

        /* RETURN INFO */
        .return-info {
            margin-top: 2px;
            padding: 10px;
            background: #faf7f4;
            border: 1px solid var(--line);
            border-radius: 10px;
            line-height: 1.7;
        }

        .return-info-title {
            font-weight: 700;
            color: var(--maroon-900);
            margin-bottom: 4px;
            font-size: 12px;
        }

        .return-info-row {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .return-info-label {
            color: var(--muted);
        }

        .return-info-value {
            color: #3a2b2e;
            font-weight: 600;
            word-break: break-word;
        }

        .return-status-select {
            width: 100%;
            padding: 7px 8px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: #fff;
            font-family: inherit;
            font-size: 11px;
            color: #3a2b2e;
            outline: none;
            margin-top: 7px;
        }

        .return-status-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(199, 154, 92, .12);
        }

        .btn-track-return {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 100%;
            margin-top: 7px;
            padding: 7px 10px;
            border-radius: 7px;
            background: #166534;
            color: #fff;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
        }

        .btn-track-return:hover {
            background: #14532d;
            color: #fff;
        }

        .return-no-link {
            margin-top: 6px;
            font-size: 10px;
            color: var(--muted);
            line-height: 1.5;
        }

        /* ACTION */
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

        .btn-return.is-urgent {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
            box-shadow: 0 4px 12px rgba(220, 38, 38, .25);
        }

        .btn-return.is-urgent:hover {
            background: #b91c1c;
        }

        .btn-return.is-disabled {
            background: #f5f5f5;
            border-color: #ddd;
            color: #999;
            cursor: not-allowed;
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

        /* MODAL */
        .kyrix-modal-overlay {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            background: rgba(36, 20, 23, .65);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity .25s ease;
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
            box-shadow: 0 20px 50px rgba(67, 13, 23, .25);
            border: 1px solid var(--line);
            transform: translateY(15px);
            transition: transform .25s ease;
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
            gap: 15px;
            margin-bottom: 6px;
        }

        .order-summary-box .row-info:last-child {
            margin-bottom: 0;
        }

        /* CONDITION */
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
            box-shadow: 0 4px 14px rgba(22, 163, 74, .12);
        }

        .condition-card.selected-damaged {
            border-color: #dc2626;
            background: #fef2f2;
            box-shadow: 0 4px 14px rgba(220, 38, 38, .12);
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

        .desc-good {
            color: #166534;
            font-weight: 600;
        }

        .desc-damaged {
            color: #991b1b;
            font-weight: 600;
        }

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
            box-shadow: 0 0 0 3px rgba(199, 154, 92, .15);
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
            box-shadow: 0 4px 12px rgba(111, 26, 43, .2);
        }

        .btn-modal-submit:hover {
            background: var(--maroon-900);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .condition-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .kyrix-table {
                min-width: 1080px;
            }

            .kyrix-modal-card {
                max-height: 94vh;
            }

            .modal-body-kyrix {
                padding: 18px;
            }

            .modal-footer-kyrix {
                padding: 14px 18px;
                flex-direction: column-reverse;
            }

            .btn-modal-cancel,
            .btn-modal-submit {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')

    <div class="kyrix-admin-container">

        @php
            $currentTab = $tab ?? request('tab', 'active');
        @endphp

        {{-- HEADER --}}
        <div class="admin-header">
            <div class="admin-heading">
                <span class="eyebrow">KYRIX RENTAL · RETURNS</span>

                <h1>จัดการรับคืนชุด</h1>

                <p>
                    ตรวจสอบข้อมูลการคืนชุดของลูกค้า ติดตามพัสดุ และตรวจรับชุดเมื่อชุดถึงร้าน
                </p>
            </div>
        </div>

        {{-- STATS --}}
        @if (isset($counts))
            <div class="stats-grid">

                <a href="{{ route('owner.returns.index', ['tab' => 'active']) }}"
                    class="stat-card active {{ $currentTab == 'active' ? 'active-filter' : '' }}">

                    <div class="stat-label">
                        กำลังเช่า / รอคืน
                    </div>

                    <div class="stat-value">
                        {{ number_format($counts['active'] ?? 0) }} รายการ
                    </div>

                </a>

                <a href="{{ route('owner.returns.index', ['tab' => 'overdue']) }}"
                    class="stat-card overdue {{ $currentTab == 'overdue' ? 'active-filter' : '' }}">

                    <div class="stat-label">
                        เกินกำหนดคืน
                    </div>

                    <div class="stat-value" style="color:#dc2626;">
                        {{ number_format($counts['overdue'] ?? 0) }} รายการ
                    </div>

                </a>

                <a href="{{ route('owner.returns.index', ['tab' => 'returned']) }}"
                    class="stat-card returned {{ $currentTab == 'returned' ? 'active-filter' : '' }}">

                    <div class="stat-label">
                        คืนเรียบร้อยแล้ว
                    </div>

                    <div class="stat-value">
                        {{ number_format($counts['returned'] ?? 0) }} รายการ
                    </div>

                </a>

            </div>
        @endif

        {{-- TABS --}}
        <div class="tabs-wrap">

            <a href="{{ route('owner.returns.index', ['tab' => 'active']) }}"
                class="tab-btn {{ $currentTab == 'active' ? 'active-tab' : '' }}">

                <i class="fa-solid fa-clock-rotate-left"></i>
                กำลังเช่าอยู่

            </a>

            <a href="{{ route('owner.returns.index', ['tab' => 'overdue']) }}"
                class="tab-btn {{ $currentTab == 'overdue' ? 'active-tab' : '' }}">

                <i class="fa-solid fa-triangle-exclamation"></i>
                เกินกำหนด

            </a>

            <a href="{{ route('owner.returns.index', ['tab' => 'returned']) }}"
                class="tab-btn {{ $currentTab == 'returned' ? 'active-tab' : '' }}">

                <i class="fa-solid fa-circle-check"></i>
                คืนเรียบร้อยแล้ว

            </a>

        </div>

        {{-- TABLE --}}
        <div class="content-card">

            <div class="table-wrapper">

                <table class="kyrix-table">

                    <thead>

                        <tr>

                            <th style="width:11%;">
                                เลขที่เช่า
                            </th>

                            <th style="width:18%;">
                                ลูกค้า
                            </th>

                            <th style="width:19%;">
                                ชุดที่เช่า
                            </th>

                            <th style="width:12%; text-align:center;">
                                กำหนดคืน
                            </th>

                            <th style="width:12%; text-align:center;">
                                สถานะการเช่า
                            </th>

                            <th style="width:18%;">
                                ข้อมูลคืนชุด
                            </th>

                            <th style="width:10%; text-align:center;">
                                จัดการ
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($rentals as $rental)

                            @php

                                $rentalId = $rental->rental_id ?? $rental->id;

                                $rawStatus = strtolower($rental->status ?? 'renting');

                                $status = $currentTab === 'overdue' ? 'overdue' : $rawStatus;

                                /*
                                |--------------------------------------------------------------------------
                                | CUSTOMER
                                |--------------------------------------------------------------------------
                                */

                                $customer = $rental->customer;

                                $customerName = 'ไม่ระบุชื่อ';

                                if ($customer) {
                                    $customerName = trim(
                                        ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''),
                                    );

                                    if ($customerName === '') {
                                        $customerName = 'ไม่ระบุชื่อ';
                                    }
                                }

                                $contactPhone = $rental->recipient_phone ?: $customer->phone ?? null;

                                /*
                                |--------------------------------------------------------------------------
                                | ITEMS
                                |--------------------------------------------------------------------------
                                */

                                $rentalItems = $rental->rentalDetails ?? ($rental->details ?? collect());

                                /*
                                |--------------------------------------------------------------------------
                                | OVERDUE
                                |--------------------------------------------------------------------------
                                */

                                $daysLate = null;

                                if ($status === 'overdue' && $rental->end_date) {
                                    $daysLate = \Carbon\Carbon::parse($rental->end_date)->diffInDays(
                                        \Carbon\Carbon::today(),
                                    );
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | RENTAL STATUS
                                |--------------------------------------------------------------------------
                                */

                                $statusText = match ($status) {
                                    'confirmed' => 'ยืนยันแล้ว',

                                    'renting', 'active' => 'กำลังเช่า',

                                    'pending_return' => 'ลูกค้าแจ้งคืนแล้ว',

                                    'overdue' => $daysLate ? 'เกินกำหนด ' . $daysLate . ' วัน' : 'เกินกำหนด',

                                    'returned', 'completed' => 'คืนแล้ว',

                                    default => ucfirst($rental->status ?? '-'),
                                };

                                $statusClass = match ($status) {
                                    'confirmed', 'renting', 'active' => 'status-active',

                                    'pending_return' => 'status-overdue',

                                    'overdue' => 'status-overdue',

                                    'returned', 'completed' => 'status-returned',

                                    default => 'status-default',
                                };

                                /*
                                |--------------------------------------------------------------------------
                                | RETURN STATUS ใหม่
                                |--------------------------------------------------------------------------
                                |
                                | not_returned       = ยังไม่คืน
                                | returned_requested = แจ้งคืนแล้ว
                                | returned           = คืนชุดแล้ว
                                |
                                | รองรับข้อมูลเก่าที่เคยมี pending_return
                                |
                                */

                                $returnStatus = trim((string) ($rental->return_status ?? ''));

                                if ($returnStatus === '') {
                                    if ($rental->status === 'pending_return') {
                                        $returnStatus = 'returned_requested';
                                    } elseif (in_array($rental->status, ['returned', 'completed'], true)) {
                                        $returnStatus = 'returned';
                                    } else {
                                        $returnStatus = 'not_returned';
                                    }
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | RETURN METHOD
                                |--------------------------------------------------------------------------
                                */

                                $returnMethod = trim((string) ($rental->return_method ?? ''));

                                $returnCarrier = trim((string) ($rental->return_shipping_carrier ?? ''));

                                $returnTrackingNo = trim(
                                    (string) ($rental->return_tracking_number ?: $rental->return_tracking_no ?? ''),
                                );

                                /*
                                | ข้อมูลเก่าไม่มี return_method
                                | ถ้ามีเลขพัสดุให้ถือว่าเป็น parcel
                                */
                                if ($returnMethod === '') {
                                    $returnMethod =
                                        $returnTrackingNo !== ''
                                            ? 'parcel'
                                            : ($returnStatus === 'returned_requested'
                                                ? 'store'
                                                : '');
                                }

                                $returnMethodText = match ($returnMethod) {
                                    'parcel' => 'ส่งพัสดุ',
                                    'store' => 'คืนที่ร้าน',
                                    default => '-',
                                };

                                /*
                                |--------------------------------------------------------------------------
                                | RETURN SHIPPING STATUS
                                |--------------------------------------------------------------------------
                                */

                                $returnShippingStatus = trim((string) ($rental->return_shipping_status ?? ''));

                                /*
                                |--------------------------------------------------------------------------
                                | RETURN STATUS LABEL
                                |--------------------------------------------------------------------------
                                */

                                $returnStatusText = match ($returnStatus) {
                                    'not_returned' => 'ยังไม่คืน',
                                    'returned_requested' => 'แจ้งคืนแล้ว',
                                    'returned' => 'คืนชุดแล้ว',
                                    default => 'ยังไม่คืน',
                                };

                                $returnStatusClass = match ($returnStatus) {
                                    'returned' => 'return-ready',
                                    'returned_requested' => 'return-waiting',
                                    default => 'return-waiting',
                                };

                                /*
                                |--------------------------------------------------------------------------
                                | RETURN REQUEST
                                |--------------------------------------------------------------------------
                                */

                                $hasReturnRequest =
                                    $returnStatus !== 'not_returned' ||
                                    $returnCarrier !== '' ||
                                    $returnTrackingNo !== '' ||
                                    $returnShippingStatus !== '';

                                /*
                                |--------------------------------------------------------------------------
                                | TRACKING URL
                                |--------------------------------------------------------------------------
                                */

                                $returnTrackingUrl = null;

                                if ($returnTrackingNo !== '' && $returnCarrier !== '') {
                                    $tracking = urlencode($returnTrackingNo);

                                    $carrierLower = mb_strtolower($returnCarrier);

                                    if (
                                        str_contains($carrierLower, 'ไปรษณีย์ไทย') ||
                                        str_contains($carrierLower, 'thailand post') ||
                                        str_contains($carrierLower, 'thai post')
                                    ) {
                                        $returnTrackingUrl =
                                            'https://track.thailandpost.co.th/?trackNumber=' . $tracking;
                                    } elseif (str_contains($carrierLower, 'flash')) {
                                        $returnTrackingUrl = 'https://www.flashexpress.co.th/fle/tracking';
                                    } elseif (
                                        str_contains($carrierLower, 'j&t') ||
                                        str_contains($carrierLower, 'jnt')
                                    ) {
                                        $returnTrackingUrl =
                                            'https://www.jtexpress.co.th/service/track?waybillNo=' . $tracking;
                                    } elseif (
                                        str_contains($carrierLower, 'kex') ||
                                        str_contains($carrierLower, 'kerry')
                                    ) {
                                        $returnTrackingUrl = 'https://th.kex-express.com/th/track-parcel';
                                    }
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | CAN INSPECT
                                |--------------------------------------------------------------------------
                                |
                                | คืนที่ร้าน:
                                | แจ้งคืนแล้ว → ตรวจรับได้
                                |
                                | ส่งพัสดุ:
                                | ต้องมีสถานะ "ถึงร้านแล้ว"
                                |
                                */

                                $canInspectReturn =
                                    $returnStatus === 'returned_requested' &&
                                    ($returnMethod === 'store' ||
                                        ($returnMethod === 'parcel' && $returnShippingStatus === 'ถึงร้านแล้ว') ||
                                        ($returnMethod === '' && $returnTrackingNo === ''));

                                /*
                                |--------------------------------------------------------------------------
                                | ITEM SUMMARY
                                |--------------------------------------------------------------------------
                                */

                                $itemNames = [];

                                foreach ($rentalItems as $d) {
                                    if ($d->product) {
                                        $name = $d->product->product_name ?? 'ชุดเช่า';

                                        if (!empty($d->selected_size)) {
                                            $name .= ' (' . $d->selected_size . ')';
                                        }

                                        if (!empty($d->quantity) && $d->quantity > 1) {
                                            $name .= ' x' . $d->quantity;
                                        }

                                        $itemNames[] = $name;
                                    }
                                }

                                $itemSummary = implode(', ', $itemNames);

                                /*
                                |--------------------------------------------------------------------------
                                | DEPOSIT
                                |--------------------------------------------------------------------------
                                */

                                $modalDepositAmount = (float) ($rental->deposit_amount ?? 100);
                            @endphp

                            <tr class="{{ $status === 'overdue' ? 'row-overdue' : '' }}">

                                {{-- RENTAL --}}
                                <td>

                                    <strong style="color:var(--maroon-900);">
                                        #RENT-{{ $rentalId }}
                                    </strong>

                                    <div
                                        style="
                                            margin-top:3px;
                                            font-size:10px;
                                            color:var(--muted);
                                        ">

                                        {{ $rental->formatted_code ?? 'KR-' . $rentalId }}

                                    </div>

                                </td>

                                {{-- CUSTOMER --}}
                                <td>

                                    <div
                                        style="
                                            font-weight:650;
                                            color:#2d1e21;
                                        ">

                                        {{ $customerName }}

                                    </div>

                                    @if (!empty($contactPhone))
                                        <div
                                            style="
                                                font-size:11px;
                                                color:var(--muted);
                                                margin-top:3px;
                                            ">

                                            <i class="fa-solid fa-phone"></i>

                                            {{ $contactPhone }}

                                        </div>
                                    @endif

                                </td>

                                {{-- ITEMS --}}
                                <td>

                                    @forelse($rentalItems as $detail)
                                        <div class="dress-thumb-row">

                                            <span class="cell-truncate"
                                                title="{{ $detail->product->product_name ?? 'ไม่พบข้อมูลชุด' }}"
                                                style="
                                                    font-weight:600;
                                                    color:#2d1e21;
                                                ">

                                                {{ $detail->product->product_name ?? 'ไม่พบข้อมูลชุด' }}

                                            </span>

                                        </div>

                                        @if ($detail->selected_size || $detail->selected_color)
                                            <div
                                                style="
                                                    font-size:11px;
                                                    color:var(--muted);
                                                ">

                                                {{ $detail->selected_size }}

                                                @if ($detail->selected_size && $detail->selected_color)
                                                    ·
                                                @endif

                                                {{ $detail->selected_color }}

                                                @if (($detail->quantity ?? 0) > 1)
                                                    · x{{ $detail->quantity }}
                                                @endif

                                            </div>
                                        @endif

                                    @empty

                                        <span
                                            style="
                                                color:var(--muted);
                                                font-size:12px;
                                            ">

                                            -

                                        </span>
                                    @endforelse

                                </td>

                                {{-- DUE DATE --}}
                                <td
                                    style="
                                        text-align:center;
                                        font-weight:600;
                                        color:#444;
                                    ">

                                    {{ $rental->end_date ? \Carbon\Carbon::parse($rental->end_date)->format('d/m/Y') : '-' }}

                                    @if ($status === 'overdue' && $daysLate !== null)
                                        <div class="overdue-hint">

                                            <i class="fa-solid fa-triangle-exclamation"></i>

                                            เลยกำหนด
                                            {{ $daysLate }}
                                            วัน

                                        </div>
                                    @endif

                                </td>

                                {{-- RENTAL STATUS --}}
                                <td style="text-align:center;">

                                    <span class="status-badge {{ $statusClass }}">

                                        @if ($status === 'overdue')
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        @elseif ($status === 'pending_return')
                                            <i class="fa-solid fa-rotate-left"></i>
                                        @elseif ($status === 'returned' || $status === 'completed')
                                            <i class="fa-solid fa-circle-check"></i>
                                        @endif

                                        {{ $statusText }}

                                    </span>

                                </td>

                                {{-- RETURN INFO --}}
                                <td>

                                    @if ($hasReturnRequest)
                                        <div class="return-info">

                                            {{-- วิธีคืน --}}
                                            <div class="return-info-title">

                                                <i class="fa-solid fa-rotate-left"></i>

                                                วิธีคืน:
                                                {{ $returnMethodText }}

                                            </div>

                                            {{-- สถานะคืนชุดหลัก --}}
                                            <div
                                                style="
                                                    margin-top:6px;
                                                ">

                                                <span class="status-badge {{ $returnStatusClass }}"
                                                    style="
                                                        font-size:10px;
                                                        padding:4px 9px;
                                                    ">

                                                    @if ($returnStatus === 'returned')
                                                        <i class="fa-solid fa-circle-check"></i>
                                                    @elseif ($returnStatus === 'returned_requested')
                                                        <i class="fa-solid fa-clock"></i>
                                                    @else
                                                        <i class="fa-solid fa-shirt"></i>
                                                    @endif

                                                    {{ $returnStatusText }}

                                                </span>

                                            </div>

                                            {{-- วันที่ลูกค้าแจ้งคืน --}}
                                            @if ($rental->return_requested_at)
                                                <div
                                                    style="
                                                        margin-top:6px;
                                                        font-size:10px;
                                                        color:var(--muted);
                                                    ">

                                                    <i class="fa-regular fa-calendar"></i>

                                                    แจ้งคืนเมื่อ:

                                                    {{ \Carbon\Carbon::parse($rental->return_requested_at)->format('d/m/Y H:i') }}

                                                </div>
                                            @endif

                                            {{-- บริษัทขนส่ง --}}
                                            @if ($returnMethod === 'parcel' && $returnCarrier)
                                                <div class="return-info-row" style="margin-top:5px;">

                                                    <span class="return-info-label">
                                                        บริษัทขนส่ง:
                                                    </span>

                                                    <span class="return-info-value">
                                                        {{ $returnCarrier }}
                                                    </span>

                                                </div>
                                            @endif

                                            {{-- เลขพัสดุ --}}
                                            @if ($returnMethod === 'parcel' && $returnTrackingNo)
                                                <div class="return-info-row">

                                                    <span class="return-info-label">
                                                        เลขพัสดุ:
                                                    </span>

                                                    <span class="return-info-value">
                                                        {{ $returnTrackingNo }}
                                                    </span>

                                                </div>
                                            @endif

                                            {{-- สถานะขนส่ง --}}
                                            @if ($returnMethod === 'parcel' && $returnTrackingNo !== '' && $returnShippingStatus !== '')
                                                <div style="margin-top:6px;">

                                                    <span
                                                        class="status-badge
                                                            {{ $returnShippingStatus === 'ถึงร้านแล้ว' ? 'return-ready' : 'return-shipping' }}"
                                                        style="
                                                            font-size:10px;
                                                            padding:4px 9px;
                                                        ">

                                                        @if ($returnShippingStatus === 'ถึงร้านแล้ว')
                                                            <i class="fa-solid fa-circle-check"></i>
                                                        @elseif (in_array($returnShippingStatus, ['กำลังขนส่ง', 'กำลังนำจ่าย'], true))
                                                            <i class="fa-solid fa-truck-fast"></i>
                                                        @else
                                                            <i class="fa-solid fa-box"></i>
                                                        @endif

                                                        {{ $returnShippingStatus }}

                                                    </span>

                                                </div>
                                            @endif

                                            {{-- STATUS UPDATE --}}
                                            @if ($returnMethod === 'parcel' && $returnTrackingNo !== '' && $returnStatus === 'returned_requested')
                                                <form action="{{ route('owner.returns.update-status', $rentalId) }}"
                                                    method="POST" style="margin-top:7px;">

                                                    @csrf

                                                    <select name="return_shipping_status" class="return-status-select"
                                                        onchange="this.form.submit()">

                                                        <option value="ลูกค้าส่งคืนแล้ว"
                                                            {{ in_array($returnShippingStatus, ['', 'ลูกค้าส่งคืนแล้ว', 'ส่งพัสดุแล้ว'], true) ? 'selected' : '' }}>
                                                            ลูกค้าส่งคืนแล้ว
                                                        </option>

                                                        <option value="กำลังขนส่ง"
                                                            {{ $returnShippingStatus === 'กำลังขนส่ง' ? 'selected' : '' }}>
                                                            กำลังขนส่ง
                                                        </option>

                                                        <option value="กำลังนำจ่าย"
                                                            {{ $returnShippingStatus === 'กำลังนำจ่าย' ? 'selected' : '' }}>
                                                            กำลังนำจ่าย
                                                        </option>

                                                        <option value="ถึงร้านแล้ว"
                                                            {{ $returnShippingStatus === 'ถึงร้านแล้ว' ? 'selected' : '' }}>
                                                            ถึงร้านแล้ว
                                                        </option>

                                                    </select>

                                                </form>
                                            @endif

                                            {{-- TRACKING --}}
                                            @if ($returnTrackingUrl)
                                                <a href="{{ $returnTrackingUrl }}" target="_blank"
                                                    rel="noopener noreferrer" class="btn-track-return">

                                                    <i class="fa-solid fa-location-arrow"></i>

                                                    ติดตามพัสดุส่งคืน

                                                </a>
                                            @elseif ($returnMethod === 'parcel' && $returnTrackingNo)
                                                <div class="return-no-link">

                                                    <i class="fa-solid fa-circle-info"></i>

                                                    ระบบยังไม่มีลิงก์ติดตาม
                                                    สำหรับบริษัทขนส่งนี้

                                                </div>
                                            @endif

                                            {{-- วันที่ส่งพัสดุ --}}
                                            @if ($rental->return_shipped_at)
                                                <div
                                                    style="
                                                        margin-top:6px;
                                                        font-size:10px;
                                                        color:var(--muted);
                                                    ">

                                                    วันที่ส่งคืน:

                                                    {{ \Carbon\Carbon::parse($rental->return_shipped_at)->format('d/m/Y H:i') }}

                                                </div>
                                            @endif

                                            {{-- วันที่รับคืน --}}
                                            @if ($rental->return_received_at)
                                                <div
                                                    style="
                                                        margin-top:4px;
                                                        font-size:10px;
                                                        color:#16a34a;
                                                        font-weight:700;
                                                    ">

                                                    <i class="fa-solid fa-circle-check"></i>

                                                    ร้านรับคืนเมื่อ:

                                                    {{ \Carbon\Carbon::parse($rental->return_received_at)->format('d/m/Y H:i') }}

                                                </div>
                                            @endif

                                            {{-- กำหนดถึงร้าน --}}
                                            @if ($rental->return_estimated_delivery_at)
                                                <div
                                                    style="
                                                        margin-top:2px;
                                                        font-size:10px;
                                                        color:var(--muted);
                                                    ">

                                                    คาดว่าจะถึงร้าน:

                                                    {{ \Carbon\Carbon::parse($rental->return_estimated_delivery_at)->format('d/m/Y H:i') }}

                                                </div>
                                            @endif

                                        </div>
                                    @else
                                        <span
                                            style="
                                                font-size:11px;
                                                color:var(--muted);
                                            ">

                                            ยังไม่มีการแจ้งคืนชุด

                                        </span>
                                    @endif

                                </td>

                                {{-- ACTION --}}
                                <td style="text-align:center;">

                                    @if ($returnStatus !== 'returned' && !in_array($rawStatus, ['returned', 'completed'], true))
                                        <div class="action-stack">

                                            @if ($canInspectReturn)
                                                <button type="button"
                                                    class="btn-return {{ $status === 'overdue' ? 'is-urgent' : '' }}"
                                                    onclick="openInspectionModal(
                                                        @js($rentalId),
                                                        @js($rental->formatted_code ?? 'KR-' . $rentalId),
                                                        @js($customerName),
                                                        @js($contactPhone),
                                                        @js(number_format($modalDepositAmount, 2, '.', '')),
                                                        @js($itemSummary)
                                                    )">

                                                    <i class="fa-solid fa-clipboard-check"></i>

                                                    ตรวจรับคืน & จัดการมัดจำ

                                                </button>
                                            @elseif ($returnMethod === 'parcel' && $returnTrackingNo && $returnShippingStatus !== 'ถึงร้านแล้ว')
                                                <div
                                                    style="
                                                        width:100%;
                                                        padding:8px;
                                                        border-radius:8px;
                                                        background:#eff6ff;
                                                        border:1px solid #bfdbfe;
                                                        color:#1d4ed8;
                                                        font-size:10.5px;
                                                        line-height:1.5;
                                                        text-align:center;
                                                    ">

                                                    <i class="fa-solid fa-truck"></i>

                                                    รอพัสดุถึงร้าน
                                                    ก่อนตรวจรับ

                                                </div>
                                            @elseif ($returnStatus === 'returned_requested')
                                                <div
                                                    style="
                                                        width:100%;
                                                        padding:8px;
                                                        border-radius:8px;
                                                        background:#fff7ed;
                                                        border:1px solid #fed7aa;
                                                        color:#9a3412;
                                                        font-size:10.5px;
                                                        line-height:1.5;
                                                        text-align:center;
                                                    ">

                                                    <i class="fa-solid fa-clock"></i>

                                                    รอร้านดำเนินการ

                                                </div>
                                            @endif

                                            @if ($status === 'overdue' && !empty($contactPhone))
                                                <a href="tel:{{ $contactPhone }}" class="btn-call">

                                                    <i class="fa-solid fa-phone"></i>

                                                    โทรหาลูกค้า

                                                </a>
                                            @endif

                                        </div>
                                    @else
                                        <div
                                            style="
                                                display:flex;
                                                flex-direction:column;
                                                align-items:center;
                                                gap:5px;
                                            ">

                                            @if ($rental->condition_status === 'good')
                                                <span class="status-badge"
                                                    style="
                                                        background:#eef7ef;
                                                        color:#16a34a;
                                                        font-size:11px;
                                                    ">

                                                    <i class="fa-solid fa-circle-check"></i>

                                                    ชุดสมบูรณ์

                                                </span>

                                                <span
                                                    style="
                                                        font-size:11px;
                                                        color:#16a34a;
                                                        font-weight:700;
                                                    ">

                                                    คืนมัดจำแล้ว
                                                    ฿{{ number_format((float) ($rental->deposit_refund_amount ?? $modalDepositAmount), 2) }}

                                                </span>

                                                @if ($rental->refund_slip)
                                                    <a href="{{ $rental->refund_slip_url ?? asset(ltrim($rental->refund_slip, '/')) }}"
                                                        target="_blank" rel="noopener noreferrer"
                                                        style="
                                                            font-size:11px;
                                                            color:var(--gold-dark);
                                                            text-decoration:underline;
                                                        ">

                                                        <i class="fa-solid fa-receipt"></i>

                                                        ดูสลิปคืนเงิน

                                                    </a>
                                                @endif
                                            @elseif ($rental->condition_status === 'damaged')
                                                <span class="status-badge"
                                                    style="
                                                        background:#fef2f2;
                                                        color:#dc2626;
                                                        font-size:11px;
                                                    ">

                                                    <i class="fa-solid fa-triangle-exclamation"></i>

                                                    ชุดชำรุดเสียหาย

                                                </span>

                                                <span
                                                    style="
                                                        font-size:11px;
                                                        color:#dc2626;
                                                        font-weight:700;
                                                    ">

                                                    ยึดมัดจำ
                                                    ฿{{ number_format((float) ($rental->deposit_amount ?? $modalDepositAmount), 2) }}

                                                </span>

                                                @if ($rental->damage_note)
                                                    <span
                                                        style="
                                                            font-size:10.5px;
                                                            color:#888;
                                                            max-width:150px;
                                                            white-space:nowrap;
                                                            overflow:hidden;
                                                            text-overflow:ellipsis;
                                                        "
                                                        title="{{ $rental->damage_note }}">

                                                        เหตุ:
                                                        {{ $rental->damage_note }}

                                                    </span>
                                                @endif

                                                @if ($rental->damage_image)
                                                    <a href="{{ $rental->damage_image_url ?? asset(ltrim($rental->damage_image, '/')) }}"
                                                        target="_blank" rel="noopener noreferrer"
                                                        style="
                                                            font-size:11px;
                                                            color:#dc2626;
                                                            text-decoration:underline;
                                                        ">

                                                        <i class="fa-solid fa-image"></i>

                                                        ดูรูปความเสียหาย

                                                    </a>
                                                @endif
                                            @else
                                                <span class="status-badge status-returned">

                                                    <i class="fa-solid fa-check"></i>

                                                    รับคืนแล้ว

                                                </span>
                                            @endif

                                        </div>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7">

                                    <div class="empty-state">

                                        <div class="empty-icon">

                                            <i class="fa-solid fa-rotate-left"></i>

                                        </div>

                                        <div
                                            style="
                                                font-weight:600;
                                                font-size:14px;
                                                color:var(--maroon-900);
                                            ">

                                            ไม่พบรายการในหมวดหมู่นี้

                                        </div>

                                        <div
                                            style="
                                                font-size:12px;
                                                margin-top:4px;
                                            ">

                                            เมื่อมีรายการเช่าที่ตรงกับสถานะดังกล่าว
                                            ข้อมูลจะแสดงผลที่นี่

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if (method_exists($rentals, 'hasPages') && $rentals->hasPages())
                <div
                    style="
                        padding:16px 20px;
                        background:var(--cream);
                        border-top:1px solid var(--line);
                    ">

                    {{ $rentals->links() }}

                </div>
            @endif

        </div>

    </div>

    {{-- INSPECTION MODAL --}}
    <div class="kyrix-modal-overlay" id="inspectionModal" onclick="closeInspectionModal(event)">

        <div class="kyrix-modal-card" onclick="event.stopPropagation()">

            <form id="inspectionForm" action="" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="modal-header-kyrix">

                    <h3>

                        <i class="fa-solid fa-clipboard-check" style="color:var(--gold);"></i>

                        ตรวจสภาพชุด & จัดการเงินมัดจำ

                    </h3>

                    <button type="button" class="modal-close-btn" onclick="closeInspectionModal()">

                        &times;

                    </button>

                </div>

                <div class="modal-body-kyrix">

                    {{-- SUMMARY --}}
                    <div class="order-summary-box">

                        <div class="row-info">

                            <span style="color:var(--muted);">
                                รหัสการเช่า:
                            </span>

                            <strong id="modalRentalCode" style="color:var(--maroon-900);">
                                #KR-0000
                            </strong>

                        </div>

                        <div class="row-info">

                            <span style="color:var(--muted);">
                                ลูกค้า:
                            </span>

                            <span id="modalCustomerName" style="font-weight:600;">
                                -
                            </span>

                        </div>

                        <div class="row-info">

                            <span style="color:var(--muted);">
                                ชุดที่เช่า:
                            </span>

                            <span id="modalItemSummary"
                                style="
                                    font-weight:600;
                                    color:#444;
                                    text-align:right;
                                ">
                                -
                            </span>

                        </div>

                        <div class="row-info"
                            style="
                                margin-top:8px;
                                padding-top:8px;
                                border-top:1px dashed #e2d8d5;
                            ">

                            <span
                                style="
                                    color:var(--maroon-900);
                                    font-weight:700;
                                ">
                                เงินมัดจำประกันชุด:
                            </span>

                            <strong id="modalDepositAmount"
                                style="
                                    font-size:15px;
                                    color:#b45309;
                                ">
                                ฿100.00
                            </strong>

                        </div>

                    </div>

                    {{-- CONDITION --}}
                    <label
                        style="
                            display:block;
                            font-size:13px;
                            font-weight:700;
                            color:var(--maroon-900);
                            margin-bottom:10px;
                        ">

                        ผลการตรวจสภาพชุด
                        (เลือกข้อใดข้อหนึ่ง)

                        <span style="color:#dc2626;">
                            *
                        </span>

                    </label>

                    <div class="condition-grid">

                        {{-- GOOD --}}
                        <div class="condition-card selected-good" id="cardGood" onclick="selectCondition('good')">

                            <input type="radio" name="condition_status" value="good" id="radioGood" checked
                                required>

                            <div class="condition-icon" style="color:#16a34a;">

                                <i class="fa-solid fa-circle-check"></i>

                            </div>

                            <div class="condition-title" style="color:#166534;">

                                ชุดสมบูรณ์ ไม่เสียหาย

                            </div>

                            <div class="condition-desc desc-good">

                                คืนเงินมัดจำเต็มจำนวน

                            </div>

                        </div>

                        {{-- DAMAGED --}}
                        <div class="condition-card" id="cardDamaged" onclick="selectCondition('damaged')">

                            <input type="radio" name="condition_status" value="damaged" id="radioDamaged">

                            <div class="condition-icon" style="color:#dc2626;">

                                <i class="fa-solid fa-triangle-exclamation"></i>

                            </div>

                            <div class="condition-title" style="color:#991b1b;">

                                ชุดชำรุด / มีความเสียหาย

                            </div>

                            <div class="condition-desc desc-damaged">

                                ไม่คืนเงินมัดจำ (ยึดมัดจำ)

                            </div>

                        </div>

                    </div>

                    {{-- GOOD --}}
                    <div id="goodDetailBox" class="condition-detail-box"
                        style="
                            background:#f0fdf4;
                            border-color:#bbf7d0;
                        ">

                        <div
                            style="
                                font-size:13px;
                                color:#166534;
                                font-weight:600;
                                margin-bottom:10px;
                            ">

                            <i class="fa-solid fa-hand-holding-dollar"></i>

                            ระบบจะคืนเงินมัดจำเต็มจำนวน ฿
                            <span id="refundAmountText">
                                100.00
                            </span>

                            ให้ลูกค้า

                        </div>

                        <div class="form-group-k">

                            <label for="refund_slip">

                                แนบสลิปหลักฐานโอนคืนเงินมัดจำ (ถ้ามี):

                            </label>

                            <input type="file" name="refund_slip" id="refund_slip" class="form-input-k"
                                accept="image/*">

                            <small
                                style="
                                    color:var(--muted);
                                    font-size:11px;
                                ">

                                สามารถแนบภาพสลิปโอนคืนเงิน
                                เพื่อให้ลูกค้าตรวจสอบได้

                            </small>

                        </div>

                    </div>

                    {{-- DAMAGED --}}
                    <div id="damagedDetailBox" class="condition-detail-box"
                        style="
                            display:none;
                            background:#fef2f2;
                            border-color:#fecaca;
                        ">

                        <div
                            style="
                                font-size:13px;
                                color:#991b1b;
                                font-weight:700;
                                margin-bottom:10px;
                            ">

                            <i class="fa-solid fa-ban"></i>

                            ทางร้านจะยึดเงินมัดจำ ฿
                            <span id="forfeitAmountText">
                                100.00
                            </span>

                            เต็มจำนวน (ไม่คืนเงิน)

                        </div>

                        <div class="form-group-k">

                            <label for="damage_note">

                                ระบุสาเหตุความเสียหาย / ตำหนิที่ตรวจพบ

                                <span style="color:#dc2626;">
                                    *
                                </span>

                            </label>

                            <input type="text" name="damage_note" id="damage_note" class="form-input-k"
                                placeholder="เช่น ซิปแตก, ผ้ามีรอยขาดที่ชายกระโปรง, คราบเปื้อนฝังแน่น">

                        </div>

                        <div class="form-group-k">

                            <label for="damage_image">

                                แนบรูปถ่ายสภาพชุดชำรุดเพื่อเป็นหลักฐาน:

                            </label>

                            <input type="file" name="damage_image" id="damage_image" class="form-input-k"
                                accept="image/*">

                            <small
                                style="
                                    color:#991b1b;
                                    font-size:11px;
                                ">

                                รูปภาพจะแสดงในหน้ารายละเอียดการเช่าของลูกค้า

                            </small>

                        </div>

                    </div>

                    {{-- NOTE --}}
                    <div class="form-group-k" style="margin-top:14px;">

                        <label for="return_note">

                            บันทึกเพิ่มเติมของทางร้าน (ถ้ามี):

                        </label>

                        <textarea name="return_note" id="return_note" class="form-input-k" rows="2"
                            placeholder="ระบุข้อความบันทึกช่วยจำสำหรับออเดอร์นี้..."></textarea>

                    </div>

                </div>

                <div class="modal-footer-kyrix">

                    <button type="button" class="btn-modal-cancel" onclick="closeInspectionModal()">

                        ยกเลิก

                    </button>

                    <button type="submit" class="btn-modal-submit" id="btnSubmitModal">

                        <i class="fa-solid fa-check"></i>

                        ยืนยันการตรวจรับชุด

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        function openInspectionModal(
            rentalId,
            rentalCode,
            customerName,
            contactPhone,
            depositAmount,
            itemSummary
        ) {

            const form =
                document.getElementById('inspectionForm');

            if (!form) {
                return;
            }

            form.action =
                "{{ url('owner/returns') }}/" +
                rentalId +
                "/confirm";

            document.getElementById(
                    'modalRentalCode'
                ).innerText =
                '#' + rentalCode;

            document.getElementById(
                    'modalCustomerName'
                ).innerText =
                customerName +
                (
                    contactPhone ?
                    ' (' + contactPhone + ')' :
                    ''
                );

            document.getElementById(
                    'modalItemSummary'
                ).innerText =
                itemSummary ||
                'รายการชุดเช่า';

            document.getElementById(
                    'modalDepositAmount'
                ).innerText =
                '฿' + Number(depositAmount).toLocaleString(
                    'th-TH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );

            document.getElementById(
                    'refundAmountText'
                ).innerText =
                Number(depositAmount).toLocaleString(
                    'th-TH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );

            document.getElementById(
                    'forfeitAmountText'
                ).innerText =
                Number(depositAmount).toLocaleString(
                    'th-TH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );

            selectCondition('good');

            document.getElementById(
                'damage_note'
            ).value = '';

            document.getElementById(
                'return_note'
            ).value = '';

            const refundSlip =
                document.getElementById(
                    'refund_slip'
                );

            if (refundSlip) {
                refundSlip.value = '';
            }

            const damageImage =
                document.getElementById(
                    'damage_image'
                );

            if (damageImage) {
                damageImage.value = '';
            }

            const modal =
                document.getElementById(
                    'inspectionModal'
                );

            if (modal) {

                modal.classList.add(
                    'active'
                );

                document.body.style.overflow =
                    'hidden';

            }

        }

        function closeInspectionModal(event) {

            if (
                event &&
                event.target !== event.currentTarget
            ) {
                return;
            }

            const modal =
                document.getElementById(
                    'inspectionModal'
                );

            if (modal) {

                modal.classList.remove(
                    'active'
                );

                document.body.style.overflow =
                    '';

            }

        }

        function selectCondition(type) {

            const cardGood =
                document.getElementById(
                    'cardGood'
                );

            const cardDamaged =
                document.getElementById(
                    'cardDamaged'
                );

            const radioGood =
                document.getElementById(
                    'radioGood'
                );

            const radioDamaged =
                document.getElementById(
                    'radioDamaged'
                );

            const goodBox =
                document.getElementById(
                    'goodDetailBox'
                );

            const damagedBox =
                document.getElementById(
                    'damagedDetailBox'
                );

            const damageNote =
                document.getElementById(
                    'damage_note'
                );

            const btnSubmit =
                document.getElementById(
                    'btnSubmitModal'
                );

            if (
                !cardGood ||
                !cardDamaged ||
                !radioGood ||
                !radioDamaged ||
                !goodBox ||
                !damagedBox ||
                !damageNote ||
                !btnSubmit
            ) {
                return;
            }

            if (type === 'good') {

                cardGood.classList.add(
                    'selected-good'
                );

                cardDamaged.classList.remove(
                    'selected-damaged'
                );

                radioGood.checked = true;

                goodBox.style.display =
                    'block';

                damagedBox.style.display =
                    'none';

                damageNote.required =
                    false;

                btnSubmit.innerHTML =
                    '<i class="fa-solid fa-circle-check"></i>' +
                    ' ยืนยันรับคืน & คืนมัดจำ ฿' +
                    document.getElementById(
                        'refundAmountText'
                    ).innerText;

                btnSubmit.style.background =
                    'linear-gradient(135deg, #15803d, #166534)';

            } else {

                cardDamaged.classList.add(
                    'selected-damaged'
                );

                cardGood.classList.remove(
                    'selected-good'
                );

                radioDamaged.checked = true;

                goodBox.style.display =
                    'none';

                damagedBox.style.display =
                    'block';

                damageNote.required =
                    true;

                btnSubmit.innerHTML =
                    '<i class="fa-solid fa-triangle-exclamation"></i>' +
                    ' ยืนยันรับคืน & ยึดเงินมัดจำ (ชุดเสียหาย)';

                btnSubmit.style.background =
                    'linear-gradient(135deg, #dc2626, #991b1b)';

            }

        }

        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key === 'Escape') {
                    closeInspectionModal();
                }

            }
        );
    </script>
@endpush
