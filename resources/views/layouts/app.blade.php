<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/BosketTreeView.css') }}" rel="stylesheet">
</head>
<body class="app sidebar-md-show">
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
</body>
@yield('scripts')
<script src="{{asset('js/formValidation.js')}}" defer></script>
</html>
