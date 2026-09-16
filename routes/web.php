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
use App\Http\Controllers\SocialAuthController;

use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerDressController;
use App\Http\Controllers\Owner\OwnerBookingController;
use App\Http\Controllers\Owner\OwnerPaymentController;
use App\Http\Controllers\Owner\OwnerReturnController;
use App\Http\Controllers\Owner\OwnerCustomerController;
use App\Http\Controllers\Owner\OwnerReportController;

/*
|--------------------------------------------------------------------------
| KYRIX Dress Rental
|--------------------------------------------------------------------------
*/

/* =========================
   PUBLIC
========================= */

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/dresses', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/dresses/{id}', [ProductController::class, 'show'])
    ->name('products.show');


/* =========================
   CART
========================= */

Route::prefix('cart')->name('cart.')->group(function () {

    Route::get('/', [CartController::class, 'index'])
        ->name('index');

    Route::post('/add', [CartController::class, 'add'])
        ->name('add');

    Route::post('/update/{itemKey}', [CartController::class, 'update'])
        ->name('update');

    Route::post('/remove/{itemKey}', [CartController::class, 'remove'])
        ->name('remove');

    Route::post('/clear', [CartController::class, 'clear'])
        ->name('clear');
});


/* =========================
   AUTH
========================= */

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.reset.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/* =========================
   SOCIAL LOGIN
========================= */

Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');


/* =========================
   CUSTOMER
========================= */

Route::middleware('customer')->group(function () {

    Route::get('/customer/dashboard', function () {
        return view('customer.dashboard');
    })->name('customer.dashboard');


    /* ===== RENTAL ===== */

    Route::post('/dresses/{id}/book', [RentalController::class, 'book'])
        ->name('rentals.book');

    Route::get('/rentals/{id}/payment', [RentalController::class, 'payment'])
        ->name('rentals.payment');

    Route::post('/rentals/{id}/payment', [RentalController::class, 'submitPayment'])
        ->name('rentals.payment.submit');

    Route::get('/my-rentals', [RentalController::class, 'index'])
        ->name('rentals.index');

    Route::get('/my-rentals/{id}', [RentalController::class, 'show'])
        ->name('rentals.show');

    Route::post('/my-rentals/{id}/slip', [RentalController::class, 'uploadSlip'])
        ->name('rentals.upload-slip');

    Route::post('/my-rentals/{id}/return', [RentalController::class, 'requestReturn'])
        ->name('rentals.request-return');

    Route::get('/rental-history', [RentalController::class, 'history'])
        ->name('rentals.history');


    /* ===== CHECKOUT ===== */

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout/process', [CheckoutController::class, 'process'])
        ->name('checkout.process');


    /* ===== PROFILE ===== */

    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::post('/profile/update', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');


    /* ===== REVIEW ===== */

    Route::get(
        '/rentals/{rental}/products/{product}/review',
        [ReviewController::class, 'create']
    )->name('reviews.create');

    Route::post(
        '/reviews/store',
        [ReviewController::class, 'store']
    )->name('reviews.store');
});


/* =========================
   OWNER / ADMIN PANEL
   ใช้ Laravel Auth + OwnerMiddleware
========================= */

Route::middleware(['auth', 'owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {


        /* ===== DASHBOARD ===== */

        Route::get(
            '/dashboard',
            [OwnerDashboardController::class, 'index']
        )->name('dashboard');


        /* ===== DRESSES MANAGEMENT ===== */

        Route::prefix('dresses')
            ->name('dresses.')
            ->group(function () {

                Route::get(
                    '/',
                    [OwnerDressController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [OwnerDressController::class, 'create']
                )->name('create');

                Route::post(
                    '/store',
                    [OwnerDressController::class, 'store']
                )->name('store');

                Route::get(
                    '/{id}/edit',
                    [OwnerDressController::class, 'edit']
                )->name('edit');

                Route::post(
                    '/{id}/update',
                    [OwnerDressController::class, 'update']
                )->name('update');

                Route::delete(
                    '/{id}/delete',
                    [OwnerDressController::class, 'destroy']
                )->name('destroy');

                Route::post(
                    '/{id}/toggle-status',
                    [OwnerDressController::class, 'toggleStatus']
                )->name('toggle-status');
            });


        /* ===== BOOKINGS / RENTALS ===== */

        Route::prefix('bookings')
            ->name('bookings.')
            ->group(function () {

                Route::get(
                    '/',
                    [OwnerBookingController::class, 'index']
                )->name('index');

                Route::get(
                    '/{id}',
                    [OwnerBookingController::class, 'show']
                )->name('show');

                Route::post(
                    '/{id}/status',
                    [OwnerBookingController::class, 'updateStatus']
                )->name('updateStatus');
            });


        /* ===== PAYMENTS ===== */

        Route::prefix('payments')
            ->name('payments.')
            ->group(function () {

                Route::get(
                    '/',
                    [OwnerPaymentController::class, 'index']
                )->name('index');

                Route::post(
                    '/{id}/approve',
                    [OwnerPaymentController::class, 'approve']
                )->name('approve');

                Route::post(
                    '/{id}/reject',
                    [OwnerPaymentController::class, 'reject']
                )->name('reject');
            });


        /* ===== RETURNS ===== */

        Route::prefix('returns')
            ->name('returns.')
            ->group(function () {

                Route::get(
                    '/',
                    [OwnerReturnController::class, 'index']
                )->name('index');

                Route::post(
                    '/{id}/confirm',
                    [OwnerReturnController::class, 'confirmReturn']
                )->name('confirm');
            });


        /* ===== CUSTOMERS ===== */

        Route::prefix('customers')
            ->name('customers.')
            ->group(function () {

                Route::get(
                    '/',
                    [OwnerCustomerController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [OwnerCustomerController::class, 'create']
                )->name('create');

                Route::post(
                    '/store',
                    [OwnerCustomerController::class, 'store']
                )->name('store');

                Route::get(
                    '/{id}',
                    [OwnerCustomerController::class, 'show']
                )->name('show');

                Route::get(
                    '/{id}/edit',
                    [OwnerCustomerController::class, 'edit']
                )->name('edit');

                Route::put(
                    '/{id}',
                    [OwnerCustomerController::class, 'update']
                )->name('update');

                Route::delete(
                    '/{id}',
                    [OwnerCustomerController::class, 'destroy']
                )->name('destroy');
            });


        /* ===== REPORTS ===== */

        Route::prefix('reports')
            ->name('reports.')
            ->group(function () {

                Route::get(
                    '/',
                    [OwnerReportController::class, 'index']
                )->name('index');
            });
    });