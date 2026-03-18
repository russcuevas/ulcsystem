<?php

use App\Http\Controllers\admin\AdminCollectorController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminSecretaryController;
use App\Http\Controllers\admin\area\AdminManilaClientsController;
use App\Http\Controllers\admin\area\AdminManilaController;
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
Route::put('/admin/secretary/update/{id}', [AdminSecretaryController::class, 'AdminUpdateSecretary'])->name('admin.secretary.update');

// ADMIN COLLECTOR
Route::get('/admin/collector', [AdminCollectorController::class, 'AdminCollectorPage'])->name('admin.collector.page');
Route::put('/admin/collector/update/{id}', [AdminCollectorController::class, 'AdminUpdateCollector'])->name('admin.collector.update');

// ADMIN AREAS
Route::get('/admin/areas/manila', [AdminManilaController::class, 'AdminManilaPage'])->name('admin.manila.area.page');
Route::get('/admin/areas/manila/clients/{id}', [AdminManilaClientsController::class, 'AdminManilaClientsPage'])->name('admin.manila.area.clients.page');
Route::post('/admin/areas/manila/clients/{id}/add', [AdminManilaClientsController::class, 'AdminAddClientRequest'])->name('admin.manila.area.clients.add');
Route::get('/admin/areas/manila/clients/{id}/loans', [AdminManilaClientsController::class, 'AdminViewClientLoans'])->name('admin.manila.area.client.loans');
