@extends('layouts.customer')

@section('title', 'KYRIX | ร้านเช่าชุดราตรี ชุดไทย ชุดแต่งงาน สูทสากลระดับพรีเมียม')

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #f7ede8 0%, #faf8f5 50%, #ffffff 100%);
        padding: 70px 24px;
        position: relative;
        overflow: hidden;
    }
    .hero-container {
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        align-items: center;
        gap: 50px;
    }
    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid var(--border);
        color: var(--primary);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }
    .hero-title {
        font-size: 48px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 20px;
        color: var(--text-main);
    }
    .hero-title span {
        color: var(--primary);
        position: relative;
        display: inline-block;
    }
    .hero-desc {
        font-size: 17px;
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 34px;
        max-width: 540px;
    }
    .hero-actions {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .hero-stats {
        display: flex;
        gap: 32px;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid var(--border);
    }
    .hero-stat-item h3 {
        font-size: 28px;
        font-weight: 800;
        color: var(--primary);
    }
    .hero-stat-item p {
        font-size: 13px;
        color: var(--text-muted);
    }

    .hero-gallery {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        position: relative;
    }
    .hero-img-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        position: relative;
    }
    .hero-img-card img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .hero-img-card:hover img {
        transform: scale(1.05);
    }
    .hero-img-card.tall {
        height: 340px;
    }
    .hero-img-card.tall img {
        height: 100%;
    }
    .hero-float-badge {
        position: absolute;
        bottom: 20px;
        right: -10px;
        background: #fff;
        padding: 14px 20px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Section Styling */
    .section-wrap {
        max-width: 1280px;
        margin: 70px auto 0;
        padding: 0 24px;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 32px;
    }
    .section-title {
        font-size: 30px;
        font-weight: 800;
        color: var(--text-main);
    }
    .section-subtitle {
        color: var(--text-muted);
        font-size: 15px;
        margin-top: 4px;
    }
    .view-all-link {
        color: var(--primary);
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .view-all-link:hover {
        gap: 10px;
    }

    /* Category Cards */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
    }
    .category-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 24px 16px;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    .category-card:hover {
        border-color: var(--primary);
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }
    .category-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .category-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
    }
    .category-count {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* Product Grid & Card */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 26px;
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
        border-color: rgba(122,31,43,0.3);
    }
    .product-img-wrap {
        position: relative;
        width: 100%;
        height: 340px;
        background: #eee;
        overflow: hidden;
    }
    .product-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .product-card:hover .product-img-wrap img {
        transform: scale(1.06);
    }
    .product-badges {
        position: absolute;
        top: 14px;
        left: 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .tag-pill {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .tag-featured { background: var(--gold); color: #fff; }
    .tag-new { background: var(--primary); color: #fff; }
    .tag-popular { background: #1e293b; color: #fff; }

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
    }
    .product-footer {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .price-wrap {
        display: flex;
        flex-direction: column;
    }
    .price-label {
        font-size: 11px;
        color: var(--text-muted);
    }
    .price-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary);
    }
    .price-deposit {
        font-size: 11px;
        color: #888;
    }

    /* Promotions Banner */
    .promo-banner {
        background: linear-gradient(135deg, #7a1f2b 0%, #460f17 100%);
        border-radius: var(--radius-lg);
        color: #fff;
        padding: 44px 50px;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        align-items: center;
        gap: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }
    .promo-banner::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(198,156,76,0.15);
        top: -80px;
        right: -50px;
    }
    .promo-tag {
        display: inline-block;
        background: var(--gold);
        color: #fff;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 14px;
    }
    .promo-title {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 12px;
    }
    .promo-desc {
        color: #f1dfdf;
        font-size: 15px;
        line-height: 1.7;
        margin-bottom: 24px;
    }
    .promo-coupons {
        display: flex;
        gap: 16px;
    }
    .coupon-box {
        background: rgba(255,255,255,0.12);
        border: 1px dashed rgba(255,255,255,0.4);
        padding: 16px;
        border-radius: var(--radius-md);
        text-align: center;
        flex: 1;
        backdrop-filter: blur(5px);
    }
    .coupon-code {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #fff;
    }
    .coupon-desc {
        font-size: 12px;
        color: var(--gold-light);
        margin-top: 4px;
    }

    /* How It Works Steps */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        position: relative;
    }
    .step-card {
        background: #fff;
        padding: 30px 24px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        position: relative;
        text-align: center;
    }
    .step-num {
        position: absolute;
        top: 14px;
        right: 18px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 28px;
        font-weight: 800;
        color: #f1e9e7;
    }
    .step-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 20px;
    }
    .step-card h4 {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .step-card p {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* Reviews Section */
    .reviews-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .review-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 22px;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .review-stars {
        color: #f59e0b;
        font-size: 14px;
    }
    .review-text {
        font-size: 13px;
        color: #555;
        font-style: italic;
        line-height: 1.6;
        flex: 1;
    }
    .review-author {
        display: flex;
        align-items: center;
        gap: 10px;
        border-top: 1px solid var(--border);
        padding-top: 12px;
    }
    .author-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--gold-light);
        color: var(--gold);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
    }
    .author-name {
        font-size: 13px;
        font-weight: 600;
    }
    .author-dress {
        font-size: 11px;
        color: var(--text-muted);
    }

    @media (max-width: 1024px) {
        .hero-container {
            grid-template-columns: 1fr;
        }
        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .steps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .reviews-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .promo-banner {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 640px) {
        .hero-title {
            font-size: 34px;
        }
        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .products-grid {
            grid-template-columns: 1fr;
        }
        .steps-grid {
            grid-template-columns: 1fr;
        }
        .reviews-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-container">
        <div>
            <div class="hero-tag">
                <i class="fa-solid fa-crown" style="color: var(--gold);"></i>
                KYRIX DRESS RENTAL &bull; บูทีคเช่าชุดพรีเมียม
            </div>
            <h1 class="hero-title">
                เนรมิตความสง่างาม<br>
                <span>สำหรับค่ำคืนพิเศษ</span> ของคุณ
            </h1>
            <p class="hero-desc">
                ค้นพบชุดราตรี ชุดไทย ชุดแต่งงาน และทักซิโด้คุณภาพสูง ออกแบบและตัดเย็บอย่างประณีต ระบบจองออนไลน์ที่ง่ายดาย รวดเร็ว พร้อมบริการจัดส่งและปรับแก้ทรงฟรี
            </p>
            <div class="hero-actions">
                <a href="{{ route('products.index') }}" class="btn btn-primary" style="padding: 14px 28px; font-size: 15px;">
                    <i class="fa-solid fa-magnifying-glass"></i> ค้นหาชุดที่ถูกใจ
                </a>
                <a href="#how-it-works" class="btn btn-secondary" style="padding: 14px 24px; font-size: 15px;">
                    <i class="fa-solid fa-circle-play"></i> ขั้นตอนการเช่า
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <h3>500+</h3>
                    <p>ชุดสวยพร้อมให้เช่า</p>
                </div>
                <div class="hero-stat-item">
                    <h3>1,200+</h3>
                    <p>ลูกค้าพึงพอใจ</p>
                </div>
                <div class="hero-stat-item">
                    <h3>100%</h3>
                    <p>ซักรีดฆ่าเชื้อมาตรฐาน</p>
                </div>
            </div>
        </div>

        <div class="hero-gallery">
            <div class="hero-img-card tall">
                <img src="https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&auto=format&fit=crop&q=80" alt="ชุดราตรีหรู">
            </div>
            <div class="hero-img-card">
                <img src="https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=800&auto=format&fit=crop&q=80" alt="ชุดราตรีปักเลื่อม">
            </div>
            <div class="hero-float-badge">
                <i class="fa-solid fa-shield-check" style="color: #16a34a; font-size: 28px;"></i>
                <div>
                    <h5 style="font-size: 14px; font-weight: 700;">รับประกันความสะอาด</h5>
                    <p style="font-size: 12px; color: var(--text-muted); margin: 0;">อบไอน้ำฆ่าเชื้อทุกชุดก่อนส่ง</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 1. Categories Section -->
<section class="section-wrap" id="categories">
    <div class="section-header">
        <div>
            <h2 class="section-title">ประเภทชุดยอดนิยม</h2>
            <p class="section-subtitle">เลือกสไตล์ชุดที่เหมาะกับธีมงานและโอกาสพิเศษของคุณ</p>
        </div>
        <a href="{{ route('products.index') }}" class="view-all-link">
            ดูทุกประเภท <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="categories-grid">
        @php
            $catIcons = [
                1 => 'fa-person-dress',
                2 => 'fa-champagne-glasses',
                3 => 'fa-fan',
                4 => 'fa-heart',
                5 => 'fa-gem',
                6 => 'fa-user-tie',
            ];
        @endphp
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category_id' => $category->category_id]) }}" class="category-card">
                <div class="category-icon">
                    <i class="fa-solid {{ $catIcons[$category->category_id] ?? 'fa-sparkles' }}"></i>
                </div>
                <div class="category-name">{{ $category->category_name }}</div>
                <div class="category-count">{{ $category->products_count }} รายการ</div>
            </a>
        @endforeach
    </div>
</section>

<!-- 2. Featured Dresses (ชุดแนะนำ) -->
<section class="section-wrap">
    <div class="section-header">
        <div>
            <span class="promo-tag" style="background: var(--gold);">HANDPICKED</span>
            <h2 class="section-title">ชุดแนะนำประจำสัปดาห์</h2>
            <p class="section-subtitle">คัดสรรโดยสไตลิสต์ประจำร้าน KYRIX โดดเด่น สง่างามทุกองศา</p>
        </div>
        <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="view-all-link">
            ดูชุดแนะนำทั้งหมด <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="products-grid">
        @forelse($featuredDresses as $product)
            <div class="product-card">
                <a href="{{ route('products.show', $product->product_id) }}" class="product-img-wrap">
                    <img src="{{ $product->main_image_url }}" alt="{{ $product->product_name }}">
                    <div class="product-badges">
                        <span class="tag-pill tag-featured"><i class="fa-solid fa-star"></i> แนะนำ</span>
                        @if($product->is_new)
                            <span class="tag-pill tag-new">มาใหม่</span>
                        @endif
                    </div>
                </a>
                <div class="product-body">
                    <div class="product-cat">{{ $product->category->category_name ?? 'ชุดเช่า' }}</div>
                    <a href="{{ route('products.show', $product->product_id) }}">
                        <h3 class="product-title">{{ $product->product_name }}</h3>
                    </a>
                    <div class="product-meta">
                        <span><i class="fa-solid fa-ruler-combined"></i> {{ $product->size ?? 'M' }}</span>
                        <span><i class="fa-solid fa-palette"></i> {{ Str::limit($product->color, 16) }}</span>
                        <span><i class="fa-solid fa-eye"></i> {{ $product->views_count }}</span>
                    </div>
                    <div class="product-footer">
                        <div class="price-wrap">
                            <span class="price-label">ค่าเช่าเริ่มต้น</span>
                            <span class="price-value">฿{{ number_format($product->rental_price) }} <small style="font-size: 12px; font-weight: normal; color: #888;">/ วัน</small></span>
                            <span class="price-deposit">มัดจำ ฿{{ number_format($product->deposit) }}</span>
                        </div>
                        <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-calendar-check"></i> เช่าชุด
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">กำลังอัปเดตข้อมูลชุดแนะนำ</p>
        @endforelse
    </div>
</section>

<!-- 3. Promotion Banner Section -->
<section class="section-wrap">
    <div class="promo-banner">
        <div>
            <span class="promo-tag"><i class="fa-solid fa-fire"></i> โปรโมชั่นพิเศษต้อนรับซีซัน</span>
            <h2 class="promo-title">เช่า 3 วันขึ้นไป ลดทันที 15%</h2>
            <p class="promo-desc">
                เตรียมพร้อมสำหรับทุกงานเลี้ยง งานแต่ง หรือปาร์ตี้สำคัญ รับสิทธิ์ฟรีบริการซักรีดพรีเมียม และปรับแก้ขนาดชุดให้พอดีตัวโดยช่างมืออาชีพ
            </p>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('products.index') }}" class="btn btn-gold" style="padding: 12px 26px;">
                    <i class="fa-solid fa-tag"></i> รับโปรโมชั่นตอนนี้
                </a>
            </div>
        </div>
        <div class="promo-coupons">
            <div class="coupon-box">
                <div class="coupon-code">KYRIX15</div>
                <div class="coupon-desc">ลด 15% ค่าเช่า เมื่อเช่า 3 วันขึ้นไป</div>
            </div>
            <div class="coupon-box">
                <div class="coupon-code">FREEDRY</div>
                <div class="coupon-desc">ฟรีค่าซักรีดไอน้ำมูลค่า 150 บาท</div>
            </div>
        </div>
    </div>
</section>

<!-- 4. New Arrivals & Popular Dresses Tabs/Sections -->
<section class="section-wrap">
    <div class="section-header">
        <div>
            <h2 class="section-title">ชุดมาใหม่ & ยอดนิยม</h2>
            <p class="section-subtitle">อัปเดตคอลเลกชันล่าสุด สวยสะกดทุกสายตา</p>
        </div>
        <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="view-all-link">
            ดูชุดทั้งหมด <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="products-grid">
        @foreach($newDresses as $product)
            <div class="product-card">
                <a href="{{ route('products.show', $product->product_id) }}" class="product-img-wrap">
                    <img src="{{ $product->main_image_url }}" alt="{{ $product->product_name }}">
                    <div class="product-badges">
                        <span class="tag-pill tag-new">NEW ARRIVAL</span>
                    </div>
                </a>
                <div class="product-body">
                    <div class="product-cat">{{ $product->category->category_name ?? 'ชุดเช่า' }}</div>
                    <a href="{{ route('products.show', $product->product_id) }}">
                        <h3 class="product-title">{{ $product->product_name }}</h3>
                    </a>
                    <div class="product-meta">
                        <span><i class="fa-solid fa-ruler-combined"></i> {{ $product->size ?? 'M' }}</span>
                        <span><i class="fa-solid fa-palette"></i> {{ Str::limit($product->color, 16) }}</span>
                    </div>
                    <div class="product-footer">
                        <div class="price-wrap">
                            <span class="price-label">ค่าเช่าเริ่มต้น</span>
                            <span class="price-value">฿{{ number_format($product->rental_price) }} <small style="font-size: 12px; font-weight: normal; color: #888;">/ วัน</small></span>
                            <span class="price-deposit">มัดจำ ฿{{ number_format($product->deposit) }}</span>
                        </div>
                        <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-secondary btn-sm">
                            ดูรายละเอียด
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- 5. How To Rent Steps (วิธีการเช่า) -->
<section class="section-wrap" id="how-it-works">
    <div style="text-align: center; max-width: 600px; margin: 0 auto 40px;">
        <span class="promo-tag" style="background: var(--primary);">EASY 4 STEPS</span>
        <h2 class="section-title" style="margin-top: 8px;">ขั้นตอนการเช่าชุดที่ KYRIX</h2>
        <p class="section-subtitle">จองง่าย สะดวกสบาย มั่นใจได้ในทุกขั้นตอน</p>
    </div>

    <div class="steps-grid">
        <div class="step-card">
            <span class="step-num">01</span>
            <div class="step-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
            <h4>1. เลือกชุดและวันที่</h4>
            <p>เลือกชุดที่ชอบ ระบุไซซ์ สี และวันที่ต้องการใช้งานระบบคำนวณราคาอัตโนมัติ</p>
        </div>
        <div class="step-card">
            <span class="step-num">02</span>
            <div class="step-icon"><i class="fa-solid fa-qrcode"></i></div>
            <h4>2. ชำระเงิน & แนบสลิป</h4>
            <p>สแกน QR Code PromptPay หรือโอนเงิน แนบสลิปเข้าระบบเพื่อรอตรวจรับรองทันที</p>
        </div>
        <div class="step-card">
            <span class="step-num">03</span>
            <div class="step-icon"><i class="fa-solid fa-box-open"></i></div>
            <h4>3. รับชุดสวยพร้อมใส่</h4>
            <p>รับชุดที่หน้าร้านทองหล่อ หรือเลือกจัดส่งด่วนถึงบ้าน ชุดผ่านการซักรีดฆ่าเชื้อหอมสะอาด</p>
        </div>
        <div class="step-card">
            <span class="step-num">04</span>
            <div class="step-icon"><i class="fa-solid fa-rotate-left"></i></div>
            <h4>4. ส่งคืน & รับมัดจำคืน</h4>
            <p>ส่งคืนชุดตามกำหนด ไม่ต้องซักก่อนส่งคืน! ร้านตรวจเช็กและโอนคืนเงินมัดจำทันที</p>
        </div>
    </div>
</section>

<!-- 6. Customer Reviews -->
@if($latestReviews->count() > 0)
<section class="section-wrap">
    <div class="section-header">
        <div>
            <h2 class="section-title">รีวิวจากลูกค้าจริง</h2>
            <p class="section-subtitle">ความประทับใจจากผู้ใช้งานที่ไว้วางใจ KYRIX Dress Rental</p>
        </div>
    </div>

    <div class="reviews-grid">
        @foreach($latestReviews as $review)
            <div class="review-card">
                <div class="review-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                    @endfor
                </div>
                <p class="review-text">“{{ Str::limit($review->comment, 120) }}”</p>
                <div class="review-author">
                    <div class="author-avatar">{{ mb_substr($review->customer->user->name ?? 'C', 0, 1) }}</div>
                    <div>
                        <div class="author-name">{{ $review->customer->user->name ?? 'ลูกค้าท่านหนึ่ง' }}</div>
                        <div class="author-dress">{{ Str::limit($review->product->product_name ?? 'ชุดเช่า', 22) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
