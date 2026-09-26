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

        /* =========================================================
           CART MAIN
        ========================================================= */

        .cart-box {
            background: #fff;
            border: 1px solid #e7e2dc;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.04);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 22px;
            border-bottom: 1px solid #e8e4df;
        }

        .cart-count {
            font-size: 16px;
            font-weight: 700;
            color: #222;
        }

        .clear-cart-btn {
            border: none;
            background: transparent;
            color: #dc2626;
            font-size: 13px;
            cursor: pointer;
            padding: 4px;
            transition: 0.2s;
        }

        .clear-cart-btn:hover {
            color: #b91c1c;
        }

        /* =========================================================
           CART ITEM
        ========================================================= */

        .cart-item {
            display: grid;
            grid-template-columns: 28px 90px minmax(300px, 1fr) 120px 145px 80px;
            gap: 18px;
            align-items: center;
            padding: 20px 22px;
            border-bottom: 1px solid #ece8e3;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        /* =========================================================
           CHECKBOX
        ========================================================= */

        .cart-check {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        /* =========================================================
           IMAGE
        ========================================================= */

        .cart-item-img {
            width: 90px;
            height: 105px;
            border-radius: 8px;
            overflow: hidden;
            background: #f4f3f1;
            border: 1px solid #e7e2dc;
            flex-shrink: 0;
        }

        .cart-item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* =========================================================
           INFO
        ========================================================= */

        .cart-item-info {
            min-width: 0;
        }

        .cart-item-info h4 {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.45;
            color: #202020;
        }

        .cart-item-info a {
            text-decoration: none;
            color: inherit;
        }

        .cart-item-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px 16px;
            font-size: 13px;
            color: #777;
        }

        .cart-item-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .cart-item-meta i {
            color: #8d8d8d;
        }

        .cart-item-meta strong {
            color: #333;
            font-weight: 600;
        }

        .rental-days {
            margin-top: 7px;
            font-size: 12px;
            color: #888;
        }

        /* =========================================================
           PRICE
        ========================================================= */

        .cart-price {
            text-align: right;
            white-space: nowrap;
        }

        .cart-price-label {
            font-size: 11px;
            color: #888;
            margin-bottom: 2px;
        }

        .cart-price-main {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
        }

        .cart-price-deposit {
            margin-top: 3px;
            font-size: 12px;
            color: #888;
        }

        /* =========================================================
           QUANTITY
        ========================================================= */

        .quantity-box {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid #dfd9d3;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
        }

        .quantity-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: #fff;
            color: #555;
            cursor: default;
            font-size: 15px;
        }

        .quantity-number {
            width: 52px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-left: 1px solid #e5e0db;
            border-right: 1px solid #e5e0db;
            font-size: 14px;
            color: #222;
            background: #fff;
        }

        /* =========================================================
           REMOVE
        ========================================================= */

        .remove-area {
            text-align: right;
        }

        .remove-btn {
            border: none;
            background: transparent;
            color: #333;
            cursor: pointer;
            font-size: 13px;
            padding: 4px 0;
            transition: 0.2s;
        }

        .remove-btn:hover {
            color: #dc2626;
        }

        /* =========================================================
           NOTE
        ========================================================= */

        .cart-footer-note {
            padding: 14px 22px;
            border-top: 1px solid #ece8e3;
            background: #faf9f7;
            color: #777;
            font-size: 12px;
            line-height: 1.6;
        }

        /* =========================================================
           SUMMARY
        ========================================================= */

        .summary-bar {
            background: #fff;
            border-top: 1px solid #e7e2dc;
            padding: 16px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            width: 100%;
            box-sizing: border-box;
        }

        .summary-left {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 13px;
            flex: 1;
            min-width: 0;
        }

        .summary-left i {
            color: #777;
            flex-shrink: 0;
        }

        .summary-left strong {
            color: #222;
            font-weight: 700;
        }

        .summary-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 18px;
            flex-shrink: 0;
        }

        .summary-total-box {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            min-width: 110px;
        }

        .summary-total-text {
            font-size: 12px;
            color: #666;
            line-height: 1.2;
            margin-bottom: 3px;
        }

        .summary-total-price {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            line-height: 1.1;
        }

        .checkout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 24px;
            min-width: 190px;
            height: 48px;
            white-space: nowrap;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-sizing: border-box;
        }

        .checkout-btn.disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        /* =========================================================
           EMPTY
        ========================================================= */

        .empty-cart-box {
            background: #fff;
            border: 1px solid #e7e2dc;
            border-radius: 14px;
            padding: 80px 20px;
            text-align: center;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1050px) {

            .cart-item {
                grid-template-columns:
                    26px 80px minmax(220px, 1fr) 100px 125px 70px;

                gap: 12px;
                padding: 18px;
            }

            .cart-item-img {
                width: 80px;
                height: 95px;
            }

            .cart-item-info h4 {
                font-size: 15px;
            }

            .cart-price-main {
                font-size: 16px;
            }
        }

        @media (max-width: 850px) {

            .cart-wrapper {
                padding: 0 14px;
            }

            .cart-box {
                overflow: visible;
            }

            .cart-item {
                grid-template-columns:
                    24px 80px minmax(0, 1fr);

                gap: 12px;
                padding: 18px 16px;
            }

            .cart-price {
                grid-column: 3;
                text-align: left;
                margin-top: 4px;
            }

            .quantity-box {
                grid-column: 3;
                justify-content: flex-start;
            }

            .remove-area {
                grid-column: 3;
                text-align: left;
            }

            .cart-footer-note {
                padding: 12px 16px;
            }

            .summary-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
            }

            .summary-left {
                flex: none;
            }

            .summary-right {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 520px) {

            .cart-title {
                font-size: 22px;
            }

            .cart-header {
                padding: 15px 16px;
            }

            .cart-count {
                font-size: 14px;
            }

            .cart-item {
                grid-template-columns:
                    22px 72px minmax(0, 1fr);

                gap: 10px;
            }

            .cart-item-img {
                width: 72px;
                height: 88px;
            }

            .cart-item-info h4 {
                font-size: 14px;
            }

            .cart-item-meta {
                font-size: 11px;
                gap: 5px 10px;
            }

            .cart-price-main {
                font-size: 17px;
            }

            .summary-right {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .summary-total-box {
                align-items: flex-start;
                min-width: auto;
            }

            .summary-total-price {
                font-size: 20px;
            }

            .checkout-btn {
                width: 100%;
            }
        }
    </style>
@endpush


@section('content')

    <div class="cart-wrapper">

        {{-- =====================================================
         TITLE
    ====================================================== --}}
        <h1 class="cart-title">

            <i class="fa-solid fa-bag-shopping" style="color: var(--primary);"></i>

            <span>
                ตะกร้าเช่าชุด (Rental Cart)
            </span>

        </h1>


        @if (count($cart) > 0)

            {{-- =================================================
             CART BOX
        ================================================== --}}
            <div class="cart-box">


                {{-- =================================================
                 HEADER
            ================================================== --}}
                <div class="cart-header">

                    <div class="cart-count">

                        ชุดที่เลือกทั้งหมด
                        ({{ count($cart) }} รายการ)

                    </div>


                    <form action="{{ route('cart.clear') }}" method="POST"
                        onsubmit="return confirm('ยืนยันล้างรายการทั้งหมดในตะกร้า?');">

                        @csrf

                        <button type="submit" class="clear-cart-btn">

                            <i class="fa-solid fa-trash-can"></i>

                            ล้างตะกร้าทั้งหมด

                        </button>

                    </form>

                </div>


                {{-- =================================================
                 ITEMS
            ================================================== --}}
                @foreach ($cart as $itemKey => $item)
                    <div class="cart-item" data-item-key="{{ $itemKey }}">


                        {{-- CHECKBOX --}}
                        <input type="checkbox" class="cart-check" value="{{ $itemKey }}"
                            data-subtotal="{{ (float) ($item['subtotal'] ?? 0) }}"
                            data-deposit="{{ (float) ($item['deposit'] ?? 0) }}"
                            data-service-fee="{{ (float) ($item['service_fee'] ?? 0) }}"
                            aria-label="เลือก {{ $item['product_name'] }}" @if (in_array($itemKey, session('checkout_selected_keys', []), true)) checked @endif>


                        {{-- IMAGE --}}
                        <div class="cart-item-img">

                            <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}">

                        </div>


                        {{-- PRODUCT INFO --}}
                        <div class="cart-item-info">

                            <a href="{{ route('products.show', $item['product_id']) }}">

                                <h4>
                                    {{ $item['product_name'] }}
                                </h4>

                            </a>


                            <div class="cart-item-meta">

                                <span>

                                    <i class="fa-solid fa-tag"></i>

                                    {{ $item['product_code'] }}

                                </span>


                                <span>

                                    <i class="fa-solid fa-ruler"></i>

                                    ไซซ์:

                                    <strong>
                                        {{ $item['size'] }}
                                    </strong>

                                </span>


                                <span>

                                    <i class="fa-solid fa-palette"></i>

                                    สี:

                                    <strong>
                                        {{ $item['color'] }}
                                    </strong>

                                </span>

                            </div>


                            <div class="rental-days">

                                <i class="fa-regular fa-calendar"></i>

                                ระยะเวลาเช่า:

                                <strong>
                                    {{ $item['days'] ?? 1 }} วัน
                                </strong>

                                <span style="color:#aaa;">

                                    (เลือกวันรับ-คืนในขั้นตอนสั่งเช่า)
                                </span>

                            </div>

                        </div>


                        {{-- PRICE --}}
                        <div class="cart-price">

                            <div class="cart-price-label">
                                ค่าเช่าชุด
                            </div>

                            <div class="cart-price-main">

                                ฿{{ number_format((float) ($item['subtotal'] ?? 0), 0) }}

                            </div>

                            <div class="cart-price-deposit">

                                มัดจำ
                                ฿{{ number_format((float) ($item['deposit'] ?? 0), 0) }}

                            </div>

                        </div>


                        {{-- QUANTITY --}}
                        <div class="quantity-box">

                            <div class="quantity-control">

                                <button type="button" class="quantity-btn" aria-label="ลดจำนวน" disabled>

                                    −

                                </button>


                                <div class="quantity-number">
                                    1
                                </div>


                                <button type="button" class="quantity-btn" aria-label="เพิ่มจำนวน" disabled>

                                    +

                                </button>

                            </div>

                        </div>


                        {{-- REMOVE --}}
                        <div class="remove-area">

                            <form action="{{ route('cart.remove', $itemKey) }}" method="POST"
                                onsubmit="return confirm('ต้องการลบชุดนี้ออกจากตะกร้า?');">

                                @csrf

                                <button type="submit" class="remove-btn">

                                    ลบ

                                </button>

                            </form>

                        </div>

                    </div>
                @endforeach


                {{-- =================================================
                 FOOTER NOTE
            ================================================== --}}
                <div class="cart-footer-note">

                    <i class="fa-solid fa-circle-info"></i>

                    ราคาเช่าชุดคิดเป็น

                    <strong>
                        1 การเช่า / 1 ครั้ง
                    </strong>

                    ไม่ได้คิดตามจำนวนวัน

                    โดยจำนวนวันที่แสดงใช้สำหรับระบุช่วงเวลาที่ลูกค้าเช่าชุดเท่านั้น

                </div>


                {{-- =================================================
                 SUMMARY
            ================================================== --}}
                <form action="{{ route('checkout.index') }}" method="GET" class="summary-bar" id="checkoutForm">


                    {{-- LEFT --}}
                    <div class="summary-left">

                        <i class="fa-solid fa-receipt"></i>

                        <span>

                            ค่าเช่าชุดรวม

                            <strong id="selected-item-count">
                                0 รายการ
                            </strong>

                        </span>

                    </div>


                    {{-- RIGHT --}}
                    <div class="summary-right">


                        {{-- TOTAL --}}
                        <div class="summary-total-box">

                            <div class="summary-total-text">
                                ยอดชำระสุทธิ
                            </div>

                            <div class="summary-total-price" id="cart-grand-total">

                                ฿0

                            </div>

                        </div>


                        {{-- BUTTON --}}
                        <button type="submit" class="btn btn-primary checkout-btn disabled" id="checkoutButton" disabled>

                            <i class="fa-solid fa-credit-card"></i>

                            ดำเนินการสั่งเช่า

                        </button>


                    </div>


                    {{-- HIDDEN SELECTED ITEM KEYS --}}
                    <div id="selectedCheckoutInputs"></div>

                </form>

            </div>


            {{-- =====================================================
             BACK TO PRODUCTS
        ====================================================== --}}
            <div style="text-align:center; margin-top:16px;">

                <a href="{{ route('products.index') }}"
                    style="
                    font-size:13px;
                    color:var(--text-muted);
                    text-decoration:none;
                ">

                    <i class="fa-solid fa-arrow-left"></i>

                    เลือกดูชุดเพิ่มเติม

                </a>

            </div>
        @else
            {{-- =====================================================
             EMPTY CART
        ====================================================== --}}
            <div class="empty-cart-box">

                <i class="fa-solid fa-basket-shopping"
                    style="
                    font-size:64px;
                    color:#cbd5e1;
                    margin-bottom:20px;
                ">
                </i>


                <h2
                    style="
                font-size:22px;
                font-weight:700;
                margin-bottom:10px;
            ">

                    ตะกร้าเช่าชุดของคุณยังว่างอยู่

                </h2>


                <p
                    style="
                color:var(--text-muted);
                font-size:15px;
                margin-bottom:30px;
            ">

                    ไปเลือกชุดออกงาน ชุดสายฝอ และชุดสายหวานสวยๆ
                    แล้วกดเพิ่มลงตะกร้าได้เลย

                </p>


                <a href="{{ route('products.index') }}" class="btn btn-primary" style="padding:12px 30px;">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    เลือกดูชุดทั้งหมด

                </a>

            </div>

        @endif

    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const checkboxes =
                    document.querySelectorAll('.cart-check');

                const totalElement =
                    document.getElementById('cart-grand-total');

                const countElement =
                    document.getElementById('selected-item-count');

                const checkoutForm =
                    document.getElementById('checkoutForm');

                const checkoutButton =
                    document.getElementById('checkoutButton');

                const selectedInputsContainer =
                    document.getElementById(
                        'selectedCheckoutInputs'
                    );


                /* =========================================================
                   FORMAT BAHT
                ========================================================= */

                function formatBaht(value) {

                    return '฿' + Number(
                        value || 0
                    ).toLocaleString(
                        'th-TH', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }
                    );

                }


                /* =========================================================
                   CALCULATE SELECTED ITEMS
                ========================================================= */

                function updateSelectedTotal() {

                    let rentalTotal = 0;

                    let depositTotal = 0;

                    let serviceTotal = 0;

                    let selectedCount = 0;


                    checkboxes.forEach(function(checkbox) {

                        if (!checkbox.checked) {
                            return;
                        }


                        rentalTotal += Number(
                            checkbox.dataset.subtotal || 0
                        );


                        depositTotal += Number(
                            checkbox.dataset.deposit || 0
                        );


                        serviceTotal += Number(
                            checkbox.dataset.serviceFee || 0
                        );


                        selectedCount++;

                    });


                    /*
                     * โปรโมชั่น
                     *
                     * ยังคงใช้หลักเดียวกับระบบเดิม
                     */
                    let discountAmount = 0;

                    if (rentalTotal >= 2000) {

                        discountAmount =
                            rentalTotal * 0.20;

                    }


                    /*
                     * ค่าเช่าหลังส่วนลด
                     */
                    const netRentalTotal =
                        Math.max(
                            0,
                            rentalTotal - discountAmount
                        );


                    /*
                     * ยอดชำระสุทธิ
                     */
                    const grandTotal =
                        netRentalTotal +
                        depositTotal +
                        serviceTotal;


                    /*
                     * จำนวนรายการ
                     */
                    countElement.textContent =
                        selectedCount + ' รายการ';


                    /*
                     * ยอดเงิน
                     */
                    totalElement.textContent =
                        formatBaht(grandTotal);


                    /*
                     * ปุ่ม Checkout
                     */
                    if (selectedCount > 0) {

                        checkoutButton.disabled = false;

                        checkoutButton.classList.remove(
                            'disabled'
                        );

                    } else {

                        checkoutButton.disabled = true;

                        checkoutButton.classList.add(
                            'disabled'
                        );

                    }

                }


                /* =========================================================
                   CHECKBOX CHANGE
                ========================================================= */

                checkboxes.forEach(function(checkbox) {

                    checkbox.addEventListener(
                        'change',
                        updateSelectedTotal
                    );

                });


                /* =========================================================
                   CHECKOUT SUBMIT
                ========================================================= */

                checkoutForm.addEventListener(
                    'submit',
                    function(event) {

                        /*
                         * ล้าง input เดิม
                         */
                        selectedInputsContainer.innerHTML = '';


                        /*
                         * รายการที่เลือก
                         */
                        const selected =
                            Array.from(checkboxes)
                            .filter(function(checkbox) {

                                return checkbox.checked;

                            });


                        /*
                         * ถ้าไม่ได้เลือกอะไร
                         */
                        if (selected.length === 0) {

                            event.preventDefault();

                            alert(
                                'กรุณาเลือกชุดอย่างน้อย 1 รายการก่อนดำเนินการสั่งเช่า'
                            );

                            return;

                        }


                        /*
                         * ส่ง itemKey ที่เลือก
                         */
                        selected.forEach(function(checkbox) {

                            const input =
                                document.createElement(
                                    'input'
                                );

                            input.type = 'hidden';

                            input.name =
                                'selected[]';

                            input.value =
                                checkbox.value;

                            selectedInputsContainer
                                .appendChild(input);

                        });

                    }
                );


                /* =========================================================
                   INITIAL CALCULATION
                ========================================================= */

                updateSelectedTotal();

            });
        </script>
    @endpush

@endsection
