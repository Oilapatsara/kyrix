<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerReportController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        // Total Approved Revenue
        $totalRevenue = (float) Payment::where('status', 'approved')->sum('payment_amount');
        $monthRevenue = (float) Payment::where('status', 'approved')
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('payment_amount');

        if ($monthRevenue == 0) {
            $monthRevenue = $totalRevenue;
        }

        // Active Deposit Held (rentals in confirmed or renting)
        $activeDeposits = (float) Rental::whereIn('status', ['confirmed', 'renting'])->sum('deposit_amount');

        // Status breakdown
        $statusCounts = [
            'pending'   => Rental::whereIn('status', ['pending', 'pending_payment', 'pending_verification'])->count(),
            'confirmed' => Rental::where('status', 'confirmed')->count(),
            'renting'   => Rental::where('status', 'renting')->count(),
            'returned'  => Rental::whereIn('status', ['returned', 'completed'])->count(),
            'cancelled' => Rental::where('status', 'cancelled')->count(),
        ];

        // Top 5 most rented dresses
        $topDresses = Product::with(['category', 'images'])
            ->orderByDesc('rental_count')
            ->take(5)
            ->get();

        // Revenue by category (through products in rental_details where rental is not cancelled)
        $categoryRevenues = DB::table('rental_details')
            ->join('products', 'rental_details.product_id', '=', 'products.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->join('rentals', 'rental_details.rental_id', '=', 'rentals.rental_id')
            ->where('rentals.status', '!=', 'cancelled')
            ->select('categories.category_name', DB::raw('SUM(rental_details.subtotal) as total_revenue'), DB::raw('COUNT(rental_details.rental_detail_id) as total_rents'))
            ->groupBy('categories.category_id', 'categories.category_name')
            ->orderByDesc('total_revenue')
            ->get();

        // Monthly breakdown for table
        $monthlySummary = [];
        $monthNames = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];

        for ($m = 1; $m <= 12; $m++) {
            $rev = (float) Payment::where('status', 'approved')
                ->whereMonth('payment_date', $m)
                ->whereYear('payment_date', $currentYear)
                ->sum('payment_amount');

            $cnt = Rental::whereMonth('rental_date', $m)
                ->whereYear('rental_date', $currentYear)
                ->count();

            $monthlySummary[] = [
                'month'    => $monthNames[$m],
                'revenue'  => $rev,
                'bookings' => $cnt,
            ];
        }

        return view('owner.reports.index', compact(
            'totalRevenue',
            'monthRevenue',
            'activeDeposits',
            'statusCounts',
            'topDresses',
            'categoryRevenues',
            'monthlySummary'
        ));
    }
}
