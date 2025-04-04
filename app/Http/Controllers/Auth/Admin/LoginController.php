<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        
        if(! Auth::guard('admin')->attempt($request->only('email', 'password'), $request->boolean('remember')))
        {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        
        $request->session()->regenerate();

        $role = null;

        if(Auth::guard('admin')->check())
        {
            $role = "admin";
        }elseif(Auth::guard('azubi')->check())
        {
            $role = "azubi";
        }elseif(Auth::guard('mitarbeiter')->check())
        {
            $role = "mitarbeiter";
        }
        
        event(new LoginEvent(Auth::guard('admin')->user()->id, $role));

        return redirect()->intended(RouteServiceProvider::ADMIN_DASHBOARD);
    }

    public function destroy(Request $request): RedirectResponse
    {
        event(new LogoutEvent(Auth::guard('admin')->user()->id));

        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/admin/login');
    }
}
