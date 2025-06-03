<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::resource('books', App\Http\Controllers\BookController::class);
Route::resource('customers', App\Http\Controllers\CustomerController::class);
Route::resource('deliveries', App\Http\Controllers\DeliveryController::class);
Route::get('/inventories/pdf', [InventoryController::class, 'downloadPDF'])->name('inventories.downloadPDF');
Route::resource('inventories', App\Http\Controllers\InventoryController::class);
Route::get('/sales/debtors', [App\Http\Controllers\SaleController::class, 'debtors'])->name('sales.debtors');
Route::resource('sales', App\Http\Controllers\SaleController::class);
Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
Route::resource('users', App\Http\Controllers\UserController::class);

Route::get('/users/{id}/change-password', [UserController::class, 'changePasswordForm'])->name('users.change-password');
Route::post('/users/update-password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('users.update-password');

Route::get('/payments/create/{sale_id}', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('payments/download-pdf', [PaymentController::class, 'downloadPdf'])->name('payments.downloadPdf');




