<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laragear\TwoFactor\Facades\Auth2FA;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        attemptLogin as authenticatesUsersAttemptLogin;
		login as authenticatesUsersLogin;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $bypassIp = env('2FA_IP_BYPASS');
        $currentIp = $request->ip();
        if (env('ENABLE_PER_USER_TWO_FACTOR_AUTHENTICATION', false) && $bypassIp !== $currentIp) {
            $attempt = Auth2FA::attempt($request->only('email', 'password'));

            if ($attempt) {
                return $this->authenticatesUsersLogin($request);
            }

            return 'Hey, you should make an account!';
        }
        return $this->authenticatesUsersLogin($request);
    }

    protected function authenticated(Request $request, $user)
    {
        if (Hash::check($request->passphrase.env('KEEPASS_PASSPHRASE_VALIDATOR'), $user->passphrase_validator)) {
            $request->session()->put('kpm.private_passphrase', $request->passphrase);
        }

        $allowedIp = env('2FA_IP_BYPASS');
        $currentIp = $request->ip();
        if (env('ENABLE_TWO_FACTOR_AUTHENTICATION', false) && $currentIp !== $allowedIp) {
            /** @var User $user */
            $user = Auth::user();
            if (!$user->hasTwoFactorEnabled()) {
                return redirect()->route('auth.two_factor');
            } else {
                Auth2FA::view('two-factor::confirm');
                Auth2FA::attempt($request->only('email', 'password'), $request->filled('remember'));
            }
        }
    }
}
