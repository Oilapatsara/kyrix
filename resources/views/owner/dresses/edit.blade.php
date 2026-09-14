@extends('layouts.owner')

@section('title', 'แก้ไขข้อมูลชุด | KYRIX Admin')

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

    .current-image-preview {
        display: flex;
        align-items: center;
        gap: 15px;
        background: var(--cream);
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--line);
        margin-bottom: 12px;
    }

    .preview-thumb {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--line);
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
</style>
@endpush

@section('content')
<div class="kyrix-admin-container">

    <!-- HEADER -->
    <div class="admin-header">
        <div class="admin-heading">
            <span class="eyebrow">KYRIX RENTAL · CATALOG</span>
            <h1>แก้ไขข้อมูลชุด</h1>
            <p>อัปเดตรายละเอียด ราคา สต็อก รูปภาพ และข้อมูลจำเพาะของชุดภายในร้าน</p>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="content-card">
        @php
            $productId = $dress->product_id ?? $dress->id;
            
            // ดึงรูปภาพปัจจุบัน
            $currentImage = null;
            if (isset($dress->images) && $dress->images->count() > 0) {
                $currentImage = $dress->images->first()->image_path ?? $dress->images->first()->url ?? null;
            } elseif (!empty($dress->image)) {
                $currentImage = $dress->image;
            } elseif (!empty($dress->image_path)) {
                $currentImage = $dress->image_path;
            }
        @endphp

        <form id="editDressForm" action="{{ route('owner.dresses.update', $productId) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <!-- รหัสชุด -->
                <div class="form-group">
                    <label class="form-label">รหัสชุด (Product Code) <span style="color: #991b1b;">*</span></label>
                    <input type="text" name="product_code" value="{{ old('product_code', $dress->product_code) }}" required class="form-control" placeholder="เช่น KY-EVN-001">
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
                            <option value="{{ $catId }}" {{ (old('category_id', $dress->category_id) == $catId) ? 'selected' : '' }}>
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
                <input type="text" name="product_name" value="{{ old('product_name', $dress->product_name) }}" required class="form-control" placeholder="ระบุชื่อชุด เช่น ชุดราตรีสั้นสีทองหรูหรา">
                @error('product_name')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <!-- ราคาเช่า -->
                <div class="form-group">
                    <label class="form-label">ราคาเช่า (บาท) <span style="color: #991b1b;">*</span></label>
                    <input type="number" step="0.01" name="rental_price" value="{{ old('rental_price', $dress->rental_price) }}" required class="form-control" placeholder="0.00">
                    @error('rental_price')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- เงินมัดจำ -->
                <div class="form-group">
                    <label class="form-label">เงินมัดจำ (บาท) <span style="color: #991b1b;">*</span></label>
                    <input type="number" step="0.01" name="deposit" value="{{ old('deposit', $dress->deposit ?? 0) }}" required class="form-control" placeholder="0.00">
                    @error('deposit')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- จำนวนสต็อก -->
                <div class="form-group">
                    <label class="form-label">จำนวนในสต็อก (Stock) <span style="color: #991b1b;">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', $dress->stock ?? 1) }}" required class="form-control" placeholder="1">
                    @error('stock')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- สถานะสินค้า -->
                <div class="form-group">
                    <label class="form-label">สถานะชุด <span style="color: #991b1b;">*</span></label>
                    <select name="status" required class="form-control">
                        <option value="available" {{ (old('status', $dress->status) == 'available' || old('status', $dress->status) == 'active') ? 'selected' : '' }}>พร้อมให้เช่า (Available)</option>
                        <option value="rented" {{ (old('status', $dress->status) == 'rented') ? 'selected' : '' }}>กำลังเช่า (Rented)</option>
                        <option value="inactive" {{ (old('status', $dress->status) == 'inactive') ? 'selected' : '' }}>ปิดใช้งาน (Inactive)</option>
                    </select>
                    @error('status')
                        <div class="error-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- แก้ไขรูปภาพชุด -->
            <div class="form-group">
                <label class="form-label">รูปภาพชุด (อัปโหลดรูปใหม่เพื่อเปลี่ยนรูปเดิม)</label>
                @if($currentImage)
                    <div class="current-image-preview">
                        <img src="{{ Str::startsWith($currentImage, ['http://', 'https://']) ? $currentImage : asset('storage/' . ltrim($currentImage, '/')) }}" alt="รูปปัจจุบัน" class="preview-thumb">
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--maroon-900);">รูปภาพปัจจุบันในระบบ</div>
                            <div style="font-size: 11px; color: var(--muted);">หากต้องการเปลี่ยนรูป ให้เลือกไฟล์รูปภาพใหม่ด้านล่างนี้</div>
                        </div>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*" class="form-control" style="padding-top: 8px;">
                @error('image')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- รายละเอียดเพิ่มเติม -->
            <div class="form-group">
                <label class="form-label">รายละเอียด / คำอธิบายชุด</label>
                <textarea name="description" rows="4" class="form-control" placeholder="ระบุรายละเอียดเนื้อผ้า ไซส์ หรือเงื่อนไขการเช่า...">{{ old('description', $dress->description) }}</textarea>
                @error('description')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- ACTIONS -->
            <div class="form-actions">
                <a href="{{ route('owner.dresses.index') }}" class="admin-btn secondary">
                    ยกเลิก
                </a>
                <button type="button" onclick="confirmUpdate()" class="admin-btn primary">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmUpdate() {
        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: "คุณต้องการบันทึกการแก้ไขข้อมูลชุดนี้ใช่หรือไม่",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d2b615ff',
            cancelButtonColor: '#8a7a7d',
            confirmButtonText: 'ใช่',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดงป๊อปอัปสำเร็จทันทีก่อนส่งฟอร์ม
                Swal.fire({
                    title: 'บันทึกการแก้ไขสำเร็จ!',
                    text: 'ระบบกำลังอัปเดตข้อมูล...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    document.getElementById('editDressForm').submit();
                });
            }
        });
    }
</script>
@endsection