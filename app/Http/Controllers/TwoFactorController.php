<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function twoFactorConfirm(Request $request, ConfigContract $config)
    {
        if (!env('ENABLE_TWO_FACTOR_AUTHENTICATION', false)) {
            return redirect()->route('home');
        }
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $activated = $request->user()->confirmTwoFactorAuth($request->input('code'));
        if ($activated) {
            $this->extendSessionLifetime($request, $config);
            return redirect()->route('home');
		}

		return redirect()->back();
    }

    public function twoFactorCodeCheck(Request $request, ConfigContract $config)
    {
        if (!env('ENABLE_TWO_FACTOR_AUTHENTICATION', false)) {
            return redirect()->route('home');
        }
        $request->validate([
            '2fa_code' => 'required|numeric'
        ]);
        /** @var User $user */
        $user = Auth::user();
        $validated = $user->validateTwoFactorCode($request->input('2fa_code', false));
        if ($validated) {
            $this->extendSessionLifetime($request, $config);
            return redirect()->route('home');
		}

		return view('two-factor::confirm');
    }

    public function enableTwoFactor(Request $request)
    {
        if (!env('ENABLE_TWO_FACTOR_AUTHENTICATION', false)) {
            return redirect()->route('home');
        }
        /** @var User $user */
        $user = Auth::user();
        $secret = $request->user()->createTwoFactorAuth();

        return view('auth.two_factor', [
            'userId' => $user->id,     // As QR Code
            'qrCode' => $secret->toQr(),     // As QR Code
            'uri'     => $secret->toUri(),    // As "otpauth://" URI.
            'string'  => $secret->toString(), // As a string
        ]);
    }

    private function extendSessionLifetime(Request $request, ConfigContract $config)
    {
        [
            'two-factor.confirm.key' => $key,
            'two-factor.confirm.time' => $time
        ] = $config->get([
            'two-factor.confirm.key',
            'two-factor.confirm.time',
        ]);

        // This will let the developer remember the confirmation indefinitely.
        if ($time !== INF) {
            $time = now()->addMinutes($time)->getTimestamp();
        }

        $request->session()->put("$key.confirm.expires_at", $time);
    }
}
