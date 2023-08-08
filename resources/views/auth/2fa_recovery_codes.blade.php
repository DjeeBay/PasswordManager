@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        2FA recovery codes
                    </div>
                    <div class="card-body">
                        @foreach($codes as $code)
                            <div class="row">
                                <div class="col-12">
                                    {{\Illuminate\Support\Arr::get($code, 'code')}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
