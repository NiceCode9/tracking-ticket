<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\FakturController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/api/faktur/search', [FrontController::class, 'search'])->middleware('web')->name('faktur.search');

// Route::get('/dashboard', function () {
//     // return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->middleware(['auth', 'verified'])->name('dashboard.data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('distributor', DistributorController::class);
    Route::resource('faktur', FakturController::class);
});
Route::get('faktur/{faktur}/download', [FakturController::class, 'download'])->name('faktur.download');

require __DIR__ . '/auth.php';
