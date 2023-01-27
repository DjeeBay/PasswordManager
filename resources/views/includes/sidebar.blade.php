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
                        <i class="nav-icon cil-folder {{$colors[$colorIndex]}}"></i> {{$category->name}}
                    </a>
                </li>
                @php
                    $colorIndex++;
                @endphp
            @endforeach

            <li class="nav-item">
                <a class="nav-link" href="{{route('favorite.index')}}">
                    <i class="nav-icon cil-star text-warning"></i> Favorites
                </a>
            </li>

            @php
                $privateCategories = \App\Models\PrivateCategory::where('owner_id', '=', \Illuminate\Support\Facades\Auth::user()->id)->get();
            @endphp
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i class="nav-icon fa fa-user-secret text-danger"></i> Private categories
                </a>
                <ul class="nav-dropdown-items">
                    @if (\Illuminate\Support\Facades\Auth::user()->passphrase_validator && !session()->get('kpm.private_passphrase'))
                        <li class="nav-item text-center">
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#checkPassphraseModal">Passphrase required</button>
                        </li>
                    @else
                        @foreach($privateCategories as $category)
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('keepass.get_private', $category->id)}}">
                                    <i class="nav-icon fa fa-folder-closed @if (!$category->color || $category->color === '#000000') text-warning @endif" style="@if ($category->color) color:{{$category->color}};@endif"></i> {{$category->name}}
                                </a>
                            </li>
                        @endforeach
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('private-category.create')}}">
                            <i class="nav-icon fa fa-plus-circle text-success"></i> Add
                        </a>
                    </li>
                </ul>
            </li>

            @if (\Illuminate\Support\Facades\Auth::user()->is_admin || \Illuminate\Support\Facades\Auth::user()->can('read historic'))
                <li class="nav-item">
                    <a class="nav-link" href="{{route('historic.index')}}">
                        <i class="nav-icon cil-history text-info"></i> Historic
                    </a>
                </li>
            @endif

            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i class="nav-icon cil-settings"></i> Settings
                </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('category.index')}}">
                            <i class="nav-icon cil-list"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('private-category.index')}}">
                            <i class="nav-icon cil-list text-warning"></i> Private categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('user.index')}}">
                            <i class="nav-icon cil-people"></i> Users
                        </a>
                    </li>
                    @if (\Illuminate\Support\Facades\Auth::user()->is_admin || \Illuminate\Support\Facades\Auth::user()->can('create keepass') || \Illuminate\Support\Facades\Auth::user()->can('edit keepass'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('icon.index')}}">
                                <i class="nav-icon cil-smile"></i> Icons
                            </a>
                        </li>
                    @endif
                    @if (\Illuminate\Support\Facades\Auth::user()->is_admin || \Illuminate\Support\Facades\Auth::user()->can('import keepass'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('keepass.get_import')}}">
                                <i class="nav-icon cil-cloud-upload"></i> Import
                            </a>
                        </li>
                    @endif
                    @if (\Illuminate\Support\Facades\Auth::user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('keepass.export_database')}}">
                                <i class="nav-icon cil-cloud-download"></i> Export Database
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>

    @if (\Illuminate\Support\Facades\Auth::user()->passphrase_validator && !session()->get('kpm.private_passphrase'))
        <!-- Modal -->
        {{html()->form('POST', route('user.check_passphrase'))->class('needs-validation')->novalidate()->open()}}
        <div class="modal modal-warning fade" id="checkPassphraseModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="checkPassphraseModalLabel">Enter your passphrase</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {{html()->label('Passphrase', 'passphrase')}}
                                    {{html()->password('passphrase')->class('form-control')->placeholder('passphrase')}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Validate your passphrase</button>
                    </div>
                </div>
            </div>
        </div>
        {{html()->form()->close()}}
    @endif
</div>
