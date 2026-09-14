@extends('layouts.owner')

@section('title', 'จัดการคลังชุด | KYRIX Admin')

@push('styles')
<style>
    :root {
        --maroon-900: #430d17;
        --maroon-800: #5c1522;
        --maroon-700: #6f1a2b;
        --gold:       #c79a5c;
        --gold-dark:  #a97f45;
        --rose-bg:    #f7e7ea;
        --rose-text:  #7f2138;
        --cream:      #faf7f4;
        --ink:        #241417;
        --muted:      #8a7a7d;
        --line:       #efe6e4;
    }

    .kyrix-admin-container {
        padding: 0;
        color: var(--ink);
    }

    .admin-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .admin-heading .eyebrow {
        display: inline-block;
        color: var(--gold-dark);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 6px;
    }

    .admin-heading h1 {
        margin: 0;
        font-family: "Playfair Display", "Noto Sans Thai", serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--maroon-900);
    }

    .admin-heading p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .admin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 42px;
        padding: 0 20px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 650;
        transition: .2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .admin-btn.primary {
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        color: #fff;
        box-shadow: 0 6px 15px rgba(111, 26, 43, .25);
    }

    .admin-btn.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(111, 26, 43, .35);
    }

    .content-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 3px 18px rgba(111, 26, 43, .04);
        overflow: hidden;
    }

    .search-box-wrap {
        padding: 18px 20px;
        background: #fff;
        border-bottom: 1px solid var(--line);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .search-input {
        height: 40px;
        padding: 0 14px;
        border: 1px solid var(--line);
        border-radius: 10px;
        font-size: 13px;
        width: 280px;
        outline: none;
        transition: .2s;
    }

    .search-input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(199, 154, 92, 0.15);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .kyrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
    }

    .kyrix-table th {
        text-align: left;
        padding: 14px 18px;
        background: var(--cream);
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--line);
    }

    .kyrix-table td {
        padding: 14px 18px;
        border-top: 1px solid var(--line);
        color: #3a2b2e;
        font-size: 13px;
        vertical-align: middle;
    }

    .kyrix-table tr:hover {
        background-color: #fdfbfb;
    }

    /* ล็อกขนาดรูปภาพขนาดย่อให้เป๊ะ สวยงาม */
    .product-thumb {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid var(--line);
        background: var(--cream);
    }

    .product-thumb-placeholder {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        border-radius: 10px;
        background: var(--rose-bg);
        color: var(--rose-text);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        border: 1px solid var(--line);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-active { background: #eef7ef; color: #4f7e53; }
    .status-inactive { background: var(--rose-bg); color: var(--rose-text); }

    .action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .action-btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 650;
        text-decoration: none;
        background: #fdf8ef;
        color: var(--gold-dark);
        border: 1px solid #f3e6d0;
        transition: .2s ease;
    }
    .action-btn-edit:hover { background: #f7ecd9; }

    .action-btn-delete {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 650;
        background: #fdf2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        cursor: pointer;
        transition: .2s ease;
    }
    .action-btn-delete:hover { background: #fee2e2; }

    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: var(--muted);
    }
    .empty-icon {
        font-size: 36px;
        color: var(--rose-text);
        opacity: .6;
        margin-bottom: 12px;
    }
</style>
@endpush

@section('content')
<div class="kyrix-admin-container">

    <!-- HEADER -->
    <div class="admin-header">
        <div class="admin-heading">
            <span class="eyebrow">KYRIX RENTAL · CATALOG</span>
            <h1>จัดการคลังชุด</h1>
            <p>ตรวจสอบรายการชุดทั้งหมด จัดการเพิ่ม แก้ไข และลบสินค้าภายในร้าน</p>
        </div>
        <div>
            <a href="{{ route('owner.dresses.create') }}" class="admin-btn primary">
                <i class="fa-solid fa-plus"></i> เพิ่มชุดใหม่
            </a>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="content-card">
        
        <!-- SEARCH BAR -->
        <div class="search-box-wrap">
            <div style="font-size: 13px; font-weight: 700; color: var(--maroon-900);">
                <i class="fa-solid fa-shirt mr-1"></i> รายการชุดทั้งหมด 
                <span style="color: var(--muted); font-weight: 500;">({{ method_exists($products, 'total') ? $products->total() : count($products) }} ชุด)</span>
            </div>
            <form action="{{ route('owner.dresses.index') }}" method="GET" style="display: flex; gap: 8px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหารหัส หรือชื่อชุด..." class="search-input">
                <button type="submit" class="admin-btn" style="height: 40px; padding: 0 14px; background: var(--cream); color: var(--maroon-800); border: 1px solid var(--line);">
                    <i class="fa-solid fa-magnifying-glass"></i> ค้นหา
                </button>
            </form>
        </div>

        <!-- TABLE -->
        <div class="table-wrapper">
            <table class="kyrix-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">รหัสชุด</th>
                        <th style="width: 38%;">รูปภาพ & ชื่อชุด</th>
                        <th style="width: 15%; text-align: center;">ราคาเช่า</th>
                        <th style="width: 15%; text-align: center;">สถานะ</th>
                        <th style="width: 17%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php
                        $productImage = null;
                        if (isset($product->images) && $product->images->count() > 0) {
                            $productImage = $product->images->first()->image_path ?? $product->images->first()->url ?? null;
                        } elseif (!empty($product->image)) {
                            $productImage = $product->image;
                        } elseif (!empty($product->image_path)) {
                            $productImage = $product->image_path;
                        }

                        $productId = $product->product_id ?? $product->id;
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: var(--maroon-900);">{{ $product->product_code }}</strong>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                @if($productImage)
                                    <img src="{{ Str::startsWith($productImage, ['http://', 'https://']) ? $productImage : asset('storage/' . ltrim($productImage, '/')) }}" alt="{{ $product->product_name }}" class="product-thumb">
                                @else
                                    <div class="product-thumb-placeholder">
                                        <i class="fa-solid fa-shirt"></i>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight: 700; color: #2d1e21; font-size: 13.5px;">{{ $product->product_name }}</div>
                                    @if(!empty($product->category->name))
                                        <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">หมวดหมู่: {{ $product->category->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 750; color: var(--maroon-900);">
                            ฿{{ number_format($product->rental_price, 2) }}
                        </td>
                        <td style="text-align: center;">
                            @php 
                                $status = strtolower($product->status ?? 'active');
                                $statusLabel = match($status) {
                                    'active', 'available' => 'พร้อมให้เช่า',
                                    'rented', 'busy' => 'กำลังเช่า',
                                    'inactive' => 'ปิดใช้งาน',
                                    default => $product->status
                                };
                                $statusClass = in_array($status, ['active', 'available']) ? 'status-active' : 'status-inactive';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td style="text-align: center;">
                            <div class="action-group">
                                <!-- ปุ่มแก้ไข -->
                                <a href="{{ route('owner.dresses.edit', $productId) }}" class="action-btn-edit" title="แก้ไขข้อมูล">
                                    <i class="fa-regular fa-pen-to-square"></i> แก้ไข
                                </a>
                                <!-- ปุ่มลบ -->
                                <form action="{{ route('owner.dresses.destroy', $productId) }}" method="POST" onsubmit="return confirm('⚠️ ยืนยันการลบชุด [{{ $product->product_code }}] ออกจากระบบใช่หรือไม่?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-delete" title="ลบข้อมูล">
                                        <i class="fa-regular fa-trash-can"></i> ลบ
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-shirt"></i></div>
                                <div style="font-weight: 600; font-size: 14px; color: var(--maroon-900);">ยังไม่มีข้อมูลชุดในระบบคลังชุด</div>
                                <div style="font-size: 12px; margin-top: 4px;">เริ่มต้นเพิ่มชุดแรกของคุณด้วยการคลิกปุ่ม "เพิ่มชุดใหม่" ด้านบน</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if(method_exists($products, 'hasPages') && $products->hasPages())
            <div style="padding: 16px 20px; background: var(--cream); border-top: 1px solid var(--line);">
                {{ $products->links() }}
            </div>
        @endif

    </div>
</div>
@endsection