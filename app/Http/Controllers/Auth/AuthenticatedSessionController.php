<?php

namespace App\Http\Controllers\Auth;

use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\XmppAuthService;
use App\Traits\DetectsUserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use DetectsUserRole;

    protected $xmppAuthService;

    // إضافة بناء جديد لحقن الخدمة
    public function __construct(XmppAuthService $xmppAuthService)
    {
        $this->xmppAuthService = $xmppAuthService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('mitarbeiter.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $role = $this->detectUserRole();

        $user = Auth::guard('web')->user();

        // Add this block - Authenticate with XMPP
        $authResult = $this->xmppAuthService->authenticateUser('mitarbeiter', $user->id);
        // End of added block
        if ($authResult) {
            $authResult['xmpp_service']->setPresence($authResult['connection'], 'available');
        }else {
            Log::error("XMPP authentication failed for user ID: " . $user->id);
        }
        
        event(new LoginEvent(Auth::guard('web')->user()->id, $role));

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        event(new LogoutEvent(Auth::guard('web')->user()->id, $this->detectUserRole()));
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/mitarbeiter/login');
    }
}
