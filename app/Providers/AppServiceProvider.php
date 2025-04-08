<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Azubi;
use App\Models\User;
use App\Observers\UserObserver;
use App\Services\XMPPService;
use Illuminate\Support\ServiceProvider;
use MockXMPPService;
use App\Services\XmppAuthService;

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
    }
}
