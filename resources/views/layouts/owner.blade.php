<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KYRIX | ระบบจัดการร้านเช่าชุด')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --maroon-950: #2d080f;
            --maroon-900: #430d17;
            --maroon-800: #5c1522;
            --maroon-700: #6f1a2b;
            --maroon-600: #832033;
            --gold:       #c79a5c;
            --gold-dark:  #a97f45;
            --gold-light: #f6eedf;
            --rose-bg:    #fbf0f2;
            --rose-text:  #7f2138;
            --cream:      #faf7f4;
            --surface:    #ffffff;
            --ink:        #241417;
            --muted:      #8a7a7d;
            --line:       #efe6e4;
            --radius-sm:  8px;
            --radius-md:  12px;
            --radius-lg:  18px;
            --shadow-sm:  0 2px 8px rgba(36, 20, 23, 0.04);
            --shadow-md:  0 8px 24px rgba(36, 20, 23, 0.08);
            --shadow-lg:  0 14px 35px rgba(67, 13, 23, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Noto Sans Thai", "Plus Jakarta Sans", sans-serif;
            background-color: var(--cream);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: all .2s ease;
        }

        /* TOP NAVIGATION */
        .admin-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(67, 13, 23, 0.04);
        }

        .admin-nav-container {
            max-width: 1540px;
            margin: 0 auto;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-brand .logo-text {
            font-family: "Playfair Display", serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--maroon-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--maroon-800), var(--maroon-950));
            color: var(--gold);
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid rgba(199, 154, 92, 0.4);
            text-transform: uppercase;
        }

        /* MENU LINKS */
        .admin-menu {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
        }

        .admin-menu-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 15px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 600;
            color: #554448;
            transition: all .2s ease;
            position: relative;
        }

        .admin-menu-link i {
            font-size: 14px;
            color: var(--muted);
            transition: color .2s;
        }

        .admin-menu-link:hover {
            background: var(--rose-bg);
            color: var(--maroon-800);
        }

        .admin-menu-link:hover i {
            color: var(--maroon-800);
        }

        .admin-menu-link.active {
            background: var(--maroon-900);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(67, 13, 23, 0.25);
        }

        .admin-menu-link.active i {
            color: var(--gold);
        }

        /* RIGHT ACTIONS */
        .admin-nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* NOTIFICATION BELL */
        .notif-dropdown-wrapper {
            position: relative;
        }

        .notif-bell-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--cream);
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--maroon-800);
            cursor: pointer;
            position: relative;
            transition: all .2s;
        }

        .notif-bell-btn:hover {
            background: var(--rose-bg);
            border-color: var(--gold);
        }

        .notif-badge-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc2626;
            color: #fff;
            font-size: 10px;
            font-weight: 750;
            padding: 2px 6px;
            border-radius: 99px;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);
        }

        .notif-dropdown-menu {
            position: absolute;
            right: 0;
            top: 50px;
            width: 340px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: var(--shadow-lg);
            display: none;
            z-index: 1100;
            overflow: hidden;
            animation: fadeIn .2s ease;
        }

        .notif-dropdown-menu.show {
            display: block;
        }

        .notif-header {
            padding: 14px 16px;
            background: var(--cream);
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 750;
            color: var(--maroon-900);
        }

        .notif-body {
            max-height: 320px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            gap: 12px;
            align-items: flex-start;
            text-decoration: none;
            transition: background .2s;
        }

        .notif-item:hover {
            background: #fdfbfb;
        }

        .notif-item.unread {
            background: #fff8f8;
        }

        .notif-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fdf2f2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 13px;
        }

        .notif-content {
            font-size: 12px;
            color: var(--ink);
            line-height: 1.4;
        }

        .notif-time {
            font-size: 10.5px;
            color: var(--muted);
            margin-top: 4px;
        }

        .notif-empty {
            padding: 30px;
            text-align: center;
            color: var(--muted);
            font-size: 12.5px;
        }

        .btn-storefront {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            background: #fff;
            color: var(--maroon-800);
            border: 1px solid var(--line);
            transition: all .2s;
        }

        .btn-storefront:hover {
            border-color: var(--gold);
            background: var(--gold-light);
            color: var(--maroon-900);
        }

        .admin-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            background: #faf7f4;
            border: 1px solid var(--line);
            border-radius: 30px;
        }

        .admin-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--maroon-700), var(--maroon-950));
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
        }

        .admin-user-info {
            line-height: 1.2;
        }

        .admin-user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--maroon-900);
        }

        .admin-user-role {
            font-size: 10px;
            color: var(--muted);
        }

        .btn-logout {
            background: none;
            border: none;
            cursor: pointer;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #937d81;
            transition: all .2s;
        }

        .btn-logout:hover {
            background: #ffebee;
            color: #c62828;
        }

        /* MAIN CONTENT AREA */
        .admin-main {
            flex: 1;
            padding: 30px 0 60px;
        }

        .admin-container {
            max-width: 1540px;
            margin: 0 auto;
            padding: 0 28px;
        }

        /* FLASH MESSAGES */
        .flash-container {
            margin-bottom: 24px;
        }

        .flash-alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            font-size: 13.5px;
            font-weight: 500;
            animation: fadeIn .3s ease;
        }

        .flash-alert.success {
            background: #eef7ee;
            border: 1px solid #c8e6c9;
            color: #1e4620;
        }

        .flash-alert.error {
            background: #fdf0f2;
            border: 1px solid #f8bbd0;
            color: #880e4f;
        }

        .flash-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: inherit;
            opacity: .6;
        }

        .flash-close:hover {
            opacity: 1;
        }

        /* FOOTER */
        .admin-footer {
            border-top: 1px solid var(--line);
            padding: 20px 0;
            background: #ffffff;
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1024px) {
            .admin-menu {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- TOP NAVIGATION -->
    <header class="admin-nav">
        <div class="admin-nav-container">
            <div class="admin-brand">
                <a href="{{ route('owner.dashboard') }}" class="logo-text">
                    KYRIX
                    <span class="admin-badge">ADMIN</span>
                </a>
            </div>

            <!-- MAIN MENU -->
            <ul class="admin-menu">
                <li>
                    <a href="{{ route('owner.dashboard') }}" class="admin-menu-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>ภาพรวม</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.dresses.index') }}" class="admin-menu-link {{ request()->routeIs('owner.dresses.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-shirt"></i>
                        <span>จัดการชุด</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.bookings.index') }}" class="admin-menu-link {{ request()->routeIs('owner.bookings.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>รายการเช่า</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.payments.index') }}" class="admin-menu-link {{ request()->routeIs('owner.payments.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i>
                        <span>ตรวจสลิป</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.returns.index') }}" class="admin-menu-link {{ request()->routeIs('owner.returns.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>รับคืนชุด</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.customers.index') }}" class="admin-menu-link {{ request()->routeIs('owner.customers.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>ลูกค้า</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.reports.index') }}" class="admin-menu-link {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-column"></i>
                        <span>รายงาน</span>
                    </a>
                </li>
            </ul>

            <!-- RIGHT ACTIONS -->
            <div class="admin-nav-actions">
                
                <!-- NOTIFICATION BELL (OVERDUE RETURNS) -->
                @php
                    use App\Models\Rental;
                    // ค้นหารายการเช่าที่เลยกำหนดคืน (end_date น้อยกว่าวันนี้ และสถานะไม่ใช่ returned หรือ cancelled)
                    $today = \Carbon\Carbon::today();
                    $overdueRentals = Rental::with('customer')
                        ->whereDate('end_date', '<', $today)
                        ->whereNotIn('status', ['returned', 'completed', 'cancelled'])
                        ->get();
                @endphp

                <div class="notif-dropdown-wrapper">
                    <button type="button" class="notif-bell-btn" id="notifBellBtn" onclick="toggleNotifDropdown(event)" title="แจ้งเตือนชุดเกินกำหนด">
                        <i class="fa-regular fa-bell"></i>
                        @if($overdueRentals->count() > 0)
                            <span class="notif-badge-count" id="notifBadgeCount">{{ $overdueRentals->count() }}</span>
                        @endif
                    </button>

                    <div class="notif-dropdown-menu" id="notifDropdownMenu">
                        <div class="notif-header">
                            <span>การแจ้งเตือนชุดเกินกำหนด</span>
                            <span style="font-size: 11px; color: var(--muted); font-weight: normal;">({{ $overdueRentals->count() }} รายการ)</span>
                        </div>
                        <div class="notif-body">
                            @forelse($overdueRentals as $item)
                                @php
                                    $rId = $item->rental_id ?? $item->id;
                                    $custName = $item->customer->name ?? $item->customer_name ?? 'ลูกค้า';
                                    $daysLate = \Carbon\Carbon::parse($item->end_date)->diffInDays($today);
                                @endphp
                                <a href="{{ route('owner.returns.index', ['tab' => 'overdue']) }}" class="notif-item" onclick="markNotifRead('{{ $rId }}')">
                                    <div class="notif-icon-box">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </div>
                                    <div class="notif-content">
                                        <div><strong>#RENT-{{ $rId }}</strong> คุณ <strong>{{ $custName }}</strong> เลยกำหนดคืนแล้ว {{ $daysLate }} วัน</div>
                                        <div class="notif-time">กำหนดคืน: {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="notif-empty">
                                    <i class="fa-regular fa-bell-slash" style="font-size: 24px; margin-bottom: 6px; opacity: .5;"></i>
                                    <div>ไม่มีรายการชุดเกินกำหนดคืนในขณะนี้</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}" target="_blank" class="btn-storefront" title="เปิดดูหน้าร้าน">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>หน้าร้าน</span>
                </a>

                <div class="admin-user-pill">
                    <div class="admin-avatar">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div class="admin-user-info">
                        <div class="admin-user-name">{{ auth()->user()->name ?? 'เจ้าของร้าน' }}</div>
                        <div class="admin-user-role">ผู้ดูแลร้าน KYRIX</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-logout" title="ออกจากระบบ" onclick="return confirm('ต้องการออกจากระบบหรือไม่?')">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="admin-main">
        <div class="admin-container">
            <!-- FLASH MESSAGES -->
            @if(session('success'))
                <div class="flash-container">
                    <div class="flash-alert success">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" class="flash-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-container">
                    <div class="flash-alert error">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" class="flash-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="flash-container">
                    <div class="flash-alert error">
                        <div>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;font-weight:700;">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span>เกิดข้อผิดพลาด:</span>
                            </div>
                            <ul style="margin-left:26px;font-size:12.5px;">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="flash-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="admin-footer">
        <div class="admin-container">
            <p>© {{ now()->year }} KYRIX Dress Rental — Luxury Evening & Thai Dress Rental Management System</p>
        </div>
    </footer>

    <!-- SCRIPT FOR NOTIFICATION DROPDOWN & 12H EXPIRATION -->
    <script>
        function toggleNotifDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('notifDropdownMenu');
            menu.classList.toggle('show');
        }

        // ปิดเมนูดรอปดาวน์เมื่อคลิกพื้นที่อื่น
        window.addEventListener('click', function() {
            const menu = document.getElementById('notifDropdownMenu');
            if (menu && menu.classList.contains('show')) {
                menu.classList.remove('show');
            }
        });

        // ฟังก์ชันจัดการเวลาอ่าน (คงอยู่ 12 ชม. แล้วหายไป)
        function markNotifRead(rentalId) {
            let readNotifs = JSON.parse(localStorage.getItem('kyrix_read_notifs') || '{}');
            const now = new Date().getTime();
            readNotifs[rentalId] = now; // บันทึกเวลาที่กดอ่าน
            localStorage.setItem('kyrix_read_notifs', JSON.stringify(readNotifs));
        }

        // ตรวจสอบและซ่อนรายการที่อ่านแล้วเกิน 12 ชั่วโมงตอนโหลดหน้าเว็บ
        document.addEventListener("DOMContentLoaded", function() {
            let readNotifs = JSON.parse(localStorage.getItem('kyrix_read_notifs') || '{}');
            const now = new Date().getTime();
            const twelveHours = 12 * 60 * 60 * 1000;
            let updated = false;

            for (let id in readNotifs) {
                if (now - readNotifs[id] > twelveHours) {
                    delete readNotifs[id]; // ลบออกเมื่อเกิน 12 ชม.
                    updated = true;
                }
            }

            if (updated) {
                localStorage.setItem('kyrix_read_notifs', JSON.stringify(readNotifs));
            }

            // ซ่อนการแจ้งเตือนของ rental ที่เพิ่งกดอ่านไปไม่เกิน 12 ชม.
            const items = document.querySelectorAll('.notif-item');
            // สามารถใช้สคริปต์เพิ่มเติมถ้าต้องการซ่อน element ในดรอปดาวน์ทันทีที่กดอ่าน
        });
    </script>

    @stack('scripts')
</body>
</html>