<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'KYRIX | ระบบจัดการร้านเช่าชุด')
    </title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Font Awesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --font-main: 'Prompt', 'Plus Jakarta Sans',
                -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;

            --primary: #7a1f2b;
            --primary-dark: #58141d;
            --primary-light: #9c2e3d;

            --primary-soft: #fbf0f2;

            --gold: #c69c4c;
            --gold-dark: #a97f45;
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

            /* Legacy owner page aliases */
            --font-prompt: var(--font-main);

            --maroon-950: var(--primary-dark);
            --maroon-900: var(--primary);
            --maroon-800: var(--primary);
            --maroon-700: var(--primary-light);
            --maroon-dark: var(--primary-dark);

            --blush-bg: var(--primary-soft);
            --blush-hover: #f6e4e8;

            --cream: var(--bg);
            --ink: var(--text-main);
            --muted: var(--text-muted);
            --line: var(--border);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body,
        button,
        input,
        select,
        textarea {
            font-family: var(--font-main);
        }

        .fa,
        .fas,
        .far,
        .fab,
        .fa-solid,
        .fa-regular,
        .fa::before,
        .fas::before,
        .far::before,
        .fab::before,
        .fa-solid::before,
        .fa-regular::before {
            font-family:
                "Font Awesome 6 Free",
                "Font Awesome 6 Brands" !important;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination SVG
        |--------------------------------------------------------------------------
        */

        nav svg,
        .pagination svg,
        .pagination-wrap svg,
        .payment-pagination svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
            max-width: 1.25rem !important;
            max-height: 1.25rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
            flex-shrink: 0 !important;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: all .2s ease;
        }

        /*
        |--------------------------------------------------------------------------
        | TOP BANNER
        |--------------------------------------------------------------------------
        */

        .admin-top-banner {
            background: #2a2421;
            color: #e5dfd8;
            font-size: 13px;
            padding: 8px 30px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 15px;
        }

        .admin-top-banner a {
            color: var(--gold);
            font-weight: 500;
        }

        .admin-top-banner a:hover {
            color: #ffffff;
        }

        /*
        |--------------------------------------------------------------------------
        | TOP NAVIGATION
        |--------------------------------------------------------------------------
        */

        .admin-nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);

            position: sticky;
            top: 0;
            z-index: 1000;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .admin-nav-container {
            max-width: 1440px;
            margin: 0 auto;

            padding: 14px 24px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            min-height: 74px;
            gap: 20px;
        }

        /*
        |--------------------------------------------------------------------------
        | BRAND
        |--------------------------------------------------------------------------
        */

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
            font-family:
                'Plus Jakarta Sans',
                sans-serif !important;

            font-size: 28px;
            font-weight: 800;
            letter-spacing: 2px;
            color: var(--primary);
        }

        .admin-brand .logo-subtext {
            font-family:
                var(--font-main) !important;

            font-size: 10px;
            font-weight: 600;
            letter-spacing: 3px;

            color: var(--gold);

            text-transform: uppercase;
            margin-top: 3px;
        }

        /*
        |--------------------------------------------------------------------------
        | MENU LINKS
        |--------------------------------------------------------------------------
        */

        .admin-menu {
            display: flex;
            align-items: center;

            gap: 22px;

            list-style: none;

            height: 100%;

            margin: 0;
            padding: 0;
        }

        .admin-menu-link {
            display: inline-flex;
            align-items: center;

            gap: 8px;

            min-height: 46px;

            font-size: 15px;
            font-weight: 500;

            color: var(--text-main);

            transition: all .2s ease;

            position: relative;

            padding: 0 2px;

            white-space: nowrap;
            text-decoration: none;
        }

        .admin-menu-link i {
            font-size: 14px;
            color: inherit;
        }

        .admin-menu-link:hover {
            color: var(--primary);
        }

        .admin-menu-link.active {
            color: var(--primary);
            font-weight: 600;
        }

        .admin-menu-link.active::after {
            content: '';

            position: absolute;

            bottom: 0;
            left: 0;

            width: 100%;
            height: 2.5px;

            background: var(--primary);

            border-radius: 2px 2px 0 0;
        }

        /*
        |--------------------------------------------------------------------------
        | RIGHT ACTIONS
        |--------------------------------------------------------------------------
        */

        .admin-nav-actions {
            display: flex;
            align-items: center;

            gap: 14px;

            flex-shrink: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        .admin-search-box {
            position: relative;

            display: flex;
            align-items: center;
        }

        .admin-search-input {
            background: var(--bg);

            border: 1px solid var(--border);

            border-radius: 999px;

            padding: 8px 38px 8px 18px;

            font-size: 13.5px;

            color: var(--text-main);

            width: 195px;

            outline: none;

            transition: all .25s ease;

            font-family:
                var(--font-main) !important;
        }

        .admin-search-input::placeholder {
            color: #9e8f92;
            font-size: 13px;
        }

        .admin-search-input:focus {
            border-color: var(--primary);

            background: #ffffff;

            box-shadow:
                0 0 0 3px rgba(122, 31, 43, 0.10);

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

        .admin-search-input:focus~.admin-search-btn,
        .admin-search-box:hover .admin-search-btn {
            color: var(--primary);
        }

        /*
        |--------------------------------------------------------------------------
        | NOTIFICATION
        |--------------------------------------------------------------------------
        */

        .notif-dropdown-wrapper {
            position: relative;
        }

        .notif-bell-btn {
            width: 42px;
            height: 42px;

            border-radius: 50%;

            background: var(--blush-bg);

            border:
                1px solid rgba(122, 31, 43, 0.06);

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--primary);

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
            color: var(--primary);
        }

        .notif-badge-count {
            position: absolute;

            top: -4px;
            right: -4px;

            min-width: 23px;

            background: #dc2626;

            color: #fff;

            font-size: 10px;
            font-weight: 700;

            padding: 2px 6px;

            border-radius: 99px;

            border: 2px solid #fff;

            box-shadow:
                0 2px 6px rgba(220, 38, 38, 0.30);

            text-align: center;
        }

        .notif-dropdown-menu {
            position: absolute;

            right: 0;
            top: 50px;

            width: 420px;

            max-width: calc(100vw - 24px);

            background: #fff;

            border:
                1px solid var(--line);

            border-radius: 14px;

            box-shadow:
                var(--shadow-lg);

            display: none;

            z-index: 1100;

            overflow: hidden;

            animation:
                fadeIn .2s ease;
        }

        .notif-dropdown-menu.show {
            display: block;
        }

        .notif-header {
            padding: 14px 16px;

            background: var(--bg);

            border-bottom:
                1px solid var(--border);

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            font-size: 13px;

            font-weight: 700;

            color: var(--primary);
        }

        .notif-body {
            max-height: 390px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 13px 16px;

            border-bottom:
                1px solid var(--line);

            display: flex;

            gap: 12px;

            align-items: flex-start;

            text-decoration: none;

            transition:
                background .2s,
                opacity .2s;
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
            width: 34px;
            height: 34px;

            border-radius: 9px;

            background: #fdf2f2;

            color: #dc2626;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            font-size: 13px;
        }

        /*
        | ลูกค้าแจ้งคืนชุด
        */

        .return-notif-icon {
            background: #f0fdf4 !important;
            color: #15803d !important;
        }

        .notif-item[data-notif-key^="return-"] {
            border-left:
                3px solid #16a34a;
        }

        /*
        | เกินกำหนด
        */

        .overdue-notif-icon {
            background: #fff1f2 !important;
            color: #dc2626 !important;
        }

        .notif-item[data-notif-key^="overdue-"] {
            border-left:
                3px solid #dc2626;
        }

        .notif-content {
            flex: 1;

            min-width: 0;

            font-size: 12px;

            color: var(--text-main);

            line-height: 1.5;
        }

        .notif-content strong {
            font-weight: 700;
        }

        .notif-time {
            font-size: 10.5px;

            color: var(--muted);

            margin-top: 4px;

            line-height: 1.5;
        }

        .notif-empty {
            padding: 30px;

            text-align: center;

            color: var(--muted);

            font-size: 12.5px;
        }

        /*
        |--------------------------------------------------------------------------
        | RIGHT BUTTONS
        |--------------------------------------------------------------------------
        */

        .btn-nav-outline {
            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 7px 18px;

            background: transparent;

            color: var(--primary);

            border:
                1px solid var(--primary);

            border-radius: 10px;

            font-size: 14px;
            font-weight: 500;

            cursor: default;

            transition: all .2s ease;

            text-decoration: none;

            font-family:
                var(--font-main) !important;

            line-height: 1.3;

            white-space: nowrap;
        }

        .btn-nav-outline:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .btn-nav-solid {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 7px;

            padding: 7px 20px;

            background: var(--primary);

            color: #ffffff;

            border:
                1px solid var(--primary);

            border-radius: 10px;

            font-size: 14px;
            font-weight: 500;

            cursor: pointer;

            transition: all .2s ease;

            text-decoration: none;

            font-family:
                var(--font-main) !important;

            line-height: 1.3;

            white-space: nowrap;
        }

        .btn-nav-solid:hover {
            background: var(--primary-dark);

            border-color: var(--primary-dark);

            color: #ffffff;

            transform:
                translateY(-1px);

            box-shadow:
                0 4px 12px rgba(122, 31, 43, 0.20);
        }

        /*
        |--------------------------------------------------------------------------
        | MAIN CONTENT
        |--------------------------------------------------------------------------
        */

        .admin-main {
            flex: 1;

            padding:
                34px 0 64px;
        }

        .admin-container {
            max-width: 1440px;

            margin: 0 auto;

            padding:
                0 24px;
        }

        /*
        |--------------------------------------------------------------------------
        | FLASH MESSAGES
        |--------------------------------------------------------------------------
        */

        .flash-container {
            margin-bottom: 24px;
        }

        .flash-alert {
            padding:
                14px 20px;

            border-radius:
                var(--radius-md);

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 14px;

            font-size: 13.5px;

            font-weight: 500;

            animation:
                fadeIn .3s ease;
        }

        .flash-alert.success {
            background: #eef7ee;

            border:
                1px solid #c8e6c9;

            color: #1e4620;
        }

        .flash-alert.error {
            background: #fdf0f2;

            border:
                1px solid #f8bbd0;

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

        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .admin-footer {
            border-top:
                3px solid var(--primary);

            padding:
                20px 0;

            background: #1f1b19;

            text-align: center;

            font-size: 12px;

            color: #807771;
        }

        .admin-footer p {
            margin: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | ANIMATION
        |--------------------------------------------------------------------------
        */

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform:
                    translateY(-5px);
            }

            to {
                opacity: 1;
                transform:
                    translateY(0);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

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

        @media (max-width: 768px) {

            .admin-top-banner {
                display: none;
            }

            .admin-nav-container {
                padding:
                    12px 18px !important;
            }

            .admin-search-box {
                display: none;
            }

            .notif-dropdown-menu {
                right: -8px;
                width:
                    min(420px, calc(100vw - 20px));
            }

            .btn-nav-outline span {
                display: none;
            }

            .btn-nav-outline {
                padding:
                    8px 10px;
            }

            .btn-nav-solid {
                padding:
                    8px 12px;
            }

        }

        @media (max-width: 520px) {

            .admin-nav-actions {
                gap: 8px;
            }

            .notif-dropdown-menu {
                position: fixed;

                top: 76px;
                left: 10px;
                right: 10px;

                width: auto;
                max-width: none;
            }

            .admin-brand .logo-text {
                font-size: 23px;
            }

            .admin-brand .logo-subtext {
                font-size: 8px;
                letter-spacing: 2px;
            }

        }
    </style>

    @stack('styles')

    <style>
        :root {
            --font-main: 'Prompt', 'Plus Jakarta Sans',
                -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;

            --primary: #7a1f2b;
            --primary-dark: #58141d;
            --primary-light: #9c2e3d;

            --primary-soft: #fbf0f2;

            --gold: #c69c4c;
            --gold-dark: #a97f45;
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

            --maroon-950: var(--primary-dark);
            --maroon-900: var(--primary);
            --maroon-800: var(--primary);
            --maroon-700: var(--primary-light);
            --maroon-dark: var(--primary-dark);

            --cream: var(--bg);
            --ink: var(--text-main);
            --muted: var(--text-muted);
            --line: var(--border);
        }

        body,
        body button,
        body input,
        body select,
        body textarea,
        .kyrix-admin-container,
        .admin-heading h1,
        .admin-heading p,
        .card-title,
        .status-badge,
        .admin-btn,
        .dashboard-wrapper,
        .owner-dashboard {
            font-family: var(--font-main) !important;
        }

        body {
            background: var(--bg) !important;
            color: var(--text-main) !important;
        }

        .admin-nav {
            background: var(--surface) !important;

            border-bottom:
                1px solid var(--border) !important;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.03) !important;
        }

        .admin-nav-container {
            max-width:
                1440px !important;

            min-height:
                74px !important;

            padding:
                14px 24px !important;
        }

        .logo-text {
            font-family:
                'Plus Jakarta Sans',
                sans-serif !important;

            color:
                var(--primary) !important;

            font-weight:
                800 !important;
        }

        .logo-subtext {
            color:
                var(--gold) !important;
        }

        .admin-menu-link {
            color:
                var(--text-main) !important;

            min-height:
                46px !important;
        }

        .admin-menu-link:hover,
        .admin-menu-link.active {
            color:
                var(--primary) !important;
        }

        .admin-menu-link.active::after {
            background:
                var(--primary) !important;
        }

        .admin-heading .eyebrow,
        .breadcrumb span,
        .section-eyebrow {
            color:
                var(--gold-dark) !important;
        }

        .admin-heading h1,
        .card-title,
        .detail-card .card-title,
        .content-card h1,
        .content-card h2,
        .content-card h3 {
            color:
                var(--primary-dark) !important;
        }

        .content-card,
        .detail-card,
        .form-card,
        .stat-card,
        .report-card,
        .dashboard-card,
        .table-card {
            background:
                var(--surface) !important;

            border-color:
                var(--border) !important;

            border-radius:
                var(--radius-md) !important;

            box-shadow:
                var(--shadow-sm) !important;
        }

        .content-card:hover,
        .detail-card:hover,
        .stat-card:hover,
        .dashboard-card:hover {
            box-shadow:
                var(--shadow-md) !important;
        }

        .kyrix-table th,
        .product-table th,
        .table thead th {
            background:
                var(--bg) !important;

            color:
                var(--text-muted) !important;

            border-bottom-color:
                var(--border) !important;
        }

        .kyrix-table td,
        .product-table td,
        .table td,
        .info-row {
            border-color:
                var(--border) !important;
        }

        .admin-btn.primary,
        .btn-primary,
        .btn-nav-solid,
        .btn-add,
        .btn-modal-submit,
        .dashboard-btn.primary {
            background:
                var(--primary) !important;

            border-color:
                var(--primary) !important;

            color:
                #fff !important;
        }

        .admin-btn.primary:hover,
        .btn-primary:hover,
        .btn-nav-solid:hover,
        .btn-add:hover,
        .btn-modal-submit:hover,
        .dashboard-btn.primary:hover {
            background:
                var(--primary-dark) !important;

            border-color:
                var(--primary-dark) !important;

            box-shadow:
                0 8px 20px rgba(122, 31, 43, 0.22) !important;
        }

        .admin-btn.secondary,
        .btn-secondary,
        .btn-outline,
        .btn-nav-outline,
        .btn-reset,
        .dashboard-btn.secondary {
            background:
                #fff !important;

            color:
                var(--primary) !important;

            border-color:
                var(--primary) !important;
        }

        .admin-btn.secondary:hover,
        .btn-secondary:hover,
        .btn-outline:hover,
        .btn-nav-outline:hover,
        .btn-reset:hover,
        .dashboard-btn.secondary:hover {
            background:
                var(--primary-soft) !important;

            color:
                var(--primary) !important;
        }

        .action-btn-view,
        .btn-return {
            background:
                var(--gold-light) !important;

            color:
                var(--gold-dark) !important;

            border-color:
                #f3e6d0 !important;
        }

        .action-btn-view:hover,
        .btn-return:hover {
            background:
                var(--gold) !important;

            color:
                #fff !important;
        }

        input:focus,
        select:focus,
        textarea:focus,
        .form-control:focus,
        .form-input-k:focus,
        .admin-search-input:focus {
            border-color:
                var(--primary) !important;

            box-shadow:
                0 0 0 3px rgba(122, 31, 43, 0.10) !important;
        }

        @media (max-width: 768px) {

            .admin-top-banner {
                display: none;
            }

            .admin-nav-container {
                padding:
                    12px 18px !important;
            }

        }
    </style>

</head>

<body>

    {{-- =========================================================
         TOP BANNER
    ========================================================== --}}

    <div class="admin-top-banner">

        <span>
            KYRIX Dress Rental Boutique — ระบบจัดการร้านเช่าชุดออนไลน์
        </span>

        <a href="{{ route('home') }}" target="_blank" rel="noopener">
            <i class="fa-solid fa-store"></i>
            ดูหน้าเว็บไซต์ลูกค้า
        </a>

    </div>


    {{-- =========================================================
         TOP NAVIGATION
    ========================================================== --}}

    <header class="admin-nav">

        <div class="admin-nav-container">

            {{-- BRAND --}}

            <div class="admin-brand">

                <a href="{{ route('owner.dashboard') }}" class="logo-container">

                    <span class="logo-text">
                        KYRIX
                    </span>

                    <span class="logo-subtext">
                        DRESS RENTAL
                    </span>

                </a>

            </div>


            {{-- =================================================
                 MAIN MENU
            ================================================== --}}

            <ul class="admin-menu">

                <li>

                    <a href="{{ route('owner.dashboard') }}"
                        class="admin-menu-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">

                        <i class="fa-solid fa-chart-pie"></i>

                        <span>
                            ภาพรวม
                        </span>

                    </a>

                </li>


                <li>

                    <a href="{{ route('owner.dresses.index') }}"
                        class="admin-menu-link {{ request()->routeIs('owner.dresses.*') ? 'active' : '' }}">

                        <i class="fa-solid fa-shirt"></i>

                        <span>
                            จัดการชุด
                        </span>

                    </a>

                </li>


                <li>

                    <a href="{{ route('owner.bookings.index') }}"
                        class="admin-menu-link {{ request()->routeIs('owner.bookings.*') ? 'active' : '' }}">

                        <i class="fa-regular fa-calendar-check"></i>

                        <span>
                            รายการเช่า
                        </span>

                    </a>

                </li>


                <li>

                    <a href="{{ route('owner.payments.index') }}"
                        class="admin-menu-link {{ request()->routeIs('owner.payments.*') ? 'active' : '' }}">

                        <i class="fa-solid fa-receipt"></i>

                        <span>
                            ตรวจสลิป
                        </span>

                    </a>

                </li>


                <li>

                    <a href="{{ route('owner.returns.index') }}"
                        class="admin-menu-link {{ request()->routeIs('owner.returns.*') ? 'active' : '' }}">

                        <i class="fa-solid fa-rotate-left"></i>

                        <span>
                            รับคืนชุด
                        </span>

                    </a>

                </li>


                <li>

                    <a href="{{ route('owner.customers.index') }}"
                        class="admin-menu-link {{ request()->routeIs('owner.customers.*') ? 'active' : '' }}">

                        <i class="fa-solid fa-users"></i>

                        <span>
                            ลูกค้า
                        </span>

                    </a>

                </li>


                <li>

                    <a href="{{ route('owner.reports.index') }}"
                        class="admin-menu-link {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">

                        <i class="fa-solid fa-chart-column"></i>

                        <span>
                            รายงาน
                        </span>

                    </a>

                </li>

            </ul>


            {{-- =================================================
                 RIGHT ACTIONS
            ================================================== --}}

            <div class="admin-nav-actions">


                {{-- SEARCH --}}

                @php

                    $isCustomerPage = request()->routeIs('owner.customers.*');

                    $searchAction = $isCustomerPage ? route('owner.customers.index') : route('owner.dresses.index');

                    $searchPlaceholder = $isCustomerPage ? 'ค้นหาลูกค้า...' : 'ค้นหาชุดสวย...';

                @endphp


                <form action="{{ $searchAction }}" method="GET" class="admin-search-box">

                    <input type="text" name="search" class="admin-search-input"
                        placeholder="{{ $searchPlaceholder }}" value="{{ request('search') }}">

                    <button type="submit" class="admin-search-btn" title="ค้นหา">

                        <i class="fa-solid fa-magnifying-glass"></i>

                    </button>

                </form>


                {{-- =================================================
                     NOTIFICATION BELL
                     - ลูกค้าแจ้งคืน
                     - เกินกำหนดคืน
                ================================================== --}}

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | ลูกค้าแจ้งคืนชุดแล้ว
                    |--------------------------------------------------------------------------
                    |
                    | ใช้ return_status เป็นตัวหลัก
                    | เพื่อรองรับกรณี status อาจเป็น renting
                    | แต่ลูกค้าแจ้งคืนแล้ว
                    |
                    */

                    $pendingReturnRentals = \App\Models\Rental::with('customer')
                        ->where('return_status', 'returned_requested')
                        ->whereNotIn('status', ['returned', 'completed', 'cancelled'])
                        ->orderByDesc('return_requested_at')
                        ->get();

                    /*
                    |--------------------------------------------------------------------------
                    | รายการเกินกำหนดคืน
                    |--------------------------------------------------------------------------
                    |
                    | ไม่รวม pending_return
                    | เพื่อไม่ให้รายการเดียวกันขึ้นซ้ำ
                    |
                    */

                    $today = \Carbon\Carbon::today();

                    $overdueRentals = \App\Models\Rental::with('customer')
                        ->whereDate('end_date', '<', $today)
                        ->whereNotIn('status', ['returned', 'completed', 'cancelled', 'pending_return'])
                        ->orderBy('end_date', 'asc')
                        ->get();

                    /*
                    |--------------------------------------------------------------------------
                    | รวมรายการแจ้งเตือน
                    |--------------------------------------------------------------------------
                    */

                    $notificationItems = collect();

                    /*
                    |--------------------------------------------------------------------------
                    | Notification: ลูกค้าแจ้งคืน
                    |--------------------------------------------------------------------------
                    */

                    foreach ($pendingReturnRentals as $item) {
                        $rId = $item->rental_id ?? $item->id;

                        $custName = 'ลูกค้า';

                        if ($item->customer) {
                            $custName =
                                $item->customer->full_name ??
                                ($item->customer->name ??
                                    trim(
                                        ($item->customer->first_name ?? '') . ' ' . ($item->customer->last_name ?? ''),
                                    ));

                            if (trim($custName) === '') {
                                $custName = 'ลูกค้า';
                            }
                        }

                        $returnMethod = match ($item->return_method ?? null) {
                            'store' => 'คืนที่ร้าน',

                            'parcel' => 'ส่งพัสดุ',

                            default => 'รอตรวจสอบวิธีคืน',
                        };

                        $notificationItems->push([
                            'key' => 'return-' . $rId,

                            'type' => 'return',

                            'rental_id' => $rId,

                            'url' => route('owner.bookings.show', $rId),

                            'icon' => 'fa-rotate-left',

                            'icon_class' => 'return-notif-icon',

                            'title' => 'ลูกค้าแจ้งคืนชุดแล้ว',

                            'message' => '#' . ($item->rental_code ?? 'KR-' . $rId) . ' คุณ ' . $custName,

                            'detail' => 'วิธีคืน: ' . $returnMethod,

                            'time' => $item->return_requested_at
                                ? \Carbon\Carbon::parse($item->return_requested_at)->format('d/m/Y H:i') . ' น.'
                                : '-',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Notification: เกินกำหนด
                    |--------------------------------------------------------------------------
                    */

                    foreach ($overdueRentals as $item) {
                        $rId = $item->rental_id ?? $item->id;

                        $custName = 'ลูกค้า';

                        if ($item->customer) {
                            $custName =
                                $item->customer->full_name ??
                                ($item->customer->name ??
                                    trim(
                                        ($item->customer->first_name ?? '') . ' ' . ($item->customer->last_name ?? ''),
                                    ));

                            if (trim($custName) === '') {
                                $custName = 'ลูกค้า';
                            }
                        }

                        $daysLate = \Carbon\Carbon::parse($item->end_date)->diffInDays($today);

                        $notificationItems->push([
                            'key' => 'overdue-' . $rId,

                            'type' => 'overdue',

                            'rental_id' => $rId,

                            'url' => route('owner.returns.index', [
                                'tab' => 'overdue',
                            ]),

                            'icon' => 'fa-triangle-exclamation',

                            'icon_class' => 'overdue-notif-icon',

                            'title' => 'เกินกำหนดคืนชุด',

                            'message' => '#' . ($item->rental_code ?? 'KR-' . $rId) . ' คุณ ' . $custName,

                            'detail' => 'เลยกำหนดคืนแล้ว ' . $daysLate . ' วัน',

                            'time' => 'กำหนดคืน: ' . \Carbon\Carbon::parse($item->end_date)->format('d/m/Y'),
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | จำนวนแจ้งเตือนทั้งหมด
                    |--------------------------------------------------------------------------
                    */

                    $notificationCount = $notificationItems->count();
                @endphp


                <div class="notif-dropdown-wrapper" id="notifDropdownWrapper">

                    <button type="button" class="notif-bell-btn" id="notifBellBtn" onclick="toggleNotifDropdown(event)"
                        title="แจ้งเตือน">

                        <i class="fa-regular fa-bell"></i>


                        @if ($notificationCount > 0)
                            <span class="notif-badge-count" id="notifBadgeCount">
                                {{ $notificationCount }}
                            </span>
                        @else
                            <span class="notif-badge-count" id="notifBadgeCount" style="display:none;">
                                0
                            </span>
                        @endif

                    </button>


                    <div class="notif-dropdown-menu" id="notifDropdownMenu">

                        <div class="notif-header">

                            <span>
                                การแจ้งเตือน
                            </span>

                            <span id="notifHeaderCount"
                                style="
                                    font-size:11px;
                                    color:var(--muted);
                                    font-weight:normal;
                                ">
                                ({{ $notificationCount }} รายการ)
                            </span>

                        </div>


                        <div class="notif-body">

                            @forelse ($notificationItems
                                as $item)
                                <a href="{{ $item['url'] }}" class="notif-item" data-notif-key="{{ $item['key'] }}"
                                    data-rental-id="{{ $item['rental_id'] }}"
                                    onclick="markNotifRead(
                                        '{{ $item['key'] }}'
                                    )">

                                    <div
                                        class="
                                            notif-icon-box
                                            {{ $item['icon_class'] }}
                                        ">

                                        <i
                                            class="
                                                fa-solid
                                                {{ $item['icon'] }}
                                            "></i>

                                    </div>


                                    <div class="notif-content">

                                        <div>

                                            <strong>
                                                {{ $item['title'] }}
                                            </strong>

                                            <br>

                                            <span>
                                                {{ $item['message'] }}
                                            </span>

                                        </div>


                                        <div class="notif-time">

                                            {{ $item['detail'] }}

                                            <br>

                                            {{ $item['time'] }}


                                            <span class="notif-read-tag" style="display:none;">

                                                <i class="fa-solid fa-check"></i>

                                                อ่านแล้ว

                                            </span>

                                        </div>

                                    </div>

                                </a>

                            @empty

                                <div class="notif-empty">

                                    <i class="fa-regular fa-bell-slash"
                                        style="
                                            font-size:24px;
                                            margin-bottom:6px;
                                            opacity:.5;
                                        "></i>

                                    <div>
                                        ไม่มีรายการแจ้งเตือน
                                    </div>

                                </div>
                            @endforelse

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     OWNER PROFILE
                ================================================== --}}

                <div class="btn-nav-outline" title="บัญชีผู้ดูแลร้าน KYRIX">

                    <i class="fa-regular fa-user" style="font-size:13px;"></i>

                    <span>
                        {{ auth()->user()->name ?? 'เจ้าของร้าน' }}
                    </span>

                </div>


                {{-- =================================================
                     LOGOUT
                ================================================== --}}

                <form action="{{ route('logout') }}" method="POST"
                    style="
                        display:inline;
                        margin:0;
                    ">

                    @csrf

                    <button type="submit" class="btn-nav-solid" title="ออกจากระบบ"
                        onclick="
                            return confirm(
                                'ต้องการออกจากระบบหรือไม่?'
                            )
                        ">

                        <span>
                            ออกจากระบบ
                        </span>

                    </button>

                </form>

            </div>

        </div>

    </header>


    {{-- =========================================================
         CONTENT
    ========================================================== --}}

    <main class="admin-main">

        <div class="admin-container">


            {{-- =================================================
                 SUCCESS
            ================================================== --}}

            @if (session('success'))
                <div class="flash-container">

                    <div class="flash-alert success">

                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:10px;
                            ">

                            <i class="fa-solid fa-circle-check"></i>

                            <span>
                                {{ session('success') }}
                            </span>

                        </div>


                        <button type="button" class="flash-close"
                            onclick="
                                this.parentElement.remove()
                            ">
                            &times;
                        </button>

                    </div>

                </div>
            @endif


            {{-- =================================================
                 ERROR
            ================================================== --}}

            @if (session('error'))
                <div class="flash-container">

                    <div class="flash-alert error">

                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:10px;
                            ">

                            <i
                                class="
                                    fa-solid
                                    fa-circle-exclamation
                                "></i>

                            <span>
                                {{ session('error') }}
                            </span>

                        </div>


                        <button type="button" class="flash-close"
                            onclick="
                                this.parentElement.remove()
                            ">
                            &times;
                        </button>

                    </div>

                </div>
            @endif


            {{-- =================================================
                 VALIDATION ERRORS
            ================================================== --}}

            @if ($errors->any())

                <div class="flash-container">

                    <div class="flash-alert error">

                        <div>

                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:10px;
                                    margin-bottom:6px;
                                    font-weight:700;
                                ">

                                <i
                                    class="
                                        fa-solid
                                        fa-circle-xmark
                                    "></i>

                                <span>
                                    เกิดข้อผิดพลาด:
                                </span>

                            </div>


                            <ul
                                style="
                                    margin-left:26px;
                                    font-size:12.5px;
                                ">

                                @foreach ($errors->all() as $err)
                                    <li>
                                        {{ $err }}
                                    </li>
                                @endforeach

                            </ul>

                        </div>


                        <button type="button" class="flash-close"
                            onclick="
                                this.parentElement.remove()
                            ">
                            &times;
                        </button>

                    </div>

                </div>

            @endif


            {{-- =================================================
                 PAGE CONTENT
            ================================================== --}}

            @yield('content')

        </div>

    </main>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <footer class="admin-footer">

        <div class="admin-container">

            <p>
                © {{ now()->year }}
                KYRIX Dress Rental —
                Luxury Evening & Thai Dress Rental Management System
            </p>

        </div>

    </footer>


    {{-- =========================================================
         NOTIFICATION SCRIPT
         - อ่านแล้วเก็บ 12 ชั่วโมง
         - ไม่อ่านแล้วขึ้นจำนวนบนกระดิ่ง
    ========================================================== --}}

    <script>
        /*
            |--------------------------------------------------------------------------
            | เปิด / ปิด Dropdown
            |--------------------------------------------------------------------------
            */

        function toggleNotifDropdown(event) {

            event.stopPropagation();

            const menu =
                document.getElementById(
                    'notifDropdownMenu'
                );

            if (!menu) {
                return;
            }

            menu.classList.toggle(
                'show'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | คลิกนอก Dropdown เพื่อปิด
        |--------------------------------------------------------------------------
        */

        window.addEventListener(
            'click',
            function() {

                const menu =
                    document.getElementById(
                        'notifDropdownMenu'
                    );

                if (
                    menu &&
                    menu.classList.contains(
                        'show'
                    )
                ) {

                    menu.classList.remove(
                        'show'
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ป้องกัน click ใน dropdown แล้วปิดก่อน
        |--------------------------------------------------------------------------
        */

        const notifMenu =
            document.getElementById(
                'notifDropdownMenu'
            );

        if (notifMenu) {

            notifMenu.addEventListener(
                'click',
                function(event) {
                    event.stopPropagation();
                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | อ่าน Notification จาก LocalStorage
        |--------------------------------------------------------------------------
        */

        function getReadNotifs() {

            try {

                return JSON.parse(
                    localStorage.getItem(
                        'kyrix_read_notifs'
                    ) || '{}'
                );

            } catch (error) {

                return {};

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Refresh UI
        |--------------------------------------------------------------------------
        */

        function refreshNotifUI() {

            const readNotifs =
                getReadNotifs();


            const items =
                document.querySelectorAll(
                    '.notif-item[data-notif-key]'
                );


            let unreadCount = 0;


            items.forEach(
                function(item) {

                    const key =
                        item.getAttribute(
                            'data-notif-key'
                        );


                    const isRead =
                        Object.prototype.hasOwnProperty.call(
                            readNotifs,
                            key
                        );


                    const readTag =
                        item.querySelector(
                            '.notif-read-tag'
                        );


                    item.classList.toggle(
                        'is-read',
                        isRead
                    );


                    if (readTag) {

                        readTag.style.display =
                            isRead ?
                            'inline-flex' :
                            'none';

                    }


                    if (!isRead) {

                        unreadCount++;

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Badge
            |--------------------------------------------------------------------------
            */

            const badge =
                document.getElementById(
                    'notifBadgeCount'
                );


            if (badge) {

                if (
                    unreadCount > 0
                ) {

                    badge.textContent =
                        unreadCount;

                    badge.style.display =
                        '';

                } else {

                    badge.textContent =
                        '0';

                    badge.style.display =
                        'none';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Header Count
            |--------------------------------------------------------------------------
            */

            const headerCount =
                document.getElementById(
                    'notifHeaderCount'
                );


            if (headerCount) {

                headerCount.textContent =
                    '(' +
                    unreadCount +
                    ' รายการใหม่)';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Mark Notification Read
        |--------------------------------------------------------------------------
        */

        function markNotifRead(
            notifKey
        ) {

            const readNotifs =
                getReadNotifs();


            readNotifs[notifKey] =
                new Date().getTime();


            localStorage.setItem(
                'kyrix_read_notifs',
                JSON.stringify(
                    readNotifs
                )
            );


            /*
            | อัปเดต UI ก่อนเปลี่ยนหน้า
            */

            refreshNotifUI();

        }


        /*
        |--------------------------------------------------------------------------
        | DOM Loaded
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                let readNotifs =
                    getReadNotifs();


                const now =
                    new Date().getTime();


                /*
                | 12 ชั่วโมง
                */

                const twelveHours =
                    12 *
                    60 *
                    60 *
                    1000;


                let updated =
                    false;


                /*
                |--------------------------------------------------------------------------
                | ลบ Notification ที่อ่านแล้วเกิน 12 ชั่วโมง
                |--------------------------------------------------------------------------
                */

                Object.keys(
                    readNotifs
                ).forEach(
                    function(key) {

                        const readAt =
                            Number(
                                readNotifs[key]
                            );


                        if (
                            !readAt ||
                            now - readAt >
                            twelveHours
                        ) {

                            delete readNotifs[key];

                            updated =
                                true;

                        }

                    }
                );


                if (updated) {

                    localStorage.setItem(
                        'kyrix_read_notifs',
                        JSON.stringify(
                            readNotifs
                        )
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | แสดงสถานะปัจจุบัน
                |--------------------------------------------------------------------------
                */

                refreshNotifUI();

            }
        );
    </script>


    @stack('scripts')

</body>

</html>
