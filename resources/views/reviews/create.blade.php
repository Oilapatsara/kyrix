@extends('layouts.customer')

@section('title', 'เขียนรีวิวชุดเช่า | KYRIX')

@push('styles')
<style>
    /* ─── Page ─── */
    .review-page {
        min-height: 100vh;
        background: #faf8f5;
        padding: 48px 20px 100px;
    }
    .review-wrap {
        max-width: 800px;
        margin: 0 auto;
    }

    /* ─── Back Link ─── */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 32px;
        text-decoration: none;
        letter-spacing: .02em;
        transition: color .2s;
    }
    .back-link:hover { color: var(--primary); }

    /* ─── Page Title ─── */
    .review-page-title {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }
    .review-title-icon {
        width: 52px;
        height: 52px;
        background: var(--primary);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        flex-shrink: 0;
    }
    .review-page-title h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 4px;
        letter-spacing: -.02em;
    }
    .review-page-title p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }

    /* ─── Layout: 2 column ─── */
    .review-layout {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 24px;
        align-items: start;
    }

    /* ─── Dress Sidebar ─── */
    .dress-sidebar {
        position: sticky;
        top: 100px;
    }
    .dress-sidebar-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .dress-sidebar-img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        object-position: top center;
        display: block;
    }
    .dress-sidebar-info {
        padding: 18px;
    }
    .dress-sidebar-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--gold);
        background: var(--gold-light, #fef3c7);
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 8px;
    }
    .dress-sidebar-name {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.4;
        margin-bottom: 10px;
    }
    .dress-sidebar-meta {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .dress-sidebar-meta span { display: flex; align-items: center; gap: 6px; }
    .dress-sidebar-meta i { color: var(--primary); width: 14px; }

    /* ─── Main Form ─── */
    .review-form-panel {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ─── Card ─── */
    .rv-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .rv-card-head {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .rv-card-head-icon {
        width: 32px;
        height: 32px;
        background: var(--primary-soft, #fbf0f2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 13px;
        flex-shrink: 0;
    }
    .rv-card-head h3 {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    .rv-card-head span.optional {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 400;
        margin-left: 6px;
    }
    .rv-card-body {
        padding: 24px;
    }

    /* ─── Star Rating ─── */
    .star-picker-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }
    .star-picker {
        display: flex;
        flex-direction: row-reverse;
        gap: 8px;
    }
    .star-picker input { display: none; }
    .star-picker label {
        font-size: 42px;
        color: #e5e7eb;
        cursor: pointer;
        transition: color .15s, transform .15s;
        line-height: 1;
    }
    .star-picker input:checked ~ label,
    .star-picker label:hover,
    .star-picker label:hover ~ label {
        color: #c69c4c;
    }
    .star-picker input:checked ~ label { transform: scale(1.08); }
    .star-label-pill {
        font-size: 13px;
        font-weight: 700;
        padding: 6px 18px;
        border-radius: 20px;
        background: var(--primary-soft, #fbf0f2);
        color: var(--primary);
        min-width: 140px;
        text-align: center;
        transition: all .2s;
        min-height: 32px;
    }

    /* ─── Aspect Tags ─── */
    .aspect-tag-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .aspect-tag {
        padding: 6px 16px;
        border-radius: 6px;
        border: 1.5px solid var(--border);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        user-select: none;
        color: var(--text-muted);
        background: #faf8f5;
        letter-spacing: .01em;
    }
    .aspect-tag:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-soft,#fbf0f2); }
    .aspect-tag.active {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-soft,#fbf0f2);
        font-weight: 700;
    }

    /* ─── Textarea ─── */
    .review-textarea {
        width: 100%;
        padding: 14px 16px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        font-family: inherit;
        font-size: 14px;
        line-height: 1.8;
        color: var(--text-main);
        resize: vertical;
        min-height: 140px;
        transition: border-color .2s, box-shadow .2s;
        background: #fdf9f7;
    }
    .review-textarea:focus {
        outline: none;
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(122,31,43,.08);
    }
    .char-count {
        text-align: right;
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 6px;
    }
    .char-count.warn { color: #dc2626; font-weight: 700; }

    /* ─── Photo Upload ─── */
    .photo-drop-zone {
        border: 1.5px dashed #c8beb8;
        border-radius: 12px;
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        background: #faf8f5;
        transition: all .2s;
        position: relative;
    }
    .photo-drop-zone:hover,
    .photo-drop-zone.drag-over {
        border-color: var(--primary);
        background: var(--primary-soft,#fbf0f2);
    }
    .photo-drop-zone input { display: none; }
    .photo-upload-icon {
        width: 48px;
        height: 48px;
        background: #fff;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--primary);
        margin: 0 auto 12px;
    }
    .photo-preview-strip {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
        justify-content: center;
    }
    .photo-preview-item {
        position: relative;
        width: 90px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        border: 1.5px solid var(--border);
    }
    .photo-preview-item img { width: 100%; height: 100%; object-fit: cover; }
    .remove-photo {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        background: rgba(0,0,0,.6);
        color: #fff;
        border-radius: 50%;
        font-size: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    /* ─── Submit ─── */
    .btn-review-submit {
        width: 100%;
        padding: 15px 24px;
        font-size: 15px;
        font-weight: 800;
        letter-spacing: .04em;
        color: #fff;
        background: var(--primary);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all .25s;
        box-shadow: 0 4px 18px rgba(122,31,43,.25);
    }
    .btn-review-submit:hover {
        background: var(--primary-dark, #58141d);
        transform: translateY(-2px);
        box-shadow: 0 8px 26px rgba(122,31,43,.3);
    }
    .btn-review-submit:active { transform: translateY(0); }
    .btn-review-submit:disabled { opacity: .7; cursor: not-allowed; transform: none; }

    /* ─── Edit badge ─── */
    .edit-notice {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        background: #fffbeb;
        border: 1.5px solid #f59e0b;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 0;
    }
    .edit-notice i { font-size: 14px; }

    /* ─── Alert ─── */
    .alert-error {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 10px;
        padding: 14px 18px;
        font-size: 13px;
        color: #991b1b;
        font-weight: 500;
    }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .review-layout { grid-template-columns: 1fr; }
        .dress-sidebar { position: static; }
        .dress-sidebar-img { height: 200px; }
        .star-picker label { font-size: 36px; }
    }
</style>
@endpush

@section('content')
<div class="review-page">
    <div class="review-wrap">

        {{-- Back --}}
        <a href="{{ route('rentals.show', $rental->rental_id) }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> กลับสู่รายละเอียดการเช่า
        </a>

        {{-- Title --}}
        <div class="review-page-title">
            <div class="review-title-icon">
                <i class="fa-solid fa-{{ $existingReview ? 'pen-to-square' : 'star' }}"></i>
            </div>
            <div>
                <h1>{{ $existingReview ? 'แก้ไขรีวิวความประทับใจ' : 'เขียนรีวิวความประทับใจ' }}</h1>
                <p>แชร์ประสบการณ์การเช่าชุดให้กับลูกค้าท่านอื่น ความคิดเห็นของคุณมีคุณค่ามากสำหรับเรา</p>
            </div>
        </div>

        <div class="review-layout">

            {{-- LEFT: Dress Sidebar --}}
            <div class="dress-sidebar">
                <div class="dress-sidebar-card">
                    <img src="{{ $product->main_image_url }}" class="dress-sidebar-img" alt="{{ $product->product_name }}">
                    <div class="dress-sidebar-info">
                        <span class="dress-sidebar-badge">{{ $product->category->category_name ?? 'ชุดเช่า' }}</span>
                        <div class="dress-sidebar-name">{{ $product->product_name }}</div>
                        <div class="dress-sidebar-meta">
                            <span><i class="fa-solid fa-tag"></i> {{ $product->product_code }}</span>
                            <span><i class="fa-solid fa-receipt"></i> {{ $rental->rental_code ?? 'KR-'.$rental->rental_id }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Form --}}
            <div class="review-form-panel">

                {{-- Edit Notice --}}
                @if($existingReview)
                <div class="edit-notice">
                    <i class="fa-solid fa-circle-info"></i>
                    คุณเคยรีวิวชุดนี้ไว้แล้ว — สามารถแก้ไขรีวิวได้ที่นี่
                </div>
                @endif

                {{-- Error --}}
                @if($errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation" style="flex-shrink:0; margin-top:1px;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" id="reviewForm">
                    @csrf
                    <input type="hidden" name="rental_id"  value="{{ $rental->rental_id }}">
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                    {{-- 1: Rating --}}
                    <div class="rv-card">
                        <div class="rv-card-head">
                            <div class="rv-card-head-icon"><i class="fa-solid fa-star"></i></div>
                            <h3>คะแนนความพึงพอใจโดยรวม</h3>
                        </div>
                        <div class="rv-card-body">
                            <div class="star-picker-wrap">
                                <div class="star-picker" id="starPicker">
                                    @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}"
                                        {{ old('rating', $existingReview->rating ?? 5) == $i ? 'checked' : '' }} required>
                                    <label for="star{{ $i }}" title="{{ $i }} ดาว">
                                        <i class="fa-solid fa-star"></i>
                                    </label>
                                    @endfor
                                </div>
                                @php
                                    $dr = old('rating', $existingReview->rating ?? 5);
                                    $lbs = [1=>'ต้องปรับปรุง',2=>'พอใช้',3=>'ดี',4=>'ดีมาก',5=>'ดีเยี่ยม'];
                                @endphp
                                <div class="star-label-pill" id="starLabelText">{{ $lbs[$dr] ?? '' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- 2: Tags --}}
                    <div class="rv-card">
                        <div class="rv-card-head">
                            <div class="rv-card-head-icon"><i class="fa-solid fa-tags"></i></div>
                            <h3>จุดเด่นที่ประทับใจ <span class="optional">ไม่บังคับ</span></h3>
                        </div>
                        <div class="rv-card-body">
                            <div class="aspect-tag-grid" id="aspectTags">
                                @foreach(['เนื้อผ้าดี','ตัดเย็บประณีต','ทรงสวย','ตรงตามรูป','จัดส่งเร็ว','บริการดีมาก','ไซซ์พอดี','คุ้มค่าราคา','สีสวย','ผ้าระบายอากาศ','เหมาะกับงานต่างๆ','แพ็กสินค้าดี'] as $tag)
                                <span class="aspect-tag" data-tag="{{ $tag }}" onclick="toggleTag(this)">{{ $tag }}</span>
                                @endforeach
                            </div>
                            <input type="hidden" name="tags" id="tagsInput" value="">
                        </div>
                    </div>

                    {{-- 3: Comment --}}
                    <div class="rv-card">
                        <div class="rv-card-head">
                            <div class="rv-card-head-icon"><i class="fa-solid fa-pen-nib"></i></div>
                            <h3>ข้อความรีวิว <span style="color:#dc2626; font-size:13px;">*</span></h3>
                        </div>
                        <div class="rv-card-body">
                            <textarea name="comment" id="commentBox" class="review-textarea" rows="5"
                                placeholder="บอกเล่าประสบการณ์ของคุณ: เนื้อผ้า การตัดเย็บ ความพอดีของไซซ์ หรือบริการของ KYRIX..."
                                maxlength="1000" required>{{ old('comment', $existingReview->comment ?? '') }}</textarea>
                            <div class="char-count" id="charCount">0 / 1000</div>
                        </div>
                    </div>

                    {{-- 4: Photo --}}
                    <div class="rv-card">
                        <div class="rv-card-head">
                            <div class="rv-card-head-icon"><i class="fa-solid fa-camera"></i></div>
                            <h3>แนบรูปภาพ <span class="optional">ไม่บังคับ</span></h3>
                        </div>
                        <div class="rv-card-body">
                            @if($existingReview?->image_path)
                            <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px; padding:12px; background:#faf8f5; border-radius:10px; border:1px solid var(--border);">
                                <img src="{{ asset($existingReview->image_path) }}"
                                    style="width:64px;height:72px;object-fit:cover;border-radius:8px;border:1px solid var(--border); flex-shrink:0;">
                                <div>
                                    <div style="font-size:13px; font-weight:700; color:var(--text-main);">รูปปัจจุบัน</div>
                                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">อัปโหลดรูปใหม่เพื่อเปลี่ยน</div>
                                </div>
                            </div>
                            @endif

                            <div class="photo-drop-zone" id="dropZone"
                                onclick="document.getElementById('reviewPhoto').click();"
                                ondrop="handleDrop(event)"
                                ondragover="event.preventDefault(); this.classList.add('drag-over');"
                                ondragleave="this.classList.remove('drag-over');">
                                <input type="file" name="image" id="reviewPhoto" accept="image/*" onchange="previewPhoto(event)">
                                <div class="photo-upload-icon">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div style="font-weight:700; font-size:14px; color:var(--text-main);">คลิกหรือลากรูปมาวาง</div>
                                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">JPG, PNG, WEBP — สูงสุด 5MB</div>
                                <div class="photo-preview-strip" id="photoPreviewStrip"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 5: Submit --}}
                    <div>
                        <button type="submit" class="btn-review-submit" id="submitBtn">
                            <i class="fa-solid fa-{{ $existingReview ? 'floppy-disk' : 'paper-plane' }}"></i>
                            {{ $existingReview ? 'บันทึกการแก้ไขรีวิว' : 'ส่งรีวิว' }}
                        </button>
                        <p style="text-align:center; font-size:12px; color:var(--text-muted); margin-top:12px; display:flex; align-items:center; justify-content:center; gap:6px;">
                            <i class="fa-solid fa-shield-halved" style="color:var(--primary);"></i>
                            รีวิวของคุณจะปรากฏสาธารณะในหน้ารายละเอียดชุด
                        </p>
                    </div>

                </form>
            </div>{{-- /form panel --}}

        </div>{{-- /layout --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
    const starLabels = {1:'ต้องปรับปรุง',2:'พอใช้',3:'ดี',4:'ดีมาก',5:'ดีเยี่ยม'};
    let selectedTags = [];

    document.querySelectorAll('#starPicker input').forEach(input => {
        input.addEventListener('change', () => {
            const label = document.getElementById('starLabelText');
            label.textContent = starLabels[input.value] || '';
        });
    });

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
        const commentBox = document.getElementById('commentBox');
        if (selectedTags.length > 0 && !commentBox.value) {
            commentBox.placeholder = 'คุณเลือกจุดเด่น: ' + selectedTags.join(', ') + '\nเพิ่มเติมความคิดเห็น...';
        } else {
            commentBox.placeholder = 'บอกเล่าประสบการณ์ของคุณ: เนื้อผ้า การตัดเย็บ ความพอดีของไซซ์ หรือบริการของ KYRIX...';
        }
    }

    const commentBox = document.getElementById('commentBox');
    const charCount  = document.getElementById('charCount');
    function updateCharCount() {
        const len = commentBox.value.length;
        charCount.textContent = len + ' / 1000';
        charCount.classList.toggle('warn', len > 900);
    }
    commentBox.addEventListener('input', updateCharCount);
    updateCharCount();

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
        remove.onclick = () => { document.getElementById('reviewPhoto').value = ''; strip.innerHTML = ''; };
        item.appendChild(img);
        item.appendChild(remove);
        strip.appendChild(item);
    }

    document.getElementById('reviewForm').addEventListener('submit', () => {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังส่งรีวิว...';
        btn.disabled = true;
    });
</script>
@endpush
