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
        font-family: 'Noto Sans Thai', sans-serif !important;
        box-sizing: border-box;
    }

    .dashboard-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 10px 50px;
        color: var(--ink);
    }

    /* --- Header ส่วนหัวหน้าจอ --- */
    .dash-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--line);
    }
    .dash-title h1 {
        font-size: 22px;
        font-weight: 800;
        color: var(--maroon-900);
        margin: 0 0 4px;
        letter-spacing: -0.5px;
    }
    .dash-title p {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(111, 26, 43, 0.15);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(111, 26, 43, 0.25);
    }
    .btn-action svg { width: 18px; height: 18px; }

    /* --- KPI Metric Cards (3 กล่องบน) --- */
    .kpi-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (min-width: 768px) {
        .kpi-grid { grid-template-columns: repeat(3, 1fr); }
    }
    .kpi-card {
        background: var(--card-bg);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 22px 24px;
        position: relative;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
    }
    .kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .kpi-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }
    .kpi-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .kpi-icon.maroon { background: #fdf2f4; color: var(--maroon-700); }
    .kpi-icon.gold { background: #fbf6ec; color: var(--gold-dark); }
    .kpi-icon.green { background: #ecfdf5; color: #059669; }
    
    .kpi-icon svg { width: 20px; height: 20px; }
    .kpi-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--ink);
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* --- Charts Section (2 กล่องกราฟล่าง) --- */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    @media (min-width: 1024px) {
        .charts-grid { grid-template-columns: 1fr 1fr; }
    }
    .chart-card {
        background: var(--card-bg);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .chart-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--line);
    }
    .chart-header svg {
        width: 20px;
        height: 20px;
        color: var(--maroon-700);
    }
    .chart-header h3 {
        font-size: 15px;
        font-weight: 700;
        color: var(--maroon-900);
        margin: 0;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* --- Print Header (ซ่อนไว้ปกติ จะแสดงเฉพาะตอนสั่งพิมพ์) --- */
    .print-only-header {
        display: none;
    }

    /* --- สไตล์เวลาสั่งพิมพ์เอกสาร (Print Layout) --- */
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        aside, nav, header, .dash-header .btn-action, .no-print {
            display: none !important;
        }
        .dashboard-wrapper {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .print-only-header {
            display: block;
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .print-only-header h2 { font-size: 20px; font-weight: bold; margin: 0 0 5px; color: #000; }
        .print-only-header p { font-size: 12px; color: #555; margin: 0; }
        
        .kpi-card, .chart-card {
            background: #fff !important;
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            break-inside: avoid;
            margin-bottom: 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    
    <!-- ส่วนหัวเฉพาะตอนพิมพ์เอกสาร -->
    <div class="print-only-header">
        <h2>KYRIX - รายงานสรุปผลประกอบการประจำร้าน</h2>
        <p>พิมพ์เมื่อวันที่: {{ date('d/m/Y H:i') }} | ข้อมูลสำหรับผู้บริหาร</p>
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
            <div class="chart-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3>ชุดยอดนิยมสูงสุด (จำนวนครั้งที่เช่า)</h3>
            </div>
            <div class="chart-container">
                <canvas id="topDressesChart"></canvas>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. กราฟวงกลมพรีเมียม (Doughnut) ---
        const categoryData = @json($categoryRevenues ?? []);
        const catLabels = categoryData.map(item => item.category_name || item.name || 'หมวดหมู่ทั่วไป');
        const catRevenues = categoryData.map(item => item.total_revenue || 0);

        const ctxCat = document.getElementById('categoryRevenueChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels.length > 0 ? catLabels : ['ยังไม่มีข้อมูล'],
                datasets: [{
                    data: catRevenues.length > 0 ? catRevenues : [1],
                    backgroundColor: [
                        '#6f1a2b', // Maroon
                        '#c79a5c', // Gold
                        '#430d17', // Dark Maroon
                        '#a97f45', // Dark Gold
                        '#7c6e71', // Muted Gray
                        '#d9b382'  // Light Gold
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
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
                            font: { family: 'Noto Sans Thai', size: 12, weight: '500' },
                            color: '#1e1013'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(67, 13, 23, 0.9)',
                        titleFont: { family: 'Noto Sans Thai', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Noto Sans Thai', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return ' รายได้: ฿' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });

        // --- 2. กราฟแท่งแนวนอนพรีเมียม (Horizontal Bar) ---
        const dressData = @json($topDresses ?? []);
        const dressLabels = dressData.map(item => item.product_name || item.name || '-');
        const dressCounts = dressData.map(item => item.rental_count || item.bookings_count || 0);

        const ctxDress = document.getElementById('topDressesChart').getContext('2d');
        new Chart(ctxDress, {
            type: 'bar',
            data: {
                labels: dressLabels.length > 0 ? dressLabels : ['ยังไม่มีข้อมูล'],
                datasets: [{
                    label: 'จำนวนครั้งที่เช่า',
                    data: dressCounts.length > 0 ? dressCounts : [0],
                    backgroundColor: '#c79a5c',
                    borderRadius: 6,
                    borderWidth: 0,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(67, 13, 23, 0.9)',
                        titleFont: { family: 'Noto Sans Thai', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Noto Sans Thai', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ยอดเช่า: ' + context.raw + ' ครั้ง';
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
                            font: { family: 'Noto Sans Thai', size: 12, weight: '500' },
                            color: '#1e1013'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection