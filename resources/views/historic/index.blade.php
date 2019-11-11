@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Historic</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-cyan">
            Historic
        </div>

        <div class="card-body">
            {{html()->form('GET', route('historic.index'))->open()}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {{html()->select('category', Auth::user()->categories->pluck('name', 'id'), Request()->category)->placeholder('Category')->class('form-control')}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{html()->text('title', Request()->title)->placeholder('Title')->class('form-control')}}
                    </div>
                </div>
                <div class="col-md-2">
                    {{html()->submit('Filter')->class('btn btn-primary')}}
                </div>
            </div>
            {{html()->form()->close()}}
            <div class="table-responsive">
                <table class="table table-hover table-dark table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Category</th>
                        <th scope="col"><a href="{{route('historic.index', ['sortBy' => 'created', 'category' => Request()->category, 'title' => Request()->title])}}">Created at</a> @if(Request()->sortBy === 'created')<i class="cui-chevron-bottom text-warning"></i>@endif</th>
                        <th scope="col">Created by</th>
                        <th scope="col"><a href="{{route('historic.index', ['sortBy' => 'updated', 'category' => Request()->category, 'title' => Request()->title])}}">Updated at</a> @if(Request()->sortBy === 'updated' || !Request()->sortBy)<i class="cui-chevron-bottom text-warning"></i>@endif</th>
                        <th scope="col">Updated by</th>
                        <th scope="col"><a href="{{route('historic.index', ['sortBy' => 'deleted', 'category' => Request()->category, 'title' => Request()->title])}}">Deleted at</a> @if(Request()->sortBy === 'deleted')<i class="cui-chevron-bottom text-warning"></i>@endif</th>
                        <th scope="col">Deleted by</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($keepasses as $keepass)
                        <tr>
                            <td>
                                {{$keepass->title}}
                                <div><small><em>({{$keepass->fullpath}})</em></small></div>
                            </td>
                            <td>{{$keepass->category->name}}</td>
                            <td>{{$keepass->created_at}}</td>
                            <td>{{optional(App\Models\User::withTrashed()->where('id', '=', $keepass->created_by)->first())->name}}</td>
                            <td>{{$keepass->updated_at}}</td>
                            <td>{{optional(App\Models\User::withTrashed()->where('id', '=', $keepass->updated_by)->first())->name}}</td>
                            <td>{{$keepass->deleted_at}}</td>
                            <td>
                                {{optional(App\Models\User::withTrashed()->where('id', '=', $keepass->deleted_by)->first())->name}}
                                @if ($keepass->deleted_at && (Auth::user()->is_admin || Auth::user()->can('restore historic')))
                                    {{html()->form('GET', route('historic.restore', $keepass->id))->class('form-check-inline')->open()}}
                                    {{html()->submit('Restore')->class('btn btn-danger btn-sm')}}
                                    {{html()->form()->close()}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{$keepasses->appends(['sortBy' => Request()->sortBy, 'category' => Request()->category, 'title' => Request()->title])->links()}}
        </div>
    </div>
@endsection
