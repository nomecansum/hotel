@extends('layout_login')

@section('content')
<section id="content" class="content">
    <div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <div class="content__wrap">
            <!-- Login card -->
            <div class="card shadow-lg">
                <div class="card-body">

                    <div class="text-center">
                        <h1 class="h3">Restablecer password</h1>
                        <p>Indique su e-mail para recuperar su password.</p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->token }}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('e-mail') }}</label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ app('request')->input('email')??($email ?? old('email')) }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirmar Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button class="btn btn-warning btn-lg" type="submit">Reset Password</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ url('/login') }}" class="btn-link text-decoration-none">Volver a login</a>
                    </div>

                </div>
            </div>
            <!-- END : Login card -->
        </div>
    </div>
</section>
@endsection
