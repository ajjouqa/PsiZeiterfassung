<?php

use App\Http\Controllers\DailyStatus;
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
    Route::get('dashboard/mitarbeiters', [UserController::class, 'mitarbeiter'])->name('admin.mitarbeiter');
    Route::get('dashboard/azubis', [UserController::class, 'azubis'])->name('admin.azubi');

    Route::get('dashboard/add-user', [UserController::class, 'create'])->name('admin.create.user');
    Route::post('dashboard/add-user/store', [UserController::class, 'store'])->name('admin.store.user');
    Route::get('dashboard/presence/{userType}/{userId}', [XmppPresenceController::class, 'showPresenceLogs'])->name('xmpp.presence.logs');
    Route::get('dashboard/dailyPresence/{userType}/{userId}', [XmppPresenceController::class, 'showDailySummaries'])->name('xmpp.presence.daily');

    Route::post('xmpp-heartbeat', [XmppHeartbeatController::class, 'update']);
    Route::post('xmpp-disconnect', [XmppHeartbeatController::class, 'disconnect']);


    Route::put('Update_overtime', [DailyStatus::class, 'updateOvertime'])->name('update.overtime');
    Route::put('Update_status', [DailyStatus::class, 'updateStatus'])->name('update.status');



    Route::get('/generate-daily-presence-pdf/{userType}/{userId}/{month}', [XmppPresenceController::class, 'generateDailyPresencePDF'])->name('generate.daily.presence.pdf');

});


Route::prefix('azubi')->middleware(['auth:azubi'])->group(function () {
    Route::get('dashboard',[DashboardController::class,'AzubiDashboard'])->name('azubi.dashboard');
    Route::get('dashboard/presence/{userType}/{userId}', [XmppPresenceController::class, 'showPresenceLogs'])->name('xmpp.presence.logs.azubi');
    Route::get('dashboard/dailyPresence/{userType}/{userId}', [XmppPresenceController::class, 'showDailySummaries'])->name('xmpp.presence.daily.azubi');
    Route::get('/generate-daily-presence-pdf/{userType}/{userId}', [XmppPresenceController::class, 'generateDailyPresencePDF'])->name('generate.daily.presence.pdf.azubi');
    
    Route::post('xmpp-heartbeat', [XmppHeartbeatController::class, 'update']);
    Route::post('xmpp-disconnect', [XmppHeartbeatController::class, 'disconnect']);
});



Route::prefix('mitarbeiter')->middleware(['auth:web'])->group(function () {
    Route::get('dashboard',[DashboardController::class,'MitarbeiterDashboard'])->name('mitarbeiter.dashboard');
    Route::get('dashboard/presence/{userType}/{userId}', [XmppPresenceController::class, 'showPresenceLogs'])->name('xmpp.presence.logs.mitarbeiter');
    Route::get('dashboard/dailyPresence/{userType}/{userId}', [XmppPresenceController::class, 'showDailySummaries'])->name('xmpp.presence.daily.mitarbeiterter');
    Route::get('/generate-daily-presence-pdf/{userType}/{userId}', [XmppPresenceController::class, 'generateDailyPresencePDF'])->name('generate.daily.presence.pdf.mitarbeiter');

    Route::post('xmpp-heartbeat', [XmppHeartbeatController::class, 'update']);
    Route::post('xmpp-disconnect', [XmppHeartbeatController::class, 'disconnect']);
});


