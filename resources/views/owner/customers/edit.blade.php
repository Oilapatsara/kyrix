@extends('layouts.owner')

@section('title', 'แก้ไขข้อมูลลูกค้า | KYRIX Admin')

@push('styles')
<!-- ฟอนต์ Noto Sans Thai -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --maroon-900: #430d17;
        --maroon-800: #5c1522;
        --maroon-700: #6f1a2b;
        --gold:       #c79a5c;
        --gold-dark:  #a97f45;
        --rose-bg:    #fcf6f7;
        --rose-border:#f3e1e4;
        --rose-text:  #7f2138;
        --ink:        #241417;
        --muted:      #8a7a7d;
        --line:       #e8e0df;
    }

    /* บังคับใช้ฟอนต์ */
    .edit-container, .edit-container * {
        font-family: 'Noto Sans Thai', sans-serif !important;
        box-sizing: border-box;
    }

    .edit-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px 10px 40px;
        color: var(--ink);
    }

    /* --- MAIN CARD (กรอบเดียวกัน) --- */
    .main-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    /* --- HEADER --- */
    .card-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--line);
    }
    .breadcrumb {
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .breadcrumb a { color: var(--muted); text-decoration: none; transition: 0.2s; }
    .breadcrumb a:hover { color: var(--maroon-700); }
    .breadcrumb span { color: var(--gold-dark); margin: 0 4px; }
    
    .card-header h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--maroon-900);
        margin: 0 0 4px;
    }
    .card-header p {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }

    /* --- BUTTONS --- */
    .header-actions {
        display: flex;
        gap: 12px;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .btn svg { width: 18px; height: 18px; }
    
    .btn-outline {
        background: #fff;
        border-color: var(--line);
        color: var(--ink);
    }
    .btn-outline:hover { background: #f9f9f9; border-color: #dcdcdc; }

    .btn-profile {
        background: var(--rose-bg);
        border-color: var(--rose-border);
        color: var(--rose-text);
    }
    .btn-profile:hover { background: #f7e7ea; }

    .btn-save {
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        color: #fff;
        padding: 12px 24px;
        box-shadow: 0 4px 12px rgba(111, 26, 43, .2);
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(111, 26, 43, .3); }

    /* --- PROFILE BANNER (แถบข้อมูลลูกค้า) --- */
    .profile-banner {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        background-color: var(--rose-bg);
        border: 1px solid var(--rose-border);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        gap: 20px;
    }
    .avatar-circle {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--maroon-700), var(--gold));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .profile-info { flex-grow: 1; }
    .profile-info h3 { margin: 0; font-size: 18px; font-weight: 700; color: var(--maroon-900); }
    .profile-info p { margin: 4px 0 0; font-size: 13px; color: var(--gold-dark); font-weight: 600; }
    
    .profile-stats {
        display: flex;
        gap: 24px;
        padding-left: 20px;
        border-left: 1px solid #e6d3d6;
    }
    .stat-box { text-align: left; }
    .stat-box span { display: block; font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
    .stat-box strong { font-size: 14px; color: var(--ink); font-weight: 700; }

    @media (max-width: 640px) {
        .profile-stats { border-left: none; padding-left: 0; width: 100%; border-top: 1px solid #e6d3d6; padding-top: 16px; margin-top: 10px; }
    }

    /* --- FORM --- */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    @media (min-width: 640px) {
        .form-grid { grid-template-columns: 1fr 1fr; }
    }
    
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 8px;
    }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background-color: #fafafa;
        font-size: 14px;
        color: var(--ink);
        transition: 0.2s;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: var(--gold);
        outline: none;
        box-shadow: 0 0 0 3px rgba(199, 154, 92, 0.15);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--line);
    }
</style>
@endpush

@section('content')
<div class="edit-container">

    @php
        $custId = $customer->customer_id ?? $customer->id;
        $firstName = $customer->first_name ?? $customer->name;
        $lastName = $customer->last_name ?? '';
        $rentalsCount = $customer->rentals_count ?? ($customer->rentals ? $customer->rentals->count() : 0);
    @endphp

    <!-- แจ้งเตือน Error ถ้ามี (อยู่บนสุด) -->
    @if ($errors->any())
        <div style="background: #fdf2f2; border: 1px solid #fbd5d5; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; display: flex; gap: 12px; align-items: flex-start;">
            <!-- Icon Alert -->
            <svg style="color: #c81e1e; width: 24px; height: 24px; flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <strong style="color: #9b1c1c; font-size: 14px; display: block; margin-bottom: 4px;">พบข้อผิดพลาด กรุณาตรวจสอบข้อมูลอีกครั้ง</strong>
                <ul style="margin: 0; padding-left: 18px; color: #c81e1e; font-size: 13px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- การ์ดหลัก (ทุกอย่างอยู่ในนี้) -->
    <div class="main-card">
        
        <!-- ส่วนหัว (Header) -->
        <div class="card-header">
            <div>
                <div class="breadcrumb">
                    <a href="{{ route('owner.customers.index') }}">รายชื่อลูกค้า</a> <span>/</span> แก้ไขข้อมูล
                </div>
                <h1>แก้ไขข้อมูลส่วนตัวลูกค้า</h1>
                <p>จัดการข้อมูลติดต่อของลูกค้ารหัส #CUST-{{ str_pad($custId, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            <div class="header-actions">
                @if(Route::has('owner.customers.show'))
                
                @endif
            </div>
        </div>

        <!-- แถบข้อมูลสรุปโปรไฟล์ลูกค้า (Profile Banner) -->
        <div class="profile-banner">
            <div class="avatar-circle">
                {{ mb_substr($firstName, 0, 1) }}{{ mb_substr($lastName, 0, 1) }}
            </div>
            <div class="profile-info">
                <h3>{{ $firstName }} {{ $lastName }}</h3>
                <p>#CUST-{{ str_pad($custId, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="profile-stats">
                <div class="stat-box">
                    <span>วันที่สมัครบัญชี</span>
                    <strong>{{ $customer->created_at ? $customer->created_at->format('d/m/Y') : '-' }}</strong>
                </div>
                <div class="stat-box">
                    <span>ประวัติการเช่า</span>
                    <strong>{{ $rentalsCount }} ครั้ง</strong>
                </div>
            </div>
        </div>

        <!-- ฟอร์มแก้ไขข้อมูล (Form) -->
        <form action="{{ route('owner.customers.update', $custId) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- ชื่อจริง -->
                <div class="form-group">
                    <label for="first_name">ชื่อจริง <span style="color:red;">*</span></label>
                    <input type="text" name="first_name" id="first_name" class="form-control" 
                        value="{{ old('first_name', $firstName) }}" placeholder="เช่น สมชาย" required>
                </div>
                <!-- นามสกุล -->
                <div class="form-group">
                    <label for="last_name">นามสกุล</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" 
                        value="{{ old('last_name', $lastName) }}" placeholder="เช่น ใจดี">
                </div>
                <!-- อีเมล -->
                <div class="form-group">
                    <label for="email">อีเมลระบบ</label>
                    <input type="email" name="email" id="email" class="form-control" 
                        value="{{ old('email', $customer->email) }}" placeholder="example@email.com">
                </div>
                <!-- เบอร์โทรศัพท์ -->
                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label>
                    <input type="text" name="phone" id="phone" class="form-control" 
                        value="{{ old('phone', $customer->phone) }}" placeholder="0812345678">
                </div>
            </div>

            <!-- ปุ่มบันทึก/ยกเลิก (Actions) -->
            <div class="form-actions">
                <a href="{{ route('owner.customers.index') }}" class="btn btn-outline">ยกเลิก</a>
                <button type="submit" class="btn btn-save">
                    <!-- Icon Save -->
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>

    </div>

</div>
@endsection