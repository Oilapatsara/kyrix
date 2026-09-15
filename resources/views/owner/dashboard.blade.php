@extends('layouts.owner')

@section('title', 'ภาพรวมร้าน | KYRIX Admin')

@php
    /*
    |--------------------------------------------------------------------------
    | DATA FROM CONTROLLER
    |--------------------------------------------------------------------------
    */
    $stats = $stats ?? [];

    $monthlyRevenue   = $stats['monthly_revenue']   ?? 0;
    $todayRevenue     = $stats['today_revenue']     ?? 0;
    $totalBookings    = $stats['total_bookings']    ?? 0;
    $activeRentals    = $stats['active_rentals']    ?? 0;
    $pendingBookings  = $stats['pending_bookings']  ?? 0;
    $availableDresses = $stats['available_dresses'] ?? 0;
    $totalDresses     = $stats['total_dresses']     ?? 0;
    $totalCustomers   = $stats['total_customers']   ?? 0;

    $recentBookings  = $recentBookings ?? collect();
    $upcomingReturns = $upcomingReturns ?? collect();
    $pendingPayments = $pendingPayments ?? collect();
    $popularDresses  = $popularDresses ?? collect();

    /*
    |--------------------------------------------------------------------------
    | OWNER NAME
    |--------------------------------------------------------------------------
    */
    $ownerName = $ownerName
        ?? (
            auth()->check()
                ? (
                    auth()->user()->name
                    ?? auth()->user()->shop_name
                    ?? null
                )
                : null
        )
        ?? 'เจ้าของร้าน';

    /*
    |--------------------------------------------------------------------------
    | MONTHLY REVENUE CHART
    |--------------------------------------------------------------------------
    */
    $monthlyLabels = $monthlyLabels ?? [
        'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.',
    ];

    $monthlyData = $monthlyData ?? array_fill(0, 12, 0);

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Format money.
     */
    $money = function ($value) {
        return number_format((float) $value, 2);
    };

    /**
     * Safe route helper.
     *
     * รองรับทั้ง:
     *
     * $link('owner.bookings.index')
     *
     * $link('owner.bookings.show', 15)
     *
     * $link('owner.bookings.show', ['booking' => 15])
     *
     * ถ้า route ไม่มีอยู่จริง จะคืน null
     */
    $link = function (string $name, $params = []) {

        if (!\Route::has($name)) {
            return null;
        }

        /*
        | ถ้าส่ง ID มาเป็น scalar เช่น 15
        | ให้แปลงเป็น array ก่อนส่งให้ route()
        */
        if (!is_array($params)) {
            $params = [$params];
        }

        try {
            return route($name, $params);
        } catch (\Throwable $e) {
            return null;
        }
    };

    /**
     * Status text.
     */
    $statusText = function ($status) {

        $status = strtolower(trim((string) $status));

        return match ($status) {
            'pending'         => 'รอการยืนยัน',
            'confirmed'       => 'ยืนยันแล้ว',
            'waiting_payment' => 'รอชำระเงิน',
            'paid'            => 'ชำระเงินแล้ว',
            'rented'          => 'กำลังเช่า',
            'returning'       => 'รอคืนชุด',
            'returned'        => 'คืนชุดแล้ว',
            'completed'       => 'เสร็จสิ้น',
            'cancelled',
            'canceled'        => 'ยกเลิก',
            default           => $status !== '' ? $status : 'ไม่ระบุ',
        };
    };

    /**
     * Status CSS class.
     */
    $statusClass = function ($status) {

        $status = strtolower(trim((string) $status));

        return match ($status) {
            'pending'         => 'status-pending',
            'confirmed'       => 'status-confirmed',
            'waiting_payment' => 'status-warning',
            'paid'            => 'status-paid',
            'rented'          => 'status-rented',
            'returning'       => 'status-returning',
            'returned'        => 'status-returned',
            'completed'       => 'status-completed',
            'cancelled',
            'canceled'        => 'status-cancelled',
            default           => 'status-default',
        };
    };
@endphp


