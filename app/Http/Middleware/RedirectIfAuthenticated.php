<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (Auth::guard('admin')->check() && !$request->is('admin/dashboard')) {
            return redirect(RouteServiceProvider::ADMIN_DASHBOARD);
        }
    
        if (Auth::guard('azubi')->check() && !$request->is('azubi/dashboard')) {
            return redirect(RouteServiceProvider::AZUBI_DASHBOARD);
        }
    
        if (Auth::guard('web')->check() && !$request->is('mitarbeiter/dashboard')) {
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
