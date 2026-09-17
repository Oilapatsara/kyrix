<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'KYRIX | ร้านเช่าชุดราตรี ชุดไทย ชุดแต่งงาน สูทสากล')
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
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 50px rgba(122, 31, 43, 0.12);
        }


        /* ==============================
           Reset
        ============================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Prevent unstyled giant Laravel pagination SVGs */
        nav svg,
        .pagination svg,
        .pagination-wrap svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
            max-width: 1.25rem !important;
            max-height: 1.25rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
            flex-shrink: 0 !important;
        }

        /* ==============================
           Global Bootstrap-5 Pagination Style
        ============================== */
        .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
            gap: 6px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 14px;
            color: var(--primary);
            text-decoration: none;
            background-color: #fff;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .page-item.active .page-link {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(122, 31, 43, 0.3);
        }
        .page-item.disabled .page-link {
            color: #bbb;
            pointer-events: none;
            background-color: #fafafa;
            border-color: var(--border);
        }
        .page-item .page-link:hover:not(.active) {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-1px);
        }
        .pagination-wrap {
            margin-top: 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .pagination-wrap p.text-sm {
            font-size: 13px;
            color: var(--text-muted);
        }


        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }


        body {
            font-family: 'Prompt', 'Plus Jakarta Sans', sans-serif;
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
           Top Notification Bar
        ============================== */

        .top-banner {
            background: #2a2421;
            color: #e5dfd8;
            font-size: 13px;
            padding: 8px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .top-banner a {
            color: var(--gold);
            font-weight: 500;
        }


        /* ==============================
           Main Navigation
        ============================== */

        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }


        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }


        /* ==============================
           Logo
        ============================== */

        .logo-wrap {
            display: flex;
            flex-direction: column;
        }


        .logo {
            font-family: 'Plus Jakarta Sans', sans-serif;
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
           Navigation Menu
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
            padding: 6px 0;
            position: relative;
            display: block;
            transition: color 0.25s ease;
        }


        .nav-links a:hover {
            color: var(--primary);
        }


        .nav-links a.active {
            color: var(--primary);
        }


        /* ==============================
           Sliding Indicator
        ============================== */

        .nav-indicator {
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 0;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
            pointer-events: none;
            margin: 0;
            padding: 0;
            transition:
                transform 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                width 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }


        /* ==============================
           Header Actions
        ============================== */

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }


        /* ==============================
           Search
        ============================== */

        .search-box {
            position: relative;
            width: 220px;
        }


        .search-box input {
            width: 100%;
            padding: 9px 36px 9px 14px;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: #faf8f5;
            font-family: inherit;
            font-size: 13px;
            transition: all 0.2s;
        }


        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(122, 31, 43, 0.1);
        }


        .search-box button {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: var(--text-muted);
            cursor: pointer;
        }


        /* ==============================
           Cart
        ============================== */

        .cart-btn {
            position: relative;
            background: var(--primary-soft);
            color: var(--primary);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.2s;
        }


        .cart-btn:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
        }


        .cart-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--gold);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }


        /* ==============================
           User Menu
        ============================== */

        .user-menu {
            position: relative;
        }


        .user-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px 5px 5px;
            border-radius: 30px;
            background: #fff;
            border: 1px solid var(--border);
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            font-family: inherit;
        }


        .user-trigger:hover {
            border-color: var(--primary);
            background: var(--primary-soft);
        }


        /* รูปโปรไฟล์ */

        .profile-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: 2px solid var(--primary-soft);
        }


        /* ถ้ายังไม่มีรูป */

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--primary);
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


        /* ==============================
           Dropdown
        ============================== */

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            min-width: 260px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            padding: 8px 0;
            display: none;
            z-index: 200;
        }


        .user-menu:hover .dropdown-menu {
            display: block;
        }


        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            font-size: 14px;
            color: var(--text-main);
            width: 100%;
            transition: all 0.2s ease;
        }


        .dropdown-item:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }


        .dropdown-item i {
            width: 18px;
            text-align: center;
        }


        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 6px 0;
        }


        /* ==============================
           Dropdown User Info
        ============================== */

        .profile-dropdown-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }


        .profile-dropdown-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }


        .profile-dropdown-role {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }


        /* ==============================
           Buttons
        ============================== */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease;
            text-decoration: none;
        }


        .btn-primary {
            background: var(--primary);
            color: #fff;
        }


        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 8px 20px rgba(122, 31, 43, 0.25);
            transform: translateY(-2px);
        }


        .btn-secondary {
            background: #fff;
            color: var(--primary);
            border: 1px solid var(--primary);
        }


        .btn-secondary:hover {
            background: var(--primary-soft);
        }


        .btn-gold {
            background: var(--gold);
            color: #fff;
        }


        .btn-gold:hover {
            background: #b58c3f;
            box-shadow: 0 8px 20px rgba(198, 156, 76, 0.3);
        }


        .btn-sm {
            padding: 7px 14px;
            font-size: 13px;
            border-radius: 8px;
        }


        .btn-block {
            width: 100%;
        }


        /* ==============================
           Flash Alerts
        ============================== */

        .alerts-container {
            max-width: 1280px;
            margin: 20px auto 0;
            padding: 0 24px;
        }


        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
        }


        .alert-success {
            background: #edfbf3;
            color: #1a7f47;
            border: 1px solid #b7ecd0;
        }


        .alert-error,
        .alert-danger {
            background: #fdf2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }


        .alert-warning {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }


        /* ==============================
           Main Content
        ============================== */

        .main-content {
            flex: 1;
        }


        /* ==============================
           Badges
        ============================== */

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
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
            background: var(--primary-soft);
            color: var(--primary);
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
           Footer
        ============================== */

        .footer {
            background: #1f1b19;
            color: #d1c8c1;
            margin-top: 70px;
            padding-top: 60px;
            border-top: 3px solid var(--primary);
        }


        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px 50px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
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
            background: var(--gold);
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
            color: #a69e97;
            font-size: 14px;
        }


        .footer-links a:hover {
            color: var(--gold);
            padding-left: 5px;
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
            color: var(--gold);
            margin-top: 4px;
        }


        .footer-bottom {
            border-top: 1px solid #332d29;
            padding: 20px 24px;
            text-align: center;
            font-size: 13px;
            color: #807771;
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
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transform: translateY(25px);
            transition:
                opacity 0.3s ease,
                visibility 0.3s ease,
                transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                background 0.25s ease,
                box-shadow 0.25s ease;
            z-index: 99999;
            box-shadow: 0 8px 22px rgba(122, 31, 43, 0.25);
        }


        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }


        .back-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-7px);
            box-shadow: 0 14px 30px rgba(122, 31, 43, 0.35);
        }


        .back-to-top:active {
            transform: translateY(-2px) scale(0.94);
        }


        .back-to-top i {
            transition: transform 0.25s ease;
        }


        .back-to-top:hover i {
            transform: translateY(-3px);
        }


        /* ==============================
           Mobile
        ============================== */

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: var(--text-main);
            cursor: pointer;
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
                grid-template-columns: 1fr 1fr;
            }
        }


        @media (max-width: 600px) {

            .footer-container {
                grid-template-columns: 1fr;
            }

            .top-banner {
                display: none;
            }

            .profile-name {
                display: none;
            }

            .back-to-top {
                width: 44px;
                height: 44px;
                right: 16px;
                bottom: 16px;
            }

            .dropdown-menu {
                right: -20px;
                min-width: 240px;
            }
        }
    </style>

    @stack('styles')

