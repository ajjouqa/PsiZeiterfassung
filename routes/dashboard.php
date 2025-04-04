<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Admin\LoginController;
use App\Http\Controllers\Auth\Admin\RegisterController;



/*-----------------------------Admin dashboard--------------------------------------------*/

Route::prefix('admin')->middleware(['auth:admin'])->group( function () {
    Route::get('dashboard',[DashboardController::class,'AdminDashboard'])->name('admin.dashboard');
    Route::get('dashboard/admins',[UserController::class,'admins'])->name('admin.admin');
});











