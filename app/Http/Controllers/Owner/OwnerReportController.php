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

        // Revenue and rents by category (combines real-time rental_details and product rental metrics)
        $detailsRev = DB::table('rental_details')
            ->join('products', 'rental_details.product_id', '=', 'products.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->join('rentals', 'rental_details.rental_id', '=', 'rentals.rental_id')
            ->where('rentals.status', '!=', 'cancelled')
            ->select(
                'categories.category_id',
                'categories.category_name',
                DB::raw('SUM(rental_details.subtotal) as total_revenue'),
                DB::raw('COUNT(rental_details.rental_detail_id) as total_rents')
            )
            ->groupBy('categories.category_id', 'categories.category_name')
            ->get()
            ->keyBy('category_id');

        $productStats = DB::table('categories')
            ->leftJoin('products', 'categories.category_id', '=', 'products.category_id')
            ->select(
                'categories.category_id',
                'categories.category_name',
                DB::raw('COALESCE(SUM(products.rental_count * products.rental_price), 0) as baseline_revenue'),
                DB::raw('COALESCE(SUM(products.rental_count), 0) as baseline_rents')
            )
            ->groupBy('categories.category_id', 'categories.category_name')
            ->get();

        $categoryRevenues = $productStats->map(function ($cat) use ($detailsRev) {
            $catId = $cat->category_id;
            $detail = $detailsRev->get($catId);

            $rev = $detail ? max((float)$detail->total_revenue, (float)$cat->baseline_revenue) : (float)$cat->baseline_revenue;
            $rents = $detail ? max((int)$detail->total_rents, (int)$cat->baseline_rents) : (int)$cat->baseline_rents;

            return (object) [
                'category_id'   => $cat->category_id,
                'category_name' => $cat->category_name,
                'total_revenue' => $rev,
                'total_rents'   => $rents,
            ];
        })->filter(function ($item) {
            return $item->total_revenue > 0 || $item->total_rents > 0;
        })->sortByDesc('total_revenue')->values();

        // 1. Daily Revenue (past 30 days)
        $dailyLabels = [];
        $dailyData   = [];
        $dailyCounts = [];
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        $thaiShortMonths = [
            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
        ];

        $dailyPayments = Payment::where('status', 'approved')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as p_date, SUM(payment_amount) as total, COUNT(payment_id) as p_count')
            ->groupBy('p_date')
            ->get()
            ->keyBy('p_date');

        for ($d = clone $startDate; $d <= $endDate; $d->addDay()) {
            $dKey = $d->format('Y-m-d');
            $dailyLabels[] = $d->format('j') . ' ' . $thaiShortMonths[$d->month];
            $dailyData[]   = isset($dailyPayments[$dKey]) ? (float) $dailyPayments[$dKey]->total : 0.0;
            $dailyCounts[] = isset($dailyPayments[$dKey]) ? (int) $dailyPayments[$dKey]->p_count : 0;
        }

        // 2. Monthly Revenue (12 months of current year)
        $monthlyLabels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $monthlyData   = array_fill(0, 12, 0.0);
        $monthlyCounts = array_fill(0, 12, 0);

        $monthPayments = Payment::where('status', 'approved')
            ->whereYear('payment_date', $currentYear)
            ->selectRaw('MONTH(payment_date) as p_month, SUM(payment_amount) as total, COUNT(payment_id) as p_count')
            ->groupBy('p_month')
            ->get()
            ->keyBy('p_month');

        // Monthly breakdown for table
        $monthlySummary = [];
        $monthNames = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];

        for ($m = 1; $m <= 12; $m++) {
            $rev = isset($monthPayments[$m]) ? (float) $monthPayments[$m]->total : 0.0;
            $cnt = isset($monthPayments[$m]) ? (int) $monthPayments[$m]->p_count : 0;
            $monthlyData[$m - 1]   = $rev;
            $monthlyCounts[$m - 1] = $cnt;

            $bookingCnt = Rental::whereMonth('rental_date', $m)
                ->whereYear('rental_date', $currentYear)
                ->count();

            $monthlySummary[] = [
                'month'    => $monthNames[$m],
                'short'    => $monthlyLabels[$m - 1],
                'revenue'  => $rev,
                'payments' => $cnt,
                'bookings' => $bookingCnt,
            ];
        }

        return view('owner.reports.index', compact(
            'totalRevenue',
            'monthRevenue',
            'activeDeposits',
            'statusCounts',
            'topDresses',
            'categoryRevenues',
            'dailyLabels',
            'dailyData',
            'dailyCounts',
            'monthlyLabels',
            'monthlyData',
            'monthlyCounts',
            'monthlySummary',
            'currentYear'
        ));
    }
}
