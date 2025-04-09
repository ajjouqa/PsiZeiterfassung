<?php
use App\Http\Controllers\Auth\Azubi\LoginController;
use App\Http\Controllers\Auth\Azubi\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;




Route::middleware(['guest:azubi'])->prefix('azubi')->group( function () {

    Route::get('login', [LoginController::class, 'create'])->name('azubi.login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('azubi.register');
    Route::post('register', [RegisterController::class, 'store']);

});
Route::middleware('auth:azubi')->prefix('azubi')->group( function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('azubi.logout');
});