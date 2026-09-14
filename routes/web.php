<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| KYRIX Dress Rental
|--------------------------------------------------------------------------
*/

/* =========================
   PUBLIC
========================= */
Route::get('/',[HomeController::class,'index'])->name('home');
Route::get('/dresses',[ProductController::class,'index'])->name('products.index');
Route::get('/dresses/{id}',[ProductController::class,'show'])->name('products.show');

/* =========================
   CART
========================= */
Route::prefix('cart')->name('cart.')->group(function(){
    Route::get('/',[CartController::class,'index'])->name('index');
    Route::post('/add',[CartController::class,'add'])->name('add');
    Route::post('/update/{itemKey}',[CartController::class,'update'])->name('update');
    Route::post('/remove/{itemKey}',[CartController::class,'remove'])->name('remove');
    Route::post('/clear',[CartController::class,'clear'])->name('clear');
});

/* =========================
   AUTH
========================= */
Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('login.submit');
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register'])->name('register.submit');
Route::get('/forgot-password',[AuthController::class,'showForgotPassword'])->name('password.request');
Route::post('/forgot-password',[AuthController::class,'forgotPassword'])->name('password.reset.submit');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');

/* =========================
   CUSTOMER
   ใช้ CustomerMiddleware
========================= */
Route::middleware('customer')->group(function(){

    Route::get('/customer/dashboard',function(){
        return view('customer.dashboard');
    })->name('customer.dashboard');

    Route::post('/dresses/{id}/book',[RentalController::class,'book'])->name('rentals.book');
    Route::get('/rentals/{id}/payment',[RentalController::class,'payment'])->name('rentals.payment');
    Route::post('/rentals/{id}/payment',[RentalController::class,'submitPayment'])->name('rentals.payment.submit');

    Route::get('/checkout',[CheckoutController::class,'index'])->name('checkout.index');
    Route::post('/checkout/process',[CheckoutController::class,'process'])->name('checkout.process');

    Route::get('/my-rentals',[RentalController::class,'index'])->name('rentals.index');
    Route::get('/my-rentals/{id}',[RentalController::class,'show'])->name('rentals.show');
    Route::post('/my-rentals/{id}/slip',[RentalController::class,'uploadSlip'])->name('rentals.upload-slip');
    Route::post('/my-rentals/{id}/return',[RentalController::class,'requestReturn'])->name('rentals.request-return');

    Route::get('/rental-history',[RentalController::class,'history'])->name('rentals.history');

    Route::get('/profile',[ProfileController::class,'index'])->name('profile.index');
    Route::post('/profile/update',[ProfileController::class,'update'])->name('profile.update');
    Route::post('/profile/password',[ProfileController::class,'updatePassword'])->name('profile.password');

    Route::get('/rentals/{rental}/products/{product}/review',[ReviewController::class,'create'])->name('reviews.create');
    Route::post('/reviews/store',[ReviewController::class,'store'])->name('reviews.store');
});

/* =========================
   OWNER
   ใช้ Laravel Auth จาก users
========================= */
Route::middleware('auth')->group(function(){

    Route::get('/owner/dashboard',function(){
        if(auth()->user()->role!=='owner'){
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email'=>'ไม่มีสิทธิ์เข้าถึงหน้านี้'
            ]);
        }

        return view('owner.dashboard');
    })->name('owner.dashboard');
});