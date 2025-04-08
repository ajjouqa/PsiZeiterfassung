<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\XmppHeartbeatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Admin\LoginController;
use App\Http\Controllers\Auth\Admin\RegisterController;
use App\Http\Controllers\XmppPresenceController;



/*-----------------------------Admin dashboard--------------------------------------------*/

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('dashboard/admins', [UserController::class, 'admins'])->name('admin.admin');
    Route::get('dashboard/add-user', [UserController::class, 'create'])->name('admin.create.user');
    Route::post('dashboard/add-user/store', [UserController::class, 'store'])->name('admin.store.user');
    Route::get('dashboard/presence/{userType}/{userId}', [XmppPresenceController::class, 'showPresenceLogs'])->name('xmpp.presence.logs');
    Route::get('dashboard/daily-presence/{userType}/{userId}', [XmppPresenceController::class, 'showDailySummaries'])->name('xmpp.presence.daily');

    Route::post('xmpp-heartbeat', [XmppHeartbeatController::class, 'update']);
    Route::post('xmpp-disconnect', [XmppHeartbeatController::class, 'disconnect']);
});


Route::prefix('azubi')->middleware(['auth:azubi'])->group(function () {
    Route::post('xmpp-heartbeat', [XmppHeartbeatController::class, 'update']);
    Route::post('xmpp-disconnect', [XmppHeartbeatController::class, 'disconnect']);
});



Route::prefix('mitarbeiter')->middleware(['auth:web'])->group(function () {
    Route::post('xmpp-heartbeat', [XmppHeartbeatController::class, 'update']);
    Route::post('xmpp-disconnect', [XmppHeartbeatController::class, 'disconnect']);
});




