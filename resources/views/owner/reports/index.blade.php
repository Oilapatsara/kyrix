@extends('layouts.owner')

@section('title', 'รายงานผลประกอบการ | KYRIX Admin')

@push('styles')
<!-- ฟอนต์ Noto Sans Thai -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --maroon-900: #430d17;
        --maroon-700: #6f1a2b;
        --gold:       #c79a5c;
        --gold-light: #f4ebd0;
        --gold-dark:  #a97f45;
        --rose-bg:    #fcf6f7;
        --rose-text:  #7f2138;
        --ink:        #1e1013;
        --muted:      #7c6e71;
        --line:       #eae2e1;
        --bg-body:    #f8f5f5;
        --card-bg:    #ffffff;
    }

    /* บังคับใช้ฟอนต์และปรับระบบแสงเงา */
    .dashboard-wrapper, .dashboard-wrapper * {
        font-family: 'Prompt', sans-serif !important;
        box-sizing: border-box;
    }

    .dashboard-wrapper {
        max-width: 1360px;
        margin: 0 auto;
        padding: 24px 28px 60px;
        color: var(--ink);
    }

    /* --- Header ส่วนหัวหน้าจอ --- */
    .dash-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 26px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--line);
    }
    .dash-title h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--maroon-900);
        margin: 0 0 5px;
        letter-spacing: -0.5px;
    }
    .dash-title p {
        font-size: 13.5px;
        color: var(--muted);
        margin: 0;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        color: #fff;
        border: none;
        padding: 11px 22px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(111, 26, 43, 0.18);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(111, 26, 43, 0.28);
    }
    .btn-action svg { width: 18px; height: 18px; }

    /* --- KPI Metric Cards (3 กล่องบน) --- */
    .kpi-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 22px;
        margin-bottom: 26px;
    }
    @media (min-width: 768px) {
        .kpi-grid { grid-template-columns: repeat(3, 1fr); }
    }
    .kpi-card {
        background: var(--card-bg);
        border: 1px solid #efe8e6;
        border-radius: 16px;
        padding: 24px 28px;
        position: relative;
        box-shadow: 0 3px 14px rgba(67, 13, 23, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(67, 13, 23, 0.07);
    }
    .kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .kpi-label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }
    .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .kpi-icon.maroon { background: #fdf2f4; color: var(--maroon-700); }
    .kpi-icon.gold { background: #fbf6ec; color: var(--gold-dark); }
    .kpi-icon.green { background: #ecfdf5; color: #059669; }
    
    .kpi-icon svg { width: 22px; height: 22px; }
    .kpi-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--ink);
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* --- Charts Section (2 กล่องกราฟล่าง) --- */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 26px;
    }
    @media (min-width: 1024px) {
        .charts-grid { grid-template-columns: 1fr 1fr; }
    }
    .chart-card {
        background: var(--card-bg);
        border: 1px solid #efe8e6;
        border-radius: 16px;
        padding: 26px 30px;
        box-shadow: 0 3px 14px rgba(67, 13, 23, 0.03);
    }
    .chart-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--line);
    }
    .chart-header svg {
        width: 22px;
        height: 22px;
        color: var(--maroon-700);
    }
    .chart-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--maroon-900);
        margin: 0;
    }
    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }

    /* --- กราฟแนวโน้มรายได้ (Revenue Trend Card) --- */
    .trend-card {
        background: var(--card-bg);
        border: 1px solid #efe8e6;
        border-radius: 18px;
        padding: 26px 30px;
        margin-bottom: 26px;
        box-shadow: 0 3px 16px rgba(67, 13, 23, 0.03);
    }
    .trend-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--line);
    }
    .trend-title-area {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .trend-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #fdf2f4;
        color: var(--maroon-700);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ebdcd9;
    }
    .trend-icon-box svg { width: 22px; height: 22px; }
    .trend-title-area h3 {
        font-size: 17px;
        font-weight: 800;
        color: var(--maroon-900);
        margin: 0;
    }
    .trend-title-area p {
        font-size: 12.5px;
        color: var(--muted);
        margin: 2px 0 0;
    }
    .toggle-group {
        display: inline-flex;
        background: #f4edea;
        padding: 4px;
        border-radius: 12px;
        border: 1px solid #ebdcd9;
        gap: 4px;
    }
    .toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        background: transparent;
        padding: 8px 18px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .toggle-btn svg { width: 15px; height: 15px; }
    .toggle-btn:hover {
        color: var(--maroon-900);
    }
    .toggle-btn.active {
        background: #ffffff;
        color: var(--maroon-900);
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(67, 13, 23, 0.08);
    }
    .trend-stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        background: #faf6f5;
        border: 1px solid #f0e6e4;
        border-radius: 14px;
        padding: 16px 24px;
        margin-bottom: 22px;
    }
    @media (max-width: 900px) {
        .trend-stats-bar {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .trend-stat-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-right: 14px;
        border-right: 1px solid var(--line);
    }
    .trend-stat-item:last-child {
        border-right: none;
    }
    .trend-stat-label {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .trend-stat-val {
        font-size: 18px;
        font-weight: 800;
    }
    .text-maroon { color: var(--maroon-900); }
    .text-gold { color: var(--gold-dark); }
    .text-green { color: #059669; }
    .trend-chart-container {
        position: relative;
        height: 330px;
        width: 100%;
    }

    /* --- ตารางสรุปรายได้รายเดือน (Monthly Table Card) --- */
    .table-card {
        background: var(--card-bg);
        border: 1px solid #efe8e6;
        border-radius: 18px;
        padding: 26px 30px;
        margin-top: 26px;
        box-shadow: 0 3px 16px rgba(67, 13, 23, 0.03);
    }
    .table-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--line);
    }
    .table-card-header svg {
        width: 22px;
        height: 22px;
        color: var(--maroon-700);
    }
    .table-card-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--maroon-900);
        margin: 0;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
        text-align: left;
    }
    .report-table th {
        background: #faf6f5;
        color: var(--muted);
        font-weight: 700;
        font-size: 12px;
        padding: 13px 18px;
        border-bottom: 2px solid var(--line);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .report-table td {
        padding: 13px 18px;
        border-bottom: 1px solid #f2eceb;
        color: var(--ink);
    }
    .report-table tr:hover td {
        background: #fdfbfb;
    }
    .report-table tr.highlight-row td {
        background: #fffcf9;
    }
    .report-table tfoot td {
        border-top: 2px solid var(--maroon-900);
        border-bottom: none;
        background: #faf6f5;
        padding: 15px 18px;
    }
    .month-bullet {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        margin-right: 8px;
    }
    .month-bullet.active {
        background: var(--maroon-700);
    }
    .badge-success {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .badge-neutral {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: #f1f5f9;
        color: #64748b;
    }

    /* --- Print Header (ซ่อนไว้ปกติ จะแสดงเฉพาะตอนสั่งพิมพ์) --- */
    .print-only-header {
        display: none;
    }

    /* --- สไตล์สำหรับสั่งพิมพ์เอกสาร (Print Layout สวยงาม หรูหรา สบายตา พอดี 1 หน้า A4) --- */
    @page {
        size: A4 portrait;
        margin: 7mm 10mm;
    }

    @media print {
        html, body {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            color: #1e1013 !important;
            font-family: 'Prompt', sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ซ่อนเมนู แถบด้านข้าง และปุ่มต่างๆ */
        header, footer, aside, nav, .admin-header, .admin-footer, .flash-container,
        .dash-header, .btn-action, .toggle-group, .no-print {
            display: none !important;
        }

        main, .admin-main, .admin-container, .dashboard-wrapper {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* หัวจดหมายสำหรับพิมพ์ (Executive Letterhead) */
        .print-only-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-end !important;
            padding-bottom: 7px !important;
            margin-bottom: 10px !important;
            border-bottom: 2.5px solid #6f1a2b !important;
        }
        .print-brand h2 {
            font-size: 18px !important;
            font-weight: 800 !important;
            color: #430d17 !important;
            margin: 0 !important;
            letter-spacing: 0.5px !important;
        }
        .print-brand p {
            font-size: 10.5px !important;
            color: #6f1a2b !important;
            margin: 2px 0 0 !important;
            font-weight: 500 !important;
        }
        .print-meta {
            text-align: right !important;
            font-size: 10px !important;
            color: #444 !important;
            line-height: 1.4 !important;
        }

        /* กล่อง KPI 3 กล่อง (โปร่งสบาย ตัวเลขคมชัด) */
        .kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 12px !important;
            margin-bottom: 12px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .kpi-card {
            padding: 10px 16px !important;
            border-radius: 10px !important;
            border: 1px solid #ebdcd9 !important;
            background: #fffcfc !important;
            box-shadow: none !important;
        }
        .kpi-top {
            margin-bottom: 4px !important;
        }
        .kpi-label {
            font-size: 10px !important;
            letter-spacing: 0.5px !important;
            color: #7c6e71 !important;
        }
        .kpi-icon {
            width: 28px !important;
            height: 28px !important;
            border-radius: 8px !important;
        }
        .kpi-icon svg {
            width: 15px !important;
            height: 15px !important;
        }
        .kpi-value {
            font-size: 19px !important;
            font-weight: 800 !important;
        }

        /* การ์ดกราฟแท่งแนวโน้มรายได้ (Trend Card) */
        .trend-card {
            padding: 12px 18px !important;
            border-radius: 12px !important;
            margin-bottom: 12px !important;
            border: 1px solid #ebdcd9 !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .trend-header {
            margin-bottom: 8px !important;
            padding-bottom: 6px !important;
        }
        .trend-title-area {
            gap: 10px !important;
        }
        .trend-icon-box {
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
        }
        .trend-icon-box svg {
            width: 16px !important;
            height: 16px !important;
        }
        .trend-title-area h3 {
            font-size: 13.5px !important;
            color: #430d17 !important;
            font-weight: 800 !important;
        }
        .trend-title-area p {
            font-size: 10px !important;
            color: #7c6e71 !important;
        }
        .trend-stats-bar {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 10px !important;
            padding: 8px 16px !important;
            margin-bottom: 10px !important;
            border-radius: 8px !important;
            background: #faf6f5 !important;
            border: 1px solid #ebdcd9 !important;
        }
        .trend-stat-item {
            gap: 2px !important;
            padding-right: 8px !important;
            border-right: 1px solid #ebdcd9 !important;
        }
        .trend-stat-item:last-child {
            border-right: none !important;
        }
        .trend-stat-label {
            font-size: 9.5px !important;
            font-weight: 700 !important;
        }
        .trend-stat-val {
            font-size: 14.5px !important;
            font-weight: 800 !important;
        }
        .trend-chart-container {
            height: 175px !important;
            max-height: 175px !important;
        }

        /* กล่อง 2 กราฟ (สัดส่วนรายได้ & ชุดยอดนิยม) */
        .charts-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 12px !important;
            margin-bottom: 12px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .chart-card {
            padding: 12px 18px !important;
            border-radius: 12px !important;
            border: 1px solid #ebdcd9 !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .chart-header {
            margin-bottom: 8px !important;
            padding-bottom: 6px !important;
            gap: 8px !important;
        }
        .chart-header svg {
            width: 16px !important;
            height: 16px !important;
        }
        .chart-header h3 {
            font-size: 13px !important;
            color: #430d17 !important;
            font-weight: 800 !important;
        }
        #topDressesLegend {
            display: none !important;
        }
        .chart-container {
            height: 170px !important;
            max-height: 170px !important;
        }

        /* ตารางสรุปรายได้รายเดือน */
        .table-card {
            padding: 12px 18px !important;
            border-radius: 12px !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            border: 1px solid #ebdcd9 !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .table-card-header {
            margin-bottom: 8px !important;
            padding-bottom: 6px !important;
            gap: 8px !important;
        }
        .table-card-header svg {
            width: 16px !important;
            height: 16px !important;
        }
        .table-card-header h3 {
            font-size: 13px !important;
            color: #430d17 !important;
            font-weight: 800 !important;
        }
        .table-card-header p {
            display: none !important;
        }
        .report-table {
            font-size: 10.5px !important;
        }
        .report-table th {
            padding: 5px 10px !important;
            font-size: 10px !important;
            background: #faf6f5 !important;
            font-weight: 700 !important;
        }
        .report-table td {
            padding: 4.8px 10px !important;
            font-size: 10px !important;
        }
        .report-table tfoot td {
            padding: 6px 10px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            background: #faf6f5 !important;
            color: #430d17 !important;
        }
        .month-bullet {
            width: 6px !important;
            height: 6px !important;
            margin-right: 5px !important;
        }
        .badge-success, .badge-neutral {
            padding: 2px 7px !important;
            font-size: 9px !important;
            border-radius: 4px !important;
        }

        /* บังคับ canvas ไม่ให้ดันความสูงเกินคอนเทนเนอร์ */
        canvas {
            max-height: 100% !important;
            width: 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    
    <!-- ส่วนหัวเฉพาะตอนพิมพ์เอกสาร (Executive Print Letterhead) -->
    <div class="print-only-header">
        <div class="print-brand">
            <h2>KYRIX DRESS RENTAL</h2>
            <p>รายงานสรุปผลประกอบการและสถิติภาพรวมร้าน (Executive Analytics Report)</p>
        </div>
        <div class="print-meta">
            <div><strong>ปีข้อมูล:</strong> พ.ศ. {{ $currentYear + 543 }} ({{ $currentYear }})</div>
            <div><strong>วันที่พิมพ์:</strong> {{ date('d/m/Y H:i') }} น.</div>
            <div><strong>ผู้จัดพิมพ์:</strong> {{ auth()->user()->name ?? 'ผู้บริหารร้าน' }}</div>
        </div>
    </div>

    <!-- Header หน้าจอปกติ -->
    <div class="dash-header">
        <div class="dash-title">
            <h1>รายงานผลประกอบการและสถิติ</h1>
            <p>วิเคราะห์ภาพรวมรายได้ ยอดจอง และความนิยมของชุดภายในร้าน</p>
        </div>
        <button onclick="window.print()" class="btn-action no-print">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            พิมพ์รายงานผู้บริหาร
        </button>
    </div>

    <!-- KPI Metric Cards (3 การ์ดบน) -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-top">
                <p class="kpi-label">รายได้รวมทั้งหมด</p>
                <div class="kpi-icon maroon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="kpi-value">฿{{ number_format($totalRevenue ?? 0, 2) }}</p>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <p class="kpi-label">รายได้เดือนนี้</p>
                <div class="kpi-icon gold">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <p class="kpi-value">฿{{ number_format($monthRevenue ?? 0, 2) }}</p>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <p class="kpi-label">เงินมัดจำค้ำประกันคงค้าง</p>
                <div class="kpi-icon green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <p class="kpi-value">฿{{ number_format($activeDeposits ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- กราฟแนวโน้มรายได้ (เลือกดูได้ทั้ง รายวัน และ รายเดือน) -->
    <div class="trend-card">
        <div class="trend-header">
            <div class="trend-title-area">
                <div class="trend-icon-box">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h3 id="trendChartTitle">สถิติรายได้รายวัน (30 วันล่าสุด)</h3>
                    <p id="trendChartSubtitle">แสดงกราฟแท่งรายได้แยกตามรายวันย้อนหลัง 30 วัน</p>
                </div>
            </div>

            <!-- Segmented Switch: รายวัน / รายเดือน -->
            <div class="toggle-group">
                <button type="button" class="toggle-btn active" id="btnViewDaily" onclick="switchRevenuePeriod('daily')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    รายวัน (30 วันล่าสุด)
                </button>
                <button type="button" class="toggle-btn" id="btnViewMonthly" onclick="switchRevenuePeriod('monthly')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    รายเดือน (ปี {{ $currentYear }})
                </button>
            </div>
        </div>

        <!-- Quick Summary Stats Bar -->
        <div class="trend-stats-bar">
            <div class="trend-stat-item">
                <span class="trend-stat-label">ยอดรวมในช่วงที่เลือก</span>
                <span class="trend-stat-val text-maroon" id="statPeriodTotal">฿0.00</span>
            </div>
            <div class="trend-stat-item">
                <span class="trend-stat-label" id="statAvgLabel">เฉลี่ยต่อวัน</span>
                <span class="trend-stat-val text-gold" id="statPeriodAvg">฿0.00</span>
            </div>
            <div class="trend-stat-item">
                <span class="trend-stat-label" id="statPeakLabel">วันที่รายได้สูงสุด</span>
                <span class="trend-stat-val text-green" id="statPeriodPeak">-</span>
            </div>
            <div class="trend-stat-item">
                <span class="trend-stat-label">จำนวนรายการชำระ</span>
                <span class="trend-stat-val" style="color: var(--ink);" id="statPeriodCount">0 รายการ</span>
            </div>
        </div>

        <div class="trend-chart-container">
            <canvas id="revenueTrendChart"></canvas>
        </div>
    </div>

    <!-- Charts Section (2 กราฟหลักล่าง) -->
    <div class="charts-grid">
        
        <!-- กราฟที่ 1: สัดส่วนรายได้ตามประเภทชุด -->
        <div class="chart-card">
            <div class="chart-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                <h3>สัดส่วนรายได้ตามประเภทชุด</h3>
            </div>
            <div class="chart-container">
                <canvas id="categoryRevenueChart"></canvas>
            </div>
        </div>

        <!-- กราฟที่ 2: ชุดยอดนิยมสูงสุด -->
        <div class="chart-card">
            <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3>ชุดยอดนิยมสูงสุด (จำนวนครั้งที่เช่า)</h3>
                </div>
                <div id="topDressesLegend" style="display: flex; gap: 8px; flex-wrap: wrap; font-size: 11px;"></div>
            </div>
            <div class="chart-container">
                <canvas id="topDressesChart"></canvas>
            </div>
        </div>

    </div>

    <!-- ตารางสรุปภาพรวมรายได้รายเดือน -->
    <div class="table-card">
        <div class="table-card-header">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <div>
                <h3>ตารางสรุปรายได้และสถิติการเช่ารายเดือน (ประจำปี {{ $currentYear }})</h3>
                <p style="font-size: 12px; color: var(--muted); margin: 2px 0 0;">รายละเอียดสถิติยอดเงินที่อนุมัติแล้วและจำนวนครั้งการจองแยกตามรายเดือน</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">เดือน</th>
                        <th style="width: 25%; text-align: right;">รายได้รวม (บาท)</th>
                        <th style="width: 20%; text-align: center;">จำนวนรายการชำระ</th>
                        <th style="width: 20%; text-align: center;">ยอดจองชุด (ครั้ง)</th>
                        <th style="width: 10%; text-align: center;">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @php $yearTotalRev = 0; $yearTotalBookings = 0; $yearTotalPayments = 0; @endphp
                    @foreach($monthlySummary as $row)
                        @php 
                            $yearTotalRev += $row['revenue']; 
                            $yearTotalBookings += $row['bookings'];
                            $yearTotalPayments += $row['payments'];
                        @endphp
                        <tr class="{{ $row['revenue'] > 0 ? 'highlight-row' : '' }}">
                            <td style="font-weight: 600;">
                                <span class="month-bullet {{ $row['revenue'] > 0 ? 'active' : '' }}"></span>
                                {{ $row['month'] }}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: {{ $row['revenue'] > 0 ? 'var(--maroon-900)' : 'var(--muted)' }};">
                                ฿{{ number_format($row['revenue'], 2) }}
                            </td>
                            <td style="text-align: center;">
                                {{ $row['payments'] > 0 ? $row['payments'] . ' รายการ' : '-' }}
                            </td>
                            <td style="text-align: center;">
                                {{ $row['bookings'] > 0 ? $row['bookings'] . ' ครั้ง' : '-' }}
                            </td>
                            <td style="text-align: center;">
                                @if($row['revenue'] > 0)
                                    <span class="badge-success">มีรายได้</span>
                                @else
                                    <span class="badge-neutral">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="font-weight: 800; color: var(--maroon-900);">รวมทั้งสิ้นตลอดปี {{ $currentYear }}</td>
                        <td style="text-align: right; font-weight: 800; color: var(--maroon-900); font-size: 15px;">
                            ฿{{ number_format($yearTotalRev, 2) }}
                        </td>
                        <td style="text-align: center; font-weight: 700;">{{ $yearTotalPayments }} รายการ</td>
                        <td style="text-align: center; font-weight: 700;">{{ $yearTotalBookings }} ครั้ง</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ============================================================
        // 0. กราฟแนวโน้มรายได้ (Revenue Trend Chart: สลับ รายวัน / รายเดือน)
        // ============================================================
        const dailyLabels  = @json($dailyLabels ?? []);
        const dailyData    = @json($dailyData ?? []);
        const dailyCounts  = @json($dailyCounts ?? []);

        const monthlyLabels = @json($monthlyLabels ?? []);
        const monthlyData   = @json($monthlyData ?? []);
        const monthlyCounts = @json($monthlyCounts ?? []);

        const canvasTrend = document.getElementById('revenueTrendChart');
        const ctxTrend = canvasTrend.getContext('2d');

        let currentPeriod = 'daily';

        const trendChart = new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'รายได้ (บาท)',
                    data: dailyData,
                    backgroundColor: '#8b1e3f',
                    hoverBackgroundColor: '#6f1a2b',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 15, bottom: 5, left: 10, right: 15 }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30, 16, 19, 0.95)',
                        titleFont: { family: 'Noto Sans Thai', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Noto Sans Thai', size: 12 },
                        padding: 14,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                const val = Number(context.raw) || 0;
                                const idx = context.dataIndex;
                                const cnt = (currentPeriod === 'daily') 
                                    ? (dailyCounts[idx] || 0) 
                                    : (monthlyCounts[idx] || 0);
                                return [
                                    ' รายได้: ฿' + val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}),
                                    ' จำนวนรายการชำระ: ' + cnt + ' รายการ'
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Noto Sans Thai', size: 11, weight: '500' },
                            color: '#7c6e71',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 16
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#eae2e1', borderDash: [3, 3] },
                        ticks: {
                            font: { family: 'Noto Sans Thai', size: 11, weight: '500' },
                            color: '#7c6e71',
                            callback: function(val) {
                                return '฿' + Number(val).toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        function updateTrendStats(type) {
            const data = (type === 'daily') ? dailyData : monthlyData;
            const labels = (type === 'daily') ? dailyLabels : monthlyLabels;
            const counts = (type === 'daily') ? dailyCounts : monthlyCounts;

            const total = data.reduce((acc, curr) => acc + Number(curr), 0);
            const totalCount = counts.reduce((acc, curr) => acc + Number(curr), 0);
            const countNonZero = data.filter(v => Number(v) > 0).length;
            const avg = countNonZero > 0 ? (total / countNonZero) : (data.length > 0 ? total / data.length : 0);

            let maxVal = -1;
            let peakLabel = '-';
            data.forEach((val, idx) => {
                if (Number(val) > maxVal) {
                    maxVal = Number(val);
                    peakLabel = labels[idx];
                }
            });

            document.getElementById('statPeriodTotal').innerText = '฿' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('statPeriodAvg').innerText = '฿' + avg.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('statAvgLabel').innerText = (type === 'daily') ? 'เฉลี่ยต่อวัน (ที่มีรายได้)' : 'เฉลี่ยต่อเดือน (ที่มีรายได้)';
            document.getElementById('statPeakLabel').innerText = (type === 'daily') ? 'วันที่รายได้สูงสุด' : 'เดือนที่รายได้สูงสุด';
            document.getElementById('statPeriodPeak').innerText = (maxVal > 0) ? (peakLabel + ' (฿' + maxVal.toLocaleString() + ')') : 'ยังไม่มีข้อมูล';
            document.getElementById('statPeriodCount').innerText = totalCount + ' รายการ';
        }

        window.switchRevenuePeriod = function(type) {
            currentPeriod = type;
            const btnDaily = document.getElementById('btnViewDaily');
            const btnMonthly = document.getElementById('btnViewMonthly');
            const titleEl = document.getElementById('trendChartTitle');
            const subTitleEl = document.getElementById('trendChartSubtitle');

            if (type === 'daily') {
                btnDaily.classList.add('active');
                btnMonthly.classList.remove('active');
                titleEl.innerText = 'สถิติรายได้รายวัน (30 วันล่าสุด)';
                subTitleEl.innerText = 'แสดงกราฟแท่งรายได้แยกตามรายวันย้อนหลัง 30 วัน';

                trendChart.data.labels = dailyLabels;
                trendChart.data.datasets[0].data = dailyData;
                trendChart.data.datasets[0].maxBarThickness = 24;
                trendChart.data.datasets[0].backgroundColor = '#8b1e3f';
                trendChart.data.datasets[0].hoverBackgroundColor = '#6f1a2b';
            } else {
                btnDaily.classList.remove('active');
                btnMonthly.classList.add('active');
                titleEl.innerText = 'สถิติรายได้รายเดือน (ประจำปี {{ $currentYear }})';
                subTitleEl.innerText = 'แสดงกราฟแท่งเปรียบเทียบรายได้แต่ละเดือน ม.ค. - ธ.ค.';

                trendChart.data.labels = monthlyLabels;
                trendChart.data.datasets[0].data = monthlyData;
                trendChart.data.datasets[0].maxBarThickness = 48;
                trendChart.data.datasets[0].backgroundColor = '#6f1a2b';
                trendChart.data.datasets[0].hoverBackgroundColor = '#9f1239';
            }

            trendChart.update();
            updateTrendStats(type);
        };

        // คำนวณสถิติของช่วงเริ่มต้น
        updateTrendStats('daily');

        // --- ระบบชุดสีประจำหมวดหมู่ (Category Color Theme) แยกสีชัดเจนไม่กลืนกัน ---
        const categoryTheme = {
            'ชุดราตรียาว': {
                color: '#8b1e3f',      // Velvet Wine / Maroon
                hover: '#9f1239',
                name: 'ชุดราตรียาว'
            },
            'y2k': {
                color: '#8b1e3f',      // Velvet Wine / Maroon
                hover: '#9f1239',
                name: 'ชุด y2k'
            },
            'ชุดไทย': {
                color: '#d97706',      // Royal Amber Gold
                hover: '#f59e0b',
                name: 'ชุดไทย'
            },
            'ชุดเพื่อนเจ้าสาว': {
                color: '#0d9488',      // Emerald / Sage Teal
                hover: '#14b8a6',
                name: 'ชุดเพื่อนเจ้าสาว'
            },
            'เพื่อนเจ้าสาว': {
                color: '#0d9488',      // Emerald / Sage Teal
                hover: '#14b8a6',
                name: 'ชุดเพื่อนเจ้าสาว'
            },
            'ชุดแต่งงาน': {
                color: '#e11d48',      // Romantic Rose Pink
                hover: '#f43f5e',
                name: 'ชุดแต่งงาน & พรีเวดดิ้ง'
            },
            'ชุดราตรีสั้น': {
                color: '#2563eb',      // Royal Sapphire Blue
                hover: '#3b82f6',
                name: 'ชุดราตรีสั้น / ค็อกเทล'
            },
            'ค็อกเทล': {
                color: '#2563eb',      // Royal Sapphire Blue
                hover: '#3b82f6',
                name: 'ชุดราตรีสั้น / ค็อกเทล'
            },
            'ชุดสูท': {
                color: '#475569',      // Slate Charcoal
                hover: '#64748b',
                name: 'ชุดสูทสากล'
            },
            'ทักซิโด้': {
                color: '#475569',      // Slate Charcoal
                hover: '#64748b',
                name: 'ชุดสูทสากล & ทักซิโด้'
            }
        };

        const distinctFallbackColors = [
            '#0d9488', '#d97706', '#8b1e3f', '#2563eb', '#e11d48', '#7c3aed', '#059669', '#ea580c', '#0284c7', '#d946ef'
        ];

        function getCategoryColor(name, index = 0) {
            if (!name) return distinctFallbackColors[index % distinctFallbackColors.length];
            const lower = name.toLowerCase();
            for (const key in categoryTheme) {
                if (lower.includes(key.toLowerCase())) {
                    return categoryTheme[key].color;
                }
            }
            return distinctFallbackColors[index % distinctFallbackColors.length];
        }

        // --- 1. กราฟวงกลมพรีเมียม (Doughnut) สัดส่วนรายได้ตามประเภทชุด ---
        const categoryData = @json($categoryRevenues ?? []);
        const catFullNames = categoryData.map(item => item.category_name || item.name || 'หมวดหมู่ทั่วไป');
        const catLabels = categoryData.map(item => {
            const raw = item.category_name || item.name || 'หมวดหมู่ทั่วไป';
            return raw.split('(')[0].trim();
        });
        const catRevenues = categoryData.map(item => Number(item.total_revenue) || 0);
        const catCounts = categoryData.map(item => Number(item.total_rents) || 0);
        const catColors = categoryData.map((item, idx) => getCategoryColor(item.category_name, idx));

        window.trendChart = trendChart;

        const ctxCat = document.getElementById('categoryRevenueChart').getContext('2d');
        window.catChart = new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels.length > 0 ? catLabels : ['ยังไม่มีข้อมูล'],
                datasets: [{
                    data: catRevenues.length > 0 ? catRevenues : [1],
                    backgroundColor: catColors.length > 0 ? catColors : ['#8b1e3f'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 16,
                            font: { family: 'Noto Sans Thai', size: 12, weight: '600' },
                            color: '#1e1013',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 16, 19, 0.95)',
                        titleFont: { family: 'Noto Sans Thai', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Noto Sans Thai', size: 12 },
                        padding: 14,
                        cornerRadius: 10,
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return catFullNames[index] || context[0].label;
                            },
                            label: function(context) {
                                let value = Number(context.raw) || 0;
                                let total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                let percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                const index = context.dataIndex;
                                const count = catCounts[index] || 0;
                                return [
                                    ' รายได้: ฿' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + percent + '%)',
                                    ' ยอดเช่าสะสม: ' + count + ' ครั้ง'
                                ];
                            }
                        }
                    }
                }
            }
        });

        // --- 2. กราฟแท่งแนวนอน (Horizontal Bar) ชุดยอดนิยม แยกสีตามหมวดหมู่ชัดเจน ---
        const dressData = @json($topDresses ?? []);
        const dressFullNames = dressData.map(item => item.product_name || item.name || '-');
        const dressLabels = dressData.map(item => {
            const raw = item.product_name || item.name || '-';
            return raw.length > 28 ? raw.substring(0, 26) + '...' : raw;
        });
        const dressCounts = dressData.map(item => item.rental_count || item.bookings_count || 0);
        const dressCategories = dressData.map(item => item.category ? item.category.category_name : 'หมวดหมู่ทั่วไป');
        
        // แยกสีของแต่ละแท่งตามหมวดหมู่ชุด
        const dressBarColors = dressData.map((item, idx) => {
            const catName = item.category ? item.category.category_name : '';
            return getCategoryColor(catName, idx);
        });

        // สร้าง Badge สัญลักษณ์สีแยกตามหมวดหมู่ด้านบนกราฟ
        const uniqueCats = [];
        dressData.forEach((item, idx) => {
            const cName = item.category ? item.category.category_name : 'หมวดหมู่ทั่วไป';
            if (!uniqueCats.find(c => c.name === cName)) {
                uniqueCats.push({
                    name: cName,
                    color: getCategoryColor(cName, idx)
                });
            }
        });

        const legendContainer = document.getElementById('topDressesLegend');
        if (legendContainer && uniqueCats.length > 0) {
            legendContainer.innerHTML = uniqueCats.map(c => 
                `<span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 600; color: #430d17; background: #faf5f3; padding: 3px 8px; border-radius: 6px; border: 1px solid #ebdcd9;">
                    <span style="width: 9px; height: 9px; border-radius: 50%; background: ${c.color}; display: inline-block;"></span>
                    ${c.name.split('(')[0].trim()}
                </span>`
            ).join('');
        }

        const ctxDress = document.getElementById('topDressesChart').getContext('2d');
        window.dressChart = new Chart(ctxDress, {
            type: 'bar',
            data: {
                labels: dressLabels.length > 0 ? dressLabels : ['ยังไม่มีข้อมูล'],
                datasets: [{
                    label: 'จำนวนครั้งที่เช่า',
                    data: dressCounts.length > 0 ? dressCounts : [0],
                    backgroundColor: dressBarColors.length > 0 ? dressBarColors : '#8b1e3f',
                    borderRadius: 6,
                    borderWidth: 0,
                    barThickness: 22
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 15,
                        right: 25,
                        top: 5,
                        bottom: 5
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30, 16, 19, 0.95)',
                        titleFont: { family: 'Noto Sans Thai', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Noto Sans Thai', size: 12 },
                        padding: 14,
                        cornerRadius: 10,
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return dressFullNames[index] || context[0].label;
                            },
                            label: function(context) {
                                const index = context.dataIndex;
                                const cat = dressCategories[index] || 'หมวดหมู่ทั่วไป';
                                return [
                                    ' หมวดหมู่: ' + cat,
                                    ' ยอดเช่าทั้งหมด: ' + context.raw + ' ครั้ง'
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#eae2e1', borderDash: [4, 4] },
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Noto Sans Thai', size: 11 },
                            color: '#7c6e71'
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Noto Sans Thai', size: 12, weight: '600' },
                            color: '#1e1013',
                            autoSkip: false
                        }
                    }
                }
            }
        });

        // จัดการปรับขนาดกราฟอัตโนมัติก่อนพิมพ์เอกสาร เพื่อให้พอดี 1 หน้า A4 อย่างสมบูรณ์
        window.addEventListener('beforeprint', function() {
            if (window.trendChart) window.trendChart.resize();
            if (window.catChart) window.catChart.resize();
            if (window.dressChart) window.dressChart.resize();
        });
        window.addEventListener('afterprint', function() {
            if (window.trendChart) window.trendChart.resize();
            if (window.catChart) window.catChart.resize();
            if (window.dressChart) window.dressChart.resize();
        });
    });
</script>
@endpush
@endsection