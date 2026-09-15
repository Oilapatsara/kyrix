@extends('layouts.owner')

@section('title', 'เพิ่มชุดใหม่ | KYRIX Admin')

@push('styles')
<!-- โหลด SweetAlert2 สำหรับป๊อปอัปแจ้งเตือนสุดหรู -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        max-width: 900px;
        margin: 0 auto;
    }

    .admin-header {
        margin-bottom: 24px;
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
        font-family: 'Prompt', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--maroon-900);
    }

    .admin-heading p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .content-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 3px 18px rgba(111, 26, 43, .04);
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--maroon-900);
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid var(--line);
        border-radius: 10px;
        font-size: 13.5px;
        color: var(--ink);
        background: #fff;
        outline: none;
        transition: .2s;
    }

    .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(199, 154, 92, 0.15);
    }

    textarea.form-control {
        height: auto;
        padding: 12px 14px;
        resize: vertical;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .admin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 44px;
        padding: 0 24px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13.5px;
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

    .admin-btn.secondary {
        background: #fff;
        color: var(--muted);
        border-color: var(--line);
    }

    .admin-btn.secondary:hover {
        background: var(--cream);
        color: var(--ink);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--line);
    }

    .error-feedback {
        color: #991b1b;
        font-size: 11.5px;
        margin-top: 5px;
    }

    /* ---------- ส่วนที่เพิ่มใหม่ ---------- */

    /* หัวข้อคั่นแต่ละบล็อก */
    .block-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 32px 0 16px;
        padding-top: 22px;
        border-top: 1px solid var(--line);
        font-size: 14px;
        font-weight: 700;
        color: var(--maroon-900);
    }

    .block-title i {
        color: var(--gold-dark);
        font-size: 14px;
    }

    .block-title small {
        font-weight: 500;
        color: var(--muted);
        font-size: 12px;
    }

    /* เลือกไซซ์ */
    .chip-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .chip {
        position: relative;
        cursor: pointer;
    }

    .chip input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .chip span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 58px;
        height: 42px;
        padding: 0 16px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #fff;
        color: var(--ink);
        font-size: 13.5px;
        font-weight: 650;
        transition: .18s ease;
    }

    .chip:hover span {
        border-color: var(--gold);
        color: var(--maroon-800);
    }

    .chip input:checked + span {
        background: var(--maroon-800);
        border-color: var(--maroon-800);
        color: #fff;
        box-shadow: 0 5px 12px rgba(92, 21, 34, .22);
    }

    .chip input:focus-visible + span {
        box-shadow: 0 0 0 3px rgba(199, 154, 92, .35);
    }

    /* ตารางสัดส่วน */
    .measure-box {
        background: var(--rose-bg);
        border: 1px solid #f0d7dc;
        border-radius: 14px;
        padding: 20px;
    }

    .measure-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    .measure-item {
        background: #fff;
        border: 1px solid #f3dfe3;
        border-radius: 10px;
        padding: 12px;
    }

    .measure-item label {
        display: block;
        font-size: 12px;
        font-weight: 650;
        color: var(--rose-text);
        margin-bottom: 8px;
    }

    .measure-item .form-control {
        height: 38px;
        font-size: 13px;
        text-align: center;
        border-color: #f0d7dc;
    }

    .measure-note {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
    }

    .measure-note .form-control {
        height: 38px;
        font-size: 12.5px;
        border-color: #f0d7dc;
        color: var(--rose-text);
    }

    /* สีของชุด */
    .color-row {
        display: grid;
        grid-template-columns: 56px 1fr 44px;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
    }

    .color-row input[type="color"] {
        width: 100%;
        height: 44px;
        padding: 4px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
    }

    .icon-btn {
        height: 44px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #fff;
        color: var(--muted);
        cursor: pointer;
        transition: .18s ease;
    }

    .icon-btn:hover {
        background: var(--rose-bg);
        border-color: #f0d7dc;
        color: var(--rose-text);
    }

    .add-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
        background: none;
        border: none;
        padding: 0;
        color: var(--gold-dark);
        font-size: 12.5px;
        font-weight: 650;
        cursor: pointer;
    }

    .add-link:hover {
        color: var(--maroon-700);
    }

    @media (max-width: 720px) {
        .form-row,
        .measure-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="kyrix-admin-container">

    <!-- HEADER -->
    <div class="admin-header">
        <div class="admin-heading">
            <span class="eyebrow">KYRIX RENTAL · CATALOG</span>
            <h1>เพิ่มชุดใหม่</h1>
            <p>กรอกรายละเอียดข้อมูลชุด ราคา สต็อก ไซซ์ สี สัดส่วน และอัปโหลดรูปภาพเพื่อเพิ่มลงในคลัง</p>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="content-card">
        <form id="createDressForm" action="{{ route('owner.dresses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <!-- รหัสชุด -->
                <div class="form-group">
                    <label class="form-label">รหัสชุด (Product Code) <span style="color: #991b1b;">*</span></label>
                    <input type="text" name="product_code" value="{{ old('product_code', $defaultCode ?? '') }}" required class="form-control" placeholder="เช่น KY-DRS-0001">
                    @error('product_code')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- หมวดหมู่ชุด -->
                <div class="form-group">
                    <label class="form-label">หมวดหมู่ชุด <span style="color: #991b1b;">*</span></label>
                    <select name="category_id" required class="form-control">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        @foreach($categories as $category)
                            @php 
                                $catId = $category->category_id ?? $category->id;
                            @endphp
                            <option value="{{ $catId }}" {{ old('category_id') == $catId ? 'selected' : '' }}>
                                {{ $category->name ?? $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- ชื่อชุด -->
            <div class="form-group">
                <label class="form-label">ชื่อชุด / ชื่อสินค้า <span style="color: #991b1b;">*</span></label>
                <input type="text" name="product_name" value="{{ old('product_name') }}" required class="form-control" placeholder="ระบุชื่อชุด เช่น ชุดราตรีสั้นสีทองหรูหรา">
                @error('product_name')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <!-- ราคาเช่า -->
                <div class="form-group">
                    <label class="form-label">ราคาเช่า (บาท) <span style="color: #991b1b;">*</span></label>
                    <input type="number" step="0.01" name="rental_price" value="{{ old('rental_price') }}" required class="form-control" placeholder="0.00">
                    @error('rental_price')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- เงินมัดจำ -->
                <div class="form-group">
                    <label class="form-label">เงินมัดจำ (บาท) <span style="color: #991b1b;">*</span></label>
                    <input type="number" step="0.01" name="deposit" value="{{ old('deposit', 0) }}" required class="form-control" placeholder="0.00">
                    @error('deposit')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- จำนวนสต็อก -->
                <div class="form-group">
                    <label class="form-label">จำนวนในสต็อก (Stock) <span style="color: #991b1b;">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 1) }}" required class="form-control" placeholder="1">
                    @error('stock')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- สถานะสินค้า -->
                <div class="form-group">
                    <label class="form-label">สถานะชุด <span style="color: #991b1b;">*</span></label>
                    <select name="status" required class="form-control">
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>พร้อมให้เช่า (Available)</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ปิดใช้งาน (Inactive)</option>
                    </select>
                    @error('status')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- ======================= ไซซ์และสี ======================= -->
            <div class="block-title">
                <i class="fa-solid fa-shirt"></i>
                ไซซ์และสีที่มีให้เช่า <small>ลูกค้าจะเห็นเป็นตัวเลือกในหน้ารายละเอียดชุด</small>
            </div>

            <!-- เลือกไซซ์ -->
            <div class="form-group">
                <label class="form-label">เลือกไซซ์ (Size) <span style="color: #991b1b;">*</span></label>
                @php
                    $sizeOptions  = ['S', 'M', 'L', 'XL', 'Free Size'];
                    $selectedSize = old('sizes', ['S']);
                @endphp
                <div class="chip-group">
                    @foreach($sizeOptions as $size)
                        <label class="chip">
                            <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, (array) $selectedSize) ? 'checked' : '' }}>
                            <span>{{ $size }}</span>
                        </label>
                    @endforeach
                </div>
                @error('sizes')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- เลือกสี -->
            <div class="form-group">
                <label class="form-label">เลือกสี (Color) <span style="color: #991b1b;">*</span></label>
                <div id="colorList">
                    @php
                        $oldColorNames = old('color_names', ['แดงเบอร์กันดี', 'น้ำเงินมิดไนท์บลู', 'เขียวเอเมอรัลด์']);
                        $oldColorHexes = old('color_hexes', ['#6f1a2b', '#1b2a5e', '#0f6b52']);
                    @endphp
                    @foreach((array) $oldColorNames as $i => $colorName)
                        <div class="color-row">
                            <input type="color" name="color_hexes[]" value="{{ $oldColorHexes[$i] ?? '#6f1a2b' }}">
                            <input type="text" name="color_names[]" class="form-control" value="{{ $colorName }}" placeholder="ชื่อสี เช่น แดงเบอร์กันดี">
                            <button type="button" class="icon-btn" onclick="removeColor(this)" title="ลบสีนี้">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="add-link" onclick="addColor()">
                    <i class="fa-solid fa-plus"></i> เพิ่มสีอีกหนึ่งรายการ
                </button>
                @error('color_names')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- ======================= ตารางสัดส่วน ======================= -->
            <div class="block-title">
                <i class="fa-solid fa-ruler"></i>
                ตารางสัดส่วนชุด (Measurements Guide) <small>หน่วยเป็นนิ้ว ยกเว้นความยาวเป็นเซนติเมตร</small>
            </div>

            <div class="form-group">
                <div class="measure-box">
                    <div class="measure-grid">
                        <div class="measure-item">
                            <label>รอบอก (Bust)</label>
                            <input type="text" name="bust" value="{{ old('bust') }}" class="form-control" placeholder="32-34 นิ้ว">
                        </div>
                        <div class="measure-item">
                            <label>รอบเอว (Waist)</label>
                            <input type="text" name="waist" value="{{ old('waist') }}" class="form-control" placeholder="25-27 นิ้ว">
                        </div>
                        <div class="measure-item">
                            <label>สะโพก (Hips)</label>
                            <input type="text" name="hips" value="{{ old('hips') }}" class="form-control" placeholder="35-37 นิ้ว">
                        </div>
                        <div class="measure-item">
                            <label>ความยาว (Length)</label>
                            <input type="text" name="length" value="{{ old('length') }}" class="form-control" placeholder="145 ซม.">
                        </div>
                    </div>

                    <div class="measure-note">
                        <span style="color: var(--gold-dark); font-weight: 700;">*</span>
                        <input type="text" name="measurement_note" class="form-control"
                               value="{{ old('measurement_note', 'ทางร้านมีบริการปรับแก้ทรงชั่วคราวให้พอดีสัดส่วนฟรี โดยไม่ทำให้ผ้าเสียหาย') }}"
                               placeholder="หมายเหตุที่จะแสดงใต้ตารางสัดส่วน">
                    </div>
                </div>
                @error('bust')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- ======================= รูปภาพและรายละเอียด ======================= -->
            <div class="block-title">
                <i class="fa-solid fa-image"></i>
                รูปภาพและรายละเอียด
            </div>

            <!-- รูปภาพชุด -->
            <div class="form-group">
                <label class="form-label">รูปภาพชุด (อัปโหลดรูปภาพ)</label>
                <input type="file" name="image" accept="image/*" class="form-control" style="padding-top: 8px;">
                @error('image')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- รายละเอียดเพิ่มเติม -->
            <div class="form-group">
                <label class="form-label">รายละเอียด / คำอธิบายชุด</label>
                <textarea name="description" rows="4" class="form-control" placeholder="ระบุรายละเอียดเนื้อผ้า ไซส์ หรือเงื่อนไขการเช่า...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- ACTIONS -->
            <div class="form-actions">
                <a href="{{ route('owner.dresses.index') }}" class="admin-btn secondary">
                    ยกเลิก
                </a>
                <button type="button" onclick="confirmCreate()" class="admin-btn primary">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกข้อมูลชุดใหม่
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // เพิ่มแถวสีใหม่
    function addColor() {
        const row = document.createElement('div');
        row.className = 'color-row';
        row.innerHTML = `
            <input type="color" name="color_hexes[]" value="#6f1a2b">
            <input type="text" name="color_names[]" class="form-control" placeholder="ชื่อสี เช่น แดงเบอร์กันดี">
            <button type="button" class="icon-btn" onclick="removeColor(this)" title="ลบสีนี้">
                <i class="fa-solid fa-xmark"></i>
            </button>`;
        document.getElementById('colorList').appendChild(row);
        row.querySelector('input[type="text"]').focus();
    }

    // ลบแถวสี (เหลืออย่างน้อย 1 แถว)
    function removeColor(btn) {
        const list = document.getElementById('colorList');
        if (list.children.length <= 1) {
            btn.closest('.color-row').querySelector('input[type="text"]').value = '';
            return;
        }
        btn.closest('.color-row').remove();
    }

    function confirmCreate() {
        const form  = document.getElementById('createDressForm');
        const sizes = form.querySelectorAll('input[name="sizes[]"]:checked').length;

        // ตรวจฟิลด์บังคับของเบราว์เซอร์ก่อน แล้วค่อยตรวจไซซ์
        if (!form.reportValidity()) return;

        if (sizes === 0) {
            Swal.fire({
                title: 'ยังไม่ได้เลือกไซซ์',
                text: 'เลือกอย่างน้อย 1 ไซซ์ก่อนบันทึกชุดนี้',
                icon: 'warning',
                confirmButtonColor: '#6f1a2b',
                confirmButtonText: 'กลับไปเลือกไซซ์'
            });
            return;
        }

        Swal.fire({
            title: 'ยืนยันการเพิ่มชุด?',
            text: "คุณต้องการบันทึกข้อมูลชุดใหม่นี้ลงในระบบใช่หรือไม่",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6f1a2b',
            cancelButtonColor: '#8a7a7d',
            confirmButtonText: 'ใช่, บันทึกเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังบันทึก...',
                    text: 'ระบบกำลังเพิ่มชุดใหม่เข้าคลัง',
                    icon: 'success',
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    form.submit();
                });
            }
        });
    }
</script>
@endsection