@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Icons</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-cyan">
            Icons list
            <a href="{{route('icon.create')}}">
                <button type="button" class="btn btn-sm btn-light rounded float-right"><i class="cui-plus"></i> Create</button>
            </a>
        </div>

        <div class="card-body">
            {{html()->form('GET', route('icon.index'))->open()}}
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {{html()->select('is_deletable', [0 => 'Default', 1 => 'Custom'], Request()->is_deletable)->placeholder('Icon type')->class('form-control')}}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{html()->text('title', Request()->title)->placeholder('Title')->class('form-control')}}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{html()->text('filename', Request()->filename)->placeholder('Filename')->class('form-control')}}
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
                        <th scope="col">Filename</th>
                        <th scope="col">Title</th>
                        <th scope="col">Image</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($icons as $icon)
                        <tr>
                            <td>{{$icon->filename}}</td>
                            <td>{{$icon->title}}</td>
                            <td>
                                <img src="{{asset('storage/'.$icon->path)}}" alt="{{$icon->filename}}" height="20" width="20">
                            </td>
                            <td>
                                <a href="{{route('icon.edit', $icon)}}">
                                    <button type="button" class="btn btn-warning rounded"><i class="cui-pencil"></i></button>
                                </a>
                                @if ($icon->is_deletable)
                                    <delete-button route="{{route('icon.destroy', $icon)}}"></delete-button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{$icons->appends(['is_deletable' => Request()->is_deletable, 'filename' => Request()->filename, 'title' => Request()->title])->links()}}
        </div>
    </div>
@endsection
