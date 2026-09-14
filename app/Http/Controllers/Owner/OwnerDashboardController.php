<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Revenue calculations
        $todayRevenue = (float) Payment::where('status', 'approved')
            ->whereDate('payment_date', $today)
            ->sum('payment_amount');

        $monthlyRevenue = (float) Payment::where('status', 'approved')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('payment_amount');

        // Fallback: if no payments dated today/this month, calculate from approved payments
        if ($monthlyRevenue == 0) {
            $monthlyRevenue = (float) Payment::where('status', 'approved')->sum('payment_amount');
        }

        // 2. Booking Counts
        $totalBookings = Rental::count();
        $activeRentals = Rental::whereIn('status', ['renting', 'pending_return'])->count();
        $pendingBookings = Rental::whereIn('status', ['pending', 'pending_payment', 'pending_verification'])->count();

        // 3. Dress & Customer Counts
        $availableDresses = Product::where('status', 'available')->count();
        $totalDresses = Product::count();
        $totalCustomers = Customer::count();

        $stats = [
            'monthly_revenue'   => $monthlyRevenue,
            'today_revenue'     => $todayRevenue,
            'total_bookings'    => $totalBookings,
            'active_rentals'    => $activeRentals,
            'pending_bookings'  => $pendingBookings,
            'available_dresses' => $availableDresses,
            'total_dresses'     => $totalDresses,
            'total_customers'   => $totalCustomers,
        ];

        // 4. Recent Bookings (latest 6)
        $recentBookings = Rental::with(['customer', 'details.product'])
            ->latest('rental_id')
            ->take(6)
            ->get();

        // 5. Upcoming Returns (active rentals sorting by end_date asc)
        $upcomingReturns = Rental::with(['customer', 'details.product'])
            ->whereIn('status', ['renting', 'pending_return'])
            ->orderBy('end_date', 'asc')
            ->take(5)
            ->get();

        // If none currently 'renting', pick any latest rentals for demo preview
        if ($upcomingReturns->isEmpty()) {
            $upcomingReturns = Rental::with(['customer', 'details.product'])
                ->latest('rental_id')
                ->take(3)
                ->get();
        }

        // 6. Pending Payments (needing slip approval)
        $pendingPayments = Payment::with(['rental.customer'])
            ->where('status', 'pending')
            ->latest('payment_id')
            ->take(5)
            ->get();

        // 7. Popular Dresses
        $popularDresses = Product::with(['images', 'category'])
            ->orderByDesc('rental_count')
            ->take(6)
            ->get();

        // 8. Monthly Revenue Chart (12 Months of current year)
        $monthlyLabels = [
            'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.',
        ];

        $monthlyData = array_fill(0, 12, 0.0);
        $currentYear = Carbon::now()->year;

        $paymentsByMonth = Payment::where('status', 'approved')
            ->whereYear('payment_date', $currentYear)
            ->selectRaw('MONTH(payment_date) as month, SUM(payment_amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($paymentsByMonth as $month => $total) {
            $monthlyData[$month - 1] = (float) $total;
        }

        // If no chart data this year, add current month's revenue to chart for visualization
        if (array_sum($monthlyData) == 0 && $monthlyRevenue > 0) {
            $monthlyData[Carbon::now()->month - 1] = $monthlyRevenue;
        }

        return view('owner.dashboard', compact(
            'stats',
            'recentBookings',
            'upcomingReturns',
            'pendingPayments',
            'popularDresses',
            'monthlyLabels',
            'monthlyData'
        ));
    }
}
