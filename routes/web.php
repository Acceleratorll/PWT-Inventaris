<?php

use App\Http\Controllers\Admin\CategoryProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProcessPlanController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('/dashboard', AdminDashboardController::class);
    Route::resource('/profile', ProfileController::class);
    Route::resource('/category', CategoryProductController::class);
    Route::resource('/product', ProductController::class);
    Route::resource('/rpp', ProcessPlanController::class);
});

Route::get('/get-rpps', [ProcessPlanController::class, 'getRpps'])->name('get-rpps');
Route::get('/get-products', [ProductController::class, 'getProducts'])->name('get-products');
Route::get('/get-categories', [CategoryProductController::class, 'getCategories'])->name('get-categories');
Route::get('/get-unused-products', [AdminDashboardController::class, 'getUnusedProducts'])->name('get-unused-products');
Route::get('/get-report-process-plan', [AdminDashboardController::class, 'getReportProcessPlan'])->name('get-report-process-plan');
Route::get('/get-qualifiers', [AdminDashboardController::class, 'getQualifiers'])->name('get-qualifiers');
Route::get('/material/search', [AdminDashboardController::class, 'search'])->name('material.search');

require __DIR__ . '/auth.php';