@push('styles')
<style>
    :root {
        --burgundy-900: #3b1119;
        --burgundy-800: #4a1622;
        --burgundy-700: #5c1d2b;
        --burgundy-600: #732537;

        --gold: #b89053;
        --gold-dark: #99733d;

        --surface: #ffffff;
        --background: #fcfbfa;

        --ink: #1f1416;
        --muted: #6e5c60;
        --line: #e8e2df;

        --radius: 10px;
    }

    * {
        box-sizing: border-box;
    }

    .kyrix-dashboard {
        color: var(--ink);
        padding: 4px 0;
    }

    .kyrix-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .dashboard-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--line);
        padding-bottom: 16px;
    }

    .dashboard-heading .eyebrow {
        display: inline-block;
        color: var(--gold-dark);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .dashboard-heading h1 {
        margin: 0;
        font-family: 'Prompt', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: var(--burgundy-900);
    }

    .dashboard-heading p {
        margin: 4px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .dashboard-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 38px;
        padding: 0 16px;
        border-radius: var(--radius);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .dashboard-btn.primary {
        background: var(--burgundy-900);
        color: #fff;
    }

    .dashboard-btn.primary:hover {
        background: var(--burgundy-800);
    }

    .dashboard-btn.secondary {
        background: #fff;
        color: var(--burgundy-900);
        border-color: var(--line);
    }

    .dashboard-btn.secondary:hover {
        border-color: var(--gold);
        background: #faf8f7;
    }

    /*
    =========================================================
    DISABLED LINK
    =========================================================
    */

    .dashboard-btn.disabled,
    .quick-item.disabled,
    .card-link.disabled,
    .stat-card.disabled {
        cursor: default;
        pointer-events: none;
        opacity: .65;
    }


    /* =========================================================
       HERO
    ========================================================= */

    .owner-hero {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 20px;

        background: linear-gradient(
            135deg,
            var(--burgundy-900),
            var(--burgundy-700)
        );

        color: #fff;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;

        border: 1px solid rgba(184, 144, 83, 0.2);
    }

    .owner-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 4px;
        background: rgba(184, 144, 83, 0.15);
        color: #e6c594;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .5px;
        margin-bottom: 8px;
    }

    .owner-hero h2 {
        margin: 0;
        font-family: 'Prompt', sans-serif;
        font-size: 22px;
        font-weight: 600;
    }

    .owner-hero p {
        margin: 6px 0 0;
        font-size: 13px;
        color: rgba(255, 255, 255, .8);
        max-width: 550px;
        line-height: 1.5;
    }

    .owner-hero-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .owner-hero-actions .dashboard-btn.primary {
        background: var(--gold);
        color: #fff;
        border: none;
    }

    .owner-hero-actions .dashboard-btn.primary:hover {
        background: #a67f47;
    }

    .owner-hero-actions .dashboard-btn.secondary {
        background: rgba(255,255,255,.08);
        color: #fff;
        border-color: rgba(255,255,255,.2);
    }

    .owner-hero-actions .dashboard-btn.secondary:hover {
        background: rgba(255,255,255,.15);
    }


    /* =========================================================
       KPI
    ========================================================= */

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 18px 20px;

        display: flex;
        flex-direction: column;
        justify-content: space-between;

        color: inherit;
        text-decoration: none;

        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    a.stat-card:hover {
        border-color: var(--gold);
        box-shadow: 0 6px 16px rgba(59,17,25,.08);
        transform: translateY(-2px);
    }

    a.stat-card:hover .stat-icon {
        background: var(--burgundy-900);
        color: #fff;
    }

    a.stat-card:hover .stat-go {
        color: var(--burgundy-700);
        opacity: 1;
    }

    a.stat-card:focus-visible {
        outline: 2px solid var(--gold);
        outline-offset: 2px;
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .stat-icon {
        width: 32px;
        height: 32px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 6px;
        background: #f4efed;
        color: var(--burgundy-800);
        font-size: 14px;

        transition: .18s ease;
    }

    .stat-value {
        margin-top: 12px;
        font-size: 24px;
        font-weight: 700;
        color: var(--burgundy-900);
        letter-spacing: -.5px;
    }

    .stat-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 4px;
    }

    .stat-sub {
        font-size: 11px;
        color: var(--muted);
    }

    .stat-go {
        font-size: 11px;
        font-weight: 650;
        color: var(--muted);
        opacity: .65;
        white-space: nowrap;
        transition: .18s ease;
    }


    /* =========================================================
       MAIN GRID
    ========================================================= */

    .dashboard-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1.6fr)
            minmax(320px, .9fr);

        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        padding: 16px 20px;
        border-bottom: 1px solid var(--line);
    }

    .card-title {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: var(--burgundy-900);
    }

    .card-description {
        margin: 2px 0 0;
        color: var(--muted);
        font-size: 11px;
    }

    .card-link {
        font-size: 12px;
        color: var(--burgundy-700);
        text-decoration: none;
        font-weight: 600;
    }

    .card-link:hover {
        text-decoration: underline;
    }

    .card-body {
        padding: 20px;
    }


    /* =========================================================
       CHART
    ========================================================= */

    .chart-card {
        display: flex;
        flex-direction: column;
    }

    .chart-wrapper {
        height: 260px;
        position: relative;
        width: 100%;
    }

    #revenueBarChart {
        cursor: pointer;
    }


    /* =========================================================
       QUICK MENU
    ========================================================= */

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .quick-item {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 12px;

        background: #fbf9f8;
        border: 1px solid var(--line);
        border-radius: 8px;

        color: var(--ink);
        text-decoration: none;

        transition: all .15s ease;
    }

    .quick-item:hover {
        background: #fff;
        border-color: var(--gold);
    }

    .quick-icon {
        width: 32px;
        height: 32px;
        flex: 0 0 32px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #f0e9e7;
        border-radius: 6px;
        color: var(--burgundy-800);
        font-size: 13px;
    }

    .quick-name {
        font-size: 12px;
        font-weight: 650;
        color: var(--burgundy-900);
    }

    .quick-desc {
        margin-top: 2px;
        font-size: 10px;
        color: var(--muted);
    }


    /* =========================================================
       TABLE
    ========================================================= */

    .table-wrapper {
        overflow-x: auto;
    }

    .kyrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .kyrix-table th {
        text-align: left;
        padding: 10px 16px;

        background: #fbf9f8;
        color: var(--muted);

        font-size: 11px;
        font-weight: 700;

        text-transform: uppercase;
        letter-spacing: .5px;

        border-bottom: 1px solid var(--line);
    }

    .kyrix-table td {
        padding: 12px 16px;

        border-bottom: 1px solid var(--line);

        color: var(--ink);
        font-size: 12px;

        vertical-align: middle;
    }

    .kyrix-table tr:last-child td {
        border-bottom: none;
    }

    .kyrix-table tr.row-link {
        cursor: pointer;
        transition: background .15s ease;
    }

    .kyrix-table tr.row-link:hover {
        background: #fbf9f8;
    }

    .booking-code {
        font-weight: 650;
        color: var(--burgundy-900);
    }

    .customer-name {
        font-weight: 600;
    }

    .customer-phone {
        margin-top: 2px;
        color: var(--muted);
        font-size: 11px;
    }


    /* =========================================================
       STATUS
    ========================================================= */

    .status-badge {
        display: inline-flex;
        align-items: center;

        padding: 3px 8px;

        border-radius: 4px;

        font-size: 10px;
        font-weight: 600;

        letter-spacing: .3px;
    }

    .status-pending {
        background: #fff8e8;
        color: #8a6200;
        border: 1px solid #fce8bd;
    }

    .status-confirmed {
        background: #eef4fb;
        color: #356294;
        border: 1px solid #d2e3f5;
    }

    .status-warning {
        background: #fef0eb;
        color: #a14e1a;
        border: 1px solid #fcdbd0;
    }

    .status-paid {
        background: #edf7ee;
        color: #3b6b3f;
        border: 1px solid #d2edd4;
    }

    .status-rented {
        background: #f7e7ea;
        color: var(--burgundy-800);
        border: 1px solid #ecd3d7;
    }

    .status-returning {
        background: #fdf5ea;
        color: #9c6d1f;
        border: 1px solid #fae4c8;
    }

    .status-returned {
        background: #edf6f6;
        color: #366b6c;
        border: 1px solid #cee8e8;
    }

    .status-completed {
        background: #f3efe9;
        color: #52413b;
        border: 1px solid #e2dacd;
    }

    .status-cancelled {
        background: #f4f4f4;
        color: #666;
        border: 1px solid #e0e0e0;
    }

    .status-default {
        background: #f4f4f4;
        color: #666;
        border: 1px solid #e0e0e0;
    }


    /* =========================================================
       SIDE LIST
    ========================================================= */

    .side-list {
        display: flex;
        flex-direction: column;
    }

    .side-item {
        display: flex;
        align-items: center;
        gap: 12px;

        padding: 10px 0;

        border-bottom: 1px solid var(--line);

        color: inherit;
        text-decoration: none;
    }

    .side-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .side-item:first-child {
        padding-top: 0;
    }

    a.side-item {
        margin: 0 -8px;
        padding-left: 8px;
        padding-right: 8px;
        border-radius: 8px;
        transition: background .15s ease;
    }

    a.side-item:hover {
        background: #fbf9f8;
    }

    a.side-item:hover .side-title {
        color: var(--burgundy-700);
    }

    .side-avatar {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;

        border-radius: 6px;

        background: #f4efed;
        color: var(--burgundy-800);

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 13px;
        font-weight: 700;
    }

    .side-content {
        min-width: 0;
        flex: 1;
    }

    .side-title {
        font-size: 12px;
        font-weight: 650;
        color: var(--ink);

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .side-meta {
        margin-top: 2px;
        color: var(--muted);
        font-size: 11px;
    }

    .side-price {
        font-size: 12px;
        font-weight: 700;
        color: var(--burgundy-900);
        white-space: nowrap;
    }


    /* =========================================================
       LOWER GRID
    ========================================================= */

    .lower-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }


    /* =========================================================
       POPULAR DRESSES
    ========================================================= */

    .dress-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .dress-item {
        border: 1px solid var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        background: #fff;

        display: flex;
        flex-direction: column;

        color: inherit;
        text-decoration: none;

        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    a.dress-item:hover {
        border-color: var(--gold);
        box-shadow: 0 6px 16px rgba(59,17,25,.08);
        transform: translateY(-2px);
    }

    a.dress-item:hover .dress-name {
        color: var(--burgundy-700);
    }

    .dress-image {
        aspect-ratio: 3 / 4;

        background: #f7f4f2;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;
        position: relative;
    }

    .dress-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .dress-placeholder {
        color: var(--muted);
        font-size: 24px;
    }

    .dress-info {
        padding: 12px;

        display: flex;
        flex-direction: column;
        justify-content: space-between;

        flex: 1;
    }

    .dress-name {
        font-size: 12px;
        font-weight: 700;
        color: var(--ink);

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dress-meta {
        display: flex;
        justify-content: space-between;
        gap: 5px;

        margin-top: 6px;

        font-size: 11px;
        color: var(--muted);
    }

    .dress-price {
        font-weight: 700;
        color: var(--burgundy-900);
    }


    /* =========================================================
       RETURN
    ========================================================= */

    .return-item {
        display: block;

        padding: 10px 0;

        border-bottom: 1px solid var(--line);

        color: inherit;
        text-decoration: none;
    }

    .return-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .return-item:first-child {
        padding-top: 0;
    }

    a.return-item {
        margin: 0 -8px;
        padding-left: 8px;
        padding-right: 8px;

        border-radius: 8px;

        transition: background .15s ease;
    }

    a.return-item:hover {
        background: #fbf9f8;
    }

    a.return-item:hover .return-name {
        color: var(--burgundy-700);
    }

    .return-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .return-name {
        font-size: 12px;
        font-weight: 650;
        color: var(--ink);
    }

    .return-date {
        color: var(--gold-dark);
        font-size: 11px;
        font-weight: 700;

        background: #fbf9f8;

        padding: 2px 6px;

        border-radius: 4px;
        border: 1px solid var(--line);
    }

    .return-detail {
        margin-top: 3px;
        color: var(--muted);
        font-size: 11px;
    }


    /* =========================================================
       EMPTY
    ========================================================= */

    .empty-state {
        padding: 30px 15px;
        text-align: center;
        color: var(--muted);
    }

    .empty-icon {
        font-size: 22px;
        margin-bottom: 6px;
        opacity: .5;
        color: var(--burgundy-700);
    }

    .empty-text {
        font-size: 12px;
        font-weight: 500;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 1200px) {

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .dashboard-grid,
        .lower-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {

        .kyrix-dashboard {
            padding: 12px;
        }

        .dashboard-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .stats-grid,
        .quick-grid,
        .dress-list {
            grid-template-columns: 1fr;
        }

        .owner-hero {
            padding: 20px;
        }

        .owner-hero-actions {
            width: 100%;
        }

        .owner-hero-actions .dashboard-btn {
            flex: 1;
        }
    }
</style>
@endpush


@section('content')

<div class="kyrix-dashboard">

    <div class="kyrix-container">

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="dashboard-header">

            <div class="dashboard-heading">

                <span class="eyebrow">
                    KYRIX RENTAL · MANAGEMENT
                </span>

                <h1>
                    ภาพรวมระบบร้าน
                </h1>

                <p>
                    ควบคุมและตรวจสอบภาพรวมการเช่าชุดทั้งหมดภายในหน้าเดียว
                </p>

            </div>


            <div class="header-actions">

                @php
                    $bookingsIndexUrl = $link('owner.bookings.index');
                    $dressCreateUrl   = $link('owner.dresses.create');
                @endphp


                @if($bookingsIndexUrl)

                    <a
                        href="{{ $bookingsIndexUrl }}"
                        class="dashboard-btn secondary"
                    >
                        <i class="fa-regular fa-calendar"></i>
                        รายการเช่าทั้งหมด
                    </a>

                @endif


                @if($dressCreateUrl)

                    <a
                        href="{{ $dressCreateUrl }}"
                        class="dashboard-btn primary"
                    >
                        <i class="fa-solid fa-plus"></i>
                        เพิ่มชุดใหม่
                    </a>

                @endif

            </div>

        </div>


        {{-- =====================================================
             WELCOME HERO
        ====================================================== --}}

        <div class="owner-hero">

            <div>

                <span class="owner-hero-badge">
                    <i class="fa-solid fa-store"></i>
                    ADMIN PANEL
                </span>

                <h2>
                    ยินดีต้อนรับกลับมา, {{ $ownerName }}
                </h2>

                <p>
                    ยอดขายวันนี้รวม
                    <strong>
                        ฿{{ $money($todayRevenue) }}
                    </strong>

                    และมีรายการรอดำเนินการตรวจสอบทั้งสิ้น

                    <strong>
                        {{ number_format($pendingBookings) }} รายการ
                    </strong>
                </p>

            </div>


            <div class="owner-hero-actions">

                @php
                    $pendingBookingUrl = $link(
                        'owner.bookings.index',
                        ['status' => 'pending']
                    );

                    $reportsUrl = $link('owner.reports.index');
                @endphp


                @if($pendingBookingUrl)

                    <a
                        href="{{ $pendingBookingUrl }}"
                        class="dashboard-btn secondary"
                    >
                        <i class="fa-regular fa-clock"></i>
                        ตรวจสอบคิวรอดำเนินการ
                    </a>

                @endif


                @if($reportsUrl)

                    <a
                        href="{{ $reportsUrl }}"
                        class="dashboard-btn primary"
                    >
                        <i class="fa-solid fa-chart-line"></i>
                        รายงานสถิติเชิงลึก
                    </a>

                @endif

            </div>

        </div>


        {{-- =====================================================
             KPI CARDS
        ====================================================== --}}

        @php

            $monthlyReportUrl = $link(
                'owner.reports.index',
                ['range' => 'month']
            );

            $monthlyPaymentUrl = $link(
                'owner.payments.index',
                ['range' => 'month']
            );


            $todayPaymentUrl = $link(
                'owner.payments.index',
                ['range' => 'today']
            );

            $todayReportUrl = $link(
                'owner.reports.index',
                ['range' => 'today']
            );


            $activeRentalUrl = $link(
                'owner.bookings.index',
                ['status' => 'rented']
            );


            $pendingUrl = $link(
                'owner.bookings.index',
                ['status' => 'pending']
            );


            $kpiCards = [

                [
                    'label' => 'รายได้เดือนนี้',
                    'icon'  => 'fa-solid fa-wallet',
                    'value' => '฿' . $money($monthlyRevenue),
                    'sub'   => 'ยอดรับรวมเดือนปัจจุบัน',
                    'go'    => 'ดูรายงานรายเดือน',
                    'url'   => $monthlyReportUrl
                                ?? $monthlyPaymentUrl,
                ],

                [
                    'label' => 'รายได้วันนี้',
                    'icon'  => 'fa-solid fa-coins',
                    'value' => '฿' . $money($todayRevenue),
                    'sub'   => 'ยอดเงินที่เกิดขึ้นในวันนี้',
                    'go'    => 'ดูรายการชำระเงิน',
                    'url'   => $todayPaymentUrl
                                ?? $todayReportUrl,
                ],

                [
                    'label' => 'กำลังเช่าอยู่',
                    'icon'  => 'fa-solid fa-shirt',
                    'value' => number_format($activeRentals),
                    'sub'   => 'ชุดที่ถูกเช่าอยู่ขณะนี้',
                    'go'    => 'ดูชุดที่ถูกเช่า',
                    'url'   => $activeRentalUrl,
                ],

                [
                    'label' => 'รอดำเนินการ',
                    'icon'  => 'fa-regular fa-clock',
                    'value' => number_format($pendingBookings),
                    'sub'   => 'รายการที่ต้องตรวจสอบ',
                    'go'    => 'ตรวจสอบรายการ',
                    'url'   => $pendingUrl,
                ],

            ];

        @endphp


        <div class="stats-grid">

            @foreach($kpiCards as $card)

                @if($card['url'])

                    <a
                        href="{{ $card['url'] }}"
                        class="stat-card"
                    >

                        <div class="stat-top">

                            <div class="stat-label">
                                {{ $card['label'] }}
                            </div>

                            <div class="stat-icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>

                        </div>


                        <div class="stat-value">
                            {{ $card['value'] }}
                        </div>


                        <div class="stat-bottom">

                            <span class="stat-sub">
                                {{ $card['sub'] }}
                            </span>

                            <span class="stat-go">
                                {{ $card['go'] }} →
                            </span>

                        </div>

                    </a>

                @else

                    <div class="stat-card">

                        <div class="stat-top">

                            <div class="stat-label">
                                {{ $card['label'] }}
                            </div>

                            <div class="stat-icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>

                        </div>


                        <div class="stat-value">
                            {{ $card['value'] }}
                        </div>


                        <div class="stat-bottom">

                            <span class="stat-sub">
                                {{ $card['sub'] }}
                            </span>

                        </div>

                    </div>

                @endif

            @endforeach

        </div>


        {{-- =====================================================
             CHART + QUICK MENU
        ====================================================== --}}

        <div class="dashboard-grid">


            {{-- REVENUE CHART --}}

            <div class="card chart-card">

                <div class="card-header">

                    <div>

                        <h2 class="card-title">
                            สถิติรายได้รายเดือน
                        </h2>

                        <p class="card-description">
                            คลิกที่แท่งกราฟเพื่อดูรายการของเดือนนั้น
                        </p>

                    </div>

                    <span class="card-link">
                        ปี พ.ศ. {{ now()->year + 543 }}
                    </span>

                </div>


                <div class="card-body">

                    <div class="chart-wrapper">

                        @php
                            $chartUrl =
                                $link('owner.reports.index')
                                ?? $link('owner.payments.index')
                                ?? '';
                        @endphp

                        <canvas
                            id="revenueBarChart"
                            data-month-url="{{ $chartUrl }}"
                            data-year="{{ now()->year }}"
                        ></canvas>

                    </div>

                </div>

            </div>


            {{-- QUICK MENU --}}

            <div class="card">

                <div class="card-header">

                    <div>

                        <h2 class="card-title">
                            เมนูลัดผู้ดูแลระบบ
                        </h2>

                        <p class="card-description">
                            ทางด่วนเข้าถึงฟังก์ชันสำคัญของร้าน
                        </p>

                    </div>

                </div>


                <div class="card-body">

                    <div class="quick-grid">


                        {{-- DRESSES --}}

                        @php
                            $url = $link('owner.dresses.index');
                        @endphp

                        @if($url)

                            <a
                                href="{{ $url }}"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    <i class="fa-solid fa-shirt"></i>
                                </div>

                                <div>

                                    <div class="quick-name">
                                        ชุดทั้งหมด
                                    </div>

                                    <div class="quick-desc">
                                        {{ number_format($totalDresses) }} รายการ
                                    </div>

                                </div>

                            </a>

                        @endif


                        {{-- BOOKINGS --}}

                        @php
                            $url = $link('owner.bookings.index');
                        @endphp

                        @if($url)

                            <a
                                href="{{ $url }}"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    <i class="fa-regular fa-calendar-days"></i>
                                </div>

                                <div>

                                    <div class="quick-name">
                                        การเช่า
                                    </div>

                                    <div class="quick-desc">
                                        {{ number_format($totalBookings) }} คิว
                                    </div>

                                </div>

                            </a>

                        @endif


                        {{-- CUSTOMERS --}}

                        @php
                            $url = $link('owner.customers.index');
                        @endphp

                        @if($url)

                            <a
                                href="{{ $url }}"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    <i class="fa-solid fa-users"></i>
                                </div>

                                <div>

                                    <div class="quick-name">
                                        ลูกค้า
                                    </div>

                                    <div class="quick-desc">
                                        {{ number_format($totalCustomers) }} บัญชี
                                    </div>

                                </div>

                            </a>

                        @endif


                        {{-- PAYMENTS --}}

                        @php
                            $url = $link(
                                'owner.payments.index',
                                ['status' => 'pending']
                            );
                        @endphp

                        @if($url)

                            <a
                                href="{{ $url }}"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>

                                <div>

                                    <div class="quick-name">
                                        การชำระเงิน
                                    </div>

                                    <div class="quick-desc">
                                        ตรวจสอบสลิป
                                    </div>

                                </div>

                            </a>

                        @endif


                        {{-- RETURNS --}}

                        @php
                            $url = $link('owner.returns.index');
                        @endphp

                        @if($url)

                            <a
                                href="{{ $url }}"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </div>

                                <div>

                                    <div class="quick-name">
                                        รับคืนชุด
                                    </div>

                                    <div class="quick-desc">
                                        ตรวจสภาพชุด
                                    </div>

                                </div>

                            </a>

                        @endif


                        {{-- REPORTS --}}

                        @php
                            $url = $link('owner.reports.index');
                        @endphp

                        @if($url)

                            <a
                                href="{{ $url }}"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    <i class="fa-solid fa-chart-pie"></i>
                                </div>

                                <div>

                                    <div class="quick-name">
                                        รายงาน
                                    </div>

                                    <div class="quick-desc">
                                        สรุปยอดบัญชี
                                    </div>

                                </div>

                            </a>

                        @endif


                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             RECENT BOOKINGS
        ====================================================== --}}

        <div
            class="card"
            style="margin-bottom:20px;"
        >

            <div class="card-header">

                <div>

                    <h2 class="card-title">
                        รายการเช่าล่าสุด
                    </h2>

                    <p class="card-description">
                        คลิกที่แถวเพื่อเปิดรายละเอียดการจอง
                    </p>

                </div>


                @php
                    $url = $link('owner.bookings.index');
                @endphp

                @if($url)

                    <a
                        href="{{ $url }}"
                        class="card-link"
                    >
                        ดูทั้งหมด →
                    </a>

                @endif

            </div>


            <div class="table-wrapper">

                @if(
                    $recentBookings instanceof \Illuminate\Support\Collection
                    && $recentBookings->count()
                )

                    <table class="kyrix-table">

                        <thead>

                            <tr>

                                <th>
                                    รหัสการเช่า
                                </th>

                                <th>
                                    ลูกค้า
                                </th>

                                <th>
                                    วันรับชุด
                                </th>

                                <th>
                                    วันคืนชุด
                                </th>

                                <th>
                                    ยอดรวมทั้งสิ้น
                                </th>

                                <th>
                                    สถานะปัจจุบัน
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($recentBookings as $booking)

                                @php

                                    $bookingId =
                                        $booking->booking_id
                                        ?? $booking->id
                                        ?? null;


                                    /*
                                    | สำคัญ:
                                    | ส่ง parameter เป็น associative array
                                    | เพื่อรองรับ route เช่น {booking}
                                    */
                                    $bookingUrl = null;

                                    if ($bookingId) {

                                        $bookingUrl =
                                            $link(
                                                'owner.bookings.show',
                                                ['booking' => $bookingId]
                                            )
                                            ??
                                            $link(
                                                'owner.bookings.show',
                                                [$bookingId]
                                            )
                                            ??
                                            $link(
                                                'owner.bookings.edit',
                                                ['booking' => $bookingId]
                                            )
                                            ??
                                            $link(
                                                'owner.bookings.edit',
                                                [$bookingId]
                                            );
                                    }

                                @endphp


                                <tr
                                    @if($bookingUrl)
                                        class="row-link"
                                        data-href="{{ $bookingUrl }}"
                                    @endif
                                >

                                    <td>

                                        <div class="booking-code">

                                            {{
                                                $booking->booking_code
                                                ?? $booking->code
                                                ?? (
                                                    '#' .
                                                    ($bookingId ?? '-')
                                                )
                                            }}

                                        </div>

                                    </td>


                                    <td>

                                        <div class="customer-name">

                                            {{
                                                $booking->customer->name
                                                ?? $booking->customer_name
                                                ?? $booking->user->name
                                                ?? '-'
                                            }}

                                        </div>


                                        @if(
                                            isset($booking->customer->phone)
                                            || isset($booking->phone)
                                        )

                                            <div class="customer-phone">

                                                {{
                                                    $booking->customer->phone
                                                    ?? $booking->phone
                                                    ?? ''
                                                }}

                                            </div>

                                        @endif

                                    </td>


                                    <td>

                                        @php

                                            $startDate =
                                                $booking->start_date
                                                ?? $booking->rental_start
                                                ?? null;

                                        @endphp

                                        @if($startDate)

                                            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}

                                        @else

                                            -

                                        @endif

                                    </td>


                                    <td>

                                        @php

                                            $endDate =
                                                $booking->end_date
                                                ?? $booking->rental_end
                                                ?? null;

                                        @endphp

                                        @if($endDate)

                                            {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}

                                        @else

                                            -

                                        @endif

                                    </td>


                                    <td>

                                        <strong>
                                            ฿{{
                                                $money(
                                                    $booking->total_amount
                                                    ?? $booking->total
                                                    ?? $booking->amount
                                                    ?? 0
                                                )
                                            }}
                                        </strong>

                                    </td>


                                    <td>

                                        @php
                                            $bookingStatus =
                                                $booking->status
                                                ?? 'pending';
                                        @endphp

                                        <span
                                            class="status-badge {{ $statusClass($bookingStatus) }}"
                                        >
                                            {{ $statusText($bookingStatus) }}
                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @else

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="fa-regular fa-calendar-xmark"></i>
                        </div>

                        <div class="empty-text">
                            ยังไม่มีรายการเช่าในขณะนี้
                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- =====================================================
             LOWER GRID
        ====================================================== --}}

        <div class="lower-grid">


            {{-- =================================================
                 UPCOMING RETURNS
            ================================================== --}}

            <div class="card">

                <div class="card-header">

                    <div>

                        <h2 class="card-title">
                            ชุดที่ใกล้ถึงกำหนดคืน
                        </h2>

                        <p class="card-description">
                            คลิกรายการเพื่อเปิดหน้ารับคืนชุด
                        </p>

                    </div>


                    @php
                        $url = $link('owner.returns.index');
                    @endphp

                    @if($url)

                        <a
                            href="{{ $url }}"
                            class="card-link"
                        >
                            ดูทั้งหมด
                        </a>

                    @endif

                </div>


                <div class="card-body">

                    @if(
                        $upcomingReturns instanceof \Illuminate\Support\Collection
                        && $upcomingReturns->count()
                    )

                        <div class="side-list">

                            @foreach($upcomingReturns as $return)

                                @php

                                    $returnCustomer =
                                        $return->customer->name
                                        ?? $return->customer_name
                                        ?? $return->user->name
                                        ?? 'ไม่ระบุลูกค้า';


                                    $returnDress =
                                        $return->dress->name
                                        ?? $return->dress_name
                                        ?? 'ไม่ระบุชุด';


                                    $returnDate =
                                        $return->return_date
                                        ?? $return->end_date
                                        ?? $return->rental_end
                                        ?? null;


                                    $returnId =
                                        $return->booking_id
                                        ?? $return->id
                                        ?? null;


                                    $returnUrl = null;


                                    if ($returnId) {

                                        $returnUrl =
                                            $link(
                                                'owner.returns.show',
                                                ['return' => $returnId]
                                            )
                                            ??
                                            $link(
                                                'owner.returns.show',
                                                ['booking' => $returnId]
                                            )
                                            ??
                                            $link(
                                                'owner.returns.show',
                                                [$returnId]
                                            )
                                            ??
                                            $link(
                                                'owner.bookings.show',
                                                ['booking' => $returnId]
                                            )
                                            ??
                                            $link(
                                                'owner.bookings.show',
                                                [$returnId]
                                            );
                                    }

                                @endphp


                                @if($returnUrl)

                                    <a
                                        href="{{ $returnUrl }}"
                                        class="return-item"
                                    >

                                @else

                                    <div class="return-item">

                                @endif


                                    <div class="return-top">

                                        <div class="return-name">

                                            <i class="fa-solid fa-shirt"></i>

                                            {{ $returnDress }}

                                        </div>


                                        <div class="return-date">

                                            @if($returnDate)

                                                คืน:
                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $returnDate
                                                    )->format('d/m/Y')
                                                }}

                                            @else

                                                -

                                            @endif

                                        </div>

                                    </div>


                                    <div class="return-detail">

                                        ผู้เช่า:
                                        {{ $returnCustomer }}

                                    </div>


                                @if($returnUrl)

                                    </a>

                                @else

                                    </div>

                                @endif

                            @endforeach

                        </div>

                    @else

                        <div class="empty-state">

                            <div class="empty-icon">
                                <i class="fa-regular fa-circle-check"></i>
                            </div>

                            <div class="empty-text">
                                ไม่มีชุดที่ใกล้กำหนดคืนในช่วงนี้
                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- =================================================
                 PENDING PAYMENTS
            ================================================== --}}

            <div class="card">

                <div class="card-header">

                    <div>

                        <h2 class="card-title">
                            การชำระเงินรอตรวจสอบ
                        </h2>

                        <p class="card-description">
                            คลิกรายการเพื่อเปิดสลิปและยืนยันยอด
                        </p>

                    </div>


                    @php

                        $url = $link(
                            'owner.payments.index',
                            ['status' => 'pending']
                        );

                    @endphp


                    @if($url)

                        <a
                            href="{{ $url }}"
                            class="card-link"
                        >
                            ดูทั้งหมด
                        </a>

                    @endif

                </div>


                <div class="card-body">

                    @if(
                        $pendingPayments instanceof \Illuminate\Support\Collection
                        && $pendingPayments->count()
                    )

                        <div class="side-list">

                            @foreach($pendingPayments as $payment)

                                @php

                                    $paymentCustomer =
                                        $payment->customer->name
                                        ?? $payment->customer_name
                                        ?? $payment->booking->customer->name
                                        ?? 'ไม่ระบุลูกค้า';


                                    $paymentAmount =
                                        $payment->amount
                                        ?? $payment->total_amount
                                        ?? 0;


                                    $paymentId =
                                        $payment->payment_id
                                        ?? $payment->id
                                        ?? null;


                                    $paymentUrl = null;


                                    if ($paymentId) {

                                        $paymentUrl =
                                            $link(
                                                'owner.payments.show',
                                                ['payment' => $paymentId]
                                            )
                                            ??
                                            $link(
                                                'owner.payments.show',
                                                [$paymentId]
                                            );
                                    }


                                    /*
                                    | ถ้าไม่มีหน้า payment show
                                    | ให้ fallback ไป booking
                                    */
                                    if (!$paymentUrl) {

                                        $paymentBookingId =
                                            $payment->booking_id
                                            ?? $payment->booking->booking_id
                                            ?? null;


                                        if ($paymentBookingId) {

                                            $paymentUrl =
                                                $link(
                                                    'owner.bookings.show',
                                                    ['booking' => $paymentBookingId]
                                                )
                                                ??
                                                $link(
                                                    'owner.bookings.show',
                                                    [$paymentBookingId]
                                                );
                                        }
                                    }

                                @endphp


                                @if($paymentUrl)

                                    <a
                                        href="{{ $paymentUrl }}"
                                        class="side-item"
                                    >

                                @else

                                    <div class="side-item">

                                @endif


                                    <div class="side-avatar">

                                        <i class="fa-solid fa-receipt"></i>

                                    </div>


                                    <div class="side-content">

                                        <div class="side-title">

                                            {{ $paymentCustomer }}

                                        </div>

                                        <div class="side-meta">

                                            รอตรวจสอบยอดโอนเงิน

                                        </div>

                                    </div>


                                    <div class="side-price">

                                        ฿{{ $money($paymentAmount) }}

                                    </div>


                                @if($paymentUrl)

                                    </a>

                                @else

                                    </div>

                                @endif

                            @endforeach

                        </div>

                    @else

                        <div class="empty-state">

                            <div class="empty-icon">
                                <i class="fa-regular fa-circle-check"></i>
                            </div>

                            <div class="empty-text">
                                ไม่มีรายการชำระเงินที่ค้างตรวจสอบ
                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- =====================================================
             POPULAR DRESSES
        ====================================================== --}}

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">
                        ชุดยอดนิยมสูงสุด
                    </h2>

                    <p class="card-description">
                        คลิกที่ชุดเพื่อเปิดหน้าแก้ไขข้อมูลชุดนั้น
                    </p>

                </div>


                @php
                    $url = $link('owner.dresses.index');
                @endphp


                @if($url)

                    <a
                        href="{{ $url }}"
                        class="card-link"
                    >
                        จัดการชุดทั้งหมด
                    </a>

                @endif

            </div>


            <div class="card-body">

                @if(
                    $popularDresses instanceof \Illuminate\Support\Collection
                    && $popularDresses->count()
                )

                    <div class="dress-list">

                        @foreach($popularDresses->take(6) as $dress)

                            @php

                                $dressName =
                                    $dress->name
                                    ?? $dress->dress_name
                                    ?? $dress->product_name
                                    ?? 'ไม่ระบุชื่อชุด';


                                $dressPrice =
                                    $dress->rental_price
                                    ?? $dress->rent_price
                                    ?? $dress->price
                                    ?? 0;


                                /*
                                |--------------------------------------------------------------------------
                                | IMAGE
                                |--------------------------------------------------------------------------
                                */

                                $dressImage = null;


                                if (
                                    isset($dress->images)
                                    && is_iterable($dress->images)
                                ) {

                                    $images = $dress->images;


                                    if (
                                        is_array($images)
                                        && count($images) > 0
                                    ) {

                                        $firstImg = $images[0];

                                    } elseif (
                                        $images instanceof \Illuminate\Support\Collection
                                        && $images->count() > 0
                                    ) {

                                        $firstImg = $images->first();

                                    } else {

                                        $firstImg = null;

                                    }


                                    if ($firstImg) {

                                        $dressImage =
                                            $firstImg->image_path
                                            ?? $firstImg->url
                                            ?? $firstImg->path
                                            ?? null;
                                    }

                                }


                                if (!$dressImage && isset($dress->image)) {

                                    $dressImage = $dress->image;

                                }


                                if (!$dressImage && isset($dress->image_path)) {

                                    $dressImage = $dress->image_path;

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | DRESS ROUTE
                                |--------------------------------------------------------------------------
                                */

                                $dressId =
                                    $dress->product_id
                                    ?? $dress->dress_id
                                    ?? $dress->id
                                    ?? null;


                                $dressUrl = null;


                                if ($dressId) {

                                    $dressUrl =
                                        $link(
                                            'owner.dresses.show',
                                            ['dress' => $dressId]
                                        )
                                        ??
                                        $link(
                                            'owner.dresses.show',
                                            ['product' => $dressId]
                                        )
                                        ??
                                        $link(
                                            'owner.dresses.show',
                                            ['id' => $dressId]
                                        )
                                        ??
                                        $link(
                                            'owner.dresses.show',
                                            [$dressId]
                                        )
                                        ??
                                        $link(
                                            'owner.dresses.edit',
                                            ['dress' => $dressId]
                                        )
                                        ??
                                        $link(
                                            'owner.dresses.edit',
                                            ['product' => $dressId]
                                        )
                                        ??
                                        $link(
                                            'owner.dresses.edit',
                                            ['id' => $dressId]
                                        )
                                        ??
                                        $link(
                                            'owner.dresses.edit',
                                            [$dressId]
                                        );
                                }

                            @endphp


                            @if($dressUrl)

                                <a
                                    href="{{ $dressUrl }}"
                                    class="dress-item"
                                >

                            @else

                                <div class="dress-item">

                            @endif


                                <div class="dress-image">

                                    @if($dressImage)

                                        @php

                                            $imageUrl =
                                                \Illuminate\Support\Str::startsWith(
                                                    $dressImage,
                                                    ['http://', 'https://']
                                                )
                                                ? $dressImage
                                                : asset(
                                                    'storage/' .
                                                    ltrim(
                                                        $dressImage,
                                                        '/'
                                                    )
                                                );

                                        @endphp


                                        <img
                                            src="{{ $imageUrl }}"
                                            alt="{{ $dressName }}"
                                            loading="lazy"
                                        >

                                    @else

                                        <div class="dress-placeholder">

                                            <i class="fa-solid fa-shirt"></i>

                                        </div>

                                    @endif

                                </div>


                                <div class="dress-info">

                                    <div
                                        class="dress-name"
                                        title="{{ $dressName }}"
                                    >
                                        {{ $dressName }}
                                    </div>


                                    <div class="dress-meta">

                                        <span>
                                            ค่าเช่า
                                        </span>

                                        <span class="dress-price">
                                            ฿{{ $money($dressPrice) }}
                                        </span>

                                    </div>

                                </div>


                            @if($dressUrl)

                                </a>

                            @else

                                </div>

                            @endif

                        @endforeach

                    </div>

                @else

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="fa-solid fa-shirt"></i>
                        </div>

                        <div class="empty-text">
                            ยังไม่มีข้อมูลชุดยอดนิยมในขณะนี้
                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | CLICKABLE BOOKING ROW
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('tr.row-link')
        .forEach(function (row) {

            const url = row.dataset.href;

            if (!url) {
                return;
            }


            row.addEventListener('click', function () {

                window.location.href = url;

            });


            row.addEventListener('auxclick', function (event) {

                if (event.button === 1) {

                    window.open(
                        url,
                        '_blank'
                    );

                }

            });

        });


    /*
    |--------------------------------------------------------------------------
    | REVENUE CHART
    |--------------------------------------------------------------------------
    */

    const canvas =
        document.getElementById('revenueBarChart');


    if (!canvas) {
        return;
    }


    const months =
        @json($monthlyLabels);


    const revenues =
        @json($monthlyData);


    const monthUrl =
        canvas.dataset.monthUrl || '';


    const year =
        canvas.dataset.year || new Date().getFullYear();


    new Chart(canvas, {

        type: 'bar',

        data: {

            labels: months,

            datasets: [

                {

                    label: 'รายได้ (บาท)',

                    data: revenues,

                    backgroundColor: '#5c1d2b',

                    hoverBackgroundColor: '#3b1119',

                    borderRadius: 4,

                    barThickness: 'flex',

                    maxBarThickness: 32

                }

            ]

        },


        options: {

            responsive: true,

            maintainAspectRatio: false,


            /*
            |--------------------------------------------------------------------------
            | CLICK MONTH
            |--------------------------------------------------------------------------
            */

            onClick: function (event, elements) {

                if (
                    !monthUrl
                    || !elements.length
                ) {

                    return;

                }


                const month =
                    elements[0].index + 1;


                const separator =
                    monthUrl.includes('?')
                        ? '&'
                        : '?';


                window.location.href =
                    monthUrl
                    + separator
                    + 'year='
                    + encodeURIComponent(year)
                    + '&month='
                    + encodeURIComponent(month);

            },


            plugins: {

                legend: {
                    display: false
                },


                tooltip: {

                    callbacks: {

                        label: function (context) {

                            const value =
                                context.parsed.y || 0;


                            return (
                                ' รายได้: ฿'
                                + value.toLocaleString(
                                    'th-TH',
                                    {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }
                                )
                            );

                        }

                    }

                }

            },


            scales: {

                x: {

                    grid: {
                        display: false
                    },

                    ticks: {

                        font: {
                            size: 11,
                            family: 'Noto Sans Thai'
                        },

                        color: '#6e5c60'

                    }

                },


                y: {

                    beginAtZero: true,

                    grid: {

                        color: '#e8e2df',

                        borderDash: [
                            4,
                            4
                        ]

                    },


                    ticks: {

                        font: {
                            size: 11,
                            family: 'Noto Sans Thai'
                        },

                        color: '#6e5c60',


                        callback: function (value) {

                            if (value >= 1000) {

                                return (
                                    value / 1000
                                ).toLocaleString(
                                    'th-TH'
                                ) + 'k';

                            }


                            return value;

                        }

                    }

                }

            }

        }

    });

});

</script>

@endpush