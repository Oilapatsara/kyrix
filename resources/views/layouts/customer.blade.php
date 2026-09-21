<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'KYRIX | ร้านเช่าชุดสายฝอ สายหวาน ชุดออกงาน')
    </title>


    <!-- Google Fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <!-- Font Awesome -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <style>
        :root {
            --primary: #7a1f2b;
            --primary-dark: #58141d;
            --primary-light: #9c2e3d;
            --primary-soft: #fbf0f2;

            --gold: #c69c4c;
            --gold-light: #f7eedb;

            --bg: #faf8f5;
            --surface: #ffffff;

            --text-main: #2a2421;
            --text-muted: #736b66;

            --border: #ede8e3;

            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;

            --shadow-sm:
                0 4px 12px rgba(0, 0, 0, 0.03);

            --shadow-md:
                0 10px 30px rgba(0, 0, 0, 0.06);

            --shadow-lg:
                0 20px 50px rgba(122, 31, 43, 0.12);
        }


        /* ==============================
           RESET
        ============================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }


        body {
            font-family:
                'Prompt',
                'Plus Jakarta Sans',
                sans-serif;

            background-color: var(--bg);
            color: var(--text-main);

            line-height: 1.6;

            min-height: 100vh;

            display: flex;
            flex-direction: column;
        }


        a {
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }


        /* ==============================
           HEADER
        ============================== */

        .header {
            background: var(--surface);

            border-bottom:
                1px solid var(--border);

            position: sticky;
            top: 0;

            z-index: 100;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.03);
        }


        .header-container {
            max-width: 1280px;

            margin: 0 auto;

            padding:
                14px 24px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;
        }


        /* ==============================
           LOGO
        ============================== */

        .logo-wrap {
            display: flex;
            flex-direction: column;

            flex-shrink: 0;
        }


        .logo {
            font-family:
                'Plus Jakarta Sans',
                sans-serif;

            font-size: 28px;

            font-weight: 800;

            letter-spacing: 2px;

            color: var(--primary);

            line-height: 1;
        }


        .logo-sub {
            font-size: 10px;

            letter-spacing: 3px;

            color: var(--gold);

            font-weight: 600;

            text-transform: uppercase;

            margin-top: 3px;
        }


        /* ==============================
           NAVIGATION
        ============================== */

        .nav-links {
            display: flex;

            align-items: center;

            gap: 28px;

            list-style: none;

            position: relative;
        }


        .nav-links a {
            font-size: 15px;

            font-weight: 500;

            color: var(--text-main);

            padding:
                6px 0;

            position: relative;

            display: block;

            transition:
                color 0.25s ease;
        }


        .nav-links a:hover {
            color: var(--primary);
        }


        .nav-links a.active {
            color: var(--primary);
        }


        .nav-indicator {
            position: absolute;

            left: 0;

            bottom: -1px;

            width: 0;

            height: 2px;

            background:
                var(--primary);

            border-radius: 2px;

            pointer-events: none;

            transition:
                transform 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                width 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }


        /* ==============================
           HEADER ACTIONS
        ============================== */

        .header-actions {
            display: flex;

            align-items: center;

            gap: 16px;

            flex-shrink: 0;
        }


        /* ==============================
           SEARCH
        ============================== */

        .search-box {
            position: relative;

            width: 220px;

            flex-shrink: 0;
        }


        .search-box input {
            width: 100%;

            padding:
                9px 36px 9px 14px;

            border-radius: 30px;

            border:
                1px solid var(--border);

            background: #faf8f5;

            font-family: inherit;

            font-size: 13px;

            transition:
                all 0.2s;
        }


        .search-box input:focus {
            outline: none;

            border-color:
                var(--primary);

            background: #fff;

            box-shadow:
                0 0 0 3px rgba(122, 31, 43, 0.1);
        }


        .search-box button {
            position: absolute;

            right: 12px;

            top: 50%;

            transform:
                translateY(-50%);

            border: none;

            background: none;

            color:
                var(--text-muted);

            cursor: pointer;
        }


        /* ==============================
           CART
        ============================== */

        .cart-btn {
            position: relative;

            background:
                var(--primary-soft);

            color:
                var(--primary);

            width: 42px;

            height: 42px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 18px;

            transition:
                all 0.2s;

            flex-shrink: 0;
        }


        .cart-btn:hover {
            background:
                var(--primary);

            color: #fff;

            transform:
                translateY(-2px);
        }


        .cart-badge {
            position: absolute;

            top: -4px;

            right: -4px;

            background:
                var(--gold);

            color: #fff;

            font-size: 11px;

            font-weight: 700;

            width: 20px;

            height: 20px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            box-shadow:
                0 2px 6px rgba(0, 0, 0, 0.15);
        }


        /* ==============================
           USER MENU
        ============================== */

        .user-menu {
            position: relative;
        }


        .user-trigger {
            display: flex;

            align-items: center;

            gap: 8px;

            padding:
                5px 12px 5px 5px;

            border-radius: 30px;

            background: #fff;

            border:
                1px solid var(--border);

            cursor: pointer;

            font-size: 14px;

            font-weight: 500;

            transition:
                all 0.2s ease;

            font-family: inherit;
        }


        .user-trigger:hover,
        .user-menu.active .user-trigger {
            border-color:
                var(--primary);

            background:
                var(--primary-soft);
        }


        .user-avatar {
            width: 34px;

            height: 34px;

            border-radius: 50%;

            background:
                var(--primary);

            color: #fff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 13px;

            font-weight: 700;

            flex-shrink: 0;
        }


        .profile-name {
            max-width: 130px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

            font-weight: 500;
        }


        .dropdown-arrow {
            transition:
                transform 0.25s ease;
        }


        .user-menu.active .dropdown-arrow {
            transform:
                rotate(180deg);
        }


        /* ==============================
           DROPDOWN
        ============================== */

        .dropdown-menu {
            position: absolute;

            right: 0;

            top:
                calc(100% + 8px);

            background: #fff;

            min-width: 260px;

            border-radius:
                var(--radius-md);

            box-shadow:
                0 12px 36px rgba(0, 0, 0, 0.1);

            border:
                1px solid var(--border);

            padding: 8px 0;

            display: none;

            z-index: 200;
        }


        .user-menu:hover .dropdown-menu,
        .user-menu.active .dropdown-menu {
            display: block;
        }


        .dropdown-item {
            display: flex;

            align-items: center;

            gap: 10px;

            padding:
                10px 18px;

            font-size: 14px;

            color:
                var(--text-main);

            width: 100%;

            transition:
                all 0.2s ease;
        }


        .dropdown-item:hover {
            background:
                var(--primary-soft);

            color:
                var(--primary);
        }


        .dropdown-item i {
            width: 18px;

            text-align: center;
        }


        .dropdown-divider {
            height: 1px;

            background:
                var(--border);

            margin: 6px 0;
        }


        /* ==============================
           DROPDOWN USER INFO
        ============================== */

        .profile-dropdown-header {
            padding:
                14px 18px;

            border-bottom:
                1px solid var(--border);

            display: flex;

            align-items: center;

            gap: 12px;
        }


        .profile-dropdown-name {
            font-size: 14px;

            font-weight: 600;

            color:
                var(--text-main);

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        .profile-dropdown-role {
            font-size: 12px;

            color:
                var(--text-muted);

            margin-top: 2px;
        }


        .logout-item {
            color: #dc2626 !important;
        }


        .logout-item:hover {
            background: #fef2f2 !important;

            color: #b91c1c !important;
        }


        /* ==============================
           BUTTONS
        ============================== */

        .btn {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            padding:
                11px 22px;

            border-radius: 10px;

            font-weight: 600;

            font-size: 14px;

            font-family: inherit;

            cursor: pointer;

            border: none;

            transition:
                all 0.25s ease;

            text-decoration: none;
        }


        .btn-primary {
            background:
                var(--primary);

            color: #fff;
        }


        .btn-primary:hover {
            background:
                var(--primary-dark);

            box-shadow:
                0 8px 20px rgba(122, 31, 43, 0.25);

            transform:
                translateY(-2px);
        }


        .btn-secondary {
            background: #fff;

            color:
                var(--primary);

            border:
                1px solid var(--primary);
        }


        .btn-secondary:hover {
            background:
                var(--primary-soft);
        }


        .btn-gold {
            background:
                var(--gold);

            color: #fff;
        }


        .btn-gold:hover {
            background:
                #b58c3f;

            box-shadow:
                0 8px 20px rgba(198, 156, 76, 0.3);
        }


        .btn-sm {
            padding:
                7px 14px;

            font-size: 13px;

            border-radius: 8px;
        }


        .btn-block {
            width: 100%;
        }


        /* ==============================
           FLASH ALERTS
        ============================== */

        .alerts-container {
            max-width: 1280px;

            margin:
                20px auto 0;

            padding:
                0 24px;
        }


        .alert {
            padding:
                14px 20px;

            border-radius:
                var(--radius-md);

            margin-bottom:
                16px;

            display: flex;

            align-items: center;

            gap: 12px;

            font-size: 14px;

            font-weight: 500;

            box-shadow:
                var(--shadow-sm);
        }


        .alert-success {
            background: #edfbf3;

            color: #1a7f47;

            border:
                1px solid #b7ecd0;
        }


        .alert-error,
        .alert-danger {
            background: #fdf2f2;

            color: #b91c1c;

            border:
                1px solid #fecaca;
        }


        .alert-warning {
            background: #fffbeb;

            color: #b45309;

            border:
                1px solid #fde68a;
        }


        /* ==============================
           MAIN
        ============================== */

        .main-content {
            flex: 1;
        }


        /* ==============================
           BADGES
        ============================== */

        .badge {
            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding:
                4px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;
        }


        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }


        .badge-info {
            background: #e0f2fe;
            color: #0369a1;
        }


        .badge-primary {
            background:
                var(--primary-soft);

            color:
                var(--primary);
        }


        .badge-indigo {
            background: #e0e7ff;
            color: #4338ca;
        }


        .badge-teal {
            background: #ccfbf1;
            color: #0f766e;
        }


        .badge-amber {
            background: #ffedd5;
            color: #c2410c;
        }


        .badge-success {
            background: #dcfce7;
            color: #15803d;
        }


        .badge-emerald {
            background: #d1fae5;
            color: #065f46;
        }


        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }


        /* ==============================
           FOOTER
        ============================== */

        .footer {
            background:
                #1f1b19;

            color:
                #d1c8c1;

            margin-top:
                70px;

            padding-top:
                60px;

            border-top:
                3px solid var(--primary);
        }


        .footer-container {
            max-width: 1280px;

            margin: 0 auto;

            padding:
                0 24px 50px;

            display: grid;

            grid-template-columns:
                2fr 1fr 1fr 1.5fr;

            gap: 40px;
        }


        .footer-col h4 {
            color: #fff;

            font-size: 17px;

            margin-bottom: 20px;

            position: relative;

            padding-bottom: 8px;
        }


        .footer-col h4::after {
            content: '';

            position: absolute;

            bottom: 0;

            left: 0;

            width: 35px;

            height: 2px;

            background:
                var(--gold);
        }


        .footer-col p {
            font-size: 14px;

            line-height: 1.8;

            color: #a69e97;
        }


        .footer-links {
            list-style: none;
        }


        .footer-links li {
            margin-bottom: 10px;
        }


        .footer-links a {
            color:
                #a69e97;

            font-size: 14px;
        }


        .footer-links a:hover {
            color:
                var(--gold);

            padding-left: 5px;
        }


        .contact-info {
            list-style: none;
        }


        .contact-info li {
            display: flex;

            align-items: flex-start;

            gap: 12px;

            margin-bottom: 14px;

            font-size: 14px;

            color: #a69e97;
        }


        .contact-info i {
            color:
                var(--gold);

            margin-top: 4px;
        }


        .footer-bottom {
            border-top:
                1px solid #332d29;

            padding:
                20px 24px;

            text-align: center;

            font-size: 13px;

            color:
                #807771;
        }


        /* ==============================
           BACK TO TOP
        ============================== */

        .back-to-top {
            position: fixed;

            right: 24px;

            bottom: 24px;

            width: 50px;

            height: 50px;

            border: none;

            border-radius: 50%;

            background:
                var(--primary);

            color: #fff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 17px;

            cursor: pointer;

            opacity: 0;

            visibility: hidden;

            transform:
                translateY(25px);

            transition:
                opacity 0.3s ease,
                visibility 0.3s ease,
                transform 0.35s ease,
                background 0.25s ease,
                box-shadow 0.25s ease;

            z-index: 99999;

            box-shadow:
                0 8px 22px rgba(122, 31, 43, 0.25);
        }


        .back-to-top.show {
            opacity: 1;

            visibility: visible;

            transform:
                translateY(0);
        }


        .back-to-top:hover {
            background:
                var(--primary-dark);

            transform:
                translateY(-7px);

            box-shadow:
                0 14px 30px rgba(122, 31, 43, 0.35);
        }


        /* ==============================
           MOBILE
        ============================== */

        .mobile-menu-btn {
            display: none;

            background: none;

            border: none;

            font-size: 22px;

            color:
                var(--text-main);

            cursor: pointer;
        }


        @media (max-width: 1200px) {

            .nav-links {
                gap: 16px;
            }

            .search-box {
                width: 180px;
            }

        }


        @media (max-width: 992px) {

            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .search-box {
                display: none;
            }

            .footer-container {
                grid-template-columns:
                    1fr 1fr;
            }

            .header-actions {
                gap: 10px;
            }

        }


        @media (max-width: 600px) {

            .header-container {
                padding:
                    12px 16px;

                gap: 10px;
            }

            .logo {
                font-size: 24px;
            }

            .logo-sub {
                font-size: 8px;
            }

            .profile-name {
                display: none;
            }

            .guest-actions {
                gap: 5px !important;
            }

            .guest-actions .btn-sm {
                padding:
                    6px 9px;

                font-size: 11px;
            }

            .footer-container {
                grid-template-columns:
                    1fr;
            }

            .top-banner {
                display: none;
            }

            .dropdown-menu {
                right: -20px;

                min-width: 240px;
            }

            .back-to-top {
                width: 44px;
                height: 44px;

                right: 16px;
                bottom: 16px;
            }

        }
    </style>


    @stack('styles')

