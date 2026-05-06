<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirectByRole'])
        ->name('dashboard');

    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdminDashboard'])
        ->name('super_admin.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->name('admin.dashboard');

    Route::get('/logistics/dashboard', [DashboardController::class, 'logisticsDashboard'])
        ->name('logistics.dashboard');

    Route::get('/branch/dashboard', [DashboardController::class, 'branchDashboard'])
        ->name('branch.dashboard');

    Route::get('/operator/dashboard', [DashboardController::class, 'operatorDashboard'])
        ->name('operator.dashboard');

    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])
        ->name('user.dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    /*
    |--------------------------------------------------------------------------
    | Super Admin and Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super_admin,admin')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('privileges', PermissionController::class);
    Route::resource('role-privileges', RolePermissionController::class);
    Route::resource('pages', PageController::class);

    Route::resource('users', UserController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('services', ServiceController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Logistics
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super_admin,admin,logistics')->group(function () {
        // Route::resource('waybill-logistics', WaybillLogisticController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Branch
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super_admin,admin,branch')->group(function () {
        // Route::resource('waybills', WaybillController::class);
        // Route::resource('return-waybills', ReturnWaybillController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Operator
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super_admin,admin,operator')->group(function () {
        // Route::resource('waybill-records', WaybillRecordController::class);
        // Route::resource('waybill-items', WaybillItemController::class);
        // Route::resource('waybill-photos', WaybillPhotoController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Regular User
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')->group(function () {
        // User-only routes here
    });
});