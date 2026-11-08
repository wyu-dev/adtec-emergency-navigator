<?php

use App\Http\Controllers\EvacuationController;
use Illuminate\Support\Facades\Route;

Route::get('/emergency/status', [EvacuationController::class, 'status'])->name('api.emergency.status');
Route::get('/emergency/vapid-key', [EvacuationController::class, 'vapidPublicKey'])->name('api.emergency.vapid');
Route::post('/push/subscribe', [EvacuationController::class, 'subscribe'])->name('api.push.subscribe');
Route::post('/evacuee/check-in', [EvacuationController::class, 'checkIn'])->name('api.evacuee.checkin');
