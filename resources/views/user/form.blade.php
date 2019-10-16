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
                <div class="col-md-6">
                    <div class="form-group">
                        {{html()->label('Name (pseudo)', 'name')}}
                        {{html()->text('name', old('name'))->class('form-control')->placeholder('name')->required()}}
                    </div>
                </div>

                @if ((isset($user) && (Auth::user()->id === $user->id || Auth::user()->is_admin)) || !isset($user))
                    <div class="col-md-6">
                        <div class="form-group">
                            {{html()->label('Password', 'password')}}
                            {{html()->password('password')->class('form-control')->placeholder('password')->required(!isset($user))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{html()->label('Password Confirmation', 'password_confirmation')}}
                            {{html()->password('password_confirmation')->class('form-control')->placeholder('password confirmation')}}
                        </div>
                    </div>
                @endif
            </div>

            @if (Auth::user()->is_admin)
                <hr>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <div>{{html()->label('Admin')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('is_admin', old('is_admin'))->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                </div>
            @endif

            @if (Auth::user()->is_admin || Auth::user()->can('manage user permissions'))
                <hr>
                <div class="row">
                    <div class="col-12"><h4>User permissions</h4></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Create')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('create user')->checked(isset($user) && $user->can('create user'))->id('createUser')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Update')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('edit user')->checked(isset($user) && $user->can('edit user'))->id('editUser')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Delete')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('delete user')->checked(isset($user) && $user->can('delete user'))->id('deleteUser')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Manage permissions')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('manage user permissions')->checked(isset($user) && $user->can('manage user permissions'))->id('manageUserPermissions')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="card-footer">
            {{html()->submit('Save')->class('btn btn-success')}}
        </div>
        {{html()->closeModelForm()}}
    </div>
@endsection
