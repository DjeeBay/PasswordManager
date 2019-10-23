<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">Menu</li>
            @php $categories = Auth::user()->is_admin ? App\Models\Category::all()->sortBy('name') : Auth::user()->categories->sortBy('name'); @endphp
            @foreach($categories as $category)
                <li class="nav-item">
                    <a class="nav-link" href="{{route('keepass.get', $category->id)}}">
                        <i class="nav-icon cui-folder"></i> {{$category->name}}
                    </a>
                </li>
            @endforeach
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
                </ul>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
