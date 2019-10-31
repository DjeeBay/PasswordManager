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
            {{html()->modelForm($user, 'PUT', route('user.update', $user->id))->class('needs-validation')->novalidate()->open()}}
        @else
            {{html()->form('POST', route('user.store'))->class('needs-validation')->novalidate()->open()}}
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
                            {{html()->label('Password (min. 8)', 'password')}}
                            {{html()->password('password')->class('form-control')->placeholder('password')->required(!isset($user))}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{html()->label('Password Confirmation', 'password_confirmation')}}
                            {{html()->password('password_confirmation')->class('form-control')->placeholder('password confirmation')->required(!isset($user))}}
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
                    <div class="col-12"><h4>Users permissions</h4></div>
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

                <hr>
                <div class="row">
                    <div class="col-12"><h4>Keepass permissions</h4></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Create')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('create keepass')->checked(isset($user) && $user->can('create keepass'))->id('createKeepass')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Update')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('edit keepass')->checked(isset($user) && $user->can('edit keepass'))->id('editKeepass')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Delete')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('delete keepass')->checked(isset($user) && $user->can('delete keepass'))->id('deleteKeepass')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Import')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('import keepass')->checked(isset($user) && $user->can('import keepass'))->id('importKeepass')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12"><h4>Categories permissions</h4></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Create')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('create category')->checked(isset($user) && $user->can('create category'))->id('createCategory')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Update')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('edit category')->checked(isset($user) && $user->can('edit category'))->id('editCategory')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Delete')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('delete category')->checked(isset($user) && $user->can('delete category'))->id('deleteCategory')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12"><h4>Categories</h4></div>
                    @foreach ($categories as $category)
                        <div class="col-md-3">
                            <div class="form-group">
                                <div>{{html()->label($category->name)}}</div>
                                {{html()->label()->class('switch switch-success')->html(
                                    html()->checkbox('categories[]', old('categories[]'))->value($category->id)->checked(isset($user) && $user->categories->where('id', $category->id)->first())->id('category'.$category->id)->class('switch-input').'<span class="switch-slider"></span>')}}
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr>
                <div class="row">
                    <div class="col-12"><h4>Historic permissions</h4></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Read')}}</div>
                            {{html()->label()->class('switch switch-warning')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('read historic')->checked(isset($user) && $user->can('read historic'))->id('readHistoric')->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label('Restore deleted keepass')}}</div>
                            {{html()->label()->class('switch switch-warning')->html(
                                html()->checkbox('permissions[]', old('permissions[]'))->value('restore historic')->checked(isset($user) && $user->can('restore historic'))->id('restoreHistoric')->class('switch-input').'<span class="switch-slider"></span>')}}
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
