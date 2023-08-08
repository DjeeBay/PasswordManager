<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>

            <a class="navbar-brand pl-lg-5" href="{{ url('/') }}">
                <img class="mr-1" src="{{asset('img/logo.png')}}" height="25" alt="KeePassManager Logo">
                <span class="w-100 text-center">{{ config('app.name', 'KeePassManager') }}</span>
            </a>

                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav ml-auto">
                    @if (env('ENABLE_PER_USER_TWO_FACTOR_AUTHENTICATION', false) && !\Illuminate\Support\Facades\Auth::user()->hasTwoFactorEnabled())
                        <li class="nav-item dropdown px-3">
                            <a class="nav-link text-primary" href="{{ route('2fa.prepare_per_user_two_factor') }}">
                                <i class="fa fa-user-secret"></i> {{__('Enable 2FA')}}
                            </a>
                        </li>
                    @endif
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

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
</header>
