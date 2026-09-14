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
            --maroon-950: #4a0f19;
            --maroon-900: #751B2A; /* สีแดงเข้มไวน์แดงตามรูปโลโก้ */
            --maroon-800: #7A2031;
            --maroon-700: #8c2539;
            --gold:       #C5A059; /* สีทองพรีเมียมละมุนตา */
            --gold-dark:  #A88349;
            --gold-light: #f9f6f0;
            --rose-bg:    #fcf3f4;
            --rose-text:  #751B2A;
            --cream:      #faf8f6;
            --surface:    #ffffff;
            --ink:        #2b1a1d;
            --muted:      #8a7c7f;
            --line:       #f0e8e6;
            --radius-sm:  8px;
            --radius-md:  12px;
            --radius-lg:  18px;
            --shadow-sm:  0 2px 8px rgba(43, 26, 29, 0.04);
            --shadow-md:  0 8px 24px rgba(43, 26, 29, 0.08);
            --shadow-lg:  0 14px 35px rgba(117, 27, 42, 0.12);
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
            box-shadow: 0 4px 20px rgba(117, 27, 42, 0.03);
        }

        .admin-nav-container {
            max-width: 1540px;
            margin: 0 auto;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-brand .logo-container {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .admin-brand .logo-text {
            font-family: "Playfair Display", serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 2.5px;
            color: var(--maroon-900);
        }

        .admin-brand .logo-subtext {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 2.8px;
            color: var(--gold-dark);
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* MENU LINKS */
        .admin-menu {
            display: flex;
            align-items: center;
            gap: 24px;
            list-style: none;
            height: 100%;
        }

        .admin-menu-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 2px;
            height: 100%;
            font-size: 14px;
            font-weight: 500;
            color: #4a3b3e;
            transition: all .2s ease;
            position: relative;
        }

        .admin-menu-link i {
            font-size: 13.5px;
            color: var(--muted);
            transition: color .2s;
        }

        .admin-menu-link:hover {
            color: var(--maroon-900);
        }

        .admin-menu-link:hover i {
            color: var(--maroon-900);
        }

        .admin-menu-link.active {
            color: var(--maroon-900);
            font-weight: 700;
        }

        .admin-menu-link.active i {
            color: var(--maroon-900);
        }

        /* Active bottom border indicator style */
        .admin-menu-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--maroon-900);
            border-radius: 3px 3px 0 0;
        }

        /* RIGHT ACTIONS */
        .admin-nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* SEARCH BOX */
        .admin-search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .admin-search-input {
            background: #faf8f7;
            border: 1px solid var(--line);
            border-radius: 99px;
            padding: 9px 38px 9px 16px;
            font-size: 13px;
            color: var(--ink);
            width: 220px;
            outline: none;
            transition: all .2s;
            font-family: inherit;
        }

        .admin-search-input::placeholder {
            color: #b5a4a7;
        }

        .admin-search-input:focus {
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.15);
            width: 240px;
        }

        .admin-search-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
            transition: color .2s;
        }

        .admin-search-input:focus ~ .admin-search-btn,
        .admin-search-box:hover .admin-search-btn {
            color: var(--maroon-900);
        }

        /* NOTIFICATION BELL */
        .notif-dropdown-wrapper {
            position: relative;
        }

        .notif-bell-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--rose-bg);
            border: 1px solid rgba(117, 27, 42, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--maroon-900);
            cursor: pointer;
            position: relative;
            transition: all .2s;
        }

        .notif-bell-btn:hover {
            background: #f7e6e8;
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

        .admin-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 16px 5px 6px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 30px;
            box-shadow: var(--shadow-sm);
        }

        .admin-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--maroon-900);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .admin-user-info {
            line-height: 1.2;
        }

        .admin-user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--maroon-900);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .admin-user-role {
            font-size: 10px;
            color: var(--muted);
        }

        .btn-logout {
            background: none;
            border: none;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #937d81;
            transition: all .2s;
            margin-left: 4px;
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

        @media (max-width: 1200px) {
            .admin-menu {
                gap: 16px;
            }
            .admin-search-input {
                width: 160px;
            }
            .admin-search-input:focus {
                width: 180px;
            }
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
                <a href="{{ route('owner.dashboard') }}" class="logo-container">
                    <span class="logo-text">KYRIX</span>
                    <span class="logo-subtext">DRESS RENTAL</span>
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
                
                <!-- SEARCH INPUT BOX -->
                <form action="{{ route('owner.dresses.index') }}" method="GET" class="admin-search-box">
                    <input type="text" name="search" class="admin-search-input" placeholder="ค้นหาชุดสวย..." value="{{ request('search') }}">
                    <button type="submit" class="admin-search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                <!-- NOTIFICATION BELL (OVERDUE RETURNS) -->
                @php
                    use App\Models\Rental;
                    $today = \Carbon\Carbon::today();
                    $overdueRentals = Rental::with('customer')
                        ->whereDate('end_date', '<', $today)
                        ->whereNotIn('status', ['returned', 'completed', 'cancelled'])
                        ->get();
                @endphp

                <div class="notif-dropdown-wrapper">
                    <button type="button" class="notif-bell-btn" id="notifBellBtn" onclick="toggleNotifDropdown(event)" title="แจ้งเตือนชุดเกินกำหนด">
                        <i class="fa-solid fa-bag-shopping" style="font-size: 14px;"></i>
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

                <!-- USER PROFILE PILL -->
                <div class="admin-user-pill">
                    <div class="admin-avatar">
                        {{ mb_substr(auth()->user()->name ?? 'อ', 0, 1) }}
                    </div>
                    <div class="admin-user-info">
                        <div class="admin-user-name">
                            {{ auth()->user()->name ?? 'เจ้าของร้าน' }}
                            <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: var(--muted);"></i>
                        </div>
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

        window.addEventListener('click', function() {
            const menu = document.getElementById('notifDropdownMenu');
            if (menu && menu.classList.contains('show')) {
                menu.classList.remove('show');
            }
        });

        function markNotifRead(rentalId) {
            let readNotifs = JSON.parse(localStorage.getItem('kyrix_read_notifs') || '{}');
            const now = new Date().getTime();
            readNotifs[rentalId] = now;
            localStorage.setItem('kyrix_read_notifs', JSON.stringify(readNotifs));
        }

        document.addEventListener("DOMContentLoaded", function() {
            let readNotifs = JSON.parse(localStorage.getItem('kyrix_read_notifs') || '{}');
            const now = new Date().getTime();
            const twelveHours = 12 * 60 * 60 * 1000;
            let updated = false;

            for (let id in readNotifs) {
                if (now - readNotifs[id] > twelveHours) {
                    delete readNotifs[id];
                    updated = true;
                }
            }

            if (updated) {
                localStorage.setItem('kyrix_read_notifs', JSON.stringify(readNotifs));
            }
        });
    </script>

    @stack('scripts')
</body>
</html>