@extends('layouts.customer')

@section('title', 'ชุดทั้งหมด | KYRIX ร้านเช่าชุดออกงาน ชุดสายฝอ และชุดสายหวาน')

@push('styles')
    <style>
        .catalog-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 30px 24px;
        }

        .catalog-header-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .catalog-title h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
        }

        .catalog-title p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .catalog-layout {
            max-width: 1280px;
            margin: 36px auto 70px;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 32px;
            align-items: start;
        }

        /* Filter Sidebar */
        .filter-card {
            background: #fff;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 90px;
        }

        .filter-group {
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .filter-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .filter-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .filter-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            cursor: pointer;
        }

        .filter-checkbox input[type="radio"],
        .filter-checkbox input[type="checkbox"] {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
        }

        .price-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .price-inputs input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
        }

        .sizes-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-pill-label {
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid var(--border);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .size-pill-label:hover,
        .size-pill-input:checked+.size-pill-label {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .size-pill-input {
            display: none;
        }

        /* Toolbar */
        .catalog-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .results-count {
            font-size: 14px;
            color: var(--text-muted);
        }

        .sort-select {
            padding: 9px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: #fff;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-main);
            cursor: pointer;
        }

        /* Product Grid */
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .product-card {
            background: #fff;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
            border-color: rgba(122, 31, 43, 0.3);
        }

        .product-img-wrap {
            position: relative;
            width: 100%;
            height: 440px;
            background: #f0ebe8;
            overflow: hidden;
        }

        .product-img-wrap img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: top center;
            transition: transform 0.4s ease;
            background: #f0ebe8;
        }

        .product-card:hover .product-img-wrap img {
            transform: scale(1.04);
        }

        .product-badges {
            position: absolute;
            top: 14px;
            left: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 5;
        }

        .status-badge {
            position: absolute;
            bottom: 12px;
            right: 12px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            z-index: 5;
        }

        .status-available {
            background: #15803d;
            color: #fff;
        }

        .status-rented {
            background: #b45309;
            color: #fff;
        }

        /* รูปสินค้าโหลดไม่ได้ */
        .product-image-fallback {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            background: #f0ebe8;
            color: #999;
            text-align: center;
            padding: 20px;
        }

        .product-image-fallback i {
            font-size: 40px;
            color: #c8bdb8;
        }

        .product-image-fallback span {
            font-size: 13px;
        }

        .product-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-cat {
            font-size: 12px;
            color: var(--gold);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .product-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.4;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-meta {
            display: flex;
            gap: 12px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .product-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .price-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
        }

        .price-deposit {
            font-size: 11px;
            color: #888;
            display: block;
            margin-top: 2px;
        }

        /* =====================================================
               CUSTOM PAGINATION
            ====================================================== */

        .pagination-wrap {
            width: 100%;
            margin-top: 32px;
            padding: 16px 0 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .pagination-info {
            color: var(--text-muted);
            font-size: 12px;
            white-space: nowrap;
        }

        .pagination-info strong {
            color: var(--text-main);
            font-weight: 700;
        }

        .pagination-buttons {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .page-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: #fff;
            color: var(--text-main);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all .2s ease;
            box-sizing: border-box;
        }

        .page-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: #fff8f8;
            transform: translateY(-1px);
        }

        .page-btn.active {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 3px 8px rgba(122, 31, 43, .18);
        }

        .page-btn.disabled {
            color: #c9c2bd;
            background: #f7f5f3;
            border-color: #e8e3df;
            cursor: default;
        }

        .page-dots {
            width: 28px;
            text-align: center;
            color: #9b918a;
            font-size: 13px;
        }

        @media (max-width: 1024px) {

            .catalog-layout {
                grid-template-columns: 1fr;
            }

            .filter-card {
                position: static;
            }

            .catalog-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 700px) {

            .pagination-wrap {
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 12px;
            }

            .pagination-info {
                text-align: center;
            }

            .pagination-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }

        }

        @media (max-width: 600px) {

            .catalog-header {
                padding: 24px 16px;
            }

            .catalog-title h1 {
                font-size: 23px;
            }

            .catalog-layout {
                padding: 0 16px;
                margin-top: 24px;
            }

            .catalog-grid {
                grid-template-columns: 1fr;
            }

            .product-img-wrap {
                height: 420px;
            }

            .product-footer {
                align-items: flex-end;
            }

        }
    </style>
@endpush


@section('content')

    <div class="catalog-header">

        <div class="catalog-header-inner">

            <div class="catalog-title">

                <h1>
                    คลังชุดเช่าทั้งหมด (Catalog)
                </h1>

                <p>
                    เลือกสรรชุดที่ตอบโจทย์ทุกงานของคุณ
                    พร้อมรายละเอียดสัดส่วน ค่าเช่า และสถานะว่าง
                </p>

            </div>

            <a href="{{ route('cart.index') }}" class="btn btn-secondary">

                <i class="fa-solid fa-bag-shopping"></i>

                ดูตะกร้าเช่า ({{ count(session('cart', [])) }})

            </a>

        </div>

    </div>


    <div class="catalog-layout">

        {{-- Sidebar Filters --}}

        <aside class="filter-card">

            <form action="{{ route('products.index') }}" method="GET" id="filterForm">

                {{-- Search --}}

                <div class="filter-group">

                    <div class="filter-title">

                        <span>
                            ค้นหาชุด
                        </span>

                    </div>

                    <div>

                        <input type="text" name="q" value="{{ request('q') }}" placeholder="ชื่อชุด, รหัส, สี..."
                            style="
                                width:100%;
                                padding:8px 12px;
                                border:1px solid var(--border);
                                border-radius:8px;
                                font-size:13px;
                            ">

                    </div>

                </div>


                {{-- Categories --}}

                <div class="filter-group">

                    <div class="filter-title">

                        <span>
                            ประเภทชุด
                        </span>

                        @if (request('category_id'))
                            <a href="{{ route('products.index', request()->except('category_id')) }}"
                                style="
                                    font-size:11px;
                                    color:var(--primary);
                                ">
                                ล้าง
                            </a>
                        @endif

                    </div>


                    <div class="filter-options">

                        <label class="filter-checkbox">

                            <input type="radio" name="category_id" value=""
                                {{ !request('category_id') ? 'checked' : '' }} onchange="this.form.submit()">

                            <span>
                                ทุกประเภทชุด
                            </span>

                        </label>


                        @foreach ($categories as $category)
                            <label class="filter-checkbox">

                                <input type="radio" name="category_id" value="{{ $category->category_id }}"
                                    {{ request('category_id') == $category->category_id ? 'checked' : '' }}
                                    onchange="this.form.submit()">

                                <span>

                                    {{ $category->category_name }}
                                    ({{ $category->products_count }})
                                </span>

                            </label>
                        @endforeach

                    </div>

                </div>


                {{-- Price --}}

                <div class="filter-group">

                    <div class="filter-title">

                        <span>
                            งบประมาณค่าเช่า (บาท)
                        </span>

                    </div>


                    <div class="price-inputs">

                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="ต่ำสุด">

                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="สูงสุด">

                    </div>


                    <button type="submit" class="btn btn-secondary btn-sm btn-block" style="margin-top:10px;">
                        กรองตามราคา
                    </button>

                </div>


                {{-- Sizes --}}

                <div class="filter-group">

                    <div class="filter-title">

                        <span>
                            ขนาด / ไซซ์
                        </span>

                    </div>


                    <div class="sizes-pills">

                        @foreach (['S', 'M', 'L', 'XL', 'Free Size'] as $sz)
                            <label>

                                <input type="radio" name="size" value="{{ $sz }}" class="size-pill-input"
                                    {{ request('size') == $sz ? 'checked' : '' }} onchange="this.form.submit()">

                                <span class="size-pill-label">
                                    {{ $sz }}
                                </span>

                            </label>
                        @endforeach

                    </div>

                </div>


                {{-- Color --}}

                <div class="filter-group">

                    <div class="filter-title">

                        <span>
                            สี
                        </span>

                    </div>


                    <input type="text" name="color" value="{{ request('color') }}" placeholder="เช่น แดง, ดำ, ชมพู..."
                        style="
                            width:100%;
                            padding:8px 12px;
                            border:1px solid var(--border);
                            border-radius:8px;
                            font-size:13px;
                        ">

                </div>


                {{-- Status --}}

                <div class="filter-group">

                    <div class="filter-title">

                        <span>
                            สถานะชุด
                        </span>

                    </div>


                    <div class="filter-options">

                        <label class="filter-checkbox">

                            <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }}
                                onchange="this.form.submit()">

                            <span>
                                ทั้งหมด
                            </span>

                        </label>


                        <label class="filter-checkbox">

                            <input type="radio" name="status" value="available"
                                {{ request('status') == 'available' ? 'checked' : '' }} onchange="this.form.submit()">

                            <span>

                                <i class="fa-solid fa-circle"
                                    style="
                                        color:#16a34a;
                                        font-size:8px;
                                    "></i>

                                ว่างพร้อมเช่า

                            </span>

                        </label>


                        <label class="filter-checkbox">

                            <input type="radio" name="status" value="rented"
                                {{ request('status') == 'rented' ? 'checked' : '' }} onchange="this.form.submit()">

                            <span>

                                <i class="fa-solid fa-circle"
                                    style="
                                        color:#b45309;
                                        font-size:8px;
                                    "></i>

                                อยู่ระหว่างเช่า

                            </span>

                        </label>


                        <label class="filter-checkbox">

                            <input type="radio" name="status" value="maintenance"
                                {{ request('status') == 'maintenance' ? 'checked' : '' }} onchange="this.form.submit()">

                            <span>

                                <i class="fa-solid fa-circle"
                                    style="
                                        color:#dc2626;
                                        font-size:8px;
                                    "></i>

                                ซ่อม / ปรับปรุง

                            </span>

                        </label>

                    </div>

                </div>


                {{-- Buttons --}}

                <div
                    style="
                        display:flex;
                        gap:8px;
                        margin-top:20px;
                    ">

                    <button type="submit" class="btn btn-primary btn-block">
                        ค้นหา
                    </button>


                    <a href="{{ route('products.index') }}" class="btn btn-secondary" title="ล้างตัวกรองทั้งหมด">

                        <i class="fa-solid fa-rotate-left"></i>

                    </a>

                </div>

            </form>

        </aside>


        {{-- Products --}}

        <main>

            {{-- Toolbar --}}

            <div class="catalog-toolbar">

                <div class="results-count">

                    พบชุดทั้งหมด

                    <strong>
                        {{ $products->total() }}
                    </strong>

                    รายการ

                    @if (request('q'))
                        สำหรับคำค้นหา

                        "<em>
                            {{ request('q') }}
                        </em>"
                    @endif

                </div>


                <div>

                    <select name="sort" class="sort-select"
                        onchange="
                            const form = document.getElementById('filterForm');
                            let sortInput = form.elements['sort'];

                            if (!sortInput) {
                                sortInput = document.createElement('input');
                                sortInput.type = 'hidden';
                                sortInput.name = 'sort';
                                form.appendChild(sortInput);
                            }

                            sortInput.value = this.value;
                            form.submit();
                        ">

                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>
                            มาใหม่ล่าสุด
                        </option>


                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                            ยอดนิยม / เช่าบ่อย
                        </option>


                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                            ราคา: ต่ำไปสูง
                        </option>


                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                            ราคา: สูงไปต่ำ
                        </option>

                    </select>

                </div>

            </div>


            {{-- =====================================================
                 PRODUCT IMAGE MAP
            ====================================================== --}}

            @php

                $currentProducts = method_exists($products, 'getCollection')
                    ? $products->getCollection()
                    : collect($products);

                $currentProductIds = $currentProducts
                    ->pluck('product_id')
                    ->filter()
                    ->map(function ($id) {
                        return (int) $id;
                    })
                    ->values()
                    ->all();

                $productImageMap = [];

                if (!empty($currentProductIds)) {
                    $imageRows = \Illuminate\Support\Facades\DB::table('product_images')
                        ->select('image_id', 'product_id', 'image_path', 'is_main')
                        ->whereIn('product_id', $currentProductIds)
                        ->orderByDesc('is_main')
                        ->orderBy('image_id')
                        ->get();

                    foreach ($imageRows as $imageRow) {
                        $productId = (int) $imageRow->product_id;

                        /*
                         | เก็บแค่รูปแรกของแต่ละ product_id
                         | เพราะ query เรียง is_main = 1 ก่อน
                         */

                        if (!isset($productImageMap[$productId])) {
                            $productImageMap[$productId] = trim((string) $imageRow->image_path);
                        }
                    }
                }

            @endphp


            {{-- Product Cards --}}

            <div class="catalog-grid">

                @forelse($products as $product)
                    @php

                        /*
                        |--------------------------------------------------------------------------
                        | รูปของสินค้านี้
                        |--------------------------------------------------------------------------
                        */

                        $productId = (int) $product->product_id;

                        $productImage = $productImageMap[$productId] ?? null;

                        $productImageUrl = null;

                        /*
                        |--------------------------------------------------------------------------
                        | แปลง image_path เป็น URL
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($productImage)) {
                            if (filter_var($productImage, FILTER_VALIDATE_URL)) {
                                $productImageUrl = $productImage;
                            } elseif (str_starts_with($productImage, '//')) {
                                $productImageUrl = $productImage;
                            } else {
                                $cleanPath = ltrim($productImage, '/');

                                if (str_starts_with($cleanPath, 'storage/')) {
                                    $productImageUrl = asset($cleanPath);
                                } else {
                                    $productImageUrl = asset('storage/' . $cleanPath);
                                }
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Variants
                        |--------------------------------------------------------------------------
                        */

                        $variants = $product->variants ?? collect();

                        $sizes = [];

                        $colors = [];

                        foreach ($variants as $variant) {
                            $sizeValue = $variant->size ?? ($variant->size_name ?? null);

                            if ($sizeValue !== null && $sizeValue !== '') {
                                $sizes[] = $sizeValue;
                            }

                            $colorValue = $variant->color ?? ($variant->color_name ?? null);

                            if ($colorValue !== null && $colorValue !== '') {
                                $colors[] = $colorValue;
                            }
                        }

                        $sizes = array_values(array_unique($sizes));

                        $colors = array_values(array_unique($colors));

                    @endphp


                    <div class="product-card">


                        {{-- Product Image --}}

                        <a href="{{ route('products.show', $product->product_id) }}" class="product-img-wrap">

                            @if ($productImageUrl)
                                <img src="{{ $productImageUrl }}" alt="{{ $product->product_name }}" loading="eager"
                                    decoding="async" referrerpolicy="no-referrer"
                                    onerror="
                                        this.onerror=null;
                                        this.style.display='none';

                                        const fallback =
                                            this.parentElement.querySelector(
                                                '.product-image-fallback'
                                            );

                                        if (fallback) {
                                            fallback.style.display='flex';
                                        }
                                    ">

                                <div class="product-image-fallback">

                                    <i class="fa-regular fa-image"></i>

                                    <span>
                                        ไม่สามารถโหลดรูปภาพสินค้าได้
                                    </span>

                                </div>
                            @else
                                <div class="product-image-fallback"
                                    style="
                                        display:flex;
                                    ">

                                    <i class="fa-regular fa-image"></i>

                                    <span>
                                        ยังไม่มีรูปภาพสินค้า
                                    </span>

                                </div>
                            @endif


                            {{-- Badges --}}

                            <div class="product-badges">

                                @if ($product->is_featured)
                                    <span class="tag-pill tag-featured">

                                        <i class="fa-solid fa-star"></i>

                                        แนะนำ

                                    </span>
                                @endif


                                @if ($product->is_new)
                                    <span class="tag-pill tag-new">
                                        NEW
                                    </span>
                                @endif


                                @if ($product->is_popular)
                                    <span class="tag-pill tag-popular">

                                        <i class="fa-solid fa-fire"></i>

                                        ยอดนิยม

                                    </span>
                                @endif

                            </div>


                            {{-- Status --}}

                            @if ($product->status === 'available')
                                <span class="status-badge status-available">

                                    <i class="fa-solid fa-check"></i>

                                    พร้อมให้เช่า

                                </span>
                            @elseif($product->status === 'rented')
                                <span class="status-badge status-rented">

                                    <i class="fa-solid fa-clock"></i>

                                    ติดจอง/เช่าอยู่

                                </span>
                            @elseif($product->status === 'maintenance')
                                <span class="status-badge"
                                    style="
                                        background:#dc2626;
                                        color:#fff;
                                    ">

                                    <i class="fa-solid fa-screwdriver-wrench"></i>

                                    ซ่อม / ปรับปรุง

                                </span>
                            @else
                                <span class="status-badge"
                                    style="
                                        background:#6b7280;
                                        color:#fff;
                                    ">
                                    ปิดใช้งาน
                                </span>
                            @endif

                        </a>


                        {{-- Product Body --}}

                        <div class="product-body">


                            {{-- Category --}}

                            <div class="product-cat">

                                {{ $product->category->category_name ?? 'ชุดเช่า' }}

                            </div>


                            {{-- Product Name --}}

                            <a href="{{ route('products.show', $product->product_id) }}">

                                <h3 class="product-title">

                                    {{ $product->product_name }}

                                </h3>

                            </a>


                            {{-- Product Meta --}}

                            <div class="product-meta">

                                <span>

                                    <i class="fa-solid fa-tag"></i>

                                    {{ $product->product_code }}

                                </span>


                                <span>

                                    <i class="fa-solid fa-ruler-combined"></i>

                                    {{ $product->size ?? 'M' }}

                                </span>


                                <span title="{{ $product->colors_text }}">

                                    <i class="fa-solid fa-palette"></i>

                                    {{ Str::limit($product->colors_text, 22) }}

                                </span>


                                <span>

                                    <i class="fa-solid fa-star"
                                        style="
                                            color:#f59e0b;
                                        "></i>

                                    {{ $product->average_rating }}

                                    ({{ $product->reviews_count }})
                                </span>

                            </div>


                            {{-- Product Footer --}}

                            <div class="product-footer">

                                <div class="price-wrap">

                                    <span class="price-value">

                                        ฿{{ number_format($product->rental_price) }}

                                        <small
                                            style="
                                                font-size:12px;
                                                font-weight:normal;
                                                color:#888;
                                            ">
                                            / วัน
                                        </small>

                                    </span>


                                    <span class="price-deposit">

                                        เงินมัดจำ
                                        ฿{{ number_format($product->deposit) }}

                                    </span>

                                </div>


                                <a href="{{ route('products.show', $product->product_id) }}"
                                    class="btn btn-primary btn-sm">
                                    เลือกเช่าชุด
                                </a>

                            </div>

                        </div>

                    </div>


                @empty

                    <div
                        style="
                            grid-column:1/-1;
                            background:#fff;
                            padding:60px 20px;
                            text-align:center;
                            border-radius:var(--radius-md);
                            border:1px solid var(--border);
                        ">

                        <i class="fa-solid fa-magnifying-glass"
                            style="
                                font-size:48px;
                                color:#cbd5e1;
                                margin-bottom:16px;
                            "></i>


                        <h3
                            style="
                                font-size:20px;
                                font-weight:700;
                                margin-bottom:8px;
                            ">
                            ไม่พบชุดที่ตรงกับเงื่อนไขการค้นหา
                        </h3>


                        <p
                            style="
                                color:var(--text-muted);
                                font-size:14px;
                                margin-bottom:24px;
                            ">
                            ลองเปลี่ยนคำค้นหา
                            หรือกดล้างตัวกรองเพื่อดูชุดทั้งหมด
                        </p>


                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            ล้างตัวกรองทั้งหมด
                        </a>

                    </div>
                @endforelse

            </div>


            {{-- =====================================================
                 CUSTOM PAGINATION
            ====================================================== --}}

            @if ($products->hasPages())

                @php

                    $currentPage = $products->currentPage();

                    $lastPage = $products->lastPage();

                    $startPage = max(1, $currentPage - 2);

                    $endPage = min($lastPage, $currentPage + 2);

                @endphp


                <div class="pagination-wrap">


                    {{-- จำนวนรายการ --}}

                    <div class="pagination-info">

                        แสดง

                        <strong>
                            {{ $products->firstItem() }}
                        </strong>

                        -

                        <strong>
                            {{ $products->lastItem() }}
                        </strong>

                        จากทั้งหมด

                        <strong>
                            {{ $products->total() }}
                        </strong>

                        รายการ

                    </div>


                    {{-- ปุ่ม Pagination --}}

                    <div class="pagination-buttons">


                        {{-- ก่อนหน้า --}}

                        @if ($products->onFirstPage())
                            <span class="page-btn disabled" aria-disabled="true">

                                <i class="fa-solid fa-chevron-left"></i>

                            </span>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" class="page-btn"
                                aria-label="หน้าก่อนหน้า">

                                <i class="fa-solid fa-chevron-left"></i>

                            </a>
                        @endif


                        {{-- หน้าแรก --}}

                        @if ($startPage > 1)

                            <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="page-btn">
                                1
                            </a>

                            @if ($startPage > 2)
                                <span class="page-dots">
                                    ...
                                </span>
                            @endif

                        @endif


                        {{-- หมายเลขหน้า --}}

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page == $currentPage)
                                <span class="page-btn active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ request()->fullUrlWithQuery(['page' => $page]) }}" class="page-btn">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor


                        {{-- หน้าสุดท้าย --}}

                        @if ($endPage < $lastPage)

                            @if ($endPage < $lastPage - 1)
                                <span class="page-dots">
                                    ...
                                </span>
                            @endif


                            <a href="{{ request()->fullUrlWithQuery(['page' => $lastPage]) }}" class="page-btn">
                                {{ $lastPage }}
                            </a>

                        @endif


                        {{-- ถัดไป --}}

                        @if ($products->hasMorePages())
                            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" class="page-btn"
                                aria-label="หน้าถัดไป">

                                <i class="fa-solid fa-chevron-right"></i>

                            </a>
                        @else
                            <span class="page-btn disabled" aria-disabled="true">

                                <i class="fa-solid fa-chevron-right"></i>

                            </span>
                        @endif


                    </div>

                </div>

            @endif


        </main>

    </div>

@endsection
