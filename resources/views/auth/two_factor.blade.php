@extends('layouts.guest')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {{ html()->form('POST', route('2fa.confirm',  $userId))->class('form-horizontal')->open() }}
                <div class="card">
                    <div class="card-header">2FA</div>
                    <div class="card-body">
                        <p>Scan the QRCode with a authenticator app (e.g. iOS Authenticator, Authy, Google Authenticator, Microsoft Authenticator...)</p>
                        <div class="row">
                            {!! $qrCode !!}
                            <br>
                            Code : {{$string}}
                        </div>
                        <div class="form-group">
                            <label for="code">Confirmation code</label>
                            <input class="form-input" type="number" name="code" required autofocus>
                        </div>
                    </div>

                    <div class="card-footer clearfix">
                        <div class="row">
                            <div class="col text-right">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
@endsection