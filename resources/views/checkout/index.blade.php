@extends('layouts.customer')

@section('title', 'ชำระเงินค่าเช่าชุด | KYRIX')

@push('styles')
<style>
    .checkout-wrap {
        max-width: 1200px;
        margin: 40px auto 80px;
        padding: 0 24px;
    }
    .checkout-header {
        margin-bottom: 30px;
    }
    .checkout-header h1 {
        font-size: 28px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 32px;
        align-items: start;
    }

    .form-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 30px;
        margin-bottom: 24px;
    }
    .card-title {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--primary);
        border-bottom: 1px solid var(--border);
        padding-bottom: 12px;
    }

    .delivery-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    .delivery-card-label {
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .delivery-card-label:hover {
        border-color: var(--primary-light);
    }
    .delivery-card-label.selected {
        border-color: var(--primary);
        background: var(--primary-soft);
    }

    .input-field {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        margin-top: 6px;
    }
    .input-field:focus {
        outline: none;
        border-color: var(--primary);
    }

    /* Payment Methods */
    .payment-methods-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 24px;
    }
    .payment-method-card {
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .payment-method-card.selected {
        border-color: var(--primary);
        background: var(--primary-soft);
    }

    /* QR Code Display */
    .qr-payment-box {
        background: #faf8f5;
        border: 2px dashed var(--border);
        border-radius: var(--radius-md);
        padding: 24px;
        text-align: center;
        margin-bottom: 24px;
    }
    .qr-image-wrap {
        width: 190px;
        height: 190px;
        background: #fff;
        border-radius: 12px;
        padding: 10px;
        margin: 0 auto 16px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qr-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .bank-details {
        background: #fff;
        padding: 14px;
        border-radius: 8px;
        font-size: 13px;
        display: inline-block;
        border: 1px solid var(--border);
        text-align: left;
    }

    /* File Upload Box */
    .slip-upload-box {
        border: 2px dashed #d1c8c1;
        border-radius: var(--radius-md);
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #faf8f5;
        position: relative;
    }
    .slip-upload-box:hover {
        border-color: var(--primary);
        background: #fff;
    }
    .slip-preview {
        max-width: 220px;
        max-height: 250px;
        border-radius: 8px;
        margin: 12px auto 0;
        display: none;
        box-shadow: var(--shadow-sm);
    }

    /* Summary in Checkout */
    .order-summary-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 26px;
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 90px;
    }
    .checkout-item-thumb {
        width: 50px;
        height: 60px;
        border-radius: 6px;
        object-fit: cover;
    }

    @media (max-width: 992px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
        .delivery-options {
            grid-template-columns: 1fr;
        }
        .payment-methods-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="checkout-wrap">
    <div class="checkout-header">
        <h1>
            <i class="fa-solid fa-credit-card" style="color: var(--primary);"></i>
            <span>ชำระเงิน & ยืนยันการเช่าชุด</span>
        </h1>
        <p style="color: var(--text-muted); font-size: 14px;">กรุณาตรวจสอบข้อมูลการรับชุดและทำรายการชำระเงินเพื่อยืนยันการจอง</p>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data" id="checkoutForm">
        @csrf
        <div class="checkout-grid">
            <!-- Left Column: Delivery & Payment Details -->
            <div>
                <!-- 1. Delivery / Pickup -->
                <div class="form-card">
                    <h3 class="card-title">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>1. วิธีการรับชุด</span>
                    </h3>

                    <div class="delivery-options">
                        <label class="delivery-card-label selected" onclick="toggleDelivery('pickup', this)">
                            <input type="radio" name="delivery_method" value="pickup" checked style="accent-color: var(--primary);">
                            <div>
                                <strong style="font-size: 14px; display: block;">รับที่หน้าร้าน KYRIX</strong>
                                <span style="font-size: 12px; color: var(--text-muted);">สาขาโคราช (โคราช) ฟรีค่าส่ง</span>
                            </div>
                        </label>

                        <label class="delivery-card-label" onclick="toggleDelivery('delivery', this)">
                            <input type="radio" name="delivery_method" value="delivery" style="accent-color: var(--primary);">
                            <div>
                                <strong style="font-size: 14px; display: block;">จัดส่งถึงที่อยู่</strong>
                                <span style="font-size: 12px; color: var(--text-muted);">แมสเซนเจอร์ใน โคราช/ EMS ทั่วไทย</span>
                            </div>
                        </label>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="font-size: 14px; font-weight: 600;">เบอร์โทรศัพท์สำหรับติดต่อรับชุด *</label>
                        <input type="text" name="recipient_phone" class="input-field" value="{{ old('recipient_phone', $customer->phone) }}" placeholder="เช่น 089-123-4567" required>
                    </div>

                    <div id="addressBox" style="display: none; margin-bottom: 16px;">
                        <label style="font-size: 14px; font-weight: 600;">ที่อยู่สำหรับจัดส่งชุด *</label>
                        <textarea name="delivery_address" class="input-field" rows="3" placeholder="บ้านเลขที่, ถนน, แขวง/ตำบล, เขต/อำเภอ, จังหวัด, รหัสไปรษณีย์">{{ old('delivery_address', $customer->address) }}</textarea>
                    </div>

                    <div style="margin-top: 10px;">
                        <label style="font-size: 13px; color: var(--text-muted);">หมายเหตุเพิ่มเติมถึงร้าน (ถ้ามี)</label>
                        <input type="text" name="note" class="input-field" placeholder="เช่น ขอรับชุดช่วงเช้า, ปรับความยาวกระโปรง ฯลฯ">
                    </div>
                </div>

                <!-- 2. Payment Method -->
                <div class="form-card">
                    <h3 class="card-title">
                        <i class="fa-solid fa-wallet"></i>
                        <span>2. ช่องทางการชำระเงิน</span>
                    </h3>

                    <div class="payment-methods-grid">
                        <label class="payment-method-card selected" onclick="selectPayment('qr', this)">
                            <input type="radio" name="payment_method" value="qr" checked style="accent-color: var(--primary);">
                            <div>
                                <strong style="display: block; font-size: 14px;">QR Code PromptPay</strong>
                                <span style="font-size: 12px; color: var(--text-muted);">สแกนจ่ายได้ทุกแอปธนาคาร</span>
                            </div>
                        </label>

                        <label class="payment-method-card" onclick="selectPayment('transfer', this)">
                            <input type="radio" name="payment_method" value="transfer" style="accent-color: var(--primary);">
                            <div>
                                <strong style="display: block; font-size: 14px;">โอนผ่านบัญชีธนาคาร</strong>
                                <span style="font-size: 12px; color: var(--text-muted);">ธ.ออมสิน</span>
                            </div>
                        </label>
                    </div>

                    <!-- QR Payment Display Box -->
                    <div class="qr-payment-box">
                        <span class="badge badge-warning" style="margin-bottom: 12px; font-size: 12px;">
                            <i class="fa-solid fa-clock"></i> ยอดชำระเงินสุทธิ: ฿{{ number_format($grandTotal) }}
                        </span>

                        <div class="qr-image-wrap">
                            <!-- PromptPay QR Code sample SVG / API -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=00020101021129370016A000000677010111011300668912345675802TH5303764540{{ number_format($grandTotal, 2, '', '') }}6304" alt="PromptPay QR Code">
                        </div>

                        <div class="bank-details">
                            <div><strong>ธนาคาร:</strong> ออมสิน </div>
                            <div><strong>เลขที่บัญชี:</strong> 020-310925126</div>
                            <div><strong>ชื่อบัญชี:</strong> บจก. ไคริกซ์ เดรส เรนทอล (KYRIX)</div>
                        </div>
                    </div>

                    <!-- Slip Upload Section -->
                    <div>
                        <label style="font-size: 14px; font-weight: 700; display: block; margin-bottom: 6px;">
                            แนบสลิปหลักฐานการโอนเงิน (Slip Upload)
                        </label>
                        <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                            เมื่อโอนเงินแล้ว กรุณาแนบรูปภาพสลิปเพื่อให้เจ้าหน้าที่อนุมัติและเตรียมชุดทันที (สามารถแนบภายหลังได้ในหน้า การจองของฉัน)
                        </p>

                        <div class="slip-upload-box" onclick="document.getElementById('slipInput').click();">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--primary); margin-bottom: 8px;"></i>
                            <div style="font-weight: 600; font-size: 14px;">คลิกเพื่อเลือกไฟล์รูปภาพสลิปโอนเงิน</div>
                            <span style="font-size: 12px; color: #888;">รองรับไฟล์ JPG, PNG, WEBP (ขนาดไม่เกิน 5MB)</span>
                            <input type="file" name="slip_image" id="slipInput" accept="image/*" style="display: none;" onchange="previewSlip(event)">
                            <img id="slipPreviewImg" class="slip-preview" alt="ตัวอย่างสลิป">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary Breakdown -->
            <div class="order-summary-card">
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                    สรุปรายการเช่า ({{ count($cart) }} ชุด)
                </h3>

                <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
                    @foreach($cart as $item)
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <img src="{{ $item['image_url'] }}" class="checkout-item-thumb" alt="{{ $item['product_name'] }}">
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 700;">{{ Str::limit($item['product_name'], 28) }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    ไซซ์: {{ $item['size'] }} | สี: {{ $item['color'] }} ({{ $item['days'] }} วัน)
                                </div>
                            </div>
                            <div style="font-weight: 700; font-size: 14px; color: var(--primary);">
                                ฿{{ number_format($item['subtotal']) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="border-top: 1px solid var(--border); padding-top: 14px;">
                    @if(!empty($discountAmount) && $discountAmount > 0)
                        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; padding: 10px 12px; border-radius: 8px; margin-bottom: 14px;">
                            <div style="font-size: 12.5px; font-weight: 700; color: #166534; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-gift"></i> {{ $discountReason }}
                            </div>
                            <span style="font-size: 11px; color: #15803d;">ส่วนลด 20% สำหรับการเช่าครบ 3 รายการ หรือยอดเช่าครบ 2,000 บาท</span>
                        </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: var(--text-muted);">
                        <span>ค่าเช่าชุดรวม:</span>
                        <strong style="color: var(--text-main);">฿{{ number_format($rentalTotal) }}</strong>
                    </div>

                    @if(!empty($discountAmount) && $discountAmount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: #16a34a; font-weight: 600;">
                        <span><i class="fa-solid fa-tag"></i> ส่วนลดโปรโมชั่น (20%):</span>
                        <strong style="color: #16a34a;">-฿{{ number_format($discountAmount) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; color: var(--text-muted);">
                        <span>ค่าเช่าสุทธิหลังหักส่วนลด:</span>
                        <strong style="color: var(--text-main);">฿{{ number_format($netRentalTotal) }}</strong>
                    </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: var(--text-muted);">
                        <span>เงินมัดจำประกันชุด (ได้รับคืน):</span>
                        <strong style="color: #b45309;">฿{{ number_format($depositTotal) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; color: var(--text-muted);">
                        <span>บริการเสริม:</span>
                        <strong style="color: var(--text-main);">฿{{ number_format($serviceTotal) }}</strong>
                    </div>

                    <div style="background: #edfbf3; border: 1px solid #b7ecd0; padding: 10px 12px; border-radius: 8px; font-size: 12px; color: #166534; margin-bottom: 16px; line-height: 1.5;">
                        <i class="fa-solid fa-shield-check"></i> เงินมัดจำประกันชุด <strong>฿{{ number_format($depositTotal) }}</strong> จะได้รับคืนทันทีในวันที่คืนชุด หากเจ้าหน้าที่ตรวจสภาพแล้วชุดไม่มีการเสียหาย (กรณีชุดเสียหาย ทางร้านขอสงวนสิทธิ์ไม่คืนเงินมัดจำ)
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 22px; font-weight: 800; color: var(--primary); margin-bottom: 24px;">
                        <span>ยอดที่ต้องชำระทันที:</span>
                        <span>฿{{ number_format($grandTotal) }}</span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                        <i class="fa-solid fa-circle-check"></i> ยืนยันการชำระเงิน & การจอง
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleDelivery(type, labelElem) {
        document.querySelectorAll('.delivery-card-label').forEach(l => l.classList.remove('selected'));
        labelElem.classList.add('selected');

        const addressBox = document.getElementById('addressBox');
        if (type === 'delivery') {
            addressBox.style.display = 'block';
            addressBox.querySelector('textarea').required = true;
        } else {
            addressBox.style.display = 'none';
            addressBox.querySelector('textarea').required = false;
        }
    }

    function selectPayment(method, labelElem) {
        document.querySelectorAll('.payment-method-card').forEach(l => l.classList.remove('selected'));
        labelElem.classList.add('selected');
    }

    function previewSlip(event) {
        const file = event.target.files[0];
        if (file) {
            const preview = document.getElementById('slipPreviewImg');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    }
</script>
@endpush

@endsection
