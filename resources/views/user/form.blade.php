@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('user.index')}}">Users</a></li>
            <li class="breadcrumb-item active">@if (isset($user))# {{$user->id}} @else New user @endif</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card border  @if (isset($user))border-warning @else border-success @endif">
        <div class="card-header @if (isset($user))bg-warning @else bg-success @endif">
            @if (isset($user))Edit @else New @endif
        </div>

        @if (isset($user))
            {{html()->modelForm($user, 'PUT', route('user.update', $user->id))->open()}}
        @else
            {{html()->form('POST', route('user.store'))->open()}}
        @endif
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {{html()->label('Firstname', 'firstname')}}
                        {{html()->text('firstname', old('firstname'))->class('form-control')->placeholder('firstname')}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {{html()->label('Lastname', 'lastname')}}
                        {{html()->text('lastname', old('lastname'))->class('form-control')->placeholder('lastname')}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {{html()->label('Email', 'email')}}
                        {{html()->email('email', old('email'))->class('form-control')->placeholder('email')->required()}}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            {{html()->submit('Save')->class('btn btn-success')}}
        </div>
        {{html()->closeModelForm()}}
    </div>
@endsection
