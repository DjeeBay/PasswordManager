@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item">Keepass</li>
            <li class="breadcrumb-item active">{{$category->name}}</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card border border-dark">
        <div class="card-header bg-dark text-center font-weight-bold">
            {{$category->name}}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <keepass-wrapper
                        :category-id="{{$category->id}}"
                        :icon-list='@json(\App\Models\Icon::all())'
                        :items='@json($items)'
                        save-route="{{route('keepass.save', $category->id)}}"
                        create-multiple-route="{{route('keepass.create-multiple', $category->id)}}"
                    ></keepass-wrapper>
                </div>
            </div>
        </div>
    </div>
@endsection
