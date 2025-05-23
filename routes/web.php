<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
Route::resource('inventories', App\Http\Controllers\InventoryController::class);
Route::resource('sales', App\Http\Controllers\SaleController::class);
Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
Route::resource('users', App\Http\Controllers\UserController::class);