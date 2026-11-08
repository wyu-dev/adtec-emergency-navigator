<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EvacuationController;
use Illuminate\Support\Facades\Route;

// Client PWA Navigation (Default Root)
Route::get('/', [EvacuationController::class, 'index'])->name('evacuation.client');
Route::get('/evacuate', [EvacuationController::class, 'index'])->name('evacuation.direct');

// Authentication (Admin)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/email', [AuthController::class, 'loginEmail'])->name('login.email');
Route::post('/login/pin', [AuthController::class, 'loginPin'])->name('login.pin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/alert/trigger', [AdminController::class, 'triggerAlert'])->name('alert.trigger');
    Route::post('/alert/resolve', [AdminController::class, 'resolveAlert'])->name('alert.resolve');

    Route::post('/assembly-point', [AdminController::class, 'storeAssemblyPoint'])->name('assembly.store');
    Route::put('/assembly-point/{point}', [AdminController::class, 'updateAssemblyPoint'])->name('assembly.update');
    Route::post('/assembly-point/{point}/active', [AdminController::class, 'setActiveAssemblyPoint'])->name('assembly.active');

    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});
