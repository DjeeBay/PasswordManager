<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">Menu</li>
            @php
                $categories = \Illuminate\Support\Facades\Auth::user()->is_admin ? App\Models\Category::all()->sortBy('name') : \Illuminate\Support\Facades\Auth::user()->categories->sortBy('name');
                $colors = [
                    'text-blue',
                    'text-warning',
                    'text-purple',
                    'text-green',
                    'text-red',
                ];
                $colorIndex = 0;
            @endphp
            @foreach($categories as $category)
                @php
                    if (($colorIndex + 1) > count($colors)) {
                        $colorIndex = 0;
                    }
                    $color = $colors[$colorIndex];
                @endphp
                <li class="nav-item">
                    <a class="nav-link" href="{{route('keepass.get', $category->id)}}">
                        <i class="nav-icon cui-folder {{$colors[$colorIndex]}}"></i> {{$category->name}}
                    </a>
                </li>
                @php
                    $colorIndex++;
                @endphp
            @endforeach

            <li class="nav-item">
                <a class="nav-link" href="{{route('favorite.index')}}">
                    <i class="nav-icon cui-star text-warning"></i> Favorites
                </a>
            </li>
            @if (\Illuminate\Support\Facades\Auth::user()->is_admin || \Illuminate\Support\Facades\Auth::user()->can('read historic'))
                <li class="nav-item">
                    <a class="nav-link" href="{{route('historic.index')}}">
                        <i class="nav-icon cui-history text-info"></i> Historic
                    </a>
                </li>
            @endif

            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i class="nav-icon cui-settings"></i> Settings
                </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('category.index')}}">
                            <i class="nav-icon cui-list"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('user.index')}}">
                            <i class="nav-icon cui-people"></i> Users
                        </a>
                    </li>
                    @if (\Illuminate\Support\Facades\Auth::user()->is_admin || \Illuminate\Support\Facades\Auth::user()->can('create keepass') || \Illuminate\Support\Facades\Auth::user()->can('edit keepass'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('icon.index')}}">
                                <i class="nav-icon cui-smile"></i> Icons
                            </a>
                        </li>
                    @endif
                    @if (\Illuminate\Support\Facades\Auth::user()->is_admin || \Illuminate\Support\Facades\Auth::user()->can('import keepass'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('keepass.get_import')}}">
                                <i class="nav-icon cui-cloud-upload"></i> Import
                            </a>
                        </li>
                    @endif
                    @if (\Illuminate\Support\Facades\Auth::user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('keepass.export_database')}}">
                                <i class="nav-icon cui-cloud-download"></i> Export Database
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
