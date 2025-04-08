<?php

namespace App\Http\Controllers\Auth\Azubi;

use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use App\Traits\DetectsUserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use App\Services\XmppAuthService;

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
        return view('azubi.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('azubi')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $role = $this->detectUserRole();
        $user = Auth::guard('azubi')->user();

        $authResult = $this->xmppAuthService->authenticateUser('azubi', $user->id);
        
        if ($authResult) {
            $authResult['xmpp_service']->setPresence($authResult['connection'], 'available');
        }else {
            Log::error("XMPP authentication failed for user ID: " . $user->id);
        }
        


        event(new LoginEvent($user->id, $role));

        // Remove this line as it's using incorrect parameters
        // $this->xmppAuthService->updatePresence('azubi', $user->id, $user->xmpp_username, 'available');

        Log::info("Request password: " . $request->input('password'));
       

        return redirect()->intended(RouteServiceProvider::AZUBI_DASHBOARD);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::guard('azubi')->user();
        $role = $this->detectUserRole();

        // Add this block - Get XMPP connection and logout
        $authResult = $this->xmppAuthService->authenticateUser('azubi', $user->id);
        if ($authResult) {
            $this->xmppAuthService->logoutUser('azubi', $user->id, $authResult['connection']);
        }
        // End of added block

        // Remove this line as it's using incorrect parameters
        // $this->xmppAuthService->updatePresence('azubi', $user->id, $user->xmpp_username, 'unavailable');

        event(new LogoutEvent($user->id, $role));
        Auth::guard('azubi')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/azubi/login');
    }
}
