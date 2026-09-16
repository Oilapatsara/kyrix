@extends('layouts.customer')

@section('title', $product->product_name . ' | KYRIX เช่าชุด')

@push('styles')
    <style>
        .breadcrumb-wrap {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 14px 24px;
            font-size: 13px;
        }

        .breadcrumb {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .detail-container {
            max-width: 1280px;
            margin: 40px auto 80px;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: start;
        }

        /* Gallery */
        .gallery-wrap {
            position: sticky;
            top: 90px;
        }

        .main-image-box {
            width: 100%;
            height: 520px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            margin-bottom: 16px;
        }

        .main-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .main-image-box:hover img {
            transform: scale(1.03);
        }

        .thumbs-row {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .thumb-btn {
            width: 80px;
            height: 95px;
            border-radius: var(--radius-sm);
            border: 2px solid transparent;
            overflow: hidden;
            cursor: pointer;
            background: #fff;
            padding: 0;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .thumb-btn.active,
        .thumb-btn:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(122, 31, 43, 0.2);
        }

        .thumb-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Info */
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .info-header {
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
        }

        .info-cat {
            font-size: 13px;
            color: var(--gold);
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .info-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.3;
            margin-bottom: 12px;
        }

        .info-meta-row {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .info-code {
            font-family: 'Plus Jakarta Sans', monospace;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 3px 10px;
            border-radius: 6px;
        }

        /* Price Box */
        .price-box {
            background: #fff;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .price-main {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
        }

        .deposit-badge {
            background: var(--gold-light);
            color: #855d14;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        /* Measurements specs table */
        .specs-card {
            background: #fff;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 20px;
        }

        .specs-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            text-align: center;
        }

        .spec-item {
            background: #faf8f5;
            padding: 10px;
            border-radius: 8px;
        }

        .spec-label {
            font-size: 11px;
            color: var(--text-muted);
        }

        .spec-val {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 2px;
        }

        /* Rental Form */
        .rental-form-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 2px solid var(--primary-soft);
            padding: 26px;
            box-shadow: var(--shadow-md);
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 16px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .options-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .option-btn {
            padding: 9px 18px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .option-btn.selected {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(122, 31, 43, 0.2);
        }

        .dates-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .date-input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 14px;
            background: #faf8f5;
        }

        .date-input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
        }

        /* Add-on Services */
        .services-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .service-radio-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
        }

        .service-radio-label:hover {
            background: var(--primary-soft);
        }

        .service-radio-label.checked {
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        /* Price Calculation Live Box */
        .live-calc-box {
            background: #faf8f5;
            border-radius: var(--radius-md);
            padding: 18px 20px;
            margin: 20px 0;
            border: 1px dashed var(--border);
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        .calc-row.total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
            font-size: 17px;
            font-weight: 800;
            color: var(--primary);
        }

        .action-buttons-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* Reviews Section */
        .reviews-section {
            max-width: 1280px;
            margin: 40px auto 80px;
            padding: 0 24px;
        }

        .reviews-header-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .rating-big {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .rating-num {
            font-size: 48px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        .rating-stars {
            color: #f59e0b;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .review-item {
            background: #fff;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 24px;
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .review-user-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .review-photo-preview {
            width: 120px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border);
            margin-top: 8px;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .detail-container {
                grid-template-columns: 1fr;
            }

            .main-image-box {
                height: 380px;
            }

            .gallery-wrap {
                position: static;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumb-wrap">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">หน้าแรก</a>
            <span>/</span>
            <a href="{{ route('products.index') }}">ชุดทั้งหมด</a>
            <span>/</span>
            <a
                href="{{ route('products.index', ['category_id' => $product->category_id]) }}">{{ $product->category->category_name ?? 'หมวดหมู่' }}</a>
            <span>/</span>
            <span style="color: var(--text-main);">{{ $product->product_name }}</span>
        </div>
    </div>

    <div class="detail-container">
        <!-- 1. Product Image Gallery -->
        <div class="gallery-wrap">
            <div class="main-image-box" id="mainImageBox">
                <img src="{{ $product->main_image_url }}" id="mainProductImage" alt="{{ $product->product_name }}">
            </div>

            @if ($product->images->count() > 1)
                <div class="thumbs-row">
                    @foreach ($product->images as $idx => $img)
                        @php
                            $thumbUrl = str_starts_with($img->image_path, 'http')
                                ? $img->image_path
                                : asset('storage/' . $img->image_path);
                        @endphp
                        <button type="button" class="thumb-btn {{ $idx === 0 ? 'active' : '' }}"
                            onclick="switchImage('{{ $thumbUrl }}', this)">
                            <img src="{{ $thumbUrl }}" alt="thumb {{ $idx }}">
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Specs summary -->
            <div class="specs-card" style="margin-top: 24px;">
                <div class="specs-title">
                    <i class="fa-solid fa-ruler-combined" style="color: var(--primary);"></i>
                    <span>ตารางสัดส่วนชุด (Measurements Guide)</span>
                </div>
                <div class="specs-grid">
                    <div class="spec-item">
                        <div class="spec-label">รอบอก (Bust)</div>
                        <div class="spec-val">{{ $product->bust ?? '32-35 นิ้ว' }}</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">รอบเอว (Waist)</div>
                        <div class="spec-val">{{ $product->waist ?? '25-28 นิ้ว' }}</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">สะโพก (Hips)</div>
                        <div class="spec-val">{{ $product->hips ?? '35-38 นิ้ว' }}</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">ความยาว (Length)</div>
                        <div class="spec-val">{{ $product->length ?? '145 ซม.' }}</div>
                    </div>
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 10px; text-align: center;">
                    * ทางร้านมีบริการปรับแก้ทรงชั่วคราวให้พอดีสัดส่วนฟรี โดยไม่ทำให้ผ้าเสียหาย
                </p>
            </div>
        </div>

        <!-- 2. Product Info & Booking Form -->
        <div class="product-info">
            <div class="info-header">
                <div class="info-cat">{{ $product->category->category_name ?? 'ชุดเช่าพรีเมียม' }}</div>
                <h1 class="info-title">{{ $product->product_name }}</h1>
                <div class="info-meta-row" style="flex-wrap: wrap; gap: 14px;">
                    <span class="info-code">รหัส: {{ $product->product_code }}</span>
                    <span><i class="fa-solid fa-boxes-stacked"></i> จำนวนชุดที่มี: <strong>{{ $product->stock }}</strong>
                        ชุด</span>
                    <span>
                        @if ($product->status === 'available')
                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> ว่าง
                                (พร้อมเช่า)</span>
                        @elseif($product->status === 'rented')
                            <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> เช่าอยู่</span>
                        @elseif($product->status === 'maintenance')
                            <span class="badge badge-danger"><i class="fa-solid fa-wrench"></i> ซ่อม / ปรับปรุง</span>
                        @else
                            <span class="badge badge-secondary"><i class="fa-solid fa-ban"></i> ปิดใช้งาน</span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Price display -->
            <div class="price-box">
                <div>
                    <span style="font-size: 12px; color: var(--text-muted); display: block;">ราคาเช่า</span>
                    <span class="price-main">฿{{ number_format($product->rental_price) }}</span>
                    <span style="font-size: 14px; color: var(--text-muted);">/ วัน</span>
                </div>
                <div>
                    <span class="deposit-badge"><i class="fa-solid fa-shield"></i> เงินมัดจำ ฿100</span>
                </div>
            </div>

            <!-- Description & Details -->
            <div>
                <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 8px;">รายละเอียดชุด</h4>
                <p style="font-size: 14px; color: #555; line-height: 1.8; margin-bottom: 12px;">{{ $product->description }}
                </p>
                <div style="display: flex; gap: 20px; font-size: 13px; color: var(--text-muted);">
                    <span><i class="fa-solid fa-ruler"></i> ไซซ์มาตรฐาน:
                        <strong>{{ $product->size ?? 'M' }}</strong></span>
                    <span><i class="fa-solid fa-palette"></i> สี: <strong>{{ $product->color ?? 'ตามแบบ' }}</strong></span>
                </div>
            </div>

            <!-- 3. Interactive Rental Booking Form -->
            <div class="rental-form-card">
                @if ($product->status === 'available' && $product->stock > 0)
                    <form action="{{ route('rentals.book', $product->product_id) }}" method="POST" id="rentalBookingForm">
                        @csrf

                        <div class="form-section-title">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>จองเช่าชุด (Book Dress)</span>
                        </div>

                        <!-- Size Chooser -->
                        <div class="form-group">
                            <label class="form-label">เลือกไซซ์ (Size):</label>
                            <div class="options-row">
                                @foreach ($product->sizes_list as $idx => $s)
                                    <button type="button" class="option-btn {{ $idx === 0 ? 'selected' : '' }}"
                                        onclick="selectSize('{{ $s }}', this)">
                                        {{ $s }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="size" id="selectedSize"
                                value="{{ $product->sizes_list[0] ?? 'M' }}">
                        </div>

                        <!-- Color Chooser -->
                        <div class="form-group">
                            <label class="form-label">เลือกสี (Color):</label>
                            <div class="options-row">
                                @foreach ($product->colors_list as $idx => $c)
                                    <button type="button" class="option-btn {{ $idx === 0 ? 'selected' : '' }}"
                                        onclick="selectColor('{{ $c }}', this)">
                                        {{ $c }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="color" id="selectedColor"
                                value="{{ $product->colors_list[0] ?? $product->color }}">
                        </div>

                        <!-- Rental Dates -->
                        <div class="form-group">
                            <label class="form-label">กำหนดวันที่เริ่มเช่า และ วันที่คืนชุด: *</label>
                            <div class="dates-grid">
                                <div>
                                    <span
                                        style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 4px;">วันที่เริ่มเช่า
                                        (Start Date)</span>
                                    <input type="date" name="start_date" id="startDate" class="date-input"
                                        value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required
                                        onchange="calculateRental()">
                                </div>
                                <div>
                                    <span
                                        style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 4px;">วันที่คืนชุด
                                        (End Date)</span>
                                    <input type="date" name="end_date" id="endDate" class="date-input"
                                        value="{{ date('Y-m-d', strtotime('+2 days')) }}" min="{{ date('Y-m-d') }}"
                                        required onchange="calculateRental()">
                                </div>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="form-group">
                            <label class="form-label">ระบุจำนวนชุดที่ต้องการเช่า: *</label>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="number" name="quantity" id="rentalQty" value="1" min="1"
                                    max="{{ $product->stock }}" class="date-input" style="width: 110px;" required
                                    onchange="calculateRental()">
                                <span style="font-size: 13px; color: var(--text-muted);">(มีในสต็อกทั้งหมด
                                    {{ $product->stock }} ชุด)</span>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="form-group">
                            <label class="form-label">เขียนหมายเหตุเพิ่มเติม (ถ้ามี):</label>
                            <input type="text" name="note" class="date-input"
                                placeholder="เช่น ขอปรับขนาดเอวเข้า 1 นิ้ว, รับชุดช่วงบ่าย">
                        </div>

                        <!-- Live Price Calculation Box -->
                        <div class="live-calc-box">
                            <div class="calc-row">
                                <span>จำนวนวันที่เช่า:</span>
                                <strong id="displayDays">3 วัน</strong>
                            </div>
                            <div class="calc-row">
                                <span>ค่าเช่าชุด (฿{{ number_format($product->rental_price) }} x <span
                                        id="displayDaysText">3</span> วัน x <span id="displayQtyText">1</span>
                                    ชุด):</span>
                                <span id="displayRentalSubtotal">฿{{ number_format($product->rental_price * 3) }}</span>
                            </div>
                            <div class="calc-row">
                                <span>เงินมัดจำประกันชุด (<span id="displayQtyDepositText">1</span> ชุด):</span>
                                <span id="displayDeposit">฿100</span>
                            </div>
                            <div class="calc-row total">
                                <span>ยอดที่ต้องจ่ายรวมทั้งหมด:</span>
                                <span
                                    id="displayGrandTotal">฿{{ number_format($product->rental_price * 3 + $product->deposit) }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                            <i class="fa-solid fa-calendar-check"></i> ยืนยันการเช่าชุด & ไปหน้าชำระเงิน
                        </button>
                    </form>
                @else
                    <div style="text-align: center; padding: 30px 10px; color: var(--text-muted);">
                        <i class="fa-solid fa-circle-exclamation"
                            style="font-size: 38px; color: #dc2626; margin-bottom: 10px;"></i>
                        <h4 style="font-size: 16px; font-weight: 700; color: #b91c1c; margin-bottom: 6px;">
                            ชุดนี้ไม่พร้อมให้เช่าในขณะนี้</h4>
                        <p style="font-size: 13px;">สถานะ: {{ $product->status_label }}</p>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm"
                            style="margin-top: 12px;">เลือกดูชุดอื่นๆ</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 4. Customer Reviews Section (Feature 10) -->
    <section class="reviews-section" id="reviews">
        <div class="reviews-header-card">
            <div class="rating-big">
                <div class="rating-num">{{ $product->average_rating }}</div>
                <div>
                    <div class="rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star{{ $i <= round($product->average_rating) ? '' : '-o' }}"></i>
                        @endfor
                    </div>
                    <div style="font-size: 14px; color: var(--text-muted);">
                        จากความประทับใจของลูกค้า <strong>{{ $product->reviews_count }}</strong> ท่าน
                    </div>
                </div>
            </div>

            <div>
                <span style="font-size: 13px; color: var(--text-muted);">
                    <i class="fa-solid fa-check-circle" style="color: #16a34a;"></i>
                    รีวิวทั้งหมดมาจากลูกค้าที่เช่าและคืนชุดกับทางร้านจริงเท่านั้น
                </span>
            </div>
        </div>

        @forelse($product->reviews as $review)
            <div class="review-item">
                <div class="review-user-row">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div
                            style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary-soft); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                            {{ mb_substr($review->customer->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">
                                {{ $review->customer->user->name ?? 'ลูกค้าสมาชิก' }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                {{ $review->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    <div style="color: #f59e0b; font-size: 14px;">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                        @endfor
                    </div>
                </div>

                <p style="font-size: 14px; color: #444; line-height: 1.7; margin: 0;">{{ $review->comment }}</p>

                @if ($review->image_path)
                    @php
                        $revImg = str_starts_with($review->image_path, 'http')
                            ? $review->image_path
                            : asset($review->image_path);
                    @endphp
                    <div>
                        <a href="{{ $revImg }}" target="_blank">
                            <img src="{{ $revImg }}" class="review-photo-preview" alt="รูปลูกค้าใส่ชุดจริง">
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div
                style="background: #fff; padding: 40px; text-align: center; border-radius: var(--radius-md); border: 1px solid var(--border); color: var(--text-muted);">
                <i class="fa-regular fa-comment-dots" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1;"></i>
                <p>ยังไม่มีรีวิวสำหรับชุดนี้ ลูกค้าที่เช่าชุดนี้จะเป็นท่านแรกที่ได้เขียนรีวิวความประทับใจ!</p>
            </div>
        @endforelse
    </section>

    @push('scripts')
        <script>
            const dailyPrice = {{ (float) $product->rental_price }};
            const depositPrice = 100;
            let currentServiceFee = 0;

            function switchImage(src, btn) {
                document.getElementById('mainProductImage').src = src;
                document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            function selectSize(sz, btn) {
                document.getElementById('selectedSize').value = sz;
                btn.closest('.options-row').querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
            }

            function selectColor(color, btn) {
                document.getElementById('selectedColor').value = color;
                btn.closest('.options-row').querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
            }

            function setService(type, fee, labelElem) {
                currentServiceFee = fee;
                document.querySelectorAll('.service-radio-label').forEach(l => l.classList.remove('checked'));
                labelElem.classList.add('checked');
                calculateRental();
            }

            function calculateRental() {
                const startInput = document.getElementById('startDate')?.value;
                const endInput = document.getElementById('endDate')?.value;
                const qtyInput = document.getElementById('rentalQty');
                const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value) || 1) : 1;

                if (!startInput || !endInput) return;

                const start = new Date(startInput);
                const end = new Date(endInput);

                if (end < start) {
                    alert('วันคืนชุดต้องไม่น้อยกว่าวันรับชุด');
                    document.getElementById('endDate').value = startInput;
                    return calculateRental();
                }

                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // inclusive days
                const days = Math.max(1, diffDays);

                const rentalSubtotal = dailyPrice * days * qty;
                const depositTotal = depositPrice * qty;
                const grandTotal = rentalSubtotal + depositTotal;

                if (document.getElementById('displayDays')) document.getElementById('displayDays').innerText = days + ' วัน';
                if (document.getElementById('displayDaysText')) document.getElementById('displayDaysText').innerText = days;
                if (document.getElementById('displayQtyText')) document.getElementById('displayQtyText').innerText = qty;
                if (document.getElementById('displayQtyDepositText')) document.getElementById('displayQtyDepositText')
                    .innerText = qty;
                if (document.getElementById('displayRentalSubtotal')) document.getElementById('displayRentalSubtotal')
                    .innerText = '฿' + rentalSubtotal.toLocaleString();
                if (document.getElementById('displayDeposit')) document.getElementById('displayDeposit').innerText = '฿' +
                    depositTotal.toLocaleString();
                if (document.getElementById('displayGrandTotal')) document.getElementById('displayGrandTotal').innerText = '฿' +
                    grandTotal.toLocaleString();
            }

            document.addEventListener('DOMContentLoaded', () => {
                calculateRental();
            });
        </script>
    @endpush

@endsection
