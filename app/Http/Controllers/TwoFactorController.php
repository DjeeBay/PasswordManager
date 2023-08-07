<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function twoFactorConfirm(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $activated = $request->user()->confirmTwoFactorAuth($request->code);
        dd($activated);
    }
}
