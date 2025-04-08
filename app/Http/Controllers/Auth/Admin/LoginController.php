<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use App\Services\XmppAuthService;
use App\Traits\DetectsUserRole;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use Log;

class LoginController extends Controller
{
    use DetectsUserRole;
    protected $xmppAuthService;

    // إضافة بناء جديد لحقن الخدمة
    public function __construct(XmppAuthService $xmppAuthService)
    {
        $this->xmppAuthService = $xmppAuthService;
    }

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

        $role = $this->detectUserRole();
        $user = Auth::guard('admin')->user();


        $authResult = $this->xmppAuthService->authenticateUser('admin', $user->id);
        // End of added block
        if ($authResult) {
            $authResult['xmpp_service']->setPresence($authResult['connection'], 'available');
        }else {
            Log::error("XMPP authentication failed for user ID: " . $user->id);
        }

        
        event(new LoginEvent(Auth::guard('admin')->user()->id, $role));

        return redirect()->intended(RouteServiceProvider::ADMIN_DASHBOARD);
    }

    public function destroy(Request $request): RedirectResponse
    {

        $user = Auth::guard('admin')->user();
        $role = $this->detectUserRole();

        // Add this block - Get XMPP connection and logout
        $authResult = $this->xmppAuthService->authenticateUser('admin', $user->id);
        if ($authResult) {
            $this->xmppAuthService->logoutUser('admin', $user->id, $authResult['connection']);
        }

        event(new LogoutEvent(Auth::guard('admin')->user()->id, $this->detectUserRole()));

        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/admin/login');
    }
}
