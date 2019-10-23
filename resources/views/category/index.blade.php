@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-cyan">
            Users list
            @if (Auth::user()->is_admin || Auth::user()->can('create category'))
                <a href="{{route('category.create')}}">
                    <button type="button" class="btn btn-sm btn-light rounded float-right"><i class="cui-plus"></i> Create</button>
                </a>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-dark table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Nb. Users</th>
                        <th scope="col">Nb. Data</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{$category->name}}</td>
                            <td></td>
                            <td></td>
                            <td>
                                @if (Auth::user()->is_admin || (Auth::user()->can('edit category') && Auth::user()->categories->where('id', $category->id)->first()))
                                    <a href="{{route('category.edit', $category)}}">
                                        <button type="button" class="btn btn-warning rounded"><i class="cui-pencil"></i></button>
                                    </a>
                                @endif
                                @if ((Auth::user()->is_admin || (Auth::user()->can('delete category')) && Auth::user()->categories->where('id', $category->id)->first()))
                                    <delete-button route="{{route('category.destroy', $category)}}"></delete-button>
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
