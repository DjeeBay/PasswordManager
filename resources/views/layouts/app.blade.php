<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KeePassManager') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/BosketTreeView.css') }}" rel="stylesheet">

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{asset('img/favicon.ico')}}" type="image/x-icon" />
	<link rel="apple-touch-icon" href="{{asset('img/apple-touch-icon.png')}}" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{asset('img/apple-touch-icon-57x57.png')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{asset('img/apple-touch-icon-72x72.png')}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('img/apple-touch-icon-76x76.png')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{asset('img/apple-touch-icon-114x114.png')}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{asset('img/apple-touch-icon-120x120.png')}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{asset('img/apple-touch-icon-144x144.png')}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{asset('img/apple-touch-icon-152x152.png')}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('img/apple-touch-icon-180x180.png')}}">
</head>
<body class="app sidebar-lg-show">
@include('includes.header')
<div id="app" class="app-body">
    <notifications></notifications>
    @include('includes.sidebar')
    <main class="main">
        @yield('breadcrumb')
        @if (isset($errors) && $errors->all())
            <div class="container-fluid">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $message)
                        <div>{{$message}}</div>
                    @endforeach
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif
        @if (isset($success) || session()->has('success'))
            <div class="container-fluid">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div>{{isset($success) ? $success : session()->get('success')}}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
<script src="{{asset('js/formValidation.js')}}"></script>
</body>
</html>
