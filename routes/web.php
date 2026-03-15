<?php

use App\Http\Controllers\admin\AdminCollectorController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminSecretaryController;
use App\Http\Controllers\auth\AuthController;
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

Route::get('/login', [AuthController::class, 'LoginPage'])->name('auth.login.page');



// ADMIN ROUTES

// ADMIN DASHBOARD
Route::get('/admin/dashboard', [AdminDashboardController::class, 'AdminDashboardPage'])->name('admin.dashboard.page');


// ADMIN SECRETARY
Route::get('/admin/secretary', [AdminSecretaryController::class, 'AdminSecretaryPage'])->name('admin.secretary.page');


// ADMIN COLLECTOR
Route::get('/admin/collector', [AdminCollectorController::class, 'AdminCollectorPage'])->name('admin.collector.page');
