@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-cyan">
            Users list
            @if (Auth::user()->is_admin || Auth::user()->can('create user'))
                <a href="{{route('user.create')}}">
                    <button type="button" class="btn btn-sm btn-light rounded float-right"><i class="cil-plus"></i> Create</button>
                </a>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-dark table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Firstname</th>
                        <th scope="col">Lastname</th>
                        <th scope="col">Email</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{$user->name}}</td>
                            <td>{{$user->firstname}}</td>
                            <td>{{$user->lastname}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                @if (Auth::user()->is_admin || Auth::user()->can('edit user') || Auth::user()->id === $user->id)
                                    <a href="{{route('user.edit', $user)}}">
                                        <button type="button" class="btn btn-warning rounded"><i class="cil-pencil"></i></button>
                                    </a>
                                @endif
                                @if ((Auth::user()->is_admin || Auth::user()->can('delete user')) && Auth::user()->id !== $user->id)
                                    <delete-button
                                        :confirm-delay-in-seconds="{{env('KEEPASS_CONFIRM_DELETE_DELAY_IN_SECONDS') >= 1 ? env('KEEPASS_CONFIRM_DELETE_DELAY_IN_SECONDS') : 5}}"
                                        route="{{route('user.destroy', $user)}}"></delete-button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
