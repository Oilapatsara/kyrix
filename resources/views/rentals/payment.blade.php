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
        }

        .method-label:hover {
            border-color: var(--primary-light);
        }

        .method-label.selected {
            border-color: var(--primary);
            background: var(--primary-soft);
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
            width: 180px;
            height: 180px;
            margin: 0 auto 16px;
            display: block;
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 8px;
            background: #fff;
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

        .payment-method-info {
            margin-bottom: 24px;
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
        }
    </style>
@endpush

@section('content')

    @php
        $depositAmount = 100;
        $serviceFee = (float) ($rental->service_fee ?? 0);
        $rentalAmount = (float) ($rental->total_amount ?? 0);
        $grandTotal = $rentalAmount + $depositAmount + $serviceFee;
    @endphp

    <div class="payment-page-wrap">

        <div class="payment-header">

            <a href="{{ route('rentals.index') }}"
                style="font-size: 13px; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px; margin-bottom: 10px;">
                <i class="fa-solid fa-arrow-left"></i>
                กลับไปหน้ารายการการเช่า
            </a>

            <h1>
                <i class="fa-solid fa-credit-card" style="color: var(--primary);"></i>
                <span>ชำระเงินค่าเช่าชุด ({{ $rental->formatted_code }})</span>
            </h1>

            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
                กรุณาเลือกวิธีการชำระเงินและอัปโหลดสลิปหลักฐานเพื่อยืนยันการจอง
            </p>

        </div>

        <div class="status-note-box">
            <strong>
                <i class="fa-solid fa-circle-info"></i>
                ขั้นตอนการตรวจสอบยอดชำระ:
            </strong>
            <br>
            เมื่อแนบสลิปแล้ว สถานะจะเป็น <strong>"รอตรวจสอบ"</strong>
            &rarr;
            เจ้าหน้าที่จะตรวจสอบสลิปภายใน 15-30 นาที
            และเปลี่ยนเป็น <strong>"อนุมัติแล้ว"</strong>
            หากสลิปไม่ถูกต้องจะแจ้งเป็น <strong>"ถูกปฏิเสธ"</strong>
        </div>

        <div class="payment-card">

            <div class="amount-summary-box">

                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 14px; color: var(--text-main);">
                    สรุปยอดเงินสำหรับใบสั่งเช่า: {{ $rental->formatted_code }}
                </h3>

                <div style="margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px dashed var(--border);">

                    @foreach ($rental->details as $detail)
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 14px; gap: 15px;">

                            <span>
                                <strong style="color: var(--primary);">
                                    {{ $detail->product->product_name ?? 'ชุดเช่า' }}
                                </strong>

                                <span style="color: var(--text-muted); font-size: 12px;">
                                    ({{ $detail->quantity }} ชุด x {{ $detail->rental_days }} วัน)
                                </span>
                            </span>

                            <span>
                                ฿{{ number_format($detail->subtotal) }}
                            </span>

                        </div>
                    @endforeach

                </div>

                <div class="amount-row">
                    <span>ค่าเช่าชุดเต็มจำนวน (100%):</span>
                    <strong>฿{{ number_format($rentalAmount) }}</strong>
                </div>

                <div class="amount-row">
                    <span>เงินมัดจำประกันชุด:</span>
                    <strong style="color: #b45309;">
                        ฿{{ number_format($depositAmount) }}
                    </strong>
                </div>

                @if ($serviceFee > 0)
                    <div class="amount-row">
                        <span>ค่าบริการเพิ่มเติม:</span>
                        <strong>
                            ฿{{ number_format($serviceFee) }}
                        </strong>
                    </div>
                @endif

                <div class="amount-row total">
                    <span>ยอดที่ต้องชำระสุทธิ:</span>
                    <span>
                        ฿{{ number_format($grandTotal) }}
                    </span>
                </div>

                <div class="payment-info-box payment-info-success">

                    <i class="fa-solid fa-shield-halved"></i>

                    <strong>
                        เงินมัดจำประกันชุด ฿{{ number_format($depositAmount) }}
                    </strong>
                    เท่านั้นที่จะได้รับคืน 100% ในวันที่ส่งคืนชุด
                    หากเจ้าหน้าที่ตรวจสภาพแล้วชุดไม่มีการเสียหาย

                    <br>

                    ค่าเช่าชุด
                    <strong>฿{{ number_format($rentalAmount) }}</strong>
                    เป็นค่าใช้จ่ายในการเช่าและ
                    <strong>จะไม่ได้รับคืน</strong>

                    <br>

                    กรณีชุดเสียหาย ทางร้านขอสงวนสิทธิ์ไม่คืนเงินมัดจำ

                </div>

            </div>

            <form action="{{ route('rentals.payment.submit', $rental->rental_id) }}" method="POST"
                enctype="multipart/form-data">

                @csrf

                <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 12px;">
                    เลือกช่องทางชำระเงิน:
                </h4>

                <div class="method-grid">

                    <label class="method-label selected" onclick="selectMethod(this)">

                        <input type="radio" name="payment_method" value="qr" checked
                            style="accent-color: var(--primary);">

                        <div>
                            <strong style="display: block; font-size: 14px;">
                                QR Code PromptPay
                            </strong>

                            <span style="font-size: 12px; color: var(--text-muted);">
                                สแกนจ่ายง่ายผ่านทุกแอปธนาคาร
                            </span>
                        </div>

                    </label>

                    <label class="method-label" onclick="selectMethod(this)">

                        <input type="radio" name="payment_method" value="transfer" style="accent-color: var(--primary);">

                        <div>
                            <strong style="display: block; font-size: 14px;">
                                โอนเงินผ่านบัญชีธนาคาร
                            </strong>

                            <span style="font-size: 12px; color: var(--text-muted);">
                                ธ.ออมสิน
                            </span>
                        </div>

                    </label>

                    <label class="method-label" onclick="selectMethod(this)">

                        <input type="radio" name="payment_method" value="cash" style="accent-color: var(--primary);">

                        <div>
                            <strong style="display: block; font-size: 14px;">
                                ชำระเงินสดที่หน้าร้าน
                            </strong>

                            <span style="font-size: 12px; color: var(--text-muted);">
                                ชำระตอนมารับชุดที่ร้านทองหล่อ
                            </span>
                        </div>

                    </label>

                    <label class="method-label" onclick="selectMethod(this)">

                        <input type="radio" name="payment_method" value="other" style="accent-color: var(--primary);">

                        <div>
                            <strong style="display: block; font-size: 14px;">
                                ช่องทางอื่นๆ
                            </strong>

                            <span style="font-size: 12px; color: var(--text-muted);">
                                บัตรเครดิต หรือ TrueMoney
                            </span>
                        </div>

                    </label>

                </div>

                <div class="payment-method-info">

                    <div class="bank-box">

                        <img src="{{ asset('images/qr/promptpay-qr.jpg') }}" class="qr-img" alt="PromptPay QR">

                        <div style="font-size: 14px;">

                            <div>
                                <strong>ยอดชำระ:</strong>
                                ฿{{ number_format($grandTotal) }}
                            </div>

                            <div>
                                <strong>ธนาคาร:</strong>
                                ออมสิน
                            </div>

                            <div>
                                <strong>เลขที่บัญชี:</strong>
                                <span
                                    style="font-family: monospace; font-size: 16px; color: var(--primary); font-weight: 700;">
                                    020310925126
                                </span>
                            </div>

                            <div>
                                <strong>ชื่อบัญชี:</strong>
                                บจก. ไคริกซ์ เดรส เรนทอล (KYRIX)
                            </div>

                        </div>

                    </div>

                </div>

                <div style="margin-bottom: 24px;">

                    <label style="font-size: 15px; font-weight: 700; display: block; margin-bottom: 6px;">
                        อัปโหลดสลิปหลักฐานการโอนเงิน (Slip Image) *
                    </label>

                    <div class="slip-box" onclick="document.getElementById('slipInput').click();">

                        <i class="fa-solid fa-cloud-arrow-up"
                            style="font-size: 36px; color: var(--primary); margin-bottom: 10px;">
                        </i>

                        <div style="font-weight: 700; font-size: 14px;">
                            คลิกเพื่อเลือกไฟล์รูปภาพสลิป
                        </div>

                        <span style="font-size: 12px; color: #888;">
                            รองรับไฟล์ JPG, PNG, WEBP (ขนาดไม่เกิน 5MB)
                        </span>

                        <input type="file" name="slip_image" id="slipInput" accept="image/jpeg,image/png,image/webp"
                            style="display: none;" onchange="previewSlip(event)" required>

                        <img id="slipPreviewImg" class="slip-preview" alt="ตัวอย่างสลิป">

                    </div>

                </div>


                <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                    <i class="fa-solid fa-check-circle"></i>
                    ยืนยันการชำระเงิน & ส่งสลิป
                </button>

            </form>

        </div>

    </div>

    @push('scripts')
        <script>
            function selectMethod(labelElem) {
                document.querySelectorAll('.method-label').forEach(function(label) {
                    label.classList.remove('selected');
                });

                labelElem.classList.add('selected');
            }

            function previewSlip(event) {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!allowedTypes.includes(file.type)) {
                    alert('กรุณาเลือกไฟล์ JPG, PNG หรือ WEBP เท่านั้น');
                    event.target.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('ขนาดไฟล์ต้องไม่เกิน 5MB');
                    event.target.value = '';
                    return;
                }

                const preview = document.getElementById('slipPreviewImg');

                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        </script>
    @endpush

@endsection
