<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

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
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>
</html>
