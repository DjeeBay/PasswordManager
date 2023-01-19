@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('private-category.index')}}">Private categories</a></li>
            <li class="breadcrumb-item active">@if (isset($category))# {{$category->id}} @else New private category @endif</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card border  @if (isset($category))border-warning @else border-success @endif">
        <div class="card-header @if (isset($category))bg-warning @else bg-success @endif">
            @if (isset($category))Edit @else New @endif
        </div>

        @if (isset($category))
            {{html()->modelForm($category, 'PUT', route('private-category.update', $category->id))->class('needs-validation')->novalidate()->open()}}
        @else
            {{html()->form('POST', route('private-category.store'))->class('needs-validation')->novalidate()->open()}}
        @endif
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {{html()->label('Name', 'name')}}
                        {{html()->text('name', old('name'))->class('form-control')->placeholder('name')->required()}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{html()->label('Description', 'description')}}
                        {{html()->textarea('description', old('description'))->class('form-control')->placeholder('description')}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {{html()->label('Color', 'color')}}
                        <input type="color" name="color" class="form-control" value="{{old('color', isset($category) ? $category->color : null)}}">
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
