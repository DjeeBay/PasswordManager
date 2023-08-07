<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laragear\TwoFactor\Contracts\TwoFactorTotp;
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

    protected function attemptLogin(Request $request)
    {
        if (env('ENABLE_TWO_FACTOR_AUTHENTICATION', false)) {
            $attempt = Auth2FA::attempt($request->only('email', 'password'), $request->filled('remember'));

            if ($attempt) {
                return redirect()->home();
//                /** @var User $user */
//                $user = Auth::user();
//                $secret = $user->createTwoFactorAuth();
//
//                return view('user.two_factor')
//                    ->withQrCode($secret->toQr());
            }
        }

        return $this->authenticatesUsersAttemptLogin($request);
    }

    protected function authenticated(Request $request, $user)
    {
        //TODO check auth user has confirmed 2FA

        if (Hash::check($request->passphrase.env('KEEPASS_PASSPHRASE_VALIDATOR'), $user->passphrase_validator)) {
            $request->session()->put('kpm.private_passphrase', $request->passphrase);
        }

        if (env('ENABLE_TWO_FACTOR_AUTHENTICATION', false)) {
            $secret = $request->user()->createTwoFactorAuth();

            return view('auth.two_factor', [
                'qrCode' => $secret->toQr(),
                'uri'     => $secret->toUri(),
                'string'  => $secret->toString(),
            ]);
        }
    }
}
