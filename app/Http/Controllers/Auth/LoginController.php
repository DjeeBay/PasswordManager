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

//	public function login(Request $request)
//	{
//		if (env('ENABLE_TWO_FACTOR_AUTHENTICATION', false)) {
//			// If the user is trying for the first time, ensure both email and the password are
//			// required to log in. If it's not, then he would issue its 2FA code. This ensures
//			// the credentials are not required again when is just issuing his 2FA code alone.
//			if ($request->isNotFilled('2fa_code')) {
//				$request->validate([
//					'email' => 'required|email',
//					'password' => 'required|string'
//				]);
//			} else {
//				$activated = Auth::user()->confirmTwoFactorAuth($request->input('2fa_code'));
//				if ($activated) {
//					return redirect()->route('home');
//				} else {
//					return redirect()->back()->withErrors(['code' => 'Wrong code']);
//				}
//			}
//
//			$attempt = Auth2FA::attempt($request->only('email', 'password'), $request->filled('remember'));
//
//			if ($attempt) {
//				/** @var User $user */
//				$user = Auth::user();
//				$userId = $user->id;
//				if (!$user->hasTwoFactorEnabled()) {
//					$secret = $user->createTwoFactorAuth();
//					//Auth::logout();
//
//					return view('auth.two_factor')
//						->withString($secret->toString())
//						->withUserId($userId)
//						->withQrCode($secret->toQr());
//				}
//				return view('two-factor::login');
//			}
//
//			return back()->withErrors(['email' => 'There is no existing user for these credentials']);
//		}
//
//		return $this->authenticatesUsersLogin($request);
//	}

	public function login(Request $request)
	{
		// If the user is trying for the first time, ensure both email and the password are
		// required to log in. If it's not, then he would issue its 2FA code. This ensures
		// the credentials are not required again when is just issuing his 2FA code alone.
		if ($request->isNotFilled('code')) {
			$request->validate([
				'email' => 'required|email',
				'password' => 'required|string'
			]);
		}

		$attempt = Auth2FA::attempt($request->only('email', 'password'), $request->filled('remember'));

		if ($attempt) {
			/** @var User $user */
			$user = Auth::user();
			if (!$user->hasTwoFactorEnabled()) {
				$secret = $request->user()->createTwoFactorAuth();
				$request->session()->put('first2faUserId', $user->id);
				//Auth::logout();
				return view('auth.two_factor', [
					'userId' => $user->id,
					'qrCode' => $secret->toQr(),     // As QR Code
					'uri'     => $secret->toUri(),    // As "otpauth://" URI.
					'string'  => $secret->toString(), // As a string
				]);
			}
			return redirect()->home();
		}

		return back()->withErrors(['email' => 'There is no existing user for these credentials']);
	}

    protected function attemptLogin(Request $request)
    {
        if (env('ENABLE_TWO_FACTOR_AUTHENTICATION', false)) {
            $attempt = Auth2FA::attempt($request->only('email', 'password'), $request->filled('remember'));

            if ($attempt) {
                /** @var User $user */
                $user = Auth::user();
                $secret = $user->createTwoFactorAuth();

                return view('auth.two_factor')
                    ->withQrCode($secret->toQr());
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
