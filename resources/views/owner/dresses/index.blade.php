@extends('layouts.owner')

@section('title', 'คลังชุด | KYRIX Rental')

@section('content')

    <style>
        * {
            box-sizing: border-box;
        }

        .dresses-page {
            padding: 28px;
            background: #f8f7f5;
            min-height: calc(100vh - 70px);
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
        }

        .page-header-left h1 {
            margin: 0 0 6px;
            font-size: 30px;
            font-weight: 800;
            color: #211c19;
            letter-spacing: -0.5px;
        }

        .page-header-left p {
            margin: 0;
            color: #8d847e;
            font-size: 13px;
        }

        .add-product-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 17px;
            border-radius: 10px;
            background: #1f1b19;
            color: #fff !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            transition: .2s ease;
            white-space: nowrap;
        }

        .add-product-btn:hover {
            background: #3a322d;
            transform: translateY(-1px);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #ece7e3;
            border-radius: 14px;
            padding: 18px 20px;
        }

        .summary-label {
            color: #918983;
            font-size: 11px;
            margin-bottom: 7px;
        }

        .summary-value {
            color: #211c19;
            font-size: 27px;
            font-weight: 800;
            line-height: 1;
        }

        .summary-card.warning .summary-value {
            color: #b26a1f;
        }

        .summary-card.danger .summary-value {
            color: #b83d36;
        }

        .toolbar {
            background: #fff;
            border: 1px solid #ece7e3;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .toolbar-form {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) 180px 180px auto;
            gap: 10px;
            align-items: center;
        }

        .search-input,
        .filter-select {
            width: 100%;
            height: 42px;
            border: 1px solid #e5dfda;
            border-radius: 9px;
            background: #fff;
            color: #332d29;
            font-size: 12px;
            padding: 0 13px;
            outline: none;
        }

        .search-input:focus,
        .filter-select:focus {
            border-color: #c9a184;
            box-shadow: 0 0 0 3px rgba(201, 161, 132, .10);
        }

        .search-btn {
            height: 42px;
            border: 0;
            border-radius: 9px;
            background: #1f1b19;
            color: #fff;
            padding: 0 16px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .reset-btn {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 15px;
            border-radius: 9px;
            border: 1px solid #e5dfda;
            background: #fff;
            color: #695f59;
            font-size: 12px;
            text-decoration: none;
            font-weight: 600;
        }

        .reset-btn:hover {
            background: #faf8f6;
        }

        .table-card {
            background: #fff;
            border: 1px solid #ece7e3;
            border-radius: 14px;
            overflow: hidden;
        }

        .table-top {
            padding: 18px 20px;
            border-bottom: 1px solid #f0ece9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .table-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: #2a2420;
        }

        .table-count {
            color: #9b918a;
            font-size: 11px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1220px;
            border-collapse: collapse;
        }

        thead th {
            background: #fbfaf9;
            color: #8c837d;
            font-size: 10px;
            font-weight: 700;
            text-align: left;
            padding: 13px 14px;
            border-bottom: 1px solid #eeeae7;
            white-space: nowrap;
        }

        tbody td {
            padding: 14px;
            border-bottom: 1px solid #f2efed;
            vertical-align: middle;
            font-size: 12px;
            color: #514943;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: #fcfbfa;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 260px;
        }

        .dress-image {
            width: 58px;
            height: 70px;
            object-fit: cover;
            object-position: center;
            border-radius: 9px;
            background: #f3f0ee;
            display: block;
            flex-shrink: 0;
            border: 1px solid #eee8e4;
        }

        .dress-image-empty {
            width: 58px;
            height: 70px;
            border-radius: 9px;
            background: #f3f0ee;
            color: #b2aaa4;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #eee8e4;
            font-size: 18px;
        }

        .product-info {
            min-width: 0;
        }

        .product-name {
            color: #29231f;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.45;
            margin-bottom: 4px;
        }

        .product-id {
            color: #a39a94;
            font-size: 10px;
        }

        .sku {
            color: #5c544e;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .gender-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 0 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }

        .gender-female {
            background: #fbecf0;
            color: #a9506b;
        }

        .gender-male {
            background: #edf3f8;
            color: #4c6f89;
        }

        .gender-other {
            background: #f2f0ee;
            color: #736b65;
        }

        .variant-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .variant-line {
            color: #6c625c;
            font-size: 10px;
            line-height: 1.4;
        }

        .variant-empty {
            color: #aaa19b;
            font-size: 10px;
        }

        .stock-number {
            font-size: 14px;
            font-weight: 800;
        }

        .stock-normal {
            color: #3d6c4d;
        }

        .stock-low {
            color: #a96a1c;
        }

        .stock-out {
            color: #b63b36;
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-height: 26px;
            padding: 0 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            margin-top: 5px;
        }

        .stock-badge.normal {
            background: #edf7f0;
            color: #4d805d;
        }

        .stock-badge.low {
            background: #fff5e7;
            color: #a9691b;
        }

        .stock-badge.out {
            background: #fcebea;
            color: #b63e38;
        }

        .price {
            color: #29231f;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        /* =========================
               ACTIONS
            ========================= */

        .action-buttons {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            min-width: 250px;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid #e4ded9;
            background: #fff;
            color: #6f665f;
            text-decoration: none;
            transition: .18s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #faf7f5;
            border-color: #d5c3b7;
            transform: translateY(-1px);
        }

        .action-btn.edit {
            color: #725b4d;
        }

        .action-btn.stock {
            color: #3d6c4d;
        }

        .action-btn.delete {
            color: #a64a44;
        }

        .delete-form {
            display: inline;
            margin: 0;
        }

        .stock-add-form {
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 0;
        }

        .stock-add-input {
            width: 58px;
            height: 36px;
            padding: 0 8px;
            border: 1px solid #e4ded9;
            border-radius: 8px;
            background: #fff;
            color: #332d29;
            font-size: 11px;
            text-align: center;
            outline: none;
        }

        .stock-add-input:focus {
            border-color: #7a1f2b;
            box-shadow: 0 0 0 3px rgba(122, 31, 43, .08);
        }

        .stock-add-label {
            color: #928983;
            font-size: 10px;
            white-space: nowrap;
        }

        .empty-row {
            text-align: center;
            padding: 65px 20px !important;
            color: #9b928c !important;
        }

        .empty-icon {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: #f5f1ee;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            color: #aaa19b;
            font-size: 21px;
        }

        .empty-title {
            color: #544c46;
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .empty-text {
            color: #9d948e;
            font-size: 11px;
        }

        /* =========================
               PAGINATION
            ========================= */

        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 20px;
            border-top: 1px solid #f0ece9;
            background: #fff;
        }

        .pagination-info {
            color: #918983;
            font-size: 11px;
            white-space: nowrap;
        }

        .pagination-info strong {
            color: #4a413c;
            font-weight: 700;
        }

        .pagination-buttons {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .page-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5dfda;
            border-radius: 8px;
            background: #fff;
            color: #625952;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            transition: all .18s ease;
        }

        .page-btn:hover {
            color: #7a1f2b;
            border-color: #c9a184;
            background: #fcf7f5;
            transform: translateY(-1px);
        }

        .page-btn.active {
            color: #fff;
            background: #7a1f2b;
            border-color: #7a1f2b;
            box-shadow: 0 3px 8px rgba(122, 31, 43, .18);
        }

        .page-btn.disabled {
            color: #c9c2bd;
            background: #f8f6f4;
            border-color: #eeeae7;
            cursor: default;
        }

        .page-dots {
            width: 24px;
            text-align: center;
            color: #a69d97;
            font-size: 11px;
        }

        .alert-success {
            margin-bottom: 16px;
            border: 1px solid #cfe5d4;
            background: #f2faf4;
            color: #4b7758;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 12px;
        }

        .alert-error {
            margin-bottom: 16px;
            border: 1px solid #efd0cc;
            background: #fdf4f3;
            color: #a54b44;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 12px;
        }

        @media (max-width: 1050px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .toolbar-form {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 700px) {
            .dresses-page {
                padding: 18px 14px;
            }

            .page-header {
                flex-direction: column;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .toolbar-form {
                grid-template-columns: 1fr;
            }

            .add-product-btn {
                width: 100%;
            }

            .pagination-wrap {
                flex-direction: column;
                justify-content: center;
                gap: 12px;
            }

            .pagination-info {
                text-align: center;
            }

            .pagination-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>


    <div class="dresses-page">

        {{-- =========================================================
             ALERT
        ========================================================== --}}

        @if (session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif


        {{-- =========================================================
             HEADER
        ========================================================== --}}

        <div class="page-header">

            <div class="page-header-left">

                <h1>คลังชุด</h1>

                <p>
                    จัดการข้อมูลชุดและตรวจสอบจำนวนสินค้าคงเหลือของร้าน
                </p>

            </div>


            <a href="{{ route('owner.dresses.create') }}" class="add-product-btn">
                <i class="fa-solid fa-plus"></i>
                เพิ่มสินค้า
            </a>

        </div>


        {{-- =========================================================
             SUMMARY
        ========================================================== --}}

        @php

            $allProducts = $products ?? collect();

            $totalProducts = method_exists($allProducts, 'total') ? $allProducts->total() : $allProducts->count();

            $totalStock = 0;

            $lowStockProducts = 0;

            $outOfStockProducts = 0;

            foreach ($allProducts as $summaryProduct) {
                /*
                 * ใช้ stock จาก products เป็นหลัก
                 * เพราะระบบเช่าชุดของคุณเก็บ stock ไว้ที่ products.stock
                 */

                $summaryStock = $summaryProduct->stock !== null ? (int) $summaryProduct->stock : 0;

                $totalStock += $summaryStock;

                if ($summaryStock <= 0) {
                    $outOfStockProducts++;
                } elseif ($summaryStock <= 3) {
                    $lowStockProducts++;
                }
            }

        @endphp


        <div class="summary-grid">

            <div class="summary-card">

                <div class="summary-label">
                    สินค้าทั้งหมด
                </div>

                <div class="summary-value">
                    {{ number_format($totalProducts) }}
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-label">
                    จำนวนคงเหลือรวม
                </div>

                <div class="summary-value">
                    {{ number_format($totalStock) }}
                </div>

            </div>


            <div class="summary-card warning">

                <div class="summary-label">
                    ใกล้หมด
                </div>

                <div class="summary-value">
                    {{ number_format($lowStockProducts) }}
                </div>

            </div>


            <div class="summary-card danger">

                <div class="summary-label">
                    หมดสต็อก
                </div>

                <div class="summary-value">
                    {{ number_format($outOfStockProducts) }}
                </div>

            </div>

        </div>


        {{-- =========================================================
             TOOLBAR
        ========================================================== --}}

        <div class="toolbar">

            <form method="GET" action="{{ url()->current() }}" class="toolbar-form">

                <input type="text" name="search" value="{{ request('search') }}" class="search-input"
                    placeholder="ค้นหาชื่อสินค้า หรือ SKU...">


                <select name="gender" class="filter-select">

                    <option value="">
                        ทุกเพศ
                    </option>

                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>
                        ผู้หญิง
                    </option>

                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>
                        ผู้ชาย
                    </option>

                </select>


                <select name="stock" class="filter-select">

                    <option value="">
                        ทุกสถานะสต็อก
                    </option>

                    <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>
                        หมดสต็อก
                    </option>

                    <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>
                        ใกล้หมด
                    </option>

                    <option value="normal" {{ request('stock') === 'normal' ? 'selected' : '' }}>
                        มีสต็อกปกติ
                    </option>

                </select>


                <div style="display:flex;gap:8px;">

                    <button type="submit" class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        ค้นหา
                    </button>


                    <a href="{{ url()->current() }}" class="reset-btn">
                        ล้าง
                    </a>

                </div>

            </form>

        </div>


        {{-- =========================================================
             TABLE
        ========================================================== --}}

        <div class="table-card">

            <div class="table-top">

                <div>

                    <h2 class="table-title">
                        รายการชุดทั้งหมด
                    </h2>

                </div>


                <div class="table-count">

                    @if (method_exists($products ?? collect(), 'total'))
                        {{ number_format($products->total()) }} รายการ
                    @else
                        {{ number_format(($products ?? collect())->count()) }} รายการ
                    @endif

                </div>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                สินค้า
                            </th>

                            <th>
                                SKU
                            </th>

                            <th>
                                ประเภท
                            </th>

                            <th>
                                ตัวเลือก
                            </th>

                            <th>
                                จำนวนคงเหลือ
                            </th>

                            <th>
                                ราคา
                            </th>

                            <th>
                                จัดการ
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($products ?? [] as $product)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | รูปภาพ
                                |--------------------------------------------------------------------------
                                */

                                $image = null;

                                $images = $product->images ?? collect();

                                $mainImageObject = $images->firstWhere('is_main', 1);

                                if (!$mainImageObject) {
                                    $mainImageObject = $images->first();
                                }

                                if ($mainImageObject && !empty($mainImageObject->image_path)) {
                                    $imagePath = trim((string) $mainImageObject->image_path);

                                    if (
                                        filter_var($imagePath, FILTER_VALIDATE_URL) ||
                                        str_starts_with($imagePath, '//')
                                    ) {
                                        $image = $imagePath;
                                    } else {
                                        $cleanImagePath = ltrim($imagePath, '/');

                                        if (str_starts_with($cleanImagePath, 'storage/')) {
                                            $image = asset($cleanImagePath);
                                        } else {
                                            $image = asset('storage/' . $cleanImagePath);
                                        }
                                    }
                                }

                                if (empty($image) && !empty($product->image_url)) {
                                    $productImage = trim((string) $product->image_url);

                                    if (
                                        filter_var($productImage, FILTER_VALIDATE_URL) ||
                                        str_starts_with($productImage, '//')
                                    ) {
                                        $image = $productImage;
                                    } else {
                                        $image = asset(ltrim($productImage, '/'));
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

                                /*
                                |--------------------------------------------------------------------------
                                | STOCK
                                |--------------------------------------------------------------------------
                                */

                                $stockTotal = (int) ($product->stock ?? 0);

                                if ($stockTotal <= 0) {
                                    $stockClass = 'stock-out';
                                    $stockBadge = 'out';
                                    $stockText = 'หมดสต็อก';
                                } elseif ($stockTotal <= 3) {
                                    $stockClass = 'stock-low';
                                    $stockBadge = 'low';
                                    $stockText = 'ใกล้หมด';
                                } else {
                                    $stockClass = 'stock-normal';
                                    $stockBadge = 'normal';
                                    $stockText = 'มีสต็อก';
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Gender
                                |--------------------------------------------------------------------------
                                */

                                $gender = strtolower(trim((string) ($product->gender ?? '')));

                                if ($gender === 'female') {
                                    $genderClass = 'gender-female';

                                    $genderText = 'ผู้หญิง';
                                } elseif ($gender === 'male') {
                                    $genderClass = 'gender-male';

                                    $genderText = 'ผู้ชาย';
                                } else {
                                    $genderClass = 'gender-other';

                                    $genderText = $product->gender ?: 'ไม่ระบุ';
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Product ID
                                |--------------------------------------------------------------------------
                                */

                                $productId = $product->product_id ?? $product->id;
                            @endphp


                            <tr>


                                {{-- PRODUCT --}}

                                <td>

                                    <div class="product-cell">

                                        @if ($image)
                                            <img src="{{ $image }}" alt="{{ $product->product_name }}"
                                                class="dress-image" loading="lazy"
                                                onerror="
                                                    this.style.display='none';
                                                    this.nextElementSibling.style.display='flex';
                                                ">

                                            <div class="dress-image-empty" style="display:none;">
                                                <i class="fa-solid fa-shirt"></i>
                                            </div>
                                        @else
                                            <div class="dress-image-empty">

                                                <i class="fa-solid fa-shirt"></i>

                                            </div>
                                        @endif


                                        <div class="product-info">

                                            <div class="product-name">

                                                {{ $product->product_name }}

                                            </div>

                                            <div class="product-id">

                                                รหัสสินค้า:
                                                {{ $productId }}

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- SKU --}}

                                <td>

                                    @php

                                        $skuList = [];

                                        foreach ($variants as $variant) {
                                            $skuValue = $variant->sku ?? null;

                                            if ($skuValue !== null && $skuValue !== '') {
                                                $skuList[] = $skuValue;
                                            }
                                        }

                                        $skuList = array_values(array_unique($skuList));

                                    @endphp


                                    @if (!empty($skuList))
                                        <div class="variant-list">

                                            @foreach (array_slice($skuList, 0, 2) as $sku)
                                                <div class="sku">
                                                    {{ $sku }}
                                                </div>
                                            @endforeach


                                            @if (count($skuList) > 2)
                                                <div
                                                    style="
                                                        color:#aaa19b;
                                                        font-size:10px;
                                                    ">
                                                    +{{ count($skuList) - 2 }}
                                                    รายการ
                                                </div>
                                            @endif

                                        </div>
                                    @else
                                        <span class="variant-empty">
                                            -
                                        </span>
                                    @endif

                                </td>


                                {{-- GENDER --}}

                                <td>

                                    <span class="gender-badge {{ $genderClass }}">
                                        {{ $genderText }}
                                    </span>

                                </td>


                                {{-- VARIANTS --}}

                                <td>

                                    <div class="variant-list">

                                        @if (!empty($sizes))
                                            <div class="variant-line">

                                                <strong>
                                                    ไซซ์:
                                                </strong>

                                                {{ implode(', ', $sizes) }}

                                            </div>
                                        @endif


                                        @if (!empty($colors))
                                            <div class="variant-line">

                                                <strong>
                                                    สี:
                                                </strong>

                                                {{ implode(', ', $colors) }}

                                            </div>
                                        @endif


                                        @if (empty($sizes) && empty($colors))
                                            <span class="variant-empty">
                                                ไม่มีตัวเลือก
                                            </span>
                                        @endif

                                    </div>

                                </td>


                                {{-- STOCK --}}

                                <td>

                                    <div
                                        class="
                                            stock-number
                                            {{ $stockClass }}
                                        ">
                                        {{ number_format($stockTotal) }}
                                    </div>


                                    <div
                                        class="
                                            stock-badge
                                            {{ $stockBadge }}
                                        ">

                                        <span>
                                            ●
                                        </span>

                                        {{ $stockText }}

                                    </div>

                                </td>


                                {{-- PRICE --}}

                                <td>

                                    <div class="price">

                                        ฿{{ number_format((float) ($product->rental_price ?? ($product->price ?? 0)), 0) }}

                                    </div>

                                </td>


                                {{-- ACTIONS --}}

                                <td>

                                    <div class="action-buttons">


                                        {{-- แก้ไข --}}

                                        <a href="{{ route('owner.dresses.edit', $productId) }}"
                                            class="action-btn edit" title="แก้ไขข้อมูลชุด">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>


                                        {{-- เพิ่มสต็อก --}}

                                        <form method="POST"
                                            action="{{ url('/owner/dresses/' . $productId . '/add-stock') }}"
                                            class="stock-add-form"
                                            onsubmit="
                                                return confirm(
                                                    'ต้องการเพิ่มสต็อกสินค้านี้ใช่หรือไม่?'
                                                );
                                            ">

                                            @csrf

                                            <span class="stock-add-label">
                                                +
                                            </span>

                                            <input type="number" name="quantity" value="1" min="1"
                                                max="9999" class="stock-add-input" title="จำนวนที่ต้องการเพิ่ม">

                                            <button type="submit" class="action-btn stock" title="เพิ่มสต็อก">

                                                <i class="fa-solid fa-plus"></i>

                                            </button>

                                        </form>


                                        {{-- ลบ --}}

                                        <form method="POST"
                                            action="{{ route('owner.dresses.destroy', $productId) }}"
                                            class="delete-form"
                                            onsubmit="
                                                return confirm(
                                                    'ต้องการลบสินค้านี้ใช่หรือไม่?'
                                                );
                                            ">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit" class="action-btn delete" title="ลบสินค้า">

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </form>


                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="7" class="empty-row">

                                    <div class="empty-icon">

                                        <i class="fa-solid fa-box-open"></i>

                                    </div>


                                    <div class="empty-title">
                                        ไม่พบสินค้า
                                    </div>


                                    <div class="empty-text">
                                        ยังไม่มีสินค้าที่ตรงกับเงื่อนไขการค้นหา
                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =========================================================
                 PAGINATION
            ========================================================== --}}

            @if (isset($products) && method_exists($products, 'hasPages') && $products->total() > 0)

                @php

                    $pagination = $products->withQueryString();

                    $currentPage = $pagination->currentPage();

                    $lastPage = $pagination->lastPage();

                    $startPage = max(1, $currentPage - 2);

                    $endPage = min($lastPage, $currentPage + 2);

                @endphp


                <div class="pagination-wrap">


                    <div class="pagination-info">

                        แสดง

                        <strong>
                            {{ $pagination->firstItem() }}
                        </strong>

                        -

                        <strong>
                            {{ $pagination->lastItem() }}
                        </strong>

                        จาก

                        <strong>
                            {{ $pagination->total() }}
                        </strong>

                        รายการ

                    </div>


                    <div class="pagination-buttons">


                        {{-- ก่อนหน้า --}}

                        @if ($pagination->onFirstPage())
                            <span class="page-btn disabled">

                                <i class="fa-solid fa-chevron-left"></i>

                            </span>
                        @else
                            <a href="{{ $pagination->previousPageUrl() }}" class="page-btn" aria-label="ก่อนหน้า">

                                <i class="fa-solid fa-chevron-left"></i>

                            </a>
                        @endif


                        {{-- หน้าแรก --}}

                        @if ($startPage > 1)

                            <a href="{{ $pagination->url(1) }}" class="page-btn">
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
                                <span class="page-btn active">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $pagination->url($page) }}" class="page-btn">
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


                            <a href="{{ $pagination->url($lastPage) }}" class="page-btn">
                                {{ $lastPage }}
                            </a>

                        @endif


                        {{-- ถัดไป --}}

                        @if ($pagination->hasMorePages())
                            <a href="{{ $pagination->nextPageUrl() }}" class="page-btn" aria-label="ถัดไป">

                                <i class="fa-solid fa-chevron-right"></i>

                            </a>
                        @else
                            <span class="page-btn disabled">

                                <i class="fa-solid fa-chevron-right"></i>

                            </span>
                        @endif

                    </div>

                </div>

            @endif

        </div>

    </div>

@endsection
