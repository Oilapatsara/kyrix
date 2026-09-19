@extends('layouts.customer')

@section('title', 'ชำระเงินค่าเช่าชุด ' . $rental->formatted_code . ' | KYRIX')

@push('styles')
    <style>
        .payment-page-wrap {
            max-width: 900px;
            margin: 40px auto 80px;
            padding: 0 24px;
        }

        .payment-header {
            margin-bottom: 24px;
        }

        .payment-header h1 {
            font-size: 26px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }

        .payment-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 32px;
            margin-bottom: 24px;
        }

        .amount-summary-box {
            background: #faf8f5;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 24px;
            margin-bottom: 26px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 15px;
            margin-bottom: 10px;
            color: var(--text-muted);
            gap: 20px;
        }

        .amount-row strong {
            color: var(--text-main);
        }

        .amount-row.total {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 2px solid var(--border);
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
        }

        .method-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .method-label {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: all 0.2s;
            background: #fff;
        }

        .method-label:hover {
            border-color: var(--primary-light);
        }

        .method-label.selected {
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        .payment-method-info {
            margin-bottom: 24px;
        }

        .payment-detail {
            display: none;
        }

        .payment-detail.active {
            display: block;
        }

        .bank-box {
            background: #fff;
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin-bottom: 26px;
        }

        .qr-img {
            width: 280px;
            height: 280px;
            margin: 0 auto 18px;
            display: block;
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 8px;
            background: #fff;
            object-fit: contain;
        }

        .qr-amount-box {
            margin: 12px auto 16px;
            padding: 12px 18px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 10px;
            max-width: 340px;
        }

        .qr-amount-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .qr-amount-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
        }

        .payment-detail-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }

        .payment-detail-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 28px;
        }

        .payment-detail-title {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .payment-detail-text {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.8;
        }

        .payment-bank-info {
            background: #faf8f5;
            border-radius: 10px;
            padding: 18px;
            line-height: 2;
            margin-top: 18px;
        }

        .payment-info-box {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            line-height: 1.6;
        }

        .payment-info-success {
            background: #edfbf3;
            border: 1px solid #b7ecd0;
            color: #166534;
        }

        .payment-info-warning {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
        }

        .payment-qr-note {
            margin-top: 12px;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .slip-box {
            border: 2px dashed #d1c8c1;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            background: #faf8f5;
            transition: all 0.2s;
        }

        .slip-box:hover {
            border-color: var(--primary);
            background: #fff;
        }

        .slip-preview {
            max-width: 220px;
            max-height: 250px;
            border-radius: 8px;
            margin: 14px auto 0;
            display: none;
            box-shadow: var(--shadow-sm);
        }

        .status-note-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .qr-error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            line-height: 1.7;
        }

        /* =========================================================
               CANCEL RENTAL
            ========================================================= */

        .cancel-rental-box {
            margin-top: 18px;
            padding: 14px 18px;
            background: #fff8f7;
            border: 1px solid #f0d0cc;
            border-radius: 10px;
        }

        .cancel-rental-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cancel-rental-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 50%;
            background: #fdeceb;
            color: #b42318;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .cancel-rental-text {
            flex: 1;
            min-width: 0;
        }

        .cancel-rental-text h3 {
            margin: 0 0 2px;
            color: #5f161e;
            font-size: 14px;
            font-weight: 800;
        }

        .cancel-rental-text p {
            margin: 0;
            color: #7c6f6a;
            font-size: 12px;
            line-height: 1.5;
        }

        .cancel-rental-btn {
            height: 38px;
            padding: 0 16px;
            border: 1px solid #dfb0ab;
            border-radius: 8px;
            background: #fff;
            color: #b42318;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: all .18s ease;
        }

        .cancel-rental-btn:hover {
            background: #b42318;
            border-color: #b42318;
            color: #fff;
            box-shadow: 0 5px 15px rgba(180, 35, 24, .18);
        }

        /* =========================================================
               CANCEL MODAL
            ========================================================= */

        .cancel-modal {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .cancel-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(32, 18, 20, .58);
            backdrop-filter: blur(4px);
        }

        .cancel-modal-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, .22);
            overflow: hidden;
            animation: cancelModalIn .2s ease;
        }

        @keyframes cancelModalIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .cancel-modal-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cancel-modal-title-wrap {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .cancel-modal-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fdeceb;
            color: #b42318;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cancel-modal-header h3 {
            margin: 0;
            color: #2a2421;
            font-size: 16px;
            font-weight: 800;
        }

        .cancel-modal-header p {
            margin: 2px 0 0;
            color: #8a817b;
            font-size: 11px;
        }

        .cancel-modal-close {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            border-radius: 8px;
            color: #8a817b;
            font-size: 22px;
            cursor: pointer;
        }

        .cancel-modal-close:hover {
            background: #f8f3f1;
            color: #5f171f;
        }

        .cancel-modal-body {
            padding: 20px;
        }

        .cancel-warning {
            display: flex;
            gap: 10px;
            padding: 13px 14px;
            margin-bottom: 20px;
            background: #fff8e8;
            border: 1px solid #f0dfad;
            border-radius: 10px;
            color: #805b13;
        }

        .cancel-warning>i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .cancel-warning strong {
            display: block;
            font-size: 13px;
        }

        .cancel-warning p {
            margin: 3px 0 0;
            font-size: 12px;
            line-height: 1.6;
        }

        /* REASON FORM */

        .cancel-form-group {
            display: block;
            width: 100%;
        }

        .cancel-form-group label {
            display: block;
            width: 100%;
            margin-bottom: 8px;
            color: #2a2421;
            font-size: 13px;
            font-weight: 700;
        }

        .cancel-form-group label span {
            color: #b42318;
        }

        .cancel-form-group select {
            display: block;
            width: 100%;
            height: 44px;
            padding: 0 40px 0 13px;
            border: 1px solid #d8d0ca;
            border-radius: 9px;
            background: #fff;
            color: #2a2421;
            font-family: inherit;
            font-size: 13px;
            box-sizing: border-box;
            margin: 0;
            cursor: pointer;
        }

        .cancel-reason-textarea {
            display: block;
            width: 100%;
            min-height: 100px;
            height: 100px;
            margin-top: 10px !important;
            padding: 12px 13px;
            resize: vertical;
            border: 1px solid #d8d0ca;
            border-radius: 9px;
            background: #fff;
            color: #2a2421;
            font-family: inherit;
            font-size: 13px;
            line-height: 1.6;
            box-sizing: border-box;
        }

        .cancel-form-group select:focus,
        .cancel-reason-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(122, 31, 43, 0.08);
        }

        .cancel-reason-textarea::placeholder {
            color: #aaa19b;
        }

        .cancel-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 20px;
            background: #faf8f5;
            border-top: 1px solid var(--border);
        }

        .cancel-back-btn,
        .cancel-confirm-btn {
            height: 40px;
            padding: 0 17px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .cancel-back-btn {
            background: #fff;
            color: #736b66;
            border: 1px solid var(--border-strong, #d5cdc6);
        }

        .cancel-back-btn:hover {
            background: #f5f1ee;
        }

        .cancel-confirm-btn {
            border: 1px solid #b42318;
            background: #b42318;
            color: #fff;
            box-shadow: 0 4px 12px rgba(180, 35, 24, .18);
        }

        .cancel-confirm-btn:hover {
            background: #941b12;
            border-color: #941b12;
        }

        @media (max-width: 600px) {
            .method-grid {
                grid-template-columns: 1fr;
            }

            .payment-card {
                padding: 22px;
            }

            .payment-page-wrap {
                padding: 0 14px;
            }

            .payment-header h1 {
                font-size: 21px;
            }

            .amount-row {
                font-size: 14px;
            }

            .amount-row.total {
                font-size: 18px;
            }

            .qr-img {
                width: 220px;
                height: 220px;
            }

            .cancel-rental-content {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .cancel-rental-btn {
                width: 100%;
            }

            .cancel-modal {
                padding: 12px;
            }

            .cancel-modal-footer {
                flex-direction: column-reverse;
            }

            .cancel-back-btn,
            .cancel-confirm-btn {
                width: 100%;
            }
        }
    </style>
@endpush


@section('content')

    @php
        $rentalAmount = (float) ($rental->total_amount ?? 0);
        $discountAmount = (float) ($rental->discount_amount ?? 0);

        $netRentalAmount = max(0, $rentalAmount - $discountAmount);

        $depositAmount = (float) ($rental->deposit_amount ?? 0);
        $serviceFee = (float) ($rental->service_fee ?? 0);

        $grandTotal = $netRentalAmount + $depositAmount + $serviceFee;

        $discountReason = $rental->discount_reason ?? 'ส่วนลดโปรโมชั่น';

        $promptPayPhone = $promptPayPhone ?? '0652599072';

        $paymentAmount = $paymentAmount ?? $grandTotal;
    @endphp


    <div class="payment-page-wrap">

        {{-- HEADER --}}
        <div class="payment-header">

            <a href="{{ route('rentals.index') }}"
                style="
                    font-size: 13px;
                    color: var(--text-muted);
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    margin-bottom: 10px;
                    text-decoration: none;
                ">
                <i class="fa-solid fa-arrow-left"></i>
                กลับไปหน้ารายการการเช่า
            </a>

            <h1>
                <i class="fa-solid fa-credit-card" style="color: var(--primary);"></i>

                <span>
                    ชำระเงินค่าเช่าชุด
                    ({{ $rental->formatted_code }})
                </span>
            </h1>

            <p
                style="
                    color: var(--text-muted);
                    font-size: 14px;
                    margin-top: 4px;
                ">
                กรุณาเลือกวิธีการชำระเงิน
                และดำเนินการตามช่องทางที่เลือก
            </p>

        </div>


        {{-- STATUS --}}
        <div class="status-note-box">

            <strong>
                <i class="fa-solid fa-circle-info"></i>
                ขั้นตอนการตรวจสอบยอดชำระ:
            </strong>

            <br>

            หลังจากชำระเงินและส่งหลักฐานแล้ว
            สถานะจะเป็น
            <strong>"รอตรวจสอบ"</strong>

            &rarr;

            เจ้าหน้าที่จะตรวจสอบหลักฐาน

            &rarr;

            เปลี่ยนเป็น
            <strong>"อนุมัติแล้ว"</strong>

        </div>


        <div class="payment-card">

            {{-- SUMMARY --}}
            <div class="amount-summary-box">

                <h3
                    style="
                        font-size: 16px;
                        font-weight: 800;
                        margin-bottom: 14px;
                        color: var(--text-main);
                    ">
                    สรุปยอดเงินสำหรับใบสั่งเช่า:
                    {{ $rental->formatted_code }}
                </h3>


                <div
                    style="
                        margin-bottom: 14px;
                        padding-bottom: 12px;
                        border-bottom: 1px dashed var(--border);
                    ">

                    @foreach ($rental->details as $detail)
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 6px;
                                font-size: 14px;
                                gap: 15px;
                            ">

                            <span>

                                <strong style="color: var(--primary);">
                                    {{ $detail->product->product_name ?? 'ชุดเช่า' }}
                                </strong>

                                <span
                                    style="
                                        color: var(--text-muted);
                                        font-size: 12px;
                                    ">
                                    ({{ $detail->quantity }}
                                    ชุด x
                                    {{ $detail->rental_days }}
                                    วัน)
                                </span>

                            </span>

                            <span>
                                ฿{{ number_format((float) $detail->subtotal, 2) }}
                            </span>

                        </div>
                    @endforeach

                </div>


                <div class="amount-row">

                    <span>
                        ค่าเช่าชุดก่อนหักส่วนลด:
                    </span>

                    <strong>
                        ฿{{ number_format($rentalAmount, 2) }}
                    </strong>

                </div>


                @if ($discountAmount > 0)
                    <div class="amount-row" style="color: #16a34a;">

                        <span>

                            <i class="fa-solid fa-tag"></i>

                            {{ $discountReason }}:

                        </span>

                        <strong style="color: #16a34a;">
                            -฿{{ number_format($discountAmount, 2) }}
                        </strong>

                    </div>


                    <div class="amount-row">

                        <span>
                            ค่าเช่าสุทธิหลังหักส่วนลด:
                        </span>

                        <strong>
                            ฿{{ number_format($netRentalAmount, 2) }}
                        </strong>

                    </div>
                @endif


                <div class="amount-row">

                    <span>
                        เงินมัดจำประกันชุด:
                    </span>

                    <strong style="color: #b45309;">
                        ฿{{ number_format($depositAmount, 2) }}
                    </strong>

                </div>


                @if ($serviceFee > 0)
                    <div class="amount-row">

                        <span>
                            ค่าบริการเพิ่มเติม:
                        </span>

                        <strong>
                            ฿{{ number_format($serviceFee, 2) }}
                        </strong>

                    </div>
                @endif


                <div class="amount-row total">

                    <span>
                        ยอดที่ต้องชำระสุทธิ:
                    </span>

                    <span>
                        ฿{{ number_format($grandTotal, 2) }}
                    </span>

                </div>


                <div class="payment-info-box payment-info-success">

                    <i class="fa-solid fa-shield-halved"></i>

                    <strong>
                        เงินมัดจำประกันชุด
                        ฿{{ number_format($depositAmount, 2) }}
                    </strong>

                    จะได้รับคืนตามเงื่อนไขของร้าน
                    หลังจากส่งคืนชุดและผ่านการตรวจสอบสภาพ

                    <br>

                    ค่าเช่าชุดสุทธิ

                    <strong>
                        ฿{{ number_format($netRentalAmount, 2) }}
                    </strong>

                    เป็นค่าใช้จ่ายในการเช่า

                </div>

            </div>


            {{-- PAYMENT FORM --}}
            @if ($rental->status === 'pending_payment')

                <form action="{{ route('rentals.payment.submit', $rental->rental_id) }}" method="POST"
                    enctype="multipart/form-data" id="paymentForm">

                    @csrf

                    <h4
                        style="
                            font-size: 15px;
                            font-weight: 700;
                            margin-bottom: 12px;
                        ">
                        เลือกช่องทางชำระเงิน:
                    </h4>


                    {{-- PAYMENT METHODS --}}
                    <div class="method-grid">

                        {{-- QR --}}
                        <label class="method-label selected" onclick="selectMethod(this)">

                            <input type="radio" name="payment_method" value="qr" checked
                                style="accent-color: var(--primary);">

                            <div>

                                <strong
                                    style="
                                        display: block;
                                        font-size: 14px;
                                    ">
                                    QR Code PromptPay
                                </strong>

                                <span
                                    style="
                                        font-size: 12px;
                                        color: var(--text-muted);
                                    ">
                                    สแกนจ่ายผ่านแอปธนาคาร
                                </span>

                            </div>

                        </label>


                        {{-- BANK TRANSFER --}}
                        <label class="method-label" onclick="selectMethod(this)">

                            <input type="radio" name="payment_method" value="transfer"
                                style="accent-color: var(--primary);">

                            <div>

                                <strong
                                    style="
                                        display: block;
                                        font-size: 14px;
                                    ">
                                    โอนเงินผ่านบัญชีธนาคาร
                                </strong>

                                <span
                                    style="
                                        font-size: 12px;
                                        color: var(--text-muted);
                                    ">
                                    ธนาคารออมสิน
                                </span>

                            </div>

                        </label>

                    </div>


                    {{-- PAYMENT DETAILS --}}
                    <div class="payment-method-info">

                        {{-- QR DETAILS --}}
                        <div id="payment-detail-qr" class="payment-detail active">

                            <div class="bank-box">

                                @if (!empty($promptPayQr))
                                    <img src="{{ $promptPayQr }}" class="qr-img" alt="PromptPay QR Code">

                                    <div class="qr-amount-box">

                                        <div class="qr-amount-label">
                                            ยอดที่ต้องชำระ
                                        </div>

                                        <div class="qr-amount-value">
                                            ฿{{ number_format($grandTotal, 2) }}
                                        </div>

                                    </div>


                                    <div
                                        style="
                                            font-size: 14px;
                                            line-height: 1.9;
                                        ">

                                        <div>

                                            <strong>
                                                PromptPay (พร้อมเพย์):
                                            </strong>

                                            {{ $promptPayPhone }}

                                        </div>


                                        <div>

                                            <strong>
                                                ชื่อบัญชี:
                                            </strong>

                                            นางสาว อภัสรา แคะมะดัน

                                        </div>


                                        <div>

                                            <strong>
                                                รหัสการเช่า:
                                            </strong>

                                            {{ $rental->formatted_code }}

                                        </div>

                                    </div>


                                    <div class="payment-info-box payment-info-success">

                                        <i class="fa-solid fa-qrcode"></i>

                                        QR นี้กำหนดยอดชำระไว้แล้ว

                                        <strong>
                                            ฿{{ number_format($grandTotal, 2) }}
                                        </strong>

                                        <br>

                                        กรุณาสแกน QR Code
                                        ตรวจสอบชื่อผู้รับและยอดเงิน
                                        ก่อนกดยืนยันในแอปธนาคาร

                                    </div>


                                    <div class="payment-qr-note">

                                        หลังจากโอนสำเร็จ
                                        กรุณาแนบสลิปด้านล่าง
                                        เพื่อให้ร้านตรวจสอบ

                                    </div>
                                @else
                                    <div class="qr-error-box">

                                        <i class="fa-solid fa-triangle-exclamation"
                                            style="
                                                font-size: 28px;
                                                margin-bottom: 8px;
                                            "></i>

                                        <div>
                                            ไม่สามารถสร้าง QR Code PromptPay ได้
                                        </div>

                                        <div
                                            style="
                                                font-size: 12px;
                                                margin-top: 5px;
                                            ">
                                            กรุณาตรวจสอบค่า
                                            <strong>PROMPTPAY_PHONE</strong>
                                            ในไฟล์ .env
                                        </div>

                                    </div>
                                @endif

                            </div>

                        </div>


                        {{-- BANK TRANSFER DETAILS --}}
                        <div id="payment-detail-transfer" class="payment-detail">

                            <div class="payment-detail-card">

                                <div class="payment-detail-icon">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>


                                <div class="payment-detail-title" style="text-align: center;">
                                    โอนเงินผ่านบัญชีธนาคาร
                                </div>


                                <div class="payment-detail-text" style="text-align: center;">
                                    กรุณาโอนเงินตามข้อมูลด้านล่าง
                                    แล้วแนบสลิปเพื่อยืนยันการชำระเงิน
                                </div>


                                <div class="payment-bank-info">

                                    <div>

                                        <strong>
                                            ธนาคาร:
                                        </strong>

                                        ออมสิน

                                    </div>


                                    <div>

                                        <strong>
                                            เลขที่บัญชี:
                                        </strong>

                                        <span
                                            style="
                                                font-family: monospace;
                                                font-size: 18px;
                                                color: var(--primary);
                                                font-weight: 700;
                                            ">
                                            020310925126
                                        </span>

                                    </div>


                                    <div>

                                        <strong>
                                            ชื่อบัญชี:
                                        </strong>

                                        นางสาว อภัสรา แคะมะดัน

                                    </div>


                                    <div>

                                        <strong>
                                            ยอดที่ต้องโอน:
                                        </strong>

                                        <span
                                            style="
                                                color: var(--primary);
                                                font-size: 20px;
                                                font-weight: 800;
                                            ">
                                            ฿{{ number_format($grandTotal, 2) }}
                                        </span>

                                    </div>

                                </div>


                                <div class="payment-info-box payment-info-warning">

                                    <i class="fa-solid fa-circle-info"></i>

                                    หลังจากโอนเงินแล้ว
                                    กรุณาแนบสลิปด้านล่าง

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- SLIP --}}
                    <div id="slipSection" style="margin-bottom: 24px;">

                        <label
                            style="
                                font-size: 15px;
                                font-weight: 700;
                                display: block;
                                margin-bottom: 6px;
                            ">
                            อัปโหลดสลิปหลักฐานการโอนเงิน
                            <span>*</span>
                        </label>


                        <div class="slip-box" onclick="document.getElementById('slipInput').click();">

                            <i class="fa-solid fa-cloud-arrow-up"
                                style="
                                    font-size: 36px;
                                    color: var(--primary);
                                    margin-bottom: 10px;
                                "></i>


                            <div
                                style="
                                    font-weight: 700;
                                    font-size: 14px;
                                ">
                                คลิกเพื่อเลือกไฟล์รูปภาพสลิป
                            </div>


                            <span
                                style="
                                    font-size: 12px;
                                    color: #888;
                                ">
                                รองรับไฟล์ JPG, PNG, WEBP
                                (ขนาดไม่เกิน 5MB)
                            </span>


                            <input type="file" name="slip_image" id="slipInput" accept="image/jpeg,image/png,image/webp"
                                style="display: none;" onchange="previewSlip(event)" required>


                            <img id="slipPreviewImg" class="slip-preview" alt="ตัวอย่างสลิป">

                        </div>

                    </div>


                    {{-- SUBMIT PAYMENT --}}
                    <button type="submit" id="submitPaymentBtn" class="btn btn-primary btn-block"
                        style="
                            padding: 14px;
                            font-size: 16px;
                        ">

                        <i class="fa-solid fa-check-circle" id="submitIcon"></i>

                        <span id="submitText">
                            ยืนยันการชำระเงิน & ส่งสลิป
                        </span>

                    </button>

                </form>


                {{-- =================================================
                     CANCEL RENTAL
                     อยู่ใต้ปุ่มชำระเงิน
                ================================================== --}}
                <div class="cancel-rental-box" id="cancelRentalSection">

                    <div class="cancel-rental-content">

                        <div class="cancel-rental-icon">
                            <i class="fa-solid fa-ban"></i>
                        </div>


                        <div class="cancel-rental-text">

                            <h3>
                                ไม่ต้องการทำรายการนี้แล้ว?
                            </h3>

                            <p>
                                รายการยังไม่ได้ชำระเงิน
                                สามารถยกเลิกได้ทันที
                                และระบบจะคืนจำนวนชุดกลับเข้าสต็อก
                            </p>

                        </div>


                        <button type="button" class="cancel-rental-btn" onclick="openCancelRentalModal()">
                            <i class="fa-solid fa-ban"></i>
                            ยกเลิกการเช่า
                        </button>

                    </div>

                </div>
            @else
                {{-- STATUS AFTER PAYMENT --}}
                <div
                    style="
                        padding: 20px;
                        background: #faf8f5;
                        border: 1px solid var(--border);
                        border-radius: 12px;
                        text-align: center;
                    ">

                    <div
                        style="
                            font-size: 42px;
                            color: var(--primary);
                            margin-bottom: 10px;
                        ">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>


                    <h3
                        style="
                            font-size: 18px;
                            font-weight: 800;
                            margin-bottom: 6px;
                        ">
                        รายการนี้ไม่อยู่ในขั้นตอนการชำระเงินแล้ว
                    </h3>


                    <p
                        style="
                            margin: 0;
                            color: var(--text-muted);
                            font-size: 13px;
                        ">
                        กรุณากลับไปดูรายละเอียดสถานะการเช่า
                    </p>


                    <a href="{{ route('rentals.show', $rental->rental_id) }}" class="btn btn-primary"
                        style="margin-top: 16px;">
                        ดูรายละเอียดการเช่า
                    </a>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
         CANCEL MODAL
    ========================================================= --}}
    @if ($rental->status === 'pending_payment')
        <div id="cancelRentalModal" class="cancel-modal" aria-hidden="true">

            <div class="cancel-modal-backdrop"></div>


            <div class="cancel-modal-card" role="dialog" aria-modal="true" aria-labelledby="cancelModalTitle">

                {{-- HEADER --}}
                <div class="cancel-modal-header">

                    <div class="cancel-modal-title-wrap">

                        <div class="cancel-modal-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>


                        <div>

                            <h3 id="cancelModalTitle">
                                ยืนยันการยกเลิก
                            </h3>

                            <p>
                                {{ $rental->formatted_code }}
                            </p>

                        </div>

                    </div>


                    <button type="button" class="cancel-modal-close" onclick="closeCancelRentalModal()"
                        aria-label="ปิด">
                        &times;
                    </button>

                </div>


                {{-- CANCEL FORM --}}
                <form action="{{ route('rentals.cancel', $rental->rental_id) }}" method="POST">

                    @csrf


                    <div class="cancel-modal-body">

                        <div class="cancel-warning">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <div>

                                <strong>
                                    คุณกำลังจะยกเลิกรายการเช่านี้
                                </strong>

                                <p>
                                    รายการนี้ยังไม่ได้ชำระเงิน
                                    เมื่อยืนยันแล้ว
                                    สถานะจะเปลี่ยนเป็น
                                    <strong>ยกเลิกแล้ว</strong>
                                    และระบบจะคืนจำนวนชุดกลับเข้าสต็อก
                                </p>

                            </div>

                        </div>


                        {{-- REASON --}}
                        <div class="cancel-form-group">

                            <label for="cancel_reason_select">

                                เหตุผลในการยกเลิก

                                <span>*</span>

                            </label>


                            <select id="cancel_reason_select" name="cancel_reason_select"
                                onchange="handleCancelReasonChange()">

                                <option value="">
                                    -- เลือกเหตุผล --
                                </option>

                                <option value="เปลี่ยนใจ">
                                    เปลี่ยนใจ
                                </option>

                                <option value="ไม่สะดวกใช้ชุดแล้ว">
                                    ไม่สะดวกใช้ชุดแล้ว
                                </option>

                                <option value="เปลี่ยนวันเช่า">
                                    ต้องการเปลี่ยนวันเช่า
                                </option>

                                <option value="พบชุดที่ต้องการใหม่">
                                    พบชุดที่ต้องการใหม่
                                </option>

                                <option value="อื่น ๆ">
                                    อื่น ๆ
                                </option>

                            </select>


                            <textarea name="cancel_reason" id="cancel_reason" class="cancel-reason-textarea" rows="4"
                                placeholder="ระบุเหตุผลเพิ่มเติม..." required></textarea>

                        </div>

                    </div>


                    {{-- FOOTER --}}
                    <div class="cancel-modal-footer">

                        <button type="button" class="cancel-back-btn" onclick="closeCancelRentalModal()">
                            ย้อนกลับ
                        </button>


                        <button type="submit" class="cancel-confirm-btn" onclick="return confirmCancelRental()">

                            <i class="fa-solid fa-ban"></i>

                            ยืนยันการยกเลิก

                        </button>

                    </div>

                </form>

            </div>

        </div>
    @endif


    @push('scripts')
        <script>
            /* =====================================================
                       PAYMENT METHOD
                    ====================================================== */

            function selectMethod(labelElem) {

                const radio =
                    labelElem.querySelector(
                        'input[type="radio"]'
                    );

                if (!radio) {
                    return;
                }

                radio.checked = true;

                document
                    .querySelectorAll('.method-label')
                    .forEach(function(label) {
                        label.classList.remove('selected');
                    });

                labelElem.classList.add('selected');

                showPaymentDetail(
                    radio.value
                );
            }


            function showPaymentDetail(method) {

                document
                    .querySelectorAll('.payment-detail')
                    .forEach(function(detail) {
                        detail.classList.remove('active');
                    });


                const target =
                    document.getElementById(
                        'payment-detail-' + method
                    );


                if (target) {
                    target.classList.add('active');
                }


                const submitText =
                    document.getElementById(
                        'submitText'
                    );

                const submitIcon =
                    document.getElementById(
                        'submitIcon'
                    );


                if (!submitText || !submitIcon) {
                    return;
                }


                if (method === 'qr') {

                    submitText.textContent =
                        'ยืนยันการชำระเงิน & ส่งสลิป';

                    submitIcon.className =
                        'fa-solid fa-qrcode';

                } else if (method === 'transfer') {

                    submitText.textContent =
                        'ยืนยันการโอนเงิน & ส่งสลิป';

                    submitIcon.className =
                        'fa-solid fa-building-columns';

                }
            }


            /* =====================================================
               SLIP PREVIEW
            ====================================================== */

            function previewSlip(event) {

                const file =
                    event.target.files[0];

                if (!file) {
                    return;
                }


                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];


                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    alert(
                        'กรุณาเลือกไฟล์ JPG, PNG หรือ WEBP เท่านั้น'
                    );

                    event.target.value = '';

                    return;
                }


                if (
                    file.size >
                    5 * 1024 * 1024
                ) {

                    alert(
                        'ขนาดไฟล์ต้องไม่เกิน 5MB'
                    );

                    event.target.value = '';

                    return;
                }


                const preview =
                    document.getElementById(
                        'slipPreviewImg'
                    );


                if (!preview) {
                    return;
                }


                preview.src =
                    URL.createObjectURL(
                        file
                    );

                preview.style.display =
                    'block';
            }


            /* =====================================================
               CANCEL MODAL
            ====================================================== */

            function openCancelRentalModal() {

                const modal =
                    document.getElementById(
                        'cancelRentalModal'
                    );


                if (!modal) {
                    return;
                }


                modal.style.display =
                    'flex';


                modal.setAttribute(
                    'aria-hidden',
                    'false'
                );


                document.body.style.overflow =
                    'hidden';
            }


            function closeCancelRentalModal() {

                const modal =
                    document.getElementById(
                        'cancelRentalModal'
                    );


                if (!modal) {
                    return;
                }


                modal.style.display =
                    'none';


                modal.setAttribute(
                    'aria-hidden',
                    'true'
                );


                document.body.style.overflow =
                    '';
            }


            function handleCancelReasonChange() {

                const select =
                    document.getElementById(
                        'cancel_reason_select'
                    );


                const textarea =
                    document.getElementById(
                        'cancel_reason'
                    );


                if (!select || !textarea) {
                    return;
                }


                if (
                    select.value ===
                    'อื่น ๆ'
                ) {

                    textarea.value = '';

                    textarea.placeholder =
                        'กรุณาระบุเหตุผลในการยกเลิก...';

                    textarea.focus();

                    return;
                }


                textarea.value =
                    select.value;


                textarea.placeholder =
                    select.value !== '' ?
                    'สามารถแก้ไขหรือระบุรายละเอียดเพิ่มเติมได้' :
                    'ระบุเหตุผลเพิ่มเติม...';
            }


            function confirmCancelRental() {

                const reason =
                    document.getElementById(
                        'cancel_reason'
                    );


                if (
                    !reason ||
                    !reason.value.trim()
                ) {

                    alert(
                        'กรุณาระบุเหตุผลในการยกเลิก'
                    );


                    if (reason) {
                        reason.focus();
                    }


                    return false;
                }


                return confirm(
                    'ยืนยันการยกเลิกรายการเช่านี้ใช่หรือไม่?\n\n' +
                    'รายการนี้ยังไม่ได้ชำระเงิน\n' +
                    'เมื่อยืนยันแล้ว สถานะจะเปลี่ยนเป็น "ยกเลิกแล้ว" และระบบจะคืนชุดเข้าสต็อก'
                );
            }


            /* =====================================================
               PAGE READY
            ====================================================== */

            document.addEventListener(
                'DOMContentLoaded',
                function() {

                    const checked =
                        document.querySelector(
                            'input[name="payment_method"]:checked'
                        );


                    if (checked) {

                        showPaymentDetail(
                            checked.value
                        );
                    }


                    /* ESC ปิด Modal */
                    document.addEventListener(
                        'keydown',
                        function(event) {

                            if (
                                event.key ===
                                'Escape'
                            ) {

                                closeCancelRentalModal();
                            }
                        }
                    );


                    /* คลิกพื้นหลัง Modal */
                    document.addEventListener(
                        'click',
                        function(event) {

                            const modal =
                                document.getElementById(
                                    'cancelRentalModal'
                                );


                            if (!modal) {
                                return;
                            }


                            const backdrop =
                                modal.querySelector(
                                    '.cancel-modal-backdrop'
                                );


                            if (
                                backdrop &&
                                event.target === backdrop
                            ) {

                                closeCancelRentalModal();
                            }
                        }
                    );

                }
            );
        </script>
    @endpush

@endsection
