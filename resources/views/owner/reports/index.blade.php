@extends('layouts.owner')

@section('title', 'รายงานร้าน | KYRIX Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">รายงานสรุปผลประกอบการ</h2>
            <p class="text-sm text-gray-500">วิเคราะห์รายได้ ยอดจอง และสถิติต่างๆ ของร้าน</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-lg shadow border-l-4" style="border-left-color: #430d17;">
            <p class="text-sm text-gray-500">รายได้รวมทั้งหมด</p>
            <p class="text-2xl font-bold text-gray-800">฿{{ number_format($totalRevenue ?? 0, 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow border-l-4" style="border-left-color: #c79a5c;">
            <p class="text-sm text-gray-500">รายได้เดือนนี้</p>
            <p class="text-2xl font-bold text-gray-800">฿{{ number_format($monthRevenue ?? 0, 2) }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-sm text-gray-500">เงินมัดจำค้ำประกันคงค้าง</p>
            <p class="text-2xl font-bold text-gray-800">฿{{ number_format($activeDeposits ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Grid: Top Dresses & Category Revenues -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        
        <!-- Top Dresses -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-shirt text-maroon-800"></i> ชุดยอดนิยมสูงสุด
            </h3>
            <ul class="divide-y divide-gray-200">
                @forelse($topDresses ?? [] as $dress)
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-medium text-gray-700">{{ $dress->product_name ?? $dress->name ?? '-' }}</span>
                        <span class="bg-rose-100 text-rose-800 text-xs px-2.5 py-1 rounded-full font-semibold">
                            เช่า {{ $dress->rental_count ?? $dress->bookings_count ?? 0 }} ครั้ง
                        </span>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-400">ยังไม่มีข้อมูลชุดยอดนิยม</li>
                @endforelse
            </ul>
        </div>

        <!-- Category Revenues -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-maroon-800"></i> รายได้ตามประเภทชุด
            </h3>
            <ul class="divide-y divide-gray-200">
                @forelse($categoryRevenues ?? [] as $cat)
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-medium text-gray-700">{{ $cat->category_name ?? $cat->name ?? 'หมวดหมู่ทั่วไป' }}</span>
                        <span class="font-bold text-gray-900">฿{{ number_format($cat->total_revenue ?? 0, 2) }}</span>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-400">ยังไม่มีข้อมูลรายได้ตามประเภท</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>
@endsection