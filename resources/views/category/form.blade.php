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

            @if (Auth::user()->is_admin)
                <hr>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <div>{{html()->label('Restricted ? (only admins can choose which users can access)')}}</div>
                            {{html()->label()->class('switch switch-primary')->html(
                                html()->checkbox('restricted', old('restricted'))->class('switch-input').'<span class="switch-slider"></span>')}}
                        </div>
                    </div>
                </div>
            @endif

            <hr>
            <div id="usersSelection" class="row">
                <div class="col-6">
                    <h4>Who can access ?</h4>
                </div>
                <div class="col-6 text-right">
                    <button type="button" id="selectAll" class="btn btn-dark btn-sm">Toggle all</button>
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

@section('scripts')
    <script type="text/javascript">
        window.addEventListener('load', function () {
            let button = document.getElementById('selectAll')
            let checkboxes = document.querySelectorAll('#usersSelection input[type=checkbox]')
            button.addEventListener('click', function (event) {
                let isFirstChecked = checkboxes.length && checkboxes[0].checked
                for (let i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = !isFirstChecked
                    isFirstChecked ? checkboxes[i].removeAttribute('checked') : checkboxes[i].setAttribute('checked', 'checked')
                }
            }, false)
        })
    </script>
@endsection
