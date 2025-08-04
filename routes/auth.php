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



Route::middleware(['guest:web'])->prefix('mitarbeiter')->group( function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('mitarbeiter.login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [AuthenticatedSessionController::class, 'create'])->name('mitarbeiter.register');
    Route::post('register', [AuthenticatedSessionController::class, 'store']);

});
Route::middleware('auth:web')->prefix('mitarbeiter')->group( function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('mitarbeiter.logout');
});