@extends('layouts.customer')

@section('title', 'เขียนรีวิวชุดเช่า | KYRIX')

@push('styles')
<style>
    /* ─── Page Layout ─── */
    .review-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #fdf8f3 0%, #faf5ee 100%);
        padding: 40px 20px 80px;
    }
    .review-wrap {
        max-width: 760px;
        margin: 0 auto;
    }

    /* ─── Back link ─── */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 24px;
        transition: color .2s;
    }
    .back-link:hover { color: var(--primary); }

    /* ─── Header Banner ─── */
    .review-header-banner {
        background: linear-gradient(135deg, var(--primary) 0%, #c9956e 100%);
        border-radius: var(--radius-lg);
        padding: 32px 36px;
        color: #fff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 8px 32px rgba(183,130,97,.35);
    }
    .review-header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }
    .review-header-banner h1 {
        font-size: 22px;
        font-weight: 800;
        margin: 0 0 4px;
    }
    .review-header-banner p {
        font-size: 13px;
        opacity: .85;
        margin: 0;
    }

    /* ─── Dress Preview Card ─── */
    .dress-preview-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 20px;
        margin-bottom: 24px;
        display: flex;
        gap: 18px;
        align-items: center;
        box-shadow: var(--shadow-sm);
    }
    .dress-preview-img {
        width: 80px;
        height: 96px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid var(--border);
    }
    .dress-preview-badge {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--gold);
        background: var(--gold-light, #fef3c7);
        padding: 3px 8px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 6px;
    }
    .dress-preview-name {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 4px;
    }
    .dress-preview-meta {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ─── Main Form Card ─── */
    .review-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .review-card-section {
        padding: 28px 32px;
        border-bottom: 1px solid var(--border);
    }
    .review-card-section:last-child { border-bottom: none; }
    .review-section-title {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--text-muted);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .review-section-title i { color: var(--primary); }

    /* ─── Interactive Star Rating ─── */
    .star-rating-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
    }
    .star-picker {
        display: flex;
        flex-direction: row-reverse;
        gap: 6px;
    }
    .star-picker input { display: none; }
    .star-picker label {
        font-size: 44px;
        color: #e5e7eb;
        cursor: pointer;
        transition: color .15s, transform .15s;
        line-height: 1;
    }
    .star-picker input:checked ~ label,
    .star-picker label:hover,
    .star-picker label:hover ~ label {
        color: #f59e0b;
        transform: scale(1.12);
    }
    .star-label-text {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-muted);
        min-height: 22px;
        transition: color .2s;
    }
    .star-label-text.filled { color: var(--primary); }

    /* ─── Aspect Tags ─── */
    .aspect-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }
    .aspect-tag {
        padding: 6px 14px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        user-select: none;
        color: var(--text-muted);
        background: #faf8f5;
    }
    .aspect-tag:hover,
    .aspect-tag.active {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-soft, #fdf0e8);
    }

    /* ─── Comment Textarea ─── */
    .review-textarea {
        width: 100%;
        padding: 14px 16px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        font-family: inherit;
        font-size: 14px;
        line-height: 1.7;
        color: var(--text-main);
        resize: vertical;
        min-height: 130px;
        transition: border-color .2s, box-shadow .2s;
    }
    .review-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(183,130,97,.12);
    }
    .char-count {
        text-align: right;
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 6px;
    }
    .char-count.warn { color: #dc2626; }

    /* ─── Photo Upload ─── */
    .photo-drop-zone {
        border: 2px dashed #d1c8c1;
        border-radius: 12px;
        padding: 32px 24px;
        text-align: center;
        cursor: pointer;
        background: #faf8f5;
        transition: all .2s;
        position: relative;
    }
    .photo-drop-zone:hover,
    .photo-drop-zone.drag-over {
        border-color: var(--primary);
        background: #fff5ef;
    }
    .photo-drop-zone input { display: none; }
    .photo-preview-strip {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }
    .photo-preview-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        border: 1.5px solid var(--border);
    }
    .photo-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-preview-item .remove-photo {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        background: rgba(0,0,0,.65);
        color: #fff;
        border-radius: 50%;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    /* ─── Submit Button ─── */
    .btn-submit-review {
        width: 100%;
        padding: 16px;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: .03em;
        background: linear-gradient(135deg, var(--primary) 0%, #c9956e 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all .25s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 20px rgba(183,130,97,.3);
    }
    .btn-submit-review:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(183,130,97,.4);
    }
    .btn-submit-review:active { transform: translateY(0); }

    /* ─── Edit badge ─── */
    .edit-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff7ed;
        border: 1.5px solid #f59e0b;
        color: #92400e;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 16px;
    }

    /* ─── Alert ─── */
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #991b1b;
    }

    @media (max-width: 600px) {
        .review-header-banner { flex-direction: column; text-align: center; padding: 24px; }
        .review-card-section { padding: 20px; }
        .star-picker label { font-size: 36px; }
        .dress-preview-card { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="review-page">
    <div class="review-wrap">

        {{-- Back link --}}
        <a href="{{ route('rentals.show', $rental->rental_id) }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> กลับสู่รายละเอียดการเช่า
        </a>

        {{-- Header Banner --}}
        <div class="review-header-banner">
            <div class="review-header-icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <h1>{{ $existingReview ? 'แก้ไขรีวิวความประทับใจ' : 'เขียนรีวิวความประทับใจ' }}</h1>
                <p>แชร์ประสบการณ์การเช่าชุดให้ลูกค้าท่านอื่นได้รับรู้ ความคิดเห็นของคุณมีค่ามากสำหรับเรา ❤️</p>
            </div>
        </div>

        {{-- Dress Preview --}}
        <div class="dress-preview-card">
            <img src="{{ $product->main_image_url }}" class="dress-preview-img" alt="{{ $product->product_name }}">
            <div>
                <span class="dress-preview-badge">{{ $product->category->category_name ?? 'ชุดเช่า' }}</span>
                <div class="dress-preview-name">{{ $product->product_name }}</div>
                <div class="dress-preview-meta">
                    <i class="fa-solid fa-barcode" style="margin-right:4px;"></i>
                    รหัสชุด: {{ $product->product_code }}
                    &nbsp;·&nbsp;
                    <i class="fa-solid fa-receipt" style="margin-right:4px;"></i>
                    รหัสเช่า: {{ $rental->rental_code ?? 'KR-'.$rental->rental_id }}
                </div>
            </div>
        </div>

        {{-- Edit Notice --}}
        @if($existingReview)
        <div class="edit-badge">
            <i class="fa-solid fa-pen-to-square"></i>
            คุณเคยรีวิวชุดนี้แล้ว — แก้ไขรีวิวได้ที่นี่
        </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i>
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Main Form Card --}}
        <div class="review-card">
            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" id="reviewForm">
                @csrf
                <input type="hidden" name="rental_id"  value="{{ $rental->rental_id }}">
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                {{-- Section 1: Star Rating --}}
                <div class="review-card-section">
                    <div class="review-section-title">
                        <i class="fa-solid fa-star"></i>
                        ให้คะแนนความพึงพอใจโดยรวม
                    </div>
                    <div class="star-rating-wrap">
                        <div class="star-picker" id="starPicker">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}"
                                {{ old('rating', $existingReview->rating ?? 5) == $i ? 'checked' : '' }} required>
                            <label for="star{{ $i }}" title="{{ $i }} ดาว">
                                <i class="fa-solid fa-star"></i>
                            </label>
                            @endfor
                        </div>
                        <div class="star-label-text" id="starLabelText">
                            @php
                                $defaultRating = old('rating', $existingReview->rating ?? 5);
                                $labels = [1=>'ต้องปรับปรุง 😕',2=>'พอใช้ 😐',3=>'ดี 🙂',4=>'ดีมาก 😊',5=>'ดีเยี่ยม ✨'];
                            @endphp
                            {{ $labels[$defaultRating] ?? '' }}
                        </div>
                    </div>
                </div>

                {{-- Section 2: Quick Tags --}}
                <div class="review-card-section">
                    <div class="review-section-title">
                        <i class="fa-solid fa-tags"></i>
                        แตะเพื่อเลือกจุดเด่น (ไม่บังคับ)
                    </div>
                    <div class="aspect-tags" id="aspectTags">
                        @foreach(['เนื้อผ้าดี','ตัดเย็บประณีต','ทรงสวย','ตรงตามรูป','จัดส่งเร็ว','บริการดีมาก','ไซซ์พอดี','คุ้มค่าราคา','สีสวย','ผ้าระบายอากาศ','เหมาะกับงานต่างๆ','แพ็กสินค้าดี'] as $tag)
                        <span class="aspect-tag" data-tag="{{ $tag }}" onclick="toggleTag(this)">{{ $tag }}</span>
                        @endforeach
                    </div>
                    <input type="hidden" name="tags" id="tagsInput" value="">
                </div>

                {{-- Section 3: Comment --}}
                <div class="review-card-section">
                    <div class="review-section-title">
                        <i class="fa-solid fa-pen-nib"></i>
                        เขียนข้อความรีวิว <span style="color:#dc2626;">*</span>
                    </div>
                    <textarea name="comment" id="commentBox" class="review-textarea" rows="5"
                        placeholder="บอกเล่าประสบการณ์ของคุณ: เนื้อผ้า การตัดเย็บ ความพอดีของไซซ์ หรือบริการของ KYRIX..."
                        maxlength="1000" required>{{ old('comment', $existingReview->comment ?? '') }}</textarea>
                    <div class="char-count" id="charCount">0 / 1000</div>
                </div>

                {{-- Section 4: Photo Upload --}}
                <div class="review-card-section">
                    <div class="review-section-title">
                        <i class="fa-solid fa-camera"></i>
                        แนบรูปภาพขณะสวมใส่ชุด (ไม่บังคับ)
                    </div>

                    @if($existingReview?->image_path)
                    <div style="margin-bottom:12px; display:flex; align-items:center; gap:10px;">
                        <img src="{{ asset($existingReview->image_path) }}"
                            style="width:80px;height:90px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                        <div>
                            <div style="font-size:13px; font-weight:600;">รูปปัจจุบัน</div>
                            <div style="font-size:12px; color:var(--text-muted);">อัปโหลดใหม่เพื่อเปลี่ยนรูป</div>
                        </div>
                    </div>
                    @endif

                    <div class="photo-drop-zone" id="dropZone"
                        onclick="document.getElementById('reviewPhoto').click();"
                        ondrop="handleDrop(event)" ondragover="event.preventDefault(); this.classList.add('drag-over');"
                        ondragleave="this.classList.remove('drag-over');">
                        <input type="file" name="image" id="reviewPhoto" accept="image/*" onchange="previewPhoto(event)">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:32px; color:var(--primary); margin-bottom:10px;"></i>
                        <div style="font-weight:700; font-size:14px; color:var(--text-main);">คลิกหรือลากไฟล์มาวางที่นี่</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">รองรับ JPG, PNG, WEBP — ขนาดสูงสุด 5MB</div>
                        <div class="photo-preview-strip" id="photoPreviewStrip"></div>
                    </div>
                </div>

                {{-- Section 5: Submit --}}
                <div class="review-card-section">
                    <button type="submit" class="btn-submit-review" id="submitBtn">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ $existingReview ? 'บันทึกการแก้ไขรีวิว' : 'ส่งรีวิว' }}
                    </button>
                    <p style="text-align:center; font-size:12px; color:var(--text-muted); margin-top:12px;">
                        <i class="fa-solid fa-shield-halved"></i>
                        รีวิวของคุณจะถูกเผยแพร่สาธารณะในหน้ารายละเอียดชุด
                    </p>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const starLabels = {1:'ต้องปรับปรุง 😕',2:'พอใช้ 😐',3:'ดี 🙂',4:'ดีมาก 😊',5:'ดีเยี่ยม ✨'};
    let selectedTags = [];

    // Star rating label update
    document.querySelectorAll('#starPicker input').forEach(input => {
        input.addEventListener('change', () => {
            const label = document.getElementById('starLabelText');
            label.textContent = starLabels[input.value] || '';
            label.classList.add('filled');
        });
    });

    // Aspect tag toggle
    function toggleTag(el) {
        const tag = el.dataset.tag;
        if (el.classList.contains('active')) {
            el.classList.remove('active');
            selectedTags = selectedTags.filter(t => t !== tag);
        } else {
            el.classList.add('active');
            selectedTags.push(tag);
        }
        document.getElementById('tagsInput').value = selectedTags.join(',');

        // Append tags to comment placeholder hint
        const commentBox = document.getElementById('commentBox');
        if (selectedTags.length > 0 && !commentBox.value) {
            commentBox.placeholder = 'คุณเลือกจุดเด่น: ' + selectedTags.join(', ') + '\nเพิ่มเติมความคิดเห็นของคุณที่นี่...';
        } else {
            commentBox.placeholder = 'บอกเล่าประสบการณ์ของคุณ: เนื้อผ้า การตัดเย็บ ความพอดีของไซซ์ หรือบริการของ KYRIX...';
        }
    }

    // Character count
    const commentBox = document.getElementById('commentBox');
    const charCount  = document.getElementById('charCount');

    function updateCharCount() {
        const len = commentBox.value.length;
        charCount.textContent = len + ' / 1000';
        charCount.classList.toggle('warn', len > 900);
    }
    commentBox.addEventListener('input', updateCharCount);
    updateCharCount();

    // Photo preview
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        showPhotoPreview(file);
    }

    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('dropZone').classList.remove('drag-over');
        const file = event.dataTransfer.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        // Assign to input
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('reviewPhoto').files = dt.files;
        showPhotoPreview(file);
    }

    function showPhotoPreview(file) {
        const strip = document.getElementById('photoPreviewStrip');
        strip.innerHTML = '';
        const item = document.createElement('div');
        item.className = 'photo-preview-item';
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        const remove = document.createElement('div');
        remove.className = 'remove-photo';
        remove.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        remove.onclick = () => {
            document.getElementById('reviewPhoto').value = '';
            strip.innerHTML = '';
        };
        item.appendChild(img);
        item.appendChild(remove);
        strip.appendChild(item);
    }

    // Submit feedback
    document.getElementById('reviewForm').addEventListener('submit', (e) => {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังส่งรีวิว...';
        btn.disabled = true;
    });
</script>
@endpush