</head>


<body>


    <!-- ==============================
         Header Navigation
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
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
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


                <!-- Sliding Indicator -->

                <span class="nav-indicator" aria-hidden="true"></span>

            </ul>


            <!-- ==============================
                 Header Actions
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
                     User / Guest
                ============================== -->

                @php
                    $customerId = session('customer_id');
                    $customerName = session('customer_name', 'ผู้ใช้งาน');
                    $customer = null;

                    if ($customerId) {
                        $customer = \App\Models\Customer::find($customerId);
                    }
                @endphp


                @if ($customer)
                    <!-- ==============================
                         Logged In Profile
                    ============================== -->

                    <div class="user-menu">


                        <!-- Profile Button -->

                        <button type="button" class="user-trigger" aria-label="เมนูผู้ใช้งาน">

                            <div class="user-avatar">
                                {{ mb_substr($customerName, 0, 1) }}
                            </div>


                            <span class="profile-name">
                                {{ $customerName }}
                            </span>


                            <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>

                        </button>


                        <!-- ==============================
                             Profile Dropdown
                        ============================== -->

                        <div class="dropdown-menu">


                            <!-- User Info -->

                            <div class="profile-dropdown-header">


                                <div class="user-avatar"
                                    style="
                                        width: 42px;
                                        height: 42px;
                                        font-size: 16px;
                                    ">
                                    {{ mb_substr($customerName, 0, 1) }}
                                </div>


                                <div>

                                    <div class="profile-dropdown-name">
                                        {{ $customerName }}
                                    </div>

                                    <div class="profile-dropdown-role">
                                        สมาชิก KYRIX
                                    </div>

                                </div>

                            </div>


                            <!-- Dashboard -->

                            <a href="{{ route('customer.dashboard') }}" class="dropdown-item">
                                <i class="fa-solid fa-gauge-high"></i>
                                แดชบอร์ดของฉัน
                            </a>


                            <!-- Rentals -->

                            <a href="{{ route('rentals.index') }}" class="dropdown-item">
                                <i class="fa-solid fa-calendar-check"></i>
                                การจองของฉัน
                            </a>


                            <!-- Rental History -->

                            <a href="{{ route('rentals.history') }}" class="dropdown-item">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                ประวัติการเช่าชุด
                            </a>


                            <!-- Profile -->

                            <a href="{{ route('profile.index') }}" class="dropdown-item">
                                <i class="fa-solid fa-user-pen"></i>
                                โปรไฟล์ของฉัน
                            </a>


                            <div class="dropdown-divider"></div>


                            <!-- Logout -->

                            <form action="{{ route('logout') }}" method="POST">

                                @csrf

                                <button type="submit" class="dropdown-item"
                                    style="
                                        width: 100%;
                                        border: none;
                                        background: none;
                                        cursor: pointer;
                                        text-align: left;
                                        color: #dc2626;
                                        font-family: inherit;
                                    ">

                                    <i class="fa-solid fa-right-from-bracket"></i>

                                    ออกจากระบบ

                                </button>

                            </form>


                        </div>

                    </div>
                @else
                    <!-- ==============================
                         Guest
                    ============================== -->

                    <div
                        style="
                            display: flex;
                            gap: 8px;
                        ">

                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">
                            เข้าสู่ระบบ
                        </a>


                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                            สมัครสมาชิก
                        </a>

                    </div>
                @endif


            </div>

        </div>

    </header>


    <!-- ==============================
         Flash Alerts
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
         Main Content
    ============================== -->

    <main class="main-content">

        @yield('content')

    </main>


    <!-- ==============================
         Footer
    ============================== -->

    <footer class="footer">


        <div class="footer-container">


            <!-- About -->

            <div class="footer-col">


                <span class="logo" style="color: #fff;">
                    KYRIX
                </span>


                <p
                    style="
                        margin-top: 14px;
                        max-width: 320px;
                    ">
                    ร้านเช่าชุดออนไลน์อันดับหนึ่ง
                    บริการเช่าชุดราตรี ชุดไทย
                    ชุดแต่งงาน และสูทสากล
                    เกรดพรีเมียม
                    คัตติ้งเนี้ยบ สะอาด หอม
                    พร้อมใช้งานสำหรับวันสำคัญของคุณ
                </p>


                <div
                    style="
                        display: flex;
                        gap: 12px;
                        margin-top: 20px;
                    ">


                    <a href="javascript:void(0)" title="Facebook — เร็วๆ นี้"
                        style="
                            width: 36px;
                            height: 36px;
                            border-radius: 50%;
                            background: #2f2824;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: var(--gold);
                            opacity: 0.6;
                            cursor: default;
                        ">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>


                    <a href="javascript:void(0)" title="Instagram — เร็วๆ นี้"
                        style="
                            width: 36px;
                            height: 36px;
                            border-radius: 50%;
                            background: #2f2824;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: var(--gold);
                            opacity: 0.6;
                            cursor: default;
                        ">
                        <i class="fa-brands fa-instagram"></i>
                    </a>


                    <a href="javascript:void(0)" title="Line — เร็วๆ นี้"
                        style="
                            width: 36px;
                            height: 36px;
                            border-radius: 50%;
                            background: #2f2824;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: var(--gold);
                            opacity: 0.6;
                            cursor: default;
                        ">
                        <i class="fa-brands fa-line"></i>
                    </a>


                    <a href="javascript:void(0)" title="TikTok — เร็วๆ นี้"
                        style="
                            width: 36px;
                            height: 36px;
                            border-radius: 50%;
                            background: #2f2824;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: var(--gold);
                            opacity: 0.6;
                            cursor: default;
                        ">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>


                </div>


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


                    <li>
                        <a href="{{ route('rentals.index') }}">
                            ติดตามสถานะการจอง
                        </a>
                    </li>


                </ul>


            </div>


            <!-- Categories -->

            <div class="footer-col">


                <h4>
                    ประเภทชุด
                </h4>


                <ul class="footer-links">


                    <li>
                        <a href="{{ route('products.index') }}?category_id=1">
                            ชุดราตรียาว
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('products.index') }}?category_id=2">
                            เดรสค็อกเทล
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('products.index') }}?category_id=3">
                            ชุดไทยบรมพิมาน
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('products.index') }}?category_id=4">
                            ชุดแต่งงาน / พรีเวดดิ้ง
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('products.index') }}?category_id=6">
                            สูททักซิโด้สากล
                        </a>
                    </li>


                </ul>


            </div>


            <!-- Contact -->

            <div class="footer-col" id="contact">


                <h4>
                    ติดต่อร้าน KYRIX
                </h4>


                <ul class="contact-info" style="list-style: none;">


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
                            06525990723
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
         Back To Top
    ============================== -->

    <button type="button" class="back-to-top" id="backToTop" aria-label="กลับขึ้นด้านบน" title="กลับขึ้นด้านบน">
        <i class="fa-solid fa-arrow-up"></i>
    </button>


    <!-- ==============================
         JavaScript
    ============================== -->

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {


                /* =========================================
                   Navigation Sliding Indicator
                ========================================= */

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


                        if (!animate) {

                            indicator.style.transition =
                                'none';

                        } else {

                            indicator.style.transition =
                                'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1), width 0.45s cubic-bezier(0.4, 0, 0.2, 1)';

                        }


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


                    function setActive(link) {

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

                        activeLink.classList.add(
                            'active'
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


                                            if (section) {

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


                                    setActive(this);

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


                            if (currentHash) {

                                const hashLink =
                                    document.querySelector(
                                        `.nav-links a[data-hash="${currentHash.substring(1)}"]`
                                    );


                                if (hashLink) {

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


                            if (homeLink) {

                                setActive(
                                    homeLink
                                );

                            }

                        }
                    );

                }


                /* =========================================
                   Back To Top
                ========================================= */

                const backToTop =
                    document.getElementById(
                        'backToTop'
                    );


                if (backToTop) {


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

            }
        );
    </script>


    @stack('scripts')

</body>

</html>
