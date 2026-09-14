@extends('layouts.customer')

@section('title', 'ตะกร้าเช่าชุดของฉัน | KYRIX')

@push('styles')
<style>
    .cart-wrapper {
        max-width: 1280px;
        margin: 40px auto 80px;
        padding: 0 24px;
    }
    .cart-title {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cart-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 32px;
        align-items: start;
    }

    /* Cart Items List */
    .cart-items-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 24px;
    }
    .cart-item {
        display: grid;
        grid-template-columns: 100px 1fr auto;
        gap: 20px;
        padding-bottom: 24px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border);
        align-items: center;
    }
    .cart-item:last-child {
        padding-bottom: 0;
        margin-bottom: 0;
        border-bottom: none;
    }
    .cart-item-img {
        width: 100px;
        height: 120px;
        border-radius: var(--radius-sm);
        overflow: hidden;
        border: 1px solid var(--border);
    }
    .cart-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .cart-item-info h4 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .cart-item-meta {
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .date-badge {
        background: var(--primary-soft);
        color: var(--primary);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .cart-item-price {
        text-align: right;
    }
    .subtotal-val {
        font-size: 18px;
        font-weight: 800;
        color: var(--primary);
    }
    .deposit-val {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* Summary Card */
    .summary-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 26px;
        position: sticky;
        top: 90px;
    }
    .summary-title {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin-bottom: 12px;
        color: var(--text-muted);
    }
    .summary-row strong {
        color: var(--text-main);
    }
    .summary-divider {
        height: 1px;
        background: var(--border);
        margin: 18px 0;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 20px;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 24px;
    }

    .empty-cart-box {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 70px 20px;
        text-align: center;
    }

    @media (max-width: 900px) {
        .cart-grid {
            grid-template-columns: 1fr;
        }
        .cart-item {
            grid-template-columns: 80px 1fr;
        }
        .cart-item-price {
            grid-column: 1/-1;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px dashed var(--border);
            padding-top: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="cart-wrapper">
    <h1 class="cart-title">
        <i class="fa-solid fa-bag-shopping" style="color: var(--primary);"></i>
        <span>ตะกร้าเช่าชุด (Rental Cart)</span>
    </h1>

    @if(count($cart) > 0)
        <div class="cart-grid">
            <!-- Items Card -->
            <div class="cart-items-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                    <span style="font-weight: 600; font-size: 15px;">ชุดที่เลือกทั้งหมด ({{ count($cart) }} รายการ)</span>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('ยืนยันล้างรายการทั้งหมดในตะกร้า?');">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #dc2626; font-size: 13px; cursor: pointer;">
                            <i class="fa-solid fa-trash-can"></i> ล้างตะกร้าทั้งหมด
                        </button>
                    </form>
                </div>

                @foreach($cart as $itemKey => $item)
                    <div class="cart-item">
                        <div class="cart-item-img">
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}">
                        </div>
                        <div class="cart-item-info">
                            <a href="{{ route('products.show', $item['product_id']) }}">
                                <h4>{{ $item['product_name'] }}</h4>
                            </a>
                            <div class="cart-item-meta">
                                <span><i class="fa-solid fa-tag"></i> {{ $item['product_code'] }}</span>
                                <span><i class="fa-solid fa-ruler"></i> ไซซ์: <strong>{{ $item['size'] }}</strong></span>
                                <span><i class="fa-solid fa-palette"></i> สี: <strong>{{ $item['color'] }}</strong></span>
                            </div>

                            <!-- Dates modifier form -->
                            <form action="{{ route('cart.update', $itemKey) }}" method="POST" style="background: #faf8f5; padding: 12px 14px; border-radius: 8px; margin-top: 8px;">
                                @csrf
                                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 12px; color: var(--text-muted);">รับ:</span>
                                        <input type="date" name="start_date" value="{{ $item['start_date'] }}" min="{{ date('Y-m-d') }}" style="padding: 4px 8px; border: 1px solid var(--border); border-radius: 4px; font-size: 12px;" required>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 12px; color: var(--text-muted);">คืน:</span>
                                        <input type="date" name="end_date" value="{{ $item['end_date'] }}" min="{{ date('Y-m-d') }}" style="padding: 4px 8px; border: 1px solid var(--border); border-radius: 4px; font-size: 12px;" required>
                                    </div>
                                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 11px;">
                                        อัปเดตวัน
                                    </button>
                                </div>
                                <div style="margin-top: 6px; font-size: 12px; color: var(--text-muted);">
                                    ระยะเวลาเช่า: <strong style="color: var(--primary);">{{ $item['days'] }} วัน</strong>
                                    @if($item['service_type'] !== 'none')
                                        | บริการเสริม: <strong>+฿{{ number_format($item['service_fee']) }}</strong>
                                    @endif
                                </div>
                            </form>
                        </div>

                        <div class="cart-item-price">
                            <div class="subtotal-val">฿{{ number_format($item['subtotal']) }}</div>
                            <div class="deposit-val">มัดจำ ฿{{ number_format($item['deposit']) }}</div>
                            <form action="{{ route('cart.remove', $itemKey) }}" method="POST" style="margin-top: 14px;" onsubmit="return confirm('ต้องการลบชุดนี้ออกจากตะกร้า?');">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: #999; cursor: pointer; font-size: 13px;" title="ลบรายการ">
                                    <i class="fa-solid fa-trash-can"></i> ลบ
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Card -->
            <div class="summary-card">
                <h3 class="summary-title">สรุปยอดการเช่า</h3>

                <div class="summary-row">
                    <span>ค่าเช่าชุดรวม:</span>
                    <strong>฿{{ number_format($totals['rental_total']) }}</strong>
                </div>

                <div class="summary-row">
                    <span>เงินมัดจำรวม:</span>
                    <strong>฿{{ number_format($totals['deposit_total']) }}</strong>
                </div>

                <div class="summary-row">
                    <span>ค่าบริการเสริมรวม:</span>
                    <strong>฿{{ number_format($totals['service_total']) }}</strong>
                </div>

                <div style="font-size: 12px; color: #855d14; background: var(--gold-light); padding: 8px 12px; border-radius: 6px; margin: 14px 0;">
                    <i class="fa-solid fa-circle-info"></i> เงินมัดจำจะโอนคืนให้ลูกค้าเต็มจำนวนทันทีหลังจากทางร้านตรวจสภาพชุดเรียบร้อย
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total">
                    <span>ยอดชำระสุทธิ:</span>
                    <span>฿{{ number_format($totals['grand_total']) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                    <i class="fa-solid fa-credit-card"></i> ดำเนินการชำระเงิน
                </a>

                <div style="text-align: center; margin-top: 14px;">
                    <a href="{{ route('products.index') }}" style="font-size: 13px; color: var(--text-muted);">
                        <i class="fa-solid fa-arrow-left"></i> เลือกดูชุดเพิ่มเติม
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="empty-cart-box">
            <i class="fa-solid fa-basket-shopping" style="font-size: 64px; color: #cbd5e1; margin-bottom: 20px;"></i>
            <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 10px;">ตะกร้าเช่าชุดของคุณยังว่างอยู่</h2>
            <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 30px;">
                ไปเลือกชุดราตรี ชุดไทย หรือสูทสากลสวยๆ แล้วกดเพิ่มลงตะกร้าได้เลย
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary" style="padding: 12px 30px;">
                <i class="fa-solid fa-magnifying-glass"></i> เลือกดูชุดทั้งหมด
            </a>
        </div>
    @endif
</div>
@endsection
