@extends('layouts.owner')
@section('title', 'ภาพรวมร้าน | KYRIX Admin')

@php
    /*
    |--------------------------------------------------------------------------
    | รองรับข้อมูลจาก Controller
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

    $recentBookings   = $recentBookings   ?? collect();
    $upcomingReturns  = $upcomingReturns  ?? collect();
    $pendingPayments  = $pendingPayments  ?? collect();
    $popularDresses   = $popularDresses   ?? collect();

    $ownerName = $ownerName
        ?? (auth()->check() ? (auth()->user()->name ?? auth()->user()->shop_name ?? null) : null)
        ?? 'เจ้าของร้าน';

    /*
    |--------------------------------------------------------------------------
    | กราฟรายได้
    |--------------------------------------------------------------------------
    */
    $monthlyLabels = $monthlyLabels ?? [
        'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.',
    ];
    $monthlyData = $monthlyData ?? array_fill(0, 12, 0);

    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    */
    $money = function ($value) {
        return number_format((float) $value, 2);
    };

    $statusText = function ($status) {
        return match ($status) {
            'pending'         => 'รอการยืนยัน',
            'confirmed'       => 'ยืนยันแล้ว',
            'waiting_payment' => 'รอชำระเงิน',
            'paid'            => 'ชำระเงินแล้ว',
            'rented'          => 'กำลังเช่า',
            'returning'       => 'รอคืนชุด',
            'returned'        => 'คืนชุดแล้ว',
            'completed'       => 'เสร็จสิ้น',
            'cancelled'       => 'ยกเลิก',
            default           => $status ?: 'ไม่ระบุ'
        };
    };

    $statusClass = function ($status) {
        return match ($status) {
            'pending'         => 'status-pending',
            'confirmed'       => 'status-confirmed',
            'waiting_payment' => 'status-warning',
            'paid'            => 'status-paid',
            'rented'          => 'status-rented',
            'returning'       => 'status-returning',
            'returned'        => 'status-returned',
            'completed'       => 'status-completed',
            'cancelled'       => 'status-cancelled',
            default           => 'status-default'
        };
    };
@endphp

@push('styles')
<style>
    :root {
        --maroon-900: #430d17;
        --maroon-800: #5c1522;
        --maroon-700: #6f1a2b;
        --maroon-600: #832033;
        --gold:       #c79a5c;
        --gold-dark:  #a97f45;
        --rose-bg:    #f7e7ea;
        --rose-text:  #7f2138;
        --cream:      #faf7f4;
        --ink:        #241417;
        --muted:      #8a7a7d;
        --line:       #efe6e4;
    }

    * { box-sizing: border-box; }

    .kyrix-dashboard { color: var(--ink); padding: 0; }
    .kyrix-container { max-width: 100%; margin: 0 auto; }

    /* TOP BAR */
    .dashboard-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }
    .dashboard-heading .eyebrow {
        display: inline-block;
        color: var(--gold-dark);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 7px;
    }
    .dashboard-heading h1 {
        margin: 0;
        font-family: "Playfair Display", "Noto Sans Thai", serif;
        font-size: 30px;
        font-weight: 700;
        color: var(--maroon-900);
    }
    .dashboard-heading p { margin: 7px 0 0; color: var(--muted); font-size: 14px; }

    .header-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .dashboard-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 650;
        transition: .2s ease;
        border: 1px solid transparent;
    }
    .dashboard-btn.primary {
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        color: #fff;
        box-shadow: 0 8px 18px rgba(111, 26, 43, .28);
    }
    .dashboard-btn.primary:hover { transform: translateY(-1px); box-shadow: 0 10px 22px rgba(111, 26, 43, .35); }
    .dashboard-btn.secondary { background: #fff; color: var(--maroon-800); border-color: var(--line); }
    .dashboard-btn.secondary:hover { border-color: var(--gold); background: #fffdfb; }

    /* HERO BANNER */
    .owner-hero {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        padding: 30px 34px;
        margin-bottom: 22px;
        background: radial-gradient(120% 160% at 100% 0%, rgba(199, 154, 92, .35), transparent 55%), linear-gradient(120deg, var(--maroon-800), var(--maroon-900));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }
    .owner-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .14);
        color: var(--gold);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }
    .owner-hero h2 { margin: 0; font-family: "Playfair Display", "Noto Sans Thai", serif; font-size: 24px; }
    .owner-hero p { margin: 8px 0 0; font-size: 13px; color: rgba(255, 255, 255, .78); max-width: 520px; }
    .owner-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 2; }
    .owner-hero-actions .dashboard-btn.primary { background: var(--gold); color: var(--maroon-900); box-shadow: none; }
    .owner-hero-actions .dashboard-btn.primary:hover { background: #d6ac70; }
    .owner-hero-actions .dashboard-btn.secondary { background: rgba(255, 255, 255, .08); color: #fff; border-color: rgba(255, 255, 255, .25); }

    /* KPI */
    .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 18px; }
    .stat-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 20px;
        min-height: 145px;
        box-shadow: 0 3px 18px rgba(111, 26, 43, .04);
        position: relative;
        overflow: hidden;
    }
    .stat-card::after {
        content: "";
        position: absolute;
        right: -35px;
        bottom: -45px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: var(--rose-bg);
    }
    .stat-top { display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 2; }
    .stat-label { color: var(--muted); font-size: 13px; font-weight: 550; }
    .stat-icon {
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 11px; background: var(--rose-bg); color: var(--rose-text); font-size: 16px;
    }
    .stat-value { margin-top: 16px; font-size: 25px; line-height: 1; font-weight: 750; color: var(--maroon-900); position: relative; z-index: 2; }
    .stat-sub { margin-top: 9px; font-size: 12px; color: var(--muted); position: relative; z-index: 2; }

    /* MAIN GRID */
    .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(320px, .85fr); gap: 18px; }
    .card { background: #fff; border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 3px 18px rgba(111, 26, 43, .04); }
    .card-header { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 19px 20px; border-bottom: 1px solid var(--line); }
    .card-title { margin: 0; font-size: 15px; font-weight: 750; color: var(--maroon-900); }
    .card-description { margin: 4px 0 0; color: var(--muted); font-size: 12px; }
    .card-link { font-size: 12px; color: var(--rose-text); text-decoration: none; font-weight: 650; }
    .card-link:hover { text-decoration: underline; }
    .card-body { padding: 20px; }

    /* CHART */
    .chart-card { min-height: 390px; }
    .chart-wrapper { height: 290px; position: relative; }

    /* QUICK MENU */
    .quick-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .quick-item {
        display: flex; align-items: center; gap: 12px; padding: 13px;
        background: var(--cream); border: 1px solid var(--line); border-radius: 12px;
        color: var(--ink); text-decoration: none; transition: .2s ease;
    }
    .quick-item:hover { background: #fff; border-color: var(--gold); transform: translateY(-1px); }
    .quick-icon {
        width: 36px; height: 36px; flex: 0 0 36px;
        display: flex; align-items: center; justify-content: center;
        background: var(--rose-bg); border-radius: 10px; color: var(--rose-text);
    }
    .quick-name { font-size: 12px; font-weight: 700; color: var(--maroon-900); }
    .quick-desc { margin-top: 3px; font-size: 10px; color: var(--muted); }

    /* TABLES */
    .table-wrapper { overflow-x: auto; }
    .kyrix-table { width: 100%; border-collapse: collapse; min-width: 650px; }
    .kyrix-table th { text-align: left; padding: 12px 14px; background: var(--cream); color: var(--muted); font-size: 11px; font-weight: 700; white-space: nowrap; }
    .kyrix-table td { padding: 14px; border-top: 1px solid var(--line); color: #4a3a3d; font-size: 12px; vertical-align: middle; }
    .booking-code { font-weight: 750; color: var(--maroon-900); }
    .customer-name { font-weight: 650; color: #3a2b2e; }
    .customer-phone { margin-top: 3px; color: var(--muted); font-size: 10px; }

    /* STATUS BADGES */
    .status-badge { display: inline-flex; align-items: center; padding: 5px 9px; border-radius: 999px; font-size: 10px; font-weight: 700; white-space: nowrap; }
    .status-pending { background: #fff5dd; color: #9a6b00; }
    .status-confirmed { background: #edf5ff; color: #4773a6; }
    .status-warning { background: #fff0e7; color: #b65e25; }
    .status-paid { background: #eef7ef; color: #4f7e53; }
    .status-rented { background: var(--rose-bg); color: var(--rose-text); }
    .status-returning { background: #fdf1e3; color: var(--gold-dark); }
    .status-returned { background: #eef7f7; color: #477f80; }
    .status-completed { background: #f4ece2; color: var(--maroon-800); }
    .status-cancelled { background: #f5f5f5; color: #888; }
    .status-default { background: #f5f5f5; color: #777; }

    /* SIDE LIST */
    .side-list { display: flex; flex-direction: column; }
    .side-item { display: flex; align-items: center; gap: 12px; padding: 13px 0; border-bottom: 1px solid var(--line); }
    .side-item:last-child { border-bottom: 0; }
    .side-avatar {
        width: 40px; height: 40px; flex: 0 0 40px; border-radius: 11px;
        background: var(--rose-bg); color: var(--rose-text);
        display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 750;
    }
    .side-content { min-width: 0; flex: 1; }
    .side-title { font-size: 12px; font-weight: 700; color: #3a2b2e; }
    .side-meta { margin-top: 4px; color: var(--muted); font-size: 10px; }
    .side-price { font-size: 12px; font-weight: 750; color: var(--maroon-900); white-space: nowrap; }

    /* LOWER GRID */
    .lower-grid { display: grid; grid-template-columns: 1.2fr .8fr; gap: 18px; margin-top: 18px; }

    /* POPULAR DRESSES GRID & IMAGES FIX */
    .dress-list { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .dress-item { border: 1px solid var(--line); border-radius: 13px; overflow: hidden; background: #fff; }
    .dress-image {
        height: 140px;
        background: var(--cream);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .dress-image img { width: 100%; height: 100%; object-fit: cover; }
    .dress-placeholder { color: var(--muted); font-size: 26px; }
    .dress-info { padding: 12px; }
    .dress-name { font-size: 12px; font-weight: 700; color: #3a2b2e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dress-meta { display: flex; justify-content: space-between; gap: 5px; margin-top: 6px; font-size: 10px; color: var(--muted); }

    /* RETURN ALERT */
    .return-item { padding: 13px 0; border-bottom: 1px solid var(--line); }
    .return-item:last-child { border-bottom: 0; }
    .return-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .return-name { font-size: 12px; font-weight: 700; color: #3a2b2e; }
    .return-date { color: var(--gold-dark); font-size: 10px; font-weight: 700; }
    .return-detail { margin-top: 5px; color: var(--muted); font-size: 10px; }

    /* EMPTY */
    .empty-state { padding: 35px 15px; text-align: center; color: #c2aeb1; }
    .empty-icon { font-size: 25px; margin-bottom: 9px; opacity: .55; color: var(--rose-text); }
    .empty-text { font-size: 12px; }

    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .dashboard-grid, .lower-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .kyrix-dashboard { padding: 18px; }
        .dashboard-header { align-items: flex-start; flex-direction: column; }
        .stats-grid, .quick-grid, .dress-list { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="kyrix-dashboard">
    <div class="kyrix-container">

        <!-- HEADER -->
        <div class="dashboard-header">
            <div class="dashboard-heading">
                <span class="eyebrow">KYRIX RENTAL · ADMIN</span>
                <h1>ภาพรวมร้าน</h1>
                <p>จัดการและติดตามการเช่าชุดของร้านคุณในหน้าเดียว</p>
            </div>
            <div class="header-actions">
                <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="dashboard-btn secondary">
                    <i class="fa-regular fa-calendar"></i> รายการเช่า
                </a>
                <a href="{{ Route::has('owner.dresses.create') ? route('owner.dresses.create') : '#' }}" class="dashboard-btn primary">
                    <i class="fa-solid fa-plus"></i> เพิ่มชุดใหม่
                </a>
            </div>
        </div>

        <!-- WELCOME HERO -->
        <div class="owner-hero">
            <div>
                <span class="owner-hero-badge">
                    <i class="fa-solid fa-store"></i> OWNER PANEL
                </span>
                <h2>ยินดีต้อนรับกลับมา, {{ $ownerName }}</h2>
                <p>วันนี้มีรายได้ ฿{{ $money($todayRevenue) }} และมีรายการรอดำเนินการ {{ number_format($pendingBookings) }} รายการที่ควรตรวจสอบ</p>
            </div>
            <div class="owner-hero-actions">
                <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="dashboard-btn secondary">
                    <i class="fa-regular fa-clock"></i> รายการรอดำเนินการ
                </a>
                <a href="{{ Route::has('owner.reports.index') ? route('owner.reports.index') : '#' }}" class="dashboard-btn primary">
                    <i class="fa-solid fa-chart-column"></i> ดูรายงานร้าน
                </a>
            </div>
        </div>

        <!-- KPI -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">รายได้เดือนนี้</div>
                    <div class="stat-icon"><i class="fa-solid fa-baht-sign"></i></div>
                </div>
                <div class="stat-value">฿{{ $money($monthlyRevenue) }}</div>
                <div class="stat-sub">รายรับรวมของเดือนปัจจุบัน</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">รายได้วันนี้</div>
                    <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
                </div>
                <div class="stat-value">฿{{ $money($todayRevenue) }}</div>
                <div class="stat-sub">รายรับที่เกิดขึ้นวันนี้</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">กำลังเช่า</div>
                    <div class="stat-icon"><i class="fa-solid fa-shirt"></i></div>
                </div>
                <div class="stat-value">{{ number_format($activeRentals) }}</div>
                <div class="stat-sub">ชุดที่อยู่ระหว่างการเช่า</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">รอการดำเนินการ</div>
                    <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                </div>
                <div class="stat-value">{{ number_format($pendingBookings) }}</div>
                <div class="stat-sub">รายการที่ต้องตรวจสอบ</div>
            </div>
        </div>

        <!-- MAIN GRID (BAR CHART & QUICK MENU) -->
        <div class="dashboard-grid">
            
            <!-- REVENUE BAR CHART -->
            <div class="card chart-card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">รายได้ของร้าน (กราฟแท่ง)</h2>
                        <p class="card-description">ภาพรวมรายได้รายเดือนประจำปี</p>
                    </div>
                    <span class="card-link">ปี {{ now()->year + 543 }}</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="revenueBarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- QUICK MENU -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">เมนูจัดการ</h2>
                        <p class="card-description">ทางลัดสำหรับเจ้าของร้าน</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="quick-grid">
                        <a href="{{ Route::has('owner.dresses.index') ? route('owner.dresses.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-shirt"></i></div>
                            <div>
                                <div class="quick-name">ชุดทั้งหมด</div>
                                <div class="quick-desc">{{ number_format($totalDresses) }} ชุด</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-regular fa-calendar-days"></i></div>
                            <div>
                                <div class="quick-name">รายการเช่า</div>
                                <div class="quick-desc">{{ number_format($totalBookings) }} รายการ</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.customers.index') ? route('owner.customers.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-users"></i></div>
                            <div>
                                <div class="quick-name">ลูกค้า</div>
                                <div class="quick-desc">{{ number_format($totalCustomers) }} คน</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.payments.index') ? route('owner.payments.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-receipt"></i></div>
                            <div>
                                <div class="quick-name">การชำระเงิน</div>
                                <div class="quick-desc">ตรวจสอบรายการ</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.returns.index') ? route('owner.returns.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-rotate-left"></i></div>
                            <div>
                                <div class="quick-name">รับคืนชุด</div>
                                <div class="quick-desc">ตรวจสภาพชุด</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.reports.index') ? route('owner.reports.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-chart-column"></i></div>
                            <div>
                                <div class="quick-name">รายงาน</div>
                                <div class="quick-desc">รายงานร้าน</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT BOOKINGS -->
        <div class="card" style="margin-top:18px;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">รายการเช่าล่าสุด</h2>
                    <p class="card-description">รายการเช่าที่เกิดขึ้นล่าสุด</p>
                </div>
                <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="card-link">ดูทั้งหมด</a>
            </div>
            <div class="table-wrapper">
                @if($recentBookings instanceof \Illuminate\Support\Collection && $recentBookings->count())
                    <table class="kyrix-table">
                        <thead>
                            <tr>
                                <th>เลขที่เช่า</th>
                                <th>ลูกค้า</th>
                                <th>วันรับ</th>
                                <th>วันคืน</th>
                                <th>ยอดรวม</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <div class="booking-code">
                                            {{ $booking->booking_code ?? $booking->code ?? ('#' . ($booking->booking_id ?? $booking->id ?? '-')) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="customer-name">
                                            {{ $booking->customer->name ?? $booking->customer_name ?? $booking->user->name ?? '-' }}
                                        </div>
                                        @if(isset($booking->customer->phone) || isset($booking->phone))
                                            <div class="customer-phone">
                                                {{ $booking->customer->phone ?? $booking->phone ?? '' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ !empty($booking->start_date ?? $booking->rental_start) ? \Carbon\Carbon::parse($booking->start_date ?? $booking->rental_start)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>
                                        {{ !empty($booking->end_date ?? $booking->rental_end) ? \Carbon\Carbon::parse($booking->end_date ?? $booking->rental_end)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>
                                        <strong>฿{{ $money($booking->total_amount ?? $booking->total ?? $booking->amount ?? 0) }}</strong>
                                    </td>
                                    <td>
                                        @php $bookingStatus = $booking->status ?? 'pending'; @endphp
                                        <span class="status-badge {{ $statusClass($bookingStatus) }}">{{ $statusText($bookingStatus) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
                        <div class="empty-text">ยังไม่มีรายการเช่า</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- LOWER GRID -->
        <div class="lower-grid">
            
            <!-- UPCOMING RETURNS -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">ชุดที่ใกล้ถึงกำหนดคืน</h2>
                        <p class="card-description">รายการที่เจ้าของร้านควรติดตาม</p>
                    </div>
                    <a href="{{ Route::has('owner.returns.index') ? route('owner.returns.index') : '#' }}" class="card-link">ดูทั้งหมด</a>
                </div>
                <div class="card-body">
                    @if($upcomingReturns instanceof \Illuminate\Support\Collection && $upcomingReturns->count())
                        <div class="side-list">
                            @foreach($upcomingReturns as $return)
                                @php
                                    $returnCustomer = $return->customer->name ?? $return->customer_name ?? $return->user->name ?? 'ไม่ระบุลูกค้า';
                                    $returnDress = $return->dress->name ?? $return->dress_name ?? 'ไม่ระบุชุด';
                                    $returnDate = $return->return_date ?? $return->end_date ?? $return->rental_end;
                                @endphp
                                <div class="return-item">
                                    <div class="return-top">
                                        <div class="return-name">{{ $returnDress }}</div>
                                        <div class="return-date">
                                            @if($returnDate)
                                                {{ \Carbon\Carbon::parse($returnDate)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                    <div class="return-detail">ลูกค้า: {{ $returnCustomer }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="empty-text">ไม่มีชุดที่ใกล้ถึงกำหนดคืน</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PENDING PAYMENTS -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">การชำระเงินที่ต้องตรวจสอบ</h2>
                        <p class="card-description">รายการที่รอเจ้าของร้านตรวจสอบ</p>
                    </div>
                    <a href="{{ Route::has('owner.payments.index') ? route('owner.payments.index') : '#' }}" class="card-link">ดูทั้งหมด</a>
                </div>
                <div class="card-body">
                    @if($pendingPayments instanceof \Illuminate\Support\Collection && $pendingPayments->count())
                        <div class="side-list">
                            @foreach($pendingPayments as $payment)
                                @php
                                    $paymentCustomer = $payment->customer->name ?? $payment->customer_name ?? $payment->booking->customer->name ?? 'ไม่ระบุลูกค้า';
                                    $paymentAmount = $payment->amount ?? $payment->total_amount ?? 0;
                                @endphp
                                <div class="side-item">
                                    <div class="side-avatar"><i class="fa-solid fa-baht-sign"></i></div>
                                    <div class="side-content">
                                        <div class="side-title">{{ $paymentCustomer }}</div>
                                        <div class="side-meta">รอตรวจสอบการชำระเงิน</div>
                                    </div>
                                    <div class="side-price">฿{{ $money($paymentAmount) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fa-regular fa-circle-check"></i></div>
                            <div class="empty-text">ไม่มีรายการที่รอตรวจสอบ</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- POPULAR DRESSES (FIXED IMAGE & PRODUCTION READY) -->
        <div class="card" style="margin-top:18px;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">ชุดที่มีการเช่าสูง</h2>
                    <p class="card-description">ชุดที่ได้รับความนิยมสูงสุดจากลูกค้า</p>
                </div>
                <a href="{{ Route::has('owner.dresses.index') ? route('owner.dresses.index') : '#' }}" class="card-link">จัดการชุด</a>
            </div>
            <div class="card-body">
                @if($popularDresses instanceof \Illuminate\Support\Collection && $popularDresses->count())
                    <div class="dress-list">
                        @foreach($popularDresses->take(6) as $dress)
                            @php
                                $dressName = $dress->name ?? $dress->dress_name ?? $dress->product_name ?? 'ไม่ระบุชื่อชุด';
                                $dressPrice = $dress->rental_price ?? $dress->rent_price ?? $dress->price ?? 0;
                                
                                // ระบบดึงรูปภาพครอบคลุมทุกรูปแบบโครงสร้าง Database ใน Laravel
                                $dressImage = null;
                                if (isset($dress->images) && $dress->images->count() > 0) {
                                    $dressImage = $dress->images->first()->image_path ?? $dress->images->first()->url ?? null;
                                } elseif (!empty($dress->image)) {
                                    $dressImage = $dress->image;
                                } elseif (!empty($dress->image_path)) {
                                    $dressImage = $dress->image_path;
                                }

                                $rentCount = $dress->bookings_count ?? $dress->rental_count ?? 0;
                            @endphp
                            <div class="dress-item">
                                <div class="dress-image">
                                    @if($dressImage)
                                        {{-- ตรวจสอบว่าเป็น URL ภายนอกหรืออยู่ใน Storage --}}
                                        <img src="{{ Str::startsWith($dressImage, ['http://', 'https://']) ? $dressImage : asset('storage/' . ltrim($dressImage, '/')) }}" alt="{{ $dressName }}">
                                    @else
                                        <div class="dress-placeholder"><i class="fa-solid fa-shirt"></i></div>
                                    @endif
                                </div>
                                <div class="dress-info">
                                    <div class="dress-name" title="{{ $dressName }}">{{ $dressName }}</div>
                                    <div class="dress-meta">
                                        <span>฿{{ $money($dressPrice) }}</span>
                                        <span>เช่า {{ $rentCount }} ครั้ง</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-solid fa-shirt"></i></div>
                        <div class="empty-text">ยังไม่มีข้อมูลชุดยอดนิยม</div>
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
    const canvas = document.getElementById('revenueBarChart');
    if (!canvas) { return; }

    const labels = @json($monthlyLabels);
    const values = @json($monthlyData);
    const ctx = canvas.getContext('2d');

    // สร้างกราฟแท่ง (Bar Chart) ธีมสีเบอร์กันดีและทอง
    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'รายได้ (บาท)',
                data: values,
                backgroundColor: '#6f1a2b',
                hoverBackgroundColor: '#c79a5c',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#430d17',
                    titleColor: '#f7e7ea',
                    bodyColor: '#fff',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            return ' รายได้: ฿' + Number(context.raw || 0).toLocaleString('th-TH', { minimumFractionDigits: 2 });
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#8a7a7d', font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0e6e4' },
                    ticks: {
                        color: '#8a7a7d',
                        font: { size: 10 },
                        callback: function (value) {
                            return '฿' + Number(value).toLocaleString('th-TH');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush