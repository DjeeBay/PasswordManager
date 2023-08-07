<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function twoFactorConfirm(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $activated = $request->user()->confirmTwoFactorAuth($request->code);
		if ($activated) {

		}

		//TODO logout

		return redirect()->route('home');
    }
}
