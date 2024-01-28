@extends('layout_login')

@section('content')
<section id="content" class="content">
    <div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <div class="content__wrap">

            <!-- Login card -->
            <div class="card shadow-lg" style="width: 300px" >
                <div class="card-body">
                    <div class="w-100 text-center">
                        @if(isset($logo))
                        <img align="left" border="0" src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$logo) }}" alt="Logo cliente" title="Logo cliente" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 25%;max-width: 160px;" width="160" class="v-src-width v-src-max-width"/>
                        @else
                            <img src="{{url('/img/Mosaic_brand_300.png')}}" style="width:150px">
                        @endif
                    </div>
                    
                    <div class="text-center">
                        <h1 class="h3">Inicio de sesion</h1>
                        <p>Inicie sesion en su cuenta</p>
                    </div>

                    <form method="POST" class="mt-4" action="{{ isset($email)?route('login'):route('prelogin') }}">
                        @csrf

                        <input id="email" type="text" class="mb-3 form-control @error('email') is-invalid @enderror" placeholder="Username" name="email" value="{{ isset($email)?$email:old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        
                        @if(isset($email))
                        <div class="mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input id="_dm-loginCheck"  name="remember" value="1" class="form-check-input" type="checkbox">
                            <label for="_dm-loginCheck" class="form-check-label">
                                Remember me
                            </label>
                        </div>
                        @endif
                        <input type="hidden" name="intended" value="{{ session()->has('url.intended')?session('url.intended'):'' }}">
                        <div class="d-grid mt-5">
                            <button class="btn btn-primary btn-lg" type="submit">Login</button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('password.request') }}" class="btn-link text-decoration-none">Olvide mi password</a> 
                        {{-- <a href="{{ route('register') }}" class="btn-link text-decoration-none">Registro</a> --}}
                    </div>

                    <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                        <span class="m-0">Iniciar sesion con</span><br>

                        <!-- Social media buttons -->
                        <div class="ms-3">
                            <a href="{{ route('login.google') }}" class="btn btn-icon bg-transparent btn-underlined text-muted">
                               <img src="{{ url('/img/google.png') }}" style="width:30px">
                            </a>
                            <a href="{{ route('login.microsoft') }}" class="btn btn-icon bg-transparent btn-underlined text-muted">
                                <img src="{{ url('/img/microsoft.png') }}" style="width:30px">
                            </a>
                            {{-- <a href="#" class="btn btn-icon bg-transparent btn-underlined text-muted">
                                <i class="psi-google-plus fs-4"></i>
                            </a>
                            <a href="#" class="btn btn-icon bg-transparent btn-underlined text-muted">
                                <i class="psi-instagram fs-4"></i> --}}
                            </a>
                        </div>
                        <!-- END : Social media buttons -->

                    </div>

                </div>
            </div>
            <!-- END : Login card -->

            <!-- Demonstration purposes -->
            {{-- <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
                <button type="button" onclick="window.history.back()" class="btn btn-light">Go back</button>
                <a href="./index.html" class="btn btn-primary">Return home</a> --}}
            </div>
            <!-- END : Demonstration purposes -->

        </div>
    </div>
</section>

@endsection


