<?php

use App\Http\Controllers\admin\AdminCollectorController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminSecretaryController;
use App\Http\Controllers\admin\area\AdminManilaClientsController;
use App\Http\Controllers\admin\area\AdminManilaController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\secretary\area\SecretaryAreaController;
use App\Http\Controllers\secretary\SecretaryCollectorController;
use App\Http\Controllers\secretary\SecretaryDashboardController;
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
Route::post('/login/request', [AuthController::class, 'LoginRequest'])->name('auth.login.request');
Route::post('/logout', [AuthController::class, 'LogoutRequest'])->name('auth.logout.request');

// ADMIN ROUTES

Route::middleware('role:admin')->prefix('admin')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [AdminDashboardController::class, 'AdminDashboardPage'])
        ->name('admin.dashboard.page');

    // SECRETARY
    Route::get('/secretary', [AdminSecretaryController::class, 'AdminSecretaryPage'])
        ->name('admin.secretary.page');

    Route::put('/secretary/update/{id}', [AdminSecretaryController::class, 'AdminUpdateSecretary'])
        ->name('admin.secretary.update');

    // COLLECTOR
    Route::get('/collector', [AdminCollectorController::class, 'AdminCollectorPage'])
        ->name('admin.collector.page');

    Route::put('/collector/update/{id}', [AdminCollectorController::class, 'AdminUpdateCollector'])
        ->name('admin.collector.update');

    // AREAS - MANILA
    Route::get('/areas/manila', [AdminManilaController::class, 'AdminManilaPage'])
        ->name('admin.manila.area.page');

    Route::get('/areas/manila/clients/{id}', [AdminManilaClientsController::class, 'AdminManilaClientsPage'])
        ->name('admin.manila.area.clients.page');

    Route::post('/areas/manila/clients/{id}/add', [AdminManilaClientsController::class, 'AdminManilaAddClientRequest'])
        ->name('admin.manila.area.clients.add');

    Route::get('/areas/manila/clients/{id}/loans', [AdminManilaClientsController::class, 'AdminManilaViewClientLoans'])
        ->name('admin.manila.area.clients.loans');

    Route::put('/areas/manila/clients/{id}/update', [AdminManilaClientsController::class, 'AdminManilaUpdateClientRequest'])
        ->name('admin.manila.area.clients.update');

    Route::post('/manila/clients/{id}/renew-loan', [AdminManilaClientsController::class, 'AdminManilaSubmitRenewLoan'])
        ->name('admin.manila.area.clients.renew.loan.add');

    Route::get('/manila/clients/soa/{loanId}', [AdminManilaClientsController::class, 'AdminManilaGenerateSOA'])
        ->name('admin.manila.area.clients.generate.soa');
});

// SECRETARY ROUTES
Route::middleware('role:secretary')->prefix('secretary')->name('secretary.')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [SecretaryDashboardController::class, 'SecretaryDashboardPage'])
        ->name('dashboard.page');

    // COLLECTOR
    Route::get('/collector', [SecretaryCollectorController::class, 'SecretaryCollectorPage'])
        ->name('collector.page');

    Route::put('/collector/update/{id}', [SecretaryCollectorController::class, 'SecretaryUpdateCollector'])
        ->name('collector.update');

    Route::get('/areas', [SecretaryAreaController::class, 'index'])
        ->name('areas.page');
        
});
