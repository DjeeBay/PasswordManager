@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Home</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">Dashboard</div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row">
                @php
                $backgrounds = [
                    'bg-blue',
                    'bg-warning',
                    'bg-purple',
                    'bg-green',
                    'bg-red',
                ];
                $bgIndex = 0;
                @endphp
                @foreach(Auth::user()->categories as $category)
                    @php
                        if (($bgIndex + 1) > count($backgrounds)) {
                            $bgIndex = 0;
                        }
                        $bg = $backgrounds[$bgIndex];
                    @endphp
                    <div class="col-sm-6 col-lg-3">
                        <a class="text-decoration-none" href="{{route('keepass.get', $category->id)}}">
                            <div class="card text-white {{$bg}}">
                                <div class="card-body">
                                    <div class="text-value">{{$category->name}}</div>
                                    <div>Folders : {{\App\Models\Keepass::where([['category_id', '=', $category->id], ['is_folder', '=', 1]])->count()}}</div>
                                    <div>Passwords : {{\App\Models\Keepass::where([['category_id', '=', $category->id], ['is_folder', '=', 0]])->whereNotNull('password')->count()}}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @php
                        $bgIndex++;
                    @endphp
                @endforeach
            </div>
        </div>
    </div>
@endsection
