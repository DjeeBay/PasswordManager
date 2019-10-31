<?php

namespace App\Http\Controllers;

use App\Models\Keepass;
use App\Repositories\KeepassRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoricController extends Controller
{
    public function index(Request $request, KeepassRepository $keepassRepository)
    {
        if (Auth::user()->is_admin || Auth::user()->can('read historic')) {
            $keepasses = $keepassRepository->getHistoric($request->all());

            return view('historic.index')
                ->withKeepasses($keepasses);
        }

        return redirect()->route('home');
    }

    public function restore(Request $request, $id)
    {
        if (Auth::user()->is_admin || Auth::user()->can('restore historic')) {
            Keepass::withTrashed()->find($id)->restore();

            return redirect()->route('historic.index')->withSuccess('The entry has been restored !');
        }

        return redirect()->route('home');
    }
}
