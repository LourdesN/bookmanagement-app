<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    ProfileController,
    HomeController,
    BookController,
    CustomerController,
    DeliveryController,
    InventoryController,
    NotificationController,
    PaymentController,
    SaleController,
    SupplierController,
    UserController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard (protected by auth & email verification)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Auth routes (profile management)
Route::middleware('auth')->group(function () {
    // Laravel Breeze / Jetstream Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profile password update
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // Application routes (protected)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('books', BookController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('deliveries', DeliveryController::class);
    Route::get('/inventories/pdf', [InventoryController::class, 'downloadPDF'])->name('inventories.downloadPDF');
    Route::resource('inventories', InventoryController::class);

    Route::get('/sales/debtors', [SaleController::class, 'debtors'])->name('sales.debtors');
    Route::resource('sales', SaleController::class);

    Route::resource('suppliers', SupplierController::class);
    Route::resource('users', UserController::class);

    // Password change for users
    Route::get('/users/{id}/change-password', [UserController::class, 'changePasswordForm'])->name('users.change-password');
    Route::post('/users/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');

    // Payments
    Route::get('/payments/create/{sale_id}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/download-pdf', [PaymentController::class, 'downloadPdf'])->name('payments.downloadPdf');

    // Notifications
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

// Auth scaffolding (e.g., Laravel Breeze or Jetstream)
require __DIR__.'/auth.php';
