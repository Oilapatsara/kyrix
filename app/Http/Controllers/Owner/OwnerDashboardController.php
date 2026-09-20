<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | 1. Revenue calculations
        |--------------------------------------------------------------------------
        | ตาราง payments ใช้คอลัมน์ amount
        */

        $todayRevenue = (float) Payment::where('status', 'approved')
            ->whereDate('paid_at', $today)
            ->sum('amount');

        $monthlyRevenue = (float) Payment::where('status', 'approved')
            ->whereBetween('paid_at', [
                $startOfMonth,
                $endOfMonth,
            ])
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Fallback
        |--------------------------------------------------------------------------
        | ถ้าเดือนนี้ยังไม่มีรายได้ ให้ใช้ยอดรวม approved ทั้งหมด
        */

        if ($monthlyRevenue == 0) {
            $monthlyRevenue = (float) Payment::where('status', 'approved')
                ->sum('amount');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Booking Counts
        |--------------------------------------------------------------------------
        */

        $totalBookings = Rental::count();

        $activeRentals = Rental::whereIn('status', [
            'renting',
            'pending_return',
        ])->count();

        $pendingBookings = Rental::whereIn('status', [
            'pending',
            'pending_payment',
            'pending_verification',
        ])->count();

        /*
        |--------------------------------------------------------------------------
        | 3. Dress & Customer Counts
        |--------------------------------------------------------------------------
        */

        $availableDresses = Product::where(
            'status',
            'available'
        )->count();

        $totalDresses = Product::count();

        $totalCustomers = Customer::count();

        /*
        |--------------------------------------------------------------------------
        | Dashboard Stats
        |--------------------------------------------------------------------------
        */

        $stats = [
            'monthly_revenue' => $monthlyRevenue,
            'today_revenue' => $todayRevenue,
            'total_bookings' => $totalBookings,
            'active_rentals' => $activeRentals,
            'pending_bookings' => $pendingBookings,
            'available_dresses' => $availableDresses,
            'total_dresses' => $totalDresses,
            'total_customers' => $totalCustomers,
        ];

        /*
        |--------------------------------------------------------------------------
        | 4. Recent Bookings
        |--------------------------------------------------------------------------
        */

        $recentBookings = Rental::with([
            'customer',
            'details.product',
        ])
            ->latest('rental_id')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 5. Upcoming Returns
        |--------------------------------------------------------------------------
        */

        $upcomingReturns = Rental::with([
            'customer',
            'details.product',
        ])
            ->whereIn('status', [
                'renting',
                'pending_return',
            ])
            ->orderBy('end_date', 'asc')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ถ้ายังไม่มีรายการที่กำลังเช่า
        | ให้แสดงรายการเช่าล่าสุดแทน
        |--------------------------------------------------------------------------
        */

        if ($upcomingReturns->isEmpty()) {
            $upcomingReturns = Rental::with([
                'customer',
                'details.product',
            ])
                ->latest('rental_id')
                ->take(3)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Pending Payments
        |--------------------------------------------------------------------------
        */

        $pendingPayments = Payment::with([
            'rental.customer',
        ])
            ->where('status', 'pending')
            ->latest('payment_id')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 7. Popular Dresses
        |--------------------------------------------------------------------------
        */

        $popularDresses = Product::with([
            'images',
            'category',
        ])
            ->orderByDesc('rental_count')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 8. Monthly Revenue Chart
        |--------------------------------------------------------------------------
        */

        $monthlyLabels = [
            'ม.ค.',
            'ก.พ.',
            'มี.ค.',
            'เม.ย.',
            'พ.ค.',
            'มิ.ย.',
            'ก.ค.',
            'ส.ค.',
            'ก.ย.',
            'ต.ค.',
            'พ.ย.',
            'ธ.ค.',
        ];

        $monthlyData = array_fill(0, 12, 0.0);

        $currentYear = Carbon::now()->year;

        /*
        |--------------------------------------------------------------------------
        | สำคัญ:
        | payments ใช้ amount ไม่ใช่ total_amount
        |--------------------------------------------------------------------------
        */

        $paymentsByMonth = Payment::where(
            'status',
            'approved'
        )
            ->whereYear('paid_at', $currentYear)
            ->selectRaw(
                'MONTH(paid_at) as month, SUM(amount) as total'
            )
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($paymentsByMonth as $month => $total) {
            $monthIndex = (int) $month - 1;

            if ($monthIndex >= 0 && $monthIndex < 12) {
                $monthlyData[$monthIndex] = (float) $total;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ถ้าไม่มีข้อมูลกราฟปีนี้
        | ให้แสดงยอดเดือนปัจจุบัน
        |--------------------------------------------------------------------------
        */

        if (
            array_sum($monthlyData) == 0 &&
            $monthlyRevenue > 0
        ) {
            $monthlyData[
                Carbon::now()->month - 1
            ] = $monthlyRevenue;
        }

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'owner.dashboard',
            compact(
                'stats',
                'recentBookings',
                'upcomingReturns',
                'pendingPayments',
                'popularDresses',
                'monthlyLabels',
                'monthlyData'
            )
        );
    }
}