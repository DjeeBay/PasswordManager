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
            <favorite-list :favorite-list='@json($favorites)' remove-route="{{route('favorite.remove-multiple')}}"></favorite-list>
        </div>
    </div>
@endsection
