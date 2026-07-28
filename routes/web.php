<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Models\Category;
use App\Models\Product;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (GoldPriceService $goldPriceService) {
    $goldPrice  = $goldPriceService->getTodayPrice();
    $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

    $representativeIds = Product::where('is_available', true)
        ->whereNotNull('category_id')
        ->selectRaw('MIN(id) as id')
        ->groupBy('category_id')
        ->pluck('id');

    $products = Product::with('category')
        ->whereIn('id', $representativeIds)
        ->orderBy('sort_order')
        ->get();

    return view('welcome', compact('goldPrice', 'categories', 'products'));
})->name('home');

// ─── Authenticated Routes ──────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Role-based redirect
    Route::get('/dashboard', function () {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('customer.dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');


    // ── ADMIN ──────────────────────────────────────────────────────────────
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Products CRUD
        Route::resource('products', Admin\ProductController::class)->only(['index','create','store','edit','update','destroy']);

        // Gold Prices
        Route::get('/gold-prices',              [Admin\GoldPriceController::class, 'index'])->name('gold-prices.index');
        Route::post('/gold-prices',             [Admin\GoldPriceController::class, 'store'])->name('gold-prices.store');
        Route::post('/gold-prices/fetch',       [Admin\GoldPriceController::class, 'fetchExternal'])->name('gold-prices.fetch');

        // Reservations
        Route::get('/reservations',             [Admin\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}',[Admin\ReservationController::class,'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/confirm', [Admin\ReservationController::class, 'confirm'])->name('reservations.confirm');
        Route::post('/reservations/{reservation}/reject',  [Admin\ReservationController::class, 'reject'])->name('reservations.reject');

        // Transactions
        Route::get('/transactions',             [Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create',      [Admin\TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions',            [Admin\TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}',[Admin\TransactionController::class,'show'])->name('transactions.show');

        // Customers
        Route::get('/customers',                [Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{user}',         [Admin\CustomerController::class, 'show'])->name('customers.show');

        // Rewards
        Route::get('/rewards',                  [Admin\RewardController::class, 'index'])->name('rewards.index');

        // Installments
        Route::get('/installments',             [Admin\InstallmentController::class, 'index'])->name('installments.index');
        Route::get('/installments/{installmentPlan}', [Admin\InstallmentController::class, 'show'])->name('installments.show');
        Route::post('/installments/{installmentPlan}/payments/{installmentPayment}/pay', [Admin\InstallmentController::class, 'recordPayment'])->name('installments.payments.pay');

        // Pawns
        Route::get('/pawns',                    [Admin\PawnController::class, 'index'])->name('pawns.index');
        Route::get('/pawns/{pawn}',             [Admin\PawnController::class, 'show'])->name('pawns.show');
        Route::post('/pawns/{pawn}/redeem',     [Admin\PawnController::class, 'redeem'])->name('pawns.redeem');

        // Price Negotiations
        Route::get('/negotiations',             [Admin\PriceNegotiationController::class, 'index'])->name('negotiations.index');
        Route::get('/negotiations/{negotiation}',[Admin\PriceNegotiationController::class,'show'])->name('negotiations.show');
        Route::post('/negotiations/{negotiation}/approve', [Admin\PriceNegotiationController::class, 'approve'])->name('negotiations.approve');
        Route::post('/negotiations/{negotiation}/reject',  [Admin\PriceNegotiationController::class, 'reject'])->name('negotiations.reject');

        // Reports
        Route::get('/reports',                  [Admin\ReportController::class, 'index'])->name('reports.index');
    });

    // ── CUSTOMER ───────────────────────────────────────────────────────────
    Route::middleware('customer')->prefix('customer')->name('customer.')->group(function () {

        // Dashboard
        Route::get('/dashboard',                [Customer\DashboardController::class, 'index'])->name('dashboard');

        // Catalog
        Route::get('/catalog',                  [Customer\CatalogController::class, 'index'])->name('catalog.index');

        // Price Negotiations
        Route::get('/negotiations',             [Customer\PriceNegotiationController::class, 'index'])->name('negotiations.index');
        Route::get('/negotiations/create',      [Customer\PriceNegotiationController::class, 'create'])->name('negotiations.create');
        Route::post('/negotiations',            [Customer\PriceNegotiationController::class, 'store'])->name('negotiations.store');

        // Gold Prices
        Route::get('/gold-prices',              [Customer\GoldPriceController::class, 'index'])->name('gold-prices.index');

        // Certificates
        Route::get('/certificates',             [Customer\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [Customer\CertificateController::class, 'show'])->name('certificates.show');

        // Reservations
        Route::get('/reservations',             [Customer\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/create',      [Customer\ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations',            [Customer\ReservationController::class, 'store'])->name('reservations.store');
        Route::post('/reservations/{reservation}/cancel', [Customer\ReservationController::class, 'cancel'])->name('reservations.cancel');

        // Transactions
        Route::get('/transactions',             [Customer\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}',[Customer\TransactionController::class,'show'])->name('transactions.show');

        // Rewards
        Route::get('/rewards',                  [Customer\RewardController::class, 'index'])->name('rewards.index');

        // Installments
        Route::get('/installments',             [Customer\InstallmentController::class, 'index'])->name('installments.index');
        Route::get('/installments/{installmentPlan}', [Customer\InstallmentController::class, 'show'])->name('installments.show');

        // Pawns
        Route::get('/pawns',                    [Customer\PawnController::class, 'index'])->name('pawns.index');
        Route::get('/pawns/{pawn}',             [Customer\PawnController::class, 'show'])->name('pawns.show');
    });
});

require __DIR__.'/auth.php';
