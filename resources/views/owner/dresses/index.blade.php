@extends('layouts.owner')

@section('title', 'คลังชุด | KYRIX Rental')

@push('styles')
<style>
    :root {
        /* ---- base ---- */
        --bg: #f7f6f5;
        --surface: #ffffff;
        --surface-alt: #fbfaf9;

        /* ---- text ---- */
        --ink: #1f1a1b;
        --heading: #1f1a1b;
        --muted: #78706e;
        --faint: #a89f9d;

        /* ---- lines ---- */
        --border: #e3ddda;
        --border-strong: #d2cac6;

        /* ---- accent (single, used sparingly) ---- */
        --accent: #7a2e3d;
        --accent-ink: #ffffff;
        --accent-soft: #f7ecee;

        /* ---- status ---- */
        --green: #2f6d4f;
        --green-bg: #eaf4ee;
        --amber: #92600b;
        --amber-bg: #fcf1df;
        --red: #a23b34;
        --red-bg: #fbeae8;

        --radius: 8px;
    }

    .kyrix-page {
        color: var(--ink);
        font-family: 'IBM Plex Sans Thai', 'Noto Sans Thai', 'Inter', -apple-system, sans-serif;
        font-feature-settings: "tnum" 1;
    }

    .kyrix-page * {
        box-sizing: border-box;
    }

    /* -------------------------------------------------------
       HEADER
    ------------------------------------------------------- */

    .page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .page-title {
        margin: 0;
        color: var(--heading);
        font-size: 22px;
        line-height: 1.3;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .page-description {
        margin: 3px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .add-dress-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 38px;
        padding: 0 16px;
        border-radius: var(--radius);
        background: var(--accent);
        color: var(--accent-ink);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: background .12s ease;
        flex-shrink: 0;
    }

    .add-dress-btn:hover {
        background: #62222e;
        color: var(--accent-ink);
    }

    .add-dress-btn i {
        font-size: 11px;
    }

    /* -------------------------------------------------------
       FLASH
    ------------------------------------------------------- */

    .flash {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 16px;
        padding: 10px 13px;
        border-radius: var(--radius);
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .flash-success {
        background: var(--green-bg);
        color: var(--green);
        border-color: #d3e9da;
    }

    .flash-error {
        background: var(--red-bg);
        color: var(--red);
        border-color: #f1d3d0;
    }

    /* -------------------------------------------------------
       STAT BAR — flat, hairline-divided, not shadowed cards
    ------------------------------------------------------- */

    .stat-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: 14px;
        overflow: hidden;
    }

    .stat-cell {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 3px;
        padding: 14px 18px;
        text-decoration: none;
        color: inherit;
        border-right: 1px solid var(--border);
        transition: background .12s ease;
    }

    .stat-cell:last-child {
        border-right: none;
    }

    .stat-cell:hover {
        background: var(--surface-alt);
    }

    .stat-cell.active {
        background: var(--accent-soft);
    }

    .stat-cell.active::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2px;
        background: var(--accent);
    }

    .stat-cell-label {
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 500;
    }

    .stat-cell.active .stat-cell-label {
        color: var(--accent);
    }

    .stat-cell-number {
        color: var(--heading);
        font-size: 20px;
        font-weight: 700;
        line-height: 1.1;
    }

    /* -------------------------------------------------------
       WORKSPACE
    ------------------------------------------------------- */

    .workspace {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    /* -------------------------------------------------------
       TOOLBAR
    ------------------------------------------------------- */

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 14px 16px;
        background: var(--surface-alt);
        border-bottom: 1px solid var(--border);
    }

    .result-summary {
        color: var(--muted);
        font-size: 12.5px;
        white-space: nowrap;
    }

    .result-summary strong {
        color: var(--heading);
        font-weight: 700;
    }

    .filter-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .select-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .select-wrap i {
        position: absolute;
        left: 12px;
        font-size: 11px;
        color: var(--faint);
        pointer-events: none;
    }

    .toolbar-select {
        height: 34px;
        min-width: 150px;
        padding: 0 30px 0 32px;
        border: 1px solid var(--border);
        border-radius: 999px;
        background-color: var(--surface);
        color: var(--ink);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 500;
        outline: none;
        cursor: pointer;

        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;

        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2378706e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 11px center;
        background-size: 13px 13px;

        transition: border-color .12s ease, box-shadow .12s ease, background-color .12s ease;
    }

    .toolbar-select:hover {
        border-color: var(--border-strong);
        background-color: var(--surface-alt);
    }

    .toolbar-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-soft);
        background-color: var(--surface);
    }

    #category_id.toolbar-select {
        min-width: 210px;
    }

    .category-reset {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        height: 34px;
        padding: 0 6px;
        color: var(--muted);
        font-size: 11.5px;
        text-decoration: none;
        white-space: nowrap;
        transition: color .12s ease;
    }

    .category-reset i {
        font-size: 9px;
    }

    .category-reset:hover {
        color: var(--accent);
    }

    /* -------------------------------------------------------
       TABLE
    ------------------------------------------------------- */

    .table-wrap {
        overflow-x: auto;
    }

    .dress-table {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
    }

    .dress-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        padding: 10px 16px;
        background: var(--surface-alt);
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }

    .dress-table td {
        padding: 11px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        font-size: 13px;
    }

    .dress-table tbody tr:last-child td {
        border-bottom: none;
    }

    .dress-table tbody tr:hover {
        background: var(--surface-alt);
    }

    .dress-code {
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .dress-info {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .dress-image,
    .dress-image-empty {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 6px;
        border: 1px solid var(--border);
        object-fit: cover;
        background: var(--surface-alt);
    }

    .dress-image-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--faint);
        font-size: 15px;
    }

    .dress-name {
        color: var(--ink);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.4;
    }

    .dress-meta {
        margin-top: 2px;
        color: var(--faint);
        font-size: 11px;
    }

    .category-tag {
        color: var(--muted);
        font-size: 12.5px;
    }

    .category-empty {
        color: var(--faint);
        font-size: 12px;
    }

    .price {
        color: var(--heading);
        font-size: 13px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 24px;
        padding: 0 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status.available {
        background: var(--green-bg);
        color: var(--green);
    }

    .status.available::before {
        background: var(--green);
    }

    .status.rented {
        background: var(--amber-bg);
        color: var(--amber);
    }

    .status.rented::before {
        background: var(--amber);
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
        justify-content: flex-end;
        gap: 6px;
    }

    .edit-btn,
    .delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 28px;
        padding: 0 10px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        transition: background .12s ease, border-color .12s ease;
        cursor: pointer;
        border: 1px solid var(--border);
        background: var(--surface);
    }

    .edit-btn {
        color: var(--ink);
        text-decoration: none;
    }

    .edit-btn:hover {
        border-color: var(--border-strong);
        background: var(--surface-alt);
        color: var(--ink);
    }

    .delete-btn {
        color: var(--red);
    }

    .delete-btn:hover {
        background: var(--red-bg);
        border-color: #f1d3d0;
    }

    /* -------------------------------------------------------
       EMPTY
    ------------------------------------------------------- */

    .empty-box {
        padding: 56px 20px;
        text-align: center;
    }

    .empty-icon {
        width: 44px;
        height: 44px;
        margin: 0 auto 12px;
        border-radius: 10px;
        background: var(--surface-alt);
        border: 1px solid var(--border);
        color: var(--faint);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .empty-title {
        color: var(--heading);
        font-size: 13px;
        font-weight: 700;
    }

    .empty-text {
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
    }

    /* -------------------------------------------------------
       PAGINATION
    ------------------------------------------------------- */

    .pagination-wrap {
        padding: 12px 16px;
        background: var(--surface-alt);
        border-top: 1px solid var(--border);
    }

    /* -------------------------------------------------------
       RESPONSIVE
    ------------------------------------------------------- */

    @media (max-width: 900px) {
        .stat-bar {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-cell:nth-child(2) {
            border-right: none;
        }

        .stat-cell:nth-child(1),
        .stat-cell:nth-child(2) {
            border-bottom: 1px solid var(--border);
        }
    }

    @media (max-width: 640px) {
        .stat-bar {
            grid-template-columns: 1fr;
        }

        .stat-cell {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }

        .stat-cell:last-child {
            border-bottom: none;
        }

        .page-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .add-dress-btn {
            width: 100%;
            justify-content: center;
        }

        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .select-wrap {
            width: 100%;
        }

        .toolbar-select,
        #category_id.toolbar-select {
            flex: 1;
            min-width: 0;
            width: 100%;
        }

        .category-reset {
            justify-content: flex-start;
            width: fit-content;
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
            <h1 class="page-title">
                คลังชุด
            </h1>

            <p class="page-description">
                จัดการชุดเช่าทั้งหมด ตรวจสอบหมวดหมู่ ราคา และสถานะการใช้งาน
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
         STAT BAR
    ====================================================== --}}
    <div class="stat-bar">

        <a
            href="{{ route('owner.dresses.index', request()->except(['status','category_id','page'])) }}"
            class="stat-cell {{ !request('status') && !request('category_id') ? 'active' : '' }}"
        >
            <span class="stat-cell-label">ชุดทั้งหมด</span>
            <span class="stat-cell-number">{{ number_format($summary['total'] ?? $products->total()) }}</span>
        </a>

        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'active']
            )) }}"
            class="stat-cell {{ request('status') === 'active' ? 'active' : '' }}"
        >
            <span class="stat-cell-label">พร้อมให้เช่า</span>
            <span class="stat-cell-number">{{ number_format($summary['available'] ?? 0) }}</span>
        </a>

        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'rented']
            )) }}"
            class="stat-cell {{ request('status') === 'rented' ? 'active' : '' }}"
        >
            <span class="stat-cell-label">กำลังเช่า</span>
            <span class="stat-cell-number">{{ number_format($summary['rented'] ?? 0) }}</span>
        </a>

        <a
            href="{{ route('owner.dresses.index', array_merge(
                request()->except(['status','category_id','page']),
                ['status' => 'inactive']
            )) }}"
            class="stat-cell {{ request('status') === 'inactive' ? 'active' : '' }}"
        >
            <span class="stat-cell-label">ปิดใช้งาน</span>
            <span class="stat-cell-number">{{ number_format($summary['inactive'] ?? 0) }}</span>
        </a>

    </div>


    {{-- =====================================================
         WORKSPACE
    ====================================================== --}}
    <div class="workspace">

        {{-- =================================================
             TOOLBAR
        ================================================== --}}
        <div class="toolbar">

            <div class="result-summary">
                พบ <strong>{{ number_format($products->total()) }}</strong> รายการ
            </div>

            <form
                action="{{ route('owner.dresses.index') }}"
                method="GET"
                class="filter-group"
            >

                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                {{-- =================================================
                     CATEGORY DROPDOWN
                ================================================== --}}
                <div class="select-wrap">

                    <i class="fa-solid fa-layer-group"></i>

                    <select
                        name="category_id"
                        id="category_id"
                        class="toolbar-select"
                        aria-label="กรองตามหมวดหมู่"
                        onchange="this.form.submit()"
                    >

                        <option value="">
                            ทุกหมวดหมู่ ({{ $summary['total'] ?? $products->total() }})
                        </option>

                        @foreach($categories as $category)

                            @php
                                // ตาราง `categories` ใช้คอลัมน์ category_id / category_name
                                $categoryId = $category->category_id ?? $category->id;
                                $categoryName = $category->category_name ?? $category->name;
                            @endphp

                            <option
                                value="{{ $categoryId }}"
                                {{ (string) request('category_id') === (string) $categoryId ? 'selected' : '' }}
                            >
                                {{ $categoryName }}{{ isset($category->products_count) ? ' ('.$category->products_count.')' : '' }}
                            </option>

                        @endforeach

                    </select>

                </div>

                @if(request('category_id'))
                    <a
                        href="{{ route('owner.dresses.index', request()->except(['category_id','page'])) }}"
                        class="category-reset"
                    >
                        <i class="fa-solid fa-xmark"></i>
                        ล้างตัวกรอง
                    </a>
                @endif

                {{-- =================================================
                     SORT DROPDOWN
                ================================================== --}}
                <div class="select-wrap">

                    <i class="fa-solid fa-arrow-down-wide-short"></i>

                    <select
                        name="sort"
                        id="sort"
                        class="toolbar-select"
                        aria-label="เรียงลำดับ"
                        onchange="this.form.submit()"
                    >
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>ล่าสุด</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>ชื่อ A-Z</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>ราคา ต่ำ → สูง</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>ราคา สูง → ต่ำ</option>
                    </select>

                </div>

            </form>

        </div>


        {{-- =================================================
             TABLE
        ================================================== --}}
        <div class="table-wrap">

            <table class="dress-table">

                <thead>
                    <tr>
                        <th style="width:10%;">รหัส</th>
                        <th style="width:33%;">ชุด</th>
                        <th style="width:16%;">หมวดหมู่</th>
                        <th style="width:12%; text-align:right;">ราคาเช่า</th>
                        <th style="width:13%;">สถานะ</th>
                        <th style="width:16%; text-align:right;">จัดการ</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($products as $product)

                    @php

                        $image = null;

                        if (isset($product->images) && $product->images->count()) {
                            $image = $product->images->first()->image_path
                                ?? $product->images->first()->url;
                        }

                        if (!$image && !empty($product->image)) {
                            $image = $product->image;
                        }

                        if (!$image && !empty($product->image_path)) {
                            $image = $product->image_path;
                        }

                        $productId = $product->product_id ?? $product->id;

                        $status = strtolower($product->status ?? 'available');

                        $statusText = match ($status) {
                            'active', 'available' => 'พร้อมให้เช่า',
                            'rented', 'busy' => 'กำลังเช่า',
                            'maintenance' => 'ซ่อมบำรุง',
                            'inactive' => 'ปิดใช้งาน',
                            default => $product->status ?? '-',
                        };

                        $statusClass = match ($status) {
                            'active', 'available' => 'available',
                            'rented', 'busy' => 'rented',
                            default => 'inactive',
                        };

                        // ตาราง `categories` ใช้คอลัมน์ category_name (ไม่ใช่ name)
                        $productCategoryName = $product->category->category_name
                            ?? $product->category->name
                            ?? null;

                    @endphp

                    <tr>

                        <td>
                            <span class="dress-code">{{ $product->product_code }}</span>
                        </td>

                        <td>
                            <div class="dress-info">

                                @if($image)
                                    <img
                                        src="{{
                                            Str::startsWith($image, ['http://', 'https://'])
                                                ? $image
                                                : asset('storage/' . ltrim($image, '/'))
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
                                    <div class="dress-name">{{ $product->product_name }}</div>
                                    <div class="dress-meta">รายการ #{{ $productId }}</div>
                                </div>

                            </div>
                        </td>

                        <td>
                            @if($productCategoryName)
                                <span class="category-tag">{{ $productCategoryName }}</span>
                            @else
                                <span class="category-empty">ไม่ระบุหมวดหมู่</span>
                            @endif
                        </td>

                        <td style="text-align:right;">
                            <span class="price">฿{{ number_format($product->rental_price, 2) }}</span>
                        </td>

                        <td>
                            <span class="status {{ $statusClass }}">{{ $statusText }}</span>
                        </td>

                        <td>
                            <div class="action-group">

                                <a href="{{ route('owner.dresses.edit', $productId) }}" class="edit-btn">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                    แก้ไข
                                </a>

                                <form
                                    action="{{ route('owner.dresses.destroy', $productId) }}"
                                    method="POST"
                                    style="margin:0;"
                                    onsubmit="return confirm('ยืนยันการลบชุด {{ $product->product_code }} ใช่หรือไม่?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="delete-btn">
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
                                <div class="empty-title">ไม่พบชุดตามเงื่อนไขที่เลือก</div>
                                <div class="empty-text">ลองเลือกหมวดหมู่หรือสถานะอื่น</div>
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