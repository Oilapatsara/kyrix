<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class OwnerCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('rentals')
            ->with(['rentals']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest('customer_id')->paginate(15)->withQueryString();

        return view('owner.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::with([
            'rentals.details.product',
            'rentals.latestPayment',
            'reviews.product'
        ])->findOrFail($id);

        $totalSpent = $customer->rentals->whereIn('status', ['confirmed', 'renting', 'returned', 'completed'])->sum('total_amount');

        return view('owner.customers.show', compact('customer', 'totalSpent'));
    }
}
