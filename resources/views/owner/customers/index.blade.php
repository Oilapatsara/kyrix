@extends('layouts.owner')

@section('title', 'รายชื่อลูกค้า | KYRIX Admin')

@push('styles')
<!-- ใช้ฟอนต์ Noto Sans Thai ทั้งหน้า -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

    body, h1, h2, h3, h4, h5, h6, p, span, a, button, input, table, div {
        font-family: 'Noto Sans Thai', sans-serif !important;
    }

    .kyrix-admin-container {
        padding: 0;
        color: var(--ink);
        max-width: 1200px;
        margin: 0 auto;
    }

    /* HEADER */
    .admin-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
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
        font-size: 28px;
        font-weight: 700;
        color: var(--maroon-900);
    }

    .admin-heading p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    /* SEARCH BAR CARD */
    .search-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 18px 24px;
        margin-bottom: 24px;
        box-shadow: 0 3px 15px rgba(111, 26, 43, .03);
    }

    .search-input {
        width: 100%;
        height: 42px;
        padding: 0 14px;
        border: 1px solid var(--line);
        border-radius: 9px;
        font-size: 13.5px;
        outline: none;
        transition: .2s;
    }

    .search-input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(199, 154, 92, 0.15);
    }

    .btn-search {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 42px;
        padding: 0 20px;
        border-radius: 9px;
        background: linear-gradient(135deg, var(--maroon-700), var(--maroon-900));
        color: #fff;
        font-size: 13px;
        font-weight: 650;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(111, 26, 43, .2);
        transition: .2s;
    }
    .btn-search:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(111, 26, 43, .3);
    }

    .btn-reset {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 42px;
        padding: 0 18px;
        border-radius: 9px;
        background: #fff;
        color: var(--muted);
        border: 1px solid var(--line);
        font-size: 13px;
        font-weight: 650;
        text-decoration: none;
        transition: .2s;
    }
    .btn-reset:hover {
        background: var(--cream);
        color: var(--ink);
    }

    /* CONTENT CARD & TABLE */
    .content-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 3px 18px rgba(111, 26, 43, .04);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .kyrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .kyrix-table th {
        text-align: left;
        padding: 14px 18px;
        background: var(--cream);
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--line);
    }

    .kyrix-table td {
        padding: 14px 18px;
        border-top: 1px solid var(--line);
        color: #3a2b2e;
        font-size: 13px;
        vertical-align: middle;
    }

    .kyrix-table tr:hover {
        background-color: #fdfbfb;
    }

    /* ACTION BUTTON VIEW */
    .action-btn-view {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 650;
        text-decoration: none;
        background: #fdf8ef;
        color: var(--gold-dark);
        border: 1px solid #f3e6d0;
        transition: .2s ease;
    }
    .action-btn-view:hover {
        background: var(--gold);
        color: #fff;
    }

    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: var(--muted);
    }
    .empty-icon {
        font-size: 36px;
        color: var(--rose-text);
        opacity: .6;
        margin-bottom: 12px;
    }
</style>
@endpush

@section('content')
<div class="kyrix-admin-container">

    <!-- HEADER -->
    <div class="admin-header">
        <div class="admin-heading">
            <span class="eyebrow">KYRIX RENTAL · CUSTOMERS</span>
            <h1>จัดการรายชื่อลูกค้า</h1>
            <p>ตรวจสอบข้อมูลสมาชิก ประวัติการติดต่อ และประวัติการเช่าชุดทั้งหมดในระบบ</p>
        </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="search-card">
        <form action="{{ route('owner.customers.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาด้วยชื่อ, เบอร์โทร หรืออีเมลลูกค้า..." class="search-input">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> ค้นหา
                </button>
                @if(request('search'))
                    <a href="{{ route('owner.customers.index') }}" class="btn-reset">
                        <i class="fa-solid fa-rotate-left"></i> ล้างคำค้น
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLE CARD -->
    <div class="content-card">
        <div class="table-wrapper">
            <table class="kyrix-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">รหัสลูกค้า</th>
                        <th style="width: 28%;">ชื่อ-นามสกุล</th>
                        <th style="width: 32%;">ข้อมูลติดต่อ</th>
                        <th style="width: 12%; text-align: center;">วันที่สมัคร</th>
                        <th style="width: 13%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    @php
                        $custId = $customer->customer_id ?? $customer->id;
                        $custName = $customer->name ?? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: 'ไม่ระบุชื่อ';
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: var(--maroon-900);">CUST-{{ str_pad($custId, 4, '0', STR_PAD_LEFT) }}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #2d1e21;">{{ $custName }}</div>
                        </td>
                        <td>
                            <div style="font-size: 12px; color: #555;">
                                <i class="fa-solid fa-phone mr-1" style="color: var(--gold-dark);"></i> {{ $customer->phone ?? '-' }}
                            </div>
                            <div style="font-size: 12px; color: var(--muted); margin-top: 3px;">
                                <i class="fa-regular fa-envelope mr-1" style="color: var(--gold-dark);"></i> {{ $customer->email ?? '-' }}
                            </div>
                        </td>
                        <td style="text-align: center; font-size: 12px; color: #555;">
                            {{ $customer->created_at ? $customer->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('owner.customers.show', $custId) }}" class="action-btn-view">
                                <i class="fa-regular fa-eye"></i> ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-users-slash"></i></div>
                                <div style="font-weight: 600; font-size: 14px; color: var(--maroon-900);">ไม่พบข้อมูลลูกค้าในระบบ</div>
                                <div style="font-size: 12.5px; margin-top: 4px;">ลองเปลี่ยนคำค้นหาใหม่อีกครั้ง หรือตรวจสอบรายชื่อสมาชิกทั้งหมด</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if(method_exists($customers, 'hasPages') && $customers->hasPages())
            <div style="padding: 16px 20px; background: var(--cream); border-top: 1px solid var(--line);">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection