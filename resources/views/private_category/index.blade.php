@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Private categories</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-cyan">
            Private categories list
            <a href="{{route('private-category.create')}}">
                <button type="button" class="btn btn-sm btn-light rounded float-right"><i class="cil-plus"></i> Create</button>
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-dark table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Nb. Entries</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{$category->name}}</td>
                            <td>{{\App\Models\Keepass::where('private_category_id', '=', $category->id)->count()}}</td>
                            <td>
                                <a href="{{route('private-category.edit', $category)}}">
                                    <button type="button" class="btn btn-warning rounded"><i class="cil-pencil"></i></button>
                                </a>
                                <delete-button
                                    :confirm-delay-in-seconds="{{env('KEEPASS_CONFIRM_DELETE_DELAY_IN_SECONDS') >= 1 ? env('KEEPASS_CONFIRM_DELETE_DELAY_IN_SECONDS') : 5}}"
                                    route="{{route('private-category.destroy', $category)}}"></delete-button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
