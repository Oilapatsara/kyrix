@extends('layouts.customer')

@section('title', 'ข้อมูลส่วนตัวของฉัน | KYRIX')

@push('styles')
    <style>
        .profile-wrap {
            max-width: 1000px;
            margin: 40px auto 80px;
            padding: 0 24px;
        }

        .profile-header {
            margin-bottom: 30px;
        }

        .profile-header h1 {
            font-size: 28px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 8px;
        }

        .profile-header p {
            margin: 0;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .stat-card h3 {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin: 0 0 6px;
        }

        .stat-card p {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }

        .profile-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 30px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(143, 29, 44, 0.08);
        }

        /* =========================
               PROFILE IMAGE
            ========================= */

        .profile-image-box {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 4px 0 28px;
        }

        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 16px;
            border: 5px solid #fff;
            box-shadow:
                0 6px 24px rgba(0, 0, 0, 0.12),
                0 0 0 1px #eee;
            background: #f8e9eb;
        }

        .profile-avatar-default {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 48px;
        }

        .kyrix-profile-name {
            width: 100%;
            margin: 0 0 14px;
            padding: 0;
            text-align: center !important;
            font-size: 20px;
            font-weight: 800;
            color: #333;
            line-height: 1.5;
        }

        .profile-upload-form {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 0;
        }

        .profile-upload-btn {
            width: 210px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-sizing: border-box;
            text-align: center;
        }

        .profile-upload-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .profile-upload-btn i {
            font-size: 14px;
        }

        .profile-image-help {
            margin-top: 11px;
            color: var(--text-muted);
            font-size: 11px;
            line-height: 1.6;
            text-align: center;
        }

        .profile-image-error {
            margin-top: 8px;
            color: #b91c1c;
            font-size: 12px;
            text-align: center;
        }

        .profile-success-message {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* =========================
               PROFILE IMAGE EDITOR
            ========================= */

        .profile-editor-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(20, 15, 16, 0.68);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-editor-modal.active {
            display: flex;
        }

        .profile-editor-box {
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 20px;
            padding: 26px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        }

        .profile-editor-title {
            text-align: center;
            font-size: 20px;
            font-weight: 800;
            color: #333;
            margin-bottom: 8px;
        }

        .profile-editor-subtitle {
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .profile-editor-crop {
            width: 300px;
            height: 300px;
            max-width: 100%;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            background: #f8e9eb;
            border: 5px solid #fff;
            box-shadow:
                0 6px 24px rgba(0, 0, 0, 0.15),
                0 0 0 1px #eee;
            cursor: grab;
            touch-action: none;
        }

        .profile-editor-crop.dragging {
            cursor: grabbing;
        }

        #profileEditorImage {
            position: absolute;
            left: 50%;
            top: 50%;
            max-width: none;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            user-select: none;
            pointer-events: none;
        }

        .profile-editor-help {
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .profile-editor-control {
            margin-bottom: 22px;
        }

        .profile-editor-control label {
            display: block;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .profile-zoom-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-zoom-btn {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 8px;
            color: var(--primary);
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
        }

        .profile-zoom-btn:hover {
            background: #faf1f2;
        }

        #profileZoom {
            flex: 1;
            accent-color: var(--primary);
        }

        .profile-zoom-value {
            min-width: 50px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .profile-editor-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .profile-editor-cancel,
        .profile-editor-confirm {
            border: none;
            border-radius: 9px;
            padding: 12px 16px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .profile-editor-cancel {
            background: #f2f2f2;
            color: #555;
        }

        .profile-editor-confirm {
            background: var(--primary);
            color: #fff;
        }

        .profile-editor-cancel:hover,
        .profile-editor-confirm:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .profile-editor-confirm:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        /* =========================
               RESPONSIVE
            ========================= */

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-wrap {
                padding: 0 16px;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-card {
                padding: 15px 10px;
            }

            .profile-card {
                padding: 20px;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
            }

            .profile-upload-btn {
                width: 200px;
            }

            .profile-editor-box {
                padding: 20px;
            }

            .profile-editor-crop {
                width: 250px;
                height: 250px;
            }
        }
    </style>
@endpush

@section('content')

    <div class="profile-wrap">

        {{-- HEADER --}}
        <div class="profile-header">

            <h1>
                <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i>

                <span>ข้อมูลส่วนตัว & บัญชีของฉัน</span>
            </h1>

            <p style="
                color: var(--text-muted);
                font-size: 14px;
            ">
                จัดการข้อมูลโปรไฟล์ ที่อยู่สำหรับจัดส่งชุด และเปลี่ยนรหัสผ่านความปลอดภัย
            </p>

        </div>

        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="profile-success-message">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- STATS --}}
        <div class="stats-row">

            <div class="stat-card">
                <h3>
                    {{ $stats['total_rentals'] }}
                </h3>

                <p>
                    รายการเช่าทั้งหมด
                </p>
            </div>

            <div class="stat-card">
                <h3 style="color: #0284c7;">
                    {{ $stats['active_rentals'] }}
                </h3>

                <p>
                    รายการกำลังดำเนินการ
                </p>
            </div>

            <div class="stat-card">
                <h3 style="color: #16a34a;">
                    {{ $stats['completed_rentals'] }}
                </h3>

                <p>
                    รายการคืนชุดสำเร็จ
                </p>
            </div>

            <div class="stat-card">
                <h3 style="color: var(--gold);">
                    {{ $stats['reviews_count'] }}
                </h3>

                <p>
                    รีวิวที่เขียนแล้ว
                </p>
            </div>

        </div>

        {{-- MAIN GRID --}}
        <div class="profile-grid">

            {{-- ======================================
             PROFILE INFORMATION
        ======================================= --}}
            <div class="profile-card">

                {{-- PROFILE IMAGE --}}
                <div class="profile-image-box">

                    @if ($customer->profile_image)
                        <img src="{{ asset('storage/' . $customer->profile_image) }}" alt="รูปโปรไฟล์"
                            class="profile-avatar">
                    @else
                        <div class="profile-avatar profile-avatar-default">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif

                    <div class="kyrix-profile-name">
                        {{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) }}
                    </div>

                    {{-- FORM เฉพาะสำหรับเปลี่ยนรูป --}}
                    <form action="{{ route('profile.image.update') }}" method="POST" enctype="multipart/form-data"
                        class="profile-upload-form" id="profileImageForm">

                        @csrf

                        <label for="profile_image" class="profile-upload-btn">
                            <i class="fa-solid fa-camera"></i>
                            เปลี่ยนรูปโปรไฟล์
                        </label>

                        <input type="file" name="profile_image" id="profile_image" accept=".jpg,.jpeg,.png,.webp" hidden>

                    </form>
                    @error('profile_image')
                        <div class="profile-image-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- USER INFORMATION --}}
                <h3 class="card-title">
                    <i class="fa-solid fa-address-card"></i>
                    <span>ข้อมูลผู้ใช้งาน & ที่อยู่จัดส่ง</span>
                </h3>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf

                    <div class="form-group">

                        <label class="form-label">
                            ชื่อ - นามสกุล *
                        </label>

                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', trim($customer->first_name . ' ' . $customer->last_name)) }}" required>

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            อีเมล (Email) *
                        </label>

                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $customer->email) }}" required>

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            เบอร์โทรศัพท์ติดต่อ *
                        </label>

                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $customer->phone) }}" placeholder="เช่น 089-123-4567">

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            ที่อยู่สำหรับจัดส่งชุดเริ่มต้น
                        </label>

                        <textarea name="address" class="form-control" rows="4"
                            placeholder="บ้านเลขที่, ถนน, ซอย, เขต/อำเภอ, จังหวัด, รหัสไปรษณีย์">{{ old('address', $customer->address) }}</textarea>

                        <span
                            style="
                            font-size: 11px;
                            color: var(--text-muted);
                            margin-top: 4px;
                            display: block;
                        ">
                            * ข้อมูลที่อยู่นี้จะถูกนำไปกรอกอัตโนมัติเมื่อทำรายการเช่าชุด
                        </span>

                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 12px;">
                        <i class="fa-solid fa-floppy-disk"></i>
                        บันทึกข้อมูลส่วนตัว
                    </button>

                </form>

            </div>


            {{-- ======================================
             CHANGE PASSWORD
        ======================================= --}}
            <div class="profile-card">

                <h3 class="card-title">
                    <i class="fa-solid fa-lock"></i>
                    <span>เปลี่ยนรหัสผ่าน (Change Password)</span>
                </h3>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf

                    <div class="form-group">

                        <label class="form-label">
                            รหัสผ่านปัจจุบัน *
                        </label>

                        <input type="password" name="current_password" class="form-control"
                            placeholder="กรอกรหัสผ่านปัจจุบัน" required>

                        @error('current_password')
                            <span
                                style="
                                color: #b91c1c;
                                font-size: 12px;
                            ">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร) *
                        </label>

                        <input type="password" name="password" class="form-control" placeholder="กรอกรหัสผ่านใหม่" required>

                        @error('password')
                            <span
                                style="
                                color: #b91c1c;
                                font-size: 12px;
                            ">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            ยืนยันรหัสผ่านใหม่อีกครั้ง *
                        </label>

                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="ยืนยันรหัสผ่านใหม่อีกครั้ง" required>

                    </div>

                    <div
                        style="
                        background: #faf8f5;
                        border: 1px solid var(--border);
                        padding: 12px;
                        border-radius: 8px;
                        font-size: 12px;
                        color: var(--text-muted);
                        margin-bottom: 20px;
                        line-height: 1.6;
                    ">
                        <i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i>

                        เพื่อความปลอดภัย แนะนำให้ใช้รหัสผ่านที่มีทั้งตัวอักษรและตัวเลขผสมกัน
                    </div>

                    <button type="submit" class="btn btn-secondary btn-block" style="padding: 12px;">
                        <i class="fa-solid fa-key"></i>
                        เปลี่ยนรหัสผ่านใหม่
                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- ==========================================
     PROFILE IMAGE EDITOR MODAL
