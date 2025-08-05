<?php

namespace App\Providers;

use App\Http\View\Composers\NotificationComposer;
use App\Models\Admin;
use App\Models\Azubi;
use App\Models\User;
use App\Observers\UserObserver;
use App\Services\XMPPService;
use Illuminate\Support\ServiceProvider;
use App\Services\XmppAuthService;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(XMPPService::class, function ($app) {
            return new XMPPService(
                config('xmpp.server'),
                config('xmpp.username'),
                config('xmpp.password'),
                config('xmpp.port'),
                config('xmpp.resource'),
            );
        });

        $this->app->singleton(XmppAuthService::class, function ($app) {
            return new XmppAuthService(
                $app->make(XMPPService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        if (config('xmpp.auto_create_of_users', true)) {
            Admin::observe(UserObserver::class);
            Azubi::observe(UserObserver::class);
            User::observe(UserObserver::class);
        }

        View::composer('*', NotificationComposer::class);
    }
}
