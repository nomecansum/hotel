@extends('layout_login')

@section('content')


<div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">
    <div class="content__wrap">

        <!-- Login card -->
        <div class="card shadow-lg">
            <div class="card-body">
                <img src="{{url('/img/Mosaic_brand_300.png')}}" style="width:150px">
                <div class="text-center">
                    <h1 class="h3">Crear una nueva cuenta</h1>
                    
                </div>

                <form method="POST" id="loginform" action="{{ route('register') }}">
                    @csrf

                    <div class="w-md-400px d-inline-flex row g-3 mb-4">
                        <div class="col-sm-12">
                            <label>Nombre</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label>e-mail</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label>Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label>Confirmar password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-check">
                        <input id="_dm-registerCheck" class="form-check-input" type="checkbox">
                        <label for="_dm-registerCheck" class="form-check-label">
                            Acepto los <a href="#" class="btn-link text-decoration-underline">Terminos y condiciones de uso</a>
                        </label>
                    </div>

                    <div class="d-grid mt-5">
                        <button class="btn btn-primary btn-lg" type="submit">Registrar</button>
                    </div>
                </form>

                <div class="d-flex justify-content-between mt-4">
                    Ya es usuario ?
                    <a href="{{ route('login') }}" class="btn-link text-decoration-none">Login</a>
                </div>

                <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                    <h5 class="m-0">Registrarse con</h5>

                    <!-- Social media buttons -->
                    <div class="ms-3">
                        <a href="#" class="btn btn-icon bg-transparent btn-underlined text-muted">
                            <img src="{{ url('/img/google.png') }}" style="width:30px">
                         </a>
                    </div>
                    <!-- END : Social media buttons -->

                </div>

            </div>
        </div>
        <!-- END : Login card -->
    </div>
</div>

@endsection
