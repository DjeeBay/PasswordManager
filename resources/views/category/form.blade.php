@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('category.index')}}">Categories</a></li>
            <li class="breadcrumb-item active">@if (isset($category))# {{$category->id}} @else New category @endif</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card border  @if (isset($category))border-warning @else border-success @endif">
        <div class="card-header @if (isset($category))bg-warning @else bg-success @endif">
            @if (isset($category))Edit @else New @endif
        </div>

        @if (isset($category))
            {{html()->modelForm($category, 'PUT', route('category.update', $category->id))->class('needs-validation')->novalidate()->open()}}
        @else
            {{html()->form('POST', route('category.store'))->class('needs-validation')->novalidate()->open()}}
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
            </div>

            <hr>
            <div class="row">
                <div class="col-12">
                    <h4>Who can access ?</h4>
                </div>
                @foreach ($users as $user)
                    <div class="col-md-3">
                        <div class="form-group">
                            <div>{{html()->label($user->name)}}</div>
                            {{html()->label()->class('switch switch-success')->html(
                                html()->checkbox('users[]', old('users[]'))->value($user->id)->checked(isset($category) && $user->categories->where('id', $category->id)->first())->id('user'.$user->id)->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-footer">
            {{html()->submit('Save')->class('btn btn-success')}}
        </div>
        {{html()->closeModelForm()}}
    </div>
@endsection
