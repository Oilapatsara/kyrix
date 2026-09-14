@extends('layouts.owner')

@section('title', 'จัดการรายการเช่า | KYRIX Admin')

@push('styles')
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
    }

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
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .kyrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-pending { background: #fff5dd; color: #9a6b00; }
    .status-confirmed { background: #edf5ff; color: #4773a6; }
    .status-renting { background: var(--rose-bg); color: var(--rose-text); }
    .status-returned { background: #eef7f7; color: #477f80; }
    .status-completed { background: #eef7ef; color: #4f7e53; }
    .status-cancelled { background: #f5f5f5; color: #888; }
    .status-default { background: #f5f5f5; color: #777; }

    .action-btn-view {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 650;
        text-decoration: none;
        background: #fdf8ef;
        color: var(--gold-dark);
        border: 1px solid #f3e6d0;
        transition: .2s ease;
    }
    .action-btn-view:hover { background: #f7ecd9; }

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
            <span class="eyebrow">KYRIX RENTAL · BOOKINGS</span>
            <h1>จัดการรายการเช่าชุด</h1>
            <p>ตรวจสอบและอัปเดตสถานะการจองและการเช่าชุดของลูกค้าทั้งหมดในระบบ</p>
        </div>
    </div>

    <!-- CONTENT CARD -->
    <div class="content-card">
        <div class="table-wrapper">
            <table class="kyrix-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">เลขที่เช่า</th>
                        <th style="width: 25%;">ลูกค้า</th>
                        <th style="width: 25%; text-align: center;">วันรับ - วันคืน</th>
                        <th style="width: 15%; text-align: center;">ยอดรวม</th>
                        <th style="width: 10%; text-align: center;">สถานะ</th>
                        <th style="width: 10%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    @php
                        $statusCode = strtolower($booking->status ?? 'pending');
                        $statusText = match($statusCode) {
                            'pending'   => 'รอการยืนยัน',
                            'confirmed' => 'ยืนยันแล้ว',
                            'renting'   => 'กำลังเช่า',
                            'returned'  => 'คืนชุดแล้ว',
                            'completed' => 'เสร็จสิ้น',
                            'cancelled' => 'ยกเลิก',
                            default     => ucfirst($booking->status)
                        };
                        $statusClass = match($statusCode) {
                            'pending'   => 'status-pending',
                            'confirmed' => 'status-confirmed',
                            'renting'   => 'status-renting',
                            'returned'  => 'status-returned',
                            'completed' => 'status-completed',
                            'cancelled' => 'status-cancelled',
                            default     => 'status-default'
                        };
                        $bookingId = $booking->rental_id;

                        // ดึงชื่อและนามสกุลจากตาราง customers[cite: 1]
                        $firstName = trim($booking->customer->first_name ?? '');
                        $lastName = trim($booking->customer->last_name ?? '');
                        $fullName = trim($firstName . ' ' . $lastName);
                        
                        if ($fullName === '') {
                            $fullName = $booking->customer_name ?? 'ไม่ระบุชื่อ';
                        }
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: var(--maroon-900);">
                                {{ $booking->rental_code ?? ('#' . $bookingId) }}
                            </strong>
                        </td>
                        <td>
                            <div style="font-weight: 650; color: #2d1e21;">
                                {{ $fullName }}
                            </div>
                            @if(!empty($booking->customer->phone))
                                <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                    <i class="fa-solid fa-phone mr-1"></i> {{ $booking->customer->phone }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center; font-size: 12px; color: #555;">
                            <div>{{ !empty($booking->start_date) ? \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') : '-' }}</div>
                            <div style="font-size: 10px; color: var(--muted);">ถึง</div>
                            <div>{{ !empty($booking->end_date) ? \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') : '-' }}</div>
                        </td>
                        <td style="text-align: center; font-weight: 750; color: var(--maroon-900);">
                            ฿{{ number_format($booking->total_amount ?? 0, 2) }}
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('owner.bookings.show', $bookingId) }}" class="action-btn-view">
                                <i class="fa-regular fa-eye"></i> ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
                                <div style="font-weight: 600; font-size: 14px; color: var(--maroon-900);">ยังไม่มีรายการเช่าในระบบ</div>
                                <div style="font-size: 12px; margin-top: 4px;">เมื่อลูกค้าทำรายการจองหรือเช่าชุด ข้อมูลจะปรากฏขึ้นที่หน้านี้อัตโนมัติ</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if(method_exists($bookings, 'hasPages') && $bookings->hasPages())
            <div style="padding: 16px 20px; background: var(--cream); border-top: 1px solid var(--line);">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection