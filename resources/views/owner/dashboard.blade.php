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
        --burgundy-900: #3b1119;
        --burgundy-800: #4a1622;
        --burgundy-700: #5c1d2b;
        --burgundy-600: #732537;
        --gold:         #b89053;
        --gold-dark:    #99733d;
        --surface:      #ffffff;
        --background:   #fcfbfa;
        --ink:          #1f1416;
        --muted:        #6e5c60;
        --line:         #e8e2df;
        --radius:       10px;
    }

    * { box-sizing: border-box; }

    .kyrix-dashboard { color: var(--ink); padding: 4px 0; }
    .kyrix-container { max-width: 1400px; margin: 0 auto; }

    /* TOP BAR */
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
        font-family: "Playfair Display", "Noto Sans Thai", serif;
        font-size: 26px;
        font-weight: 700;
        color: var(--burgundy-900);
    }
    .dashboard-heading p { margin: 4px 0 0; color: var(--muted); font-size: 13px; }

    .header-actions { display: flex; gap: 8px; flex-wrap: wrap; }
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
    .dashboard-btn.primary:hover { background: var(--burgundy-800); }
    .dashboard-btn.secondary { 
        background: #fff; 
        color: var(--burgundy-900); 
        border-color: var(--line); 
    }
    .dashboard-btn.secondary:hover { border-color: var(--gold); background: #faf8f7; }

    /* HERO BANNER */
    .owner-hero {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--burgundy-900), var(--burgundy-700));
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
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .owner-hero h2 { margin: 0; font-family: "Playfair Display", "Noto Sans Thai", serif; font-size: 22px; font-weight: 600; }
    .owner-hero p { margin: 6px 0 0; font-size: 13px; color: rgba(255, 255, 255, 0.8); max-width: 550px; line-height: 1.5; }
    .owner-hero-actions { display: flex; gap: 8px; flex-wrap: wrap; position: relative; z-index: 2; }
    .owner-hero-actions .dashboard-btn.primary { background: var(--gold); color: #fff; border: none; }
    .owner-hero-actions .dashboard-btn.primary:hover { background: #a67f47; }
    .owner-hero-actions .dashboard-btn.secondary { background: rgba(255, 255, 255, 0.08); color: #fff; border-color: rgba(255, 255, 255, 0.2); }
    .owner-hero-actions .dashboard-btn.secondary:hover { background: rgba(255, 255, 255, 0.15); }

    /* KPI */
    .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-top { display: flex; align-items: center; justify-content: space-between; }
    .stat-label { color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-icon {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 6px; background: #f4efed; color: var(--burgundy-800); font-size: 14px;
    }
    .stat-value { margin-top: 12px; font-size: 24px; font-weight: 700; color: var(--burgundy-900); letter-spacing: -0.5px; }
    .stat-sub { margin-top: 4px; font-size: 11px; color: var(--muted); }

    /* MAIN GRID */
    .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(320px, 0.9fr); gap: 20px; margin-bottom: 20px; }
    .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); }
    .card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 20px; border-bottom: 1px solid var(--line); }
    .card-title { margin: 0; font-size: 14px; font-weight: 700; color: var(--burgundy-900); }
    .card-description { margin: 2px 0 0; color: var(--muted); font-size: 11px; }
    .card-link { font-size: 12px; color: var(--burgundy-700); text-decoration: none; font-weight: 600; }
    .card-link:hover { text-decoration: underline; }
    .card-body { padding: 20px; }

    /* CHART */
    .chart-card { display: flex; flex-direction: column; }
    .chart-wrapper { height: 260px; position: relative; width: 100%; }

    /* QUICK MENU */
    .quick-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .quick-item {
        display: flex; align-items: center; gap: 10px; padding: 12px;
        background: #fbf9f8; border: 1px solid var(--line); border-radius: 8px;
        color: var(--ink); text-decoration: none; transition: all 0.15s ease;
    }
    .quick-item:hover { background: #fff; border-color: var(--gold); }
    .quick-icon {
        width: 32px; height: 32px; flex: 0 0 32px;
        display: flex; align-items: center; justify-content: center;
        background: #f0e9e7; border-radius: 6px; color: var(--burgundy-800); font-size: 13px;
    }
    .quick-name { font-size: 12px; font-weight: 650; color: var(--burgundy-900); }
    .quick-desc { margin-top: 2px; font-size: 10px; color: var(--muted); }

    /* TABLES */
    .table-wrapper { overflow-x: auto; }
    .kyrix-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .kyrix-table th { text-align: left; padding: 10px 16px; background: #fbf9f8; color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--line); }
    .kyrix-table td { padding: 12px 16px; border-bottom: 1px solid var(--line); color: var(--ink); font-size: 12px; vertical-align: middle; }
    .kyrix-table tr:last-child td { border-bottom: none; }
    .booking-code { font-weight: 650; color: var(--burgundy-900); }
    .customer-name { font-weight: 600; }
    .customer-phone { margin-top: 2px; color: var(--muted); font-size: 11px; }

    /* STATUS BADGES */
    .status-badge { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; letter-spacing: 0.3px; }
    .status-pending { background: #fff8e8; color: #8a6200; border: 1px solid #fce8bd; }
    .status-confirmed { background: #eef4fb; color: #356294; border: 1px solid #d2e3f5; }
    .status-warning { background: #fef0eb; color: #a14e1a; border: 1px solid #fcdbd0; }
    .status-paid { background: #edf7ee; color: #3b6b3f; border: 1px solid #d2edd4; }
    .status-rented { background: #f7e7ea; color: var(--burgundy-800); border: 1px solid #ecd3d7; }
    .status-returning { background: #fdf5ea; color: #9c6d1f; border: 1px solid #fae4c8; }
    .status-returned { background: #edf6f6; color: #366b6c; border: 1px solid #cee8e8; }
    .status-completed { background: #f3efe9; color: #52413b; border: 1px solid #e2dacd; }
    .status-cancelled { background: #f4f4f4; color: #666; border: 1px solid #e0e0e0; }
    .status-default { background: #f4f4f4; color: #666; border: 1px solid #e0e0e0; }

    /* SIDE LIST */
    .side-list { display: flex; flex-direction: column; }
    .side-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--line); }
    .side-item:last-child { border-bottom: 0; padding-bottom: 0; }
    .side-item:first-child { padding-top: 0; }
    .side-avatar {
        width: 34px; height: 34px; flex: 0 0 34px; border-radius: 6px;
        background: #f4efed; color: var(--burgundy-800);
        display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;
    }
    .side-content { min-width: 0; flex: 1; }
    .side-title { font-size: 12px; font-weight: 650; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .side-meta { margin-top: 2px; color: var(--muted); font-size: 11px; }
    .side-price { font-size: 12px; font-weight: 700; color: var(--burgundy-900); white-space: nowrap; }

    /* LOWER GRID */
    .lower-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin-bottom: 20px; }

    /* POPULAR DRESSES GRID */
    .dress-list { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .dress-item { border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; background: #fff; display: flex; flex-direction: column; }
    
    /* ปรับสัดส่วนรูปภาพเป็นแนวตั้ง 3:4 เพื่อให้พอดีกับรูปชุดและแสดงเต็มใบโดยไม่ถูกตัด */
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
    .dress-placeholder { color: var(--muted); font-size: 24px; }
    .dress-info { padding: 12px; display: flex; flex-direction: column; justify-content: space-between; flex: 1; }
    .dress-name { font-size: 12px; font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dress-meta { display: flex; justify-content: space-between; gap: 5px; margin-top: 6px; font-size: 11px; color: var(--muted); }
    .dress-price { font-weight: 700; color: var(--burgundy-900); }

    /* RETURN ALERT */
    .return-item { padding: 10px 0; border-bottom: 1px solid var(--line); }
    .return-item:last-child { border-bottom: 0; padding-bottom: 0; }
    .return-item:first-child { padding-top: 0; }
    .return-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .return-name { font-size: 12px; font-weight: 650; color: var(--ink); }
    .return-date { color: var(--gold-dark); font-size: 11px; font-weight: 700; background: #fbf9f8; padding: 2px 6px; border-radius: 4px; border: 1px solid var(--line); }
    .return-detail { margin-top: 3px; color: var(--muted); font-size: 11px; }

    /* EMPTY */
    .empty-state { padding: 30px 15px; text-align: center; color: var(--muted); }
    .empty-icon { font-size: 22px; margin-bottom: 6px; opacity: 0.5; color: var(--burgundy-700); }
    .empty-text { font-size: 12px; font-weight: 500; }

    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .dashboard-grid, .lower-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .kyrix-dashboard { padding: 12px; }
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
                <span class="eyebrow">KYRIX RENTAL · MANAGEMENT</span>
                <h1>ภาพรวมระบบร้าน</h1>
                <p>ควบคุมและตรวจสอบภาพรวมการเช่าชุดทั้งหมดภายในหน้าเดียว</p>
            </div>
            <div class="header-actions">
                <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="dashboard-btn secondary">
                    <i class="fa-regular fa-calendar"></i> รายการเช่าทั้งหมด
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
                    <i class="fa-solid fa-store"></i> ADMIN PANEL
                </span>
                <h2>ยินดีต้อนรับกลับมา, {{ $ownerName }}</h2>
                <p>ยอดขายวันนี้รวม <strong>฿{{ $money($todayRevenue) }}</strong> และมีรายการรอดำเนินการตรวจสอบทั้งสิ้น <strong>{{ number_format($pendingBookings) }} รายการ</strong></p>
            </div>
            <div class="owner-hero-actions">
                <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="dashboard-btn secondary">
                    <i class="fa-regular fa-clock"></i> ตรวจสอบคิวรอดำเนินการ
                </a>
                <a href="{{ Route::has('owner.reports.index') ? route('owner.reports.index') : '#' }}" class="dashboard-btn primary">
                    <i class="fa-solid fa-chart-line"></i> รายงานสถิติเชิงลึก
                </a>
            </div>
        </div>

        <!-- KPI -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">รายได้เดือนนี้</div>
                    <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <div class="stat-value">฿{{ $money($monthlyRevenue) }}</div>
                <div class="stat-sub">ยอดรับรวมเดือนปัจจุบัน</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">รายได้วันนี้</div>
                    <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
                </div>
                <div class="stat-value">฿{{ $money($todayRevenue) }}</div>
                <div class="stat-sub">ยอดเงินที่เกิดขึ้นในวันนี้</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">กำลังเช่าอยู่</div>
                    <div class="stat-icon"><i class="fa-solid fa-shirt"></i></div>
                </div>
                <div class="stat-value">{{ number_format($activeRentals) }}</div>
                <div class="stat-sub">ชุดที่ถูกสวมใส่อยู่ขณะนี้</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">รอดำเนินการ</div>
                    <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                </div>
                <div class="stat-value">{{ number_format($pendingBookings) }}</div>
                <div class="stat-sub">รายการที่ต้องตรวจสอบด่วน</div>
            </div>
        </div>

        <!-- MAIN GRID (BAR CHART & QUICK MENU) -->
        <div class="dashboard-grid">
            
            <!-- REVENUE BAR CHART -->
            <div class="card chart-card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">สถิติรายได้รายเดือน</h2>
                        <p class="card-description">กราฟเปรียบเทียบยอดรายรับตลอดปีปัจจุบัน</p>
                    </div>
                    <span class="card-link">ปี พ.ศ. {{ now()->year + 543 }}</span>
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
                        <h2 class="card-title">เมนูลัดผู้ดูแลระบบ</h2>
                        <p class="card-description">ทางด่วนเข้าถึงฟังก์ชันสำคัญของร้าน</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="quick-grid">
                        <a href="{{ Route::has('owner.dresses.index') ? route('owner.dresses.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-shirt"></i></div>
                            <div>
                                <div class="quick-name">ชุดทั้งหมด</div>
                                <div class="quick-desc">{{ number_format($totalDresses) }} รายการ</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-regular fa-calendar-days"></i></div>
                            <div>
                                <div class="quick-name">การเช่า</div>
                                <div class="quick-desc">{{ number_format($totalBookings) }} คิว</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.customers.index') ? route('owner.customers.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-users"></i></div>
                            <div>
                                <div class="quick-name">ลูกค้า</div>
                                <div class="quick-desc">{{ number_format($totalCustomers) }} บัญชี</div>
                            </div>
                        </a>
                        <a href="{{ Route::has('owner.payments.index') ? route('owner.payments.index') : '#' }}" class="quick-item">
                            <div class="quick-icon"><i class="fa-solid fa-receipt"></i></div>
                            <div>
                                <div class="quick-name">การชำระเงิน</div>
                                <div class="quick-desc">ตรวจสอบสลิป</div>
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
                            <div class="quick-icon"><i class="fa-solid fa-chart-pie"></i></div>
                            <div>
                                <div class="quick-name">รายงาน</div>
                                <div class="quick-desc">สรุปยอดบัญชี</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT BOOKINGS -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">รายการเช่าล่าสุด</h2>
                    <p class="card-description">ประวัติการทำรายการจองชุดล่าสุดภายในระบบ</p>
                </div>
                <a href="{{ Route::has('owner.bookings.index') ? route('owner.bookings.index') : '#' }}" class="card-link">ดูทั้งหมด &rarr;</a>
            </div>
            <div class="table-wrapper">
                @if($recentBookings instanceof \Illuminate\Support\Collection && $recentBookings->count())
                    <table class="kyrix-table">
                        <thead>
                            <tr>
                                <th>รหัสการเช่า</th>
                                <th>ลูกค้า</th>
                                <th>วันรับชุด</th>
                                <th>วันคืนชุด</th>
                                <th>ยอดรวมทั้งสิ้น</th>
                                <th>สถานะปัจจุบัน</th>
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
                        <div class="empty-text">ยังไม่มีรายการเช่าในขณะนี้</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- LOWER GRID (RETURNS & PAYMENTS) -->
        <div class="lower-grid">
            
            <!-- UPCOMING RETURNS -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">ชุดที่ใกล้ถึงกำหนดคืน</h2>
                        <p class="card-description">รายการที่ต้องติดต่อรับคืนชุดตามกำหนด</p>
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
                                        <div class="return-name"><i class="fa-solid fa-shirt text-muted me-1"></i> {{ $returnDress }}</div>
                                        <div class="return-date">
                                            @if($returnDate)
                                                คืน: {{ \Carbon\Carbon::parse($returnDate)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                    <div class="return-detail">ผู้เช่า: {{ $returnCustomer }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fa-regular fa-circle-check"></i></div>
                            <div class="empty-text">ไม่มีชุดที่ใกล้กำหนดคืนในช่วงนี้</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PENDING PAYMENTS -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">การชำระเงินรอตรวจสอบ</h2>
                        <p class="card-description">สลิปโอนเงินที่รอการยืนยันจากคุณ</p>
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
                                    <div class="side-avatar"><i class="fa-solid fa-receipt"></i></div>
                                    <div class="side-content">
                                        <div class="side-title">{{ $paymentCustomer }}</div>
                                        <div class="side-meta">รอตรวจสอบยอดโอนเงิน</div>
                                    </div>
                                    <div class="side-price">฿{{ $money($paymentAmount) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fa-regular fa-circle-check"></i></div>
                            <div class="empty-text">ไม่มีรายการชำระเงินที่ค้างตรวจสอบ</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- POPULAR DRESSES -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">ชุดยอดนิยมสูงสุด</h2>
                    <p class="card-description">ชุดที่มีสถิติการถูกจองเช่าสูงสุดในร้าน</p>
                </div>
                <a href="{{ Route::has('owner.dresses.index') ? route('owner.dresses.index') : '#' }}" class="card-link">จัดการชุดทั้งหมด</a>
            </div>
            <div class="card-body">
                @if($popularDresses instanceof \Illuminate\Support\Collection && $popularDresses->count())
                    <div class="dress-list">
                        @foreach($popularDresses->take(6) as $dress)
                            @php
                                $dressName = $dress->name ?? $dress->dress_name ?? $dress->product_name ?? 'ไม่ระบุชื่อชุด';
                                $dressPrice = $dress->rental_price ?? $dress->rent_price ?? $dress->price ?? 0;
                                
                                // ระบบดึงรูปภาพครอบคลุมทุกโครงสร้าง Database
                                $dressImage = null;
                                if (isset($dress->images) && is_iterable($dress->images) && count($dress->images) > 0) {
                                    $firstImg = is_array($dress->images) ? $dress->images[0] : $dress->images->first();
                                    $dressImage = $firstImg->image_path ?? $firstImg->url ?? $firstImg->path ?? null;
                                } elseif (isset($dress->image)) {
                                    $dressImage = $dress->image;
                                } elseif (isset($dress->image_path)) {
                                    $dressImage = $dress->image_path;
                                }
                            @endphp
                            <div class="dress-item">
                                <div class="dress-image">
                                    @if($dressImage)
                                        <img src="{{ asset($dressImage) }}" alt="{{ $dressName }}">
                                    @else
                                        <div class="dress-placeholder"><i class="fa-solid fa-shirt"></i></div>
                                    @endif
                                </div>
                                <div class="dress-info">
                                    <div class="dress-name" title="{{ $dressName }}">{{ $dressName }}</div>
                                    <div class="dress-meta">
                                        <span>ค่าเช่า</span>
                                        <span class="dress-price">฿{{ $money($dressPrice) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-solid fa-shirt"></i></div>
                        <div class="empty-text">ยังไม่มีข้อมูลชุดยอดนิยมในขณะนี้</div>
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
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('revenueBarChart');
        if (ctx) {
            const months = @json($monthlyLabels);
            const revenues = @json($monthlyData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'รายได้ (บาท)',
                        data: revenues,
                        backgroundColor: '#5c1d2b',
                        hoverBackgroundColor: '#3b1119',
                        borderRadius: 4,
                        barThickness: 'flex',
                        maxBarThickness: 32,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.y || 0;
                                    return ' รายได้: ฿' + value.toLocaleString('th-TH', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, family: 'Noto Sans Thai' }, color: '#6e5c60' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e8e2df', borderDash: [4, 4] },
                            ticks: { 
                                font: { size: 11, family: 'Noto Sans Thai' }, 
                                color: '#6e5c60',
                                callback: function(value) {
                                    if (value >= 1000) {
                                        return (value / 1000).toLocaleString() + 'k';
                                    }
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush