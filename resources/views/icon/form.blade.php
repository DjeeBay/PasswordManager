@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('icon.index')}}">Icons</a></li>
            <li class="breadcrumb-item active">@if (isset($icon))# {{$icon->id}} @else New icon @endif</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card border  @if (isset($icon))border-warning @else border-success @endif">
        <div class="card-header @if (isset($icon))bg-warning @else bg-success @endif">
            @if (isset($icon))Edit @else New @endif
        </div>

        @if (isset($icon))
            {{html()->modelForm($icon, 'PUT', route('icon.update', $icon->id))->acceptsFiles()->class('needs-validation')->novalidate()->open()}}
        @else
            {{html()->form('POST', route('icon.store'))->acceptsFiles()->class('needs-validation')->novalidate()->open()}}
        @endif
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {{html()->label('Title', 'title')}}
                        {{html()->text('title', old('title'))->class('form-control')->placeholder('title')}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{html()->label('Icon', 'icon')}}
                        {{html()->file('icon')->accept('.png')->class('form-control')->attribute(isset($icon) ? null : 'required')}}
                    </div>
                </div>
            </div>

            @if (isset($icon))
                <hr>
                <div class="text-center">
                    <img class="img-fluid" src="{{asset('storage/'.$icon->path)}}" alt="{{$icon->filename}}">
                </div>
            @endif
        </div>

        <div class="card-footer">
            {{html()->submit('Save')->class('btn btn-success')}}
        </div>
        {{html()->closeModelForm()}}
    </div>
@endsection
