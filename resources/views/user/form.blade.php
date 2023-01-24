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
            </div>

            @if ((isset($user) && (Auth::user()->id === $user->id || Auth::user()->is_admin)) || !isset($user))
                <div class="row border-danger b-a-2">
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
                    @if (isset($user))
                        <div class="col-md-6">
                            <div class="form-group">
                                {{html()->label('Passphrase', 'passphrase')}}
                                {{html()->password('passphrase')->class('form-control')->placeholder('passphrase')->attribute('autocomplete', 'off')}}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if (isset($user) && $user->id === \Illuminate\Support\Facades\Auth::user()->id)
                <div class="row mt-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#passphraseModal">
                            Change passphrase
                        </button>
                    </div>
                </div>
            @endif

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

        @if (isset($user) && \Illuminate\Support\Facades\Auth::user()->id === $user->id)
            <!-- Modal -->
            {{html()->form('POST', route('user.update_passphrase', ['id' => $user]))->class('needs-validation')->novalidate()->open()}}
                <div class="modal modal-danger fade" id="passphraseModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="passphraseModalLabel">Change your passphrase</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p class="p-2 bg-dark text-white font-weight-bold font-lg">
                                    <i class="fa fa-warning text-danger"></i> Be careful, you should keep your passphrase carefully. Losing it means losing all your private passwords.
                                    <br>There is no way to recover your passphrase and so there will be no way to recover your private passwords.
                                </p>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{html()->label('Old Passphrase', 'old_passphrase')}}
                                            {{html()->password('old_passphrase')->class('form-control')->placeholder('old passphrase')}}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{html()->label('New Passphrase', 'new_passphrase')}}
                                            {{html()->password('new_passphrase')->class('form-control')->placeholder('new passphrase')->required()}}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{html()->label('New Passphrase Confirmation', 'new_passphrase_confirmation')}}
                                            {{html()->password('new_passphrase_confirmation')->class('form-control')->placeholder('new passphrase confirmation')->required()}}
                                        </div>
                                    </div>
                                </div>
                                <p class="p-2 border-info b-a-2">
                                    <i class="fa fa-info-circle text-info"></i> Your passphrase is used to encrypt all your private passwords. It provides a better security level, preventing your private passwords from being decrypted even if your account has been hacked.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">Save new passphrase</button>
                            </div>
                        </div>
                    </div>
                </div>
            {{html()->form()->close()}}
        @endif
    </div>
@endsection