</head>


<body>


    <!-- ==============================
         HEADER
    ============================== -->

    <header class="header">

        <div class="header-container">


            <!-- Logo -->

            <a href="{{ route('home') }}" class="logo-wrap">

                <span class="logo">
                    KYRIX
                </span>

                <span class="logo-sub">
                    DRESS RENTAL
                </span>

            </a>


            <!-- Navigation -->

            <ul class="nav-links">


                <li>

                    <a href="{{ route('home') }}"
                        class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        หน้าแรก
                    </a>

                </li>


                <li>

                    <a href="{{ route('products.index') }}"
                        class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        ชุดทั้งหมด
                    </a>

                </li>


                <li>

                    <a href="{{ route('home') }}#categories" class="js-hash-link" data-hash="categories">
                        ประเภทชุด
                    </a>

                </li>


                <li>

                    <a href="{{ route('home') }}#how-it-works" class="js-hash-link" data-hash="how-it-works">
                        วิธีการเช่า
                    </a>

                </li>


                <li>

                    <a href="{{ route('home') }}#contact" class="js-hash-link" data-hash="contact">
                        ติดต่อร้าน
                    </a>

                </li>


                <span class="nav-indicator" aria-hidden="true"></span>

            </ul>


            <!-- ==============================
                 HEADER ACTIONS
            ============================== -->

            <div class="header-actions">


                <!-- Search -->

                <form action="{{ route('products.index') }}" method="GET" class="search-box">

                    <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชุดสวย...">

                    <button type="submit">

                        <i class="fa-solid fa-magnifying-glass"></i>

                    </button>

                </form>


                <!-- Cart -->

                @php

                    $cart = session('cart', []);

                    $cartCount = collect($cart)->sum(function ($item) {
                        return (int) ($item['qty'] ?? 1);
                    });
                @endphp


                <a href="{{ route('cart.index') }}" class="cart-btn" title="ตะกร้าเช่าชุด">

                    <i class="fa-solid fa-bag-shopping"></i>


                    @if ($cartCount > 0)
                        <span class="cart-badge">

                            {{ $cartCount }}

                        </span>
                    @endif

                </a>


                <!-- ==============================
                     USER STATUS
                ============================== -->

                @php

                    $customerId = session('customer_id');

                    $customer = $customerId ? \App\Models\Customer::find($customerId) : null;

                    $customerLoggedIn = (bool) session('customer_logged_in', false);

                    $authLoggedIn = auth()->check();

                    /*
                    |--------------------------------------------------------------------------
                    | Owner / Admin
                    |--------------------------------------------------------------------------
                    */

                    $isOwner = $authLoggedIn && in_array(auth()->user()->role ?? null, ['owner', 'admin'], true);

                    /*
                    |--------------------------------------------------------------------------
                    | Customer
                    |--------------------------------------------------------------------------
                    */

                    $isCustomer = $customerLoggedIn && $customer;

                    /*
                    |--------------------------------------------------------------------------
                    | Logged In
                    |--------------------------------------------------------------------------
                    */

                    $isLoggedIn = $isOwner || $isCustomer || $authLoggedIn;

                    /*
                    |--------------------------------------------------------------------------
                    | Display Name
                    |--------------------------------------------------------------------------
                    */

                    if ($isOwner) {
                        $displayName = auth()->user()->name ?? 'เจ้าของร้าน';

                        $roleLabel = 'เจ้าของร้าน / ผู้ดูแลระบบ';
                    } elseif ($isCustomer) {
                        $displayName = session('customer_name');

                        if (empty($displayName)) {
                            $displayName = trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));
                        }

                        if (empty($displayName)) {
                            $displayName = 'ลูกค้า KYRIX';
                        }

                        $roleLabel = 'สมาชิก KYRIX';
                    } elseif ($authLoggedIn) {
                        $displayName = auth()->user()->name ?? 'ผู้ใช้งาน';

                        $roleLabel = 'ผู้ใช้งาน';
                    } else {
                        $displayName = null;

                        $roleLabel = null;
                    }

                    $userInitial = $displayName ? mb_substr($displayName, 0, 1, 'UTF-8') : '';
                @endphp


                @if ($isLoggedIn)

                    <!-- ==============================
                         LOGGED IN
                    ============================== -->

                    <div class="user-menu" id="userMenu">


                        <!-- User Trigger -->

                        <button type="button" class="user-trigger" id="userTrigger" aria-expanded="false"
                            aria-label="เมนูผู้ใช้งาน">

                            <div class="user-avatar">

                                {{ $userInitial }}

                            </div>


                            <span class="profile-name">

                                {{ $displayName }}

                            </span>


                            <i class="fa-solid fa-chevron-down dropdown-arrow"
                                style="
                                    font-size:10px;
                                    margin-left:2px;
                                "></i>

                        </button>


                        <!-- Dropdown -->

                        <div class="dropdown-menu" id="userDropdown">


                            <!-- User Info -->

                            <div class="profile-dropdown-header">

                                <div class="user-avatar"
                                    style="
                                        width:42px;
                                        height:42px;
                                        font-size:16px;
                                    ">

                                    {{ $userInitial }}

                                </div>


                                <div
                                    style="
                                        min-width:0;
                                    ">

                                    <div class="profile-dropdown-name">

                                        {{ $displayName }}

                                    </div>


                                    <div class="profile-dropdown-role">

                                        {{ $roleLabel }}

                                    </div>

                                </div>

                            </div>


                            <!-- OWNER / ADMIN MENU -->

                            @if ($isOwner)

                                @if (Route::has('owner.dashboard'))
                                    <a href="{{ route('owner.dashboard') }}"
                                        class="dropdown-item">

                                        <i class="fa-solid fa-gauge-high"></i>

                                        แดชบอร์ดเจ้าของร้าน

                                    </a>
                                @endif


                                @if (Route::has('owner.bookings.index'))
                                    <a href="{{ route('owner.bookings.index') }}"
                                        class="dropdown-item">

                                        <i class="fa-solid fa-calendar-check"></i>

                                        จัดการการจอง

                                    </a>
                                @endif


                                @if (Route::has('owner.dresses.index'))
                                    <a href="{{ route('owner.dresses.index') }}"
                                        class="dropdown-item">

                                        <i class="fa-solid fa-shirt"></i>

                                        จัดการชุด

                                    </a>
                                @endif
                            @else
                                <!-- CUSTOMER MENU -->

                                @if (Route::has('customer.dashboard'))
                                    <a href="{{ route('customer.dashboard') }}"
                                        class="dropdown-item">

                                        <i class="fa-solid fa-gauge-high"></i>

                                        แดชบอร์ดของฉัน

                                    </a>
                                @endif


                                @if (Route::has('rentals.index'))
                                    <a href="{{ route('rentals.index') }}"
                                        class="dropdown-item">

                                        <i class="fa-solid fa-calendar-check"></i>

                                        การจองของฉัน

                                    </a>
                                @endif


                                @if (Route::has('rentals.history'))
                                    <a href="{{ route('rentals.history') }}"
                                        class="dropdown-item">

                                        <i class="fa-solid fa-clock-rotate-left"></i>

                                        ประวัติการเช่าชุด

                                    </a>
                                @endif


                                @if (Route::has('profile.index'))
                                    <a href="{{ route('profile.index') }}"
                                        class="dropdown-item">

                                        <i class="fa-solid fa-user-pen"></i>

                                        โปรไฟล์ของฉัน

                                    </a>
                                @endif

                            @endif


                            <div class="dropdown-divider"></div>


                            <!-- Logout -->

                            @if (Route::has('logout'))
                                <form action="{{ route('logout') }}" method="POST" style="margin:0;">

                                    @csrf

                                    <button type="submit" class="dropdown-item logout-item"
                                        style="
                                            width:100%;
                                            border:none;
                                            background:none;
                                            cursor:pointer;
                                            text-align:left;
                                            font-family:inherit;
                                            font-size:14px;
                                        "
                                        onclick="
                                            return confirm(
                                                'ต้องการออกจากระบบหรือไม่?'
                                            );
                                        ">

                                        <i class="fa-solid fa-right-from-bracket"></i>

                                        ออกจากระบบ

                                    </button>

                                </form>
                            @endif

                        </div>

                    </div>
                @else
                    <!-- ==============================
                         GUEST
                    ============================== -->

                    <div class="guest-actions"
                        style="
                            display:flex;
                            align-items:center;
                            gap:8px;
                        ">

                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">
                                เข้าสู่ระบบ
                            </a>
                        @endif


                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                                สมัครสมาชิก
                            </a>
                        @endif

                    </div>

                @endif


            </div>

        </div>

    </header>


    <!-- ==============================
         FLASH ALERTS
    ============================== -->

    @if (session('success') || session('error') || session('warning'))

        <div class="alerts-container">


            @if (session('success'))
                <div class="alert alert-success">

                    <i class="fa-solid fa-circle-check"></i>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>
            @endif


            @if (session('error'))
                <div class="alert alert-error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>
                        {{ session('error') }}
                    </span>

                </div>
            @endif


            @if (session('warning'))
                <div class="alert alert-warning">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    <span>
                        {{ session('warning') }}
                    </span>

                </div>
            @endif


        </div>

    @endif


    <!-- ==============================
         MAIN CONTENT
    ============================== -->

    <main class="main-content">

        @yield('content')

    </main>


    <!-- ==============================
         FOOTER
    ============================== -->

    <footer class="footer">


        <div class="footer-container">


            <!-- About -->

            <div class="footer-col">

                <span class="logo" style="color:#fff;">
                    KYRIX
                </span>


                <p
                    style="
                        margin-top:14px;
                        max-width:320px;
                    ">

                    ร้านเช่าชุดออนไลน์อันดับหนึ่ง

                    บริการเช่าชุดสายฝอ
                    สายหวาน
                    ชุดออกงาน

                    เกรดพรีเมียม

                    คัตติ้งเนี้ยบ
                    สะอาด
                    หอม

                    พร้อมใช้งานสำหรับวันสำคัญของคุณ

                </p>

            </div>


            <!-- Footer Menu -->

            <div class="footer-col">

                <h4>
                    เมนูลัด
                </h4>


                <ul class="footer-links">


                    <li>

                        <a href="{{ route('home') }}">
                            หน้าแรก
                        </a>

                    </li>


                    <li>

                        <a href="{{ route('products.index') }}">
                            ชุดทั้งหมด
                        </a>

                    </li>


                    <li>

                        <a href="{{ route('home') }}#how-it-works">
                            ขั้นตอนการเช่าชุด
                        </a>

                    </li>


                    <li>

                        <a href="{{ route('cart.index') }}">
                            ตะกร้าเช่าชุด
                        </a>

                    </li>


                    @if (Route::has('rentals.index'))
                        <li>

                            <a
                                href="{{ route('rentals.index') }}">
                                ติดตามสถานะการจอง
                            </a>

                        </li>
                    @endif


                </ul>

            </div>


            <!-- Categories -->

            <div class="footer-col">

                <h4>
                    ประเภทชุด
                </h4>


                <ul class="footer-links">


                    <li>

                        <a
                            href="{{ route('products.index') }}?category_id=1">
                            ชุดสายฝอ
                        </a>

                    </li>


                    <li>

                        <a
                            href="{{ route('products.index') }}?category_id=2">
                            ชุดสายหวาน
                        </a>

                    </li>


                    <li>

                        <a
                            href="{{ route('products.index') }}?category_id=3">
                            ชุดออกงาน
                        </a>

                    </li>


                    <li>

                        <a
                            href="{{ route('products.index') }}?category_id=4">
                            มินิเดรส
                        </a>

                    </li>


                    <li>

                        <a
                            href="{{ route('products.index') }}?category_id=6">
                            เสื้อ & ท็อปส์
                        </a>

                    </li>


                    <li>

                        <a
                            href="{{ route('products.index') }}?category_id=6">
                            กระโปรง
                        </a>

                    </li>


                    <li>

                        <a
                            href="{{ route('products.index') }}?category_id=6">
                            กางเกง
                        </a>

                    </li>


                </ul>

            </div>


            <!-- Contact -->

            <div class="footer-col" id="contact">

                <h4>
                    ติดต่อร้าน KYRIX
                </h4>


                <ul class="contact-info">


                    <li>

                        <i class="fa-solid fa-location-dot"></i>

                        <span>

                            77 ตำบลในเมือง

                            อำเภอเมือง

                            จังหวัดนครราชสีมา 30000

                        </span>

                    </li>


                    <li>

                        <i class="fa-solid fa-phone"></i>

                        <span>
                            0652599072
                        </span>

                    </li>


                    <li>

                        <i class="fa-solid fa-envelope"></i>

                        <span>
                            apatsara1a@gmail.com
                        </span>

                    </li>


                    <li>

                        <i class="fa-solid fa-clock"></i>

                        <span>

                            เปิดบริการทุกวัน:

                            10:00 - 20:00 น.

                        </span>

                    </li>


                </ul>

            </div>


        </div>


        <div class="footer-bottom">

            &copy; 2026 KYRIX Dress Rental.

            สงวนลิขสิทธิ์ทุกประการ.

            ระบบจัดการเช่าชุดครบวงจร.

        </div>

    </footer>


    <!-- ==============================
         BACK TO TOP
    ============================== -->

    <button type="button" class="back-to-top" id="backToTop" aria-label="กลับขึ้นด้านบน" title="กลับขึ้นด้านบน">

        <i class="fa-solid fa-arrow-up"></i>

    </button>


    <!-- ==============================
         JAVASCRIPT
    ============================== -->

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {


                /* =====================================
                   Navigation Sliding Indicator
                ===================================== */

                const nav =
                    document.querySelector(
                        '.nav-links'
                    );


                const indicator =
                    document.querySelector(
                        '.nav-indicator'
                    );


                const links =
                    document.querySelectorAll(
                        '.nav-links a'
                    );


                if (
                    nav &&
                    indicator &&
                    links.length
                ) {


                    function moveIndicator(
                        link,
                        animate = true
                    ) {

                        if (!link) {

                            indicator.style.width =
                                '0px';

                            return;
                        }


                        const navRect =
                            nav.getBoundingClientRect();


                        const linkRect =
                            link.getBoundingClientRect();


                        const left =
                            linkRect.left -
                            navRect.left;


                        const width =
                            linkRect.width;


                        indicator.style.transition =
                            animate ?
                            'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1), width 0.45s cubic-bezier(0.4, 0, 0.2, 1)' :
                            'none';


                        indicator.style.width =
                            width + 'px';


                        indicator.style.transform =
                            `translateX(${left}px)`;


                        if (!animate) {

                            requestAnimationFrame(
                                function() {

                                    indicator.style.transition =
                                        'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1), width 0.45s cubic-bezier(0.4, 0, 0.2, 1)';

                                }
                            );

                        }

                    }


                    function setActive(
                        link
                    ) {

                        links.forEach(
                            function(item) {

                                item.classList.remove(
                                    'active'
                                );

                            }
                        );


                        if (link) {

                            link.classList.add(
                                'active'
                            );


                            moveIndicator(
                                link,
                                true
                            );

                        }

                    }


                    let activeLink =
                        document.querySelector(
                            '.nav-links a.active'
                        );


                    const hash =
                        window.location.hash;


                    if (hash) {

                        const hashLink =
                            document.querySelector(
                                `.nav-links a[data-hash="${hash.substring(1)}"]`
                            );


                        if (hashLink) {

                            activeLink =
                                hashLink;

                        }

                    }


                    if (activeLink) {

                        moveIndicator(
                            activeLink,
                            false
                        );

                    }


                    links.forEach(
                        function(link) {

                            link.addEventListener(
                                'click',
                                function(e) {

                                    const href =
                                        this.getAttribute(
                                            'href'
                                        ) || '';


                                    if (
                                        this.classList.contains(
                                            'js-hash-link'
                                        ) &&
                                        href.includes('#')
                                    ) {

                                        const isSamePage =
                                            href.startsWith(
                                                window.location.origin +
                                                window.location.pathname +
                                                '#'
                                            ) ||
                                            href.startsWith(
                                                '#'
                                            );


                                        if (isSamePage) {

                                            e.preventDefault();


                                            setActive(
                                                this
                                            );


                                            const hashValue =
                                                this.dataset.hash;


                                            const section =
                                                document.getElementById(
                                                    hashValue
                                                );


                                            if (
                                                section
                                            ) {

                                                section.scrollIntoView({
                                                    behavior: 'smooth',

                                                    block: 'start'
                                                });

                                            }


                                            history.pushState(
                                                null,
                                                '',
                                                '#' +
                                                hashValue
                                            );


                                            return;

                                        }

                                    }


                                    setActive(
                                        this
                                    );

                                }
                            );

                        }
                    );


                    window.addEventListener(
                        'resize',
                        function() {

                            const current =
                                document.querySelector(
                                    '.nav-links a.active'
                                );


                            if (current) {

                                moveIndicator(
                                    current,
                                    false
                                );

                            }

                        }
                    );


                    window.addEventListener(
                        'popstate',
                        function() {

                            const currentHash =
                                window.location.hash;


                            if (
                                currentHash
                            ) {

                                const hashLink =
                                    document.querySelector(
                                        `.nav-links a[data-hash="${currentHash.substring(1)}"]`
                                    );


                                if (
                                    hashLink
                                ) {

                                    setActive(
                                        hashLink
                                    );

                                    return;

                                }

                            }


                            const homeLink =
                                document.querySelector(
                                    '.nav-links a[href="{{ route('home') }}"]'
                                );


                            if (
                                homeLink
                            ) {

                                setActive(
                                    homeLink
                                );

                            }

                        }
                    );

                }


                /* =====================================
                   Back To Top
                ===================================== */

                const backToTop =
                    document.getElementById(
                        'backToTop'
                    );


                if (
                    backToTop
                ) {


                    function toggleBackToTop() {

                        if (
                            window.scrollY >
                            300
                        ) {

                            backToTop.classList.add(
                                'show'
                            );

                        } else {

                            backToTop.classList.remove(
                                'show'
                            );

                        }

                    }


                    window.addEventListener(
                        'scroll',
                        toggleBackToTop, {
                            passive: true
                        }
                    );


                    toggleBackToTop();


                    backToTop.addEventListener(
                        'click',
                        function() {

                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });

                        }
                    );

                }


                /* =====================================
                   User Dropdown
                ===================================== */

                const userMenu =
                    document.getElementById(
                        'userMenu'
                    );


                const userTrigger =
                    document.getElementById(
                        'userTrigger'
                    );


                if (
                    userMenu &&
                    userTrigger
                ) {


                    userTrigger.addEventListener(
                        'click',
                        function(e) {

                            e.preventDefault();

                            e.stopPropagation();


                            userMenu.classList.toggle(
                                'active'
                            );


                            const isExpanded =
                                userMenu.classList.contains(
                                    'active'
                                );


                            userTrigger.setAttribute(
                                'aria-expanded',
                                isExpanded ?
                                'true' :
                                'false'
                            );

                        }
                    );


                    document.addEventListener(
                        'click',
                        function(e) {

                            if (
                                !userMenu.contains(
                                    e.target
                                )
                            ) {

                                userMenu.classList.remove(
                                    'active'
                                );


                                userTrigger.setAttribute(
                                    'aria-expanded',
                                    'false'
                                );

                            }

                        }
                    );


                    document.addEventListener(
                        'keydown',
                        function(e) {

                            if (
                                e.key ===
                                'Escape'
                            ) {

                                userMenu.classList.remove(
                                    'active'
                                );


                                userTrigger.setAttribute(
                                    'aria-expanded',
                                    'false'
                                );

                            }

                        }
                    );

                }

            }
        );
    </script>


    @stack('scripts')

</body>

</html>
