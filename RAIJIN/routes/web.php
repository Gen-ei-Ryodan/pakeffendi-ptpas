<?php

use App\Http\Controllers\SqlTestController;
use App\Http\Controllers\StaffAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('staff.guest')->group(function (): void {
    Route::get('/login', [StaffAuthController::class, 'show'])->name('login');
    Route::post('/login', [StaffAuthController::class, 'login']);
});

Route::middleware('staff')->group(function (): void {
    Route::get('/', [SqlTestController::class, 'index']);
    Route::post('/logout', [StaffAuthController::class, 'logout']);
});
