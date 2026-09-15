<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KYRIX | ระบบจัดการร้านเช่าชุด')</title>

    <!-- Google Fonts: Prompt & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --font-prompt: 'Prompt', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --maroon-950: #4D141B;
            --maroon-900: #7A1F2B; /* โทนแดงไวน์/มารูน */
            --maroon-800: #882230;
            --maroon-700: #9C2A3B;
            --maroon-dark: #631722;
            --gold:       #C69C4C; /* โทนทอง */
            --gold-dark:  #B89047;
            --gold-light: #fdfbf7;
            --blush-bg:   #faedef; /* สีชมพูอ่อนสำหรับไอคอนกระเป๋า */
            --blush-hover:#f7e1e4;
            --cream:      #faf8f6;
            --surface:    #ffffff;
            --ink:        #2b1a1d;
            --muted:      #8a7c7f;
            --line:       #ece4e3;
            --radius-sm:  8px;
            --radius-md:  12px;
            --radius-lg:  18px;
            --shadow-sm:  0 2px 8px rgba(43, 26, 29, 0.04);
            --shadow-md:  0 8px 24px rgba(43, 26, 29, 0.08);
            --shadow-lg:  0 14px 35px rgba(122, 31, 43, 0.12);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font-prompt) !important;
        }

        body {
            font-family: var(--font-prompt) !important;
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

        /* TOP NAVIGATION (ตามแบบสไตล์ในภาพตัวอย่าง) */
        .admin-nav {
            background: #ffffff;
            border-bottom: 1px solid #ebe5e4;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(122, 31, 43, 0.02);
        }

        .admin-nav-container {
            max-width: 1560px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
            gap: 24px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .admin-brand .logo-container {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
            text-decoration: none;
        }

        .admin-brand .logo-text {
            font-family: "Playfair Display", serif !important;
            font-size: 27px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--maroon-900);
        }

        .admin-brand .logo-subtext {
            font-family: var(--font-prompt) !important;
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 3.5px;
            color: var(--gold-dark);
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* MENU LINKS (คลีน ไม่มีไอคอน มีขีดเส้นใต้สีมารูนสำหรับเมนูที่แอคทีฟ) */
        .admin-menu {
            display: flex;
            align-items: center;
            gap: 30px;
            list-style: none;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .admin-menu-link {
            display: inline-flex;
            align-items: center;
            height: 72px;
            font-size: 15px;
            font-weight: 500;
            color: #3d3537;
            transition: all .2s ease;
            position: relative;
            padding: 0 2px;
            white-space: nowrap;
            text-decoration: none;
        }

        .admin-menu-link:hover {
            color: var(--maroon-900);
        }

        .admin-menu-link.active {
            color: var(--maroon-900);
            font-weight: 600;
        }

        /* ขีดเส้นใต้สีแดงไวน์/มารูนใต้เมนูที่เลือก ตรงตามรูปตัวอย่าง */
        .admin-menu-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2.5px;
            background: var(--maroon-900);
            border-radius: 2px 2px 0 0;
        }

        /* RIGHT ACTIONS */
        .admin-nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        /* SEARCH PILL */
        .admin-search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .admin-search-input {
            background: #f8f5f5;
            border: 1px solid #ebe4e4;
            border-radius: 999px;
            padding: 8px 38px 8px 18px;
            font-size: 13.5px;
            color: var(--ink);
            width: 195px;
            outline: none;
            transition: all .25s ease;
            font-family: var(--font-prompt) !important;
        }

        .admin-search-input::placeholder {
            color: #9e8f92;
            font-size: 13px;
        }

        .admin-search-input:focus {
            border-color: var(--maroon-900);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(122, 31, 43, 0.08);
            width: 225px;
        }

        .admin-search-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #8b7c7f;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: color .2s;
        }

        .admin-search-input:focus ~ .admin-search-btn,
        .admin-search-box:hover .admin-search-btn {
            color: var(--maroon-900);
        }

        /* NOTIFICATION / BAG BUTTON (วงกลมสีชมพูอ่อน ตามภาพตัวอย่าง) */
        .notif-dropdown-wrapper {
            position: relative;
        }

        .notif-bell-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--blush-bg);
            border: 1px solid rgba(122, 31, 43, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--maroon-900);
            cursor: pointer;
            position: relative;
            transition: all .2s ease;
        }

        .notif-bell-btn:hover {
            background: var(--blush-hover);
            transform: scale(1.04);
        }

        .notif-bell-btn i {
            font-size: 15px;
            color: var(--maroon-900);
        }

        .notif-badge-count {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #dc2626;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
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
            font-weight: 700;
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

        .notif-item.is-read {
            opacity: .55;
        }

        .notif-item.is-read .notif-icon-box {
            background: #f2efec;
            color: var(--muted);
        }

        .notif-read-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 600;
            color: var(--muted);
            margin-left: 8px;
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

        /* RIGHT BUTTONS (สไตล์ปุ่มเส้นขอบ และ ปุ่มทึบสีมารูน ตามแบบในรูปภาพ) */
        .btn-nav-outline {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 18px;
            background: transparent;
            color: var(--maroon-900);
            border: 1.5px solid var(--maroon-900);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            font-family: var(--font-prompt) !important;
            line-height: 1.3;
            white-space: nowrap;
        }

        .btn-nav-outline:hover {
            background: var(--blush-bg);
            color: var(--maroon-900);
        }

        .btn-nav-solid {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 20px;
            background: var(--maroon-900);
            color: #ffffff;
            border: 1.5px solid var(--maroon-900);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            font-family: var(--font-prompt) !important;
            line-height: 1.3;
            white-space: nowrap;
        }

        .btn-nav-solid:hover {
            background: var(--maroon-dark);
            border-color: var(--maroon-dark);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(122, 31, 43, 0.2);
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

            <!-- MAIN MENU (สไตล์คลีนตามแบบในรูปภาพ) -->
            <ul class="admin-menu">
                <li>
                    <a href="{{ route('owner.dashboard') }}" class="admin-menu-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                        <span>ภาพรวม</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.dresses.index') }}" class="admin-menu-link {{ request()->routeIs('owner.dresses.*') ? 'active' : '' }}">
                        <span>จัดการชุด</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.bookings.index') }}" class="admin-menu-link {{ request()->routeIs('owner.bookings.*') ? 'active' : '' }}">
                        <span>รายการเช่า</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.payments.index') }}" class="admin-menu-link {{ request()->routeIs('owner.payments.*') ? 'active' : '' }}">
                        <span>ตรวจสลิป</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.returns.index') }}" class="admin-menu-link {{ request()->routeIs('owner.returns.*') ? 'active' : '' }}">
                        <span>รับคืนชุด</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.customers.index') }}" class="admin-menu-link {{ request()->routeIs('owner.customers.*') ? 'active' : '' }}">
                        <span>ลูกค้า</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('owner.reports.index') }}" class="admin-menu-link {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">
                        <span>รายงาน</span>
                    </a>
                </li>
            </ul>

            <!-- RIGHT ACTIONS (ค้นหา, ไอคอนกระเป๋าชมพู, ปุ่มเส้นขอบ, ปุ่มทึบมารูน) -->
            <div class="admin-nav-actions">
                
                <!-- SEARCH INPUT BOX -->
                <form action="{{ route('owner.dresses.index') }}" method="GET" class="admin-search-box">
                    <input type="text" name="search" class="admin-search-input" placeholder="ค้นหาชุดสวย..." value="{{ request('search') }}">
                    <button type="submit" class="admin-search-btn" title="ค้นหา">
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
                        <i class="fa-solid fa-bag-shopping"></i>
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
                                    $custName = $item->customer ? ($item->customer->name ?? $item->customer->first_name . ' ' . $item->customer->last_name) : 'ลูกค้า (ไม่พบข้อมูล)';
                                    $daysLate = \Carbon\Carbon::parse($item->end_date)->diffInDays($today);
                                @endphp
                                <a href="{{ route('owner.returns.index', ['tab' => 'overdue']) }}" class="notif-item" data-rental-id="{{ $rId }}" onclick="markNotifRead('{{ $rId }}', this)">
                                    <div class="notif-icon-box">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </div>
                                    <div class="notif-content">
                                        <div><strong>#RENT-{{ $rId }}</strong> คุณ <strong>{{ $custName }}</strong> เลยกำหนดคืนแล้ว {{ $daysLate }} วัน</div>
                                        <div class="notif-time">
                                            กำหนดคืน: {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                                            <span class="notif-read-tag" style="display:none;"><i class="fa-solid fa-check"></i> อ่านแล้ว</span>
                                        </div>
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

                <!-- USER PROFILE BUTTON (OUTLINE STYLE) -->
                <div class="btn-nav-outline" title="บัญชีผู้ดูแลร้าน KYRIX">
                    <i class="fa-regular fa-user" style="font-size: 13px;"></i>
                    <span>{{ auth()->user()->name ?? 'เจ้าของร้าน' }}</span>
                </div>

                <!-- LOGOUT BUTTON (SOLID MAROON STYLE) -->
                <form action="{{ route('logout') }}" method="POST" style="display:inline; margin:0;">
                    @csrf
                    <button type="submit" class="btn-nav-solid" title="ออกจากระบบ" onclick="return confirm('ต้องการออกจากระบบหรือไม่?')">
                        <span>ออกจากระบบ</span>
                    </button>
                </form>

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

        function getReadNotifs() {
            return JSON.parse(localStorage.getItem('kyrix_read_notifs') || '{}');
        }

        // อัปเดตหน้าตารายการ + ตัวเลขแจ้งเตือนที่หัวกระดิ่ง ให้ตรงกับสถานะอ่านแล้วใน localStorage
        function refreshNotifUI() {
            const readNotifs = getReadNotifs();
            const items = document.querySelectorAll('.notif-item[data-rental-id]');
            let unreadCount = 0;

            items.forEach(function(item) {
                const id = item.getAttribute('data-rental-id');
                const isRead = Object.prototype.hasOwnProperty.call(readNotifs, id);
                const readTag = item.querySelector('.notif-read-tag');

                item.classList.toggle('is-read', isRead);
                if (readTag) {
                    readTag.style.display = isRead ? 'inline-flex' : 'none';
                }
                if (!isRead) {
                    unreadCount++;
                }
            });

            const badge = document.getElementById('notifBadgeCount');
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        function markNotifRead(rentalId, el) {
            let readNotifs = getReadNotifs();
            const now = new Date().getTime();
            readNotifs[rentalId] = now;
            localStorage.setItem('kyrix_read_notifs', JSON.stringify(readNotifs));

            // อัปเดตทันทีให้เห็นผลก่อนเปลี่ยนหน้า
            refreshNotifUI();
        }

        document.addEventListener("DOMContentLoaded", function() {
            let readNotifs = getReadNotifs();
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

            refreshNotifUI();
        });
    </script>

    @stack('scripts')
</body>
</html>