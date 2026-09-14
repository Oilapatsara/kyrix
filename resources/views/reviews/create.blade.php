@extends('layouts.customer')

@section('title', 'เขียนรีวิวชุดเช่า | KYRIX')

@push('styles')
<style>
    .review-form-wrap {
        max-width: 700px;
        margin: 40px auto 80px;
        padding: 0 24px;
    }
    .review-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 36px;
    }
    .dress-preview-card {
        display: flex;
        gap: 16px;
        align-items: center;
        background: #faf8f5;
        border-radius: var(--radius-md);
        padding: 16px;
        margin-bottom: 24px;
        border: 1px solid var(--border);
    }
    .dress-preview-img {
        width: 70px;
        height: 85px;
        border-radius: 8px;
        object-fit: cover;
    }

    /* Star Rating Chooser */
    .star-rating-box {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 8px;
        margin: 20px 0;
    }
    .star-rating-box input {
        display: none;
    }
    .star-rating-box label {
        font-size: 38px;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating-box input:checked ~ label,
    .star-rating-box label:hover,
    .star-rating-box label:hover ~ label {
        color: #f59e0b;
    }

    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
        display: block;
    }
    .form-textarea {
        width: 100%;
        padding: 12px 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-family: inherit;
        font-size: 14px;
        line-height: 1.6;
    }
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
    }

    .photo-upload-zone {
        border: 2px dashed #d1c8c1;
        border-radius: var(--radius-md);
        padding: 24px;
        text-align: center;
        cursor: pointer;
        background: #faf8f5;
    }
    .photo-upload-zone:hover {
        border-color: var(--primary);
        background: #fff;
    }
    .review-photo-preview {
        max-width: 180px;
        max-height: 200px;
        border-radius: 8px;
        margin: 12px auto 0;
        display: none;
    }
</style>
@endpush

@section('content')
<div class="review-form-wrap">
    <div class="review-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <span class="badge badge-warning" style="margin-bottom: 8px;">VERIFIED RENTAL</span>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--text-main);">เขียนรีวิวความประทับใจ</h1>
            <p style="font-size: 13px; color: var(--text-muted);">
                แชร์ความคิดเห็นและภาพถ่ายชุดสวย เพื่อเป็นประโยชน์ต่อลูกค้าท่านอื่นๆ
            </p>
        </div>

        <div class="dress-preview-card">
            <img src="{{ $product->main_image_url }}" class="dress-preview-img" alt="{{ $product->product_name }}">
            <div>
                <span style="font-size: 11px; color: var(--gold); font-weight: 700; text-transform: uppercase;">{{ $product->category->category_name ?? 'ชุดเช่า' }}</span>
                <h4 style="font-size: 16px; font-weight: 700; margin: 2px 0;">{{ $product->product_name }}</h4>
                <div style="font-size: 12px; color: var(--text-muted);">
                    รหัสการเช่า: {{ $rental->rental_code ?? 'KR-2026-'.$rental->rental_id }}
                </div>
            </div>
        </div>

        <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="rental_id" value="{{ $rental->rental_id }}">
            <input type="hidden" name="product_id" value="{{ $product->product_id }}">

            <!-- 1. Star Rating -->
            <div style="text-align: center; margin-bottom: 24px;">
                <label class="form-label" style="margin-bottom: 4px;">ให้คะแนนความพึงพอใจโดยรวม *</label>
                <div class="star-rating-box">
                    <input type="radio" id="star5" name="rating" value="5" {{ old('rating', $existingReview->rating ?? 5) == 5 ? 'checked' : '' }} required>
                    <label for="star5" title="5 ดาว - ดีเยี่ยม"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star4" name="rating" value="4" {{ old('rating', $existingReview->rating ?? 5) == 4 ? 'checked' : '' }}>
                    <label for="star4" title="4 ดาว - ดีมาก"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star3" name="rating" value="3" {{ old('rating', $existingReview->rating ?? 5) == 3 ? 'checked' : '' }}>
                    <label for="star3" title="3 ดาว - ปานกลาง"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star2" name="rating" value="2" {{ old('rating', $existingReview->rating ?? 5) == 2 ? 'checked' : '' }}>
                    <label for="star2" title="2 ดาว - พอใช้"><i class="fa-solid fa-star"></i></label>
                    <input type="radio" id="star1" name="rating" value="1" {{ old('rating', $existingReview->rating ?? 5) == 1 ? 'checked' : '' }}>
                    <label for="star1" title="1 ดาว - ต้องปรับปรุง"><i class="fa-solid fa-star"></i></label>
                </div>
            </div>

            <!-- 2. Comment -->
            <div class="form-group">
                <label class="form-label">เขียนข้อความรีวิว *</label>
                <textarea name="comment" class="form-textarea" rows="4" placeholder="บอกเล่าความรู้สึก เนื้อผ้า การตัดเย็บ ความพอดีของขนาด หรือบริการของร้าน KYRIX..." required>{{ old('comment', $existingReview->comment ?? '') }}</textarea>
            </div>

            <!-- 3. Photo Upload -->
            <div class="form-group">
                <label class="form-label">แนบรูปภาพขณะสวมใส่ชุด (ไม่บังคับ)</label>
                <div class="photo-upload-zone" onclick="document.getElementById('reviewPhotoInput').click();">
                    <i class="fa-solid fa-camera" style="font-size: 28px; color: var(--primary); margin-bottom: 8px;"></i>
                    <div style="font-weight: 600; font-size: 13px;">คลิกเพื่ออัปโหลดรูปภาพชุดสวยของคุณ</div>
                    <span style="font-size: 11px; color: #888;">รองรับไฟล์ JPG, PNG, WEBP</span>
                    <input type="file" name="image" id="reviewPhotoInput" accept="image/*" style="display: none;" onchange="previewReviewPhoto(event)">
                    <img id="reviewPhotoPreview" class="review-photo-preview" alt="ภาพรีวิว">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 15px;">
                <i class="fa-solid fa-paper-plane"></i> ส่งรีวิว
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewReviewPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const preview = document.getElementById('reviewPhotoPreview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    }
</script>
@endpush

@endsection
