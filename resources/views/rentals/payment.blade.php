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

    /* Method Radios */
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
        align-items: center;
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

    /* QR / Bank Info */
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

    /* Slip Upload Box */
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

    /* Payment Status Timeline */
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

    @media (max-width: 600px) {
        .method-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="payment-page-wrap">
    <div class="payment-header">
        <a href="{{ route('rentals.index') }}" style="font-size: 13px; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px; margin-bottom: 10px;">
            <i class="fa-solid fa-arrow-left"></i> กลับไปหน้ารายการการเช่า
        </a>
        <h1>
            <i class="fa-solid fa-credit-card" style="color: var(--primary);"></i>
            <span>ชำระเงินค่าเช่าชุด ({{ $rental->formatted_code }})</span>
        </h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
            กรุณาเลือกวิธีการชำระเงินและอัปโหลดสลิปหลักฐานเพื่อยืนยันการจอง
        </p>
    </div>

    <!-- Status Process Note -->
    <div class="status-note-box">
        <strong><i class="fa-solid fa-circle-info"></i> ขั้นตอนการตรวจสอบยอดชำระ:</strong><br>
        เมื่อแนบสลิปแล้ว สถานะจะเป็น <strong>"รอตรวจสอบ"</strong> &rarr; เจ้าหน้าที่จะตรวจสอบสลิปภายใน 15-30 นาที และเปลี่ยนเป็น <strong>"อนุมัติแล้ว"</strong> (หากสลิปไม่ถูกต้องจะแจ้งเป็น <strong>"ถูกปฏิเสธ"</strong>)
    </div>

    <div class="payment-card">
        <!-- 1. Amount Summary -->
        <div class="amount-summary-box">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 14px; color: var(--text-main);">
                สรุปยอดเงินสำหรับใบสั่งเช่า: {{ $rental->formatted_code }}
            </h3>

            <!-- Dress summary -->
            <div style="margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px dashed var(--border);">
                @foreach($rental->details as $detail)
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 14px;">
                        <span>
                            <strong style="color: var(--primary);">{{ $detail->product->product_name ?? 'ชุดเช่า' }}</strong>
                            <span style="color: var(--text-muted); font-size: 12px;">({{ $detail->quantity }} ชุด x {{ $detail->rental_days }} วัน)</span>
                        </span>
                        <span>฿{{ number_format($detail->subtotal) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="amount-row">
                <span>ยอดค่าเช่าชุด:</span>
                <strong>฿{{ number_format($rental->total_amount) }}</strong>
            </div>

            <div class="amount-row">
                <span>เงินมัดจำประกันชุด (ได้รับคืนเมื่อคืนชุด):</span>
                <strong style="color: #b45309;">฿{{ number_format($rental->deposit_amount) }}</strong>
            </div>

            <div class="amount-row total">
                <span>ยอดที่ต้องชำระสุทธิ:</span>
                <span>฿{{ number_format($rental->grand_total) }}</span>
            </div>
        </div>

        <!-- 2. Payment Form -->
        <form action="{{ route('rentals.payment.submit', $rental->rental_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Payment Method Selection -->
            <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 12px;">เลือกช่องทางชำระเงิน:</h4>
            <div class="method-grid">
                <label class="method-label selected" onclick="selectMethod('qr', this)">
                    <input type="radio" name="payment_method" value="qr" checked style="accent-color: var(--primary);">
                    <div>
                        <strong style="display: block; font-size: 14px;">QR Code PromptPay</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">สแกนจ่ายง่ายผ่านทุกแอปธนาคาร</span>
                    </div>
                </label>

                <label class="method-label" onclick="selectMethod('transfer', this)">
                    <input type="radio" name="payment_method" value="transfer" style="accent-color: var(--primary);">
                    <div>
                        <strong style="display: block; font-size: 14px;">โอนเงินผ่านบัญชีธนาคาร</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">ธ.ออมสิน</span>
                    </div>
                </label>

                <label class="method-label" onclick="selectMethod('cash', this)">
                    <input type="radio" name="payment_method" value="cash" style="accent-color: var(--primary);">
                    <div>
                        <strong style="display: block; font-size: 14px;">ชำระเงินสดที่หน้าร้าน</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">ชำระตอนมารับชุดที่ร้านทองหล่อ</span>
                    </div>
                </label>

                <label class="method-label" onclick="selectMethod('other', this)">
                    <input type="radio" name="payment_method" value="other" style="accent-color: var(--primary);">
                    <div>
                        <strong style="display: block; font-size: 14px;">ช่องทางอื่นๆ</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">บัตรเครดิต หรือ TrueMoney</span>
                    </div>
                </label>
            </div>

            <!-- QR & Bank Details Box -->
            <div class="bank-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=00020101021129370016A000000677010111011300668912345675802TH5303764540{{ number_format($rental->grand_total, 2, '', '') }}6304" class="qr-img" alt="PromptPay QR">
                <div style="font-size: 14px;">
                    <div><strong>ธนาคาร:</strong> ออมสิน</div>
                    <div><strong>เลขที่บัญชี:</strong> <span style="font-family: monospace; font-size: 16px; color: var(--primary); font-weight: 700;">020310925126</span></div>
                    <div><strong>ชื่อบัญชี:</strong> บจก. ไคริกซ์ เดรส เรนทอล (KYRIX)</div>
                </div>
            </div>

            <!-- Slip Upload -->
            <div style="margin-bottom: 24px;">
                <label style="font-size: 15px; font-weight: 700; display: block; margin-bottom: 6px;">
                    อัปโหลดสลิปหลักฐานการโอนเงิน (Slip Image) *
                </label>
                <div class="slip-box" onclick="document.getElementById('slipInput').click();">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 36px; color: var(--primary); margin-bottom: 10px;"></i>
                    <div style="font-weight: 700; font-size: 14px;">คลิกเพื่อเลือกไฟล์รูปภาพสลิป</div>
                    <span style="font-size: 12px; color: #888;">รองรับไฟล์ JPG, PNG, WEBP (ขนาดไม่เกิน 5MB)</span>
                    <input type="file" name="slip_image" id="slipInput" accept="image/*" style="display: none;" required onchange="previewSlip(event)">
                    <img id="slipPreviewImg" class="slip-preview" alt="ตัวอย่างสลิป">
                </div>
            </div>

            <!-- Note -->
            <div style="margin-bottom: 24px;">
                <label style="font-size: 14px; font-weight: 600; display: block; margin-bottom: 6px;">หมายเหตุเพิ่มเติม (ถ้ามี):</label>
                <input type="text" name="note" class="input-field" placeholder="เช่น โอนจากบัญชีธนาคารกรุงเทพ เวลา 14:30 น.">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                <i class="fa-solid fa-check-circle"></i> ยืนยันการชำระเงิน & ส่งสลิป
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function selectMethod(method, labelElem) {
        document.querySelectorAll('.method-label').forEach(l => l.classList.remove('selected'));
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
