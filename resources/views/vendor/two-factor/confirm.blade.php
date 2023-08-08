@extends('layouts.guest')

@section('content')
    <form method="post" action="{{route('2fa.code_check')}}">
        @csrf
        <div class="form-row justify-content-center py-3">
            <div class="col-sm-8 col-8 mb-3">
                <p class="text-center">
                    {{ trans('two-factor::messages.continue') }}
                </p>
                <input type="text" name="2fa_code" id="2fa_code"
                       class="@error('2fa_code') is-invalid @enderror form-control form-control-lg"
                       minlength="6" placeholder="123456" required autofocus>
            </div>
            <div class="w-100"></div>
            <div class="col-auto mb-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    {{ trans('two-factor::messages.confirm') }}
                </button>
            </div>
        </div>
    </form>
@endsection
