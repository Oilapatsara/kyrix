@extends('layouts.owner')

@section('title', 'คลังชุด | KYRIX Rental')

@push('styles')
<style>
    :root {
        --ink: #23191b;
        --heading: #3f1620;
        --primary: #6c2031;
        --primary-dark: #541522;

        --bg: #f8f6f4;
        --surface: #ffffff;
        --surface-soft: #fcfaf9;

        --border: #e9e2df;
        --muted: #8c8082;

        --green-bg: #edf7ef;
        --green: #4f7c57;

        --orange-bg: #fff4e8;
        --orange: #a86216;

        --red-bg: #fdf0f1;
        --red: #9a273a;

        --pink-bg: #f8e9ec;
        --pink: #7c2940;

        --shadow: 0 8px 30px rgba(47, 24, 29, .05);
    }

    .kyrix-page {
        color: var(--ink);
    }

    /* -------------------------------------------------------
       HEADER
    ------------------------------------------------------- */

    .page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .page-eyebrow {
        display: block;
        margin-bottom: 7px;
        color: #a97855;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.8px;
    }

    .page-title {
        margin: 0;
        color: var(--heading);
        font-size: 29px;
        line-height: 1.15;
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .page-description {
        margin: 7px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .add-dress-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 17px;
        border-radius: 10px;
        background: var(--primary);
        color: #fff;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 750;
        transition: .18s ease;
        box-shadow: 0 5px 15px rgba(108, 32, 49, .15);
    }

    .add-dress-btn:hover {
        background: var(--primary-dark);
        color: #fff;
        transform: translateY(-1px);
    }

    /* -------------------------------------------------------
       FLASH
    ------------------------------------------------------- */

    .flash {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 18px;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .flash-success {
        background: var(--green-bg);
        color: var(--green);
        border: 1px solid #d8eadb;
    }

    .flash-error {
        background: var(--red-bg);
        color: var(--red);
        border: 1px solid #f2ced4;
    }

    /* -------------------------------------------------------
       STAT CARDS
    ------------------------------------------------------- */

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 13px;
        margin-bottom: 18px;
    }

    .stat-card {
        position: relative;
        display: block;
        min-height: 126px;
        padding: 17px 18px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        color: inherit;
        text-decoration: none;
        overflow: hidden;
        transition: .18s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        border-color: #dcc9cd;
        box-shadow: var(--shadow);
    }

    .stat-card.active {
        border-color: #8e5260;
        box-shadow: 0 0 0 2px rgba(108, 32, 49, .06);
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--surface-soft);
        border: 1px solid var(--border);
        color: var(--primary);
        font-size: 14px;
    }

    .stat-link {
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
    }

    .stat-label {
        margin-bottom: 3px;
        color: var(--muted);
        font-size: 11px;
    }

    .stat-number {
        color: var(--heading);
        font-size: 27px;
        line-height: 1;
        font-weight: 800;
    }

    .stat-arrow {
        position: absolute;
        right: 18px;
        bottom: 16px;
        color: #b8aaad;
        font-size: 11px;
    }

    /* -------------------------------------------------------
       WORKSPACE
    ------------------------------------------------------- */

    .workspace {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .workspace-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 17px 19px;
        border-bottom: 1px solid var(--border);
    }

    .workspace-title {
        margin: 0;
        color: var(--heading);
        font-size: 14px;
        font-weight: 800;
    }

    .workspace-subtitle {
        margin-top: 3px;
        color: var(--muted);
        font-size: 11px;
    }

    .result-count {
        padding: 6px 9px;
        border-radius: 8px;
        background: var(--surface-soft);
        border: 1px solid var(--border);
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* -------------------------------------------------------
       CATEGORY FILTER
    ------------------------------------------------------- */

    .category-section {
        padding: 15px 19px 16px;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .category-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .category-label {
        color: var(--heading);
        font-size: 11px;
        font-weight: 800;
    }

    .category-reset {
        color: var(--muted);
        font-size: 10px;
        text-decoration: none;
        font-weight: 700;
    }

    .category-reset:hover {
        color: var(--primary);
    }

    .category-list {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .category-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 11px;
        border-radius: 9px;
        border: 1px solid var(--border);
        background: #fff;
        color: #5f4e52;
        text-decoration: none;
        font-size: 10.5px;
        font-weight: 700;
        transition: .16s ease;
    }

    .category-item:hover {
        border-color: #cdaeb5;
        color: var(--primary);
    }

    .category-item.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .category-count {
        opacity: .72;
        font-size: 9px;
    }

    /* -------------------------------------------------------
       TOOLBAR
    ------------------------------------------------------- */

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 12px 19px;
        background: var(--surface-soft);
        border-bottom: 1px solid var(--border);
    }

    .current-filter {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .current-filter-label {
        color: var(--muted);
        font-size: 10.5px;
    }

    .current-filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border-radius: 7px;
        background: #fff;
        border: 1px solid var(--border);
        color: var(--heading);
        font-size: 10px;
        font-weight: 700;
    }

    .current-filter-badge i {
        color: var(--primary);
        font-size: 9px;
    }

    .sort-form {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .sort-form label {
        color: var(--muted);
        font-size: 10px;
        white-space: nowrap;
    }

    .sort-select {
        height: 34px;
        min-width: 150px;
        padding: 0 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #fff;
        color: var(--ink);
        font-size: 10.5px;
        outline: none;
    }

    /* -------------------------------------------------------
       TABLE
    ------------------------------------------------------- */

    .table-wrap {
        overflow-x: auto;
    }

    .dress-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: collapse;
    }

    .dress-table th {
        padding: 12px 18px;
        background: #fcfaf9;
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        font-size: 9.5px;
        font-weight: 800;
        text-align: left;
        letter-spacing: .5px;
        white-space: nowrap;
    }

    .dress-table td {
        padding: 13px 18px;
        border-bottom: 1px solid #f0ebea;
        vertical-align: middle;
        font-size: 12px;
    }

    .dress-table tbody tr {
        transition: .14s ease;
    }

    .dress-table tbody tr:hover {
        background: #fdfbfa;
    }

    .dress-code {
        color: var(--heading);
        font-size: 11px;
        font-weight: 800;
    }

    .dress-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dress-image,
    .dress-image-empty {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 9px;
        border: 1px solid var(--border);
        object-fit: cover;
        background: #f8f5f3;
    }

    .dress-image-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ac9a9d;
        font-size: 17px;
    }

    .dress-name {
        color: #2d2022;
        font-size: 12.5px;
        font-weight: 750;
        line-height: 1.4;
    }

    .dress-meta {
        margin-top: 3px;
        color: var(--muted);
        font-size: 9.5px;
    }

    .category-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 28px;
        padding: 0 8px;
        border-radius: 7px;
        background: var(--pink-bg);
        color: var(--pink);
        font-size: 9.5px;
        font-weight: 750;
    }

    .category-empty {
        color: #b3a6a9;
        font-size: 10px;
    }

    .price {
        color: var(--heading);
        font-size: 12px;
        font-weight: 800;
    }

    .status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 27px;
        padding: 0 9px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 50%;
    }

    .status.available {
        background: var(--green-bg);
        color: var(--green);
    }

    .status.available::before {
        background: var(--green);
    }

    .status.rented {
        background: var(--orange-bg);
        color: var(--orange);
    }

    .status.rented::before {
        background: var(--orange);
    }

    .status.inactive {
        background: var(--red-bg);
        color: var(--red);
    }

    .status.inactive::before {
        background: var(--red);
    }

    .action-group {
        display: flex;
        justify-content: center;
        gap: 6px;
    }

    .edit-btn,
    .delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 30px;
        padding: 0 9px;
        border-radius: 7px;
        font-size: 9.5px;
        font-weight: 750;
        transition: .15s ease;
        cursor: pointer;
    }

    .edit-btn {
        background: #fffaf1;
        color: #9b6d2f;
        border: 1px solid #efdfc8;
        text-decoration: none;
    }

    .edit-btn:hover {
        background: #f8eedf;
    }

    .delete-btn {
        background: #fff7f8;
        color: var(--red);
        border: 1px solid #f2d5d9;
    }

    .delete-btn:hover {
        background: #fdebed;
    }

    /* -------------------------------------------------------
       EMPTY
    ------------------------------------------------------- */

    .empty-box {
        padding: 65px 20px;
        text-align: center;
    }

    .empty-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 13px;
        border-radius: 16px;
        background: var(--surface-soft);
        border: 1px solid var(--border);
        color: #aa979b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .empty-title {
        color: var(--heading);
        font-size: 13px;
        font-weight: 800;
    }

    .empty-text {
        margin-top: 5px;
        color: var(--muted);
        font-size: 11px;
    }

    /* -------------------------------------------------------
       PAGINATION
    ------------------------------------------------------- */

    .pagination-wrap {
        padding: 13px 18px;
        background: var(--surface-soft);
        border-top: 1px solid var(--border);
    }

    /* -------------------------------------------------------
       RESPONSIVE
    ------------------------------------------------------- */

    @media (max-width: 1100px) {
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .page-head {
            align-items: flex-start;
        }
    }

    @media (max-width: 700px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }

        .page-head {
            flex-direction: column;
        }

        .add-dress-btn {
            width: 100%;
        }

        .toolbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .sort-form {
            width: 100%;
        }

        .sort-select {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="kyrix-page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="page-head">

        <div>
            <span class="page-eyebrow">
                KYRIX RENTAL · INVENTORY
            </span>

            <h1 class="page-title">
                คลังชุด
            </h1>

            <p class="page-description">
                จัดการชุดเช่าทั้งหมดของร้าน ตรวจสอบหมวดหมู่ ราคา และสถานะการใช้งาน
            </p>
        </div>

        <a
            href="{{ route('owner.dresses.create') }}"
            class="add-dress-btn"
        >
            <i class="fa-solid fa-plus"></i>
            เพิ่มชุด
        </a>

    </div>


    {{-- =====================================================
         FLASH MESSAGE
    ====================================================== --}}
    @if(session('success'))
        <div class="flash flash-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flash flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif


    {{-- =====================================================
         CLICKABLE STAT CARDS
    ====================================================== --}}
    <div class="stat-grid">

        {{-- ALL --}}
        <a
            href="{{ route('owner.dresses.index', request()->except(['status','category_id','page'])) }}"
            class="stat-card {{ !request('status') && !request('category_id') ? 'active' : '' }}"
        >

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fa-solid fa-shirt"></i>
                </div>

                <div class="stat-link">
                    ดูทั้งหมด
                </div>
            </div>

            <div class="stat-label">
                ชุดทั้งหมด
            </div>

            <div class="stat-number">
                {{ number_format($summary['total'] ?? $products->total()) }}
            </div>

            <i class="fa-solid fa-arrow-right stat-arrow"></i>

        </a>


        {{-- AVAILABLE --}}
        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'active']
            )) }}"
            class="stat-card {{ request('status') === 'active' ? 'active' : '' }}"
        >

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div class="stat-link">
                    เปิดใช้งาน
                </div>
            </div>

            <div class="stat-label">
                พร้อมให้เช่า
            </div>

            <div class="stat-number">
                {{ number_format($summary['available'] ?? 0) }}
            </div>

            <i class="fa-solid fa-arrow-right stat-arrow"></i>

        </a>


        {{-- RENTED --}}
        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'rented']
            )) }}"
            class="stat-card {{ request('status') === 'rented' ? 'active' : '' }}"
        >

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fa-solid fa-person-dress"></i>
                </div>

                <div class="stat-link">
                    กำลังใช้งาน
                </div>
            </div>

            <div class="stat-label">
                กำลังเช่า
            </div>

            <div class="stat-number">
                {{ number_format($summary['rented'] ?? 0) }}
            </div>

            <i class="fa-solid fa-arrow-right stat-arrow"></i>

        </a>


        {{-- INACTIVE --}}
        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'inactive']
            )) }}"
            class="stat-card {{ request('status') === 'inactive' ? 'active' : '' }}"
        >

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fa-solid fa-eye-slash"></i>
                </div>

                <div class="stat-link">
                    ดูรายการ
                </div>
            </div>

            <div class="stat-label">
                ปิดใช้งาน
            </div>

            <div class="stat-number">
                {{ number_format($summary['inactive'] ?? 0) }}
            </div>

            <i class="fa-solid fa-arrow-right stat-arrow"></i>

        </a>

    </div>


    {{-- =====================================================
         WORKSPACE
    ====================================================== --}}
    <div class="workspace">

        {{-- WORKSPACE HEADER --}}
        <div class="workspace-head">

            <div>
                <h2 class="workspace-title">
                    รายการชุด
                </h2>

                <div class="workspace-subtitle">
                    เลือกหมวดหมู่หรือสถานะเพื่อดูรายการที่ต้องการ
                </div>
            </div>

            <div class="result-count">
                {{ number_format($products->total()) }} รายการ
            </div>

        </div>


        {{-- =================================================
             CATEGORY FILTER
        ================================================== --}}
        <div class="category-section">

            <div class="category-header">

                <div class="category-label">
                    หมวดหมู่ชุด
                </div>

                @if(request('category_id'))

                    <a
                        href="{{ route(
                            'owner.dresses.index',
                            request()->except(['category_id','page'])
                        ) }}"
                        class="category-reset"
                    >
                        ล้างหมวดหมู่
                    </a>

                @endif

            </div>


            <div class="category-list">

                {{-- ALL --}}
                <a
                    href="{{ route(
                        'owner.dresses.index',
                        request()->except(['category_id','page'])
                    ) }}"
                    class="
                        category-item
                        {{ !request('category_id') ? 'active' : '' }}
                    "
                >
                    ทั้งหมด

                    <span class="category-count">
                        {{ $summary['total'] ?? $products->total() }}
                    </span>
                </a>


                {{-- CATEGORIES --}}
                @foreach($categories as $category)

                    @php
                        // ตาราง `categories` ในฐานข้อมูลใช้คอลัมน์ category_id / category_name
                        // (ไม่ใช่ id / name) — ใส่ fallback ไว้เผื่อ Model มี accessor อื่น
                        $categoryId = $category->category_id ?? $category->id;
                        $categoryName = $category->category_name ?? $category->name;
                    @endphp

                    <a
                        href="{{ route(
                            'owner.dresses.index',
                            array_merge(
                                request()->except('page'),
                                ['category_id' => $categoryId]
                            )
                        ) }}"
                        class="
                            category-item
                            {{ (string) request('category_id') === (string) $categoryId ? 'active' : '' }}
                        "
                    >

                        {{ $categoryName }}

                        @if(isset($category->products_count))
                            <span class="category-count">
                                {{ $category->products_count }}
                            </span>
                        @endif

                    </a>

                @endforeach

            </div>

        </div>


        {{-- =================================================
             TOOLBAR
        ================================================== --}}
        <div class="toolbar">

            <div class="current-filter">

                <span class="current-filter-label">
                    กำลังแสดง:
                </span>


                @if(request('category_id'))

                    @php
                        $selectedCategory = $categories->first(function ($cat) {
                            $catId = $cat->category_id ?? $cat->id;
                            return (string) $catId === (string) request('category_id');
                        });
                    @endphp

                    @if($selectedCategory)

                        <span class="current-filter-badge">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ $selectedCategory->category_name ?? $selectedCategory->name }}
                        </span>

                    @endif

                @endif


                @if(request('status'))

                    <span class="current-filter-badge">
                        <i class="fa-solid fa-circle"></i>

                        @switch(request('status'))

                            @case('active')
                                พร้อมให้เช่า
                                @break

                            @case('rented')
                                กำลังเช่า
                                @break

                            @case('inactive')
                                ปิดใช้งาน
                                @break

                        @endswitch

                    </span>

                @endif


                @if(!request('category_id') && !request('status'))

                    <span class="current-filter-badge">
                        <i class="fa-solid fa-list"></i>
                        ชุดทั้งหมด
                    </span>

                @endif

            </div>


            {{-- SORT --}}
            <form
                action="{{ route('owner.dresses.index') }}"
                method="GET"
                class="sort-form"
            >

                @if(request('category_id'))
                    <input
                        type="hidden"
                        name="category_id"
                        value="{{ request('category_id') }}"
                    >
                @endif

                @if(request('status'))
                    <input
                        type="hidden"
                        name="status"
                        value="{{ request('status') }}"
                    >
                @endif

                <label for="sort">
                    เรียง:
                </label>

                <select
                    name="sort"
                    id="sort"
                    class="sort-select"
                    onchange="this.form.submit()"
                >

                    <option
                        value="latest"
                        {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}
                    >
                        ล่าสุด
                    </option>

                    <option
                        value="name_asc"
                        {{ request('sort') === 'name_asc' ? 'selected' : '' }}
                    >
                        ชื่อ A-Z
                    </option>

                    <option
                        value="price_low"
                        {{ request('sort') === 'price_low' ? 'selected' : '' }}
                    >
                        ราคา ต่ำ → สูง
                    </option>

                    <option
                        value="price_high"
                        {{ request('sort') === 'price_high' ? 'selected' : '' }}
                    >
                        ราคา สูง → ต่ำ
                    </option>

                </select>

            </form>

        </div>


        {{-- =================================================
             TABLE
        ================================================== --}}
        <div class="table-wrap">

            <table class="dress-table">

                <thead>

                    <tr>

                        <th style="width:11%;">
                            รหัส
                        </th>

                        <th style="width:31%;">
                            ชุด
                        </th>

                        <th style="width:17%;">
                            หมวดหมู่
                        </th>

                        <th style="width:12%; text-align:center;">
                            ราคาเช่า
                        </th>

                        <th style="width:13%; text-align:center;">
                            สถานะ
                        </th>

                        <th style="width:16%; text-align:center;">
                            จัดการ
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($products as $product)

                    @php

                        $image = null;

                        if (
                            isset($product->images) &&
                            $product->images->count()
                        ) {
                            $image =
                                $product->images
                                    ->first()
                                    ->image_path
                                ??
                                $product->images
                                    ->first()
                                    ->url;
                        }

                        if (!$image && !empty($product->image)) {
                            $image = $product->image;
                        }

                        if (!$image && !empty($product->image_path)) {
                            $image = $product->image_path;
                        }

                        $productId =
                            $product->product_id
                            ??
                            $product->id;

                        $status =
                            strtolower(
                                $product->status ?? 'available'
                            );

                        $statusText = match ($status) {

                            'active',
                            'available'
                                => 'พร้อมให้เช่า',

                            'rented',
                            'busy'
                                => 'กำลังเช่า',

                            'maintenance'
                                => 'ซ่อมบำรุง',

                            'inactive'
                                => 'ปิดใช้งาน',

                            default
                                => $product->status ?? '-'

                        };

                        $statusClass = match ($status) {

                            'active',
                            'available'
                                => 'available',

                            'rented',
                            'busy'
                                => 'rented',

                            default
                                => 'inactive'

                        };

                        // ตาราง `categories` ใช้คอลัมน์ category_name (ไม่ใช่ name)
                        $productCategoryName = $product->category->category_name
                            ?? $product->category->name
                            ?? null;

                    @endphp


                    <tr>

                        {{-- CODE --}}
                        <td>

                            <div class="dress-code">
                                {{ $product->product_code }}
                            </div>

                        </td>


                        {{-- PRODUCT --}}
                        <td>

                            <div class="dress-info">

                                @if($image)

                                    <img
                                        src="{{
                                            Str::startsWith(
                                                $image,
                                                [
                                                    'http://',
                                                    'https://'
                                                ]
                                            )
                                            ? $image
                                            : asset(
                                                'storage/' .
                                                ltrim(
                                                    $image,
                                                    '/'
                                                )
                                            )
                                        }}"
                                        alt="{{ $product->product_name }}"
                                        class="dress-image"
                                    >

                                @else

                                    <div class="dress-image-empty">

                                        <i class="fa-solid fa-shirt"></i>

                                    </div>

                                @endif


                                <div>

                                    <div class="dress-name">
                                        {{ $product->product_name }}
                                    </div>

                                    <div class="dress-meta">
                                        รายการ #{{ $productId }}
                                    </div>

                                </div>

                            </div>

                        </td>


                        {{-- CATEGORY --}}
                        <td>

                            @if($productCategoryName)

                                <span class="category-tag">

                                    <i class="fa-solid fa-layer-group"></i>

                                    {{ $productCategoryName }}

                                </span>

                            @else

                                <span class="category-empty">
                                    ไม่ระบุหมวดหมู่
                                </span>

                            @endif

                        </td>


                        {{-- PRICE --}}
                        <td style="text-align:center;">

                            <span class="price">
                                ฿{{ number_format(
                                    $product->rental_price,
                                    2
                                ) }}
                            </span>

                        </td>


                        {{-- STATUS --}}
                        <td style="text-align:center;">

                            <span class="status {{ $statusClass }}">
                                {{ $statusText }}
                            </span>

                        </td>


                        {{-- ACTION --}}
                        <td>

                            <div class="action-group">

                                <a
                                    href="{{ route(
                                        'owner.dresses.edit',
                                        $productId
                                    ) }}"
                                    class="edit-btn"
                                >
                                    <i class="fa-regular fa-pen-to-square"></i>
                                    แก้ไข
                                </a>


                                <form
                                    action="{{ route(
                                        'owner.dresses.destroy',
                                        $productId
                                    ) }}"
                                    method="POST"
                                    style="margin:0;"
                                    onsubmit="
                                        return confirm(
                                            'ยืนยันการลบชุด {{ $product->product_code }} ใช่หรือไม่?'
                                        );
                                    "
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="delete-btn"
                                    >
                                        <i class="fa-regular fa-trash-can"></i>
                                        ลบ
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6">

                            <div class="empty-box">

                                <div class="empty-icon">
                                    <i class="fa-solid fa-shirt"></i>
                                </div>

                                <div class="empty-title">
                                    ไม่พบชุดตามเงื่อนไขที่เลือก
                                </div>

                                <div class="empty-text">
                                    ลองเลือกหมวดหมู่หรือสถานะอื่น
                                </div>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- =================================================
             PAGINATION
        ================================================== --}}
        @if($products->hasPages())

            <div class="pagination-wrap">

                {{ $products->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

@endsection