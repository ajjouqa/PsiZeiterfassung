<?php

namespace App\Http\Controllers\Auth\Azubi;

use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use App\Traits\DetectsUserRole;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use DetectsUserRole;
    public function create(): View
    {
        return view('azubi.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if(! Auth::guard('azubi')->attempt($request->only('email', 'password'), $request->boolean('remember')))
        {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $role = $this->detectUserRole();

        event(new LoginEvent(Auth::guard('azubi')->user()->id, $role));

        return redirect()->intended(RouteServiceProvider::AZUBI_DASHBOARD);
    }

    public function destroy(Request $request): RedirectResponse
    {
        event(new LogoutEvent(Auth::guard('azubi')->user()->id, $this->detectUserRole()));
        Auth::guard('azubi')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/azubi/login');
    }
}
