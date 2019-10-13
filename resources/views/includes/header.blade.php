<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
{{--    <a class="navbar-brand" href="https://coreui.io">--}}
{{--        <img src="https://coreui.io/docs/assets/brand/logo.svg" width="89" height="25" alt="CoreUI Logo">--}}
{{--    </a>--}}
{{--    <ul class="nav navbar-nav mr-auto d-md-down-none">--}}
{{--        <li class="nav-item px-3">--}}
{{--            <a class="nav-link" href="https://coreui.io/">CoreUI Website</a>--}}
{{--        </li>--}}
{{--        <li class="nav-item px-3">--}}
{{--            <a class="nav-link" href="https://coreui.io/icons/">CoreUI Icons</a>--}}
{{--        </li>--}}
{{--        <li class="nav-item px-3">--}}
{{--            <a class="btn btn-danger" href="https://coreui.io/#sneak-peek">Try CoreUI PRO 3.0.0-alpha</a>--}}
{{--        </li>--}}
{{--    </ul>--}}
{{--    <ul class="nav navbar-nav d-md-down-none">--}}
{{--        <li class="nav-item px-3">--}}
{{--            <a class="btn btn btn-outline-warning" href="https://coreui.io/support/">Contact &amp; Support</a>--}}
{{--        </li>--}}
{{--    </ul>--}}

            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>

                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item dropdown px-3">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
</header>
