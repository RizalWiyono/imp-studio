<?php

use App\Http\Controllers\System\ActivityLogController;
use App\Http\Controllers\System\SystemSettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RBAC\MenuController;
use App\Http\Controllers\RBAC\RoleController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\RBAC\AccessControlController;
use App\Http\Controllers\DataMaster\ArticlesController;

// Redirect root
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Routes (prefix /dashboard, middleware auth)
Route::prefix('/dashboard')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'indexAdmin'])->name('dashboard')->middleware('check.permission:dashboard');
    Route::get('/management', [DashboardController::class, 'indexManagement'])
        ->name('dashboard.management')
        ->middleware('check.permission:dashboard.manjemen');

    // Role Management (Super Admin only)
    Route::resource('roles', RoleController::class)->middleware('check.permission:roles');

    // Access Control (Assign Permissions) (Super Admin only)
    Route::get('access-control', [AccessControlController::class, 'index'])->name('access-control.index')->middleware('check.permission:access-control');
    Route::get('access-control/{role}', [AccessControlController::class, 'edit'])->name('access-control.edit')->middleware('check.permission:access-control');
    Route::post('access-control/{role}', [AccessControlController::class, 'update'])->name('access-control.assign')->middleware('check.permission:access-control');


    // Menu Management (Super Admin only)
    Route::resource('menus', MenuController::class)->middleware('check.permission:menus');

    // User Management (Super Admin only)
    Route::resource('users', UserController::class)->middleware('check.permission:users');

    // System Settings (Super Admin only)
    Route::resource('settings', SystemSettingController::class)->middleware('check.permission:settings');

    Route::resource('activity-log', ActivityLogController::class)
        ->middleware('check.permission:activity-log');

    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [ArticlesController::class, 'index'])->name('index');
        Route::get('/create', [ArticlesController::class, 'create'])->name('create');
        Route::post('/', [ArticlesController::class, 'store'])->name('store');
        Route::get('/{article}/edit', [ArticlesController::class, 'edit'])->name('edit');
        Route::put('/{article}', [ArticlesController::class, 'update'])->name('update');
        Route::delete('/{article}', [ArticlesController::class, 'destroy'])->name('destroy');
        Route::get('/{article}', [ArticlesController::class, 'show'])->name('show');
    });
});
