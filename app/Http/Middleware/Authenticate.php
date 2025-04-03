<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{

    protected function redirectTo($request)
    {
        if (!$request) {
            return null;
        }

        if ($request->expectsJson()) {
            return null;
        }

        if ($request->is('azubi/*')) {
            return route('azubi.login');
        } elseif ($request->is('admin/*')) {
            return route('admin.login');
        }

        return route('mitarbeiter.login'); 
    }
}
