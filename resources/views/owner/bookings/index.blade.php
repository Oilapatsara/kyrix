@extends('layouts.owner')

@section('title', 'จัดการรายการเช่า | KYRIX Admin')

@push('styles')
    <style>
        :root {
            --maroon-900: #430d17;
            --maroon-800: #5c1522;
            --maroon-700: #6f1a2b;
            --gold: #c79a5c;
            --gold-dark: #a97f45;
            --rose-bg: #f7e7ea;
            --rose-text: #7f2138;
            --cream: #faf7f4;
            --ink: #241417;
            --muted: #8a7a7d;
            --line: #efe6e4;
            --success-bg: #eef7ef;
            --success-text: #4f7e53;
            --danger-bg: #fff1f2;
            --danger-text: #b42318;
            --danger-line: #fecdd3;
        }

        .kyrix-admin-container {
            padding: 0;
            color: var(--ink);
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

        /* CARD */
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

        /* TABLE */
        .kyrix-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 780px;
        }

        .kyrix-table th {
            text-align: left;
            padding: 14px 18px;
            background: var(--cream);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid var(--line);
        }

        .kyrix-table td {
            padding: 14px 18px;
            border-top: 1px solid var(--line);
            color: #3a2b2e;
            font-size: 13px;
            vertical-align: middle;
        }

        .kyrix-table tbody tr {
            transition: background-color .18s ease;
        }

        .kyrix-table tbody tr:hover {
            background-color: #fdfbfb;
        }

        /* STATUS */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pending {
            background: #fff5dd;
            color: #9a6b00;
        }

        .status-confirmed {
            background: #edf5ff;
            color: #4773a6;
        }

        .status-renting {
            background: var(--rose-bg);
            color: var(--rose-text);
        }

        .status-returned {
            background: #eef7f7;
            color: #477f80;
        }

        .status-completed {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .status-cancelled {
            background: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid var(--danger-line);
        }

        .status-default {
            background: #f5f5f5;
            color: #777;
        }

        /* VIEW BUTTON */
        .action-btn-view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 12px;
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
            background: #f7ecd9;
            border-color: #e8d4b2;
            transform: translateY(-1px);
        }

        /* EMPTY */
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

        /* PAGINATION */
        .pagination-area {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 20px;
            background: var(--cream);
            border-top: 1px solid var(--line);
            flex-wrap: wrap;
        }

        .pagination-summary {
            color: var(--muted);
            font-size: 12px;
            font-weight: 500;
        }

        .pagination-summary strong {
            color: var(--maroon-900);
            font-weight: 700;
        }

        .pagination-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        .pagination-link,
        .pagination-page {
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 9px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--maroon-800);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            transition: all .18s ease;
            box-sizing: border-box;
        }

        .pagination-link:hover,
        .pagination-page:hover {
            border-color: var(--gold);
            background: #fffaf4;
            color: var(--maroon-900);
            transform: translateY(-1px);
        }

        .pagination-page.active {
            background: linear-gradient(135deg,
                    var(--maroon-700),
                    var(--maroon-900));
            border-color: var(--maroon-900);
            color: #fff;
            box-shadow: 0 4px 10px rgba(67, 13, 23, .16);
        }

        .pagination-link.disabled {
            background: #f5f1ee;
            color: #b2a6a3;
            border-color: #eee7e4;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-dots {
            min-width: 28px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        /* RESPONSIVE */
        @media (max-width: 760px) {
            .pagination-area {
                align-items: flex-start;
                flex-direction: column;
            }

            .pagination-nav {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .admin-heading h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {

            .pagination-link.previous-text,
            .pagination-link.next-text {
                font-size: 0;
                width: 36px;
                padding: 0;
            }

            .pagination-link.previous-text i,
            .pagination-link.next-text i {
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="kyrix-admin-container">

        {{-- HEADER --}}
        <div class="admin-header">
            <div class="admin-heading">
                <span class="eyebrow">
                    KYRIX RENTAL · BOOKINGS
                </span>

                <h1>
                    จัดการรายการเช่าชุด
                </h1>

                <p>
                    ตรวจสอบและอัปเดตสถานะการจองและการเช่าชุดของลูกค้าทั้งหมดในระบบ
                </p>
            </div>
        </div>

        {{-- CONTENT CARD --}}
        <div class="content-card">
            <div class="table-wrapper">
                <table class="kyrix-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">
                                เลขที่เช่า
                            </th>

                            <th style="width: 25%;">
                                ลูกค้า
                            </th>

                            <th style="width: 25%; text-align: center;">
                                วันรับ - วันคืน
                            </th>

                            <th style="width: 15%; text-align: center;">
                                ยอดรวม
                            </th>

                            <th style="width: 10%; text-align: center;">
                                สถานะ
                            </th>

                            <th style="width: 10%; text-align: center;">
                                จัดการ
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $statusCode = strtolower($booking->status ?? 'pending');

                                $statusText = match ($statusCode) {
                                    'pending', 'pending_payment' => 'รอชำระเงิน',

                                    'pending_verification' => 'รอตรวจสอบสลิป',

                                    'confirmed' => 'ยืนยันแล้ว',

                                    'ready_pickup' => 'รอรับชุด',

                                    'renting' => 'กำลังเช่า',

                                    'pending_return' => 'รอตรวจรับคืน',

                                    'returned' => 'คืนชุดแล้ว',

                                    'completed' => 'เสร็จสิ้น',

                                    'cancelled' => 'ยกเลิกแล้ว',

                                    default => ucfirst($booking->status ?? '-'),
                                };

                                $statusClass = match ($statusCode) {
                                    'pending', 'pending_payment', 'pending_verification' => 'status-pending',

                                    'confirmed', 'ready_pickup' => 'status-confirmed',

                                    'renting' => 'status-renting',

                                    'pending_return' => 'status-pending',

                                    'returned' => 'status-returned',

                                    'completed' => 'status-completed',

                                    'cancelled' => 'status-cancelled',

                                    default => 'status-default',
                                };

                                $bookingId = $booking->rental_id;

                                // ชื่อลูกค้า
                                $firstName = trim($booking->customer->first_name ?? '');

                                $lastName = trim($booking->customer->last_name ?? '');

                                $fullName = trim($firstName . ' ' . $lastName);

                                if ($fullName === '') {
                                    $fullName = $booking->customer_name ?? 'ไม่ระบุชื่อ';
                                }
                            @endphp

                            <tr>

                                {{-- RENTAL CODE --}}
                                <td>
                                    <strong
                                        style="
                                            color: var(--maroon-900);
                                        ">
                                        {{ $booking->rental_code ?? '#' . $bookingId }}
                                    </strong>
                                </td>

                                {{-- CUSTOMER --}}
                                <td>
                                    <div
                                        style="
                                            font-weight: 650;
                                            color: #2d1e21;
                                        ">
                                        {{ $fullName }}
                                    </div>

                                    @if (!empty($booking->customer->phone))
                                        <div
                                            style="
                                                font-size: 11px;
                                                color: var(--muted);
                                                margin-top: 2px;
                                            ">
                                            <i class="fa-solid fa-phone"></i>
                                            {{ $booking->customer->phone }}
                                        </div>
                                    @endif
                                </td>

                                {{-- DATES --}}
                                <td
                                    style="
                                        text-align: center;
                                        font-size: 12px;
                                        color: #555;
                                    ">
                                    <div>
                                        {{ !empty($booking->start_date) ? \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') : '-' }}
                                    </div>

                                    <div
                                        style="
                                            font-size: 10px;
                                            color: var(--muted);
                                            margin: 2px 0;
                                        ">
                                        ถึง
                                    </div>

                                    <div>
                                        {{ !empty($booking->end_date) ? \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                {{-- TOTAL --}}
                                <td
                                    style="
                                        text-align: center;
                                        font-weight: 750;
                                        color: var(--maroon-900);
                                    ">
                                    ฿{{ number_format($booking->grand_total ?? 0, 2) }}

                                    @if (!empty($booking->discount_amount) && $booking->discount_amount > 0)
                                        <div
                                            style="
                                                font-size: 11px;
                                                color: #16a34a;
                                                font-weight: 600;
                                                margin-top: 2px;
                                            ">
                                            <i class="fa-solid fa-gift"></i>
                                            ลด
                                            {{ $booking->discount_reason ?? 'โปรโมชั่น' }}
                                            (-฿{{ number_format($booking->discount_amount, 0) }})
                                        </div>
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td style="text-align: center;">
                                    <span class="status-badge {{ $statusClass }}">

                                        @if ($statusCode === 'cancelled')
                                            <i class="fa-solid fa-circle-xmark"></i>
                                        @elseif ($statusCode === 'completed')
                                            <i class="fa-solid fa-circle-check"></i>
                                        @elseif ($statusCode === 'confirmed' || $statusCode === 'ready_pickup')
                                            <i class="fa-solid fa-check"></i>
                                        @elseif ($statusCode === 'renting')
                                            <i class="fa-solid fa-person-walking"></i>
                                        @elseif ($statusCode === 'returned')
                                            <i class="fa-solid fa-box"></i>
                                        @else
                                            <i class="fa-solid fa-clock"></i>
                                        @endif

                                        {{ $statusText }}
                                    </span>
                                </td>

                                {{-- ACTION --}}
                                <td style="text-align: center;">
                                    <a href="{{ route('owner.bookings.show', $bookingId) }}" class="action-btn-view">
                                        <i class="fa-regular fa-eye"></i>
                                        ดูรายละเอียด
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">

                                        <div class="empty-icon">
                                            <i class="fa-regular fa-calendar-xmark"></i>
                                        </div>

                                        <div
                                            style="
                                                font-weight: 600;
                                                font-size: 14px;
                                                color: var(--maroon-900);
                                            ">
                                            ยังไม่มีรายการเช่าในระบบ
                                        </div>

                                        <div
                                            style="
                                                font-size: 12px;
                                                margin-top: 4px;
                                            ">
                                            เมื่อลูกค้าทำรายการจองหรือเช่าชุด
                                            ข้อมูลจะปรากฏขึ้นที่หน้านี้อัตโนมัติ
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CUSTOM PAGINATION --}}
            @if (method_exists($bookings, 'hasPages') && $bookings->hasPages())

                @php
                    $currentPage = $bookings->currentPage();
                    $lastPage = $bookings->lastPage();
                    $firstItem = $bookings->firstItem();
                    $lastItem = $bookings->lastItem();
                    $totalItems = $bookings->total();

                    $pageStart = max(1, $currentPage - 2);

                    $pageEnd = min($lastPage, $currentPage + 2);

                    if ($currentPage <= 3) {
                        $pageEnd = min($lastPage, 5);
                    }

                    if ($currentPage >= $lastPage - 2) {
                        $pageStart = max(1, $lastPage - 4);
                    }
                @endphp

                <div class="pagination-area">

                    {{-- SUMMARY --}}
                    <div class="pagination-summary">
                        แสดง

                        <strong>
                            {{ $firstItem ?? 0 }}
                        </strong>

                        ถึง

                        <strong>
                            {{ $lastItem ?? 0 }}
                        </strong>

                        จาก

                        <strong>
                            {{ $totalItems }}
                        </strong>

                        รายการ
                    </div>

                    {{-- NAVIGATION --}}
                    <nav class="pagination-nav" aria-label="Pagination">

                        {{-- PREVIOUS --}}
                        @if ($bookings->onFirstPage())
                            <span class="pagination-link previous-text disabled" aria-disabled="true">
                                <i class="fa-solid fa-chevron-left"></i>
                                <span>ก่อนหน้า</span>
                            </span>
                        @else
                            <a href="{{ $bookings->previousPageUrl() }}" class="pagination-link previous-text"
                                aria-label="หน้าก่อนหน้า">
                                <i class="fa-solid fa-chevron-left"></i>
                                <span>ก่อนหน้า</span>
                            </a>
                        @endif

                        {{-- FIRST PAGE --}}
                        @if ($pageStart > 1)

                            <a href="{{ $bookings->url(1) }}" class="pagination-page">
                                1
                            </a>

                            @if ($pageStart > 2)
                                <span class="pagination-dots">
                                    ...
                                </span>
                            @endif

                        @endif

                        {{-- PAGE NUMBERS --}}
                        @for ($page = $pageStart; $page <= $pageEnd; $page++)
                            @if ($page == $currentPage)
                                <span class="pagination-page active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $bookings->url($page) }}" class="pagination-page">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        {{-- LAST PAGE --}}
                        @if ($pageEnd < $lastPage)

                            @if ($pageEnd < $lastPage - 1)
                                <span class="pagination-dots">
                                    ...
                                </span>
                            @endif

                            <a href="{{ $bookings->url($lastPage) }}" class="pagination-page">
                                {{ $lastPage }}
                            </a>

                        @endif

                        {{-- NEXT --}}
                        @if ($bookings->hasMorePages())
                            <a href="{{ $bookings->nextPageUrl() }}" class="pagination-link next-text"
                                aria-label="หน้าถัดไป">
                                <span>ถัดไป</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="pagination-link next-text disabled" aria-disabled="true">
                                <span>ถัดไป</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        @endif

                    </nav>
                </div>
            @elseif (method_exists($bookings, 'total') && $bookings->total() > 0)
                {{-- กรณีมีข้อมูลหน้าเดียว --}}
                <div class="pagination-area">

                    <div class="pagination-summary">
                        แสดง

                        <strong>
                            {{ $bookings->firstItem() ?? 1 }}
                        </strong>

                        ถึง

                        <strong>
                            {{ $bookings->lastItem() ?? $bookings->total() }}
                        </strong>

                        จาก

                        <strong>
                            {{ $bookings->total() }}
                        </strong>

                        รายการ
                    </div>

                </div>

            @endif
        </div>
    </div>
@endsection
