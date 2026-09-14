@extends('layouts.customer')
@section('title', 'แดชบอร์ดบัญชีของฉัน | KYRIX')
@push('styles')
    <style>
        .dash-wrap {
            max-width: 1200px;
            margin: 40px auto 80px;
            padding: 0 24px
        }

        .welcome-banner {
            background: linear-gradient(135deg, #7a1f2b 0%, #4a131b 100%);
            color: #fff;
            border-radius: var(--radius-lg);
            padding: 36px 40px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px
        }

        .welcome-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px
        }

        .welcome-sub {
            color: #f1dfdf;
            font-size: 15px;
            margin: 0
        }

        .shortcuts-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 36px
        }

        .shortcut-card {
            background: #fff;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: all .3s;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
            text-decoration: none
        }

        .shortcut-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary)
        }

        .shortcut-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px
        }

        .shortcut-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main)
        }

        .shortcut-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
            margin: 0
        }

        .active-rentals-box {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 30px;
            box-shadow: var(--shadow-sm)
        }

        .box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border)
        }

        @media(max-width:900px) {
            .shortcuts-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(max-width:500px) {
            .shortcuts-grid {
                grid-template-columns: 1fr
            }

            .welcome-banner {
                padding: 24px
            }
        }
    </style>
@endpush
@section('content')
    @php
        use App\Models\Customer;
        $customerId = session('customer_id');
        $customer = $customerId ? Customer::find($customerId) : null;
        $currentRentals = $customer
            ? $customer
                ->rentals()
                ->whereNotIn('status', ['returned', 'cancelled'])
                ->latest()
                ->take(3)
                ->get()
            : collect();
        $statusLabels = [
            'pending' => 'รอดำเนินการ',
            'confirmed' => 'ยืนยันการเช่า',
            'renting' => 'กำลังเช่า',
            'returned' => 'คืนชุดแล้ว',
            'cancelled' => 'ยกเลิก',
        ];
        $statusClasses = [
            'pending' => 'badge-warning',
            'confirmed' => 'badge-success',
            'renting' => 'badge-info',
            'returned' => 'badge-success',
            'cancelled' => 'badge-danger',
        ];
    @endphp

    <div class="dash-wrap">
        <div class="welcome-banner">
            <div>
                <span class="badge badge-warning"
                    style="margin-bottom:10px;background:rgba(255,255,255,.2);color:#fff;">CUSTOMER MEMBER</span>
                <h1 class="welcome-title">ยินดีต้อนรับ,
                    {{ $customer ? trim($customer->first_name . ' ' . $customer->last_name) : 'ลูกค้า' }}</h1>
                <p class="welcome-sub">พร้อมเลือกชุดสวยสำหรับวันพิเศษของคุณ จัดการการเช่า ตรวจสอบสถานะ และดูประวัติได้ที่นี่
                </p>
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <a href="{{ route('products.index') }}" class="btn btn-gold" style="padding:12px 24px;">
                    <i class="fa-solid fa-magnifying-glass"></i> ค้นหาชุดเช่า
                </a>
                <a href="{{ route('rentals.index') }}" class="btn btn-secondary"
                    style="border-color:#fff;background:rgba(255,255,255,.15);color:#fff;">
                    <i class="fa-solid fa-clock-rotate-left"></i> การเช่าของฉัน
                </a>
            </div>
        </div>

        <div class="shortcuts-grid">
            <a href="{{ route('products.index') }}" class="shortcut-card">
                <div class="shortcut-icon"><i class="fa-solid fa-vest-patches"></i></div>
                <div>
                    <h4 class="shortcut-title">ชุดทั้งหมด</h4>
                    <p class="shortcut-desc">เลือกชมชุดและดูรายละเอียด ราคา ไซซ์ สี และสถานะของชุด</p>
                </div>
            </a>

            <a href="{{ route('rentals.index') }}" class="shortcut-card">
                <div class="shortcut-icon"><i class="fa-solid fa-timeline"></i></div>
                <div>
                    <h4 class="shortcut-title">การเช่าของฉัน</h4>
                    <p class="shortcut-desc">ติดตามสถานะการเช่า ดูรายละเอียด และแนบหลักฐานการชำระเงิน</p>
                </div>
            </a>

            <a href="{{ route('rentals.history') }}" class="shortcut-card">
                <div class="shortcut-icon"><i class="fa-solid fa-box-archive"></i></div>
                <div>
                    <h4 class="shortcut-title">ประวัติการเช่า</h4>
                    <p class="shortcut-desc">ดูรายการเช่าที่เสร็จสิ้นและประวัติการใช้งานของคุณ</p>
                </div>
            </a>

            <a href="{{ route('profile.index') }}" class="shortcut-card">
                <div class="shortcut-icon"><i class="fa-solid fa-user-gear"></i></div>
                <div>
                    <h4 class="shortcut-title">ข้อมูลส่วนตัว</h4>
                    <p class="shortcut-desc">แก้ไขชื่อ เบอร์โทรศัพท์ ที่อยู่ และรหัสผ่านของคุณ</p>
                </div>
            </a>
        </div>

        <div class="active-rentals-box">
            <div class="box-header">
                <h3 style="font-size:18px;font-weight:800;color:var(--text-main);margin:0;">
                    <i class="fa-solid fa-spinner fa-spin" style="color:var(--primary);margin-right:8px;"></i>
                    รายการเช่าที่กำลังดำเนินการ ({{ $currentRentals->count() }})
                </h3>
                <a href="{{ route('rentals.index') }}" class="view-all-link">ดูทั้งหมด <i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>

            @if ($currentRentals->count() > 0)
                <div style="display:flex;flex-direction:column;gap:16px;">
                    @foreach ($currentRentals as $rental)
                        @php
                            $status = $rental->status;
                            $statusLabel = $statusLabels[$status] ?? $status;
                            $statusClass = $statusClasses[$status] ?? 'badge-warning';
                            $total = $rental->total_amount ?? 0;
                        @endphp

                        <div
                            style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:#faf8f5;border-radius:10px;border:1px solid var(--border);flex-wrap:wrap;gap:14px;">
                            <div>
                                <strong style="font-family:'Plus Jakarta Sans',monospace;color:var(--primary);">
                                    #KYRIX-{{ str_pad($rental->rental_id, 5, '0', STR_PAD_LEFT) }}
                                </strong>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">
                                    รับ: {{ \Carbon\Carbon::parse($rental->start_date)->format('d/m/Y') }}
                                    - คืน: {{ \Carbon\Carbon::parse($rental->end_date)->format('d/m/Y') }}
                                </div>
                            </div>

                            <div>
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div>
                                <span style="font-weight:800;color:var(--primary);font-size:16px;">
                                    ฿{{ number_format($total, 2) }}
                                </span>
                            </div>

                            <div>
                                <a href="{{ route('rentals.show', $rental->rental_id) }}" class="btn btn-secondary btn-sm">
                                    ตรวจสอบ <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:30px;color:var(--text-muted);">
                    <i class="fa-solid fa-circle-check" style="font-size:36px;color:#16a34a;margin-bottom:10px;"></i>
                    <p style="font-size:14px;margin:0;">ไม่มีรายการเช่าที่กำลังดำเนินการ คุณสามารถเลือกชมชุดสวย ๆ ได้ทันที
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
