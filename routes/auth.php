<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('mitarbeiter')->middleware(['guest:web'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('mitarbeiter.register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('mitarbeiter.login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::prefix('mitarbeiter')->middleware(['auth:web'])->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('mitarbeiter.logout');
    Route::get('dashboard',[DashboardController::class,'MitarbeiterDashboard'])->name('mitarbeiter.dashboard');
    
});
