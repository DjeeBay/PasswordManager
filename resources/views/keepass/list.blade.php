@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item">Keepass</li>
            <li class="breadcrumb-item active">{{$category->name}}</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card border border-dark">
        <div class="card-header bg-dark text-center font-weight-bold">
            {{$category->name}}
        </div>
        <div class="card-body">
            @if (!\Illuminate\Support\Facades\Auth::user()->passphrase_validator)
                <h5 class="card-title bg-danger p-2">Please consider providing a passphrase to ensure your private passwords are securely stored. <a class="text-warning" href="{{route('user.edit', \Illuminate\Support\Facades\Auth::user()->id)}}">Click here</a></h5>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <keepass-wrapper
                        add-favorites-route="{{route($isPrivate ? 'favorite.add-multiple-private' : 'favorite.add-multiple')}}"
                        :confirm-delay-in-seconds="{{env('KEEPASS_CONFIRM_DELETE_DELAY_IN_SECONDS') >= 1 ? env('KEEPASS_CONFIRM_DELETE_DELAY_IN_SECONDS') : 5}}"
                        :category-id="{{$category->id}}"
                        :icon-list='@json(\App\Models\Icon::all())'
                        :items='@json($items)'
                        :entry-mode="{{\Illuminate\Support\Facades\Route::currentRouteName() === 'keepass.get_entry' || \Illuminate\Support\Facades\Route::currentRouteName() === 'keepass.get_private_entry' ? 'true' : 'false'}}"
                        save-route="{{route($isPrivate ? 'keepass.save_private' : 'keepass.save', $category->id)}}"
                        create-multiple-route="{{route($isPrivate ? 'keepass.create-multiple-private' : 'keepass.create-multiple', $category->id)}}"
                        :is-private='{{$isPrivate ? 'true' : 'false'}}'
                        :is-passphrase-enabled='{{\Illuminate\Support\Facades\Auth::user()->passphrase_validator ? 'true' : 'false'}}'
                    ></keepass-wrapper>
                </div>
            </div>
        </div>
    </div>
@endsection
