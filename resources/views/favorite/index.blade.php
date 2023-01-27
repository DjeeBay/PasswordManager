@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Favorites</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-cyan">
            Favorites
        </div>

        <div class="card-body p-0 p-lg-4">
            @if (\Illuminate\Support\Facades\Auth::user()->passphrase_validator && !\Illuminate\Support\Facades\Hash::check(Session::get('kpm.private_passphrase').env('KEEPASS_PASSPHRASE_VALIDATOR'), \Illuminate\Support\Facades\Auth::user()->passphrase_validator))
                <h5 class="card-title bg-danger p-2">Your passphrase is not provided or not valid. Your private passwords won't be available.</h5>
            @endif
            <favorite-list :favorite-list='@json($favorites)' remove-route="{{route('favorite.remove-multiple')}}"></favorite-list>
        </div>
    </div>
@endsection
