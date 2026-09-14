<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Database\Seeders\DressRentalCustomerSeeder;
class HomeController extends Controller
{
    public function index()
    {
        if(Product::count()===0){
            $seeder=new DressRentalCustomerSeeder();
            $seeder->run();
        }

        $categories=Category::where('status','active')
            ->withCount('products')
            ->get();

        $featuredDresses=Product::where('status','available')
            ->where('is_featured',true)
            ->with(['category','mainImage'])
            ->take(6)
            ->get();

        $newDresses=Product::where('status','available')
            ->where('is_new',true)
            ->with(['category','mainImage'])
            ->latest()
            ->take(6)
            ->get();

        $popularDresses=Product::where('status','available')
            ->orderBy('rental_count','desc')
            ->with(['category','mainImage'])
            ->take(6)
            ->get();

        $latestReviews=Review::where('status','published')
            ->with(['product','customer'])
            ->latest()
            ->take(4)
            ->get();

        return view('home',compact(
            'categories',
            'featuredDresses',
            'newDresses',
            'popularDresses',
            'latestReviews'
        ));
    }
}