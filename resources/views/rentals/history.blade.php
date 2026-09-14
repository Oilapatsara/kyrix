@extends('layouts.customer')

@section('title', 'ประวัติการเช่าชุด | KYRIX')

@push('styles')
<style>
    .history-wrap {
        max-width: 1000px;
        margin: 40px auto 80px;
        padding: 0 24px;
    }
    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .history-header h1 {
        font-size: 26px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .history-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .history-card {
        background: #fff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 24px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
        flex-wrap: wrap;
        gap: 16px;
    }
    .history-card:hover {
        border-color: rgba(122,31,43,0.3);
        box-shadow: var(--shadow-md);
    }

    .history-main {
        display: flex;
        gap: 20px;
        align-items: center;
    }
    .history-thumb {
        width: 75px;
        height: 90px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid var(--border);
    }
    .history-code {
        font-family: 'Plus Jakarta Sans', monospace;
        font-size: 20px;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 4px;
    }
    .history-dress-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }
    .history-dates {
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .history-right {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    @media (max-width: 650px) {
        .history-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .history-right {
            text-align: left;
            align-items: flex-start;
            width: 100%;
            border-top: 1px dashed var(--border);
            padding-top: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="history-wrap">
    <div class="history-header">
        <div>
            <h1>
                <i class="fa-solid fa-box-archive" style="color: var(--primary);"></i>
                <span>ประวัติการเช่าชุด (Rental History)</span>
            </h1>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
                ดูรายการเช่าที่ผ่านมา เช่น สถานะ คืนแล้ว หรือ ยกเลิก
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('rentals.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-clock-rotate-left"></i> ดูการเช่าปัจจุบัน
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fa-solid fa-sparkles"></i> เช่าชุดใหม่
            </a>
        </div>
    </div>

    <div class="history-list">
        @forelse($pastRentals as $rental)
            @php
                $firstDetail = $rental->details->first();
                $dressName = $firstDetail->product->product_name ?? 'ชุดเช่า';
                $thumbUrl = $firstDetail->product->main_image_url ?? '';
            @endphp
            <div class="history-card">
                <div class="history-main">
                    @if($thumbUrl)
                        <img src="{{ $thumbUrl }}" class="history-thumb" alt="{{ $dressName }}">
                    @endif
                    <div>
                        <div class="history-code">{{ $rental->formatted_code }}</div>
                        <div class="history-dress-name">
                            {{ $dressName }}
                            @if($rental->details->count() > 1)
                                <span style="font-size: 12px; color: var(--text-muted); font-weight: normal;">
                                    (และอีก {{ $rental->details->count() - 1 }} ชุด)
                                </span>
                            @endif
                        </div>
                        <div class="history-dates">
                            <span><i class="fa-regular fa-calendar-check" style="color: var(--primary);"></i> เช่า {{ date('d/m/Y', strtotime($rental->start_date)) }}</span>
                            <span><i class="fa-regular fa-calendar-xmark" style="color: #b91c1c;"></i> คืน {{ date('d/m/Y', strtotime($rental->end_date)) }}</span>
                        </div>
                    </div>
                </div>

                <div class="history-right">
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted);">สถานะ:</span>
                        <span class="badge {{ $rental->status_badge_class }}" style="font-size: 13px; padding: 4px 12px;">
                            {{ $rental->status_label }}
                        </span>
                    </div>
                    <div style="font-size: 16px; font-weight: 800; color: var(--primary);">
                        ฿{{ number_format($rental->grand_total) }}
                    </div>
                    <a href="{{ route('rentals.show', $rental->rental_id) }}" class="btn btn-secondary btn-sm" style="margin-top: 4px;">
                        ดูรายละเอียด
                    </a>
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 70px 20px; text-align: center;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 48px; color: #cbd5e1; margin-bottom: 14px;"></i>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px;">ยังไม่มีประวัติการเช่าในอดีต</h3>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
                    เมื่อชุดที่คุณเช่าได้รับการตรวจรับคืนแล้ว รายการจะถูกจัดเก็บไว้ที่นี่
                </p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> เลือกดูชุดทั้งหมด
                </a>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 24px; display: flex; justify-content: center;">
        {{ $pastRentals->links() }}
    </div>
</div>
@endsection
