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
        margin-bottom: 6px;
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
        margin-bottom: 20px;
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
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }

    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-wrap">
    <div class="profile-header">
        <h1>
            <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i>
            <span>ข้อมูลส่วนตัว & บัญชีของฉัน</span>
        </h1>
        <p style="color: var(--text-muted); font-size: 14px;">จัดการข้อมูลโปรไฟล์ ที่อยู่สำหรับจัดส่งชุด และเปลี่ยนรหัสผ่านความปลอดภัย</p>
    </div>

    <!-- Stats Bar -->
    <div class="stats-row">
        <div class="stat-card">
            <h3>{{ $stats['total_rentals'] }}</h3>
            <p>รายการเช่าทั้งหมด</p>
        </div>
        <div class="stat-card">
            <h3 style="color: #0284c7;">{{ $stats['active_rentals'] }}</h3>
            <p>รายการกำลังดำเนินการ</p>
        </div>
        <div class="stat-card">
            <h3 style="color: #16a34a;">{{ $stats['completed_rentals'] }}</h3>
            <p>รายการคืนชุดสำเร็จ</p>
        </div>
        <div class="stat-card">
            <h3 style="color: var(--gold);">{{ $stats['reviews_count'] }}</h3>
            <p>รีวิวที่เขียนแล้ว</p>
        </div>
    </div>

    <div class="profile-grid">
        <!-- 1. Edit Profile Form (แก้ชื่อ, แก้เบอร์โทร, แก้อีเมล, แก้ที่อยู่) -->
        <div class="profile-card">
            <h3 class="card-title">
                <i class="fa-solid fa-address-card"></i>
                <span>ข้อมูลผู้ใช้งาน & ที่อยู่จัดส่ง</span>
            </h3>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">ชื่อ - นามสกุล *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', trim($customer->first_name . ' ' . $customer->last_name)) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">อีเมล (Email) *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">เบอร์โทรศัพท์ติดต่อ *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" placeholder="เช่น 089-123-4567">
                </div>

                <div class="form-group">
                    <label class="form-label">ที่อยู่สำหรับจัดส่งชุดเริ่มต้น</label>
                    <textarea name="address" class="form-control" rows="4" placeholder="บ้านเลขที่, ถนน, ซอย, เขต/อำเภอ, จังหวัด, รหัสไปรษณีย์">{{ old('address', $customer->address) }}</textarea>
                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">
                        * ข้อมูลที่อยู่นี้จะถูกนำไปกรอกอัตโนมัติเมื่อทำรายการเช่าชุด
                    </span>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 12px;">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกข้อมูลส่วนตัว
                </button>
            </form>
        </div>

        <!-- 2. Change Password Form (เปลี่ยนรหัสผ่าน) -->
        <div class="profile-card">
            <h3 class="card-title">
                <i class="fa-solid fa-lock"></i>
                <span>เปลี่ยนรหัสผ่าน (Change Password)</span>
            </h3>

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">รหัสผ่านปัจจุบัน *</label>
                    <input type="password" name="current_password" class="form-control" placeholder="กรอกรหัสผ่านปัจจุบัน" required>
                    @error('current_password')
                        <span style="color: #b91c1c; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร) *</label>
                    <input type="password" name="password" class="form-control" placeholder="กรอกรหัสผ่านใหม่" required>
                    @error('password')
                        <span style="color: #b91c1c; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">ยืนยันรหัสผ่านใหม่อีกครั้ง *</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่านใหม่อีกครั้ง" required>
                </div>

                <div style="background: #faf8f5; border: 1px solid var(--border); padding: 12px; border-radius: 8px; font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">
                    <i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i> เพื่อความปลอดภัย แนะนำให้ใช้รหัสผ่านที่มีทั้งตัวอักษรและตัวเลขผสมกัน
                </div>

                <button type="submit" class="btn btn-secondary btn-block" style="padding: 12px;">
                    <i class="fa-solid fa-key"></i> เปลี่ยนรหัสผ่านใหม่
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
