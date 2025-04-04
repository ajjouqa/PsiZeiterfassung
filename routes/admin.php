<?php
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Admin\LoginController;
use App\Http\Controllers\Auth\Admin\RegisterController;





Route::prefix('admin')->middleware(['guest:admin'])->group( function () {

    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('admin.register');
    Route::post('register', [RegisterController::class, 'store']);

});


Route::prefix('admin')->middleware(['auth:admin'])->group( function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');
});