========================================== --}}

    <div id="profileEditorModal" class="profile-editor-modal">

        <div class="profile-editor-box">

            <div class="profile-editor-title">

                <i class="fa-solid fa-image" style="color: var(--primary);"></i>

                ปรับรูปโปรไฟล์

            </div>

            <div class="profile-editor-subtitle">
                จัดตำแหน่งรูปให้พอดีกับวงกลมก่อนบันทึก
            </div>

            <div class="profile-editor-crop" id="profileEditorCrop">

                <img id="profileEditorImage" src="" alt="Preview">

            </div>

            <div class="profile-editor-help">

                ลากรูปเพื่อเลื่อนตำแหน่ง

                <br>

                ใช้แถบเลื่อนหรือปุ่ม + / − เพื่อย่อหรือขยายรูป

            </div>

            <div class="profile-editor-control">

                <label>
                    ขนาดรูป
                </label>

                <div class="profile-zoom-row">

                    <button type="button" class="profile-zoom-btn" onclick="changeProfileZoom(-0.1)">
                        −
                    </button>

                    <input type="range" id="profileZoom" min="0.5" max="3" step="0.01"
                        value="1">

                    <button type="button" class="profile-zoom-btn" onclick="changeProfileZoom(0.1)">
                        +
                    </button>

                    <div class="profile-zoom-value" id="profileZoomValue">
                        100%
                    </div>

                </div>

            </div>

            <div class="profile-editor-actions">

                <button type="button" class="profile-editor-cancel" onclick="closeProfileEditor()">
                    ยกเลิก
                </button>

                <button type="button" class="profile-editor-confirm" id="profileEditorSaveButton"
                    onclick="saveEditedProfileImage()">
                    <i class="fa-solid fa-floppy-disk"></i>
                    บันทึกรูปนี้
                </button>

            </div>

        </div>

    </div>


    @push('scripts')
        <script>
            let profileImage = null;

            let profileZoom = 1;

            let profilePosX = 0;
            let profilePosY = 0;

            let isDraggingProfileImage = false;

            let dragStartX = 0;
            let dragStartY = 0;

            const profileInput =
                document.getElementById('profile_image');

            const profileModal =
                document.getElementById('profileEditorModal');

            const profileEditorImage =
                document.getElementById('profileEditorImage');

            const profileEditorCrop =
                document.getElementById('profileEditorCrop');

            const profileZoomInput =
                document.getElementById('profileZoom');

            const profileZoomValue =
                document.getElementById('profileZoomValue');

            const profileImageForm =
                document.getElementById('profileImageForm');

            const profileEditorSaveButton =
                document.getElementById('profileEditorSaveButton');


            /* =========================
               SELECT IMAGE
            ========================= */

            profileInput.addEventListener(
                'change',
                function(event) {

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

                    if (!allowedTypes.includes(file.type)) {

                        alert(
                            'กรุณาเลือกไฟล์ JPG, PNG หรือ WEBP เท่านั้น'
                        );

                        profileInput.value = '';

                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {

                        alert(
                            'ขนาดไฟล์ต้องไม่เกิน 5MB'
                        );

                        profileInput.value = '';

                        return;
                    }

                    const reader =
                        new FileReader();

                    reader.onload =
                        function(e) {

                            profileEditorImage.onload =
                                function() {

                                    profileImage =
                                        new Image();

                                    profileImage.onload =
                                        function() {

                                            profileZoom = 1;
                                            profilePosX = 0;
                                            profilePosY = 0;

                                            profileZoomInput.value =
                                                1;

                                            profileZoomValue.textContent =
                                                '100%';

                                            profileEditorSaveButton.disabled =
                                                false;

                                            updateProfileEditorImage();

                                            profileModal.classList.add(
                                                'active'
                                            );

                                        };

                                    profileImage.src =
                                        e.target.result;

                                };

                            profileEditorImage.src =
                                e.target.result;
                        };

                    reader.readAsDataURL(file);
                }
            );


            /* =========================
               ZOOM
            ========================= */

            profileZoomInput.addEventListener(
                'input',
                function() {

                    profileZoom =
                        parseFloat(this.value);

                    profileZoomValue.textContent =
                        Math.round(profileZoom * 100) +
                        '%';

                    updateProfileEditorImage();
                }
            );


            function changeProfileZoom(amount) {

                profileZoom += amount;

                if (profileZoom < 0.5) {
                    profileZoom = 0.5;
                }

                if (profileZoom > 3) {
                    profileZoom = 3;
                }

                profileZoomInput.value =
                    profileZoom;

                profileZoomValue.textContent =
                    Math.round(profileZoom * 100) +
                    '%';

                updateProfileEditorImage();
            }


            /* =========================
               UPDATE PREVIEW
            ========================= */

            function updateProfileEditorImage() {

                if (!profileImage) {
                    return;
                }

                const containerWidth =
                    profileEditorCrop.clientWidth;

                const containerHeight =
                    profileEditorCrop.clientHeight;

                const imageRatio =
                    profileImage.width /
                    profileImage.height;

                let baseWidth;
                let baseHeight;

                if (imageRatio > 1) {

                    baseHeight =
                        containerHeight;

                    baseWidth =
                        baseHeight *
                        imageRatio;

                } else {

                    baseWidth =
                        containerWidth;

                    baseHeight =
                        baseWidth /
                        imageRatio;
                }

                const finalWidth =
                    baseWidth *
                    profileZoom;

                const finalHeight =
                    baseHeight *
                    profileZoom;

                profileEditorImage.style.width =
                    finalWidth + 'px';

                profileEditorImage.style.height =
                    finalHeight + 'px';

                profileEditorImage.style.left =
                    'calc(50% + ' +
                    profilePosX +
                    'px)';

                profileEditorImage.style.top =
                    'calc(50% + ' +
                    profilePosY +
                    'px)';

                profileEditorImage.style.transform =
                    'translate(-50%, -50%)';
            }


            /* =========================
               DRAG IMAGE
            ========================= */

            function startProfileDrag(
                clientX,
                clientY
            ) {

                isDraggingProfileImage =
                    true;

                profileEditorCrop.classList.add(
                    'dragging'
                );

                dragStartX =
                    clientX -
                    profilePosX;

                dragStartY =
                    clientY -
                    profilePosY;
            }


            function moveProfileDrag(
                clientX,
                clientY
            ) {

                if (!isDraggingProfileImage) {
                    return;
                }

                profilePosX =
                    clientX -
                    dragStartX;

                profilePosY =
                    clientY -
                    dragStartY;

                updateProfileEditorImage();
            }


            function stopProfileDrag() {

                isDraggingProfileImage =
                    false;

                profileEditorCrop.classList.remove(
                    'dragging'
                );
            }


            profileEditorCrop.addEventListener(
                'mousedown',
                function(event) {

                    startProfileDrag(
                        event.clientX,
                        event.clientY
                    );
                }
            );


            document.addEventListener(
                'mousemove',
                function(event) {

                    moveProfileDrag(
                        event.clientX,
                        event.clientY
                    );
                }
            );


            document.addEventListener(
                'mouseup',
                function() {

                    stopProfileDrag();
                }
            );


            /* =========================
               TOUCH
            ========================= */

            profileEditorCrop.addEventListener(
                'touchstart',
                function(event) {

                    if (!event.touches[0]) {
                        return;
                    }

                    startProfileDrag(
                        event.touches[0].clientX,
                        event.touches[0].clientY
                    );
                }, {
                    passive: true
                }
            );


            profileEditorCrop.addEventListener(
                'touchmove',
                function(event) {

                    if (!event.touches[0]) {
                        return;
                    }

                    moveProfileDrag(
                        event.touches[0].clientX,
                        event.touches[0].clientY
                    );

                    event.preventDefault();
                }, {
                    passive: false
                }
            );


            profileEditorCrop.addEventListener(
                'touchend',
                function() {

                    stopProfileDrag();
                }
            );


            /* =========================
               CLOSE EDITOR
            ========================= */

            function closeProfileEditor() {

                profileModal.classList.remove(
                    'active'
                );

                profileInput.value = '';

                profileImage = null;

                profileZoom = 1;
                profilePosX = 0;
                profilePosY = 0;

                profileZoomInput.value =
                    1;

                profileZoomValue.textContent =
                    '100%';

                profileEditorSaveButton.disabled =
                    false;
            }


            /* =========================
               SAVE EDITED IMAGE
            ========================= */

            function saveEditedProfileImage() {

                if (!profileImage) {
                    return;
                }

                profileEditorSaveButton.disabled =
                    true;

                const outputSize = 600;

                const canvas =
                    document.createElement('canvas');

                canvas.width =
                    outputSize;

                canvas.height =
                    outputSize;

                const ctx =
                    canvas.getContext('2d');

                const cropSize =
                    profileEditorCrop.clientWidth;

                const displayWidth =
                    parseFloat(
                        profileEditorImage.style.width
                    );

                const displayHeight =
                    parseFloat(
                        profileEditorImage.style.height
                    );

                const scale =
                    outputSize /
                    cropSize;

                const imageWidth =
                    displayWidth *
                    scale;

                const imageHeight =
                    displayHeight *
                    scale;

                const imageX =
                    (
                        cropSize / 2 +
                        profilePosX -
                        displayWidth / 2
                    ) *
                    scale;

                const imageY =
                    (
                        cropSize / 2 +
                        profilePosY -
                        displayHeight / 2
                    ) *
                    scale;

                ctx.fillStyle =
                    '#ffffff';

                ctx.fillRect(
                    0,
                    0,
                    outputSize,
                    outputSize
                );

                ctx.drawImage(
                    profileImage,
                    imageX,
                    imageY,
                    imageWidth,
                    imageHeight
                );

                canvas.toBlob(
                    function(blob) {

                        if (!blob) {

                            alert(
                                'ไม่สามารถเตรียมรูปภาพได้'
                            );

                            profileEditorSaveButton.disabled =
                                false;

                            return;
                        }

                        const newFile =
                            new File(
                                [blob],
                                'profile_image.jpg', {
                                    type: 'image/jpeg'
                                }
                            );

                        const dataTransfer =
                            new DataTransfer();

                        dataTransfer.items.add(
                            newFile
                        );

                        profileInput.files =
                            dataTransfer.files;

                        profileModal.classList.remove(
                            'active'
                        );

                        /*
                         * ส่งฟอร์มทันที
                         * ไม่ต้องมีปุ่มบันทึกรูปซ้ำบนหน้า
                         */
                        profileImageForm.submit();

                    },
                    'image/jpeg',
                    0.9
                );
            }


            /* =========================
               ESC CLOSE
            ========================= */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        profileModal.classList.contains(
                            'active'
                        )
                    ) {
                        closeProfileEditor();
                    }

                }
            );
        </script>
    @endpush

@endsection
